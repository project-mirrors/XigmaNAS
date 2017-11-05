<?php
/*
	system_syslogconf.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2017 The NAS4Free Project <info@nas4free.org>.
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
require_once 'properties_syslogconf.php';

function system_syslogconf_get_sphere() {
	global $config;
	
	$sphere = new co_sphere_grid('system_syslogconf','php');
	$sphere->modify->basename($sphere->basename() . '_edit');
	$sphere->notifier('syslogconf');
	$sphere->row_identifier('uuid');
	$sphere->enadis(true);
	$sphere->lock(true);
	$sphere->sym_add(gtext('Add Option'));
	$sphere->sym_mod(gtext('Edit Option'));
	$sphere->sym_del(gtext('Option is marked for deletion'));
	$sphere->sym_loc(gtext('Option is locked'));
	$sphere->sym_unl(gtext('Option is unlocked'));
	$sphere->cbm_delete(gtext('Delete Selected Options'));
	$sphere->cbm_delete_confirm(gtext('Do you want to delete selected options?'));
	$sphere->cbm_disable(gtext('Disable Selected Options'));
	$sphere->cbm_disable_confirm(gtext('Do you want to disable selected options?'));
	$sphere->cbm_enable(gtext('Enable Selected Options'));
	$sphere->cbm_enable_confirm(gtext('Do you want to enable selected options?'));
	$sphere->cbm_toggle(gtext('Toggle Selected Options'));
	$sphere->cbm_toggle_confirm(gtext('Do you want to toggle selected options?'));
	$sphere->grid = &array_make_branch($config,'system','syslogconf','param');
	return $sphere;
}
function syslogconf_process_updatenotification($mode,$data) {
	global $config;
	
	$retval = 0;
	$sphere = &system_syslogconf_get_sphere();
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			write_config();
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
//	init properties and sphere
$property = new properties_syslogconf();
$sphere = &system_syslogconf_get_sphere();
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
$methods = ['GET','POST'];
$methods_regexp = sprintf('/^(%s)$/',implode('|',array_map(function($element) { return preg_quote($element,'/'); },$methods)));
$method = filter_input(INPUT_SERVER,'REQUEST_METHOD',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => NULL,'regexp' => $methods_regexp]]);
switch($method):
	case 'POST':
		$actions = [
			'apply',
			$sphere->get_cbm_button_val_delete()
		];
		if($sphere->enadis() && property_exists($property,'enable')):
			$actions[] = $sphere->get_cbm_button_val_enable();
			$actions[] = $sphere->get_cbm_button_val_disable();
			$actions[] = $sphere->get_cbm_button_val_toggle();
		endif;
		$actions_regexp = sprintf('/^(%s)$/',implode('|',array_map(function($element) { return preg_quote($element,'/'); },$actions)));
		$action = filter_input(INPUT_POST,'submit',FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => '','regexp' => $actions_regexp]]);
		switch($action):
			case 'apply':
				$retval = 0;
				$retval |= updatenotify_process($sphere->notifier(),$sphere->notifier_processor());
				config_lock();
				$retval |= rc_restart_service('syslogd');
				config_unlock();
				$savemsg = get_std_save_message($retval);
				if($retval == 0):
					updatenotify_delete($sphere->notifier());
				endif;
				header($sphere->header());
				exit;
				break;
			case $sphere->get_cbm_button_val_enable():
				if($sphere->enadis() && property_exists($property,'enable')):
					$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
					$updateconfig = false;
					foreach($sphere->cbm_grid as $sphere->cbm_row):
						$sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier());
						if($sphere->row_id  !== false):
							if(isset($sphere->grid[$sphere->row_id][$property->enable->get_name()]) && (is_bool($sphere->grid[$sphere->row_id][$property->enable->get_name()]) ? $sphere->grid[$sphere->row_id][$property->enable->get_name()] : true)):
							else:
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
				endif;
				header($sphere->header());
				exit;
				break;
			case $sphere->get_cbm_button_val_disable():
				if($sphere->enadis() && property_exists($property,'enable')):
					$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
					$updateconfig = false;
					foreach($sphere->cbm_grid as $sphere->cbm_row):
						$sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier());
						if($sphere->row_id !== false):
							if(isset($sphere->grid[$sphere->row_id][$property->enable->get_name()]) && (is_bool($sphere->grid[$sphere->row_id][$property->enable->get_name()]) ? $sphere->grid[$sphere->row_id][$property->enable->get_name()] : true)):
								$sphere->grid[$sphere->row_id][$property->enable->get_name()] = false;
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
				endif;
				header($sphere->header());
				exit;
				break;
			case $sphere->get_cbm_button_val_toggle():
				if($sphere->enadis() && property_exists($property,'enable')):
					$sphere->cbm_grid = filter_input(INPUT_POST,$sphere->cbm_name,FILTER_DEFAULT,['flags' => FILTER_REQUIRE_ARRAY,'options' => ['default' => []]]);
					$updateconfig = false;
					foreach($sphere->cbm_grid as $sphere->cbm_row):
						$sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier());
						if($sphere->row_id !== false):
							if(isset($sphere->grid[$sphere->row_id][$property->enable->get_name()]) && (is_bool($sphere->grid[$sphere->row_id][$property->enable->get_name()]) ? $sphere->grid[$sphere->row_id][$property->enable->get_name()] : true)):
								$sphere->grid[$sphere->row_id][$property->enable->get_name()] = false;
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
$pgtitle = [gtext('System'),gtext('Advanced'),gtext('syslog.conf')];
$record_exists = count($sphere->grid) > 0;
$a_col_width = ['5%','20%','20%','20%','10%','15','10%'];
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
		add_tabnav_upper()->
			mount_tabnav_record('system_advanced.php',gtext('Advanced'))->
			mount_tabnav_record('system_email.php',gtext('Email'))->
			mount_tabnav_record('system_email_reports.php',gtext('Email Reports'))->
			mount_tabnav_record('system_monitoring.php',gtext('Monitoring'))->
			mount_tabnav_record('system_swap.php',gtext('Swap'))->
			mount_tabnav_record('system_rc.php',gtext('Command Scripts'))->
			mount_tabnav_record('system_cron.php',gtext('Cron'))->
			mount_tabnav_record('system_loaderconf.php',gtext('loader.conf'))->
			mount_tabnav_record('system_rcconf.php',gtext('rc.conf'))->
			mount_tabnav_record('system_sysctl.php',gtext('sysctl.conf'))->
			mount_tabnav_record('system_syslogconf.php',gtext('syslog.conf'),gtext('Reload page'),true);
//	create data area
$content = $pagecontent->add_area_data();
//	display information, warnings and errors
$content->
	mount_input_errors($input_errors)->
	mount_info_box($savemsg)->
	mount_error_box($errormsg);
if(file_exists($d_sysrebootreqd_path)):
	$content->mount_info_box(get_std_save_message(0));
endif;
if(updatenotify_exists($sphere->notifier())):
	$content->mount_config_has_changed_box();
endif;
$table = $content->add_table_data_selection();
$table->mount_colgroup_with_styles('width',$a_col_width);
$thead = $table->addTHEAD();
$thead->mount_titleline(gtext('Overview'),$n_col_width);
$tr = $thead->addTR();
if($record_exists):
	$tr->addTH_class('lhelc')->mount_cbm_checkbox_toggle($sphere);
else:
	$tr->mountTH_class('lhelc');
endif;
$tr->
	mountTH_class('lhell',$property->facility->get_title())->
	mountTH_class('lhell',$property->level->get_Title())->
	mountTH_class('lhell',$property->value->get_Title())->
	mountTH_class('lhelc',gtext('Status'))->
	mountTH_class('lhell',$property->comment->get_Title())->
	mountTH_class('lhebl',gtext('Toolbox'));
$tbody = $table->addTBODY();
if($record_exists):
	foreach ($sphere->grid as $sphere->row):
		$notificationmode = updatenotify_get_mode($sphere->notifier(),$sphere->get_row_identifier_value());
		$is_notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
		$is_enabled = $sphere->enadis() ? isset($sphere->row[$property->enable->get_name()]) : true;
		$is_notprotected = $sphere->lock() ? !isset($sphere->row[$property->protected->get_name()]) : true;
		$src = ($is_enabled) ? $g_img['ena'] : $g_img['dis'];
		$title = ($is_enabled) ? gtext('Enabled') : gtext('Disabled');
		$tbody->
			addTR()->
				push()->
				addTD_class($is_enabled ? 'lcelc' : 'lcelcd')->
					mount_cbm_checkbox($sphere,!($is_notdirty && $is_notprotected))->
				pop()->
				mountTD_class($is_enabled ? 'lcell' : 'lcelld',htmlspecialchars($sphere->row[$property->facility->get_name()] ?? ''))->
				mountTD_class($is_enabled ? 'lcell' : 'lcelld',htmlspecialchars($sphere->row[$property->level->get_name()] ?? ''))->
				mountTD_class($is_enabled ? 'lcell' : 'lcelld',htmlspecialchars($sphere->row[$property->value->get_name()] ?? ''))->
				push()->
				addTD_class($is_enabled ? 'lcelc' : 'lcelcd')->
					addA(['title' => $title])->mountIMG(['src' => $src,'alt' => ''])->
				pop()->
				mountTD_class($is_enabled ? 'lcell' : 'lcelld',htmlspecialchars($sphere->row[$property->comment->get_name()] ?? ''))->
				add_toolbox_area()->
					mount_toolbox($sphere,$is_notprotected,$is_notdirty)->
					mountTD()->
					mountTD();
	endforeach;
else:
	$tbody->mount_no_records_found($n_col_width);
endif;
$table->mount_footer_with_add($sphere,$n_col_width);
$document->add_area_buttons()->mount_cbm_button_enadis($sphere)->mount_cbm_button_delete($sphere);
$document->render();
?>
