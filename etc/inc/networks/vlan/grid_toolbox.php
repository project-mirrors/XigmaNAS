<?php
/*
	grid_toolbox.php

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

namespace networks\vlan;

use common\properties as myp;
use common\rmo as myr;
use common\sphere as mys;
use common\toolbox as myt;

use const UPDATENOTIFY_MODE_DIRTY;
use const UPDATENOTIFY_MODE_DIRTY_CONFIG;

use function get_std_save_message;
use function new_page;
use function updatenotify_cbm_delete;
use function updatenotify_cbm_disable;
use function updatenotify_cbm_enable;
use function updatenotify_cbm_toggle;
use function updatenotify_exists;
use function updatenotify_get_mode;
use function updatenotify_process;
use function write_config;

/**
 *	Wrapper class for autoloading functions
 */
class grid_toolbox extends myt\grid_toolbox {
/**
 *	Create the sphere object
 *	@return mys\grid
 */
	public static function init_sphere() {
		$sphere = new mys\grid();
		shared_toolbox::init_sphere($sphere);
		$sphere->
			set_script('interfaces_vlan')->
			set_modify('interfaces_vlan_edit')->
			setmsg_sym_add(gettext('Add VLAN'))->
			setmsg_sym_mod(gettext('Edit VLAN'))->
			setmsg_sym_del(gettext('VLAN is marked for deletion'))->
			setmsg_sym_loc(gettext('VLAN is locked'))->
			setmsg_sym_unl(gettext('VLAN is unlocked'))->
			setmsg_cbm_delete(gettext('Delete Selected VLANs'))->
			setmsg_cbm_disable(gettext('Disable Selected VLANs'))->
			setmsg_cbm_enable(gettext('Enable Selected VLANs'))->
			setmsg_cbm_toggle(gettext('Toggle Selected VLANs'))->
			setmsg_cbm_delete_confirm(gettext('Do you want to delete selected VLANs?'))->
			setmsg_cbm_disable_confirm(gettext('Do you want to disable selected VLANs?'))->
			setmsg_cbm_enable_confirm(gettext('Do you want to enable selected VLANs?'))->
			setmsg_cbm_toggle_confirm(gettext('Do you want to toggle selected VLANs?'));
		return $sphere;
	}
/**
 *	Create the request method object
 *	@param grid_properties $cop
 *	@param mys\grid $sphere
 *	@return myr\rmo The request method object
 */
	public static function init_rmo(grid_properties $cop,mys\grid $sphere) {
		$rmo = myr\rmo_grid_templates::rmo_base($cop,$sphere);
		return $rmo;
	}
/**
 *	Create the properties object
 *	@return grid_properties The properties object
 */
	public static function init_properties() {
		$cop = new grid_properties();
		return $cop;
	}
/**
 *	Render the page
 *	@global array $input_errors
 *	@global string $errormsg
 *	@global string $savemsg
 *	@param grid_properties $cop
 *	@param mys\grid $sphere
 */
	public static function render(grid_properties $cop,mys\grid $sphere) {
		global $input_errors;
		global $errormsg;
		global $savemsg;

		$record_exists = count($sphere->grid) > 0;
		$use_tablesort = count($sphere->grid) > 1;
		$a_col_width = ['5%','10%','10%','25%','10%','30%','10%'];
		$n_col_width = count($a_col_width);
		if($use_tablesort):
			$document = new_page($sphere->get_page_title(),$sphere->get_script()->get_scriptname(),'tablesort');
		else:
			$document = new_page($sphere->get_page_title(),$sphere->get_script()->get_scriptname());
		endif;
//		add tab navigation
		shared_toolbox::add_tabnav($document);
//		get areas
		$body = $document->getElementById('main');
		$pagecontent = $document->getElementById('pagecontent');
//		create data area
		$content = $pagecontent->add_area_data();
//		display information, warnings and errors
		$content->
			ins_input_errors($input_errors)->
			ins_info_box($savemsg)->
			ins_error_box($errormsg);
		if(updatenotify_exists($sphere->get_notifier())):
			$content->ins_config_has_changed_box();
		endif;
		//	add content
		$table = $content->add_table_data_selection();
		$table->ins_colgroup_with_styles('width',$a_col_width);
		$thead = $table->addTHEAD();
		$tbody = $table->addTBODY();
		$tfoot = $table->addTFOOT();
		$thead->ins_titleline(gettext('Overview'),$n_col_width);
		$tr = $thead->addTR();
		if($use_tablesort):
			$tr->
				push()->
				addTHwC('lhelc sorter-false parser-false')->
					ins_cbm_checkbox_toggle($sphere)->
				pop()->
				insTHwC('lhell',$cop->get_if()->get_title())->
				insTHwC('lhell',$cop->get_tag()->get_title())->
				insTHwC('lhell',$cop->get_vlandev()->get_title())->
				insTHwC('lhelc sorter-image',gettext('Status'))->
				insTHwC('lhell',$cop->get_description()->get_title())->
				insTHwC('lhebl sorter-false parser-false',$cop->get_toolbox()->get_title());
		else:
			$tr->
				insTHwC('lhelc')->
				insTHwC('lhell',$cop->get_if()->get_title())->
				insTHwC('lhell',$cop->get_tag()->get_title())->
				insTHwC('lhell',$cop->get_vlandev()->get_title())->
				insTHwC('lhelc',gettext('Status'))->
				insTHwC('lhell',$cop->get_description()->get_title())->
				insTHwC('lhebl',$cop->get_toolbox()->get_title());
		endif;
		if($record_exists):
			foreach($sphere->grid as $sphere->row_id => $sphere->row):
				$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->get_row_identifier_value());
				$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
				$is_enabled = $sphere->is_enadis_enabled() ? (is_bool($test = $sphere->row[$cop->get_enable()->get_name()] ?? false) ? $test : true): true;
				$is_notprotected = $sphere->is_lock_enabled() ? !(is_bool($test = $sphere->row[$cop->get_protected()->get_name()] ?? false) ? $test : true) : true;
				$dc = $is_enabled ? '' : 'd';
				$tbody->
					addTR()->
						push()->
						addTDwC('lcelc' . $dc)->
							ins_cbm_checkbox($sphere,!($is_notdirty && $is_notprotected))->
						pop()->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_if()->get_name()] ?? '')->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_tag()->get_name()] ?? '')->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_vlandev()->get_name()] ?? '')->
						ins_enadis_icon($is_enabled)->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_description()->get_name()] ?? '')->
						add_toolbox_area()->
							ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
							ins_maintainbox($sphere,false)->
							ins_informbox($sphere,false);
			endforeach;
		else:
			$tfoot->ins_no_records_found($n_col_width);
		endif;
		$tfoot->ins_record_add($sphere,$n_col_width);
		$document->
			add_area_buttons()->
				ins_cbm_button_enadis($sphere)->
				ins_cbm_button_delete($sphere);
