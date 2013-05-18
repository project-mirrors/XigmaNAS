<?php
/*
	pl.php

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
// Polish Language Module

$GLOBALS["charset"] = "UTF-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "d-m-Y H:i";
$GLOBALS["error_msg"] = array(

	// error
	"error"		=> "B��D(�DY)",
	"back"			=> "Z Powrotem",

	// root
	"home"			=> "Katalog domowy nie istnieje. Sprawd� swoje ustawienia.",
	"abovehome"		=> "Obecny katalog nie mo�e by� powy�ej katalogu domowego.",
	"targetabovehome"	=> "Katalog docelowy nie mo�e by� powy�ej katalogu domowego.",

	// exist
	"direxist"		=> "Ten katalog nie istnieje.",
	//"filedoesexist"	=> "This file already exists.",
	"fileexist"		=> "Ten plik nie istnieje.",
	"itemdoesexist"		=> "Ta pozycja ju� istnieje.",
	"itemexist"		=> "Ta pozycja nie istnieje.",
	"targetexist"		=> "Katalog docelowy nie istnieje.",
	"targetdoesexist"	=> "Pozycja docelowa ju� istnieje.",

	// open
	"opendir"		=> "Nie mog� otworzy� katalogu.",
	"readdir"		=> "Nie mog� odczyta� katalogu.",

	// access
	"accessdir"		=> "Nie masz dost�pu do tego katalogu.",
	"accessfile"		=> "Nie masz dost�pu do tego pliku.",
	"accessitem"		=> "Nie masz dost�pu do tej pozycji.",
	"accessfunc"		=> "Nie masz dost�pu do tej funkcji.",
	"accesstarget"	=> "Nie masz dost�pu do katalogu docelowego.",

	// actions
	"chmod_not_allowed"  => 'Changing Permissions to NONE is not allowed!',
	"permread"		=> "Pobranie uprawnie� nie uda�o si�.",
	"permchange"		=> "Zmiana uprawnie� si� nie powiod�a.",
	"openfile"		=> "Otawrcie pliku si� nie powiod�o.",
	"savefile"		=> "Zapis pliku si� nie powiod�o.",
	"createfile"		=> "Utworzenie pliku si� nie powiod�o.",
	"createdir"		=> "Utworzenie katalogu si� nie powiod�o.",
	"uploadfile"		=> "Wrzucanie pliku na serwer si� nie powiod�o.",
	"copyitem"		=> "Kopiowanie si� nie powiod�o.",
	"moveitem"		=> "Przenoszenie si� nie powiod�o.",
	"delitem"		=> "Usuwanie si� nie powiod�o.",
	"chpass"		=> "Zmiana has�a nie powiod�a si�.",
	"deluser"		=> "Usuwanie u�ytkowika si� nie powiod�o.",
	"adduser"		=> "Dodanie u�ytkownika si� nie powiod�o.",
	"saveuser"		=> "Zapis u�ytkownika si� nie powiod�o.",
	"searchnothing"	=> "Musisz dostarczy� czego� do szukania.",

	// misc
	"miscnofunc"		=> "Funkcja niedost�pna.",
	"miscfilesize"	=> "Rozmiar pliku przekroczy� maksymaln� warto��.",
	"miscfilepart"	=> "Plik zosta� za�adowany tylko cz�ciowo.",
	"miscnoname"		=> "Musisz nada� nazw�.",
	"miscselitems"	=> "Nie zaznaczy�e� �adnej pozycji.",
	"miscdelitems"	=> "Jeste� pewny �e chcesz usun�� te (\"+num+\") pozycje?",
	"miscdeluser"		=> "Jeste� pewny �e chcesz usun�� u�ytkownika '\"+user+\"'?",
	"miscnopassdiff"	=> "Nowe has�o nie r�ni si� od obecnego.",
	"miscnopassmatch"	=> "Podane has�a r�ni� si�.",
	"miscfieldmissed"	=> "Opuszczono wa�ne pole.",
	"miscnouserpass"	=> "U�ytkownik i has�o s� niezgodne.",
	"miscselfremove"	=> "Nie mo�esz siebie usun��.",
	"miscuserexist"	=> "U�ytkownik ju� istnieje.",
	"miscnofinduser"	=> "U�ytkownika nie znaleziono.",
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "ZMIANA UPRAWNIE�",
	"editlink"		=> "EDYCJA",
	"downlink"		=> "DOWNLOAD",
	"download_selected"	=> "DOWNLOAD SELECTED FILES",
	"uplink"		=> "KATALOG WY�EJ",
	"homelink"		=> "KATALOG DOMOWY",
	"reloadlink"		=> "OD�WIE�",
	"copylink"		=> "KOPIUJ",
	"movelink"		=> "PRZENIE�",
	"dellink"		=> "USU�",
	"comprlink"		=> "ARCHIWIZUJ",
	"adminlink"		=> "ADMINISTRUJ",
	"logoutlink"		=> "WYLOGUJ",
	"uploadlink"		=> "WRZU� PLIK NA SERWER - UPLOAD",
	"searchlink"		=> "SZUKAJ",
	"unziplink"		=> "UNZIP",

	// list
	"nameheader"		=> "Nazwa",
	"sizeheader"		=> "Rozmiar",
	"typeheader"		=> "Typ",
	"modifheader"		=> "Zmodyfikowano",
	"permheader"		=> "Prawa dost�pu",
	"actionheader"	=> "Akcje",
	"pathheader"		=> "�cie�ka",

	// buttons
	"btncancel"		=> "Zrezygnuj",
	"btnsave"		=> "Zapisz",
	"btnchange"		=> "Zmie�",
	"btnreset"		=> "Reset",
	"btnclose"		=> "Zamknij",
	"btncreate"		=> "Utw�rz",
	"btnsearch"		=> "Szukaj",
	"btnupload"		=> "Wrzu� na serwer",
	"btncopy"		=> "Kopiuj",
	"btnmove"		=> "Przenie�",
	"btnlogin"		=> "Zaloguj",
	"btnlogout"		=> "Wyloguj",
	"btnadd"		=> "Dodaj",
	"btnedit"		=> "Edycja",
	"btnremove"		=> "Usu�",
	"btnunzip"		=> "Unzip",

	// actions
	"actdir"		=> "Katalog",
	"actperms"		=> "Zmiana uprawnie�",
	"actedit"		=> "Edycja pliku",
	"actsearchresults"	=> "Rezultaty szukania",
	"actcopyitems"	=> "Kopiuj pozycje",
	"actcopyfrom"		=> "Kpiuj z /%s do /%s ",
	"actmoveitems"	=> "Przenie� pozycje",
	"actmovefrom"		=> "Przenie� z /%s do /%s ",
	"actlogin"		=> "Nazwa u�ytkownika",
	"actloginheader"	=> "Zaloguj si� by u�ywa� QuiXplorer",
	"actadmin"		=> "Administracja",
	"actchpwd"		=> "Zmie� has�o",
	"actusers"		=> "U�ytkownicy",
	"actarchive"		=> "Pozycje zarchiwizowane",
	"actunzipitem"	=> "Extracting",
	"actupload"		=> "Wrzucanie na serwer- Upload",

	// misc
	"miscitems"		=> " -Ilo�c element�w",
	"miscfree"		=> "Wolnego miejsca",
	"miscusername"	=> "Nazwa u�ytkownika",
	"miscpassword"	=> "Has�o",
	"miscoldpass"		=> "Stare has�o",
	"miscnewpass"		=> "Nowe has�o",
	"miscconfpass"	=> "Potwierd� has�o",
	"miscconfnewpass"	=> "Potwierd� nowe has�o",
	"miscchpass"		=> "Zmie� has�o",
	"mischomedir"		=> "Katalog g��wny",
	"mischomeurl"		=> "URL Katalogu domowego",
	"miscshowhidden"	=> "Show hidden items",
	"mischidepattern"	=> "Hide pattern",
	"miscperms"		=> "Uprawnienia",
	"miscuseritems"	=> "(nazwa, katalog domowy, poka� pozycje ukryte, uprawnienia, czy aktywny)",
	"miscadduser"		=> "dodaj u�ytkownika",
	"miscedituser"	=> "edycja u�ytkownika '%s'",
	"miscactive"		=> "Aktywny",
	"misclang"		=> "J�zyk",
	"miscnoresult"	=> "Bez rezultatu.",
	"miscsubdirs"		=> "Szukaj w podkatalogach",
	"miscpermissions"	=> array(
					"read"		=> array("Read", "User may read and download a file"),
					"create" 	=> array("Write", "User may create a new file"),
					"change"	=> array("Change", "User may change (upload, modify) an existing file"),
					"delete"	=> array("Delete", "User may delete an existing file"),
					"password"	=> array("Change password", "User may change the password"),
					"admin"	=> array("Administrator", "Full access"),
			),
	"miscyesno"		=> array("Tak","Nie","T","N"),
	"miscchmod"		=> array("W�a�ciciel", "Grupa", "Publiczny"),
);
?>