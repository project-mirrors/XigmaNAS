<?php
/*
	diag_infos_ups.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018-2019 XigmaNAS <info@xigmanas.com>.
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
	of XigmaNAS, either expressed or implied.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';

if($_POST):
	$upsc_enable = $_POST['raw_upsc_enable'];
endif;

function tblrow($name,$value,$symbol = null,$id = null) {
	if(!$value):
		return;
	endif;
	if($symbol == '&deg;C'):
		$value = sprintf("%.1f",$value);
	endif;
	if($symbol == 'Hz'):
		$value = sprintf("%d",$value);
	endif;
	if($symbol == ' seconds' && $value > 60):
		$minutes = (int) ($value / 60);
		$seconds = $value % 60;
		if($minutes > 60):
			$hours = (int) ($minutes / 60);
			$minutes = $minutes % 60;
			$value = $hours;
			$symbol = ' hours ' . $minutes . ' minutes ' . $seconds . $symbol;
		else:
			$value = $minutes;
			$symbol = ' minutes ' . $seconds . $symbol;
		endif;
	endif;
	if($symbol == 'pre'):
		$value = '<pre class="cmdoutput">' . $value;
		$symbol = '</pre>';
	endif;
	print(<<<EOD
<tr id='{$id}'>
	<td class="celltag">{$name}</td>
	<td class="celldata">{$value}{$symbol}</td>
</tr>
EOD
	. PHP_EOL);
}
function tblrowbar($name,$value,$symbol,$red,$yellow,$green) {
	if(!$value):
		return;
	endif;
	$value = sprintf("%.1f",$value);
	$red = explode('-',$red);
	$yellow = explode('-',$yellow);
	$green = explode('-',$green);
	sort($red);
	sort($yellow);
	sort($green);

	if($value >= $red[0] && $value <= ($red[0]+9)):
		$color = 'black';
		$bgcolor = 'red';
	endif;
	if($value >= ($red[0]+10) && $value <= $red[1]):
		$color = 'white';
		$bgcolor = 'red';
	endif;
	if($value >= $yellow[0] && $value <= $yellow[1]):
		$color = 'black';
		$bgcolor = 'yellow';
	endif;
	if($value >= $green[0] && $value <= ($green[0]+9)):
		$color = 'black';
		$bgcolor = 'green';
	endif;
	if($value >= ($green[0]+10) && $value <= $green[1]):
		$color = 'white';
		$bgcolor = 'green';
	endif;
	print(<<<EOD
<tr>
	<td class="celltag">{$name}</td>
	<td class="celldata">
		<div style="width: 290px; height: 12px; border-top: thin solid gray; border-bottom: thin solid gray; border-left: thin solid gray; border-right: thin solid gray;">
			<div style="width: {$value}{$symbol}; height: 12px; background-color: {$bgcolor};">
				<div style="text-align: center; color: {$color}">{$value}{$symbol}</div>
			</div>
		</div>
	</td>
</tr>
EOD
	. PHP_EOL);
}
$pgtitle = [gtext('Diagnostics'),gtext('Information'),gtext('UPS')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
function upsc_enable_change() {
	var element = document.getElementById('raw_upsc_enable');
	if(element != null) {
		switch(element.checked) {
			case true:
				showElementById('upsc_raw_command','show');
				break;
			case false:
				showElementById('upsc_raw_command','hide');
				break;
		}
	}
}
//]]>
</script>
<?php
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('diag_infos_disks.php',gettext('Disks'))->
			ins_tabnav_record('diag_infos_disks_info.php',gettext('Disks (Info)'))->
			ins_tabnav_record('diag_infos_part.php',gettext('Partitions'))->
			ins_tabnav_record('diag_infos_smart.php',gettext('S.M.A.R.T.'))->
			ins_tabnav_record('diag_infos_space.php',gettext('Space Used'))->
			ins_tabnav_record('diag_infos_swap.php',gettext('Swap'))->
			ins_tabnav_record('diag_infos_mount.php',gettext('Mounts'))->
			ins_tabnav_record('diag_infos_raid.php',gettext('Software RAID'))->
			ins_tabnav_record('diag_infos_iscsi.php',gettext('iSCSI Initiator'))->
			ins_tabnav_record('diag_infos_ad.php',gettext('MS Domain'))->
			ins_tabnav_record('diag_infos_samba.php',gettext('CIFS/SMB'))->
			ins_tabnav_record('diag_infos_ftpd.php',gettext('FTP'))->
			ins_tabnav_record('diag_infos_rsync_client.php',gettext('RSYNC Client'))->
			ins_tabnav_record('diag_infos_netstat.php',gettext('Netstat'))->
			ins_tabnav_record('diag_infos_sockets.php',gettext('Sockets'))->
			ins_tabnav_record('diag_infos_ipmi.php',gettext('IPMI Stats'))->
			ins_tabnav_record('diag_infos_ups.php',gettext('UPS'),gettext('Reload page'),true);
$document->render();
?>
<table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!isset($config['ups']['enable'])):
?>
		<table class="area_data_settings">
			<colgroup>
				<col class="area_data_settings_col_tag">
				<col class="area_data_settings_col_data">
			</colgroup>
			<thead>
<?php
				html_titleline2(gettext('UPS Information & Status'));
?>
			</thead>
			<tbody>
				<tr>
					<td class="celltag"><?=gtext('Information');?></td>
					<td class="celldata">
<?php
						echo '<pre class="cmdoutput">';
						echo gtext('UPS is disabled.');
						echo '</pre>';
?>
					</td>
				</tr>
			</tbody>
		</table>
<?php
	else:
?>
		<table class="area_data_settings">
			<colgroup>
				<col class="area_data_settings_col_tag">
				<col class="area_data_settings_col_data">
			</colgroup>
			<thead>
<?php
				html_titleline2(gettext('UPS Information & Status'));
?>
			</thead>
			<tbody>
<?php
				if(isset($config['ups']['ups2'])):
?>
					<tr>
						<td class="celltag"><?=gtext('Selected UPS');?></td>
						<td class="celldata">
							<form name="form2" action="diag_infos_ups.php" method="get">
								<select name="if" class="formfld" onchange="submit()">
<?php
									$curif = $config['ups']['upsname'];
									if(isset($_GET['if']) && $_GET['if']):
										$curif = $_GET['if'];
									endif;
									$ifnum = $curif;
									$ifdescrs = [$config['ups']['upsname'] => $config['ups']['upsname'],$config['ups']['ups2_upsname'] => $config['ups']['ups2_upsname']];
									foreach($ifdescrs as $ifn => $ifd):
										echo '<option value="',$ifn,'"';
										if($ifn == $curif):
											echo ' selected="selected"';
										endif;
										echo '>',htmlspecialchars($ifd),'</option>',PHP_EOL;
									endforeach;
?>
								</select>
							</form>
						</td>
					</tr>
<?php
				else:
					$ifnum = $config['ups']['upsname'];
				endif;
				$cmd = "/usr/local/bin/upsc {$ifnum}@{$config['ups']['ip']}";
				$handle = popen($cmd,'r');
				if($handle):
					$read = fread($handle, 4096);
					pclose($handle);
					$lines = explode(PHP_EOL,$read);
					$ups = [];
					foreach($lines as $line):
						$line = explode(':', $line);
						$ups[$line[0]] = trim($line[1]);
					endforeach;
					if(count($lines) == 1):
						tblrow(gtext('ERROR:'), 'Data stale!');
					endif;
					tblrow(gtext('Manufacturer'), $ups['device.mfr']);
					tblrow(gtext('Model'), $ups['device.model']);
					tblrow(gtext('Type'), $ups['device.type']);
					tblrow(gtext('Serial number'), $ups['device.serial']);
					tblrow(gtext('Firmware version'), $ups['ups.firmware']);
					$status = explode(' ', $ups['ups.status']);
					foreach($status as $condition):
						if($disp_status):
							$disp_status .= ', ';
						endif;
						switch($condition):
							case 'WAIT':
								$disp_status .= gtext('UPS Waiting');
								break;
							case 'OFF':
								$disp_status .= gtext('UPS Off Line');
								break;
							case 'OL':
								$disp_status .= gtext('UPS On Line');
								break;
							case 'OB':
								$disp_status .= gtext('UPS On Battery');
								break;
							case 'TRIM':
								$disp_status .= gtext('SmartTrim');
								break;
							case 'BOOST':
								$disp_status .= gtext('SmartBoost');
								break;
							case 'OVER':
								$disp_status .= gtext('Overload');
								break;
							case 'LB':
								$disp_status .= gtext('Battery Low');
								break;
							case 'RB':
								$disp_status .= gtext('Replace Battery UPS');
								break;
							case 'CAL':
								$disp_status .= gtext('Calibration Battery');
								break;
							case 'CHRG':
								$disp_status .= gtext('Charging Battery');
								break;
							default:
								$disp_status .= $condition;
								break;
						endswitch;
					endforeach;
					tblrow(gtext('Status'), $disp_status);
					tblrowbar(gtext('Load'), $ups['ups.load'], '%', '100-80', '79-60', '59-0');
					tblrowbar(gtext('Battery level'), $ups['battery.charge'], '%', '0-29' ,'30-79', '80-100');
//					status
					tblrow(gtext('Battery voltage'), $ups['battery.voltage'], 'V');
					tblrow(gtext('Input voltage'), $ups['input.voltage'], 'V');
					tblrow(gtext('Input frequency'), $ups['input.frequency'], 'Hz');
					tblrow(gtext('Output voltage'), $ups['output.voltage'], 'V');
					tblrow(gtext('Temperature'), $ups['ups.temperature'], ' &deg;C');
					tblrow(gtext('Remaining battery runtime'), $ups['battery.runtime'], ' seconds');
					html_separator2();
//					output
					html_titleline2(gettext('UPS Unit General Information'));
					tblrow(gtext('UPS status'), $ups['ups.status']);
					tblrow(gtext('UPS alarms'), $ups['ups.alarm']);
					tblrow(gtext('Internal UPS clock time'), $ups['ups.time']);
					tblrow(gtext('Internal UPS clock date'), $ups['ups.date']);
					tblrow(gtext('UPS model'), $ups['ups.model']);
					tblrow(gtext('Manufacturer'), $ups['ups.mfr']);
					tblrow(gtext('Manufacturing date'), $ups['ups.mfr.date']);
					tblrow(gtext('Serial number'), $ups['ups.serial']);
					tblrow(gtext('Vendor ID'), $ups['ups.vendorid']);
					tblrow(gtext('Product ID'), $ups['ups.productid']);
					tblrow(gtext('UPS firmware'), $ups['ups.firmware']);
					tblrow(gtext('Auxiliary device firmware'), $ups['ups.firmware.aux']);
					tblrow(gtext('UPS temperature'), $ups['ups.temperature'], ' &deg;C');
					tblrow(gtext('UPS load'), $ups['ups.load'], '%');
					tblrow(gtext('Load when UPS switches to overload condition ("OVER")'), $ups['ups.load.high'], '%');
					tblrow(gtext('UPS system identifier'), $ups['ups.id']);
					tblrow(gtext('Interval to wait before restarting the load'), $ups['ups.delay.start'], ' seconds');
					tblrow(gtext('Interval to wait before rebooting the UPS)'), $ups['ups.delay.reboot'], ' seconds');
					tblrow(gtext('Interval to wait after shutdown with delay command'), $ups['ups.delay.shutdown'], ' seconds');
					tblrow(gtext('Time before the load will be started'), $ups['ups.timer.start'], ' seconds');
					tblrow(gtext('Time before the load will be rebooted'), $ups['ups.timer.reboot'], ' seconds');
					tblrow(gtext('Time before the load will be shutdown'), $ups['ups.timer.shutdown'], ' seconds');
					tblrow(gtext('Interval between self tests'), $ups['ups.test.interval'], ' seconds');
					tblrow(gtext('Results of last self test'), $ups['ups.test.result']);
					tblrow(gtext('Language to use on front panel'), $ups['ups.display.language']);
					tblrow(gtext('UPS external contact sensors'), $ups['ups.contacts']);
					tblrow(gtext('Efficiency of the UPS (Ratio of the output current on the input current)'), $ups['ups.efficiency'], '%');
					tblrow(gtext('Current value of apparent power (Volt-Amps)'), $ups['ups.power'], 'VA');
					tblrow(gtext('Nominal value of apparent power (Volt-Amps)'), $ups['ups.power.nominal'], 'VA');
					tblrow(gtext('Current value of real power (Watts)'), $ups['ups.realpower'], 'W');
					tblrow(gtext('Nominal value of real power (Watts)'), $ups['ups.realpower.nominal'], 'W');
					tblrow(gtext('UPS beeper status'), $ups['ups.beeper.status']);
					tblrow(gtext('UPS type'), $ups['ups.type']);
					tblrow(gtext('UPS watchdog status'), $ups['ups.watchdog.status']);
					tblrow(gtext('UPS starts when mains is (re)applied'), $ups['ups.start.auto']);
					tblrow(gtext('Allow to start UPS from battery'), $ups['ups.start.battery']);
					tblrow(gtext('UPS coldstarts from battery'), $ups['ups.start.reboot']);
					html_separator2();
					html_titleline2(gettext('Incoming Line/Power Information'));
					tblrow(gtext('Input voltage'), $ups['input.voltage'], 'V');
					tblrow(gtext('Maximum incoming voltage seen'), $ups['input.voltage.maximum'], 'V');
					tblrow(gtext('Minimum incoming voltage seen'), $ups['input.voltage.minimum'], 'V');
					tblrow(gtext('Nominal input voltage'), $ups['input.voltage.nominal'], 'V');
					tblrow(gtext('Extended input voltage range'), $ups['input.voltage.extended']);
					tblrow(gtext('Reason for last transfer to battery (* opaque)'), $ups['input.transfer.reason']);
					tblrow(gtext('Low voltage transfer point'), $ups['input.transfer.low'], 'V');
					tblrow(gtext('High voltage transfer point'), $ups['input.transfer.high'], 'V');
					tblrow(gtext('smallest settable low voltage transfer point'), $ups['input.transfer.low.min'], 'V');
					tblrow(gtext('greatest settable low voltage transfer point'), $ups['input.transfer.low.max'], 'V');
					tblrow(gtext('smallest settable high voltage transfer point'), $ups['input.transfer.high.min'], 'V');
					tblrow(gtext('greatest settable high voltage transfer point'), $ups['input.transfer.high.max'], 'V');
					tblrow(gtext('Input power sensitivity'), $ups['input.sensitivity']);
					tblrow(gtext('Input power quality (* opaque)'), $ups['input.quality']);
					tblrow(gtext('Input current (A)'), $ups['input.current'], 'A');
					tblrow(gtext('Nominal input current (A)'), $ups['input.current.nominal'], 'A');
					tblrow(gtext('Input line frequency (Hz)'), $ups['input.frequency'], 'Hz');
					tblrow(gtext('Nominal input line frequency (Hz)'), $ups['input.frequency.nominal'], 'Hz');
					tblrow(gtext('Input line frequency low (Hz)'), $ups['input.frequency.low'], 'Hz');
					tblrow(gtext('Input line frequency high (Hz)'), $ups['input.frequency.high'], 'Hz');
					tblrow(gtext('Extended input frequency range'), $ups['input.frequency.extended']);
					tblrow(gtext('Low voltage boosting transfer point'), $ups['input.transfer.boost.low'], 'V');
					tblrow(gtext('High voltage boosting transfer point'), $ups['input.transfer.boost.high'], 'V');
					tblrow(gtext('Low voltage trimming transfer point'), $ups['input.transfer.trim.low'], 'V');
					tblrow(gtext('High voltage trimming transfer point'), $ups['input.transfer.trim.high'], 'V');
					html_separator2();
					html_titleline2(gettext('Outgoing Power/Inverter Information'));
					tblrow(gtext('Output voltage (V)'), $ups['output.voltage'], 'V');
					tblrow(gtext('Nominal output voltage (V)'), $ups['output.voltage.nominal'], 'V');
					tblrow(gtext('Output frequency (Hz)'), $ups['output.frequency'], 'Hz');
					tblrow(gtext('Nominal output frequency (Hz)'), $ups['output.frequency.nominal'], 'Hz');
					tblrow(gtext('Output current (A)'), $ups['output.current'], 'A');
					tblrow(gtext('Nominal output current (A)'), $ups['output.current.nominal'], 'A');
					html_separator2();
					html_titleline2(gettext('Battery Details'));
					tblrow(gtext('Battery level'), $ups['battery.charge'], '%');
					tblrow(gtext('Battery Remaining level when UPS switches to Shutdown mode (Low Battery)'), $ups['battery.charge.low'], '%');
					tblrow(gtext('Minimum battery level for UPS restart after power-off'), $ups['battery.charge.restart'], '%');
					tblrow(gtext('Battery level when UPS switches to "Warning" state'), $ups['battery.charge.warning'], '%');
					tblrow(gtext('Battery voltage'), $ups['battery.voltage'], 'V');
					tblrow(gtext('Battery capacity'), $ups['battery.capacity'], 'Ah');
					tblrow(gtext('Battery current'), $ups['battery.current'], 'A');
					tblrow(gtext('Battery temperature'), $ups['battery.temperature'], ' &deg;C');
					tblrow(gtext('Nominal battery voltage'), $ups['battery.voltage.nominal'], 'V');
					tblrow(gtext('Remaining battery runtime'), $ups['battery.runtime'], ' seconds');
					tblrow(gtext('When UPS switches to Low Battery'), $ups['battery.runtime.low'], ' seconds');
					tblrow(gtext('Battery alarm threshold'), $ups['battery.alarm.threshold']);
					tblrow(gtext('Battery change date'), $ups['battery.date']);
					tblrow(gtext('Battery manufacturing date'), $ups['battery.mfr.date']);
					tblrow(gtext('Number of battery packs'), $ups['battery.packs']);
					tblrow(gtext('Number of bad battery packs'), $ups['battery.packs.bad']);
					tblrow(gtext('Battery chemistry'), $ups['battery.type']);
					tblrow(gtext('Prevent deep discharge of battery'), $ups['battery.protection']);
					tblrow(gtext('Switch off when running on battery and no/low load'), $ups['battery.energysave']);
					html_separator2();
					html_titleline2(gettext('Ambient Conditions From External Probe Equipment'));
					tblrow(gtext('Ambient temperature (degrees C)'), $ups['ambient.temperature'], ' &deg;C');
					tblrow(gtext('Temperature alarm (enabled/disabled)'), $ups['ambient.temperature.alarm']);
					tblrow(gtext('Temperature threshold high (degrees C)'), $ups['ambient.temperature.high'], ' &deg;C');
					tblrow(gtext('Temperature threshold low (degrees C)'), $ups['ambient.temperature.low'], ' &deg;C');
					tblrow(gtext('Maximum temperature seen (degrees C)'), $ups['ambient.temperature.maximum'], ' &deg;C');
					tblrow(gtext('Minimum temperature seen (degrees C)'), $ups['ambient.temperature.minimum'], ' &deg;C');
					tblrow(gtext('Ambient relative humidity (percent)'), $ups['ambient.humidity'], '%');
					tblrow(gtext('Relative humidity alarm (enabled/disabled)'), $ups['ambient.humidity.alarm']);
					tblrow(gtext('Relative humidity threshold high (percent)'), $ups['ambient.humidity.high'], '%');
					tblrow(gtext('Relative humidity threshold high (percent)'), $ups['ambient.humidity.low'], '%');
					tblrow(gtext('Maximum relative humidity seen (percent)'), $ups['ambient.humidity.maximum'], '%');
					tblrow(gtext('Minimum relative humidity seen (percent)'), $ups['ambient.humidity.minimum'], '%');
					html_separator2();
					html_titleline2(gettext('Smart Outlet Management'));
					tblrow('[Main] Outlet system identifier', $ups['outlet.id']);
					tblrow('[Main] Outlet description', $ups['outlet.desc']);
					tblrow('[Main] Outlet switch control (on/off)', $ups['outlet.switch']);
					tblrow('[Main] Outlet switch status (on/off)', $ups['outlet.status']);
					tblrow('[Main] Outlet switch ability (yes/no)', $ups['outlet.switchable']);
					tblrow('[Main] Remaining battery level to power off this outlet', $ups['outlet.autoswitch.charge.low'], '%');
					tblrow('[Main] Interval to wait before shutting down this outlet', $ups['outlet.delay.shutdown'], ' seconds');
					tblrow('[Main] Interval to wait before restarting this outlet', $ups['outlet.delay.start'], ' seconds');
					tblrow('[Main] Current (A)', $ups['outlet.current'], 'A');
					tblrow('[Main] Maximum seen current (A)', $ups['outlet.current.maximum'], 'A');
					tblrow('[Main] Current value of real power (W)', $ups['outlet.realpower'], 'W');
					tblrow('[Main] Voltage (V)', $ups['outlet.voltage'], 'V');
					tblrow('[Main] Power Factor (dimensionless value between 0 and 1)', $ups['outlet.powerfactor']);
					tblrow('[Main] Crest Factor (dimensionless, equal to or greater than 1)', $ups['outlet.crestfactor']);
					tblrow('[Main] Apparent power (VA)', $ups['outlet.power'], 'VA');
					for($i = 1; $ups['outlet.'.$i.'.id']; $i++):
						tblrow('['.$i.'] Outlet system identifier', $ups['outlet.'.$i.'.id']);
						tblrow('['.$i.'] Outlet description', $ups['outlet.'.$i.'.desc']);
						tblrow('['.$i.'] Outlet switch control (on/off)', $ups['outlet.'.$i.'.switch']);
						tblrow('['.$i.'] Outlet switch status (on/off)', $ups['outlet.'.$i.'.status']);
						tblrow('['.$i.'] Outlet switch ability (yes/no)', $ups['outlet.'.$i.'.switchable']);
						tblrow('['.$i.'] Remaining battery level to power off this outlet', $ups['outlet.'.$i.'.autoswitch.charge.low'], '%');
						tblrow('['.$i.'] Interval to wait before shutting down this outlet', $ups['outlet.'.$i.'.delay.shutdown'], ' seconds');
						tblrow('['.$i.'] Interval to wait before restarting this outlet', $ups['outlet.'.$i.'.delay.start'], ' seconds');
						tblrow('['.$i.'] Current (A)', $ups['outlet.'.$i.'.current'], 'A');
						tblrow('['.$i.'] Maximum seen current (A)', $ups['outlet.'.$i.'.current.maximum'], 'A');
						tblrow('['.$i.'] Current value of real power (W)', $ups['outlet.'.$i.'.realpower'], 'W');
						tblrow('['.$i.'] Voltage (V)', $ups['outlet.'.$i.'.voltage'], 'V');
						tblrow('['.$i.'] Power Factor (dimensionless value between 0 and 1)', $ups['outlet.'.$i.'.powerfactor']);
						tblrow('['.$i.'] Crest Factor (dimensionless, equal to or greater than 1)', $ups['outlet.'.$i.'.crestfactor']);
						tblrow('['.$i.'] Apparent power (VA)', $ups['outlet.'.$i.'.power'], 'VA');
					endfor;
					html_separator2();
					html_titleline2(gettext('NUT Internal Driver Information'));
					tblrow(gtext('Driver used'), $ups['driver.name']);
					tblrow(gtext('Driver version'), $ups['driver.version']);
					tblrow(gtext('Driver version internal'), $ups['driver.version.internal']);
					tblrow(gtext('Parameter xxx (ups.conf or cmdline -x) setting'), $ups['driver.parameter.xxx']);
					tblrow(gtext('Flag xxx (ups.conf or cmdline -x) status'), $ups['driver.flag.xxx']);
					html_separator2();
					html_titleline2(gettext('Internal Server Information'));
					tblrow(gtext('Server information'), $ups['server.info']);
					tblrow(gtext('Server version'), $ups['server.version']);
					html_separator2();
					html_titleline_checkbox2('raw_upsc_enable','NUT',$upsc_enable ? true : false,(gettext('Show RAW UPS Info')),'upsc_enable_change()');
					tblrow(gtext('RAW info'),htmlspecialchars($read), 'pre', 'upsc_raw_command');
					unset($handle,$read,$lines,$status,$disp_status,$ups);
				endif;
				unset($cmd);
?>
			</tbody>
		</table>
<?php
	endif;
?>
</td></tr></tbody></table>
<script type="text/javascript">
//<![CDATA[
upsc_enable_change();
//]]>
</script>
<?php
include 'fend.inc';