//		additional javascript code
		$body->ins_javascript($sphere->get_js());
		$body->add_js_on_load($sphere->get_js_on_load());
		$body->add_js_document_ready($sphere->get_js_document_ready());
		$document->render();
	}
/**
 *	process request method
 *	@global string $d_sysrebootreqd_path
 *	@global array $input_errors
 *	@global string $errormsg
 *	@global string $savemsg
 *	@param myp\container $cop
 *	@param mys\root $sphere
 *	@param myr\rmo $rmo
 */
	public static function looper(myp\container $cop,mys\root $sphere,myr\rmo $rmo) {
		global $d_sysrebootreqd_path;
		global $input_errors;
		global $errormsg;
		global $savemsg;

//		preset $savemsg in case a reboot is pending
		if(file_exists($d_sysrebootreqd_path)):
			$savemsg = get_std_save_message(0);
		endif;
		[$page_method,$page_action,$page_mode] = $rmo->validate();
		switch($page_method):
			case 'SESSION':
				switch($page_action):
					case $sphere->get_script()->get_basename():
//						catch error code
						$retval = filter_var($_SESSION[$sphere->get_script()->get_basename()],FILTER_VALIDATE_INT,['options' => ['default' => 0]]);
						unset($_SESSION['submit'],$_SESSION[$sphere->get_script()->get_basename()]);
						$savemsg = get_std_save_message($retval);
						break;
				endswitch;
				break;
			case 'POST':
				switch($page_action):
					case 'apply':
						$retval = 0;
						$retval |= updatenotify_process($sphere->get_notifier(),$sphere->get_notifier_processor());
						$_SESSION['submit'] = $sphere->get_script()->get_basename();
						$_SESSION[$sphere->get_script()->get_basename()] = $retval;
						header($sphere->get_script()->get_location());
						exit;
						break;
					case $sphere->get_cbm_button_val_delete():
						updatenotify_cbm_delete($sphere,$cop);
						header($sphere->get_script()->get_location());
						exit;
						break;
					case $sphere->get_cbm_button_val_toggle():
						if(updatenotify_cbm_toggle($sphere,$cop)):
							write_config();
						endif;
						header($sphere->get_script()->get_location());
						exit;
						break;
					case $sphere->get_cbm_button_val_enable():
						if(updatenotify_cbm_enable($sphere,$cop)):
							write_config();
						endif;
						header($sphere->get_script()->get_location());
						exit;
						break;
					case $sphere->get_cbm_button_val_disable():
						if(updatenotify_cbm_disable($sphere,$cop)):
							write_config();
						endif;
						header($sphere->get_script()->get_location());
						exit;
						break;
				endswitch;
				break;
		endswitch;
	}
}
