<?php
/*
	filemanager.php

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
/*------------------------------------------------------------------------------
			QuiXplorer v2.5.8 Modified for XigmaNAS
------------------------------------------------------------------------------*/
$pgperm['allowuser'] = true;

require_once 'autoload.php';
require_once 'auth.inc';
require_once 'guiconfig.inc';

use common\arr;

//	check if service is enabled
$sphere = arr::make_branch($config,'system');
$test = $sphere['disablefm'] ?? false;
$disablefm = is_bool($test) ? $test : true;
if($disablefm):
	http_response_code(403);
	Session::destroy();
	exit;
endif;
umask(002); // Added to make created files/dirs group writable
require_once 'fm_qx.php';
require_once 'fm_init.php';

global $action;

$current_dir = qx_request('dir','');
switch($action):
	case 'edit':
//		edit file
		require 'fm_edit_editarea.php';
		edit_file($current_dir,$GLOBALS['item']);
		break;
	case 'delete':
//		delete items
		require 'fm_del.php';
		del_items($current_dir);
		break;
	case 'copy':
//		copy items
	case 'move':
//		move items
		require 'fm_copy_move.php';
		copy_move_items($current_dir);
		break;
	case 'download':
//		download item
		ob_start(); // prevent unwanted output
		require 'fm_down.php';
		ob_end_clean(); // get rid of cached unwanted output
		global $item;
		if ($item == ''):
			show_error(gtext("You haven't selected any item(s)."));
		endif;
		download_item($current_dir,$item);
		ob_start(false); // prevent unwanted output
		exit;
		break;
	case 'download_selected':
//		download selected items
		ob_start(); // prevent unwanted output
		require 'fm_down.php';
		ob_end_clean(); // get rid of cached unwanted output
		download_selected($current_dir);
		ob_start(false); // prevent unwanted output
		exit;
		break;
	case 'unzip':
//		unzip item
		require 'fm_unzip.php';
		unzip_item($current_dir);
		break;
	case 'mkitem':
//		create item
		require 'fm_mkitem.php';
		make_item($current_dir);
		break;
	case 'chmod':
//		change item permission
		require 'fm_chmod.php';
		chmod_item($current_dir,$GLOBALS['item']);
		break;
	case 'search':
//		search for items
		require 'fm_search.php';
		search_items($current_dir);
		break;
	case 'arch':
//		create archive
		require 'fm_archive.php';
		archive_items($current_dir);
		break;
	default:
		require 'fm_list.php';
		list_dir($current_dir);
		break;
endswitch;
show_footer();
