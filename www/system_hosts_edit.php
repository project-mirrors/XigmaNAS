<?php
/*
	system_hosts_edit.php

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

$pgtitle = [gtext('Network'),gtext('Hosts'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_hosts = &array_make_branch($config,'system','hosts');
if(empty($a_hosts)):
else:
	array_sort_key($a_hosts,'name');
endif;

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_hosts, "uuid")))) {
	$pconfig['uuid'] = $a_hosts[$cnid]['uuid'];
	$pconfig['name'] = $a_hosts[$cnid]['name'];
	$pconfig['address'] = $a_hosts[$cnid]['address'];
	$pconfig['descr'] = $a_hosts[$cnid]['descr'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['address'] = "";
	$pconfig['descr'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: system_hosts.php");
		exit;
	}

	// Input validation.
	$reqdfields = ['name','address'];
	$reqdfieldsn = [gtext('Hostname'),gtext('IP Address')];
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);

	if (($_POST['name'] && !is_validdesc($_POST['name']))) {
		$input_errors[] = gtext("The host name contain invalid characters.");
	}
	if (($_POST['address'] && !is_ipaddr($_POST['address']))) {
		$input_errors[] = gtext("A valid IP address must be specified.");
	}

	// Check for duplicates.
	$index = array_search_ex($_POST['name'], $a_hosts, "name");
	if (FALSE !== $index) {
		if (!((FALSE !== $cnid) && ($a_hosts[$cnid]['uuid'] === $a_hosts[$index]['uuid']))) {
			$input_errors[] = gtext("An host with this name already exists.");
		}
	}

	if (empty($input_errors)) {
		$host = [];
		$host['uuid'] = $_POST['uuid'];
		$host['name'] = $_POST['name'];
		$host['address'] = $_POST['address'];
		$host['descr'] = $_POST['descr'];

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_hosts[$cnid] = $host;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_hosts[] = $host;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("hosts", $mode, $host['uuid']);
		write_config();

		header("Location: system_hosts.php");
		exit;
	}
}
include 'fbegin.inc';
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabcont">
		<form action="system_hosts_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<?php
			if(!empty($input_errors)):
				print_input_errors($input_errors);
			endif;
?>
			<table width="100%" border="0" cellpadding="6" cellspacing="0">
<?php
				html_titleline2(gettext('Hosts Setup'), 2);
				html_inputbox("name", gtext("Hostname"), $pconfig['name'], gtext("The host name may only consist of the characters a-z, A-Z and 0-9, - , _ and ."), true, 40);
				html_inputbox("address", gtext("IP Address"), $pconfig['address'], gtext("The IP address that this hostname represents."), true, 20);
				html_inputbox("descr", gtext("Description"), $pconfig['descr'], gtext("You may enter a description here for your reference."), false, 20);
?>
			</table>
			<div id="submit">
				<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gtext("Save") : gtext("Add")?>" />
				<input name="Cancel" type="submit" class="formbtn" value="<?=gtext("Cancel");?>" />
				<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
			</div>
<?php
			include 'formend.inc';
?>
		</form>
	</td></tr>
</table>
<?php
include 'fend.inc';
