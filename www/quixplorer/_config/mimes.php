<?php
/*
	mimes.php

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
//	editable files:
$GLOBALS['editable_ext'] = [
	'\.txt$|\.php$|\.php3$|\.phtml$|\.inc$|\.sql$|\.pl$',
	'\.htm$|\.html$|\.shtml$|\.dhtml$|\.xml$',
	'\.js$|\.css$|\.cgi$|\.cpp$\.c$|\.cc$|\.cxx$|\.hpp$|\.h$',
	'\.pas$|\.p$|\.java$|\.py$|\.sh$\.tcl$|\.tk$',
	'\.dxs$|\.uni$',
	'\.htaccess$'
];
//	----------------------------------------------------------------------------
//	unzipable files:
$GLOBALS['unzipable_ext'] = [
	'\.zip$|\.gz$|\.tar$|\.bz2$|\.tgz$'
];
//	----------------------------------------------------------------------------
//	image files:
$GLOBALS['images_ext'] = '\.png$|\.bmp$|\.jpg$|\.jpeg$|\.gif$|\.tif$';
//	----------------------------------------------------------------------------
//	mime types: (description,image,extension,type)
$GLOBALS['super_mimes'] = [
//	dir, exe, file
	'dir' => [\gettext('Directory'),'filetypes/folder_2.png','','dir'],
	'exe' => [\gettext('Executable File'),'exe.gif','\.exe$|\.com$|\.bin$','','exe'],
	'file' => [\gettext('File'),'filetypes/icon_generic.gif','','file'],
	'link' => [\gettext('Link'),'filetypes/icon_generic.gif','','link']
];
$GLOBALS['used_mime_types'] = [
//	text
	'text' => [\gettext('Text File'),'filetypes/document-text.png','\.txt$|\.htaccess$|\.plexignore$','text'],
//	programming
	'php' => [\gettext('PHP Script'),'filetypes/page_white_php.png','\.php$|\.php3$|\.phtml$|\.inc$|\.dxs$|\.uni$','php'],
	'sql' => [\gettext('SQL File'),'src.gif','\.sql$','sql'],
	'perl' => [\gettext('PERL Script'),'pl.gif','\.pl$','pl'],
	'html' => [\gettext('HTML Page'),'html.gif','\.htm$|\.html$|\.shtml$|\.dhtml$','html'],
	'xml' => [\gettext('XML File'),'filetypes/icon_xml.gif','\.xml$','xml'],
	'js' => [\gettext('Javascript File'),'filetypes/icon_js.gif','\.js$','js'],
	'css' => [\gettext('CSS File'),'src.gif','\.css$','css'],
	'cgi' => [\gettext('CGI Script'),'exe.gif','\.cgi$','cgi'],
	'py' => [\gettext('Python Script'),'py.gif','\.py$','py'],
	'sh' => [\gettext('Shell Script'),'sh.gif','\.sh$','sh'],
//	C++
	'c' => [\gettext('C File'),'filetypes/page_white_c.png','\.c$','c'],
	'cpps' => [\gettext('C++ Source File'),'filetypes/page_white_cplusplus.png','\.cpp$|\.cc$|\.cxx$','cpp'],
	'cpph' => [\gettext('C++ Header File'),'h.gif','\.hpp$|\.h$','cpp'],
//	Java
	'javas' => [\gettext('Java Source File'),'java.gif','\.java$','java'],
	'javac' => [\gettext('Java Class File'),'java.gif','\.class$|\.jar$','java'],
//	Pascal
	'pas' => [\gettext('Pascal File'),'src.gif','\.p$|\.pas$','pas'],
//	images
	'gif' => [\gettext('GIF Image'),'filetypes/picture_2.png','\.gif$','gif'],
	'jpg' => [\gettext('JPG Image'),'filetypes/picture_2.png','\.jpg$|\.jpeg$','jpg'],
	'bmp' => [\gettext('BMP Image'),'filetypes/picture_2.png','\.bmp$','bmp'],
	'png' => [\gettext('PNG Image'),'filetypes/picture_2.png','\.png$','png'],
	'tif' => [\gettext('TIF Image'),'filetypes/picture_2.png','\.tif$|\.tiff$','tif'],
//	PSD
	'psd' => [\gettext('Photoshop File'),'filetypes/icon_photoshop.gif','\.psd$','psd'],
//	compressed
	'zip' => [\gettext('ZIP Archive'),'filetypes/compress.png','\.zip$','zip'],
	'tar' => [\gettext('TAR Archive'),'tar.gif','\.tar$','tar'],
	'gzip' => [\gettext('GZIP Archive'),'tgz.gif','\.tgz$|\.gz$','gzip'],
	'bzip2' => [\gettext('BZIP2 Archive'),'tgz.gif','\.bz2$','bzip2'],
	'rar' => [\gettext('RAR Archive'),'tgz.gif','\.rar$','rar'],
//	'deb' => [\gettext('Debian Package File'),'package.gif','\.deb$','deb'],
//	'rpm' => [\gettext('Redhat Package File'),'package.gif','\.rpm$','rpm'],
//	music
	'mp3' => [\gettext('MP3 Audio File'),'filetypes/music.png','\.mp3$','mp3'],
	'flac' => [\gettext('FLAC Audio File'),'flac.gif','\.flac$','flac'],
	'wav' => [\gettext('WAV Audio File'),'sound.gif','\.wav$','wav'],
	'midi' => [\gettext('MIDI File'),'midi.gif','\.mid$','mid'],
	'real' => [\gettext('RealAudio File'),'real.gif','\.rm$|\.ra$|\.ram$','real'],
	'dts' => [\gettext('DTS Audio File'),'sound.gif','\.dts$','dts'],
	'ac3' => [\gettext('AC3 Audio File'),'sound.gif','\.ac3$','ac3'],
	'ogg' => [\gettext('OGG Audio File'),'sound.gif','\.ogg$','ogg'],
	'play' => [\gettext('MP3 Playlist'),'mp3.gif','\.pls$|\.m3u$','m3u'],
//	movie
	'avi' => [\gettext('AVI File'),'filetypes/film_2.png','\.avi$','avi'],
	'mpg' => [\gettext('MPEG File'),'video.gif','\.mpg$|\.mpeg$','mpeg'],
	'mov' => [\gettext('MOV File'),'video.gif','\.mov$','mov'],
	'mkv' => [\gettext('MKV File'),'mkv.gif','\.mkv$','mkv'],
	'vob' => [\gettext('VOB File'),'vob.gif','\.vob$','vob'],
	'flash' => [\gettext('Flash File'),'flash.gif','\.swf$','swf'],
	'mp4' => [\gettext('MP4 File'),'filetypes/film_1.png','\.mp4$','mp4'],
	'ts' => [\gettext('TS File'),'filetypes/ts.png','\.ts$','ts'],
//	Micosoft / Adobe
	'word' => [\gettext('Word Document'),'filetypes/page_white_word_1.png','\.doc$|\.docx$','doc'],
	'excel' => [\gettext('Excel Sheet'),'filetypes/page_white_excel_1.png','\.xls$|\.xlsx$','xls'],
	'power' => [\gettext('Powerpoint Presentation'),'filetypes/page_white_powerpoint.png','\.ppt$|\.pptx$|\.pps$','ppt'],
	'pdf' => [\gettext('PDF File'),'filetypes/document-pdf.png','\.pdf$','pdf']
];
