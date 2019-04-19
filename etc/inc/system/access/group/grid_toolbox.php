<?php
/*
	grid_toolbox.php

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
namespace system\access\group;

use common\properties as myp;
use common\rmo as myr;
use common\sphere as mys;
/**
 *	Wrapper class for autoloading functions
 */
final class grid_toolbox {
/**
 *	Create the sphere object
 *	@return \common\sphere\grid
 */
	public static function init_sphere() {
		$sphere = new mys\grid();
		shared_toolbox::init_sphere($sphere);
		$sphere->
			set_script('access_groups')->
			set_modify('access_groups_edit')->
			setmsg_sym_add(gettext('Add Group'))->
			setmsg_sym_mod(gettext('Edit Group'))->
			setmsg_sym_del(gettext('Group is marked for deletion'))->
			setmsg_sym_loc(gettext('Group is protected'))->
			setmsg_sym_unl(gettext('Group is unlocked'))->
			setmsg_cbm_delete(gettext('Delete Selected Groups'))->
			setmsg_cbm_delete_confirm(gettext('Do you want to delete selected groups?'))->
			setmsg_cbm_disable(gettext('Disable Selected Groups'))->
			setmsg_cbm_disable_confirm(gettext('Do you want to disable selected groups?'))->
			setmsg_cbm_enable(gettext('Enable Selected Groups'))->
			setmsg_cbm_enable_confirm(gettext('Do you want to enable selected groups?'))->
			setmsg_cbm_toggle(gettext('Toggle Selected Groups'))->
			setmsg_cbm_toggle_confirm(gettext('Do you want to toggle selected groups?'));
		return $sphere;
	}
/**
 *	Create the request method object
 *	@param grid_properties $cop
 *	@param mys\grid $sphere
 *	@return \common\rmo\rmo The request method object
 */
	public static function init_rmo(grid_properties $cop,mys\grid $sphere) {
		$rmo = myr\rmo_grid_templates::rmo_base($cop,$sphere);
		$rmo->add('POST','show',PAGE_MODE_POST);
		$rmo->add('POST','hide',PAGE_MODE_POST);
		return $rmo;
	}
/**
 *	Create the property object
 *	@return \system\access\group\grid_properties
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
 *	@param \system\access\group\grid_properties $cop
 *	@param \common\sphere\grid $sphere
 */
	public static function render(grid_properties $cop,mys\grid $sphere) {
		global $config;
		global $input_errors;
		global $errormsg;
		global $savemsg;

		$hidesystemgroups = $_SESSION['access.hidesystemgroups'] ?? false;
		$known_groups = ($hidesystemgroups ? [] : \system_get_group_list());
		$pgtitle = [gettext('Access'),gettext('Groups')];
		$row_count = count($sphere->grid) + count($known_groups);
		$row_exists = ($row_count > 0);
		$use_tablesort = ($row_count > 1);
		$a_col_width = ['5%','25%','25%','35%','10%'];
		$n_col_width = count($a_col_width);
		if($use_tablesort):
			$document = new_page($pgtitle,$sphere->get_script()->get_scriptname(),'tablesort');
		else:
			$document = new_page($pgtitle,$sphere->get_script()->get_scriptname());
		endif;
//		get areas
		$body = $document->getElementById('main');
		$pagecontent = $document->getElementById('pagecontent');
//		add tab navigation
		shared_toolbox::add_tabnav($document);
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
//		add content
		$table = $content->add_table_data_selection();
		$table->ins_colgroup_with_styles('width',$a_col_width);
		$thead = $table->addTHEAD();
		$tbody = $table->addTBODY();
		$tfoot = $table->addTFOOT();
		$thead->ins_titleline(gettext('Overview'),$n_col_width);
		if($use_tablesort):
			$thead->
				addTR()->
					push()->
					addTHwC('lhelc sorter-false parser-false')->
						ins_cbm_checkbox_toggle($sphere)->
					pop()->
					insTHwC('lhell',$cop->get_name()->get_title())->
					insTHwC('lhell',$cop->get_gid()->get_title())->
					insTHwC('lhell',$cop->get_description()->get_title())->
					insTHwC('lhebl',$cop->get_toolbox()->get_title());
		else:
			$thead->
				addTR()->
					insTHwC('lhelc')->
					insTHwC('lhell',$cop->get_name()->get_title())->
					insTHwC('lhell',$cop->get_gid()->get_title())->
					insTHwC('lhell',$cop->get_description()->get_title())->
					insTHwC('lhebl',$cop->get_toolbox()->get_title());
		endif;
		if($row_exists):
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
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_name()->get_name()] ?? '')->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_gid()->get_name()] ?? '')->
						insTDwC('lcell' . $dc,$sphere->row[$cop->get_description()->get_name()] ?? '')->
						add_toolbox_area()->
							ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
							ins_maintainbox(false)->
							ins_informbox($sphere,false);
				unset($known_groups[$sphere->row[$cop->get_name()->get_name()]]);
			endforeach;
			$gettext_system_group = gettext('System Group');
			$is_notdirty = true;
			$is_enabled = true;
			$is_notprotected = false;
			foreach($known_groups as $k_name => $v_gid):
				$tbody->
					addTR()->
						insTDwC('lcelc')->
						insTDwC('lcell',$k_name ?? '')->
						insTDwC('lcell',$v_gid ?? '')->
						insTDwC('lcell',$gettext_system_group)->
						add_toolbox_area()->
							ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
							ins_maintainbox(false)->
							ins_informbox($sphere,false);
			endforeach;
		else:
			$tfoot->ins_no_records_found($n_col_width);
		endif;
		$tfoot->ins_record_add($sphere,$n_col_width);
		$buttons = $document->add_area_buttons();
		$buttons->
			ins_cbm_button_enadis($sphere)->
			ins_cbm_button_delete($sphere);
		if($hidesystemgroups):
			$buttons->ins_button_submit('show',gettext('Show System Groups'));
		else:
			$buttons->ins_button_submit('hide',gettext('Hide System Groups'));
		endif;
//		additional javascript code
		$body->addJavaScript($sphere->get_js());
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
 *	@param \common\properties\container $cop
 *	@param \common\sphere\root $sphere
 *	@param \common\rmo\rmo $rmo
 */
	final public static function looper(myp\container $cop,mys\root $sphere,myr\rmo $rmo) {
		global $config;
		global $d_sysrebootreqd_path;
		global $input_errors;
		global $errormsg;
		global $savemsg;

//		preset $savemsg in case a reboot is pending
		if(file_exists($d_sysrebootreqd_path)):
			$savemsg = get_std_save_message(0);
		endif;
		list($page_method,$page_action,$page_mode) = $rmo->validate();
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
						config_lock();
						$retval |= rc_exec_service('userdb');
						config_unlock();
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
					case 'show':
						$referer = 'access.hidesystemgroups';
						$hidesystemgroups = $_SESSION[$referer] ?? false;
						if($hidesystemgroups):
							$_SESSION[$referer] = false;
							header($sphere->get_script()->get_location());
							exit;
						endif;
						break;
					case 'hide':
						$referer = 'access.hidesystemgroups';
						$hidesystemgroups = $_SESSION[$referer] ?? false;
						if(!$hidesystemgroups):
							$_SESSION[$referer] = true;
							header($sphere->get_script()->get_location());
							exit;
						endif;
						break;
				endswitch;
				break;
		endswitch;
	}
}
