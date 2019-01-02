<?php
/*
	status_interfaces.php

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

$show_separator = false;
$ifdescrs = ['lan' => 'LAN'];
for($j = 1;isset($config['interfaces']['opt' . $j]);$j++):
	$ifdescrs['opt' . $j] = $config['interfaces']['opt' . $j]['descr'];
endfor;
$pgtitle = [gtext('Status'),gtext('Interfaces')];
include 'fbegin.inc';
?>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform">
<?php
	foreach($ifdescrs as $ifdescr => $ifname):
		$sphere_record = get_interface_info_ex($ifdescr);
?>
		<table class="area_data_settings">
			<colgroup>
				<col class="area_data_settings_col_tag">
				<col class="area_data_settings_col_data">
			</colgroup>
			<thead>
<?php
				if($show_separator):
					html_separator2();
				else:
					$show_separator = true;
				endif;
				html_titleline2(sprintf(gettext('%s Interface'),$ifname));
?>
			</thead>
			<tbody>
<?php
				$show_ip_details = true;
				html_text2('interface',gettext('Interface'),$sphere_record['hwif']);
				if(isset($sphere_record['dhcplink']) && $sphere_record['dhcplink']):
					html_text2('dhcplink',gettext('DHCP'),$sphere_record['dhcplink']);
					if('down' == $sphere_record['dhcplink']):
						$show_ip_details = false;
					endif;
				endif;
				if(isset($sphere_record['pppoelink']) && $sphere_record['pppoelink']):
					html_text2('pppoelink',gettext('PPPoE'),$sphere_record['pppoelink']);
					if('down' == $sphere_record['pppoelink']):
						$show_ip_details = false;
					endif;
				endif;
				if(isset($sphere_record['pptplink']) && $sphere_record['pptplink']):
					html_text2('pptplink',gettext('PPTP'),$sphere_record['pptplink']);
					if('down' == $sphere_record['pptplink']):
						$show_ip_details = false;
					endif;
				endif;
				if(isset($sphere_record['macaddr']) && $sphere_record['macaddr']):
					html_text2('macaddr',gettext('MAC Address'),$sphere_record['macaddr']);
				endif;
				if('down' != $sphere_record['status']):
					if($show_ip_details):
						if(isset($sphere_record['ipaddr']) && $sphere_record['ipaddr']):
							html_text2('ipaddr',gettext('IP Address'),$sphere_record['ipaddr']);
						endif;
						if(isset($sphere_record['subnet']) && $sphere_record['subnet']):
							html_text2('subnet',gettext('Subnet Mask'),$sphere_record['subnet']);
						endif;
						if(isset($sphere_record['gateway']) && $sphere_record['gateway']):
							html_text2('gateway',gettext('Gateway'),$sphere_record['gateway']);
						endif;
						if(isset($sphere_record['ipv6addr']) && $sphere_record['ipv6addr']):
							html_text2('ipv6addr',gettext('IPv6 Address'),$sphere_record['ipv6addr']);
						endif;
						if(isset($sphere_record['ipv6subnet']) && $sphere_record['ipv6subnet']):
							html_text2('ipv6subnet',gettext('IPv6 Prefix'),$sphere_record['ipv6subnet']);
						endif;
						if(isset($sphere_record['ipv6gateway']) && $sphere_record['ipv6gateway']):
							html_text2('ipv6gateway',gettext('IPv6 Gateway'),$sphere_record['ipv6gateway']);
						endif;
						if('wan' == $ifdescr && file_exists("{$g['varetc_path']}/nameservers.conf")):
							$filename = sprintf('%s/nameservers.conf',$g['varetc_path']);
							$helpinghand = '<pre>' . htmlspecialchars(file_get_contents($filename)) .'</pre>';
							html_text2('ispdnsservers',gettext('ISP DNS Servers').$helpinghand);
						endif;
					endif;
					if(isset($sphere_record['media']) && $sphere_record['media']):
						html_text2('media',gettext('Media'),$sphere_record['media']);
					endif;
					if(isset($sphere_record['channel']) && $sphere_record['channel']):
						html_text2('channel',gettext('Channel'),$sphere_record['channel']);
					endif;
					if(isset($sphere_record['ssid']) && $sphere_record['ssid']):
						html_text2('ssid',gettext('SSID'),$sphere_record['ssid']);
					endif;
					html_text2('mtu',gettext('MTU'),$sphere_record['mtu']);
					$helpinghand = $sphere_record['inpkts'] .
							'/' . 
							$sphere_record['outpkts'] . 
							' (' . 
							format_bytes($sphere_record['inbytes']) . 
							'/' . 
							format_bytes($sphere_record['outbytes']) . 
							')';
					html_text2('inpkts',gettext('In/Out Packets'),$helpinghand);
					if(isset($sphere_record['inerrs'])):
						$helpinghand = $sphere_record['inerrs'] . '/' . $sphere_record['outerrs'];
						html_text2('inerrs',gettext('In/Out Errors'),$helpinghand);
					endif;
					if(isset($sphere_record['collisions'])):
						html_text2('collisions',gettext('Collisions'),$sphere_record['collisions']);
					endif;
				endif;
				html_text2('status',gettext('Status'),$sphere_record['status']);
?>
			</tbody>
		</table>
<?php
	endforeach;
	include 'formend.inc';
?>
</form></td></tr></tbody></table>
<?php
include 'fend.inc';
