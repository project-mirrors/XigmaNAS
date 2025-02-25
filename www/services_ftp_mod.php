<?php
/*
	services_ftp_mod.php

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

$a_rule = &array_make_branch($config,'ftpd','mod_ban','rule');
$pconfig['mod_ban_enable'] = isset($config['ftpd']['mod_ban']['enable']);

if ($_POST) {
	$pconfig = $_POST;

	$config['ftpd']['mod_ban']['enable'] = isset($_POST['mod_ban_enable']) ? true : false;

	write_config();

	$retval = 0;
	if (!file_exists($d_sysrebootreqd_path)) {
		$retval |= updatenotify_process("ftpd_mod_ban", "ftpd_mod_ban_process_updatenotification");
		config_lock();
		$retval |= rc_update_service("proftpd");
		config_unlock();
	}
	$savemsg = get_std_save_message($retval);
	if ($retval == 0) {
		updatenotify_delete("ftpd_mod_ban");
	}
}


if (isset($_GET['act']) && $_GET['act'] === "del") {
	if ($_GET['uuid'] === "all") {
		foreach ($a_rule as $rulek => $rulev) {
			updatenotify_set("ftpd_mod_ban", UPDATENOTIFY_MODE_DIRTY, $a_rule[$rulek]['uuid']);
		}
	} else {
		updatenotify_set("ftpd_mod_ban", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
	}
	header("Location: services_ftp_mod.php");
	exit;
}

function ftpd_mod_ban_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['ftpd']['mod_ban']['rule'])) {
				$index = array_search_ex($data, $config['ftpd']['mod_ban']['rule'], "uuid");
				if (false !== $index) {
					unset($config['ftpd']['mod_ban']['rule'][$index]);
					write_config();
				}
			}
			break;
	}

	return $retval;
}
$pgtitle = [gtext('Services'),gtext('FTP'),gtext('Modules')];
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="services_ftp.php"><span><?=gtext("Settings");?></span></a></li>
				<li class="tabact"><a href="services_ftp_mod.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Modules");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="services_ftp_mod.php" method="post" onsubmit="spinner()">
				<?php if (updatenotify_exists("ftpd_mod_ban")) print_config_change_box();?>
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("mod_ban_enable", gtext("Ban List"), !empty($pconfig['mod_ban_enable']) ? true : false, gtext("Enable"), "enable_change(false)");?>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Rules");?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="30%" class="listhdrlr"><?=gtext("Event");?></td>
									<td width="30%" class="listhdrr"><?=gtext("Frequency");?></td>
									<td width="30%" class="listhdrr"><?=gtext("Expire");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php $i = 0; foreach ($a_rule as $rule):?>
								<?php $notificationmode = updatenotify_get_mode("ftpd_mod_ban", $rule['uuid']);?>
								<tr>
									<td class="listlr"><?=htmlspecialchars($rule['event']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($rule['occurrence']);?>/<?=htmlspecialchars($rule['timeinterval']);?></td>
									<td class="listr"><?=htmlspecialchars($rule['expire']);?>&nbsp;</td>
									<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
									<td valign="middle" nowrap="nowrap" class="list">
										<a href="services_ftp_mod_ban_edit.php?uuid=<?=$rule['uuid'];?>"><img src="images/edit.png" title="<?=gtext("Edit rule");?>" border="0" alt="<?=gtext("Edit rule");?>" /></a>
										<a href="services_ftp_mod.php?act=del&amp;mod=mod_ban&amp;uuid=<?=$rule['uuid'];?>" onclick="return confirm('<?=gtext("Do you really want to delete this rule?");?>')"><img src="images/delete.png" title="<?=gtext("Delete rule");?>" border="0" alt="<?=gtext("Delete rule");?>" /></a>
									</td>
									<?php else:?>
									<td valign="middle" nowrap="nowrap" class="list">
										<img src="images/delete.png" border="0" alt="" />
									</td>
									<?php endif;?>
								</tr>
								<?php $i++; endforeach;?>
								<tr>
									<td class="list" colspan="3"></td>
									<td class="list">
										<a href="services_ftp_mod_ban_edit.php"><img src="images/add.png" title="<?=gtext("Add rule");?>" border="0" alt="<?=gtext("Add rule");?>" /></a>
										<?php if (!empty($a_rule)):?>
										<a href="services_ftp_mod.php?act=del&amp;mod=mod_ban&amp;uuid=all" onclick="return confirm('<?=gtext("Do you really want to delete all rules?");?>')"><img src="images/delete.png" title="<?=gtext("Delete all rules");?>" border="0" alt="<?=gtext("Delete all rules");?>" /></a>
										<?php endif;?>
									</td>
								</tr>
							</table>
							<div id="remarks">
								<?php html_remark("note", "", gtext("The module provides automatic bans that are triggered based on configurable criteria. A ban prevents the banned user, host, or class from logging into the server, it does not prevent the banned user, host, or class from connecting to the server."));?>
							</div>
						</td>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save & Restart");?>" />
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
