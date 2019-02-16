<?php
/*
	disks_crypt.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018-2019 XigmaNAS <info@xigmanas.com>.
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
require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'co_sphere.php';

function geli_process_updatenotification($mode, $data) {
	global $config;
	$retval = 0;
	$sphere = &disks_crypt_get_sphere();
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if(false !== ($sphere->row_id = array_search_ex($data,$sphere->grid,$sphere->get_row_identifier()))):
				//	Kill encrypted volume.
				disks_geli_kill($sphere->grid[$sphere->row_id]['devicespecialfile']);
				//	Reset disk file system type attribute ('fstype') in configuration.
				set_conf_disk_fstype($sphere->grid[$sphere->row_id]['device'][0],'');
				unset($sphere->grid[$sphere->row_id]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
function disks_crypt_get_sphere() {
	global $config;
	$sphere = new co_sphere_grid('disks_crypt','php');
	$sphere->get_modify()->set_basename($sphere->get_basename() . '_edit');
	$sphere->set_notifier('geli');
	$sphere->set_row_identifier('uuid');
	$sphere->set_enadis(false);
	$sphere->set_lock(false);
	$sphere->
		setmsg_sym_add(gettext('Add Encrypted Volume'))->
		setmsg_sym_mod(gettext('Edit Encrypted Volume'))->
		setmsg_sym_del(gettext('Encrypted volume is marked for deletion'))->
		setmsg_sym_loc(gettext('Encrypted volume is protected'))->
		setmsg_sym_unl(gettext('Encrypted volume is unlocked'))->
		setmsg_cbm_delete(gettext('Delete Selected Encrypted Volumes'))->
		setmsg_cbm_delete_confirm(gettext('Do you want to delete selected encrypted volumes?'));
	$sphere->grid = &array_make_branch($config,'geli','vdisk');
	return $sphere;
}
$sphere = &disks_crypt_get_sphere();
array_sort_key($sphere->grid,'devicespecialfile');
$errormsg = '';
$input_errors = [];
if($_POST):
	if(isset($_POST['apply']) && $_POST['apply']):
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			$retval = updatenotify_process($sphere->get_notifier(),$sphere->get_notifier_processor());
		endif;
		$savemsg = get_std_save_message($retval);
		if($retval == 0):
			updatenotify_delete($sphere->get_notifier());
		endif;
		header($sphere->get_location());
		exit;
	endif;
	if(isset($_POST['submit'])):
		switch($_POST['submit']):
			case 'clearimport':
			case 'import':
				$retval = 0;
				switch($_POST['submit']):
					case 'clearimport':
						$retval = disks_import_all_encrypted_disks(true);
						break;
					case 'import':
						$retval = disks_import_all_encrypted_disks(false);
						break;
				endswitch;
				switch($retval <=> 0):
					case 0:
						$savemsg = gtext('No new encrypted disks have been found.');
						disks_update_mounts();
						break;
					case 1:
						$savemsg = gtext('All encrypted disks have been imported.');
						disks_update_mounts();
						break;
					case -1:
						$input_errors[] = gtext('Errors have been detected during import.');
						break;
				endswitch;
				
				//	ensure at least an empty array is available
				$sphere->grid = &array_make_branch($config,'geli','vdisk');
//				header($sphere->get_location());
//				exit;
			case $sphere->get_cbm_button_val_delete():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->get_row_identifier()))):
						//	disks_exists: 0 = yes, 1 = no
						if(disks_exists($sphere->grid[$sphere->row_id]['devicespecialfile'])):
							$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
							switch ($mode_updatenotify):
								case UPDATENOTIFY_MODE_NEW:
									updatenotify_clear($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
									updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY_CONFIG,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
									break;
								case UPDATENOTIFY_MODE_MODIFIED:
									updatenotify_clear($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
									updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
									break;
								case UPDATENOTIFY_MODE_UNKNOWN:
									updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_DIRTY,$sphere->grid[$sphere->row_id][$sphere->get_row_identifier()]);
									break;
							endswitch;
						endif;
					endif;
				endforeach;
//				header($sphere->get_location());
//				exit;
				break;
/*
			case $sphere->get_cbm_button_val_disable():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier()))):
						if(isset($sphere->grid[$sphere->row_id]['enable'])):
							unset($sphere->grid[$sphere->row_id]['enable']);
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->set_notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_enable():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier()))):
						if(!(isset($sphere->grid[$sphere->row_id]['enable']))):
							$sphere->grid[$sphere->row_id]['enable'] = true;
							$updateconfig = true;
							$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
								updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
							endif;
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
			case $sphere->get_cbm_button_val_toggle():
				$sphere->cbm_grid = $_POST[$sphere->get_cbm_name()] ?? [];
				$updateconfig = false;
				foreach($sphere->cbm_grid as $sphere->cbm_row):
					if(false !== ($sphere->row_id = array_search_ex($sphere->cbm_row,$sphere->grid,$sphere->row_identifier()))):
						if(isset($sphere->grid[$sphere->row_id]['enable'])):
							unset($sphere->grid[$sphere->row_id]['enable']);
						else:
							$sphere->grid[$sphere->row_id]['enable'] = true;					
						endif;
						$updateconfig = true;
						$mode_updatenotify = updatenotify_get_mode($sphere->get_notifier(),$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
						if(UPDATENOTIFY_MODE_UNKNOWN == $mode_updatenotify):
							updatenotify_set($sphere->get_notifier(),UPDATENOTIFY_MODE_MODIFIED,$sphere->grid[$sphere->row_id][$sphere->row_identifier()]);
						endif;
					endif;
				endforeach;
				if($updateconfig):
					write_config();
					$updateconfig = false;
				endif;
				header($sphere->get_location());
				exit;
				break;
 */
		endswitch;
	endif;
