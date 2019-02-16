<?php
/*
	services\ctld\toolbox.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright © 2018-2019 XigmaNAS <info@xigmanas.com>.
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
	of XigmaNAS, either expressed or implied.
*/
namespace services\ctld;
use common\rmo as myr;
use common\sphere as mys;
/**
 *	Wrapper class for autoloading functions
 */
final class toolbox_row {
/**
 *	Create the sphere object
 *	@global array $config
 *	@return \common\sphere\row The sphere object
 */
	public static function init_sphere() {
		global $config;
		
		$sphere = new mys\row('services_ctl','php');
		$sphere->set_enadis(true);
		$sphere->grid = &array_make_branch($config,'ctld');
		return $sphere;
	}
/**
 *	Create the request method object
 *	@param \services\ctld\extended_properties $cop
 *	@param \common\sphere\row $sphere
 *	@return \common\rmo\rmo The request method object
 */
	public static function init_rmo(extended_properties $cop,mys\row $sphere) {
		$rmo = new myr\rmo();
		$rmo->
			set_default('GET','view',PAGE_MODE_VIEW)->
			add('GET','edit',PAGE_MODE_EDIT)->
			add('GET','view',PAGE_MODE_VIEW)->
			add('POST','edit',PAGE_MODE_EDIT)->
			add('POST','reload',PAGE_MODE_VIEW)->
			add('POST','restart',PAGE_MODE_VIEW)->
			add('POST','save',PAGE_MODE_POST)->
			add('POST','view',PAGE_MODE_VIEW)->
			add('SESSION',$sphere->get_basename(),PAGE_MODE_VIEW);
		if($sphere->is_enadis_enabled()):
			$rmo->
				add('POST','disable',PAGE_MODE_VIEW)->
				add('POST','enable',PAGE_MODE_VIEW);
		endif;
		return $rmo;
	}
/**
 *	Creates the property object
 *	@return \services\ctld\extended_properties
 */
	public static function init_properties() {
		$cop = new extended_properties();
		return $cop;
	}
}
