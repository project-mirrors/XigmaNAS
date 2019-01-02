<?php
/*
	disks_crypt_edit.php

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

$a_geli = &array_make_branch($config,'geli','vdisk');
if(empty($a_geli)):
else:
	array_sort_key($a_geli,'devicespecialfile');
endif;
// Get list of all configured disks (physical and virtual).
$a_alldisk = get_conf_all_disks_list_filtered();
// Check whether there are disks configured, othersie display a error message.
if(!count($a_alldisk)):
	$nodisks_error = gtext('You must add disks first.');
endif;
// Check if protocol is HTTPS, otherwise display a warning message.
if("http" === $config['system']['webgui']['protocol']):
	$nohttps_error = gtext('You should use HTTPS as WebGUI protocol for sending passphrase.');
endif;
if($_POST):
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;
	if(isset($_POST['Cancel']) && $_POST['Cancel']):
		header('Location: disks_crypt.php');
		exit;
	endif;
	// Input validation.
	$reqdfields = ['disk','ealgo','passphrase','passphraseconf'];
	$reqdfieldsn = [gtext('Disk'),gtext('Encryption algorithm'),gtext('Passphrase'),gtext('Passphrase Confirmation')];
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	// Check for duplicate disks.
	if(array_search_ex("{$_POST['disk']}.eli",$a_geli,'devicespecialfile')):
		$input_errors[] = gtext('This disk already exists in the disk list.');
	endif;
	// Check for a passphrase mismatch.
	if($_POST['passphrase'] !== $_POST['passphraseconf']):
		$input_errors[] = gtext("Passphrase don't match.");
	endif;
	if(0 != mwexec("/sbin/kldstat -q -m aesni")):
		mwexec("/sbin/kldload -q aesni.ko");
	endif;
	if(empty($input_errors)):
		$pconfig['do_action'] = true;
		$pconfig['init'] = isset($_POST['init']) ? true : false;
		$pconfig['name'] = $a_alldisk[$_POST['disk']]['name']; // e.g. da2
		$pconfig['devicespecialfile'] = $a_alldisk[$_POST['disk']]['devicespecialfile']; // e.g. /dev/da2
		$pconfig['aalgo'] = "none";
		// Check whether disk is mounted.
		if(disks_ismounted_ex($pconfig['devicespecialfile'], "devicespecialfile")):
			$helpinghand = sprintf('disks_mount_tools.php?disk=%1$s&action=umount', $pconfig['devicespecialfile']);
			$link = sprintf('<a href="%1$s">%2$s</a>', $helpinghand, gtext('Unmount this disk first before proceeding.'));
			$errormsg = gtext('The disk is currently mounted!') . ' ' . $link;
			$pconfig['do_action'] = false;
		endif;
		if($pconfig['do_action']):
			// Set new file system type attribute ('fstype') in configuration.
			set_conf_disk_fstype($pconfig['devicespecialfile'], "geli");
			// Get disk information.
			$diskinfo = disks_get_diskinfo($pconfig['devicespecialfile']);
			$geli = [];
			$geli['uuid'] = uuid();
			$geli['name'] = $pconfig['name'];
			$geli['device'] = $pconfig['devicespecialfile'];
			$geli['devicespecialfile'] = "{$geli['device']}.eli";
			$geli['desc'] = "Encrypted disk";
			$geli['size'] = format_bytes($diskinfo['mediasize_bytes'],2,true,is_sidisksizevalues());
			$geli['aalgo'] = $pconfig['aalgo'];
			$geli['ealgo'] = $pconfig['ealgo'];
			$a_geli[] = $geli;
			write_config();
		endif;
	endif;
endif;
if(!isset($pconfig['do_action'])):
	// Default values.
	$pconfig['do_action'] = false;
	$pconfig['init'] = false;
	$pconfig['disk'] = 0;
	$pconfig['aalgo'] = "";
	$pconfig['ealgo'] = "AES";
	$pconfig['keylen'] = "";
	$pconfig['passphrase'] = "";
	$pconfig['name'] = "";
	$pconfig['devicespecialfile'] = "";
endif;
$pgtitle = [gtext('Disks'),gtext('Encryption'),gtext('Add')];
include 'fbegin.inc';
?>
<script type="text/javascript">
<!--
function ealgo_change() {
	// Disable illegal values in 'Key length' selective list.
	for (i = 0; i < document.iform.keylen.length; i++) {
		var disabled = false;
		switch (document.iform.ealgo.value) {
		case "3DES":
			disabled = (document.iform.keylen.options[i].value >= 256);
			break;
		case "AES":
		case "AES-CBC":
		case "Camellia":
			disabled = (document.iform.keylen.options[i].value > 256);
			break;
		}
		document.iform.keylen.options[i].disabled = disabled;
	}

	// Set key length to 'default' whether an illegal value is selected.
	var selected = document.iform.keylen.selectedIndex;
	if (document.iform.keylen.options[selected].disabled == true)
		document.iform.keylen.selectedIndex = 0;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="disks_crypt.php" title="<?=gtext('Reload page');?>" ><span><?=gtext('Management');?></span></a></li>
		<li class="tabinact"><a href="disks_crypt_tools.php"><span><?=gtext('Tools');?></span></a></li>
	</ul></td></tr>
	<tr>
		<td class="tabcont">
			<form action="disks_crypt_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<?php
				if(!empty($nohttps_error)):
					print_warning_box($nohttps_error);
				endif;
				if(!empty($nodisks_error)):
					print_error_box($nodisks_error);
				endif;
				if(!empty($errormsg)):
					print_error_box($errormsg);
				endif;
				if(!empty($input_errors)):
					print_input_errors($input_errors);
				endif;
?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
<?php
					html_titleline(gtext('Encryption Settings'));
?>
					<tr>
						<td valign="top" class="vncellreq"><?=gtext('Disk');?></td>
						<td class="vtable">
							<select name="disk" class="formfld" id="disk">
								<option value=""><?=gtext("Must choose one");?></option>
<?php
								$i = -1;
								$use_si = is_sidisksizevalues();
								foreach ($a_alldisk as $diskv):
									++$i;
									if(0 == strcmp($diskv['class'], "geli")):
										continue;
									endif;
									if(0 == strcmp($diskv['size'], "NA")):
										continue;
									endif;
									if(1 == disks_exists($diskv['devicespecialfile'])):
										continue;
									endif;
?>
									<option value="<?=$i;?>" <?php if ($pconfig['disk'] == $i) echo "selected=\"selected\"";?>>
<?php
										$diskinfo = disks_get_diskinfo($diskv['devicespecialfile']);
										$helpinghand = format_bytes($diskinfo['mediasize_bytes'],2,true,$use_si);
										echo htmlspecialchars(sprintf('%s: %s (%s)',$diskv['name'],$helpinghand,$diskv['desc']));
?>
									</option>
<?php
								endforeach;
?>
							</select>
						</td>
					</tr>
<?php
					/* Remove Data Intergrity Algorithhm : there is a bug when enabled
					<tr>
						<td valign="top" class="vncellreq"><?=gtext("Data Integrity Algorithm");?></td>
						<td class="vtable">
							<select name="aalgo" class="formfld" id="aalgo">
								<option value="none" <?php if ($pconfig['aalgo'] === "none") echo "selected=\"selected\""; ?>>none</option>
								<option value="HMAC/MD5" <?php if ($pconfig['aalgo'] === "HMAC/MD5") echo "selected=\"selected\""; ?>>HMAC/MD5</option>
								<option value="HMAC/SHA1" <?php if ($pconfig['aalgo'] === "HMAC/SHA1") echo "selected=\"selected\""; ?>>HMAC/SHA1</option>
								<option value="HMAC/RIPEMD160" <?php if ($pconfig['aalgo'] === "HMAC/RIPEMD160") echo "selected=\"selected\""; ?>>HMAC/RIPEMD160</option>
								<option value="HMAC/SHA256" <?php if ($pconfig['aalgo'] === "HMAC/SHA256") echo "selected=\"selected\""; ?>>HMAC/SHA256</option>
								<option value="HMAC/SHA384" <?php if ($pconfig['aalgo'] === "HMAC/SHA384") echo "selected=\"selected\""; ?>>HMAC/SHA384</option>
								<option value="HMAC/SHA512" <?php if ($pconfig['aalgo'] === "HMAC/SHA512") echo "selected=\"selected\""; ?>>HMAC/SHA512</option>
							</select>
						</td>
					</tr>
					*/
