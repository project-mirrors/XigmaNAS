<?php
/*
	interfaces_wlan_edit.php

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
require_once 'interfaces.inc';

if (isset($_GET['uuid']))
	$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = [gtext('Network'), gtext('Interface Management'), gtext('WLAN'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_wlans = &array_make_branch($config,'vinterfaces','wlan');
if(empty($a_wlans)):
else:
	array_sort_key($a_wlans,'if');
endif;

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_wlans, "uuid")))) {
	$pconfig['enable'] = isset($a_wlans[$cnid]['enable']);
	$pconfig['uuid'] = $a_wlans[$cnid]['uuid'];
	$pconfig['if'] = $a_wlans[$cnid]['if'];
	$pconfig['wlandev'] = $a_wlans[$cnid]['wlandev'];
	$pconfig['desc'] = $a_wlans[$cnid]['desc'];

	$pconfig['apmode'] = isset($a_wlans[$cnid]['apmode']);
	$pconfig['ap_ssid'] = $a_wlans[$cnid]['ap_ssid'];
	$pconfig['ap_channel'] = $a_wlans[$cnid]['ap_channel'];
	$pconfig['ap_encryption'] = $a_wlans[$cnid]['ap_encryption'];
	$pconfig['ap_keymgmt'] = $a_wlans[$cnid]['ap_keymgmt'];
	$pconfig['ap_pairwise'] = $a_wlans[$cnid]['ap_pairwise'];
	$pconfig['ap_psk'] = $a_wlans[$cnid]['ap_psk'];
	$pconfig['ap_extraoptions'] = $a_wlans[$cnid]['ap_extraoptions'];
	$pconfig['auxparam'] = "";
	if (isset($a_wlans[$cnid]['auxparam']) && is_array($a_wlans[$cnid]['auxparam']))
		$pconfig['auxparam'] = implode("\n", $a_wlans[$cnid]['auxparam']);
} else {
	$pconfig['enable'] = true;
	$pconfig['uuid'] = uuid();
	$pconfig['if'] = "wlan" . get_nextwlan_id();
	$pconfig['wlandev'] = "";
	$pconfig['desc'] = "";

	$pconfig['apmode'] = false;
	$pconfig['ap_ssid'] = "";
	$pconfig['ap_channel'] = "1";
	$pconfig['ap_encryption'] = "wpa";
	$pconfig['ap_keymgmt'] = "WPA-PSK";
	$pconfig['ap_pairwise'] = "CCMP";
	$pconfig['ap_psk'] = "";
	$pconfig['ap_extraoptions'] = "";
	$pconfig['auxparam'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: interfaces_wlan.php");
		exit;
	}

	// Input validation.
	$reqdfields = ['wlandev'];
	$reqdfieldsn = [gtext('Physical Interface')];
	$reqdfieldst = ['string','numeric'];
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);
	if (isset($_POST['apmode'])) {
		$reqdfields = ['ap_ssid','ap_channel','ap_psk'];
		$reqdfieldsn = [gtext('SSID'),gtext('Channel'),gtext('PSK')];
		$reqdfieldst = ['string','string','string'];
		do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);
		if (preg_match("/\ |,|\'|\"/", $_POST['ap_ssid']))
			$input_errors[] = sprintf(gtext("The attribute '%s' contains invalid characters."), gtext("SSID"));
		if (preg_match("/\ |,|\'|\"/", $_POST['ap_channel']))
			$input_errors[] = sprintf(gtext("The attribute '%s' contains invalid characters."), gtext("Channel"));
		if (!empty($_POST['ap_psk']) && (strlen($_POST['ap_psk']) < 8 || strlen($_POST['ap_psk']) > 63)) {
			$input_errors[] = sprintf(gtext("The attribute '%s' is required within %d or more characters to %d characters."), gtext("PSK"), 8, 63);
		}
	}

	if (empty($input_errors)) {
		$wlan = [];
		$wlan['enable'] = !empty($_POST['enable']) ? true : false;
		$wlan['uuid'] = $_POST['uuid'];
		$wlan['if'] = $_POST['if'];
		$wlan['wlandev'] = $_POST['wlandev'];
		$wlan['desc'] = $_POST['desc'];

		$wlan['apmode'] = isset($_POST['apmode']) ? true : false;
		$wlan['ap_ssid'] = $_POST['ap_ssid'];
		$wlan['ap_channel'] = $_POST['ap_channel'];
		$wlan['ap_encryption'] = $_POST['ap_encryption'];
		$wlan['ap_keymgmt'] = $_POST['ap_keymgmt'];
		$wlan['ap_pairwise'] = $_POST['ap_pairwise'];
		$wlan['ap_extraoptions'] = $_POST['ap_extraoptions'];
		$wlan['ap_psk'] = $_POST['ap_psk'];

		// Write additional parameters.
		unset($wlan['auxparam']);
		foreach (explode("\n", $_POST['auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$wlan['auxparam'][] = $auxparam;
		}

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_wlans[$cnid] = $wlan;
		} else {
			$a_wlans[] = $wlan;
		}

		write_config();
		touch($d_sysrebootreqd_path);

		header("Location: interfaces_wlan.php");
		exit;
	}
}

function get_nextwlan_id() {
	global $config;

	$id = 0;
	$a_wlan = $config['vinterfaces']['wlan'];

	if (false !== array_search_ex("wlan" . strval($id), $a_wlan, "if")) {
		do {
			$id++; // Increase ID until a unused one is found.
		} while (false !== array_search_ex("wlan" . strval($id), $a_wlan, "if"));
	}

	return $id;
}
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">//<![CDATA[
$(document).ready(function(){
	function apmode_change(apmode_change) {
		var val = !($('#apmode').prop('checked') || apmode_change);
		$('#ap_ssid').prop('disabled', val);
		$('#ap_channel').prop('disabled', val);
		$('#ap_encryption').prop('disabled', val);
		$('#ap_keymgmt').prop('disabled', val);
		$('#ap_pairwise').prop('disabled', val);
		$('#ap_psk').prop('disabled', val);
		$('#ap_extraoptions').prop('disabled', val);
		$('#auxparam').prop('disabled', val);
	}
	$('#apmode').click(function(){
		apmode_change(false);
	});
	$('input:submit').click(function(){
		apmode_change(true);
	});
	apmode_change(false);
});
//]]>
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabinact"><a href="interfaces_assign.php"><span><?=gtext("Management");?></span></a></li>
			<li class="tabact"><a href="interfaces_wlan.php" title="<?=gtext('Reload page');?>"><span><?=gtext("WLAN");?></span></a></li>
			<li class="tabinact"><a href="interfaces_vlan.php"><span><?=gtext("VLAN");?></span></a></li>
			<li class="tabinact"><a href="interfaces_lagg.php"><span><?=gtext("LAGG");?></span></a></li>
			<li class="tabinact"><a href="interfaces_bridge.php"><span><?=gtext("Bridge");?></span></a></li>
			<li class="tabinact"><a href="interfaces_carp.php"><span><?=gtext("CARP");?></span></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<form action="interfaces_wlan_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
			<?php if ($input_errors) print_input_errors($input_errors);?>
			<table width="100%" border="0" cellpadding="6" cellspacing="0">
			<?php html_titleline(gtext("WLAN Settings"));?>
				<?php
				$a_if = []; foreach (get_interface_wlist() as $ifk => $ifv) { if (preg_match('/wlan/i', $ifk)) { continue; } $a_if[$ifk] = htmlspecialchars("{$ifk} ({$ifv['mac']})"); };
				html_combobox("wlandev", gtext("Physical Interface"), $pconfig['wlandev'], $a_if, "", true);
				html_inputbox("desc", gtext("Description"), $pconfig['desc'], gtext("You may enter a description here for your reference."), false, 40);
				html_separator();
				html_titleline_checkbox("apmode", gtext("AP mode"), !empty($pconfig['apmode']) ? true : false, gtext("Enable"), "");
				html_inputbox("ap_ssid", gtext("SSID"), $pconfig['ap_ssid'], gtext("Set the desired Service Set Identifier (aka network name)."), true, 20);
				html_inputbox("ap_channel", gtext("Channel"), $pconfig['ap_channel'], "", true, 10);
				html_combobox("ap_encryption", gtext("Encryption"), $pconfig['ap_encryption'], ['wpa' => sprintf('%s / %s', gtext('WPA'), gtext('WPA2'))], "", true, false, "encryption_change()");
				html_combobox("ap_keymgmt", gtext("Key Management Protocol"), $pconfig['ap_keymgmt'], ['WPA-PSK' => gtext('WPA-PSK (Pre Shared Key)')], "", true);
				html_combobox("ap_pairwise", gtext("Pairwise"), $pconfig['ap_pairwise'], ['CCMP' => gtext('CCMP'), 'CCMP TKIP' => gtext('CCMP TKIP')], "", true);
				html_passwordbox("ap_psk", gtext("PSK"), $pconfig['ap_psk'], gtext("Enter the passphrase that will be used in WPA-PSK mode. This must be between 8 and 63 characters long."), true, 40);
				html_inputbox("ap_extraoptions", gtext("Extra Options"), $pconfig['ap_extraoptions'], gtext("Extra options to ifconfig (usually empty)."), false, 60);
				$helpinghand = '<a href="'
					. 'http://www.freebsd.org/cgi/man.cgi?query=hostapd.conf'
					. '" target="_blank">'
					. gtext('Please check the documentation')
					. '</a>.';
				html_textarea("auxparam", gtext("Additional Parameters"), $pconfig['auxparam'], sprintf(gtext("These parameters are added to %s."), "hostapd.conf") . " " . $helpinghand, false, 65, 5, false, false);
				?>
			</table>
			<div id="submit">
				<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gtext("Save") : gtext("Add")?>" />
				<input name="Cancel" type="submit" class="formbtn" value="<?=gtext("Cancel");?>" />
				<input name="enable" type="hidden" value="<?=$pconfig['enable'];?>" />
				<input name="if" type="hidden" value="<?=$pconfig['if'];?>" />
				<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
			</div>
			<?php include 'formend.inc';?>
		</form>
	</td>
</tr>
</table>
<?php include 'fend.inc';?>
