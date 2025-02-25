<?php
/*
	system_swap.php

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
use gui\document;

arr::make_branch($config,'system','swap');
$pconfig['enable'] = isset($config['system']['swap']['enable']);
$pconfig['type'] = $config['system']['swap']['type'];
$pconfig['mountpoint'] = !empty($config['system']['swap']['mountpoint']) ? $config['system']['swap']['mountpoint'] : "";
$pconfig['devicespecialfile'] = !empty($config['system']['swap']['devicespecialfile']) ? $config['system']['swap']['devicespecialfile'] : "";
$pconfig['size'] = !empty($config['system']['swap']['size']) ? $config['system']['swap']['size'] : "";

//$swapdevice = "NONE";
//if (file_exists("{$g['etc_path']}/swapdevice"))
//	$swapdevice = trim(file_get_contents("{$g['etc_path']}/swapdevice"));
//if (empty($_POST) && (empty($pconfig['enable']) || $pconfig['enable'] === false)) {
//	if ($swapdevice != "NONE")
//		$infomsg = sprintf("%s (%s)", gtext("This server uses default swap."), $swapdevice);
//}

if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	if(isset($_POST['enable'])):
		$reqdfields = ['type'];
		$reqdfieldsn = [gtext('Type')];
		$reqdfieldst = ['string'];
		if('device' === $_POST['type']):
			$reqdfields = array_merge($reqdfields,['devicespecialfile']);
			$reqdfieldsn = array_merge($reqdfieldsn,[gtext('Device')]);
			$reqdfieldst = array_merge($reqdfieldst,['string']);
		else:
			$reqdfields = array_merge($reqdfields,['mountpoint','size']);
			$reqdfieldsn = array_merge($reqdfieldsn,[gtext('Mount Point'),gtext('Size')]);
			$reqdfieldst = array_merge($reqdfieldst,['string','numeric']);
		endif;
		do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
		do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
	endif;
	if(empty($input_errors)):
		$config['system']['swap']['enable'] = isset($_POST['enable']);
		$config['system']['swap']['type'] = $_POST['type'];
		$config['system']['swap']['mountpoint'] = $_POST['mountpoint'];
		$config['system']['swap']['devicespecialfile'] = $_POST['devicespecialfile'];
		$config['system']['swap']['size'] = $_POST['size'];
		write_config();
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			config_lock();
			$retval |= rc_update_service('swaplate');
			config_unlock();
			if(isset($_POST['enable']) && (preg_match('/\S/',$_POST['devicespecialfile']) === 1)):
				$cmd = sprintf('swapon %s', escapeshellarg($_POST['devicespecialfile']));
				write_log($cmd);
				mwexec($cmd);
			endif;
		endif;
		$savemsg = get_std_save_message($retval);
	endif;
endif;
$pgtitle = [gtext('System'),gtext('Advanced'),gtext('Swap')];
include 'fbegin.inc';
?>
<script>
//<![CDATA[
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.type.disabled = endis;
	document.iform.mountpoint.disabled = endis;
	document.iform.size.disabled = endis;
	document.iform.devicespecialfile.disabled = endis;
}
function type_change() {
	switch (document.iform.type.value) {
	case "file":
		showElementById('mountpoint_tr','show');
		showElementById('size_tr','show');
		showElementById('devicespecialfile_tr','hide');
		break;

	case "device":
		showElementById('mountpoint_tr','hide');
		showElementById('size_tr','hide');
		showElementById('devicespecialfile_tr','show');
		break;
	}
}
//]]>
</script>
<?php
$document = new document();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('system_advanced.php',gettext('Advanced'))->
			ins_tabnav_record('system_email.php',gettext('Email'))->
			ins_tabnav_record('system_email_reports.php',gettext('Email Reports'))->
			ins_tabnav_record('system_monitoring.php',gettext('Monitoring'))->
			ins_tabnav_record('system_swap.php',gettext('Swap'),gettext('Reload page'),true)->
			ins_tabnav_record('system_rc.php',gettext('Command Scripts'))->
			ins_tabnav_record('system_cron.php',gettext('Cron'))->
			ins_tabnav_record('system_loaderconf.php',gettext('loader.conf'))->
			ins_tabnav_record('system_rcconf.php',gettext('rc.conf'))->
			ins_tabnav_record('system_sysctl.php',gettext('sysctl.conf'))->
			ins_tabnav_record('system_syslogconf.php',gettext('syslog.conf'));
$document->render();
?>
<form action="system_swap.php" method="post" name="iform" id="iform" onsubmit="spinner()" class="pagecontent"><div class="area_data_top"></div><div id="area_data_frame">
<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
	if(!empty($infomsg)):
		print_info_box($infomsg);
	endif;
	if(!empty($savemsg)):
		print_info_box($savemsg);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline_checkbox2('enable',gettext('Swap Memory'),!empty($pconfig['enable']),gettext('Enable'),'enable_change(false)');
?>
		</thead>
		<tbody>
<?php
			$swapinfo = system_get_swap_info();
			if (!empty($swapinfo)):
?>
				<tr>
					<td class="celltag"><?=gtext('This server uses default swap!');?></td>
					<td class="celldata">
						<table width="100%" border="0" cellspacing="10" cellpadding="1">
<?php
							arr::sort_key($swapinfo,'device');
							$ctrlid = 0;
							foreach ($swapinfo as $swapk => $swapv):
								$ctrlid++;
								$percent_used = rtrim($swapv['capacity'], "%");
								$tooltip_used = sprintf(gtext("%s used of %s"), $swapv['used'], $swapv['total']);
								$tooltip_available = sprintf(gtext("%s available of %s"),$swapv['avail'],$swapv['total']);
								echo "<tr><td><div id='swapusage'>";
								echo "<img src='images/bar_left.gif' class='progbarl' alt='' />";
								echo "<img src='images/bar_blue.gif' name='swapusage_{$ctrlid}_bar_used' id='swapusage_{$ctrlid}_bar_used' width='{$percent_used}' class='progbarcf' title='{$tooltip_used}' alt='' />";
								echo "<img src='images/bar_gray.gif' name='swapusage_{$ctrlid}_bar_free' id='swapusage_{$ctrlid}_bar_free' width='" . (100 - $percent_used) . "' class='progbarc' title='{$tooltip_available}' alt='' />";
								echo "<img src='images/bar_right.gif' class='progbarr' alt='' /> ";
								echo sprintf(gtext('%s of %s'),
									"<span name='swapusage_{$ctrlid}_capacity' id='swapusage_{$ctrlid}_capacity' class='capacity'>{$swapv['capacity']}</span>",
									$swapv['total']);
								echo "<br />";
								echo sprintf(gtext("Device: %s | Total: %s | Used: %s | Free: %s"),
									"<span name='swapusage_{$ctrlid}_device' id='swapusage_{$ctrlid}_device' class='device'>{$swapv['device']}</span>",
									"<span name='swapusage_{$ctrlid}_total' id='swapusage_{$ctrlid}_total' class='total'>{$swapv['total']}</span>",
									"<span name='swapusage_{$ctrlid}_used' id='swapusage_{$ctrlid}_used' class='used'>{$swapv['used']}</span>",
									"<span name='swapusage_{$ctrlid}_free' id='swapusage_{$ctrlid}_free' class='free'>{$swapv['avail']}</span>");
								echo "</div></td></tr>";
								if ($ctrlid < count($swapinfo)):
									echo "<tr><td><hr size='1' /></td></tr>";
								endif;
							endforeach;
?>
						</table>
					</td>
				</tr>
<?php
			endif;
			$l_type = [
				'file' => gettext('File'),
				'device' => gettext('Device')
			];
			html_combobox2('type',gettext('Type'),$pconfig['type'],$l_type,'',true,false,'type_change()');
			html_mountcombobox2('mountpoint',gettext('Mount Point'),$pconfig['mountpoint'],gettext('Select mount point where to create the swap file.'),true);
			html_inputbox2('size',gettext('Size'),$pconfig['size'],gettext('The size of the swap file in MB.'),true,10);
			html_inputbox2('devicespecialfile',gettext('Device'),$pconfig['devicespecialfile'],sprintf(gettext('Name of the device to use as swap device, e.g. %s.'),'/dev/da0s2b'),true,20);
?>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=gtext('Save');?>" onclick="enable_change(true)" />
	</div>
<?php
	include 'formend.inc';
?>
</div><div class="area_data_pot"></div></form>
<script>
//<![CDATA[
enable_change(false);
type_change(false);
//]]>
</script>
<?php
include 'fend.inc';
