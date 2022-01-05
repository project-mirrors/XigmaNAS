<?php
/*
	qx.php

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
function qx_language() {
	global $language;

	print $language;
}
function qx_title() {
	global $site_name;

	print $site_name;
}
function qx_img($image,$msg) {
    ?><img class="button" src="/images/fm_img/$image" alt="$msg" title="$msg" /><?php
}
function qx_user() {
	echo qx_user_s();
}
function qx_user_s() {
//	FIXME return real user
	return $_SESSION['uname'] ?? 'anonymous';
}
// @returns the relative path $rel to the current directory displayed.
function qx_directory($rel = null) {
	global $dir;

	return $dir . '/' . $rel;
}
function qx_grant($link) {
	global $dir;

	switch($link):
		case 'javascript:Move();': return permissions_grant($dir,null,'move');
		case 'javascript:Copy();': return permissions_grant($dir,null,'copy');
		case 'javascript:Delete();': return permissions_grant($dir,null,'delete');
		case 'javascript:Archive();': return true;
		case 'javascript:location.reload();': return true;
	endswitch;
	if(preg_match('/\?action=upload/', $link)):
		return permissions_grant($dir,null,'create') && get_cfg_var('file_uploads');
	endif;
	if(preg_match('/\?action=list/',$link)):
		return true;
	endif;
	return false;
}
function qx_page($pagename) {
    $pagefile = sprintf('fm_%s.php',$pagename);
    if(!file_exists($pagefile)):
		show_error(qx_msg_s('error.qxmissingpage'),$pagefile);
	endif;
    require_once 'fm_header.php';
    require_once "$pagefile";
    require_once 'fm_footer.php';
    exit;
}
function qx_request($var,$default) {
  return $_REQUEST[$var] ?? $default;
}
