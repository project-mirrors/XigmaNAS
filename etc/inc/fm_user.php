<?php
/*
	user.php

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
/**
 *	Activate the user with the given user name.
 *	this function tries to find the user with the given user name
 *	in the user database and tries to activate this user.
 *	If username matches to the content of the
 *	user database, the user is activated, it's home directory,
 *	home url and permissions are set in the global variable and the
 *	function returns true.
 *	If the user cannot be authenticated, the function returns false.
 *	@param string $user User name of the user to be authenticated
 *	@return boolean
 */
function user_activate(string $user) {
	global $config;

	$sphere = array_make_branch($config,'access','user');
//	lookup user
	$sphere_rowid = array_search_ex($user,$sphere,'login');
	if($sphere_rowid !== false):
		$sphere_row = $sphere[$sphere_rowid];
		if(is_bool($test = $sphere_row['fm_enable'] ?? false) ? $test : true):
			if( is_string($sphere_row['homedir'])
				&&
				(preg_match('/\S/',$sphere_row['homedir']) === 1)
			):
				$GLOBALS['home_dir'] = $sphere_row['homedir'];
			else:
				$GLOBALS['home_dir'] = '/mnt';
			endif;
			$GLOBALS['home_url'] = sprintf('%s://%s/%s',$config['system']['webgui']['protocol'] ?? 'http',$_SERVER['HTTP_HOST'] ?? 'localhost',$user);
			if(is_bool($test = $sphere_row['fmp_show_hidden_items'] ?? false) ? $test : true):
				$GLOBALS['show_hidden']	= 1;
			else:
				$GLOBALS['show_hidden']	= 0;
			endif;
			$GLOBALS['no_access'] = '^\.ht';
			return true;
		endif;
	elseif(Session::isAdmin()):
		$GLOBALS['home_dir'] = '/';
		$GLOBALS['home_url'] = sprintf('%s://%s/%s',$config['system']['webgui']['protocol'] ?? 'http',$_SERVER['HTTP_HOST'] ?? 'localhost',$user);
		$GLOBALS['show_hidden']	= 1;
		$GLOBALS['no_access'] = '';
		return true;
	endif;
	return false;
}
