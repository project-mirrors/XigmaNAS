<?php
/*
	ro.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2013 The NAS4Free Project <info@nas4free.org>.
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
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
// Romanian Language Module

$GLOBALS["charset"] = "UTF-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "d-m-Y H:i";
$GLOBALS["error_msg"] = array(

	// error
	"error"		=> "EROARE(I)",
	"back"			=> "�napoi",
	
	// root
	"home"			=> "Directorul implicit nu exist�, verific�-�i parametrii.",
	"abovehome"		=> "Directorul curent ar putea s� nu fie deasupra directorului implicit.",
	"targetabovehome"	=> "Directorul �int� ar putea s� nu fie deasupra directorului implicit.",
	
	// exist
	"direxist"		=> "Acest director nu exist�.",
	//"filedoesexist"	=> "Acest fi�ier exist� deja.",
	"fileexist"		=> "Acest fi�ier nu exist�.",
	"itemdoesexist"	=> "Acest element exist� deja.",
	"itemexist"		=> "Acest element nu exist�.",
	"targetexist"		=> "Directorul �int� nu exist�.",
	"targetdoesexist"	=> "Elementul �int� exist� deja.",
	
	// open
	"opendir"		=> "Nu pot deschide directorul.",
	"readdir"		=> "Nu pot citi directorul.",
	
	// access
	"accessdir"		=> "Nu ai permisiunea de a accesa acest director.",
	"accessfile"		=> "Nu ai permisiunea de a accesa acest fi�ier.",
	"accessitem"		=> "Nu e�ti autorizat s� accesezi acest element.",
	"accessfunc"		=> "Nu e�ti autorizat s� folose�ti aceast� func�ie.",
	"accesstarget"	=> "Nu e�ti autorizat s� accesezi directorul �int�.",
	
	// actions
	"permread"		=> "Ob�inerea permisiunii a e�uat.",
	"permchange"		=> "Schimbarea permisiunii a e�uat.",
	"openfile"		=> "Deschiderea fi�ierului a e�uat.",
	"savefile"		=> "Salvarea fi�ierului a e�uat.",
	"createfile"		=> "Crearea fi�ierului a e�uat.",
	"createdir"		=> "Crearea directorului a esuat.",
	"uploadfile"		=> "�nc�rcarea fi�ierului a e�uat.",
	"copyitem"		=> "Copierea a e�uat.",
	"moveitem"		=> "Mutarea fi�ierului a e�uat.",
	"delitem"		=> "�tergerea a e�uat.",
	"chpass"		=> "Schimbarea parolei a e�uat.",
	"deluser"		=> "�tergerea utilizatorului a e�uat.",
	"adduser"		=> "Ad�ugarea utilizatorului a e�uat.",
	"saveuser"		=> "Salvarea utilizatorului a e�uat.",
	"searchnothing"	=> "Trebuie s� define�ti ce trebuie c�utat.",
	
	// misc
	"miscnofunc"		=> "Func�ie indisponibil�.",
	"miscfilesize"	=> "Fi�ierul dep�e�te dimensiunea maxim�.",
	"miscfilepart"	=> "Fi�ierul a fost �nc�rcat par�ial.",
	"miscnoname"		=> "Trebuie s� furnizezi un nume.",
	"miscselitems"	=> "Nu ai selectat nici un element.",
	"miscdelitems"	=> "Sigur vrei s� �tergi acest(e) \"+num+\" element(e)?",
	"miscdeluser"		=> "Sigur vrei s� �tergi utilizatorul '\"+user+\"'?",
	"miscnopassdiff"	=> "Parola nou� nu difer� de cea curent�.",
	"miscnopassmatch"	=> "Parolele nu sunt identice.",
	"miscfieldmissed"	=> "Ai s�rit un c�mp important.",
	"miscnouserpass"	=> "Utilizator sau parol� incorect(�).",
	"miscselfremove"	=> "Nu te po�i �terge pe tine insu�i.",
	"miscuserexist"	=> "Utilizatorul exist� deja.",
	"miscnofinduser"	=> "Nu g�sesc utilizatorul.",
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "SCHIMBARE PERMISIUNI",
	"editlink"		=> "EDITARE",
	"downlink"		=> "DESC�RCARE",
	"download_selected"	=> "DOWNLOAD SELECTED FILES",
	"uplink"		=> "SUS",
	"homelink"		=> "ACAS�",
	"reloadlink"		=> "RE�NC�RCARE",
	"copylink"		=> "COPIERE",
	"movelink"		=> "MUTARE",
	"dellink"		=> "�TERGERE",
	"comprlink"		=> "ARHIV�",
	"adminlink"		=> "ADMIN",
	"logoutlink"		=> "DELOGARE",
	"uploadlink"		=> "�NC�RCARE",
	"searchlink"		=> "C�UTARE",
	"unziplink"		=> "UNZIP",
	
	// list
	"nameheader"		=> "Nume",
	"sizeheader"		=> "Dimensiune",
	"typeheader"		=> "Tip",
	"modifheader"		=> "Modificat",
	"permheader"		=> "Permisiuni",
	"actionheader"	=> "Ac�iuni",
	"pathheader"		=> "Cale",
	
	// buttons
	"btncancel"		=> "Anulare",
	"btnsave"		=> "Salvare",
	"btnchange"		=> "Modificare",
	"btnreset"		=> "Resetare",
	"btnclose"		=> "�nchide",
	"btncreate"		=> "Creeaz�",
	"btnsearch"		=> "Caut�",
	"btnupload"		=> "�nc�rcare",
	"btncopy"		=> "Copiere",
	"btnmove"		=> "Mutare",
	"btnlogin"		=> "Logare",
	"btnlogout"		=> "Delogare",
	"btnadd"		=> "Ad�ugare",
	"btnedit"		=> "Editare",
	"btnremove"		=> "�tergere",
	"btnunzip"		=> "Unzip",
	
	// actions
	"actdir"		=> "Director",
	"actperms"		=> "Schimbare permisiuni",
	"actedit"		=> "Editare fi�ier",
	"actsearchresults"	=> "C�utare rezultate",
	"actcopyitems"	=> "Copiere element(e)",
	"actcopyfrom"		=> "Copiere din /%s �n /%s ",
	"actmoveitems"	=> "Mutare element(e)",
	"actmovefrom"		=> "Mutare din /%s �n /%s ",
	"actlogin"		=> "Logare",
	"actloginheader"	=> "Logare pentru folosirea QuiXplorer",
	"actadmin"		=> "Administrare",
	"actchpwd"		=> "Schimbare parol�",
	"actusers"		=> "Utilizatori",
	"actarchive"		=> "Archivare element(e)",
	"actunzipitem"	=> "Extracting",
	"actupload"		=> "�nc�rcare fi�ier(e)",
	
	// misc
	"miscitems"		=> "Element(e)",
	"miscfree"		=> "Liber",
	"miscusername"	=> "Utilizator",
	"miscpassword"	=> "Parola",
	"miscoldpass"		=> "Parola veche",
	"miscnewpass"		=> "Parola nou�",
	"miscconfpass"	=> "Confirmare parol�",
	"miscconfnewpass"	=> "Confirmare parol� nou�",
	"miscchpass"		=> "Schimbare parol�",
	"mischomedir"		=> "Director implicit",
	"mischomeurl"		=> "URL implicit",
	"miscshowhidden"	=> "Arat� elementele ascunse",
	"mischidepattern"	=> "Ascunde elementul",
	"miscperms"		=> "Permisiuni",
	"miscuseritems"	=> "(nume, director implicit, arat� elementele ascunse, permisiuni, activ)",
	"miscadduser"		=> "ad�ugare utilizator",
	"miscedituser"	=> "editare utilizator '%s'",
	"miscactive"		=> "Activ",
	"misclang"		=> "Limba",
	"miscnoresult"	=> "Nu exist� rezultate disponibile.",
	"miscsubdirs"		=> "C�utare subdirectoare",
	"miscpermissions"	=> array(
					"read"		=> array("Read", "User may read and download a file"),
					"create" 	=> array("Write", "User may create a new file"),
					"change"	=> array("Change", "User may change (upload, modify) an existing file"),
					"delete"	=> array("Delete", "User may delete an existing file"),
					"password"	=> array("Change password", "User may change the password"),
					"admin"	=> array("Administrator", "Full access"),
			),
	"miscyesno"		=> array("Da","Nu","D","N"),
	"miscchmod"		=> array("Proprietar", "Grup", "Public"),
);
?>