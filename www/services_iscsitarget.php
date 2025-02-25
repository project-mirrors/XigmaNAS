<?php
/*
	services_iscsitarget.php

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

array_make_branch($config,'iscsitarget','portalgroup');
array_make_branch($config,'iscsitarget','initiatorgroup');
array_make_branch($config,'iscsitarget','authgroup');

function cmp_tag($a, $b) {
    if ($a['tag'] == $b['tag']) {
        return 0;
    }
    return ($a['tag'] > $b['tag']) ? 1 : -1;
}
usort($config['iscsitarget']['portalgroup'], "cmp_tag");
usort($config['iscsitarget']['initiatorgroup'], "cmp_tag");
usort($config['iscsitarget']['authgroup'], "cmp_tag");

$pconfig['enable'] = isset($config['iscsitarget']['enable']);
$pconfig['nodebase'] = $config['iscsitarget']['nodebase'];
$pconfig['discoveryauthmethod'] = $config['iscsitarget']['discoveryauthmethod'];
$pconfig['discoveryauthgroup'] = $config['iscsitarget']['discoveryauthgroup'];
$pconfig['timeout'] = $config['iscsitarget']['timeout'];
$pconfig['nopininterval'] = $config['iscsitarget']['nopininterval'];
$pconfig['maxr2t'] = $config['iscsitarget']['maxr2t'];
$pconfig['maxsessions'] = $config['iscsitarget']['maxsessions'];
$pconfig['maxconnections'] = $config['iscsitarget']['maxconnections'];
$pconfig['firstburstlength'] = $config['iscsitarget']['firstburstlength'];
$pconfig['maxburstlength'] = $config['iscsitarget']['maxburstlength'];
$pconfig['maxrecvdatasegmentlength'] = $config['iscsitarget']['maxrecvdatasegmentlength'];
$pconfig['maxoutstandingr2t'] = $config['iscsitarget']['maxoutstandingr2t'];
$pconfig['defaulttime2wait'] = $config['iscsitarget']['defaulttime2wait'];
$pconfig['defaulttime2retain'] = $config['iscsitarget']['defaulttime2retain'];

$pconfig['uctlenable'] = isset($config['iscsitarget']['uctlenable']);
$pconfig['uctladdress'] = $config['iscsitarget']['uctladdress'];
$pconfig['uctlport'] = $config['iscsitarget']['uctlport'];
$pconfig['uctlnetmask'] = $config['iscsitarget']['uctlnetmask'];
$pconfig['uctlauthmethod'] = $config['iscsitarget']['uctlauthmethod'];
$pconfig['uctlauthgroup'] = $config['iscsitarget']['uctlauthgroup'];
$pconfig['mediadirectory'] = $config['iscsitarget']['mediadirectory'];

if (!isset($pconfig['uctladdress']) || $pconfig['uctladdress'] == '') {
	$pconfig['uctladdress'] = "127.0.0.1";
	$pconfig['uctlport'] = "3261";
	$pconfig['uctlnetmask'] = "127.0.0.1/8";
	$pconfig['uctlauthmethod'] = "CHAP";
	$pconfig['uctlauthgroup'] = 0;
	$pconfig['mediadirectory'] = "/mnt";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;

	// Input validation.
	$reqdfields = ['nodebase','discoveryauthmethod','discoveryauthgroup'];
	$reqdfieldsn = [gtext('Node Base'),gtext('Discovery Auth Method'),gtext('Discovery Auth Group')];
	$reqdfieldst = ['string','string','numericint'];

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	$reqdfields = ['timeout','nopininterval','maxr2t','maxsessions','maxconnections','firstburstlength','maxburstlength','maxrecvdatasegmentlength','maxoutstandingr2t','defaulttime2wait','defaulttime2retain'];
	$reqdfieldsn = [gtext('I/O Timeout'),gtext('NOPIN Interval'),gtext('Max. Sessions'),gtext('Max. Connections'),gtext('Max. pre-send R2T'),gtext('FirstBurstLength'),gtext('MaxBurstLength'),
	gtext('MaxRecvDataSegmentLength'),gtext('MaxOutstandingR2T'),gtext('DefaultTime2Wait'),gtext('DefaultTime2Retain')];
	$reqdfieldst = explode(" ", "numericint numericint numericint numericint numericint numericint numericint numericint numericint numericint numericint");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if ((strcasecmp("Auto", $pconfig['discoveryauthmethod']) != 0
	   && strcasecmp("None", $pconfig['discoveryauthmethod']) != 0)
		&& $pconfig['discoveryauthgroup'] == 0) {
		$input_errors[] = sprintf(gtext("The attribute '%s' is required."), gtext("Discovery Auth Group"));
	}

	$reqdfields = ['uctladdress','uctlport','uctlnetmask','uctlauthmethod','uctlauthgroup','mediadirectory'];
	$reqdfieldsn = [gtext('Controller IP address'),gtext('Controller TCP Port'),gtext('Controller Authorised Network'),gtext('Controller Auth Method'),gtext('Controller Auth Group'),gtext('Media Directory')];
	$reqdfieldst = ['string','numericint','string','string','numericint','string'];

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if (isset($_POST['uctlenable'])) {
		if ((strcasecmp("Auto", $pconfig['uctlauthmethod']) != 0
		   && strcasecmp("None", $pconfig['uctlauthmethod']) != 0)
			&& $pconfig['uctlauthgroup'] == 0) {
			if (count($config['iscsitarget']['authgroup']) == 0) {
				$errormsg .= gtext('No configured Auth Group.')
					. ' '
					. '<a href="'
					. 'services_iscsitarget_ag.php' . '">'
					. gtext('Please add a new Auth Group first')
					. '</a>.<br />'
					. "\n";
			}
			$input_errors[] = sprintf(gtext("The attribute '%s' is required."), gtext("Controller Auth Group"));
		}
	}

	$nodebase = $_POST['nodebase'];
	$nodebase = preg_replace('/\s/', '', $nodebase);
	$pconfig['nodebase'] = $nodebase;
	if (empty($input_errors)) {
		$config['iscsitarget']['enable'] = isset($_POST['enable']) ? true : false;
		$config['iscsitarget']['nodebase'] = $nodebase;
		$config['iscsitarget']['discoveryauthmethod'] = $_POST['discoveryauthmethod'];
		$config['iscsitarget']['discoveryauthgroup'] = $_POST['discoveryauthgroup'];
		$config['iscsitarget']['timeout'] = $_POST['timeout'];
		$config['iscsitarget']['nopininterval'] = $_POST['nopininterval'];
		$config['iscsitarget']['maxr2t'] = $_POST['maxr2t'];
		$config['iscsitarget']['maxsessions'] = $_POST['maxsessions'];
		$config['iscsitarget']['maxconnections'] = $_POST['maxconnections'];
		$config['iscsitarget']['firstburstlength'] = $_POST['firstburstlength'];
		$config['iscsitarget']['maxburstlength'] = $_POST['maxburstlength'];
		$config['iscsitarget']['maxrecvdatasegmentlength'] = $_POST['maxrecvdatasegmentlength'];
		$config['iscsitarget']['maxoutstandingr2t'] = $_POST['maxoutstandingr2t'];
		$config['iscsitarget']['defaulttime2wait'] = $_POST['defaulttime2wait'];
		$config['iscsitarget']['defaulttime2retain'] = $_POST['defaulttime2retain'];

		$config['iscsitarget']['uctlenable'] = isset($_POST['uctlenable']) ? true : false;
		$config['iscsitarget']['uctladdress'] = $_POST['uctladdress'];
		$config['iscsitarget']['uctlport'] = $_POST['uctlport'];
		$config['iscsitarget']['uctlnetmask'] = $_POST['uctlnetmask'];
		$config['iscsitarget']['uctlauthmethod'] = $_POST['uctlauthmethod'];
		$config['iscsitarget']['uctlauthgroup'] = $_POST['uctlauthgroup'];
		$config['iscsitarget']['mediadirectory'] = $_POST['mediadirectory'];
		write_config();
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("iscsi_target");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		header('Location: services_iscsitarget.php');
		exit;
	}
}
$pgtitle = [gtext('Services'),gtext("iSCSI Target")];
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.nodebase.disabled = endis;
	document.iform.discoveryauthmethod.disabled = endis;
	document.iform.discoveryauthgroup.disabled = endis;
	document.iform.timeout.disabled = endis;
	document.iform.nopininterval.disabled = endis;
	document.iform.maxr2t.disabled = endis;
	document.iform.maxsessions.disabled = endis;
	document.iform.maxconnections.disabled = endis;
	document.iform.firstburstlength.disabled = endis;
	document.iform.maxburstlength.disabled = endis;
	document.iform.maxrecvdatasegmentlength.disabled = endis;
	document.iform.maxoutstandingr2t.disabled = endis;
	document.iform.defaulttime2wait.disabled = endis;
	document.iform.defaulttime2retain.disabled = endis;

	document.iform.uctladdress.disabled = endis;
	document.iform.uctlport.disabled = endis;
	document.iform.uctlnetmask.disabled = endis;
	document.iform.uctlauthmethod.disabled = endis;
	document.iform.uctlauthgroup.disabled = endis;
	document.iform.mediadirectory.disabled = endis;
}

function uctlenable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	var endis2 = !(document.iform.uctlenable.checked || enable_change);

	if (!endis2) {
		showElementById("uctladdress_tr", 'show');
		showElementById("uctlport_tr", 'show');
		showElementById("uctlnetmask_tr", 'show');
		showElementById("uctlauthmethod_tr", 'show');
		showElementById("uctlauthgroup_tr", 'show');
		showElementById("mediadirectory_tr", 'show');
	} else {
		showElementById("uctladdress_tr", 'hide');
		showElementById("uctlport_tr", 'hide');
		showElementById("uctlnetmask_tr", 'hide');
		showElementById("uctlauthmethod_tr", 'hide');
		showElementById("uctlauthgroup_tr", 'hide');
		showElementById("mediadirectory_tr", 'hide');
	}
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="services_iscsitarget.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Settings");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gtext("Targets");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gtext("Portals");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gtext("Initiators");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gtext("Auths");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_media.php"><span><?=gtext("Media");?></span></a></li>
	</ul></td></tr>
	<tr>
		<td class="tabcont">
			<form action="services_iscsitarget.php" method="post" name="iform" id="iform" onsubmit="spinner()">
				<?php if (!empty($errormsg)) print_error_box($errormsg);?>
				<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", gtext("iSCSI Target"), !empty($pconfig['enable']) ? true : false, gtext("Enable"), "enable_change(false)");?>
					<?php html_inputbox("nodebase", gtext("Base Name"), $pconfig['nodebase'], gtext("The base name (e.g. iqn.2007-09.jp.ne.peach.istgt) will append the target name that is not starting with 'iqn.'."), true, 60, false);?>
					<?php html_combobox("discoveryauthmethod", gtext("Discovery Auth Method"), $pconfig['discoveryauthmethod'], ['Auto' => gtext('Auto'), 'CHAP' => gtext('CHAP'), 'CHAP Mutual' => gtext('Mutual CHAP'), 'None' => gtext('None')], gtext("The method can be accepted in discovery session. Auto means both none and authentication."), true);?>
					<?php
					$ag_list = [];
					$ag_list['0'] = gtext("None");
					foreach($config['iscsitarget']['authgroup'] as $ag) {
						if ($ag['comment']) {
							$l = sprintf(gtext("Tag%d (%s)"), $ag['tag'], $ag['comment']);
						} else {
							$l = sprintf(gtext("Tag%d"), $ag['tag']);
						}
						$ag_list[$ag['tag']] = htmlspecialchars($l);
					}
					html_combobox("discoveryauthgroup", gtext("Discovery Auth Group"), $pconfig['discoveryauthgroup'], $ag_list, gtext("The initiator can discover the targets with correct user and secret in specific Auth Group."), true);
					html_separator();
					html_titleline(gtext("Advanced Settings"));
					html_inputbox("timeout", gtext("I/O Timeout"), $pconfig['timeout'], sprintf(gtext("I/O timeout in seconds (%d by default)."), 30), true, 30, false);
					html_inputbox("nopininterval", gtext("NOPIN Interval"), $pconfig['nopininterval'], sprintf(gtext("NOPIN sending interval in seconds (%d by default)."), 20), true, 30, false);
					html_inputbox("maxsessions", gtext("Max. Sessions"), $pconfig['maxsessions'], sprintf(gtext("Maximum number of sessions holding at same time (%d by default)."), 16), true, 30, false);
					html_inputbox("maxconnections", gtext("Max. Connections"), $pconfig['maxconnections'], sprintf(gtext("Maximum number of connections in each session (%d by default)."), 4), true, 30, false);
					html_inputbox("maxr2t", gtext("Max. pre-send R2T"), $pconfig['maxr2t'], sprintf(gtext("Maximum number of pre-send R2T in each connection (%d by default). The actual number is limited to QueueDepth of the target."), 32), true, 30, false);
					html_inputbox("firstburstlength", gtext("FirstBurstLength"), $pconfig['firstburstlength'], sprintf(gtext("iSCSI initial parameter (%d by default)."), 262144), true, 30, false);
					html_inputbox("maxburstlength", gtext("MaxBurstLength"), $pconfig['maxburstlength'], sprintf(gtext("iSCSI initial parameter (%d by default)."), 1048576), true, 30, false);
					html_inputbox("maxrecvdatasegmentlength", gtext("MaxRecvDataSegmentLength"), $pconfig['maxrecvdatasegmentlength'], sprintf(gtext("iSCSI initial parameter (%d by default)."), 262144), true, 30, false);
					html_inputbox("maxoutstandingr2t", gtext("MaxOutstandingR2T"), $pconfig['maxoutstandingr2t'], sprintf(gtext("iSCSI initial parameter (%d by default)."), 16), true, 30, false);
					html_inputbox("defaulttime2wait", gtext("DefaultTime2Wait"), $pconfig['defaulttime2wait'], sprintf(gtext("iSCSI initial parameter (%d by default)."), 2), true, 30, false);
					html_inputbox("defaulttime2retain", gtext("DefaultTime2Retain"), $pconfig['defaulttime2retain'], sprintf(gtext("iSCSI initial parameter (%d by default)."), 60), true, 30, false);
					html_separator();
					html_titleline_checkbox("uctlenable", gtext("iSCSI Target Logical Unit Controller"), !empty($pconfig['uctlenable']) ? true : false, gtext("Enable"), "uctlenable_change(false)");
					html_inputbox("uctladdress", gtext("Controller IP address"), $pconfig['uctladdress'], sprintf(gtext("Logical Unit Controller IP address (%s by default)"), "127.0.0.1(localhost)"), true, 30, false);
					html_inputbox("uctlport", gtext("Controller TCP Port"), $pconfig['uctlport'], sprintf(gtext("Logical Unit Controller TCP port (%d by default)"), 3261), true, 15, false);
					html_inputbox("uctlnetmask", gtext("Controller Authorised network"), $pconfig['uctlnetmask'], sprintf(gtext("Logical Unit Controller Authorised network (%s by default)"), "127.0.0.1/8"), true, 30, false);
					html_combobox("uctlauthmethod", gtext("Controller Auth Method"), $pconfig['uctlauthmethod'], ['CHAP' => gtext('CHAP'), 'CHAP mutual' => gtext('Mutual CHAP'), 'None' => gtext('None')], gtext("The method can be accepted in the controller."), true);
					$ag_list = [];
					$ag_list['0'] = gtext("Must choose one");
					foreach($config['iscsitarget']['authgroup'] as $ag) {
						if ($ag['comment']) {
							$l = sprintf(gtext("Tag%d (%s)"), $ag['tag'], $ag['comment']);
						} else {
							$l = sprintf(gtext("Tag%d"), $ag['tag']);
						}
						$ag_list[$ag['tag']] = htmlspecialchars($l);
					}
					html_combobox("uctlauthgroup", gtext("Controller Auth Group"), $pconfig['uctlauthgroup'], $ag_list, gtext("The istgtcontrol can access the targets with correct user and secret in specific Auth Group."), true);
					html_filechooser("mediadirectory", gtext("Media Directory"), $pconfig['mediadirectory'], gtext("Directory that contains removable media. (e.g /mnt/iscsi/)"), $g['media_path'], true);
					?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save & Restart");?>" onclick="enable_change(true)" />
				</div>
				<div id="remarks">
					<?php html_remark("note", gtext("Note"), sprintf(gtext("You must have a minimum of %dMiB RAM for using iSCSI target."), 512));?>
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!--
enable_change();
uctlenable_change();
//-->
</script>
<?php include 'fend.inc';?>
