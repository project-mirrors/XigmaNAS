<?php
/*
	status_graph_ups.php

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
$rrd_ups = true;
$refresh = 300;
if(isset($config['rrdgraphs']['refresh_time'])):
	if(!empty($config['rrdgraphs']['refresh_time'])):
		$refresh = $config['rrdgraphs']['refresh_time'];
	endif;
endif;
mwexec('/usr/local/share/rrdgraphs/rrd-graph.sh ups',true);
$pgtitle = [gtext('Status'),gtext('Monitoring'),gtext('UPS')];
include 'fbegin.inc';
?>
<meta http-equiv="refresh" content="<?=$refresh?>">
<?php
$document = new co_DOMDocument();
include 'status_graph_tabs.inc';
$document->render();
?>
<table id="area_data"><tbody><tr><td id="area_data_frame">
	<table class="area_data_settings">
		<colgroup>
			<col style="width:100%">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext('UPS'),1);
?>
		</thead>
		<tbody>
			<tr><td><?=sprintf(gtext('Graph updates every %d seconds.'),$refresh);?></td></tr>
			<tr><td>
				<div align="center" style="min-width:840px;">
					<br>
					<img src="/images/rrd/rrd-ups_daily.png?rand=<?=time()?>" alt="RRDGraphs Daily UPS Graph">
					<br><br>
					<img src="/images/rrd/rrd-ups_weekly.png?rand=<?=time()?>" alt="RRDGraphs Weekly UPS Graph">
					<br><br>
					<img src="/images/rrd/rrd-ups_monthly.png?rand=<?=time()?>" alt="RRDGraphs Monthly UPS Graph">
					<br><br>
					<img src="/images/rrd/rrd-ups_yearly.png?rand=<?=time()?>" alt="RRDGraphs Yearly UPS Graph">
				</div>
			</td></tr>
		</tbody>
	</table>
</td></tr></tbody></table>
<?php
include 'fend.inc';
