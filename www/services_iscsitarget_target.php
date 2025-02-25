<?php
/*
	services_iscsitarget_target.php

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

$pconfig['enable'] = isset($config['iscsitarget']['enable']);

if ($_POST) {
	$pconfig = $_POST;

	//$config['iscsitarget']['enable'] = $_POST['enable'] ? true : false;

	if (isset($_POST['apply']) && $_POST['apply']) {
		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("iscsitarget_extent", "iscsitargetextent_process_updatenotification");
			$retval |= updatenotify_process("iscsitarget_target", "iscsitargettarget_process_updatenotification");
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
			updatenotify_delete("iscsitarget_extent");
			updatenotify_delete("iscsitarget_target");
		}
	}
}

array_make_branch($config,'iscsitarget','portalgroup');
array_make_branch($config,'iscsitarget','initiatorgroup');
array_make_branch($config,'iscsitarget','authgroup');
array_make_branch($config,'iscsitarget','extent');
array_make_branch($config,'iscsitarget','device');
array_make_branch($config,'iscsitarget','target');

function cmp_tag($a, $b) {
	if ($a['tag'] == $b['tag'])
		return 0;
	return ($a['tag'] > $b['tag']) ? 1 : -1;
}
usort($config['iscsitarget']['portalgroup'], "cmp_tag");
usort($config['iscsitarget']['initiatorgroup'], "cmp_tag");
usort($config['iscsitarget']['authgroup'], "cmp_tag");

array_sort_key($config['iscsitarget']['extent'], "name");
array_sort_key($config['iscsitarget']['device'], "name");
//array_sort_key($config['iscsitarget']['target'], "name");

function get_fulliqn($name) {
	global $config;
	$fullname = $name;
	$basename = $config['iscsitarget']['nodebase'];
	if (strncasecmp("iqn.", $name, 4) != 0
		&& strncasecmp("eui.", $name, 4) != 0
		&& strncasecmp("naa.", $name, 4) != 0) {
		if (strlen($basename) != 0) {
			$fullname = $basename.":".$name;
		}
	}
	return $fullname;
}

function cmp_target($a, $b) {
	$aname = get_fulliqn($a['name']);
	$bname = get_fulliqn($b['name']);
	return strcasecmp($aname, $bname);
}
usort($config['iscsitarget']['target'], "cmp_target");

if (isset($_GET['act']) && $_GET['act'] === "del") {
	switch ($_GET['type']) {
		case "extent":
			$index = array_search_ex($_GET['uuid'], $config['iscsitarget']['extent'], "uuid");
			if ($index !== false) {
				$extent = $config['iscsitarget']['extent'][$index];
				foreach ($config['iscsitarget']['device'] as $device) {
					if (isset($device['storage'])) {
						foreach ($device['storage'] as $storage) {
							if ($extent['name'] === $storage) {
								$input_errors[] = gtext("This extent is used.");
							}
						}
					}
				}
				foreach ($config['iscsitarget']['target'] as $target) {
					if (isset($target['storage'])) {
						foreach ($target['storage'] as $storage) {
							if ($extent['name'] === $storage) {
								$input_errors[] = gtext("This extent is used.");
							}
						}
					}
					if (isset($target['lunmap'])) {
						foreach ($target['lunmap'] as $lunmap) {
							if ($extent['name'] === $lunmap['extentname']) {
								$input_errors[] = gtext("This extent is used.");
							}
						}
					}
				}
			}
			if (empty($input_errors)) {
				updatenotify_set("iscsitarget_extent", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
				header("Location: services_iscsitarget_target.php");
				exit;
			}
			break;

		case "target":
			updatenotify_set("iscsitarget_target", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
			header("Location: services_iscsitarget_target.php");
			exit;
			break;
	}
}

function iscsitargetextent_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_DIRTY:
			$cnid = array_search_ex($data, $config['iscsitarget']['extent'], "uuid");
			if (FALSE !== $cnid) {
				unset($config['iscsitarget']['extent'][$cnid]);
				write_config();
			}
			break;
	}

	return $retval;
}

function iscsitargettarget_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_DIRTY:
			$cnid = array_search_ex($data, $config['iscsitarget']['target'], "uuid");
			if (FALSE !== $cnid) {
				unset($config['iscsitarget']['target'][$cnid]);
				write_config();
			}
			break;
	}

	return $retval;
}
$pgtitle = [gtext('Services'),gtext('iSCSI Target'),gtext('Target')];
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gtext("Settings");?></span></a></li>
		<li class="tabact"><a href="services_iscsitarget_target.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Targets");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gtext("Portals");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gtext("Initiators");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gtext("Auths");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_media.php"><span><?=gtext("Media");?></span></a></li>
	</ul></td></tr>
	<tr>
		<td class="tabcont">
			<form action="services_iscsitarget_target.php" method="post" name="iform" id="iform">
				<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<?php if (updatenotify_exists("iscsitarget_extent") || updatenotify_exists("iscsitarget_target")) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gtext("Targets");?></td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Extent");?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="20%" class="listhdrlr"><?=gtext("Name");?></td>
									<td width="50%" class="listhdrr"><?=gtext("Path");?></td>
									<td width="20%" class="listhdrr"><?=gtext("Size");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php foreach($config['iscsitarget']['extent'] as $extent):?>
									<?php $sizeunit = $extent['sizeunit']; if (!$sizeunit) { $sizeunit = "MB"; }?>
									<?php if ($sizeunit === "MB") { $psizeunit = gtext("MiB"); } else if ($sizeunit === "GB") { $psizeunit = gtext("GiB"); } else if ($sizeunit === "TB") { $psizeunit = gtext("TiB"); } else if ($sizeunit === "auto") { $psizeunit = gtext("Auto"); } else { $psizeunit = $sizeunit; }?>
									<?php $notificationmode = updatenotify_get_mode("iscsitarget_extent", $extent['uuid']);?>
									<tr>
										<td class="listlr"><?=htmlspecialchars($extent['name']);?>&nbsp;</td>
										<td class="listr"><?=htmlspecialchars($extent['path']);?>&nbsp;</td>
										<td class="listr"><?=htmlspecialchars($extent['size']);?><?=htmlspecialchars($psizeunit)?>&nbsp;</td>
										<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
											<td valign="middle" nowrap="nowrap" class="list">
												<a href="services_iscsitarget_extent_edit.php?uuid=<?=$extent['uuid'];?>"><img src="images/edit.png" title="<?=gtext("Edit extent");?>" border="0" alt="<?=gtext("Edit extent");?>" /></a>
												<a href="services_iscsitarget_target.php?act=del&amp;type=extent&amp;uuid=<?=$extent['uuid'];?>" onclick="return confirm('<?=gtext("Do you really want to delete this extent?");?>')"><img src="images/delete.png" title="<?=gtext("Delete extent");?>" border="0" alt="<?=gtext("Delete extent");?>" /></a>
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
									<td class="list"><a href="services_iscsitarget_extent_edit.php"><img src="images/add.png" title="<?=gtext("Add extent");?>" border="0" alt="<?=gtext("Add extent");?>" /></a></td>
								</tr>
							</table>
							<?=gtext("Extents must be defined before they can be used, and extents cannot be used more than once.");?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Target");?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="35%" class="listhdrlr"><?=gtext("Name");?></td>
									<td width="4%" class="listhdrr"><?=gtext("Flags");?></td>
									<td width="30%" class="listhdrr"><?=gtext("LUNs");?></td>
									<td width="7%" class="listhdrr"><?=gtext("PG");?></td>
									<td width="7%" class="listhdrr"><?=gtext("IG");?></td>
									<td width="7%" class="listhdrr"><?=gtext("AG");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php foreach($config['iscsitarget']['target'] as $target):?>
									<?php
									$pgtag = $target['pgigmap'][0]['pgtag'];
									$igtag = $target['pgigmap'][0]['igtag'];
									if (!empty($target['pgigmap'][1])) {
										$pgtag2 = $target['pgigmap'][1]['pgtag'];
										$igtag2 = $target['pgigmap'][1]['igtag'];
									} else {
										$pgtag2 = 0;
										$igtag2 = 0;
									}
									$agtag = $target['agmap'][0]['agtag'];
									$name = get_fulliqn($target['name']);
									$disabled = !isset($target['enable']) ? sprintf("(%s)", gtext("Disabled")) : "";
									if ($pgtag == 0 && count($config['iscsitarget']['portalgroup']) != 0)
										$pgtag = 1;
									if ($igtag == 0 && count($config['iscsitarget']['initiatorgroup']) != 0)
										$igtag = 1;
									$LUNs = [];
									$LUNs['0'] = "N/A";
									if (isset($target['lunmap'])) {
										foreach ($target['lunmap'] as $lunmap) {
											$index = array_search_ex($lunmap['extentname'], $config['iscsitarget']['extent'], "name");
											if (false !== $index) {
												$LUNs[$lunmap['lun']] = $config['iscsitarget']['extent'][$index]['path'];
											}
										}
									} else {
										if (isset($target['storage'])) {
											foreach ($target['storage'] as $storage) {
												$index = array_search_ex($storage, $config['iscsitarget']['extent'], "name");
												if (false !== $index) {
													$LUNs['0'] = $config['iscsitarget']['extent'][$index]['path'];
												}
											}
										}
									}
									?>
									<?php $notificationmode = updatenotify_get_mode("iscsitarget_target", $target['uuid']);?>
									<tr>
										<td class="listlr"><?=htmlspecialchars($name);?> <?=htmlspecialchars($disabled);?>&nbsp;</td>
										<td class="listr"><?=htmlspecialchars($target['flags']);?>&nbsp;</td>
										<td class="listr">
											<?php
											foreach ($LUNs as $key => $val) {
												echo sprintf("%s%s=%s<br />", gtext("LUN"), $key, $val);
											}
											?>
										</td>
										<td class="listr">
											<?php
											if ($pgtag == 0) {
												echo gtext("none");
											} else {
												echo htmlspecialchars($pgtag);
											}
											if ($pgtag2 != 0) {
												echo ",".htmlspecialchars($pgtag2);
											}
											?>
										</td>
										<td class="listr">
											<?php
											if ($igtag == 0) {
												echo gtext("none");
											} else {
												echo htmlspecialchars($igtag);
											}
											if ($igtag2 != 0) {
												echo ",".htmlspecialchars($igtag2);
											}
											?>
										</td>
										<td class="listr">
											<?php
											if ($agtag == 0) {
												echo gtext("none");
											} else {
												echo htmlspecialchars($agtag);
											}
											?>
										</td>
										<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
											<td valign="middle" nowrap="nowrap" class="list">
												<a href="services_iscsitarget_target_edit.php?uuid=<?=$target['uuid'];?>"><img src="images/edit.png" title="<?=gtext("Edit target");?>" border="0" alt="<?=gtext("Edit target");?>" /></a>
												<a href="services_iscsitarget_target.php?act=del&amp;type=target&amp;uuid=<?=$target['uuid'];?>" onclick="return confirm('<?=gtext("Do you really want to delete this target?");?>')"><img src="images/delete.png" title="<?=gtext("Delete target");?>" border="0" alt="<?=gtext("Delete target");?>" /></a>
											</td>
										<?php else:?>
											<td valign="middle" nowrap="nowrap" class="list">
												<img src="images/delete.png" border="0" alt="" />
											</td>
										<?php endif;?>
									</tr>
								<?php endforeach;?>
								<tr>
									<td class="list" colspan="6"></td>
									<td class="list"><a href="services_iscsitarget_target_edit.php"><img src="images/add.png" title="<?=gtext("Add target");?>" border="0" alt="<?=gtext("Add target");?>" /></a></td>
								</tr>
							</table>
							<?=gtext("At the highest level, a target is what is presented to the initiator, and is made up of one or more extents.");?>
						</td>
					</tr>
				</table>
				<div id="remarks">
					<?php
					$helpinghand = gtext('You must add at least a Portal Group, an Initiator Group and an Extent before you can configure a Target.')
						. '<br />'
						. gtext('A Portal Group is identified by a tag number and defines IP addresses and listening TCP ports.')
						. '<br />'
						. gtext('An Initiator Group is identified by a tag number and defines authorised initiator names and networks.')
						. '<br />'
						. gtext('An Auth Group is identified by a tag number and is optional. If the Target does not use CHAP authentication it defines authorised users and secrets for additional security.')
						. '<br />'
						. gtext('An Extent defines the storage area of the Target.');
					html_remark("note", gtext('Note'), $helpinghand);
					?>
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
