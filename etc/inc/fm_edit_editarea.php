<?php
/*
	fm_edit_editarea.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2021 XigmaNAS® <info@xigmanas.com>.
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

require_once 'autoload.php';
require_once 'fm_permissions.php';

use common\session;

//	save edited file
function savefile($file_name) {
	$code = $GLOBALS['__POST']['code'];
	$fp = @fopen($file_name,'w');
	if($fp === false):
		show_error(htmlspecialchars(basename($file_name)) . ': ' . gtext('File saving failed.'));
	endif;
	fputs($fp,$code);
	@fclose($fp);
}
//	edit file
function edit_file($dir,$item) {
	if(!permissions_grant($dir,$item,'change')):
		show_error(gtext('You are not allowed to use this function.'));
	endif;
	if(!get_is_file($dir,$item)):
		show_error(htmlspecialchars($item) . ': ' . gtext("This file doesn't exist."));
	endif;
	if(!get_show_item($dir,$item)):
		show_error(htmlspecialchars($item) . ': ' . gtext('You are not allowed to access this file.'));
	endif;
	$fname = get_abs_item($dir,$item);
	if(isset($GLOBALS['__POST']['dosave']) && $GLOBALS['__POST']['dosave'] == 'yes'):
//		save / save as
		$item = basename($GLOBALS['__POST']['fname']);
		$fname2 = get_abs_item($dir,$item);
		if(!isset($item) || $item == ''):
			show_error(gtext('You must supply a name.'));
		endif;
		if($fname != $fname2 && @file_exists($fname2)):
			show_error(htmlspecialchars($item) . ': ' . gtext('This item already exists.'));
		endif;
		savefile($fname2);
		$fname = $fname2;
	endif;
//	open file
	$fp = @fopen($fname,'r');
	if($fp === false):
		show_error(htmlspecialchars($item) . ': ' . gtext('File opening failed.'));
	endif;
//	header
	$s_item = get_rel_item($dir,$item);
	if(strlen($s_item) > 50):
		$s_item = '...' . substr($s_item,-47);
	endif;
	show_header(gtext('Edit file') . ': /' . htmlspecialchars($s_item));
	echo '<div id="area_data_frame">',"\n";

//	Word-wrap checkbox controller
?>
<script>
//<![CDATA[
	function chwrap() {
		if(document.editfrm.wrap.checked) {
			document.editfrm.code.wrap="soft";
		} else {
			document.editfrm.code.wrap="off";
		}
	}
//]]>>
</script>
<?php
	$buffer = '';
	while(!feof($fp)):
		$buffer .= fgets($fp,4096);
	endwhile;
	@fclose($fp);
//	Form
	echo	'<br>',"\n";
	echo	'<form name="editfrm" method="post" action="',make_link('edit',$dir,$item),'">',"\n";
	echo		'<div id="formextension">',"\n",'<input name="authtoken" type="hidden" value="',session::get_authtoken(),'">',"\n",'</div>',"\n";
	echo		'<input type="hidden" name="dosave" value="yes">',"\n";
	echo		'<textarea name="code" id="txtedit" rows="27" cols="125" style="font-family: monospace;" wrap="off">',htmlspecialchars($buffer),'</textarea>',"\n";
	echo		'<br>',"\n";
	echo		'<br>',"\n";
	echo		'<table>',"\n",
					'<tr>',"\n",
						'<td>',gtext('Wrap text:'),' </td>',"\n",
						'<td><input type="checkbox" name="wrap" onClick="javascript:chwrap();" value="1"></td>',"\n",
					'</tr>',"\n",
				'</table>',"\n";
	echo		'<br>',"\n";
	echo		'<table>',"\n",
					'<tr>',"\n",
						'<td><input type="text" name="fname" value="',htmlspecialchars($item),'"></td>',"\n",
						'<td><input type="submit" value="',gtext('Save'),'"></td>',"\n",
						'<td><input type="reset" value="',gtext('Reset'),'"></td>',"\n",
						'<td><input type="button" value="',gtext('Close'),'" onClick="javascript:location=\'',make_link('list',$dir,null),'\';"></td>',"\n",
					'</tr>',"\n",
				'</table>',"\n";
	echo		'<br>',"\n";
	echo	'</form>',"\n";
?>
<script>
//<![CDATA[
	if(document.editfrm) document.editfrm.code.focus();
//]]>
</script>
<?php
	echo '</div>',"\n";
}
