<?php
/*
	services_iscsitarget_ag.php

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

require_once 'autoload.php';
require_once 'auth.inc';
require_once 'guiconfig.inc';

use common\arr;

if($_POST):
	$pconfig = $_POST;
	if(isset($_POST['apply']) && $_POST['apply']):
		write_config();
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			$retval |= updatenotify_process("iscsitarget_ag", "iscsitargetag_process_updatenotification");
			config_lock();
			$retval |= rc_update_reload_service("iscsi_target");
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
		if($retval == 0):
			if(get_hast_role() != 'secondary'):
				$savemsg .= '<br>'
					. gtext('A reload request has been sent to the daemon.')
					. ' '
					. '<a href="' . 'diag_log.php?log=2' . '">'
					. gtext('You can verify the result in the log file.')
					. '</a>';
			endif;
			updatenotify_delete('iscsitarget_ag');
		endif;
	endif;
endif;
$a_iscsitarget_ag = &arr::make_branch($config,'iscsitarget','authgroup');
if(empty($a_iscsitarget_ag)):
else:
	arr::sort_key($a_iscsitarget_ag,'tag');
endif;
arr::make_branch($config,'iscsitarget','target');
if(isset($_GET['act']) && $_GET['act'] === 'del'):
	$index = arr::search_ex($_GET['uuid'],$config['iscsitarget']['authgroup'],'uuid');
	if($index !== false):
		$ag = $config['iscsitarget']['authgroup'][$index];
		if($ag['tag'] == $config['iscsitarget']['discoveryauthgroup']):
			$input_errors[] = gtext('This tag is used.');
		endif;
		foreach ($config['iscsitarget']['target'] as $target):
			if(isset($target['agmap'])):
				foreach($target['agmap'] as $agmap):
					if($agmap['agtag'] == $ag['tag']):
						$input_errors[] = gtext('This tag is used.');
					endif;
				endforeach;
			endif;
		endforeach;
	endif;

	if(empty($input_errors)):
		updatenotify_set('iscsitarget_ag',UPDATENOTIFY_MODE_DIRTY,$_GET['uuid']);
		header('Location: services_iscsitarget_ag.php');
		exit;
	endif;
endif;
function iscsitargetag_process_updatenotification($mode,$data) {
	global $config;

	$retval = 0;
	switch($mode):
		case UPDATENOTIFY_MODE_DIRTY:
			$cnid = arr::search_ex($data,$config['iscsitarget']['authgroup'],'uuid');
			if($cnid !== false):
				unset($config['iscsitarget']['authgroup'][$cnid]);
				write_config();
			endif;
			break;
	endswitch;
	return $retval;
}
$pgtitle = [gtext('Services'),gtext('iSCSI Target'),gtext('Auth Group')];
include 'fbegin.inc';
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gtext('Settings');?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gtext('Targets');?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gtext('Portals');?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gtext('Initiators');?></span></a></li>
		<li class="tabact"><a href="services_iscsitarget_ag.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Auths');?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_media.php"><span><?=gtext('Media');?></span></a></li>
	</ul></td></tr>
	<tr>
		<td class="tabcont">
			<form action="services_iscsitarget_ag.php" method="post" name="iform" id="iform">
<?php
				if(!empty($input_errors)):
					print_input_errors($input_errors);
				endif;
				if(!empty($savemsg)):
					print_info_box($savemsg);
				endif;
				if(updatenotify_exists('iscsitarget_ag')):
					print_config_change_box();
				endif;
?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gtext("Auth Groups");?></td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext("Auth Group");?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5%" class="listhdrlr"><?=gtext('Tag');?></td>
									<td width="30%" class="listhdrr"><?=gtext('CHAP Users');?></td>
									<td width="30%" class="listhdrr"><?=gtext('Mutual CHAP Users');?></td>
									<td width="25%" class="listhdrr"><?=gtext('Comment');?></td>
									<td width="10%" class="list"></td>
								</tr>
<?php
								foreach($config['iscsitarget']['authgroup'] as $ag):
									if(!isset($ag['agauth']) || !is_array($ag['agauth'])):
										$ag['agauth'] = [];
									endif;
									arr::sort_key($ag['agauth'],'authuser');
									$notificationmode = updatenotify_get_mode('iscsitarget_ag',$ag['uuid']);
?>
									<tr>
										<td class="listlr"><?=htmlspecialchars($ag['tag']);?>&nbsp;</td>
										<td class="listr">
<?php
											if(count($ag['agauth']) == 0):
												echo "&nbsp;";
											endif;
											foreach($ag['agauth'] as $agauth):
												 echo htmlspecialchars($agauth['authuser'])."<br />\n";
											endforeach;
?>
										</td>
										<td class="listr">
<?php
											if(count($ag['agauth']) == 0):
												echo "&nbsp;";
											endif;
											foreach($ag['agauth'] as $agauth):
												echo htmlspecialchars($agauth['authmuser'])."<br />\n";
											endforeach;
?>
										</td>
										<td class="listr"><?=htmlspecialchars($ag['comment']);?>&nbsp;</td>
<?php
										if(UPDATENOTIFY_MODE_DIRTY != $notificationmode):
?>
											<td valign="middle" nowrap="nowrap" class="list">
												<a href="services_iscsitarget_ag_edit.php?uuid=<?=$ag['uuid'];?>"><img src="images/edit.png" title="<?=gtext("Edit auth group");?>" border="0" alt="<?=gtext("Edit auth group");?>" /></a>
												<a href="services_iscsitarget_ag.php?act=del&amp;type=ag&amp;uuid=<?=$ag['uuid'];?>" onclick="return confirm('<?=gtext("Do you really want to delete this auth group?");?>')"><img src="images/delete.png" title="<?=gtext("Delete auth group");?>" border="0" alt="<?=gtext("Delete auth group");?>" /></a>
											</td>
<?php
										else:
?>
											<td valign="middle" nowrap="nowrap" class="list">
												<img src="images/delete.png" border="0" alt="" />
											</td>
<?php
										endif;
?>
									</tr>
<?php
								endforeach;
?>
								<tr>
									<td class="list" colspan="4"></td>
									<td class="list"><a href="services_iscsitarget_ag_edit.php"><img src="images/add.png" title="<?=gtext("Add auth group");?>" border="0" alt="<?=gtext("Add auth group");?>" /></a></td>
								</tr>
							</table>
							<?=gtext('Auth Groups contains authorised users and secrets for additional security.');?>
						</td>
					</tr>
				</table>
<?php
				include 'formend.inc';
?>
			</form>
		</td>
	</tr>
</table>
<?php
include 'fend.inc';
