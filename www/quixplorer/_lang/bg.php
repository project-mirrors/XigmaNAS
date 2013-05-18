<?php
/*
	bg.php
	
	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2013 The NAS4Free Project <info@nas4free.org>.
	All rights reserved.

	Portions of Quixplorer (http://quixplorer.sourceforge.net).
	Author: The QuiX project.

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
	of the authors and should not be interpreted as representing official policies, 
	either expressed or implied, of the NAS4Free Project.
*/
// Bulgarian Language Module

$GLOBALS["charset"] = "UTF-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(

      // error
      "error"			=> "������(�)",
      "back"			=> "�����",
      
      // root
      "home"			=> "��������� ���������� �� ����������, ��������� ������ ���������.",
      "abovehome"		=> "�������� ���������� �� ���� �� ���� ����� ���������.",
      "targetabovehome"	=> "�������� ���������� �� ���� �� ���� ����� ���������.",

      // exist
      "direxist"		=> "������������ �� ����������",
      //"filedoesexist"	=> "���� � ���� ��� ���� ����������",
      "fileexist"		=> "����� ���� �� ����������",
      "itemdoesexist"	=> "����� ����� ���� ����������",
      "itemexist"		=> "����� ����� �� ����������",
      "targetexist"		=> "�������� ���������� �� ����������",
      "targetdoesexist"	=> "�������� ����� �� ����������",
      
      // open
      "opendir"		=> "������������ �� ���� �� ���� ��������",
      "readdir"		=> "������������ �� ���� �� ���� ���������",

      // access
      "accessdir"		=> "������ ������ �� ���� ����������",
      "accessfile"		=> "������ ������ �� ���� ����",
      "accessitem"		=> "������ ������ �� ���� �����",
      "accessfunc"		=> "������ ����� �� �������� ���� �������",
      "accesstarget"		=> "������ ������ �� �������� ����������",

      // actions
      "permread"		=> "������ ��� ���������� �� ����� �� ������",
      "permchange"		=> "������ ��� ����� ����� �� ������",
      "openfile"		=> "������ ��� �������� �� ����",
      "savefile"		=> "������ ��� ����� �� ����",
      "createfile"		=> "������ ��� ��������� �� ����",
      "createdir"		=> "������ ��� ��������� �� ����������",
      "uploadfile"		=> "������ ��� ������� �� ����",
      "copyitem"		=> "������ ��� ��������",
      "moveitem"		=> "������ ��� ������������",
      "delitem"		=> "������ ��� ���������",
      "chpass"		=> "������ ��� ������� �� ������",
      "deluser"		=> "������ ��� ��������� �� ����������",
      "adduser"		=> "������ ��� ��������� �� ����������",
      "saveuser"		=> "������ ��� ����� �� ����������",
      "searchnothing"	=> "��������� ������ �� �������",
      
      // misc
      "miscnofunc"		=> "���������� �������",
      "miscfilesize"		=> "�������� ���������� ������ �� �����",
      "miscfilepart"		=> "����� � ����� ��������",
      "miscnoname"		=> "������ �� �������� ���",
      "miscselitems"		=> "�� ��� ������� �����(�)",
      "miscdelitems"		=> "������� �� ��� �� ������ �� �������� ���� \"+num+\" �����(�)?",
      "miscdeluser"		=> "������� �� ��� �� ������ �� �������� ���������� '\"+user+\"'?",
      "miscnopassdiff"	=> "������ ������ �� �� �������� �� ����������",
      "miscnopassmatch"	=> "�������� �� ��������",
      "miscfieldmissed"	=> "���������� ��� �� ��������� ����� ����",
      "miscnouserpass"	=> "������ ��� ��� ������",
      "miscselfremove"	=> "�� ������ �� �������� ����������� �� ������",
      "miscuserexist"	=> "������������ ���� ����������",
      "miscnofinduser"	=> "������������ �� ���� �� ���� ������",
);
$GLOBALS["messages"] = array(
      // links
      "permlink"		=> "������� ����� �� ������",
      "editlink"		=> "����������",
      "downlink"		=> "�������",
	"download_selected"	=> "DOWNLOAD SELECTED FILES",
      "uplink"		=> "������",
      "homelink"		=> "������",
      "reloadlink"		=> "������",
      "copylink"		=> "�������",
      "movelink"		=> "��������",
      "dellink"		=> "������",
      "comprlink"		=> "���������",
      "adminlink"		=> "��������������",
      "logoutlink"		=> "�����",
      "uploadlink"		=> "�������",
      "searchlink"		=> "�����",
	"unziplink"		=> "UNZIP",
      
      // list
      "nameheader"		=> "����",
      "sizeheader"		=> "������",
      "typeheader"		=> "���",
      "modifheader"		=> "��������",
      "permheader"		=> "�����",
      "actionheader"		=> "��������",
      "pathheader"		=> "���",
      
      // buttons
      "btncancel"		=> "������",
      "btnsave"		=> "�������",
      "btnchange"		=> "�������",
      "btnreset"		=> "�������",
      "btnclose"		=> "�������",
      "btncreate"		=> "������",
      "btnsearch"		=> "�����",
      "btnupload"		=> "�������",
      "btncopy"		=> "�������",
      "btnmove"		=> "��������",
      "btnlogin"		=> "����",
      "btnlogout"		=> "�����",
      "btnadd"		=> "������",
      "btnedit"		=> "����������",
      "btnremove"		=> "������",
      "btnunzip"		=> "Unzip",
      
      // actions
      "actdir"		=> "�����",
      "actperms"		=> "������� �� �����",
      "actedit"		=> "���������� ����",
      "actsearchresults"	=> "��������� �� �������",
      "actcopyitems"		=> "������� �����(�)",
      "actcopyfrom"		=> "������� �� /%s � /%s ",
      "actmoveitems"		=> "�������� �����(�)",
      "actmovefrom"		=> "�������� �� /%s � /%s ",
      "actlogin"		=> "����",
      "actloginheader"	=> "���� �� �� ������� QuiXplorer",
      "actadmin"		=> "��������������",
      "actchpwd"		=> "����� ������",
      "actusers"		=> "�����������",
      "actarchive"		=> "��������� ������(�)",
      "actunzipitem"		=> "Extracting",
      "actupload"		=> "������� ����(���)",
      
      // misc
      "miscitems"		=> "�����(�)",
      "miscfree"		=> "��������",
      "miscusername"		=> "����������",
      "miscpassword"		=> "������",
      "miscoldpass"		=> "����� ������",
      "miscnewpass"		=> "���� ������",
      "miscconfpass"		=> "���������� ������",
      "miscconfnewpass"	=> "���������� ���� ������",
      "miscchpass"		=> "������� ������",
      "mischomedir"		=> "������� ����������",
      "mischomeurl"		=> "������� URL",
      "miscshowhidden"	=> "�������� ������ ������",
      "mischidepattern"	=> "����� �������",
      "miscperms"		=> "�����",
      "miscuseritems"	=> "(���, ������� ����������, �������� ������ ������, ����� �� ������, �������)",
      "miscadduser"		=> "������ ����������",
      "miscedituser"		=> "���������� ���������� '%�'",
      "miscactive"		=> "�������",
      "misclang"		=> "����",
      "miscnoresult"		=> "���� ���������",
      "miscsubdirs"		=> "����� � �������������",
	"miscpermissions"	=> array(
					"read"		=> array("Read", "User may read and download a file"),
					"create" 	=> array("Write", "User may create a new file"),
					"change"	=> array("Change", "User may change (upload, modify) an existing file"),
					"delete"	=> array("Delete", "User may delete an existing file"),
					"password"	=> array("Change password", "User may change the password"),
					"admin"		=> array("Administrator", "Full access"),
			),
      "miscyesno"		=> array("��","��","�","�"),
      "miscchmod"		=> array("����������", "�����", "������������"),
);
?>
