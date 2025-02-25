<?php
/*
	acces_ldap.php

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

array_make_branch($config,'ldap');
array_make_branch($config,'samba');

//LDAP take priority over MS ActiveDirectory (XigmaNAS® choicee), then disable AD:
array_make_branch($config,'ad');

$pconfig['enable'] = isset($config['ldap']['enable']);
$pconfig['hostname'] = $config['ldap']['hostname'];
$pconfig['base'] = $config['ldap']['base'];
$pconfig['anonymousbind'] = isset($config['ldap']['anonymousbind']);
$pconfig['binddn'] = $config['ldap']['binddn'];
$pconfig['bindpw'] = $config['ldap']['bindpw'];
$pconfig['bindpw2'] = $config['ldap']['bindpw'];
$pconfig['rootbinddn'] = $config['ldap']['rootbinddn'];
$pconfig['rootbindpw'] = $config['ldap']['rootbindpw'];
$pconfig['rootbindpw2'] = $config['ldap']['rootbindpw'];
$pconfig['user_suffix'] = $config['ldap']['user_suffix'];
$pconfig['password_suffix'] = $config['ldap']['password_suffix'];
$pconfig['group_suffix'] = $config['ldap']['group_suffix'];
$pconfig['machine_suffix'] = $config['ldap']['machine_suffix'];
$pconfig['pam_password'] = $config['ldap']['pam_password'];
if (isset($config['ldap']['auxparam']) && is_array($config['ldap']['auxparam']))
	$pconfig['auxparam'] = implode("\n", $config['ldap']['auxparam']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if (isset($_POST['enable']) && $_POST['enable']) {
		$reqdfields = ['hostname','base','rootbinddn','rootbindpw','user_suffix','group_suffix','password_suffix','machine_suffix'];
		$reqdfieldsn = [gtext('URI'),gtext('Base DN'),gtext('Root Bind DN'),gtext('Root Bind Password'),gtext('User Suffix'),gtext('Group Suffix'),gtext('Password Suffix'),gtext('Machine Suffix')];
		$reqdfieldst = ['string','string','string','password','string','string','string','string'];
		if (empty($_POST['anonymousbind'])) {
			$reqdfields = array_merge($reqdfields, ['binddn','bindpw']);
			$reqdfieldsn = array_merge($reqdfieldsn,[gtext('Bind DN'),gtext('Bind Password')]);
			$reqdfieldst = array_merge($reqdfieldst, ['string','password']);
		}

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);
	}

	if (($_POST['bindpw'] !== $_POST['bindpw2'])) {
		$input_errors[] = gtext("Password does not match the confirmation password. Please ensure both passwords are the same.");
	}

	if (empty($input_errors)) {
		$config['ldap']['enable'] = isset($_POST['enable']) ? true : false;
		$config['ldap']['hostname'] = $_POST['hostname'];
		$config['ldap']['base'] = $_POST['base'];
		$config['ldap']['anonymousbind'] = isset($_POST['anonymousbind']) ? true : false;
		$config['ldap']['binddn'] = $_POST['binddn'];
		$config['ldap']['bindpw'] = $_POST['bindpw'];
		$config['ldap']['rootbinddn'] = $_POST['rootbinddn'];
		$config['ldap']['rootbindpw'] = $_POST['rootbindpw'];
		$config['ldap']['user_suffix'] = $_POST['user_suffix'];
		$config['ldap']['password_suffix'] = $_POST['password_suffix'];
		$config['ldap']['group_suffix'] = $_POST['group_suffix'];
		$config['ldap']['machine_suffix'] = $_POST['machine_suffix'];
		$config['ldap']['pam_password'] = $_POST['pam_password'];

		# Write additional parameters.
		unset($config['ldap']['auxparam']);
		foreach (explode("\n", $_POST['auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$config['ldap']['auxparam'][] = $auxparam;
		}

		// Disable AD
		if ($config['ldap']['enable']) {
			$config['samba']['security'] = "user";
			$config['ad']['enable'] = false;
		}

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			rc_exec_service("pam");
			rc_exec_service("ldap");
			rc_start_service("nsswitch");
			rc_update_service("samba");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
$pgtitle = [gtext('Access'),gtext('LDAP')];

?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.hostname.disabled = endis;
	document.iform.base.disabled = endis;
	document.iform.anonymousbind.disabled = endis;
	document.iform.binddn.disabled = endis;
	document.iform.bindpw.disabled = endis;
	document.iform.bindpw2.disabled = endis;
	document.iform.rootbinddn.disabled = endis;
	document.iform.rootbindpw.disabled = endis;
	document.iform.rootbindpw2.disabled = endis;
	document.iform.user_suffix.disabled = endis;
	document.iform.password_suffix.disabled = endis;
	document.iform.group_suffix.disabled = endis;
	document.iform.machine_suffix.disabled = endis;
	document.iform.pam_password.disabled = endis;
	document.iform.auxparam.disabled = endis;
}

function anonymousbind_change() {
	switch (document.iform.anonymousbind.checked) {
		case false:
			showElementById('binddn_tr','show');
			showElementById('bindpw_tr','show');
			break;

		case true:
			showElementById('binddn_tr','hide');
			showElementById('bindpw_tr','hide');
			break;
	}
}
//-->
</script>
<form action="access_ldap.php" method="post" name="iform" id="iform" onsubmit="spinner()">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tabcont">
				<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
				<?php if (!empty($savemsg)) print_info_box($savemsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php
					html_titleline_checkbox("enable", sprintf("%s (%s)",gtext("Lightweight Directory Access Protocol"),gtext("Client")), !empty($pconfig['enable']) ? true : false, gtext("Enable"), "enable_change(false)");
					html_inputbox("hostname", gtext("URI"), $pconfig['hostname'], gtext("The space-separated list of URIs for the LDAP server."), true, 60);
					html_inputbox("base", gtext("Base DN"), $pconfig['base'], sprintf(gtext("The default base distinguished name (DN) to use for searches, e.g. %s"), "dc=test,dc=org"), true, 40);
					html_checkbox("anonymousbind", gtext("Anonymous Bind"), !empty($pconfig['anonymousbind']) ? true : false, gtext("Enable anonymous bind."), "", true, "anonymousbind_change()");
					html_inputbox("binddn", gtext("Bind DN"), $pconfig['binddn'], sprintf(gtext("The distinguished name to bind to the directory server, e.g. %s"), "cn=admin,dc=test,dc=org"), true, 40);
					html_passwordconfbox("bindpw", "bindpw2", gtext("Bind Password"), $pconfig['bindpw'], $pconfig['bindpw2'], gtext("The cleartext credentials with which to bind."), true);
					html_inputbox("rootbinddn", gtext("Root Bind DN"), $pconfig['rootbinddn'], sprintf(gtext("The distinguished name with which to bind to the directory server, e.g. %s"), "cn=admin,dc=test,dc=org"), true, 40);
					html_passwordconfbox("rootbindpw", "rootbindpw2", gtext("Root Bind Password"), $pconfig['rootbindpw'], $pconfig['rootbindpw2'], gtext("The credentials with which to bind."), true);
					html_combobox("pam_password", gtext("Password Encryption"), $pconfig['pam_password'], ['clear' => 'clear','crypt' => 'crypt','md5' => 'md5','nds' => 'nds','racf' => 'racf','ad' => 'ad','exop' => 'exop'], gtext("The password encryption protocol to use."), true);
					html_inputbox("user_suffix", gtext("User Suffix"), $pconfig['user_suffix'], sprintf(gtext("This parameter specifies the suffix that is used for users when these are added to the LDAP directory, e.g. %s"), "ou=Users"), true, 20);
					html_inputbox("group_suffix", gtext("Group Suffix"), $pconfig['group_suffix'], sprintf(gtext("This parameter specifies the suffix that is used for groups when these are added to the LDAP directory, e.g. %s"), "ou=Groups"), true, 20);
					html_inputbox("password_suffix", gtext("Password Suffix"), $pconfig['password_suffix'], sprintf(gtext("This parameter specifies the suffix that is used for passwords when these are added to the LDAP directory, e.g. %s"), "ou=Users"), true, 20);
					html_inputbox("machine_suffix", gtext("Machine Suffix"), $pconfig['machine_suffix'], sprintf(gtext("This parameter specifies the suffix that is used for machines when these are added to the LDAP directory, e.g. %s"), "ou=Computers"), true, 20);
					html_textarea("auxparam", gtext("Additional Parameters"), $pconfig['auxparam'], sprintf(gtext("These parameters are added to %s."), "ldap.conf"), false, 65, 5, false, false);
					?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Save");?>" onclick="enable_change(true)" />
				</div>
			</td>
		</tr>
	</table>
	<?php include 'formend.inc';?>
</form>
<script type="text/javascript">
<!--
anonymousbind_change();
enable_change(false);
//-->
</script>
<?php include 'fend.inc';?>