?>
<?php
					$options = [
						'AES' => 'AES-XTS',
						'AES-CBC' => 'AES-CBC',
						'Blowfish' => 'Blowfish',
						'Camellia' => 'Camellia',
						'3DES' => '3DES'
					];
					html_combobox('ealgo',gtext('Algorithm'),$pconfig['ealgo'],$options,gtext('Select an encryption algorithm to use.'),true,false,'ealgo_change()');
					$options = [
						'' => gtext('Default'),
						128 => '128',
						192 => '192',
						256 => '256',
						448 => '448'
					];
					html_combobox('keylen',gtext('Key Length'),$pconfig['keylen'],$options,gtext('Select which key length to use for given cryptographic algorithm.') . ' ' . gtext('(Default lengths are: 128 for AES, 128 for Blowfish, 128 for Camellia and 192 for 3DES).'),false);
					html_passwordconfbox('passphrase','passphraseconf',gtext('Passphrase'),'','','',true);
					html_checkbox('init',gtext('Initialize'),$pconfig['init'] ? true : false,gtext('Initialize and encrypt disk.'),gtext('This will erase ALL data on your disk! Do not use this option if you want to add an existing encrypted disk.'));
?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext('Add');?>"/>
					<input name="Cancel" type="submit" class="formbtn" value="<?=gtext('Cancel');?>"/>
				</div>
<?php
				if($pconfig['do_action']):
					echo(sprintf("<div id='cmdoutput'>%s</div>",gtext('Command output:')));
					echo('<pre class="cmdoutput">');
					//ob_end_flush();
					if(isset($pconfig['init']) && true === $pconfig['init']):
						// Initialize and encrypt the disk.
						echo sprintf(gtext("Encrypting '%s'... Please wait") . "!<br />", $pconfig['devicespecialfile']);
						disks_geli_init($pconfig['devicespecialfile'], $pconfig['aalgo'], $pconfig['ealgo'], $pconfig['keylen'], $pconfig['passphrase'], true);
					endif;
					// Attach the disk.
					echo(sprintf(gtext("Attaching provider '%s'."), $pconfig['devicespecialfile']) . "<br />");
					disks_geli_attach($pconfig['devicespecialfile'], $pconfig['passphrase'], true);
					echo('</pre>');
				endif;
				include 'formend.inc';
?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!--
ealgo_change();
//-->
</script>
<?php
include 'fend.inc';
?>
