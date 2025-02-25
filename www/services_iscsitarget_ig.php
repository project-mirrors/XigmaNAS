<?php
/*
	services_iscsitarget_ig.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';

if ($_POST) {
	$pconfig = $_POST;

	if (isset($_POST['apply']) && $_POST['apply']) {
		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("iscsitarget_ig", "iscsitargetig_process_updatenotification");
			config_lock();
			$retval |= rc_update_reload_service("iscsi_target");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (get_hast_role() != 'secondary') {
				$savemsg .= '<br>'
					. gtext('A reload request has been sent to the daemon.')
					. ' '
					. '<a href="' . 'diag_log.php?log=2' . '">'
					. gtext('You can verify the result in the log file.')
					. '</a>';
			}
			updatenotify_delete("iscsitarget_ig");
		}
	}
}

$a_iscsitarget_ig = &array_make_branch($config,'iscsitarget','initiatorgroup');
if(empty($a_iscsitarget_ig)):
else:
	array_sort_key($a_iscsitarget_ig,'tag');
endif;
array_make_branch($config,'iscsitarget','target');

if (isset($_GET['act']) && $_GET['act'] === "del") {
	$index = array_search_ex($_GET['uuid'], $config['iscsitarget']['initiatorgroup'], "uuid");
	if ($index !== false) {
		$ig = $config['iscsitarget']['initiatorgroup'][$index];
		foreach ($config['iscsitarget']['target'] as $target) {
			if (isset($target['pgigmap'])) {
				foreach ($target['pgigmap'] as $pgigmap) {
					if ($pgigmap['igtag'] == $ig['tag']) {
						$input_errors[] = gtext("This tag is used.");
					}
				}
			}
		}
	}

	if (empty($input_errors)) {
		updatenotify_set("iscsitarget_ig", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
		header("Location: services_iscsitarget_ig.php");
		exit;
	}
}

function iscsitargetig_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_DIRTY:
			$cnid = array_search_ex($data, $config['iscsitarget']['initiatorgroup'], "uuid");
			if (FALSE !== $cnid) {
				unset($config['iscsitarget']['initiatorgroup'][$cnid]);
				write_config();
			}
			break;
	}

	return $retval;
}
$pgtitle = [gtext('Services'),gtext('iSCSI Target'),gtext('Initiator Group')];
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gtext("Settings");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gtext("Targets");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gtext("Portals");?></span></a></li>
		<li class="tabact"><a href="services_iscsitarget_ig.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Initiators");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gtext("Auths");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_media.php"><span><?=gtext("Media");?></span></a></li>
	</ul></td></tr>
	<tr>
		<td class="tabcont">
			<form action="services_iscsitarget_ig.php" method="post" name="iform" id="iform">
				<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<?php if (updatenotify_exists("iscsitarget_ig")) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gtext("Initiator Groups");?></td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Initiator Group");?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5%" class="listhdrlr"><?=gtext("Tag");?></td>
									<td width="35%" class="listhdrr"><?=gtext("Initiators");?></td>
									<td width="25%" class="listhdrr"><?=gtext("Networks");?></td>
									<td width="25%" class="listhdrr"><?=gtext("Comment");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php foreach($config['iscsitarget']['initiatorgroup'] as $ig):?>
									<?php $notificationmode = updatenotify_get_mode("iscsitarget_ig", $ig['uuid']);?>
									<tr>
										<td class="listlr"><?=htmlspecialchars($ig['tag']);?>&nbsp;</td>
										<td class="listr">
											<?php foreach ($ig['iginitiatorname'] as $initiator): ?>
												<?php echo htmlspecialchars($initiator)."<br />\n"; ?>
											<?php endforeach;?>
										</td>
										<td class="listr">
											<?php foreach ($ig['ignetmask'] as $netmask): ?>
												<?php echo htmlspecialchars($netmask)."<br />\n"; ?>
											<?php endforeach;?>
										</td>
										<td class="listr"><?=htmlspecialchars($ig['comment']);?>&nbsp;</td>
										<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
											<td valign="middle" nowrap="nowrap" class="list">
												<a href="services_iscsitarget_ig_edit.php?uuid=<?=$ig['uuid'];?>"><img src="images/edit.png" title="<?=gtext("Edit initiator group");?>" border="0" alt="<?=gtext("Edit initiator group");?>" /></a>
												<a href="services_iscsitarget_ig.php?act=del&amp;type=ig&amp;uuid=<?=$ig['uuid'];?>" onclick="return confirm('<?=gtext("Do you really want to delete this initiator group?");?>')"><img src="images/delete.png" title="<?=gtext("Delete initiator group");?>" border="0" alt="<?=gtext("Add initiator group");?>" /></a>
											</td>
										<?php else:?>
											<td valign="middle" nowrap="nowrap" class="list">
												<img src="images/delete.png" border="0" alt="" />
											</td>
										<?php endif;?>
									</tr>
								<?php endforeach;?>
								<tr>
									<td class="list" colspan="4"></td>
									<td class="list">
										<a href="services_iscsitarget_ig_edit.php"><img src="images/add.png" title="<?=gtext("Add initiator group");?>" border="0" alt="<?=gtext("Add initiator group");?>" /></a>
									</td>
								</tr>
							</table>
							<?=gtext("Initiator Groups contains authorised initiator names and networks to access the target.");?>
						</td>
					</tr>
				</table>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