endif;
$pgtitle = [gtext('Disks'),gtext('Encryption'),gtext('Management')];
include 'fbegin.inc';
echo $sphere->doj();
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('disks_crypt.php',gettext('Management'),gettext('Reload page'),true)->
			ins_tabnav_record('disks_crypt_tools.php',gettext('Tools'));
$document->render();
?>
<form action="disks_crypt.php" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(file_exists($d_sysrebootreqd_path)):
		print_info_box(get_std_save_message(0));
	endif;
	if(!empty($errormsg)):
		print_error_box($errormsg);
	endif;
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
	if(updatenotify_exists($sphere->get_notifier())):
		print_config_change_box();
	endif;
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
?>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:5%">
			<col style="width:20%">
			<col style="width:25%">
			<col style="width:20%">
			<col style="width:20%">
			<col style="width:10%">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext('Encryption Management'),6);
?>
			<tr>
				<th class="lhelc"><?=$sphere->html_checkbox_toggle_cbm();?></th>
				<th class="lhell"><?=gtext('Disk');?></th>
				<th class="lhell"><?=gtext('Data Integrity');?></th>
				<th class="lhell"><?=gtext('Encryption');?></th>
				<th class="lhell"><?=gtext('Status') ;?></th>
				<th class="lhebl"><?=gtext('Toolbox');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($sphere->grid as $sphere->row):
				$notificationmode = updatenotify_get_mode($sphere->get_notifier(),$sphere->row[$sphere->get_row_identifier()]);
				$notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
				$enabled = $sphere->is_enadis_enabled() ? isset($sphere->row['enable']) : true;
				$notprotected = $sphere->is_lock_enabled() ? !isset($sphere->row['protected']) : true;
?>
				<tr>
					<td class="<?=$enabled ? "lcelc" : "lcelcd";?>">
<?php
						if($notdirty && $notprotected && disks_exists($sphere->row['devicespecialfile'])):
							echo $sphere->html_checkbox_cbm(false);
						else:
							echo $sphere->html_checkbox_cbm(true);
						endif;
?>
					</td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere->row['name']);?></td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere->row['aalgo']);?></td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere->row['ealgo']);?></td>
					<td class="<?=$enabled ? "lcell" : "lcelld";?>">
<?php
						if(updatenotify_exists('geli')):
							$status = gtext('Configuring');
							switch ($notificationmode):
								case UPDATENOTIFY_MODE_DIRTY_CONFIG:
								case UPDATENOTIFY_MODE_DIRTY:
									$status = gtext('Deleting');
									break;
							endswitch;
							echo htmlspecialchars($status);
						else:
							$notificationmode = UPDATENOTIFY_MODE_UNKNOWN;
							if(disks_exists($sphere->row['devicespecialfile'])):
								echo("<a href=\"disks_crypt_tools.php?disk={$sphere->row['devicespecialfile']}&amp;action=attach\">" . gtext('Not attached') . '</a>');
							else:
								echo(gtext('Attached'));
							endif;
						endif;
?>
					</td>
					<td class="lcebld">
						<table class="area_data_selection_toolbox"><colgroup><col style="width:33%"><col style="width:34%"><col style="width:33%"></colgroup><tbody><tr>
<?php
							echo $sphere->html_toolbox($notprotected,$notdirty);
?>
							<td></td>
							<td></td>
						</tr></tbody></table>
					</td>
				</tr>
<?php
			endforeach;
?>
		</tbody>
		<tfoot>
<?php
			echo $sphere->html_footer_add(6);
?>
		</tfoot>
	</table>
	<div id="submit">
<?php
		if($sphere->is_enadis_enabled()):
			if($sphere->toggle()):
				echo $sphere->html_button_toggle_rows();
			else:
				echo $sphere->html_button_enable_rows();
				echo $sphere->html_button_disable_rows();
			endif;
		endif;
		echo $sphere->html_button_delete_rows();
?>
		<button name="submit" type="submit" class="formbtn spin" value="import" onclick="return confirm('<?=gtext("Do you really want to import?\\nThe existing config may be overwritten.");?>');"><?=gtext('Import Disks');?></button>
		<button name="submit" type="submit" class="formbtn spin" value="clearimport" onclick="return confirm('<?=gtext("Do you really want to clear and import?\\nThe existing config will be cleared and overwritten.");?>');"><?=gtext('Clear Config And Import Disks');?></button>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
