<?php
/*
	services_ftp_mod_ban_edit.php

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

if (isset($_GET['uuid']))
	$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = [gtext('Services'),gtext('FTP'),gtext('Ban List Rule'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_rule = &array_make_branch($config,'ftpd','mod_ban','rule');

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_rule, "uuid")))) {
	$pconfig['uuid'] = $a_rule[$cnid]['uuid'];
	$pconfig['event'] = $a_rule[$cnid]['event'];
	$pconfig['occurrence'] = $a_rule[$cnid]['occurrence'];
	$pconfig['timeinterval'] = $a_rule[$cnid]['timeinterval'];
	$pconfig['expire'] = $a_rule[$cnid]['expire'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['event'] = "MaxConnectionsPerHost";
	$pconfig['occurrence'] = $config['ftpd']['maxconperip'];
	$pconfig['timeinterval'] = "hh:mm:ss";
	$pconfig['expire'] = "hh:mm:ss";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if (!empty($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: services_ftp_mod.php");
		exit;
	}

	// Input validation
	$reqdfields = ['event','occurrence','timeinterval','expire'];
	$reqdfieldsn = [gtext('Event'),gtext('Occurrence'),gtext('Time Interval'),gtext('Expire')];
	$reqdfieldst = ['string','numeric','time','time'];

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if (empty($input_errors)) {
		$rule = [];
		$rule['uuid'] = $_POST['uuid'];
		$rule['event'] = $_POST['event'];
		$rule['occurrence'] = $_POST['occurrence'];
		$rule['timeinterval'] = $_POST['timeinterval'];
		$rule['expire'] = $_POST['expire'];

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_rule[$cnid] = $rule;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_rule[] = $rule;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("ftpd_mod_ban", $mode, $rule['uuid']);
		write_config();

		header("Location: services_ftp_mod.php");
		exit;
	}
}
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
			<form action="services_ftp_mod_ban_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
				<?php if (!empty($input_errors)) print_input_errors($input_errors); ?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<?php html_titleline(gtext("Ban List Rule Settings"));?>
					<?php html_combobox("event", gtext("Event"), $pconfig['event'], ['AnonRejectPasswords' => 'AnonRejectPasswords','ClientConnectRate' => 'ClientConnectRate','MaxClientsPerClass' => 'MaxClientsPerClass','MaxClientsPerHost' => 'MaxClientsPerHost','MaxClientsPerUser' => 'MaxClientsPerUser','MaxConnectionsPerHost' => 'MaxConnectionsPerHost','MaxHostsPerUser' => 'MaxHostsPerUser','MaxLoginAttempts' => 'MaxLoginAttempts','TimeoutIdle' => 'TimeoutIdle','TimeoutNoTransfer' => 'TimeoutNoTransfer'], gtext("This rule is triggered whenever the selected event directive occurs."), true);?>
					<?php html_inputbox("occurrence", gtext("Occurrence"), $pconfig['occurrence'], gtext("This parameter says that if N occurrences of the event happen within the given time interval, then a ban is automatically added."), true, 2);?>
					<?php html_inputbox("timeinterval", gtext("Time Interval"), $pconfig['timeinterval'], gtext("Specifies the time interval in hh:mm:ss in which the given number of occurrences must happen to add the ban."), true, 8);?>
					<?php html_inputbox("expire", gtext("Expire"), $pconfig['expire'], gtext("Specifies the time in hh:mm:ss after which the ban expires."), true, 8);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gtext("Save") : gtext("Add")?>" />
					<input name="Cancel" type="submit" class="formbtn" value="<?=gtext("Cancel");?>" />
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
