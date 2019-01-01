<?php
/*
	disks_zfs_zpool_vdevice.php

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

$sphere_scriptname = basename(__FILE__);
$sphere_scriptname_child = 'disks_zfs_zpool_vdevice_edit.php';
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = $sphere_header;
$sphere_notifier = 'zfsvdev';
$sphere_notifier_processor = 'zfsvdev_process_updatenotification';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_add = gtext('Add Virtual Device');
$gt_record_mod = gtext('Edit Virtual Device');
$gt_record_del = gtext('Virtual Device is marked for removal');
$gt_record_loc = gtext('Virtual Device is protected');
$gt_record_unl = gtext('Virtual Device is unlocked');
$gt_record_mai = gtext('Maintenance');
$gt_record_inf = gtext('Information');
$gt_selection_delete = gtext('Delete Selected Virtual Devices');
$gt_selection_delete_confirm = gtext('Do you want to delete selected virtual devices?');
$img_path = [
	'add' => 'images/add.png',
	'mod' => 'images/edit.png',
	'del' => 'images/delete.png',
	'loc' => 'images/locked.png',
	'unl' => 'images/unlocked.png',
	'mai' => 'images/maintain.png',
	'inf' => 'images/info.png'
];
// sunrise: verify if setting exists, otherwise run init tasks
$sphere_array = &array_make_branch($config,'zfs','vdevices','vdevice');
if(empty($sphere_array)):
else:
	array_sort_key($sphere_array,'name');
endif;
if($_POST):
	if(isset($_POST['apply']) && $_POST['apply']):
		$retval = 0;
//		if(!file_exists($d_sysrebootreqd_path)):
			$retval |= updatenotify_process($sphere_notifier, $sphere_notifier_processor);
			$savemsg = get_std_save_message($retval);
			if($retval === 0):
				updatenotify_delete($sphere_notifier);
			endif;
			header($sphere_header);
			exit;
//		endif;
	endif;
	if(isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']):
		$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
		foreach($checkbox_member_array as $checkbox_member_record):
			if(false !== ($index = array_search_ex($checkbox_member_record, $sphere_array, 'uuid'))):
				$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_array[$index]['uuid']);
				switch($mode_updatenotify):
					case UPDATENOTIFY_MODE_NEW:
						updatenotify_clear($sphere_notifier, $sphere_array[$index]['uuid']);
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY_CONFIG, $sphere_array[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_MODIFIED:
						updatenotify_clear($sphere_notifier, $sphere_array[$index]['uuid']);
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY, $sphere_array[$index]['uuid']);
						break;
					case UPDATENOTIFY_MODE_UNKNOWN:
						updatenotify_set($sphere_notifier, UPDATENOTIFY_MODE_DIRTY, $sphere_array[$index]['uuid']);
						break;
				endswitch;
			endif;
		endforeach;
		header($sphere_header);
		exit;
	endif;
endif;
function zfsvdev_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;
	switch($mode):
		case UPDATENOTIFY_MODE_NEW:
			break;
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
		case UPDATENOTIFY_MODE_DIRTY:
			$index = array_search_ex($data, $config['zfs']['vdevices']['vdevice'], 'uuid');
			if(false !== $index):
				unset($config['zfs']['vdevices']['vdevice'][$index]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
$a_pool = &array_make_branch($config,'zfs','pools','pool');
$pgtitle = [gtext('Disks'),gtext('ZFS'),gtext('Pools'),gtext('Virtual Device')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
	// Init action buttons
	$("#delete_selected_rows").click(function () {
		return confirm('<?=$gt_selection_delete_confirm;?>');
	});
	// Disable action buttons.
	disableactionbuttons(true);
	// Init toggle checkbox
	$("#togglemembers").click(function() {
		togglecheckboxesbyname(this, "<?=$checkbox_member_name;?>[]");
	});
	// Init member checkboxes
	$("input[name='<?=$checkbox_member_name;?>[]']").click(function() {
		controlactionbuttons(this, '<?=$checkbox_member_name;?>[]');
	});
	// Init spinner onsubmit()
	$("#iform").submit(function() { spinner(); });
	$(".spin").click(function() { spinner(); });
});
function disableactionbuttons(ab_disable) {
	$("#delete_selected_rows").prop("disabled", ab_disable);
}
function togglecheckboxesbyname(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type == 'checkbox') {
			if (!a_trigger[i].disabled) {
				a_trigger[i].checked = !a_trigger[i].checked;
				if (a_trigger[i].checked) {
					ab_disable = false;
				}
			}
		}
	}
	if (ego.type == 'checkbox') { ego.checked = false; }
	disableactionbuttons(ab_disable);
}
function controlactionbuttons(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type == 'checkbox') {
			if (a_trigger[i].checked) {
				ab_disable = false;
				break;
			}
		}
	}
	disableactionbuttons(ab_disable);
}
//]]>
</script>
<?php
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		push()->
		add_tabnav_upper()->
			ins_tabnav_record('disks_zfs_zpool.php',gtext('Pools'),gtext('Reload page'),true)->
			ins_tabnav_record('disks_zfs_dataset.php',gtext('Datasets'))->
			ins_tabnav_record('disks_zfs_volume.php',gtext('Volumes'))->
			ins_tabnav_record('disks_zfs_snapshot.php',gtext('Snapshots'))->
			ins_tabnav_record('disks_zfs_config.php',gtext('Configuration'))->
			ins_tabnav_record('disks_zfs_settings.php',gtext('Settings'))->
		pop()->
		add_tabnav_lower()->
			ins_tabnav_record('disks_zfs_zpool_vdevice.php',gtext('Virtual Device'),gtext('Reload page'),true)->
			ins_tabnav_record('disks_zfs_zpool.php',gtext('Management'))->
			ins_tabnav_record('disks_zfs_zpool_tools.php',gtext('Tools'))->
			ins_tabnav_record('disks_zfs_zpool_info.php',gtext('Information'))->
			ins_tabnav_record('disks_zfs_zpool_io.php',gtext('I/O Statistics'));
$document->render();
?>
<form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
	if(file_exists($d_sysrebootreqd_path)):
		print_info_box(get_std_save_message(0));
	endif;
	if(updatenotify_exists($sphere_notifier)):
		print_config_change_box();
	endif;
?>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:5%">
			<col style="width:15%">
			<col style="width:15%">
			<col style="width:55%">
			<col style="width:10%">
		</colgroup>
		<thead>
<?php
			html_titleline2(gtext('Overview'), 5);
?>
			<tr>
				<th class="lhelc"><input type="checkbox" class="oneemhigh" id="togglemembers" name="togglemembers" title="<?=gtext('Invert Selection');?>"/></th>
				<th class="lhell"><?=gtext('Name');?></th>
				<th class="lhell"><?=gtext('Type');?></th>
				<th class="lhell"><?=gtext('Description');?></th>
				<th class="lhebl"><?=gtext('Toolbox');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($sphere_array as $sphere_record):
				$notificationmode = updatenotify_get_mode($sphere_notifier, $sphere_record['uuid']);
				$notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
				$notprotected = !isset($sphere_record['protected']);
				$isnotmemberofapool = (false === array_search_ex($sphere_record['name'], $a_pool, 'vdevice'));
?>
				<tr>
					<td class="lcelc">
<?php
						if($notdirty && $notprotected && $isnotmemberofapool):
?>
							<input type="checkbox" class="oneemhigh" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>"/>
<?php
						else:
?>
							<input type="checkbox" class="oneemhigh" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" disabled="disabled"/>
<?php
						endif;
?>
					</td>
					<td class="lcell"><?=htmlspecialchars($sphere_record['name']);?></td>
					<td class="lcell"><?=htmlspecialchars($sphere_record['type']);?></td>
					<td class="lcell"><?=htmlspecialchars($sphere_record['desc']);?>&nbsp;</td>
					<td class="lcebld">
						<table class="area_data_selection_toolbox"><colgroup><col style="width:33%"><col style="width:34%"><col style="width:33%"></colgroup><tbody><tr>
							<td>
<?php
								if($notdirty && $notprotected):
?>
									<a href="<?=$sphere_scriptname_child;?>?uuid=<?=$sphere_record['uuid'];?>"><img src="<?=$img_path['mod'];?>" title="<?=$gt_record_mod;?>" alt="<?=$gt_record_mod;?>" /></a>
<?php
								elseif($notprotected && $isnotmemberofapool):
?>
									<img src="<?=$img_path['del'];?>" title="<?=$gt_record_del;?>" alt="<?=$gt_record_del;?>"/>
<?php
								else:
?>
									<img src="<?=$img_path['loc'];?>" title="<?=$gt_record_loc;?>" alt="<?=$gt_record_loc;?>"/>
<?php
								endif;
?>
							</td>
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
			<tr>
				<th class="lcenl" colspan="4"></th>
				<th class="lceadd"><a href="<?=$sphere_scriptname_child;?>"><img src="<?=$img_path['add'];?>" title="<?=$gt_record_add;?>" alt="<?=$gt_record_add;?>"/></a></th>
			</tr>
		</tfoot>
	</table>
	<div id="submit">
		<input name="delete_selected_rows" id="delete_selected_rows" type="submit" class="formbtn" value="<?=$gt_selection_delete;?>"/>
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
?>
