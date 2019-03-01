<?php
/*
	row_toolbox.php

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
namespace services\ctld\hub\auth_group;
use common\properties as myp;
use common\rmo as myr;
use common\sphere as mys;
use services\ctld\hub\sub\chap\grid_toolbox as tbc;
use services\ctld\hub\sub\chap_mutual\grid_toolbox as tbcm;
use services\ctld\hub\sub\initiator_name\grid_toolbox as tbin;
use services\ctld\hub\sub\initiator_portal\grid_toolbox as tbip;
/**
 *	Wrapper class for autoloading functions
 */
final class row_toolbox {
/**
 *	Create the sphere object
 *	@return \common\sphere\row The sphere object
 */
	public static function init_sphere() {
		$sphere = new mys\row();
		shared_toolbox::init_sphere($sphere);
		$sphere->
			set_script('services_ctl_auth_group_edit')->
			set_parent('services_ctl_auth_group');
		return $sphere;
	}
/**
 *	Create the request method object
 *	@return \common\rmo\rmo The request method object
 */
	public static function init_rmo() {
		return myr\rmo_row_templates::rmo_with_clone();
	}
/**
 *	Create the properties object
 *	@return \services\ctld\hub\auth_group\row_properties The properties object
 */
	public static function init_properties() {
		$cop = new row_properties();
		return $cop;
	}
/**
 *	Collects information from subordinate
 *	@param string $needle
 *	@param object $cop
 *	@param object $sphere
 *	@param object $property
 *	@return array
 */
	private static function get_additional_info(string $needle,string $key_enabled,string $key_option,string $key_selected,$sphere,$property): array {
		$options = [];
		$selected = [];
		foreach($sphere->grid as $sphere->row_id => $sphere->row):
			if(array_key_exists($key_enabled,$sphere->row)):
				$enabled = is_bool($sphere->row[$key_enabled]) ? $sphere->row[$key_enabled] : true;
//				process enabled entries
				if($enabled):
//					add name to options
					if(array_key_exists($key_option,$sphere->row)):
						$name = $sphere->row[$key_option];
						if(is_string($name)):
							$options[$name] = $name;
//							add name to selected when group contains needle
							if(array_key_exists($key_selected,$sphere->row) && is_array($sphere->row[$key_selected]) && in_array($needle,$sphere->row[$key_selected])):
								$selected[$name] = $name;
							endif;
						endif;
					endif;
				endif;
			endif;
		endforeach;
		$property->set_options($options);
		$retval = [
			'selected' => $selected,
			'property' => $property
		];
		return $retval;
	}
/**
 *	collect information about defined and selected chap records
 *	@param string $needle the auth_group to search for
 *	@return array returns the property object with options populated and the array containing the selected options
 */
	public static function get_chap_info(string $needle): array {
		$cop = tbc::init_properties();
		$key_enable = $cop->get_enable()->get_name();
		$key_option = $cop->get_name()->get_name();
		$key_selected = $cop->get_group()->get_name();
		unset($cop);
		$sphere = tbc::init_sphere();
		$property = new myp\property_list_multi();
		$property->
			set_id('gridchap')->
			set_title(gettext('CHAP'))->
			set_name('gridchap')->
			set_message_info(gettext('No CHAP users found.'));
		$retval = self::get_additional_info($needle,$key_enable,$key_option,$key_selected,$sphere,$property);
		return $retval;
	}
/**
 *	collect information about defined and selected mutual chap records
 *	@param string $needle the auth_group to search for
 *	@return array returns the property object with options populated and the array containing the selected options
 */
	public static function get_chap_mutual_info(string $needle): array {
		$cop = tbcm::init_properties();
		$key_enable = $cop->get_enable()->get_name();
		$key_option = $cop->get_name()->get_name();
		$key_selected = $cop->get_group()->get_name();
		unset($cop);
		$sphere = tbcm::init_sphere();
		$property = new myp\property_list_multi();
		$property->
			set_id('gridchapmutual')->
			set_title(gettext('Mutual-CHAP'))->
			set_name('gridchapmutual')->
			set_message_info(gettext('No Mutual-CHAP users found.'));
		$retval = self::get_additional_info($needle,$key_enable,$key_option,$key_selected,$sphere,$property);
		return $retval;
	}
/**
 *	collect information about defined and selected initiator name records
 *	@param string $needle the auth_group to search for
 *	@return array returns the property object with options populated and the array containing the selected options
 */
	public static function get_initiator_name_info(string $needle): array {
		$cop = tbin::init_properties();
		$key_enable = $cop->get_enable()->get_name();
		$key_option = $cop->get_name()->get_name();
		$key_selected = $cop->get_group()->get_name();
		unset($cop);
		$sphere = tbin::init_sphere();
		$property = new myp\property_list_multi();
		$property->
			set_id('gridinitiatorname')->
			set_title(gettext('Initiator Name'))->
			set_name('gridinitiatorname')->
			set_message_info(gettext('No initiator names found.'));
		$retval = self::get_additional_info($needle,$key_enable,$key_option,$key_selected,$sphere,$property);
		return $retval;
	}
/**
 *	collect information about defined and selected initiator portal records
 *	@param string $needle the auth_group to search for
 *	@return array returns the property object with options populated and the array containing the selected options
 */
	public static function get_initiator_portal_info(string $needle): array {
		$cop = tbip::init_properties();
		$key_enable = $cop->get_enable()->get_name();
		$key_option = $cop->get_ipaddress()->get_name();
		$key_selected = $cop->get_group()->get_name();
		unset($cop);
		$sphere = tbip::init_sphere();
		$property = new myp\property_list_multi();
		$property->
			set_id('gridinitiatorportal')->
			set_title(gettext('Initiator Portal'))->
			set_name('gridinitiatorportal')->
			set_message_info(gettext('No initiator portals found.'));
		$retval = self::get_additional_info($needle,$key_enable,$key_option,$key_selected,$sphere,$property);
		return $retval;
	}
}
