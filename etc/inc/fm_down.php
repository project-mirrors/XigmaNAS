<?php
/*
	down.php

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
require_once 'fm_archive.php';
require_once 'fm_permissions.php';
require_once 'fm_qxpage.php';

/**
 *	download_selected
 *	@param string $dir
 **/
function download_selected($dir) {
	require_once 'fm_archive.php';
	$items = qxpage_selected_items();
	_download_items($dir,$items);
}
/**
 *	download file
 *	@param string $dir
 *	@param string $item
 */
function download_item($dir,$item) {
	_download_items($dir,[$item]);
}
/**
 *	_download_items
 *	@param string $dir
 *	@param array $items
 */
function _download_items($dir,$items) {
//	check if user selected any items to download
	_debug("count items: '$items[0]'");
	if(count($items) == 0):
		show_error(gtext("You haven't selected any item(s)."));
	endif;
//	check if user has permissions to download this file
	if(! _is_download_allowed($dir,$items)):
		show_error(gtext('You are not allowed to access this item.'));
	endif;
//	if we have exactly one file and this is a real file, we directly download
	if(count($items) == 1 && get_is_file($dir,$items[0])):
		$abs_item = get_abs_item($dir,$items[0]);
		_download($abs_item,$items[0]);
	endif;
//	otherwise we do the zip download
	zip_download(get_abs_dir($dir),$items);
}
/**
 *	Downloads a file with X-Sendfile
 *	@param string $file Full path name + file name + extension
 *	@param string $localname File name + extension
 */
function _download(string $file,string $localname) {
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header(sprintf('Content-Disposition: attachment; filename="%s"',$localname));
	header('Content-Type: application/octet-stream');
	header('Content-Length: ' . filesize($file));
	header(sprintf('X-Sendfile: %s',$file));
	exit;
}
/**
 *	_is_download_allowed
 *	@param string $dir
 *	@param array $items
 *	@return boolean
 */
function _is_download_allowed($dir,$items) {
	foreach($items as $file):
		if(!permissions_grant($dir,$file,'read')):
			return false;
		endif;
		if(!get_show_item($dir,$file)):
			return false;
		endif;
		if(!file_exists(get_abs_item($dir,$file))):
			return false;
		endif;
	endforeach;
	return true;
}
