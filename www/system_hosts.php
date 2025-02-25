<?php
/*
	system_hosts.php

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

$pgtitle = [gtext('Network'),gtext('Hosts')];

if ($_POST) {
	if(isset($_POST['Submit']) && $_POST['Submit']):
		unset($input_errors);
		$pconfig = $_POST;
		if(empty($input_errors)):
			if(isset($config['system']['hostsacl']['rule'])):
				unset($config['system']['hostsacl']['rule']);
			endif;
			$grid_hostacl = &array_make_branch($config,'system','hostsacl','rule');
			foreach(explode("\n",$_POST['hostsacl']) as $rule):
				$rule = trim($rule,"\t\n\r");
				if(!empty($rule))
					$grid_hostacl[] = $rule;
			endforeach;
			write_config();
		endif;
	endif;

	if ((isset($_POST['apply']) && $_POST['apply']) || (isset($_POST['Submit']) && $_POST['Submit'])) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("hosts", "hosts_process_updatenotification");
			config_lock();
			$retval |= rc_exec_service("hosts"); // Update /etc/hosts
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("hosts");
		}
	}
}

$a_hosts = &array_make_branch($config,'system','hosts');
if(empty($a_hosts)):
else:
	array_sort_key($a_hosts,'name');
endif;
array_make_branch($config,'system','hostsacl','rule');
$pconfig['hostsacl'] = implode("\n", $config['system']['hostsacl']['rule']);

if (isset($_GET['act']) && $_GET['act'] === "del") {
	updatenotify_set("hosts", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
	header("Location: system_hosts.php");
	exit;
}

function hosts_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			$cnid = array_search_ex($data, $config['system']['hosts'], "uuid");
			if (FALSE !== $cnid) {
				unset($config['system']['hosts'][$cnid]);
				write_config();
			}
			break;
	}

	return $retval;
}
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabcont">
			<form action="system_hosts.php" method="post" onsubmit="spinner()">
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<?php if (updatenotify_exists("hosts")) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<?php html_titleline2(gettext('Hosts'), 2);?>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Hostname Database");?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="25%" class="listhdrlr"><?=gtext("Hostname");?></td>
									<td width="30%" class="listhdrr"><?=gtext("IP Address");?></td>
									<td width="35%" class="listhdrr"><?=gtext("Description");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php foreach ($a_hosts as $host):?>
								<?php if (empty($host['uuid'])) continue;?>
								<?php $notificationmode = updatenotify_get_mode("hosts", $host['uuid']);?>
								<tr>
									<td class="listlr"><?=htmlspecialchars($host['name']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($host['address']);?>&nbsp;</td>
									<td class="listbg"><?=htmlspecialchars($host['descr']);?>&nbsp;</td>
									<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
									<td valign="middle" nowrap="nowrap" class="list">
										<a href="system_hosts_edit.php?uuid=<?=$host['uuid'];?>"><img src="images/edit.png" title="<?=gtext("Edit Host");?>" border="0" alt="<?=gtext("Edit Host");?>" /></a>
										<a href="system_hosts.php?act=del&amp;uuid=<?=$host['uuid'];?>" onclick="return confirm('<?=gtext("Do you really want to delete this host?");?>')"><img src="images/delete.png" title="<?=gtext("Delete Host");?>" border="0" alt="<?=gtext("Delete Host");?>" /></a>
									</td>
									<?php else:?>
									<td valign="middle" nowrap="nowrap" class="list">
										<img src="images/delete.png" border="0" alt="" />
									</td>
									<?php endif;?>
								</tr>
								<?php endforeach;?>
								<tr>
									<td class="list" colspan="3"></td>
									<td class="list"><a href="system_hosts_edit.php"><img src="images/add.png" title="<?=gtext("Add Host");?>" border="0" alt="<?=gtext("Add Host");?>" /></a></td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
					$helpinghand = '<a href="' . 'https://www.freebsd.org/doc/en/books/handbook/tcpwrappers.html' . '" target="_blank">'
						. gtext('Check the FreeBSD documentation for detailed information about TCP Wrappers')
						. '</a>.';
					html_textarea("hostsacl", gtext("Host Access Control"), $pconfig['hostsacl'], gtext("The basic configuration usually takes the form of 'daemon : address : action'. Where daemon is the daemon name of the service started. The address can be a valid hostname, an IP address or an IPv6 address enclosed in brackets. The action field can be either allow or deny to grant or deny access appropriately. Keep in mind that configuration works off a first rule match semantic, meaning that the configuration file is scanned in ascending order for a matching rule. When a match is found the rule is applied and the search process will halt.") . " " . $helpinghand, false, 80, 8, false, false);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save and Restart");?>" />
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
