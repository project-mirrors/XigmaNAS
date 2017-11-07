<?php
/*
	smartmontools_umass.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
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
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';
require_once 'properties_smartmontools_umass.php';

function get_sphere_smartmontools_umass() {
	global $config;
	
//	sphere structure
	$sphere = new co_sphere_grid('smartmontools_umass','php');
	$sphere->modify->basename($sphere->basename() . '_edit');
	$sphere->notifier('smartmontools_umass');
	$sphere->row_identifier('uuid');
	$sphere->enadis(true);
	$sphere->lock(false);
	$sphere->sym_add(gtext('Add Record'));
	$sphere->sym_mod(gtext('Edit Record'));
	$sphere->sym_del(gtext('Record is marked for deletion'));
	$sphere->sym_loc(gtext('Record is locked'));
	$sphere->sym_unl(gtext('Record is unlocked'));
	$sphere->cbm_delete(gtext('Delete Selected Records'));
	$sphere->cbm_delete_confirm(gtext('Do you want to delete selected records?'));
//	sphere external content
	$sphere->grid = &array_make_branch($config,'smartmontools','umass','param');
	array_sort_key($sphere->grid,'name');
	return $sphere;
}
function smartmontools_umass_process_updatenotification($mode,$data) {
	global $d_sysrebootreqd_path;
	global $config;

	$retval = 0;
	$sphere = &get_sphere_smartmontools_umass();
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
		case UPDATENOTIFY_MODE_DIRTY:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->row_identifier()))):
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
//	get environment
$property = new properties_smartmontools_umass();
$sphere = &get_sphere_smartmontools_umass();
//	init indicators
$input_errors = [];
$prerequisites_ok = true;
$errormsg = '';
//	silent fix identifier
if(false !== $sphere->row_identifier()):
	$updateconfig = false;
	foreach($sphere->grid as $sphere->row_id => $sphere->row):
		if(is_array($sphere->row)):
			if(is_null($property->{$sphere->row_identifier()}->validate_array_element($sphere->row))):
				$sphere->grid[$sphere->row_id][$sphere->row_identifier()] = $property->{$sphere->row_identifier()}->get_defaultvalue();
				$updateconfig = true;
			endif;
		else:
			unset($sphere->grid[$sphere->row_id]);
			$updateconfig = true;
		endif;
	endforeach;
	if($updateconfig):
		write_config();
	endif;
endif;
//	request method
$methods = ['GET','POST'];
$methods_regexp = sprintf('/^(%s)$/',implode('|',array_map(function($element) { return preg_quote($element,'/'); },$methods)));
$method = filter_input(INPUT_SERVER,'REQUEST_METHOD',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => $methods_regexp]]);
//	determine page mode and validate resource id
switch($method):
	case 'POST':
		$actions = ['apply',$sphere->get_cbm_button_val_enable(),$sphere->get_cbm_button_val_disable(),$sphere->get_cbm_button_val_toggle(),$sphere->get_cbm_button_val_delete()];
		$actions_regexp = sprintf('/^(%s)$/',implode('|',array_map(function($element) { return preg_quote($element,'/'); },$actions)));
		$action = filter_input(INPUT_POST,'submit',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => '','regexp' => $actions_regexp]]);
		switch($action):
			case 'apply':
				$retval = 0;
				if(!file_exists($d_sysrebootreqd_path)):
					$retval |= updatenotify_process($sphere->notifier(),$sphere->notifier_processor());
				endif;
				$savemsg = get_std_save_message($retval);
				if($retval == 0):
					updatenotify_delete($sphere->notifier());
				endif;
				header($sphere->header());
				exit;
			case $sphere->get_cbm_button_val_enable():
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier()))):
						if(!(isset($sphere->grid[$sphere->row_id][$property->enable->get_name()]))):
							$sphere->grid[$sphere->row_id][$property->enable->get_name()] = true;
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->header());
				exit;
				break;
			case $sphere->get_cbm_button_val_disable():
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier()))):
						if(isset($sphere->grid[$sphere->row_id][$property->enable->get_name()])):
							unset($sphere->grid[$sphere->row_id][$property->enable->get_name()]);
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->header());
				exit;
				break;
			case $sphere->get_cbm_button_val_toggle():
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier()))):
						if(isset($sphere->grid[$sphere->row_id][$property->enable->get_name()])):
							unset($sphere->grid[$sphere->row_id][$property->enable->get_name()]);
						else:
							$sphere->grid[$sphere->row_id][$property->enable->get_name()] = true;					
						endif;
						$updateconfig = true;
						$mode_updatenotify = updatenotify_get_mode($sphere->notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
						if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
							updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->header());
				exit;
				break;
			case $sphere->get_cbm_button_val_delete():
				$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier()))):
						$mode_updatenotify = updatenotify_get_mode($sphere->notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
						switch ($mode_updatenotify):
							case UPDATENOTIFY_MODE_NEW:  
								updatenotify_clear($sphere->notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
								updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_DIRTY_CONFIG,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
								break;
							case UPDATENOTIFY_MODE_MODIFIED:
								updatenotify_clear($sphere->notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
								updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
								break;
							case UPDATENOTIFY_MODE_UNKNOWN:
								updatenotify_set($sphere->notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
								break;
						endswitch;
					endif;
				endforeach;
				header($sphere->header());
				exit;
				break;
		endswitch;
		break;
endswitch;
$pgtitle = [gtext('Disks'),gtext('Management'),gtext('S.M.A.R.T.'),gtext('USB Mass Storage Devices')];
$record_exists = count($sphere->grid) > 0;
$a_col_width = ['5%','25%','25%','10%','25%','10%'];
$n_col_width = count($a_col_width);
//	prepare additional javascript code
$jcode = $sphere->doj(false);
$document = new_page($pgtitle,$sphere->scriptname());
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	add additional javascript code
if(isset($jcode)):
	$body->addJavaScript($jcode);
endif;
//	add tab navigation
$document->
	add_area_tabnav()->
		push()->add_tabnav_upper()->
			ins_tabnav_record('disks_manage.php',gtext('HDD Management'))->
			ins_tabnav_record('disks_init.php',gtext('HDD Format'))->
			ins_tabnav_record('disks_manage_smart.php',gtext('S.M.A.R.T.'),gtext('Reload Page'),true)->
			ins_tabnav_record('disks_manage_iscsi.php',gtext('iSCSI Initiator'))->
		pop()->add_tabnav_lower()->
			ins_tabnav_record('disks_manage_smart.php',gtext('Settings'))->
			ins_tabnav_record('smartmontools_umass.php',gtext('USB Mass Storage Devices'),gtext('Reload Page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	ins_input_errors($input_errors)->
	ins_info_box($savemsg)->
	ins_error_box($errormsg);
if(file_exists($d_sysrebootreqd_path)):
	$content->ins_info_box(get_std_save_message(0));
endif;
if(updatenotify_exists($sphere->notifier())):
	$content->ins_config_has_changed_box();
endif;
$table = $content->add_table_data_selection();
$table->ins_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$thead->ins_titleline(gtext('Overview'),$n_col_width);
$tr = $thead->addTR();
if($record_exists):
	$tr->addTHwC('lhelc')->ins_cbm_checkbox_toggle($sphere);
else:
	$tr->insTHwC('lhelc');
endif;
$tr->
	insTHwC('lhell',$property->name->get_title())->
	insTHwC('lhell',$property->type->get_Title())->
	insTHwC('lhelc',gtext('Status'))->
	insTHwC('lhell',$property->description->get_Title())->
	insTHwC('lhebl',gtext('Toolbox'));
$tbody = $table->addTBODY();
if($record_exists):
	foreach($sphere->grid as $sphere->row_id => $sphere->row):
		$notificationmode = updatenotify_get_mode($sphere->notifier(),$sphere->get_row_identifier_value());
		$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
		$is_enabled = $sphere->enadis() ? isset($sphere->row[$property->enable->get_name()]) : true;
		$is_notprotected = $sphere->lock() ? !$sphere->row[$property->protected->get_name()] : true;
		$src = ($is_enabled) ? $g_img['ena'] : $g_img['dis'];
		$title = ($is_enabled) ? gtext('Enabled') : gtext('Disabled');
		$tbody->
			addTR()->
				push()->
				addTDwC($is_enabled ? 'lcelc' : 'lcelcd')->
					ins_cbm_checkbox($sphere,!($is_notdirty && $is_notprotected))->
				pop()->
				insTDwC($is_enabled ? 'lcell' : 'lcelld',htmlspecialchars($sphere->row[$property->name->get_name()] ?? ''))->
				insTDwC($is_enabled ? 'lcell' : 'lcelld',htmlspecialchars($sphere->row[$property->type->get_name()] ?? ''))->
				push()->
				addTDwC($is_enabled ? 'lcelc' : 'lcelcd')->
					addA(['title' => $title])->insIMG(['src' => $src,'alt' => ''])->
				pop()->
				insTDwC($is_enabled ? 'lcell' : 'lcelld',htmlspecialchars($sphere->row[$property->description->get_name()] ?? ''))->
				add_toolbox_area()->
					ins_toolbox($sphere,$is_notprotected,$is_notdirty)->
					insTD()->
					insTD();
	endforeach;
else:
	$tbody->addTR()->addTD(['class' => 'lcebl','colspan' => $n_col_width],gtext('No records found.'));
endif;
$table->ins_footerwa($sphere,$n_col_width);
$document->add_area_buttons()->ins_cbm_button_enadis($sphere)->ins_cbm_button_delete($sphere);
$document->render();
?>
