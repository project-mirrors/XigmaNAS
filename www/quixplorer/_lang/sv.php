<?php
/*
	sv.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
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
// Swedish Language Module

$GLOBALS["charset"] = "UTF-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "Y/m/d H:i";
$GLOBALS["error_msg"] = array(

	// error
	"error"			=> "FEL(S)",
	"back"			=> "Tillbaka",

	// root
	"home"			=> "Hemkatalogen finns ej, kontrollera dina isnt�llningar.",
	"abovehome"		=> "Aktuell katalog kan inte vara ovanf�r hemkatalogen.",
	"targetabovehome"	=> "M�lkatalogen kan inte vara ovanf�r hemkatalogen.",

	// exist
	"direxist"		=> "Denna katalog finns ej.",
	"fileexist"		=> "Denna fil finns inte.",
	"itemdoesexist"		=> "Detta objekt finns redan.",
	"itemexist"		=> "Detta objekt finns inte.",
	"targetexist"		=> "M�lkatalogen finns .",
	"targetdoesexist"	=> "M�lobjektet finns redan.",

	// open
	"opendir"		=> "Kan inte �ppna katalog.",
	"readdir"		=> "Kan inte l�sa katalog.",

	// access
	"accessdir"		=> "Du har inte r�ttigheter att komma �t denna katalog.",
	"accessfile"		=> "Du har inte r�ttigheter att komma �t denna fil.",
	"accessitem"		=> "Du har inte r�ttigheter att komma �t detta objekt.",
	"accessfunc"		=> "Du har inte r�ttigheter att anv�nda denna funktion.",
	"accesstarget"		=> "Du har inte r�ttigheter att komma �t m�lkatalog.",

	// actions
	"chmod_not_allowed"	=> '�ndring av r�ttigheter till NONE �r inte till�tet!',
	"permread"		=> "L�sning av r�ttighter misslyckades.",
	"permchange"		=> "�ndring av r�ttigheter misslyckades.",
	"openfile"		=> "�ppning av fill misslyckades.",
	"savefile"		=> "Misslyckades att spara filen.",
	"createfile"		=> "Misslyckades att skapa filen.",
	"createdir"		=> "Misslyckades att skapa katalog.",
	"uploadfile"		=> "Uppladdning av fil misslyckades.",
	"copyitem"		=> "Kopiering misslyckades.",
	"moveitem"		=> "Flytt misslyckades.",
	"delitem"		=> "Borttagning misslyckades.",
	"chpass"		=> "�ndring av l�senord misslyckades.",
	"deluser"		=> "Misslyckades att ta bort anv�ndare.",
	"adduser"		=> "Misslyckades att l�gga till anv�ndare.",
	"saveuser"		=> "Misslyckades att spara anv�ndare.",
	"searchnothing"		=> "Du m�ste ange n�got att s�ka efter.",

	// misc
	"miscnofunc"		=> "Funktion saknas.",
	"miscfilesize"		=> "Filen �verskrider maxstorlek.",
	"miscfilepart"		=> "Filen endast delvis uppladdad.",
	"miscnoname"		=> "Du m�ste ange ett namn.",
	"miscselitems"		=> "Du har inte valt n�got (n�gra) objekt.",
	"miscdelitems"		=> "�r du s�ker p� att du vill ta bort dessa \"+num+\" objekt?",
	"miscdeluser"		=> "�r du s�ker p� att du vill ta bort anvv�ndaere '\"+user+\"'?",
	"miscnopassdiff"	=> "Det nya l�seordet skiljer sig inte fr�n det gamla.",
	"miscnopassmatch"	=> "L�senorden matchar inte.",
	"miscfieldmissed"	=> "Du missade ett viktigt f�lt.",
	"miscnouserpass"	=> "Anv�ndarnamn eller l�senord �r felaktigt.",
	"miscselfremove"	=> "Du kan inte ta bort dig sj�lv.",
	"miscuserexist"		=> "Anv�ndaren finns redan.",
	"miscnofinduser"	=> "Hittar inte anv�ndaren.",
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "�NDRA R�TTIGHETER",
	"editlink"		=> "�NDRA",
	"downlink"		=> "LADDA NER",
	"download_selected"	=> "LADDA NER VALDA FILER",
	"uplink"		=> "UPP",
	"homelink"		=> "HEM",
	"reloadlink"		=> "LADDA OM",
	"copylink"		=> "KOPIERA",
	"movelink"		=> "FLYTTA",
	"dellink"		=> "TA BORT",
	"comprlink"		=> "ARKIVERA",
	"adminlink"		=> "ADMIN",
	"logoutlink"		=> "LOGGA UT",
	"uploadlink"		=> "LADDA UPP",
	"searchlink"		=> "S�K",
	"unziplink"		=> "PACKA UPP",

	// list
	"nameheader"		=> "Namn",
	"sizeheader"		=> "Storlek",
	"typeheader"		=> "Typ",
	"modifheader"		=> "�ndrad",
	"permheader"		=> "R�ttigheter",
	"actionheader"		=> "�tg�rder",
	"pathheader"		=> "S�kv�g",

	// buttons
	"btncancel"		=> "Avbryt",
	"btnsave"		=> "Spara",
	"btnchange"		=> "�ndra",
	"btnreset"		=> "�terst�ll",
	"btnclose"		=> "St�ng",
	"btncreate"		=> "Skapa",
	"btnsearch"		=> "S�k",
	"btnupload"		=> "Ladda upp",
	"btncopy"		=> "Kopiera",
	"btnmove"		=> "Flytta",
	"btnlogin"		=> "Logga in",
	"btnlogout"		=> "Logga ut",
	"btnadd"		=> "L�gg till",
	"btnedit"		=> "�ndra",
	"btnremove"		=> "Ta bort",
	"btnunzip"		=> "Packa upp",

	// actions
	"actdir"		=> "Katalog",
	"actperms"		=> "�ndra r�ttigheter",
	"actedit"		=> "�ndra fil",
	"actsearchresults"	=> "S�kresultat",
	"actcopyitems"		=> "Kopiera objekt",
	"actcopyfrom"		=> "Kopiera fr�n/%s till /%s ",
	"actmoveitems"		=> "Flytta objekt",
	"actmovefrom"		=> "Flytta objekt fr�n /%s till /%s ",
	"actlogin"		=> "Logga in",
	"actloginheader"	=> "Logga in f�r att anv�nda Filhanteraren",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "�ndra l�senord",
	"actusers"		=> "Anv�ndare",
	"actarchive"		=> "Arkivera objekt",
	"actunzipitem"		=> "Packar upp",
	"actupload"		=> "Ladda upp fil(er)",

	// misc
	"miscitems"		=> "Objekt",
	"miscfree"		=> "Ledigt",
	"miscusername"		=> "Anv�ndarnamn",
	"miscpassword"		=> "L�senord",
	"miscoldpass"		=> "Gammalt l�senord",
	"miscnewpass"		=> "Nytt l�senord",
	"miscconfpass"		=> "Bekr�fta l�senord",
	"miscconfnewpass"	=> "Bekr�fta nytt l�senord",
	"miscchpass"		=> "Byt l�senord",
	"mischomedir"		=> "Hemkatalog",
	"mischomeurl"		=> "Hem URL",
	"miscshowhidden"	=> "Visa dolda objekt",
	"mischidepattern"	=> "G�m m�nster",
	"miscperms"		=> "R�ttigheter",
	"miscuseritems"		=> "(namn, hemkatalog, visa dolda objekt, r�ttigheter, aktiva)",
	"miscadduser"		=> "l�gg till anv�ndare",
	"miscedituser"		=> "�ndra anv�ndare'%s'",
	"miscactive"		=> "Aktiv",
	"misclang"		=> "Spr�k",
	"miscnoresult"		=> "Inga resultat tillg�ngliga.",
	"miscsubdirs"		=> "S�k i underkataloger",
	"miscpermissions"	=> array(
					"l�s"		=> array("L�s", "Anv�ndaren f�r l�sa och ladda ner filen"),
					"create" 	=> array("Skriv", "Anv�ndaren f�r skapa en ny fil"),
					"�ndra"		=> array("�ndra", "Anv�ndaren f�r �ndra och ladda upp en extiterande fil"),
					"ta bort"	=> array("Ta bort", "Anv�ndaren f�r ta bort en fil"),
					"l�senord"	=> array("�ndra l�senord", "Anv�ndare f�r �ndra l�senordet"),
					"admin"		=> array("Administrat�r", "Fulla r�ttigheter"),
			),
	"miscyesno"		=> array("Ja","Nej","J","N"),
	"miscchmod"		=> array("�gare", "Grupp", "Publik"),
);

?>