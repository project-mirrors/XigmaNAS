<?php
/*
	access_ad.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2021 XigmaNAS® <info@xigmanas.com>.
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
require_once 'autoload.php';

use common\arr;

arr::make_branch($config,'ad');
arr::make_branch($config,'samba');

$pconfig['enable'] = isset($config['ad']['enable']);
$pconfig['domaincontrollername'] = $config['ad']['domaincontrollername'];
$pconfig['domainname_dns'] = $config['ad']['domainname_dns'];
$pconfig['domainname_netbios'] = $config['ad']['domainname_netbios'];
$pconfig['username'] = $config['ad']['username'];
$pconfig['password'] = $config['ad']['password'];
$pconfig['password2'] = $config['ad']['password'];
if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
//	Input validation.
	if(isset($_POST['enable']) && $_POST['enable']):
		$reqdfields = ['domaincontrollername','domainname_dns','domainname_netbios','username','password'];
		$reqdfieldsn = [gtext('Domain Controller Name'),gtext('Domain Name (DNS/Realm-Name)'),gtext('Domain Name (NetBIOS-Name)'),gtext('Administrator Name'),gtext('Administration Password')];
		$reqdfieldst = ['string','domain','netbios','string','string'];
		do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
		do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
		if(($_POST['password'] !== $_POST['password2'])):
			$input_errors[] = gtext('The confirmed password does not match. Please ensure the passwords match exactly.');
		endif;
	endif;
	if(empty($input_errors)):
		$config['ad']['domaincontrollername'] = $_POST['domaincontrollername'];
		$config['ad']['domainname_dns'] = $_POST['domainname_dns'];
		$config['ad']['domainname_netbios'] = $_POST['domainname_netbios'];
		$config['ad']['username'] = $_POST['username'];
		$config['ad']['password'] = $_POST['password'];
		$config['ad']['enable'] = isset($_POST['enable']) ? true : false;
		if($config['ad']['enable']):
			$config['samba']['enable'] = true;
			$config['samba']['security'] = 'ads';
			$config['samba']['workgroup'] = $_POST['domainname_netbios'];
		endif;
		write_config();
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)):
			config_lock();
			rc_exec_service('pam');
			rc_exec_service('ldap');
			rc_start_service('nsswitch');
			rc_update_service('samba');
			config_unlock();
		endif;
		$savemsg = get_std_save_message($retval);
	endif;
endif;
$pgtitle = [gtext('Access'),gtext('Active Directory')];
include 'fbegin.inc';
?>
<script type="text/javascript">
//<![CDATA[
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.domaincontrollername.disabled = endis;
	document.iform.domainname_dns.disabled = endis;
	document.iform.domainname_netbios.disabled = endis;
	document.iform.username.disabled = endis;
	document.iform.password.disabled = endis;
	document.iform.password2.disabled = endis;
}
//]]>
</script>
<form action="access_ad.php" method="post" name="iform" id="iform" onsubmit="spinner()">
	<table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
		if(!empty($input_errors)):
			print_input_errors($input_errors);
		endif;
		if($savemsg):
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
				html_titleline_checkbox2('enable',gettext('Active Directory'),!empty($pconfig['enable']) ? true : false,gettext('Enable'),'enable_change(false)');
?>
			</thead>
			<tbody>
<?php
				html_inputbox2('domaincontrollername',gettext('Domain Controller Name'),htmlspecialchars($pconfig['domaincontrollername']),gettext('AD or PDC name.'),true,60,false,false,256,gettext('Controller Name'));
				html_inputbox2('domainname_dns',gettext('Domain Name (DNS/Realm-Name)'),htmlspecialchars($pconfig['domainname_dns']),gettext('Domain name, e.g. example.com.'),true,60,false,false,256,gettext('DNS Name'));
				html_inputbox2('domainname_netbios',gettext('Domain Name (NetBIOS-Name)'),htmlspecialchars($pconfig['domainname_netbios']),gettext('Domain name in old format, e.g. EXAMPLE.'),true,60,false,false,256,gettext('NETBIOS Name'));
				html_inputbox2('username',gettext('Administrator Name'),htmlspecialchars($pconfig['username']),gettext('Username of a domain administrator account.'),true,60,false,false,256,gettext('Admin Name'));
				html_passwordconfbox2('password','password2',gettext('Administration Password'),$pconfig['password'],$pconfig['password2'],gettext('Password of the administrator account.'),true,60,false,gettext('Enter Password'),gettext('Confirm Password'));
?>
			</tbody>
		</table>
		<div id="submit">
			<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save");?>" onclick="enable_change(true)" />
		</div>
		<div id="remarks">
<?php
			$helpinghand  = gtext('To use Active Directory the SMB service will be enabled as well.');
			$helpinghand .= ' ';
			$helpinghand .= gtext('The following services will use AD authentication:');
			$helpinghand .= "<div id='enumeration'><ul><li>SMB</li><li>SSH</li><li>FTP</li><li>AFP</li><li>System</li></ul></div>";
			html_remark2('note',gettext('Note'),$helpinghand);
?>
		</div>
	</td></tr></tbody></table>
<?php
	include 'formend.inc';
?>
</form>
<script type="text/javascript">
//<![CDATA[
enable_change(false);
//]]>
</script>
<?php
include 'fend.inc';
