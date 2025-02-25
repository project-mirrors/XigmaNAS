<?php
/*
	filechooser.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2025 XigmaNAS® <info@xigmanas.com>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

	The views and conclusions contained in the software and documentation are those
	of the authors and should not be interpreted as representing official policies
	of XigmaNAS®, either expressed or implied.
 */

namespace filechooser;

use function format_bytes;
use function get_filesize;
use function gtext;
use function print_info_box;

class filechooser {
	protected bool $show_footer = true; // show footer
	protected bool $show_sort = true; // show sorting header
	protected bool $show_line_numbers = true; // show/hide column
	protected bool $show_file_size = true; // show/hide column
	protected bool $show_file_mod_date = true; // show/hide column
	protected bool $show_file_type = true; // show/hide column
	protected bool $calc_folder_sizes = false; // calculate folder sizes (increases processing time)
	protected bool $show_simple_type = true; // display MIME type, or "simple" file type (MIME type increases processing time)
	protected bool $show_separate_folders = true; // sort folders on top of files
	protected bool $use_natural_sort = true; // natural sort files, as opposed to regular sort (files with capital letters get sorted first)
	protected string $filter_show_files = '*';
	protected string $filter_hide_files = '.*';
	protected string $filter_show_folders = '*';
	protected string $filter_hide_folders = '.,..,.*';
	protected string $date_format = 'F d, Y g:i A'; // date format.
	protected string $folder_root = '/';
	protected string $sort_mode = 'N';
	protected string $sort_order = 'A';
	protected string $total_size = '';
	protected string $script_name = 'filechooser.php';

