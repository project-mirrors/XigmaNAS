<?php
/*
	status_graph_network.php

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
$rrd_lan = true;
$refresh = 300;
if(isset($config['rrdgraphs']['refresh_time'])):
	if(!empty($config['rrdgraphs']['refresh_time'])):
		$refresh = $config['rrdgraphs']['refresh_time'];
	endif;
endif;
mwexec('/usr/local/share/rrdgraphs/rrd-graph.sh traffic',true);
$pgtitle = [gtext('Status'),gtext('Monitoring'),gtext('Network Traffic')];
include 'fbegin.inc';
?>
<meta http-equiv="refresh" content="<?=$refresh?>">
<?php
$document = new co_DOMDocument();
include 'status_graph_tabs.inc';
$document->render();
?>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form name="form2" action="status_graph_network.php" method="get">
	<table class="area_data_settings">
		<colgroup>
			<col style="width:100%">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext('Network Traffic'),1);
?>
		</thead>
		<tbody>
			<tr><td>
<?php
				echo sprintf(gtext('Graph updates every %d seconds.'),$refresh);
				echo '&nbsp;';
				echo gtext('Selected interface:');
				echo '&nbsp;&nbsp;&nbsp;';
?>
				<select name="if" class="formfld" onchange="submit()">
<?php
					$curif = "lan";
					if(isset($_GET['if']) && $_GET['if']):
						$curif = $_GET['if'];
					endif;
					$ifnum = get_ifname($config['interfaces'][$curif]['if']);
					$ifdescrs = array('lan' => 'LAN');
					for($j = 1;isset($config['interfaces']['opt' . $j]);$j++):
						$ifdescrs['opt' . $j] = $config['interfaces']['opt' . $j]['descr'];
					endfor;
					foreach($ifdescrs as $ifn => $ifd):
						echo '<option value="',$ifn,'"';
						if($ifn == $curif):
							echo ' selected="selected"';
						endif;
						echo '>',htmlspecialchars($ifd),'</option>',PHP_EOL;
					endforeach;
?>
				</select>
			</td></tr>
			<tr><td>
			<div align="center" style="min-width:840px;">
				<br>
				<img src="/images/rrd/rrd-<?=$ifnum;?>_daily.png?rand=<?=time()?>" alt="RRDGraphs Daily Bandwidth <?=$ifnum;?> Graph">
				<br><br>
				<img src="/images/rrd/rrd-<?=$ifnum;?>_weekly.png?rand=<?=time()?>" alt="RRDGraphs Weekly Bandwidth Graph">
				<br><br>
				<img src="/images/rrd/rrd-<?=$ifnum;?>_monthly.png?rand=<?=time()?>" alt="RRDGraphs Monthly Bandwidth Graph">
				<br><br>
				<img src="/images/rrd/rrd-<?=$ifnum;?>_yearly.png?rand=<?=time()?>" alt="RRDGraphs Yearly Bandwidth Graph">
			</div>
			</td></tr>
		</tbody>
	</table>
</form></td></tr></tbody></table>
<?php
include 'fend.inc';
