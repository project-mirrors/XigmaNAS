<?php
/*
	conf.php

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
//	Configuration Variables
//	Language
$GLOBALS['language'] = $_SESSION['userlang'] ?? $config['system']['language'];
//	The filename of the QuiXplorer script:
$GLOBALS['script_name'] = sprintf('%s://%s%s',$config['system']['webgui']['protocol'],$_SERVER['HTTP_HOST'],$_SERVER['PHP_SELF']);
// allow Zip, Tar, TGz -> Only (experimental) Zip-support
$GLOBALS['zip'] = false;
$GLOBALS['tar'] = false;
$GLOBALS['tgz'] = false;
$GLOBALS['uploader'] = 'false';
//	Global User Variables (used when $require_login==false)
//	the home directory for the filemanager:
//	use forward slashed to seperate directories ('/')
//	not '\' or '\\', no trailing '/'
//	don't use the root directory as home_dir!
$GLOBALS['home_dir'] = '/';
//	the url corresponding with the home directory: (no trailing '/')
$GLOBALS['home_url'] = sprintf('%s://%s',$config['system']['webgui']['protocol'],$_SERVER['HTTP_HOST']);
//	show hidden files in QuiXplorer: (hide files starting with '.', as in Linux/UNIX)
$GLOBALS['show_hidden'] = true;
//	filenames not allowed to access: (uses PCRE regex syntax)
$GLOBALS['no_access'] = '^\.ht';
//	The title which is displayed in the browser
$GLOBALS['site_name'] = 'My Download Server';
