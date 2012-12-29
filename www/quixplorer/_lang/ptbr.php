<?php
/*
	ptbr.php
	
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
// Portugu�s - Brasil Language Module

$GLOBALS["charset"] = "UTF-8";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "d/m/Y H:i";
$GLOBALS["error_msg"] = array(

	// error
	"error"		=> "ERRO(S)",
	"back"			=> "Voltar",
	
	// root
	"home"			=> "A pasta padr�o n�o existe. Entre em contato com o administrador.",
	"abovehome"		=> "A pasta atual n�o existe. Entre em contato com o administrador..",
	"targetabovehome"	=> "A pasta destino n�o existe.",
	
	// exist
	"direxist"		=> "Esta pasta n�o existe.",
	"filedoesexist"	=> "Este arquivo j� existe.",
	"fileexist"		=> "Este arquivo n�o existe.",
	"itemdoesexist"	=> "Item j� existente.",
	"itemexist"		=> "Este item n�o existe.",
	"targetexist"		=> "A pasta destino n�o existe.",
	"targetdoesexist"	=> "A pasta destino j� existe.",
	
	// open
	"opendir"		=> "Erro ao abrir a pasta.",
	"readdir"		=> "Erro ao ler a pasta.",
	
	// access
	"accessdir"		=> "Voc� n�o tem permiss�o para acessar esta pasta.",
	"accessfile"		=> "Voc� n�o tem permiss�o para acessar este arquivo.",
	"accessitem"		=> "Voc� n�o tem permiss�o para acessar este item.",
	"accessfunc"		=> "Voc� n�o tem permiss�o para acessar esta fun��o.",
	"accesstarget"	=> "Voc� n�o tem permiss�o para acessar esta pasta.",
	
	// actions
	"permread"		=> "Sem permiss�o.",
	"permchange"		=> "Sem permiss�o.",
	"openfile"		=> "Erro ao abrir arquivo.",
	"savefile"		=> "Erro ao salvar arquivo.",
	"createfile"		=> "Erro na cria��o do arquivo.",
	"createdir"		=> "Erro na cria��o da pasta.",
	"uploadfile"		=> "Erro no upload.",
	"copyitem"		=> "Erro ao copiar.",
	"moveitem"		=> "Erro ao mover.",
	"delitem"		=> "Erro ao deletar.",
	"chpass"		=> "Erro na troca de senha.",
	"deluser"		=> "Erro ao remover usu�rio.",
	"adduser"		=> "Erro ao adicionar usu�rio.",
	"saveuser"		=> "Erro ao salvar usu�rio.",
	"searchnothing"	=> "Digite algo para buscar.",
	
	// misc
	"miscnofunc"		=> "Fun��o indispon�vel.",
	"miscfilesize"	=> "Arquivo excedeu tamanho m�ximo permitido.",
	"miscfilepart"	=> "Arquivo enviado parcialmente.",
	"miscnoname"		=> "Voc� deve indicar um nome.",
	"miscselitems"	=> "N�o houve sele��o de item(s).",
	"miscdelitems"	=> "Deseja realmente apagar \"+num+\" item(s)?",
	"miscdeluser"		=> "Deseja realmente remover o usu�rio '\"+user+\"'?",
	"miscnopassdiff"	=> "A nova senha � igual a atual.",
	"miscnopassmatch"	=> "As senhas n�o correspondem.",
	"miscfieldmissed"	=> "Voc� esqueceu um campo importante.",
	"miscnouserpass"	=> "Usu�rio ou senha incorretos.",
	"miscselfremove"	=> "Voc� n�o pode remover.",
	"miscuserexist"	=> "Usu�rio j� existente.",
	"miscnofinduser"	=> "Usu�rio n�o encontrado.",
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "ALTERAR PERMISS�ES",
	"editlink"		=> "EDITAR",
	"downlink"		=> "DOWNLOAD",
	"uplink"		=> "ACIMA",
	"homelink"		=> "IN�CIO",
	"reloadlink"		=> "ATUALIZAR",
	"copylink"		=> "COPIAR",
	"movelink"		=> "MOVER",
	"dellink"		=> "REMOVER",
	"comprlink"		=> "COMPACTAR",
	"adminlink"		=> "ADMINISTRA��O",
	"logoutlink"		=> "SAIR",
	"uploadlink"		=> "ENVIAR",
	"searchlink"		=> "BUSCAR",
	
	// list
	"nameheader"		=> "Nome",
	"sizeheader"		=> "Tamanho",
	"typeheader"		=> "Tipo",
	"modifheader"		=> "Modificado",
	"permheader"		=> "Permiss�es",
	"actionheader"	=> "A��es",
	"pathheader"		=> "Caminho",
	
	// buttons
	"btncancel"		=> "Cancelar",
	"btnsave"		=> "Salvar",
	"btnchange"		=> "Modificar",
	"btnreset"		=> "Reset",
	"btnclose"		=> "Fechar",
	"btncreate"		=> "Criar",
	"btnsearch"		=> "Buscar",
	"btnupload"		=> "Enviar",
	"btncopy"		=> "Copiar",
	"btnmove"		=> "Mover",
	"btnlogin"		=> "Login",
	"btnlogout"		=> "Logout",
	"btnadd"		=> "Adicionar",
	"btnedit"		=> "Editar",
	"btnremove"		=> "Remover",
	
	// actions
	"actdir"		=> "Pasta",
	"actperms"		=> "Modificar permiss�es",
	"actedit"		=> "Editar arquivos",
	"actsearchresults"	=> "Resultados da busca",
	"actcopyitems"	=> "Item(s) copiado(s)",
	"actcopyfrom"		=> "Copiar de /%s pa /%s ",
	"actmoveitems"	=> "Mover item(s)",
	"actmovefrom"		=> "Mover de /%s para /%s ",
	"actlogin"		=> "Login",
	"actloginheader"	=> "Login -  Disco Virtual",
	"actadmin"		=> "Administra��o",
	"actchpwd"		=> "Alterar senha",
	"actusers"		=> "Usu�rios",
	"actarchive"		=> "Compactar item(s)",
	"actupload"		=> "Enviar arqiuvo(s)",
	
	// misc
	"miscitems"		=> "Item(s)",
	"miscfree"		=> "Livre",
	"miscusername"	=> "Usu�rio",
	"miscpassword"	=> "Senha",
	"miscoldpass"		=> "Senha antiga",
	"miscnewpass"		=> "Senha nova",
	"miscconfpass"	=> "Confirme senha",
	"miscconfnewpass"	=> "Confirme nova senha",
	"miscchpass"		=> "Alterar senha",
	"mischomedir"		=> "Pasta padr�o",
	"mischomeurl"		=> "Local URL",
	"miscshowhidden"	=> "Exibir itens ocultos",
	"mischidepattern"	=> "Hide pattern",
	"miscperms"		=> "Permiss�es",
	"miscuseritems"	=> "(nome, pasta padr�o, exibir itens ocultos, permiss�es, ativo)",
	"miscadduser"		=> "Adicionar usu�rio",
	"miscedituser"	=> "Editar usu�rio '%s'",
	"miscactive"		=> "Ativo",
	"misclang"		=> "Idioma",
	"miscnoresult"	=> "Sem resultados.",
	"miscsubdirs"		=> "Buscar sub-pastas",
	"miscpermnames"	=> array("Visualizar apenas","Modificar","Alterar senha","Modificar & Alterar password","Administrador"),
	"miscyesno"		=> array("Sim","N�o","S","N"),
	"miscchmod"		=> array("Usu�rio", "Grupo", "P�blico"),
);
?>