	public function __construct() {
		global $config;

		$this->date_format = $config['system']['datetimeformat'] ?? 'Y/m/d H:i'; // date format.
//		Get path if browsing a tree.
		$path = isset($_GET['p']) ? urldecode(htmlspecialchars($_GET['p'])) : false;
//		If no path is available, set it to root.
		if(!$path):
			$path = (isset($_GET['sd'])) ? htmlspecialchars($_GET['sd']) : $this->folder_root;
		endif;
//		Check if file exists.
		if(!file_exists($path)):
			print_info_box("File not found $path");
			if(substr($path,0,1) == '/'):
				$path = $this->get_valid_parent_dir($path);
			else:
				$path = '/mnt';
			endif;
		endif;
		$dir = $path;
//		Extract path if necessary.
		if(is_file($dir)):
			$dir = dirname($dir) . '/';
		endif;
//		Check if directory string end with '/'. Add it if necessary.
		if('/' !== substr(strrev($dir),0,1)):
			$dir .= '/';
		endif;
//		Get sorting vars from URL, if nothing is set, sort by N [file Name].
		$this->sort_mode = (isset($_GET['N']) ? 'N' : (isset($_GET['S']) ? 'S' : (isset($_GET['T']) ? 'T' : (isset($_GET['M']) ? 'M' : 'N' ))));
//		Get sort ascending or descending.
		$this->sort_order = $_GET[$this->sort_mode] ?? 'A';
//		Create array of files in tree.
		$files = $this->make_file_array($dir);
//		Get size of arrays before sort.
		$totalFolders = sizeof($files['folders']);
		$totalFiles = sizeof($files['files']);
//		Sort files.
		$files = $this->sort_files($files);
		$gt_cancel = gtext('Cancel');
		$gt_ok = gtext('OK');
		echo <<<EOD
<table>
	<thead>
		<tr>
			<th>
				<input class="input" name="p" value="{$path}" type="text">
			</th>
		</tr>
		<tr>
			<th>
				<input class="formbtn" type="submit" value="{$gt_ok}">
				<input class="formbtn" type="reset" value="{$gt_cancel}">
			</th>
		</tr>
	</thead>
</table>
EOD;
//		Display file list.
		echo $this->file_list($dir,$files);
	}
	function make_file_array($dir) {
		if(!function_exists('mime_content_type')):
			function mime_content_type($file) {
				$file = escapeshellarg($file);
				$type = `file -bi $file`;
				$expl = explode(';',$type);
				return $expl[0];
			}
		endif;
		$dirArray	= [];
		$folderArray = [];
		$folderInfo = [];
		$fileArray = [];
		$fileInfo = [];
		$content = $this->get_content($dir);
		foreach($content as $file):
			if(is_dir("{$dir}/{$file}")): // is a folder
//				store elements of folder in sub array
				$folderInfo['name']	= $file;
				$folderInfo['mtime'] = @filemtime("{$dir}/{$file}");
				$folderInfo['type'] = gtext("Directory");
//				calc folder size ?
				$folderInfo['size'] = $this->calc_folder_sizes ? $this->get_folder_size("{$dir}/{$file}") : '-';
				$folderInfo['rowType'] = 'fr';
				$folderArray[] = $folderInfo;
			else: // is a file
//				store elements of file in sub array
				$fileInfo['name'] = $file;
				$fileInfo['mtime'] = @filemtime("{$dir}/{$file}");
				$fileInfo['type'] = $this->show_simple_type ? $this->get_extension("{$dir}/{$file}") : mime_content_type("{$dir}/{$file}");
				$fileInfo['size'] = @get_filesize("{$dir}/{$file}");
				$fileInfo['rowType'] = 'fl';
				$fileArray[] = $fileInfo;
			endif;
		endforeach;
		$dirArray['folders'] = $folderArray;
		$dirArray['files'] = $fileArray;
		return $dirArray;
	}
	function get_content($dir) {
		$folders = [];
		$files = [];
		$handle = @opendir($dir);
		while($file = @readdir($handle)):
			if(is_dir("{$dir}/{$file}")):
				$folders[] = $file;
			elseif(is_file("{$dir}/{$file}")):
				$files[] = $file;
			elseif(preg_match('#^/dev/zvol/#',"{$dir}") && strpos($file,'@') == false):
				/* pickup ZFS volume but not snapshot */
				$S_IFCHR = 0020000;
				$st = stat("{$dir}/{$file}");
				if ($st != false && $st['mode'] & $S_IFCHR):
					$files[] = $file;
				endif;
			endif;
		endwhile;
		@closedir($handle);

		$folders = $this->filter_content($folders,$this->filter_show_folders,$this->filter_hide_folders);
		$files = $this->filter_content($files,$this->filter_show_files,$this->filter_hide_files);

		return array_merge($folders,$files);
	}
	function filter_content($arr,$allow,$hide) {
		$allow = $this->make_regex($allow);
		$hide = $this->make_regex($hide);
		$ret = [];
		$ret = preg_grep("/$allow/",$arr);
		$ret = preg_grep("/$hide/", $ret,PREG_GREP_INVERT);
		return $ret;
	}
	function make_regex($filter) {
		$regex = str_replace('.','\.',$filter);
		$regex = str_replace('/','\/',$regex);
		$regex = str_replace('*','.+',$regex);
		$regex = str_replace(',','$|^',$regex);
		return "^$regex\$";
	}
	function get_extension($filename) {
		$justfile = explode('/',$filename);
		$justfile = $justfile[(sizeof($justfile)-1)];
		$expl = explode('.',$justfile);
		if(sizeof($expl) > 1 && $expl[sizeof($expl)-1]):
			return $expl[sizeof($expl) - 1];
		else:
			return '?';
		endif;
	}
	function get_valid_parent_dir($path) {
		if(strcmp($path,'/') == 0): // Is it already root of filesystem?
			return false;
		endif;
		$expl = explode('/',substr($path,0,-1));
		$path = substr($path,0,-strlen($expl[(sizeof($expl)-1)] . '/'));
		if(!(file_exists($path) && is_dir($path))):
			$path = $this->get_valid_parent_dir($path);
		endif;
		return $path;
	}
	function get_folder_size($dir) {
		$size = 0;
		$handle = opendir($dir);
		if($handle):
			while(false !== ($file = readdir($handle))):
				if($file != '.' && $file != '..'):
					if(is_dir("{$dir}/{$file}")):
						$size += $this->get_folder_size("{$dir}/{$file}");
					else:
						$size += @get_filesize("{$dir}/{$file}");
					endif;
				endif;
			endwhile;
		endif;
		return $size;
	}
	function sort_files($files) {
//		sort folders on top
		if($this->show_separate_folders):
			$sortedFolders = $this->order_by_column($files['folders'],'2');
			$sortedFiles = $this->order_by_column($files['files'],'1');
//			sort files depending on sort order
			if($this->sort_order == 'A'):
				ksort($sortedFolders);
				ksort($sortedFiles);
				$result = array_merge($sortedFolders,$sortedFiles);
			else:
				krsort($sortedFolders);
				krsort($sortedFiles);
				$result = array_merge($sortedFiles,$sortedFolders);
			endif;
		else: // sort folders and files together
			$files = array_merge($files['folders'],$files['files']);
			$result = $this->order_by_column($files,'1');
//			sort files depending on sort order
			$this->sort_order == 'A' ? ksort($result):krsort($result);
		endif;
		return $result;
	}
	function order_by_column($input,$type) {
		$column = $this->sort_mode;
		$result = [];

//		available sort columns
		$columnList = [
			'N' => 'name',
			'S' => 'size',
			'T' => 'type',
			'M' => 'mtime'
		];
//		row count
//		each array key gets $rowcount and $type
//		concatinated to account for duplicate array keys
		$rowcount = 0;
//		create new array with sort mode as the key
		foreach($input as $key=>$value):
//			natural sort - make array keys lowercase
			if($this->use_natural_sort):
				$col = $value[$columnList[$column]];
				$res = strtolower($col) . '.' . $rowcount . $type;
				$result[$res] = $value;
			else: // regular sort - uppercase values get sorted on top
				$res = $value[$columnList[$column]] . '.' . $rowcount . $type;
				$result[$res] = $value;
			endif;
			$rowcount++;
		endforeach;
		return $result;
	}
	function file_list($dir,$files) {
		$ret = '';
//		$ret .= '<tr>';
//		$ret .= '<td class="filelist">';
		$ret .= '<div id="area_data_frame">';

		$ret .= '<table class="area_data_selection">';
		$ret .= '<colgroup>';
		$ret .= '<col style="width:5%">';
		$ret .= '<col style="width:55%">';
		$ret .= '<col style="width:10%">';
		$ret .= '<col style="width:10%">';
		$ret .= '<col style="width:20%">';
		$ret .= '</colgroup>';
		$ret .= '<thead>';
		$ret .= $this->row('parent',$dir);
		$ret .= $this->show_sort ? $this->row('sort',$dir) : '';
		$ret .= '</thead>';
		$ret .= '<tbody>';
//		total number of files
		$rowcount  = 1;
//		total byte size of the current tree
		$totalsize = 0;
//		rows of files
		foreach($files as $file):
			$ret .= $this->row($file['rowType'],$dir,$rowcount,$file);
			$rowcount++;
			$totalsize += (int)$file['size'];
		endforeach;
		$ret .= '</tbody>';
		$ret .= '<tfoot>';
		$this->total_size = format_bytes($totalsize);
		$ret .= ($this->show_footer) ? $this->row('footer') : '';
		$ret .= '</tfoot>';
		$ret .= '</table>';

		$ret .= '</div>';
//		$ret .= '</td>';
//		$ret .= '</tr>';
		return $ret;
	}
	function row($type,$dir = null,$rowcount = null,$file = null) {
//		alternating row styles
		$rnum = $rowcount ? ($rowcount % 2 == 0 ? '_even' : '_odd') : null;
//		start row string variable to be returned
		$row = "\n" . '<tr class="' . $type . $rnum . '">' . "\n";
		switch($type):
//			file / folder row
			case 'fl':
			case 'fr':
//				line number
				$row .= $this->show_line_numbers ? '<td class="lcell">' . $rowcount . '</td>' : '';
//				filename
				$row .= '<td class="lcell"><a href="';
				$row .= '' . $this->script_name . '?p=' . urlencode("{$dir}{$file['name']}") . (($type === 'fr') ? '/' : '');
				$row .= '">' . $file['name'] . '</a></td>';
//				file size
				$row .= $this->show_file_size ? '<td class="lcell">' . format_bytes($file['size']) . '</td>' : '';
//				file type
				$row .= $this->show_file_type ? '<td class="lcell">' . $file['type'] . '</td>' : '';
//				date
				$row .= $this->show_file_mod_date ? '<td class="lcebl">' . date($this->date_format,$file['mtime']) . '</td>' : '';
				break;
			case 'sort':
//				sorting header
//				sort order. Setting ascending or descending for sorting links
				$N = ($this->sort_mode == 'N') ? ($this->sort_order == 'A' ? 'D' : 'A') : 'A';
				$S = ($this->sort_mode == 'S') ? ($this->sort_order == 'A' ? 'D' : 'A') : 'A';
				$T = ($this->sort_mode == 'T') ? ($this->sort_order == 'A' ? 'D' : 'A') : 'A';
				$M = ($this->sort_mode == 'M') ? ($this->sort_order == 'A' ? 'D' : 'A') : 'A';
				$row .= $this->show_line_numbers ? '<th class="lhell">&nbsp;</th>' : '';
				$row .= '<th class="lhell"><a href="' . $this->script_name . '?N=' . $N . '&amp;p=' . urlencode($dir) . '">' . gtext('Name') . '</a></th>';
				$row .= $this->show_file_size    ? '<th class="lhell"><a href="' . $this->script_name . '?S=' . $S . '&amp;p=' . urlencode($dir) . '">' . gtext('Size')    . '</a></th>' : '';
				$row .= $this->show_file_type    ? '<th class="lhell"><a href="' . $this->script_name . '?T=' . $T . '&amp;p=' . urlencode($dir) . '">' . gtext('Type')    . '</a></th>' : '';
				$row .= $this->show_file_mod_date ? '<th class="lhebl"><a href="' . $this->script_name . '?M=' . $M . '&amp;p=' . urlencode($dir) . '">' . gtext('Modified'). '</a></th>' : '';
				break;
			case 'parent':
//				parent directory row
				$row .= $this->show_line_numbers ? '<th class="lhetop">&laquo;</th>' : '';
				$row .= '<th class="lhetop">';
				$parent_dir = $this->get_valid_parent_dir($dir);
				if($parent_dir !== false):
					$row .= '<a href="' . $this->script_name . '?p=' . urlencode($parent_dir) . '">' . gtext('Parent Directory') . '</a>';
				else:
					$row .= '/';
				endif;
				$row .= '</th>';
				$row .= $this->show_file_size ? '<th class="lhetop">&nbsp;</th>' : '';
				$row .= $this->show_file_type ? '<th class="lhetop">&nbsp;</th>' : '';
				$row .= $this->show_file_mod_date ? '<th class="lhetop">&nbsp;</th>' : '';
				break;
			case 'footer':
//				footer row
				$row .= $this->show_line_numbers ? '<td class="ln">&nbsp;</td>' : '';
				$row .= '<td class="nm">&nbsp;</td>';
				$row .= $this->show_file_size ? '<td class="sz">' . $this->total_size . '</td>' : '';
				$row .= $this->show_file_type ? '<td class="tp">&nbsp;</td>' : '';
				$row .= $this->show_file_mod_date ? '<td class="dt">&nbsp;</td>' : '';
				break;
		endswitch;
		$row .= '</tr>';
		return $row;
	}
}