<?php
/*
	services_hast_info.php

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

function services_hast_info_ajax() {
	global $config;
	if(!isset($config['hast']['enable'])):
		return gtext('HAST is disabled.');
	endif;
	$cmd = '/sbin/hastctl status';
	$cmd .= " 2>&1";
	mwexec2($cmd,$rawdata);
	return implode(PHP_EOL,$rawdata);
}
array_make_branch($config,'hast');
if(is_ajax()):
	$status = services_hast_info_ajax();
	render_ajax($status);
endif;
$pgtitle = [gtext('Services'),gtext('HAST'),gtext('Information')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var gui = new GUI;
	gui.recall(5000, 5000, 'services_hast_info.php', null, function(data) {
		if ($('#area_refresh').length > 0) {
			$('#area_refresh').text(data.data);
		}
	});
});
//]]>
</script>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="services_hast.php"><span><?=gtext('Settings');?></span></a></li>
		<li class="tabinact"><a href="services_hast_resource.php"><span><?=gtext('Resources');?></span></a></li>
		<li class="tabact"><a href="services_hast_info.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Information');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame">
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php 
			html_titleline2(gettext('HAST Information & Status Configured Resources'));
?>
		</thead>
		<tbody><tr>
			<td class="celltag"><?=gtext('Information');?></td>
			<td class="celldata">
				<pre><span id="area_refresh"><?=services_hast_info_ajax();?></span></pre>
			</td>
		</tr></tbody>
	</table>
</td></tr></tbody></table>
<?php
include 'fend.inc';
