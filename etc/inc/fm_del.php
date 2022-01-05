<?php
/*
	del.php

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
require_once('fm_permissions.php');

//	delete files/dirs
function del_items($dir) {
//	check if user is allowed to delete files
	if(!permissions_grant($dir,null,'delete')):
		show_error(gtext('You are not allowed to use this function.'));
	endif;
	$cnt = count($GLOBALS['__POST']['selitems']);
	$err = false;
//	delete files & check for errors
	for($i = 0;$i < $cnt;++$i):
		$items[$i] = $GLOBALS['__POST']['selitems'][$i];
		$abs = get_abs_item($dir,$items[$i]);
		if(!@file_exists(get_abs_item($dir,$items[$i]))):
			$error[$i]=gtext("This item doesn't exist.");
			$err=true;
			continue;
		endif;
		if(!get_show_item($dir,$items[$i])):
			$error[$i] = gtext('You are not allowed to access this item.');
			$err=true;
			continue;
		endif;
//		Delete
		$ok = remove(get_abs_item($dir,$items[$i]));
		if($ok === false):
			$error[$i]= gtext('Deleting failed.');
			$err = true;
			continue;
		endif;
		$error[$i] = null;
	endfor;
	if($err):
//		there were errors
		$err_msg = '';
		for($i = 0;$i < $cnt;++$i):
			if($error[$i] == null):
				continue;
			endif;
			$err_msg .= $items[$i] . ' : ' . $error[$i] . "<br>\n";
		endfor;
		show_error($err_msg);
	endif;
	header('Location: '.make_link('list',$dir,null));
}
