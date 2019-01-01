<?php
/*
	status_graph_disk_usage.php

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

array_make_branch($config,'rrdgraphs');
$rrd_disk_usage = true;
$temp_array = [];
$test_arrays = 0;
if(!empty($config["rrdgraphs"]["mounts"])):
	$test_arrays = $test_arrays + 1;
endif;
if(!empty($config["rrdgraphs"]["pools"])):
	$test_arrays = $test_arrays + 2;
endif;
switch($test_arrays):
	case 1:
		$temp_array = $config["rrdgraphs"]["mounts"];
		break;
	case 2:
		$temp_array = $config["rrdgraphs"]["pools"];
		break;
	case 3:
		$temp_array = array_merge($config["rrdgraphs"]["mounts"],$config["rrdgraphs"]["pools"]);
		break;
endswitch;
if($test_arrays > 0):
	asort($temp_array);
endif;
if(isset($_GET['selector']) && $_GET['selector']):
	$current_key = $_GET['selector'];
	$current_data = $temp_array[$current_key];
else:
	$current_data = $temp_array[0];
endif;
$refresh = 300;
if(isset($config['rrdgraphs']['refresh_time'])):
	if(!empty($config['rrdgraphs']['refresh_time'])):
		$refresh = $config['rrdgraphs']['refresh_time'];
	endif;
endif;
mwexec(sprintf('/usr/local/share/rrdgraphs/rrd-graph.sh disk_usage %s',$current_data),true);
$pgtitle = [gtext('Status'),gtext('Monitoring'),gtext('Disk Usage')];
include 'fbegin.inc';
?>
<meta http-equiv="refresh" content="<?=$refresh?>">
<?php
$document = new co_DOMDocument();
include 'status_graph_tabs.inc';
$document->render();
?>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form name="form2" action="status_graph_disk_usage.php" method="get">
	<table class="area_data_settings">
		<colgroup>
			<col style="width:100%">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext('Disk Usage'),1);
?>
		</thead>
		<tbody>
			<tr><td>
<?php
				echo sprintf(gtext('Graph updates every %d seconds.'),$refresh);
				echo '&nbsp;';
				echo gtext('Selected graph:');
				echo '&nbsp;&nbsp;&nbsp;';
?>
				<select name="selector" class="formfld" onchange="submit()">
<?php
					reset($temp_array);
					$current_key = key($temp_array);
					if(isset($_GET['selector']) && $_GET['selector']):
						$current_key = $_GET['selector'];
					endif;
					$current_data = $temp_array[$current_key];
					$clean_name = str_replace('/', '-', $current_data); // clean .rrd filename from '/' for ZFS datasets
					$selector_array = $temp_array;
					foreach ($selector_array as $selector_key => $selector_data):
						echo '<option value="',$selector_key,'"';
						if($selector_key == $current_key):
							echo ' selected="selected"';
						endif;
						echo '>',htmlspecialchars($selector_data),'</option>',PHP_EOL;
				endforeach;
?>
				</select>
		</td></tr>
		<tr><td>
			<div align="center" style="min-width:840px;">
				<br>
				<img src="/images/rrd/rrd-mnt_<?=$clean_name;?>_daily.png?rand=<?=time()?>" alt="RRDGraphs Daily Disk usage Graph |<?=$clean_name;?>|">
				<br><br>
				<img src="/images/rrd/rrd-mnt_<?=$clean_name;?>_weekly.png?rand=<?=time()?>" alt="RRDGraphs Weekly Disk usage Graph">
				<br><br>
				<img src="/images/rrd/rrd-mnt_<?=$clean_name;?>_monthly.png?rand=<?=time()?>" alt="RRDGraphs Monthly Disk usage Graph">
				<br><br>
				<img src="/images/rrd/rrd-mnt_<?=$clean_name;?>_yearly.png?rand=<?=time()?>" alt="RRDGraphs Yearly Disk usage Graph">
			</div>
			</td></tr>
		</tbody>
	</table>
</form></td></tr></tbody></table>
<?php
include 'fend.inc';
