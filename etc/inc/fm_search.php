<?php
/*
	fm_search.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2020 XigmaNAS® <info@xigmanas.com>.
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
//	find items
function find_item($dir,$regex,&$list,$recur) {
	$handle = @opendir(get_abs_dir($dir));
//	open dir successful?
	if($handle !== false):
		while(($new_item = readdir($handle)) !== false):
			if(@file_exists(get_abs_item($dir,$new_item)) && get_show_item($dir,$new_item)):
//				match?
				if(preg_match($regex,$new_item) === 1):
					$list[] = [$dir,$new_item];
				endif;
//				search sub-directories
				if(get_is_dir($dir,$new_item) && $recur):
					find_item(get_rel_item($dir,$new_item),$regex,$list,$recur);
				endif;
			endif;
		endwhile;
		closedir($handle);
	endif;
}
//	make list of found items
function make_list($dir,$item,$subdir) {
//	convert shell-wildcards to PCRE Regex Syntax
	$matches = preg_split('/([\?\*])/',$item,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	if(is_array($matches) && count($matches) > 0):
		$converted_matches = [];
		foreach($matches as $single_match):
			switch($single_match):
				case '?':
					$converted_matches[] = '.';
					break;
				case '*':
					$converted_matches[] = '.*';
					break;
				default:
					$converted_matches[] = preg_quote($single_match,'/');
					break;
			endswitch;
		endforeach;
		$regex = '/^' . implode('',$converted_matches) . '$/i';
	else:
		$regex = '/.*/';
	endif;
//	search
	find_item($dir,$regex,$list,$subdir);
	if(is_array($list)):
		sort($list);
	endif;
	return $list;
}
//	print table of found items
function print_table($list) {
	if(is_array($list)):
		$cnt = count($list);
		for($i = 0;$i < $cnt;++$i):
			$dir = $list[$i][0];
			$item = $list[$i][1];
			$link = '';
			$target = '';
			if(get_is_dir($dir,$item)):
				$img = 'dir.gif';
				$link = make_link('list',get_rel_item($dir,$item),null);
			else:
				$img = get_mime_type($dir,$item,'img');
				$link = make_link('download',$dir,$item);
			endif;
			echo '<tr>',
					'<td class="lcell">',
						'<img border="0" width="16" height="16" align="ABSMIDDLE" src="/images/fm_img/' . $img . '" alt="">',
						'&nbsp;',
						'<a href="',$link,'" target="',$target,'">',htmlspecialchars($item),'</a>';
			echo	'</td>';
			echo	'<td class="lcebl">',
						'<a href="',make_link('list',$dir,null),'"> /',htmlspecialchars($dir),'</a>',
					'</td>';
			echo '</tr>',"\n";
		endfor;
	endif;
}
//	search for item
function search_items($dir) {
	if(isset($GLOBALS['__POST']['searchitem'])):
		$searchitem = $GLOBALS['__POST']['searchitem'];
		$subdir = (isset($GLOBALS['__POST']['subdir']) && $GLOBALS['__POST']['subdir'] == 'y');
		$list = make_list($dir,$searchitem,$subdir);
	else:
		$searchitem = null;
		$subdir = true;
	endif;
	$msg = gtext('Search');
	if($searchitem != null):
		$msg .= ': (/' . get_rel_item($dir,$searchitem) . ')';
	endif;
	show_header(htmlspecialchars($msg));
//	search box
	echo '<form name="searchform" action="',make_link('search',$dir,null),'" method="post">',"\n";
	echo	'<div id="formextension">',"\n",'<input name="authtoken" type="hidden" value="',Session::getAuthToken(),'">',"\n",'</div>',"\n";
	echo	'<table class="area_data_selection">',
				'<colgroup>',
					'<col style="width:100%">',
				'</colgroup>',
				'<thead>',
					'<tr>',
						'<td class="gap"></td>',
					'</tr>',
					'<tr>',
						'<th class="lhetop">',gtext('Search Filter'),'</th>',
					'</tr>',
					'<tr>',
						'<td class="gap"></td>',
					'</tr>',
				'</thead>',
				'<tbody class="donothighlight">',
					'<tr>',
						'<td>',
							'<input name="searchitem" type="text" size="25" value="',htmlspecialchars($searchitem),'">',
							'<input type="submit" value="',gtext('Search'),'">','&nbsp;',
							'<input type="button" value="',gtext('Close'),'" onClick="javascript:location=\'',make_link('list',$dir,null),'\';">',
						'</td>',
					'</tr>',
					'<tr>',
						'<td>',
							'<input type="checkbox" name="subdir" value="y"',($subdir ? ' checked' : ''),'>',gtext('Search subdirectories'),
						'</td>',
					'</tr>',
				'</tbody>',
			'</table>',"\n";
	echo '</form>',"\n";
//	search result
	if($searchitem != null):
		echo '<table class="area_data_selection">',"\n";
		echo	'<colgroup>',
					'<col style="width:42%">',
					'<col style="width:58%">',
				'</colgroup>',"\n";
		echo	'<thead>',"\n";
		echo		'<tr>',
						'<th class="gap" colspan="2"></th>',
					'</tr>',"\n";
		echo		'<tr>',
						'<th class="lhetop" colspan="2">',gtext('Search Filter Result'),'</th>',
					'</tr>',"\n";
		echo		'<tr>',
						'<th class="lhell"><b>',gtext('Name'),'</b></td>',
						'<th class="lhebl"><b>',gtext('Path'),'</b></td>',
					'</tr>',"\n";
		echo	'</thead>',"\n";
//		make & print table of found items
		if(is_countable($list) && count($list) > 0):
			echo '<tbody>',"\n";
			print_table($list);
			echo '</tbody>',"\n";
		endif;
		echo	'<tfoot>',"\n";
		echo		'<tr>';
		echo			'<td class="lcebl" colspan="2">';
		if(is_countable($list) && count($list) > 0):
			echo		count($list),' ',gtext('Item(s)'),'.';
		else:
			echo		gtext('No results available.');
		endif;
		echo			'</td>';
		echo		'</tr>',"\n";
		echo	'</tfoot>',"\n";
		echo '</table>',"\n";
	endif;
	echo '<script>',"\n";
	echo '//<![CDATA[',"\n";
	echo '	if(document.searchform) document.searchform.searchitem.focus();',"\n";
	echo '//]]>',"\n";
	echo '</script>',"\n";
}
