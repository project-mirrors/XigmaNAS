<?php
/*
	index.php

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
// Configure page permission
$pgperm['allowuser'] = true;

require_once 'auth.inc';
require_once 'guiconfig.inc';
require_once 'zfs.inc';

$use_meter_tag = (is_bool($test = $config['system']['showcolorfulmeter'] ?? false) ? $test : true);
$pgtitle = [gtext('System Information')];
$pgtitle_omit = true;
array_make_branch($config,'vinterfaces','carp');
$sysinfo = system_get_sysinfo();
if(is_ajax()) {
	render_ajax($sysinfo);
}
function render_disk_usage(array $a_diskusage) {
	global $use_meter_tag;

	$a_diskusage_elements = count($a_diskusage);
	if($a_diskusage_elements > 0):
		echo '<tr>','<td class="celltag">',gtext('Disk Space Usage'),'</td>','<td class="celldata">','<table class="area_data_settings">','<tbody>';
		$index = 0;
		foreach($a_diskusage as $r_diskusage):
			$ctrlid = sprintf('diskusage_%s',$r_diskusage['id']);
			$percent_used = $r_diskusage['percentage'];
			$percent_free = 100 - $percent_used;
			$tooltip_used = $r_diskusage['tooltip']['used'];
			$tooltip_free = $r_diskusage['tooltip']['avail'];
			echo '<tr><td class="nopad"><div id="',$ctrlid,'">';
			echo '<span id="',$ctrlid,'_name" class="name">',$r_diskusage['name'],'</span>';
			echo '<br />';
			if($use_meter_tag):
				echo '<meter id="',$ctrlid,'_v" class="diskusage" min="0" max="100" value="',$percent_used,'" title="',$tooltip_used,'">';
			endif;
			echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
				'<img src="images/bar_blue.gif" id="',$ctrlid,'_bar_used" width="',$percent_used,'" class="progbarcf" title="',$tooltip_used,'" alt="" />',
				'<img src="images/bar_gray.gif" id="',$ctrlid,'_bar_free" width="',$percent_free,'" class="progbarc" title="',$tooltip_free,'" alt=""/>',
				'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
			if($use_meter_tag):
				echo '</meter>';
			endif;
			echo '<span id="',$ctrlid,'_capofsize" class="capofsize">',$r_diskusage['capofsize'],'</span>';
			echo '<br />';
			echo sprintf(gtext('Total: %s | Used: %s | Free: %s'),
				sprintf('<span id="%s_size" class="size">%s</span>',$ctrlid,$r_diskusage['size']),
				sprintf('<span id="%s_used" class="used">%s</span>',$ctrlid,$r_diskusage['used']),
				sprintf('<span id="%s_avail" class="avail">%s</span>',$ctrlid,$r_diskusage['avail']));
			echo '</div></td></tr>',"\n";
			$index++;
			if($index < $a_diskusage_elements):
				echo '<tr><td class="nopad"><hr /></td></tr>',"\n";
			endif;
		endforeach;
		echo '</tbody>','</table>','</td>','</tr>';
	endif;
	return;
}
function render_pool_usage(array $a_poolusage) {
	global $config,$use_meter_tag;

	$a_poolusage_elements = count($a_poolusage);
	if($a_poolusage_elements > 0):
		echo '<tr>','<td class="celltag">',gtext('Pool Space Usage'),'</td>','<td class="celldata">','<table class="area_data_settings">','<tbody>';
		$index = 0;
		foreach($a_poolusage as $r_poolusage):
			$ctrlid = sprintf('poolusage_%s',$r_poolusage['id']);
			echo '<tr><td class="nopad"><div id="',$ctrlid,'">';
			$percent_used = $r_poolusage['percentage'];
			$percent_free = 100 - $percent_used;
			$tooltip_used = $r_poolusage['tooltip']['used'];
			$tooltip_free = $r_poolusage['tooltip']['avail'];
			echo '<span id="',$ctrlid,'_name" class="name">',$r_poolusage['name'],'</span>';
			echo '<br />';
			$zfs_settings = &array_make_branch($config,'zfs','settings');
			if(array_key_exists('capacity_warning',$zfs_settings)):
				$zfs_warning = filter_var($zfs_settings['capacity_warning'],FILTER_VALIDATE_INT,['options' => ['default' => 80,'min' => 80,'max' => 89]]);
			else:
				$zfs_warning = 80;
			endif;
			if(array_key_exists('capacity_critical',$zfs_settings)):
				$zfs_critical = filter_var($zfs_settings['capacity_critical'],FILTER_VALIDATE_INT,['options' => ['default' => 90,'min' => 90,'max' => 95]]);
			else:
				$zfs_critical = 90;
			endif;
			if($use_meter_tag):
				echo '<meter id="',$ctrlid,'_v" class="poolusage" min="0" max="100" optimum="40" low="',$zfs_warning,'" high="',$zfs_critical,'" value="',$percent_used,'" title="',$tooltip_used,'">';
			endif;
			echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
				'<img src="images/bar_blue.gif" id="',$ctrlid,'_bar_used" width="',$percent_used,'" class="progbarcf" title="',$tooltip_used,'" alt=""/>',
				'<img src="images/bar_gray.gif" id="',$ctrlid,'_bar_free" width="',$percent_free,'" class="progbarc" title="',$tooltip_free,'" alt=""/>',
				'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
			if($use_meter_tag):
				echo '</meter>';
			endif;
			echo '<span id="',$ctrlid,'_capofsize" class="capofsize">',$r_poolusage['capofsize'],'</span>';
			echo '<br />',"\n";
			echo sprintf(gtext('Total: %s | %s: %s | %s: %s | State: %s'),
				sprintf('<span id="%s_size" class="size">%s</span>',$ctrlid,$r_poolusage['size']),
				sprintf('<span id="%s_gt_used">%s</span>',$ctrlid,$r_poolusage['gt_used']),
				sprintf('<span id="%s_used" class="used">%s</span>',$ctrlid,$r_poolusage['used']),
				sprintf('<span id="%s_gt_avail">%s</span>',$ctrlid,$r_poolusage['gt_avail']),
				sprintf('<span id="%s_avail" class="avail">%s</span>',$ctrlid,$r_poolusage['avail']),
				sprintf('<span id="%s_state" class="state"><a href="disks_zfs_zpool_info.php?%s">%s</a></span>',$ctrlid,http_build_query(['pool' => $r_poolusage['name']],NULL,ini_get('arg_separator.output'),PHP_QUERY_RFC3986),$r_poolusage['health']));
			echo '</div></td></tr>',"\n";
			$index++;
			if($index < $a_poolusage_elements):
				echo '<tr><td class="nopad"><hr /></td></tr>',"\n";
			endif;
		endforeach;
		echo '</tbody>','</table>','</td>','</tr>';
	endif;
	return;
}
function render_swap_usage(array $a_swapusage) {
	global $use_meter_tag;

	$a_swapusage_elements = count($a_swapusage);
	if($a_swapusage_elements > 0):
		echo '<tr>','<td class="celltag">',gtext('Swap Usage'),'</td>','<td class="celldata">','<table class="area_data_settings">','<tbody>';
		$index = 0;
		foreach($a_swapusage as $r_swapusage):
			$ctrlid = sprintf('swapusage_%s',$r_swapusage['id']);
			echo '<tr><td class="nopad"><div id="',$ctrlid,'">';
			$percent_used = $r_swapusage['percentage'];
			$percent_free = 100 - $percent_used;
			$tooltip_used = $r_swapusage['tooltip']['used'];
			$tooltip_free = $r_swapusage['tooltip']['avail'];
			if($use_meter_tag):
				echo '<meter id="',$ctrlid,'_v" class="swapusage" min="0" max="100" high="95" value="',$percent_used,'" title="',$tooltip_used,'">';
			endif;
			echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
				'<img src="images/bar_blue.gif" id="',$ctrlid,'_bar_used" width="',$percent_used,'" class="progbarcf" title="',$tooltip_used,'" alt=""/>',
				'<img src="images/bar_gray.gif" id="',$ctrlid,'_bar_free" width="',$percent_free,'" class="progbarc" title="',$tooltip_free,'" alt=""/>',
				'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
			if($use_meter_tag):
				echo '</meter>';
			endif;
			echo '<span id="',$ctrlid,'_capofsize" class="capofsize">',$r_swapusage['capofsize'],'</span>';
			echo '<br />';
			echo sprintf(gtext('Device: %s | Total: %s | Used: %s | Free: %s'),
				sprintf('<span id="%s_name" class="name">%s</span>',$ctrlid,$r_swapusage['name']),
				sprintf('<span id="%s_size" class="size">%s</span>',$ctrlid,$r_swapusage['size']),
				sprintf('<span id="%s_used" class="used">%s</span>',$ctrlid,$r_swapusage['used']),
				sprintf('<span id="%s_avail" class="avail">%s</span>',$ctrlid,$r_swapusage['avail']));
			echo '</div></td></tr>',"\n";
			$index++;
			if($index < $a_swapusage_elements):
				echo '<tr><td class="nopad"><hr /></td></tr>',"\n";
			endif;
		endforeach;
		echo '</tbody>','</table>','</td>','</tr>';
	endif;
	return;
}
function render_ups_info(array $a_upsinfo) {
	global $use_meter_tag;

	$a_upsinfo_elements = count($a_upsinfo);
	if($a_upsinfo_elements > 0):
		echo '<tr>',
			'<td class="celltag">',gtext('UPS Status'),'</td>',
			'<td class="celldata">','<table class="area_data_settings" style="table-layout:auto;white-space:nowrap;">','<tbody>';
		$index = 0;
		foreach($a_upsinfo as $ui):
			$id = sprintf('ups_status_%s_',$ui['id']);
			echo '<tr>',
				'<td class="padr03">',gtext('Identifier:'),'</td>',
				'<td class="padr1" id="',$id,'name">',htmlspecialchars($ui['name']),'</td>',
				'<td class="nopad"><a href="diag_infos_ups.php">',gtext('Show UPS Information'),'</a></td>',
				'<td class="nopad100"></td>',
				'</tr>';
			echo '<tr>';
			echo '<td class="padr03">',gtext('Status:'),'</td>',
				'<td class="nopad" colspan="2" id="',$id,'disp_status">',$ui['disp_status'],'</td>',
				'<td class="nopad100"></td>';
				'</tr>';
//			load
			$idl = $id . 'load_';
			$uil = $ui['load'];
			$percent_used = (int)$uil['percentage'];
			$percent_free = 100 - $percent_used;
			$tooltip_used = sprintf('%s%%',$percent_used);
			$tooltip_free = sprintf('%s%% %s',$percent_free,gettext('available'));
			echo '<tr>';
			echo '<td class="padr03">',gtext('Load:'),'</td>';
			echo '<td class="nopad">';
			if($use_meter_tag):
				echo '<meter id="',$idl,'v" class="upsusage" min="0" optimum="30" low="60" high="80" max="100" value="',$percent_used,'" title="',$tooltip_used,'">';
			endif;
			echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
				'<img src="images/bar_blue.gif" id="',$idl,'bar_used" width="',$percent_used,'" class="progbarcf" title="',$tooltip_used,'" alt=""/>',
				'<img src="images/bar_gray.gif" id="',$idl,'bar_free" width="',$percent_free,'" class="progbarc" title="',$tooltip_free,'" alt=""/>',
				'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
			if($use_meter_tag):
				echo '</meter>';
			endif;
			echo '</td>';
			echo '<td class="nopad">',
				'<span id="',$idl,'used" class="capacity">',$percent_used,'%</span>',
				'</td>';
			echo '<td class="nopad100"></td>';
			echo '</tr>';
//			battery charge level
			$idb = $id . 'battery_';
			$uib = $ui['battery'];
			$percent_used = (int)$uib['percentage'];
			$percent_free = 100 - $percent_used;
			$tooltip_used = sprintf('%s%%',$percent_used);
			$tooltip_free = sprintf('%s%% %s',$percent_free,gtext('available'));
			echo '<tr>';
			echo '<td class="padr03">',gtext('Battery Level:'),'</td>';
			echo '<td class="nopad">';
			if($use_meter_tag):
				echo '<meter id="',$idb,'v" class="upsusage" min="0" low="30" high="80" optimum="90" max="100" value="',$percent_used,'" title="',$tooltip_used,'">';
			endif;
			echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
				'<img src="images/bar_blue.gif" id="',$idb,'bar_used" width="',$percent_used,'" class="progbarcf" title="',$tooltip_used,'" alt=""/>',
				'<img src="images/bar_gray.gif" id="',$idb,'bar_free" width="',$percent_free,'" class="progbarc" title="',$tooltip_free,'" alt=""/>',
				'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
			if($use_meter_tag):
				echo '</meter>';
			endif;
			echo '</td>';
			echo '<td class="nopad">',
				'<span id="',$idb,'used" class="capacity">',$percent_used,'%</span>',
				'</td>';
			echo '<td class="nopad100"></td>';
			echo '</tr>',"\n";
			$index++;
			if($index < $a_upsinfo_elements):
				echo '<tr><td class="nopad" colspan="4"><hr /></td></tr>',"\n";
			endif;
		endforeach;
		echo '</tbody>','</table>','</td>','</tr>';
	endif;
}
if(function_exists('date_default_timezone_set') and function_exists('date_default_timezone_get')):
	@date_default_timezone_set(@date_default_timezone_get());
endif;
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var gui = new GUI;
	gui.recall(5000, 5000, 'index.php', null, function(data) {
		if ($('#vipstatus').length > 0) {
			$('#vipstatus').text(data.vipstatus);
		}
		if ($('#system_uptime').length > 0) {
			$('#system_uptime').text(data.uptime);
		}
		if ($('#system_datetime').length > 0) {
			$('#system_datetime').text(data.date);
		}
		if ($('#memusagev').length > 0) {
			$('#memusagev').attr('value', data.memusage.usedpct);
		}
		if ($('#memusageu').length > 0) {
			$('#memusageu').attr('width', data.memusage.usedpct);
		}
		if ($('#memusagef').length > 0) {
			$('#memusagef').attr('width', data.memusage.freepct);
		}
		if ($('#memusagep').length > 0) {
			$('#memusagep').text(data.memusage.caption);
		}
		if ($('#loadaverage').length > 0) {
			$('#loadaverage').text(data.loadaverage);
		}
		if (typeof(data.cputemp) !== 'undefined') {
			if ($('#cputemp').length > 0) {
				$('#cputemp').text(data.cputemp);
			}
		}
		if (typeof(data.cputemp2) !== 'undefined') {
			for (var idx = 0; idx < data.cputemp2.length; idx++) {
				if ($('#cputemp'+idx).length > 0) {
					$('#cputemp'+idx).text(data.cputemp2[idx]);
				}
			}
		}
		if (typeof(data.cpufreq) !== 'undefined') {
			if ($('#cpufreq').length > 0) {
				$('#cpufreq').text(data.cpufreq + 'MHz');
			}
		}
		if (typeof(data.cpuusage) !== 'undefined') {
			if ($('#cpuusagev').length > 0) {
				$('#cpuusagev').attr('value',data.cpuusage);
			}
			if ($('#cpuusageu').length > 0) {
				$('#cpuusageu').attr('width',data.cpuusage);
			}
			if ($('#cpuusagef').length > 0) {
				$('#cpuusagef').attr('width',(100 - data.cpuusage));
			}
			if ($('#cpuusagep').length > 0) {
				$('#cpuusagep').text(data.cpuusage + '%');
			}
		}
		if (typeof(data.cpuusage2) !== 'undefined') {
			for (var idx = 0; idx < data.cpuusage2.length; idx++) {
				if ($('#cpuusagev'+idx).length > 0) {
					$('#cpuusagev'+idx).attr('value', data.cpuusage2[idx]);
				}
				if ($('#cpuusagep'+idx).length > 0) {
					$('#cpuusagep'+idx).text(data.cpuusage2[idx] + '%');
				}
				if ($('#cpuusageu'+idx).length > 0) {
					$('#cpuusageu'+idx).attr('width', data.cpuusage2[idx]);
				}
				if ($('#cpuusagef'+idx).length > 0) {
					$('#cpuusagef'+idx).attr('width', (100 - data.cpuusage2[idx]));
				}
			}
		}
		if (typeof(data.diskusage) !== 'undefined') {
			for (var idx = 0; idx < data.diskusage.length; idx++) {
				var du = data.diskusage[idx];
				var id_prefix = '#diskusage_'+du.id+'_';
				if ($(id_prefix+'name').length > 0) {
					$(id_prefix+'name').text(du.name);
				}
				if ($(id_prefix+'v').length > 0) {
					$(id_prefix+'v').attr('value', du.percentage);
				}
				if ($(id_prefix+'bar_used').length > 0) {
					$(id_prefix+'bar_used').attr('width', du.percentage);
					$(id_prefix+'bar_used').attr('title', du['tooltip'].used);
				}
				if ($(id_prefix+'bar_free').length > 0) {
					$(id_prefix+'bar_free').attr('width', (100 - du.percentage));
					$(id_prefix+'bar_free').attr('title', du['tooltip'].avail);
				}
				if ($(id_prefix+'capacity').length > 0) {
					$(id_prefix+'capacity').text(du.capacity);
				}
				if ($(id_prefix+'capofsize').length > 0) {
					$(id_prefix+'capofsize').text(du.capofsize);
				}
				if ($(id_prefix+'size').length > 0) {
					$(id_prefix+'size').text(du.size);
				}
				if ($(id_prefix+'used').length > 0) {
					$(id_prefix+'used').text(du.used);
				}
				if ($(id_prefix+'avail').length > 0) {
					$(id_prefix+'avail').text(du.avail);
				}
			}
		}
		if (typeof(data.poolusage) !== 'undefined') {
			for (var idx = 0; idx < data.poolusage.length; idx++) {
				var pu = data.poolusage[idx];
				var id_prefix = '#poolusage_'+pu.id+'_';
				if ($(id_prefix+'name').length > 0) {
					$(id_prefix+'name').text(pu.name);
				}
				if ($(id_prefix+'v').length > 0) {
					$(id_prefix+'v').attr('value', pu.percentage);
				}
				if ($(id_prefix+'bar_used').length > 0) {
					$(id_prefix+'bar_used').attr('width', pu.percentage);
					$(id_prefix+'bar_used').attr('title', pu['tooltip'].used);
				}
				if ($(id_prefix+'bar_free').length > 0) {
					$(id_prefix+'bar_free').attr('width', (100 - pu.percentage));
					$(id_prefix+'bar_free').attr('title', pu['tooltip'].avail);
				}
				if ($(id_prefix+'capacity').length > 0) {
					$(id_prefix+'capacity').text(pu.capacity);
				}
				if ($(id_prefix+'capofsize').length > 0) {
					$(id_prefix+'capofsize').text(pu.capofsize);
				}
				if ($(id_prefix+'size').length > 0) {
					$(id_prefix+'size').text(pu.size);
				}
				if ($(id_prefix+'gt_used').length > 0) {
					$(id_prefix+'gt_used').text(pu.gt_used);
				}
				if ($(id_prefix+'used').length > 0) {
					$(id_prefix+'used').text(pu.used);
				}
				if ($(id_prefix+'gt_avail').length > 0) {
					$(id_prefix+'gt_avail').text(pu.gt_avail);
				}
				if ($(id_prefix+'avail').length > 0) {
					$(id_prefix+'avail').text(pu.avail);
				}
				if ($(id_prefix+'state').length > 0) {
					$(id_prefix+'state').children().text(pu.health);
				}
			}
		}
		if (typeof(data.swapusage) !== 'undefined') {
			for (var idx = 0; idx < data.swapusage.length; idx++) {
				var su = data.swapusage[idx];
				var id_prefix = '#swapusage_'+su.id+'_';
				if ($(id_prefix+'name').length > 0) {
					$(id_prefix+'name').text(su.name);
				}
				if ($(id_prefix+'v').length > 0) {
					$(id_prefix+'v').attr('value', su.percentage);
				}
				if ($(id_prefix+'bar_used').length > 0) {
					$(id_prefix+'bar_used').attr('width', su.percentage);
					$(id_prefix+'bar_used').attr('title', su['tooltip'].used);
				}
				if ($(id_prefix+'bar_free').length > 0) {
					$(id_prefix+'bar_free').attr('width', (100 - su.percentage));
					$(id_prefix+'bar_free').attr('title', su['tooltip'].avail);
				}
				if ($(id_prefix+'capacity').length > 0) {
					$(id_prefix+'capacity').text(su.capacity);
				}
				if ($(id_prefix+'capofsize').length > 0) {
					$(id_prefix+'capofsize').text(su.capofsize);
				}
				if ($(id_prefix+'size').length > 0) {
					$(id_prefix+'size').text(su.size);
				}
				if ($(id_prefix+'used').length > 0) {
					$(id_prefix+'used').text(su.used);
				}
				if ($(id_prefix+'avail').length > 0) {
					$(id_prefix+'avail').text(su.avail);
				}
			}
		}
		if (typeof(data.upsinfo) !== 'undefined') {
			for (var idx = 0; idx < data.upsinfo.length; idx++) {
				var ui = data.upsinfo[idx];
				var id_prefix = '#ups_status_'+ui.id+'_';
				if ($(id_prefix+'name').length > 0) {
					$(id_prefix+'name').text(ui.name);
				}
				if ($(id_prefix+'disp_status').length > 0) {
					$(id_prefix+'disp_status').text(ui.disp_status);
				}
				var uil = ui['load'];
				var id_prefix = '#ups_status_'+ui.id+'_load_';
				if ($(id_prefix+'v').length > 0) {
					$(id_prefix+'v').attr('value', uil.percentage);
				}
				if ($(id_prefix+'bar_used').length > 0) {
					$(id_prefix+'bar_used').attr('width', uil.percentage);
					$(id_prefix+'bar_used').attr('title', uil.tooltip_used);
				}
				if ($(id_prefix+'bar_free').length > 0) {
					$(id_prefix+'bar_free').attr('width', (100 - uil.percentage));
					$(id_prefix+'bar_free').attr('title', uil.tooltip_available);
				}
				if ($(id_prefix+'used').length > 0) {
					$(id_prefix+'used').text(uil.used+'%');
				}
				var uib = ui['battery'];
				var id_prefix = '#ups_status_'+ui.id+'_battery_';
				if ($(id_prefix+'v').length > 0) {
					$(id_prefix+'v').attr('value', uib.percentage);
				}
				if ($(id_prefix+'bar_used').length > 0) {
					$(id_prefix+'bar_used').attr('width', uib.percentage);
					$(id_prefix+'bar_used').attr('title', uib.tooltip_used);
				}
				if ($(id_prefix+'bar_free').length > 0) {
					$(id_prefix+'bar_free').attr('width', (100 - uib.percentage));
					$(id_prefix+'bar_free').attr('title', uib.tooltip_available);
				}
				if ($(id_prefix+'used').length > 0) {
					$(id_prefix+'used').text(uib.used+'%');
				}
			}
		}
	});
});
//]]>
</script>
<?php
//	make sure normal user such as www can write to temporary
	$perms = fileperms("/tmp");
	if(($perms & 01777) != 01777):
		$errormsg .= sprintf(gtext("Wrong permission on %s."), "/tmp");
		$errormsg .= "<br />\n";
	endif;
	$perms = fileperms("/var/tmp");
	if(($perms & 01777) != 01777):
		$errormsg .= sprintf(gtext("Wrong permission on %s."), "/var/tmp");
		$errormsg .= "<br />\n";
	endif;
//	check DNS
	list($v4dns1,$v4dns2) = get_ipv4dnsserver();
	list($v6dns1,$v6dns2) = get_ipv6dnsserver();
	if(empty($v4dns1) && empty($v4dns2) && empty($v6dns1) && empty($v6dns2)):
		// need by service/firmware check?
		if(!isset($config['system']['disablefirmwarecheck']) || isset($config['ftpd']['enable'])):
			$errormsg .= gtext('No DNS setting found.');
			$errormsg .= "<br />\n";
		endif;
	endif;
	if(Session::isAdmin()):
		$lastconfigbackupstate = 0;
		if(isset($config['lastconfigbackup'])):
			$lastconfigbackup = intval($config['lastconfigbackup']);
			$now = time();
			if(($lastconfigbackup > 0) && ($lastconfigbackup < $now)):
				$test = $config['system']['backup']['settings']['reminderintervalshow'] ?? 28;
				$reminderintervalshow = filter_var($test,FILTER_VALIDATE_INT,['options' => ['default' => 28,'min_range' => 0,'max_range' => 9999]]);
				if($reminderintervalshow > 0):
					if(($now - $lastconfigbackup) > $reminderintervalshow * 24 * 60 * 60):
						$lastconfigbackupstate = 1;
					endif;
				endif;
			else:
				$lastconfigbackupstate = 2;
			endif;
		else:
			$lastconfigbackupstate = 3;
		endif;
		switch($lastconfigbackupstate):
			case 1:
				$errormsg .= gtext('Backup configuration reminder. The last configuration backup is older than the configured interval.');
				$errormsg .= '<br />';
				break;
			case 2:
				$errormsg .= gtext('Backup configuration. The date of the last configuration backup is invalid.');
				$errormsg .= '<br />';
				break;
			case 3:
				$errormsg .= gtext('Backup configuration. The date of the last configuration backup cannot be found.');
				$errormsg .= '<br />';
				break;
		endswitch;
	endif;
	if(!empty($errormsg)):
		print_error_box($errormsg);
	endif;
?>
<table id="area_data"><tbody><tr><td id="area_data_frame">
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext('System Information'));
?>
		</thead>
		<tbody>
<?php
			if(!empty($config['vinterfaces']['carp'])):
				html_textinfo2('vipstatus',gettext('Virtual IP address'),$sysinfo['vipstatus']);
			endif;
			html_textinfo2('hostname',gettext('Hostname'),system_get_hostname());
			html_textinfo2('version',gettext('Version'),sprintf('<strong>%s %s</strong> (%s %s)',get_product_version(),get_product_versionname(),gettext('revision'),get_product_revision()));
			html_textinfo2('builddate',gettext('Compiled'),get_datetime_locale(get_product_buildtimestamp()));
			exec('/sbin/sysctl -n kern.version',$osversion);
			html_textinfo2('platform_os',gettext('Platform OS'),sprintf('%s', $osversion[0]));
			html_textinfo2('platform',gettext('Platform'),sprintf(gettext('%s on %s'),$g['fullplatform'],$sysinfo['cpumodel']));
			if(isset($smbios['planar']) && is_array($smbios['planar'])):
				html_textinfo2('system',gettext('System'),sprintf('%s %s',$smbios['planar']['maker'] ?? '',$smbios['planar']['product'] ?? ''));
			elseif(isset($smbios['system']) && is_array($smbios['system'])):
				html_textinfo2('system',gettext('System'),sprintf('%s %s',$smbios['system']['maker'] ?? '',$smbios['system']['product'] ?? ''));
			endif;
			if(isset($smbios['bios']) && is_array($smbios['bios'])):
				html_textinfo2('system_bios',gettext('System BIOS'),sprintf('%s %s %s %s',xmlspecialchars($smbios['bios']['vendor'] ?? ''),gettext('Version:'),xmlspecialchars($smbios['bios']['version'] ?? ''),xmlspecialchars($smbios['bios']['reldate'] ?? '')));
			endif;
			html_textinfo2('system_datetime',gettext('System Time'),get_datetime_locale());
			html_textinfo2('system_uptime',gettext('System Uptime'),$sysinfo['uptime']);
			if(Session::isAdmin()):
				if($config['lastchange']):
					html_textinfo2('last_config_change',gettext('System Config Change'),get_datetime_locale($config['lastchange']));
				endif;
				if(empty($sysinfo['cputemp2'])):
					if(!empty($sysinfo['cputemp'])):
						html_textinfo2('cputemp',gettext('CPU Temperature'),sprintf('%s°C',$sysinfo['cputemp']));
					endif;
				endif;
				if(!empty($sysinfo['cpufreq'])):
					html_textinfo2('cpufreq',gettext('CPU Frequency'),sprintf('%sMHz',$sysinfo['cpufreq']));
				endif;
				$percentage = $sysinfo['cpuusage'];
				if(is_numeric($percentage)):
?>
					<tr>
						<td class="celltag"><?=gtext('CPU Usage');?></td>
						<td class="celldata">
<?php
							if($use_meter_tag):
								echo '<meter id="cpuusagev" class="cpuusage" min="0" max="100" optimum="45" low="90" high="95" value="',$percentage,'" title="',$percentage,'%">';
							endif;
							echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
								'<img src="images/bar_blue.gif" id="cpuusageu" width="',$percentage,'" class="progbarcf" alt="" />',
								'<img src="images/bar_gray.gif" id="cpuusagef" width="',100 - $percentage,'" class="progbarc" alt=""/>',
								'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
							if($use_meter_tag):
								echo '</meter>';
							endif;
							echo '<span id="cpuusagep">',$percentage,'%</span>';
?>
						</td>
					</tr>
<?php
				endif;
 //				limit the number of CPU's shown to 16 cpus
				$cpus = min($sysinfo['cpus'],16);
				if($cpus > 1):
?>
					<tr>
						<td class="celltag"><?=gtext('CPU Core Usage');?></td>
						<td class="celldata">
							<table class="area_data_settings" style="table-layout:auto;white-space:nowrap;"><tbody>

<?php
								if($cpus > 4):
									$col_max = 2; // set max number of columns for high number of CPUs
								else:
									$col_max = 1; // set max number of columns for low number of CPUs
								endif;
								$tr_count = intdiv($cpus + $col_max - 1,$col_max);
								$cpu_index = 0;
								for($tr_counter = 0;$tr_counter < $tr_count;$tr_counter++):
									echo '<tr>';
									for($td_counter = 0;$td_counter < $col_max;$td_counter++):
										if($cpu_index < $cpus):
//											action
											$percentage = $sysinfo['cpuusage2'][$cpu_index] ?? 0;
											$temperature = $sysinfo['cputemp2'][$cpu_index] ?? 0;
											echo '<td class="nopad">';
											if($use_meter_tag):
												echo '<meter id="cpuusagev',$cpu_index,'" class="cpuusage" min="0" max="100" optimum="45" low="90" high="95" value="',$percentage,'" title="',$percentage,'%">';
											endif;
											echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
												'<img src="images/bar_blue.gif" id="cpuusageu',$cpu_index,'" width="',$percentage,'" class="progbarcf" alt=""/>',
												'<img src="images/bar_gray.gif" id="cpuusagef',$cpu_index,'" width="',100 - $percentage,'" class="progbarc" alt=""/>',
												'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
											if($use_meter_tag):
												echo '</meter>';
											endif;
											echo '</td>';
											echo '<td class="padr03">',htmlspecialchars(sprintf('%s %s:',gettext('Core'),$cpu_index)),'</td>';
											echo '<td class="padr1" style="text-align:right;" id="',sprintf('cpuusagep%s',$cpu_index),'">',$percentage,'%</td>';
											if(!empty($sysinfo['cputemp2'][$cpu_index])):
												echo '<td class="padr03">',htmlspecialchars(sprintf('%s:',gettext('Temp'))),'</td>';
												echo '<td class="nopad" style="text-align:right;" id="',sprintf('cputemp%s',$cpu_index),'">',$temperature,'</td>';
												echo '<td class="padr1">°C</td>';
											else:
												echo '<td class="padr03"></td>';
												echo '<td class="nopad"></td>';
												echo '<td class="padr1"></td>';
											endif;
											$cpu_index++;
										else:
//											fill empty space
											echo '<td class="nopad"></td>';
											echo '<td class="padr03"></td>';
											echo '<td class="padr1"></td>';
											echo '<td class="padr03"></td>';
											echo '<td class="nopad"></td>';
											echo '<td class="padr1"></td>';
										endif;
									endfor;
									echo '<td class="nopad100">';
									echo '</tr>';
								endfor;
?>
							</tbody></table>
						</td>
					</tr>
<?php
				endif;
?>
				<tr>
					<td class="celltag"><?=gtext('Memory Usage');?></td>
					<td class="celldata">
<?php
						$raminfo = $sysinfo['memusage'];
						if($use_meter_tag):
							echo '<meter id="memusagev" class="memusage" min="0" max="100" high="98" value="',$raminfo['usedpct'],'" title="',$raminfo['caption'],'">';
						endif;
						echo '<img src="images/bar_left.gif" class="progbarl" alt=""/>',
							'<img src="images/bar_blue.gif" id="memusageu" width="',$raminfo['usedpct'],'" class="progbarcf" alt=""/>',
							'<img src="images/bar_gray.gif" id="memusagef" width="',$raminfo['freepct'],'" class="progbarc" alt="" />',
							'<img src="images/bar_right.gif" class="progbarr meter" alt=""/>';
						if($use_meter_tag):
							echo '</meter>';
						endif;
						echo '<span id="memusagep">',$raminfo['caption'],'</span>';
?>
					</td>
				</tr>
<?php
				render_swap_usage($sysinfo['swapusage']);
?>
				<tr>
					<td class="celltag"><?=gtext('Load Averages');?></td>
					<td class="celldata">
						<table class="area_data_settings" style="table-layout:auto;white-space:nowrap;">
							<tbody>
								<tr>
									<td class="padr1"><span id="loadaverage"><?=$sysinfo['loadaverage'];?></span></td>
									<td class="nopad"><a href="status_process.php"><?=gtext('Show Process Information');?></a></td>
									<td class="nopad100"></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
<?php
				render_disk_usage($sysinfo['diskusage']);
				render_pool_usage($sysinfo['poolusage']);
				render_ups_info($sysinfo['upsinfo']);
				unset($vmlist);
				mwexec2("/usr/bin/find /dev/vmm -type c", $vmlist);
				unset($vmlist2);
				$vbox_user = "vboxusers";
				$vbox_if = get_ifname($config['interfaces']['lan']['if']);
				$vbox_ipaddr = get_ipaddr($vbox_if);
				if(isset($config['vbox']['enable'])):
					mwexec2("/usr/local/bin/sudo -u {$vbox_user} /usr/local/bin/VBoxManage list runningvms", $vmlist2);
				else:
					$vmlist2 = [];
				endif;
				unset($vmlist3);
				if($g['arch'] == "dom0"):
					$xen_if = get_ifname($config['interfaces']['lan']['if']);
					$xen_ipaddr = get_ipaddr($xen_if);
					$vmlist_json = shell_exec("/usr/local/sbin/xl list -l");
					$vmlist3 = json_decode($vmlist_json, true);
				else:
					$vmlist3 = [];
				endif;
				if(!empty($vmlist) || !empty($vmlist2) || !empty($vmlist3)):
?>
					<tr>
						<td class="celltag"><?=gtext("Virtual Machine");?></td>
						<td class="celldata">
							<table class="area_data_settings">
<?php
								$vmtype = "BHyVe";
								$index = 0;
								foreach($vmlist as $vmpath):
									$vm = basename($vmpath);
									unset($temp);
									exec("/usr/sbin/bhyvectl ".escapeshellarg("--vm=$vm")." --get-lowmem | sed -e 's/.*\\///'", $temp);
									$vram = $temp[0] / 1024 / 1024;
									echo "<tr><td><div id='vminfo_$index'>";
									echo htmlspecialchars("$vmtype: $vm ($vram MiB)");
									echo '</div></td></tr>';
									if(++$index < count($vmlist)):
										echo '<tr><td><hr size="1" /></td></tr>';
									endif;
								endforeach;
								$vmtype = "VBox";
								$index = 0;
								foreach($vmlist2 as $vmline):
									$vm = "";
									if(preg_match("/^\"(.+)\"\s*\{(\S+)\}$/", $vmline, $match)):
										$vm = $match[1];
										$uuid = $match[2];
									endif;
									if($vm == ""):
										continue;
									endif;
									$vminfo = get_vbox_vminfo($vbox_user, $uuid);
									$vram = $vminfo['memory']['value'];
									echo '<tr><td>';
									echo htmlspecialchars("$vmtype: $vm ($vram MiB)");
									if(isset($vminfo['vrde']) && $vminfo['vrde']['value'] == 'on'):
										echo ' VNC: ';
										$vncport = $vminfo['vrdeport']['value'];
										echo htmlspecialchars(sprintf('%s:%s',$vbox_ipaddr,$vncport));
									endif;
									echo '</td></tr>',PHP_EOL;
									if(++$index < count($vmlist2)):
										echo '<tr><td><hr size="1" /></td></tr>';
									endif;
								endforeach;
								$vmtype = "Xen";
								$index = 0;
								$vncport_unused = 5900;
								foreach($vmlist3 as $k => $v):
									$domid = $v['domid'];
									$type = $v['config']['c_info']['type'];
									$vm = $v['config']['c_info']['name'];
									$vram = (int)(($v['config']['b_info']['target_memkb'] + 1023 ) / 1024);
									$vcpus = 1;
									if($domid == 0):
										$vcpus = @exec("/sbin/sysctl -q -n hw.ncpu");
										$info = get_xen_info();
										$cpus = $info['nr_cpus']['value'];
										$th = $info['threads_per_core']['value'];
										if(empty($th)) {
											$th = 1;
										}
										$core =(int)($cpus / $th);
										$mem = $info['total_memory']['value'];
										$ver = $info['xen_version']['value'];
									elseif(!empty($v['config']['b_info']['max_vcpus'])):
										$vcpus = $v['config']['b_info']['max_vcpus'];
									endif;
									echo "<tr><td><div id='vminfo3_$index'>";
									echo htmlspecialchars("$vmtype $type: $vm ($vram MiB / $vcpus VCPUs)");
									if($domid == 0):
										echo " ";
										echo htmlspecialchars("Xen version {$ver} / {$mem} MiB / {$core} core".($th > 1 ? "/HT" : ""));
									elseif($type == 'pv' && isset($v['config']['vfbs']) && isset($v['config']['vfbs'][0]['vnc'])):
										$vnc = $v['config']['vfbs'][0]['vnc'];
										$vncport = "unknown";
										/*
										if(isset($vnc['display'])):
											$vncdisplay = $vnc['display'];
											$vncport = 5900 + $vncdisplay;
										elseif(isset($vnc['findunused'])):
											$vncport = $vncport_unused;
											$vncport_unused++;
										endif;
										*/
										$console = get_xen_console($domid);
										if(!empty($console) && isset($console['vnc-port'])):
											$vncport = $console['vnc-port']['value'];
										endif;
										echo " ";
										echo htmlspecialchars("vnc://{$xen_ipaddr}:{$vncport}/");
									elseif($type == 'hvm' && isset($v['config']['b_info']['type.hvm']['vnc']['enable'])):
										$vnc = $v['config']['b_info']['type.hvm']['vnc'];
										$vncport = "unknown";
										/*
										if(isset($vnc['display'])) {
											$vncdisplay = $vnc['display'];
											$vncport = 5900 + $vncdisplay;
										} else if(isset($vnc['findunused'])) {
											$vncport = $vncport_unused;
											$vncport_unused++;
										}
										*/
										$console = get_xen_console($domid);
										if(!empty($console) && isset($console['vnc-port'])):
											$vncport = $console['vnc-port']['value'];
										endif;
										echo " ";
										echo htmlspecialchars("VNC:/{$xen_ipaddr}:{$vncport}/");
									endif;
									echo '</div></td></tr>';
									if(++$index < count($vmlist3)):
										echo '<tr><td><hr size="1" /></td></tr>';
									endif;
								endforeach;
?>
							</table>
						</td>
					</tr>
<?php
				endif;
			endif;
?>
		</tbody>
	</table>
</td></tr></tbody></table>
<?php
include 'fend.inc';
