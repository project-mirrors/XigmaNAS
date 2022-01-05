<?php
/*
	archive.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2022 XigmaNAS® <info@xigmanas.com>.
	All rights reserved.

	Portions of Quixplorer (http://quixplorer.sourceforge.net).
	Authors: quix@free.fr, ck@realtime-projects.com.
	The Initial Developer of the Original Code is The QuiX project.

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

require_once('fm_qxpage.php');
require_once('fm_lib_zipstream.php');
require_once 'fm_permissions.php';

/**
 * _zip
 * @return void
 **/
function zip_selected_items($zipfilename,$directory,$items) {
	$zipfile = new ZipArchive();
	$zipfile->open($zipfilename,ZIPARCHIVE::CREATE);
	foreach($items as $item):
		$srcfile = $directory . DIRECTORY_SEPARATOR . $item;
		if(!$zipfile->addFile($srcfile,$item)):
			show_error($srcfile . ': Failed adding item.');
		endif;
	endforeach;
	if(!$zipfile->close()):
		show_error($zipfilename . ': Failed saving zipfile.');
	endif;
}
function zip_items($dir,$name) {
	$items = qxpage_selected_items();
	if(!preg_match('/\.zip$/',$name)):
		$name .= '.zip';
	endif;
	zip_selected_items(get_abs_item($dir,$name),$dir,$items);
	header('Location: ' . make_link('list',$dir,null));
}
function zip_download($directory,$items) {
	$zipfile = new ZipStream('downloads.zip');
	foreach($items as $item):
		_zipstream_add_file($zipfile,$directory,$item);
	endforeach;
	$zipfile->finish();
}
function _zipstream_add_file($zipfile,$directory,$file_to_add) {
	$filename = $directory . DIRECTORY_SEPARATOR . $file_to_add;
	if(!@file_exists($filename)):
		show_error($filename . ' does not exist');
	endif;
	if(is_file($filename)):
		_debug("adding file $filename");
		return $zipfile->add_file($file_to_add,file_get_contents($filename));
	endif;
	if(is_dir($filename)):
		_debug("adding directory $filename");
		$files = glob($filename . DIRECTORY_SEPARATOR . '*');
		foreach ($files as $file):
			$file = str_replace($directory . DIRECTORY_SEPARATOR,'',$file);
			_zipstream_add_file($zipfile,$directory,$file);
		endforeach;
		return true;
	endif;
	_error("don't know how to handle $file_to_add");
	return false;
}

function tar_items($dir,$name) {
}
function tgz_items($dir,$name) {
}
function archive_items($dir) {
//	archive is only allowed if user may change files
	if(!permissions_grant($dir,null,'change')):
		show_error(gtext('You are not allowed to use this function.'));
	endif;
	if(!$GLOBALS['zip'] && !$GLOBALS['tar'] && !$GLOBALS['tgz']):
		show_error(gtext('Function unavailable.'));
	endif;
	if(isset($GLOBALS['__POST']['name'])):
		$name = basename($GLOBALS['__POST']['name']);
		if($name == '') show_error(gtext('You must supply a name.'));
		switch($GLOBALS['__POST']['type']):
			case 'zip':
				zip_items($dir,$name);
				break;
			case 'tar':
				tar_items($dir,$name);
				break;
			default:
				tgz_items($dir,$name);
				break;
		endswitch;
		header('Location: ' . make_link('list',$dir,null));
	endif;
	show_header(gtext('Archive item(s)'));
	echo '<div id="area_data_frame">',"\n";
	echo '<form name="archform" method="post" action="',make_link('arch',$dir,null) . '">',"\n";
	$cnt = count($GLOBALS['__POST']['selitems']);
	for($i = 0;$i < $cnt;++$i):
		echo '<input type="hidden" name="selitems[]" value="',htmlspecialchars($GLOBALS['__POST']['selitems'][$i]),'">',"\n";
	endfor;
	echo	'<table width="300">',"\n",
				'<tr>',"\n",
					'<td>',gtext('Name'),':</td>',"\n",
					'<td align="right">','<input type="text" name="name" size="25">','</td>',"\n",
				'</tr>',"\n";
	echo		'<tr>',"\n",
					'<td>',gtext('Type'),':</td>',"\n",
					'<td align="right">';
	echo				'<select name="type">',"\n";
	if($GLOBALS['zip']):
		echo				'<option value="zip">Zip</option>',"\n";
	endif;
	if($GLOBALS['tar']):
		echo				'<option value="tar">Tar</option>',"\n";
	endif;
	if($GLOBALS['tgz']):
		echo				'<option value="tgz">TGz</option>',"\n";
	endif;
	echo				'</select>';
	echo			'</td>',
				'</tr>';
	echo		'<tr>',
					'<td></td>',
					'<td align="right">',
						'<input type="submit" value="',gtext('Create'),'">',"\n",
						'<input type="button" value="'.gtext('Cancel'),'" onClick="javascript:location=\'',make_link('list',$dir,null),'\';">',"\n",
					'</td>',
				'</tr>',
			'</table>';
	echo '</form>';
?>
<script>
//<![CDATA[
	if(document.archform) document.archform.name.focus();
//]]>
</script>
<?php
	echo '</div>',"\n";
}
