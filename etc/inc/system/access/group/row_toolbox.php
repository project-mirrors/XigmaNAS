<?php
/*
	row_toolbox.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2025 XigmaNAS® <info@xigmanas.com>.
	All rights reserved.

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

namespace system\access\group;

use common\arr;
use common\rmo as myr;
use common\sphere as mys;
use common\toolbox as myt;

/**
 *	Wrapper class for autoloading functions
 */
class row_toolbox extends myt\row_toolbox {
/**
 *	Create the sphere object
 *	@global array $config
 *	@return mys\row The sphere object
 */
	public static function init_sphere() {
		global $config;

		$sphere = new mys\row();
		shared_toolbox::init_sphere($sphere);
		$sphere->
			set_script('access_groups_edit')->
			set_parent('access_groups');
		return $sphere;
	}
/**
 *	Create the request method object
 *	@return myr\rmo The request method object
 */
	public static function init_rmo() {
		return myr\rmo_row_templates::rmo_with_clone();
	}
/**
 *	Get the next available gid from system
 *	@global array $config System configuration
 *	@return	int
 */
	public static function get_next_gid(): int {
		global $config;

//		Get next available gid.
		exec('/usr/sbin/pw nextgroup',$output);
		$output = explode(':',$output[0]);
		$result = intval($output[0]);
//		Check if gid is already in use. If the user does not press the 'Apply'
//		button 'pw' does not recognize that there are already several new users
//		configured because the user db is not updated until 'Apply' is pressed.
		$a_group = arr::make_branch($config,'access','group');
		while(arr::search_ex(strval($result),$a_group,'id') !== false):
			$result++;
		endwhile;
		return $result;
	}
}
