<?php
/*
	mimes.php

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

$fm_globals = [];
//	----------------------------------------------------------------------------
//	editable files:
$fm_globals['editable_ext'] = '/\.(txt|php|php3|phtml|inc|sql|pl|htm|html|shtml|dhtml|xml|js|css|cgi|cpp|c|cc|cxx|hpp|h|pas|p|java|py|sh|tcl|tk|dxs|uni|htaccess)$/i';
//	----------------------------------------------------------------------------
//	unzipable files:
$fm_globals['unzipable_ext'] = '/\.(zip|gz|tar|bz2|tgz)$/i';
//	----------------------------------------------------------------------------
//	mime types: (description,image,extension,type)
$fm_globals['super_mimes'] = [
//	dir, exe, file
	'dir' => [gtext('Directory'),'filetypes/folder_2.png','','dir'],
	'exe' => [gtext('Executable File'),'exe.gif','/\.(exe|com|bin)$/i','','exe'],
	'file' => [gtext('File'),'filetypes/icon_generic.gif','','file'],
	'link' => [gtext('Link'),'filetypes/icon_generic.gif','','link']
];
$fm_globals['used_mime_types'] = [
//	text
	'text' => [gtext('Text File'),'filetypes/document-text.png','/\.(txt|htaccess|plexignore)$/i','text'],
//	programming
	'php' => [gtext('PHP Script'),'filetypes/page_white_php.png','/\.(php|php3|phtml|inc|dxs|uni)$/i','php'],
	'sql' => [gtext('SQL File'),'src.gif','/\.(sql)$/i','sql'],
	'perl' => [gtext('PERL Script'),'pl.gif','/\.(pl)$/i','pl'],
	'html' => [gtext('HTML Page'),'html.gif','/\.(htm|html|shtml|dhtml)$/i','html'],
	'xml' => [gtext('XML File'),'filetypes/icon_xml.gif','/\.(xml)$/i','xml'],
	'js' => [gtext('Javascript File'),'filetypes/icon_js.gif','/\.(js)$/i','js'],
	'css' => [gtext('CSS File'),'src.gif','/\.(css)$/i','css'],
	'cgi' => [gtext('CGI Script'),'exe.gif','/\.(cgi)$/i','cgi'],
	'py' => [gtext('Python Script'),'filetypes/document-text.png','/\.(py)$/i','py'],
	'sh' => [gtext('Shell Script'),'filetypes/document-text.png','/\.(sh)$/i','sh'],
//	C++
	'c' => [gtext('C File'),'filetypes/page_white_c.png','/\.(c)$/i','c'],
	'cpps' => [gtext('C++ Source File'),'filetypes/page_white_cplusplus.png','/\.(cpp|cc|cxx)$/i','cpp'],
	'cpph' => [gtext('C++ Header File'),'h.gif','/\.(hpp|h)$/i','cpp'],
//	Java
	'javas' => [gtext('Java Source File'),'java.gif','/\.(java)$/i','java'],
	'javac' => [gtext('Java Class File'),'java.gif','/\.(class|jar)$/i','java'],
//	Pascal
	'pas' => [gtext('Pascal File'),'src.gif','/\.(p|pas)$/i','pas'],
//	images
	'gif' => [gtext('GIF Image'),'filetypes/picture_2.png','/\.(gif)$/i','gif'],
	'jpg' => [gtext('JPG Image'),'filetypes/picture_2.png','/\.(jpg|jpeg)$/i','jpg'],
	'bmp' => [gtext('BMP Image'),'filetypes/picture_2.png','/\.(bmp)$/i','bmp'],
	'png' => [gtext('PNG Image'),'filetypes/picture_2.png','/\.(png)$/i','png'],
	'tif' => [gtext('TIF Image'),'filetypes/picture_2.png','/\.(tif|tiff)$/i','tif'],
//	PSD
	'psd' => [gtext('Photoshop File'),'filetypes/icon_photoshop.gif','/\.(psd)$/i','psd'],
//	compressed
	'zip' => [gtext('ZIP Archive'),'filetypes/compress.png','/\.(zip)$/i','zip'],
	'tar' => [gtext('TAR Archive'),'tar.gif','/\.(tar)$/i','tar'],
	'gzip' => [gtext('GZIP Archive'),'tgz.gif','/\.(tgz|gz)$/i','gzip'],
	'bzip2' => [gtext('BZIP2 Archive'),'tgz.gif','/\.(bz2)$/i','bzip2'],
	'rar' => [gtext('RAR Archive'),'tgz.gif','/\.(rar)$/i','rar'],
//	'deb' => [gtext('Debian Package File'),'package.gif','/\.(deb)$/i','deb'],
//	'rpm' => [gtext('Redhat Package File'),'package.gif','/\.(rpm)$/i','rpm'],
//	music
	'mp3' => [gtext('MP3 Audio File'),'filetypes/music.png','/\.(mp3)$/i','mp3'],
	'flac' => [gtext('FLAC Audio File'),'flac.gif','/\.(flac)$/i','flac'],
	'wav' => [gtext('WAV Audio File'),'sound.gif','/\.(wav)$/i','wav'],
	'midi' => [gtext('MIDI File'),'midi.gif','/\.(mid)$/i','mid'],
	'real' => [gtext('RealAudio File'),'real.gif','/\.(rm|ra|ram)$/i','real'],
	'dts' => [gtext('DTS Audio File'),'sound.gif','/\.(dts)$/i','dts'],
	'ac3' => [gtext('AC3 Audio File'),'sound.gif','/\.(ac3)$/i','ac3'],
	'ogg' => [gtext('OGG Audio File'),'sound.gif','/\.(ogg)$/i','ogg'],
	'play' => [gtext('MP3 Playlist'),'mp3.gif','/\.(pls|m3u)$/i','m3u'],
//	movie
	'avi' => [gtext('AVI File'),'filetypes/film_2.png','/\.(avi)$/i','avi'],
	'mpg' => [gtext('MPEG File'),'video.gif','/\.(mpg|mpeg)$/i','mpeg'],
	'mov' => [gtext('MOV File'),'video.gif','/\.(mov)$/i','mov'],
	'mkv' => [gtext('MKV File'),'mkv.gif','/\.(mkv)$/i','mkv'],
	'vob' => [gtext('VOB File'),'vob.gif','/\.(vob)$/i','vob'],
	'flash' => [gtext('Flash File'),'flash.gif','/\.(swf)$/i','swf'],
	'mp4' => [gtext('MP4 File'),'filetypes/film_1.png','/\.(mp4)$/i','mp4'],
	'ts' => [gtext('TS File'),'filetypes/ts.png','/\.(ts)$/i','ts'],
//	Micosoft / Adobe
	'word' => [gtext('Word Document'),'filetypes/page_white_word_1.png','/\.(doc|docx)$/i','doc'],
	'excel' => [gtext('Excel Sheet'),'filetypes/page_white_excel_1.png','/\.(xls|xlsx)$/i','xls'],
	'power' => [gtext('Powerpoint Presentation'),'filetypes/page_white_powerpoint.png','/\.(ppt|pptx|pps)$/i','ppt'],
	'pdf' => [gtext('PDF File'),'filetypes/document-pdf.png','/\.(pdf)$/i','pdf']
];
