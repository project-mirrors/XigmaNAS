<?php
/*
	disks_raid_gconcat.php

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
require("auth.inc");
require("guiconfig.inc");

$sphere_scriptname = basename(__FILE__);
$sphere_scriptname_child = 'disks_raid_gconcat_edit.php';
$sphere_header = 'Location: ' . $sphere_scriptname;
$sphere_header_parent = $sphere_header;
$sphere_notifier = 'raid_gconcat';
$sphere_notifier_processor = 'gconcat_process_updatenotification';
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_add = gettext('Add RAID');
$gt_record_mod = gettext('Edit RAID');
$gt_record_del = gettext('RAID is marked for removal');
$gt_record_loc = gettext('RAID is protected');
$gt_selection_delete = gettext('Delete Selected RAID Volumes');
$gt_selection_delete_confirm = gettext('Do you want to delete selected RAID volumes?');
$img_path = [
	'add' => 'images/add.png',
	'mod' => 'images/edit.png',
	'del' => 'images/delete.png',
	'loc' => 'images/locked.png',
	'unl' => 'images/unlocked.png'
];

// sunrise: verify if setting exists, otherwise run init tasks
if (!(isset($config['gconcat']['vdisk']) && is_array($config['gconcat']['vdisk']))) {
	$config['gconcat']['vdisk'] = [];
}
array_sort_key($config['gconcat']['vdisk'], 'name');
$sphere_array = &$config['gconcat']['vdisk'];

// get active mounts
mwexec2("mount | awk '{print $1}'", $a_mount);

if ($_POST) {
	if (isset($_POST['apply']) && $_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			// Process notifications
			$retval = updatenotify_process($sphere_notifier, $sphere_notifier_processor);
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete($sphere_notifier);
		}
		header($sphere_header);
		exit;
	}
	if (isset($_POST['delete_selected_rows']) && $_POST['delete_selected_rows']) {
		$checkbox_member_array = isset($_POST[$checkbox_member_name]) ? $_POST[$checkbox_member_name] : [];
		foreach ($checkbox_member_array as $checkbox_member_record) {
			if (false !== ($index = array_search_ex($checkbox_member_record, $sphere_array, 'uuid'))) {
				if (!isset($sphere_array[$index]['protected'])) {
					$mode_updatenotify = updatenotify_get_mode($sphere_notifier, $sphere_array[$index]['uuid']);
					switch ($mode_updatenotify) {
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
					}
				}
			}
		}
		header($sphere_header);
		exit;
	}
}

function gconcat_process_updatenotification($mode, $data) {
	global $config;
	$retval = 0;
	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
			$retval |= rc_exec_service('geom load concat');
			$retval |= disks_raid_gconcat_configure($data);
			break;
		case UPDATENOTIFY_MODE_MODIFIED:
			$retval |= rc_exec_service('geom start concat');
			break;
		case UPDATENOTIFY_MODE_DIRTY_CONFIG:
			if (is_array($config['gconcat']['vdisk'])) {
				$index = array_search_ex($data, $config['gconcat']['vdisk'], 'uuid');
				if (false !== $index) {
					unset($config['gconcat']['vdisk'][$index]);
					write_config();
				}
			}
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			$retval |= disks_raid_gconcat_delete($data);
			if (is_array($config['gconcat']['vdisk'])) {
				$index = array_search_ex($data, $config['gconcat']['vdisk'], 'uuid');
				if (false !== $index) {
					unset($config['gconcat']['vdisk'][$index]);
					write_config();
				}
			}
			break;
	}
	return $retval;
}

function is_gconcat_mounted($nameofdevice, &$arrayofmounts) {
	foreach ($arrayofmounts as $mount) {
		if (0 === strpos($mount, $nameofdevice)) {
			return true;
		}
	}
	return false;
}

$pgtitle = array(gettext('Disks'), gettext('Software RAID'), gettext('JBOD'), gettext('Management'));
?>
<?php include("fbegin.inc"); ?>
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
	$("input[name='<?=$checkbox_member_name;?>[]").click(function() {
		controlactionbuttons(this, '<?=$checkbox_member_name;?>[]');
	});
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
		if (a_trigger[i].type === 'checkbox') {
			if (!a_trigger[i].disabled) {
				a_trigger[i].checked = !a_trigger[i].checked;
				if (a_trigger[i].checked) {
					ab_disable = false;
				}
			}
		}
	}
	if (ego.type === 'checkbox') { ego.checked = false; }
	disableactionbuttons(ab_disable);
}
function controlactionbuttons(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var ab_disable = true;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type === 'checkbox') {
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
<table id="area_navigator">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gettext('Reload page'); ?>"><span><?=gettext('JBOD');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gstripe.php"><span><?=gettext('RAID 0');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gmirror.php"><span><?=gettext('RAID 1');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_graid5.php"><span><?=gettext('RAID 5');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum.php"><span><?=gettext('RAID 0/1/5');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gettext('Reload page');?>"><span><?=gettext('Management');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gconcat_tools.php"><span><?=gettext('Tools');?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gconcat_info.php"><span><?=gettext('Information');?></span></a></li>
			</ul>
		</td>
	</tr>
</table>
<table id="area_data"><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform">
	<?php
		if (!empty($errormsg)) { print_error_box($errormsg); }
		if (!empty($savemsg)) { print_info_box($savemsg); }
		if (updatenotify_exists($sphere_notifier)) { print_config_change_box(); }
	?>
	<table id="area_data_selection">
		<colgroup>
			<col style="width:5%"><!-- checkbox -->
			<col style="width:20%"><!-- Volume Name -->
			<col style="width:10%"><!-- Type -->
			<col style="width:15%"><!-- Size -->
			<col style="width:30%"><!-- Description -->
			<col style="width:10%"><!-- Status -->
			<col style="width:10%"><!-- Icons -->
		</colgroup>
		<thead>
			<?php html_titleline2(gettext('Overview'), 7);?>
			<tr>
				<td class="lhelc"><input type="checkbox" id="togglemembers" name="togglemembers" title="<?php gettext('Invert Selection');?>"/></td>
				<td class="lhell"><?=gettext('Volume Name');?></td>
				<td class="lhell"><?=gettext('Type');?></td>
				<td class="lhell"><?=gettext('Size');?></td>
				<td class="lhell"><?=gettext('Description');?></td>
				<td class="lhelc"><?=gettext('Status');?></td>
				<td class="lhebl"><?=gettext('Toolbox');?></td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="lcenl" colspan="6"></th>
				<th class="lceadd"><a href="<?=$sphere_scriptname_child;?>"><img src="<?=$img_path['add'];?>" title="<?=$gt_record_add;?>" alt="<?=$gt_record_add;?>"/></a></th>
			</tr>
		</tfoot>
		<tbody>
			<?php $raidstatus = get_gconcat_disks_list(); ?>
			<?php foreach ($sphere_array as $sphere_record): ?>
				<?php
					$size = gettext('Unknown');
					$status = gettext('Stopped');
					if (is_array($raidstatus) && array_key_exists($sphere_record['name'], $raidstatus)) {
						$size = $raidstatus[$sphere_record['name']]['size'];
						$status = $raidstatus[$sphere_record['name']]['state'];
					}
					$notificationmode = updatenotify_get_mode($sphere_notifier, $sphere_record['uuid']);
					switch ($notificationmode) {
						case UPDATENOTIFY_MODE_NEW:
							$status = $size = gettext('Initializing');
							break;
						case UPDATENOTIFY_MODE_MODIFIED:
							$status = $size = gettext('Modifying');
							break;
						case UPDATENOTIFY_MODE_DIRTY:
						case UPDATENOTIFY_MODE_DIRTY_CONFIG:
							$status = gettext('Deleting');
							break;
					}
					$status = strtoupper($status);
					$notdirty = (UPDATENOTIFY_MODE_DIRTY != $notificationmode) && (UPDATENOTIFY_MODE_DIRTY_CONFIG != $notificationmode);
					$notprotected = !isset($sphere_record['protected']);
					$notmounted = !is_gconcat_mounted($sphere_record['devicespecialfile'], $a_mount);
					$normaloperation = $notprotected && $notmounted;
				?>
				<tr>
					<td class="<?=$normaloperation ? "lcelc" : "lcelcd";?>">
						<?php if ($notdirty && $notprotected && $notmounted):?>
							<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>"/>
						<?php else:?>
							<input type="checkbox" name="<?=$checkbox_member_name;?>[]" value="<?=$sphere_record['uuid'];?>" id="<?=$sphere_record['uuid'];?>" disabled="disabled"/>
						<?php endif;?>
					</td>
					<td class="<?=$normaloperation ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['name']);?></td>
					<td class="<?=$normaloperation ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['type']);?></td>
					<td class="<?=$normaloperation ? "lcell" : "lcelld";?>"><?=$size;?>&nbsp;</td>
					<td class="<?=$normaloperation ? "lcell" : "lcelld";?>"><?=htmlspecialchars($sphere_record['desc']);?></td>
					<td class="lcelcd"><?=$status;?>&nbsp;</td>
					<td class="lcebld">
						<?php if ($notdirty && $notprotected):?>
							<a href="<?=$sphere_scriptname_child;?>?uuid=<?=$sphere_record['uuid'];?>"><img src="<?=$img_path['mod'];?>" title="<?=$gt_record_mod;?>" alt="<?=$gt_record_mod;?>" /></a>
						<?php else:?>
							<?php if ($notprotected && $notmounted):?>
								<img src="<?=$img_path['del'];?>" title="<?=gettext($gt_record_del);?>" alt="<?=gettext($gt_record_del);?>"/>
							<?php else:?>
								<img src="<?=$img_path['loc'];?>" title="<?=gettext($gt_record_loc);?>" alt="<?=gettext($gt_record_loc);?>"/>
							<?php endif;?>
						<?php endif;?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div id="submit">
		<input name="delete_selected_rows" id="delete_selected_rows" type="submit" class="formbtn" value="<?=$gt_selection_delete;?>"/>
	</div>
	<table id="area_data_messages">
		<colgroup>
			<col id="area_data_messages_col_tag">
			<col id="area_data_messages_col_data">
		</colgroup>
		<thead>
			<?php
				html_separator2();
				html_titleline2(gettext('Message Board'));
			?>
		</thead>
		<tbody>
			<?php
				html_textinfo2("info", gettext('Info'), sprintf(gettext('%s is used to create %s volumes.'), 'GEOM Concat', 'JBOD'));
				html_textinfo2("warning", gettext('Warning'), sprintf(gettext("A mounted RAID volume cannot be deleted. Remove the <a href='%s'>mount point</a> first before proceeding."), 'disks_mount.php'));
			?>
		</tbody>
	</table>
	<?php include("formend.inc"); ?>
</form></td></tr></table>
<?php include("fend.inc"); ?>
