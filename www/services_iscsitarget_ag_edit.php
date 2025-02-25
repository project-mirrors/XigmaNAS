<?php
/*
	services_iscsitarget_ag_edit.php

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

if (isset($_GET['uuid']))
	$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = [gtext('Services'), gtext('iSCSI Target'), gtext('Auth Group'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$MAX_AUTHUSERS = 4;
$GROW_AUTHUSERS = 4;

$a_iscsitarget_ag = &array_make_branch($config,'iscsitarget','authgroup');
if(empty($a_iscsitarget_ag)):
else:
	array_sort_key($a_iscsitarget_ag,'tag');
endif;

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_iscsitarget_ag, "uuid")))) {
	$pconfig['uuid'] = $a_iscsitarget_ag[$cnid]['uuid'];
	$pconfig['tag'] = $a_iscsitarget_ag[$cnid]['tag'];
	$pconfig['comment'] = $a_iscsitarget_ag[$cnid]['comment'];
	$i = 1;
	if (!isset($a_iscsitarget_ag[$cnid]['agauth']) || !is_array($a_iscsitarget_ag[$cnid]['agauth']))
		$a_iscsitarget_ag[$cnid]['agauth'] = [];
	array_sort_key($a_iscsitarget_ag[$cnid]['agauth'], "authuser");
	foreach ($a_iscsitarget_ag[$cnid]['agauth'] as $agauth) {
		$pconfig["user$i"] = $agauth['authuser'];
		$pconfig["secret$i"] = $agauth['authsecret'];
		$pconfig["secret2$i"] = $pconfig["secret$i"];
		$pconfig["muser$i"] = $agauth['authmuser'];
		$pconfig["msecret$i"] = $agauth['authmsecret'];
		$pconfig["msecret2$i"] = $pconfig["msecret$i"];
		$i++;
	}
	while ($i > $MAX_AUTHUSERS) {
		$MAX_AUTHUSERS += $GROW_AUTHUSERS;
	}
} else {
	// Find next unused tag.
	$tag = 1;
	$a_tags = [];
	foreach($a_iscsitarget_ag as $ag)
		$a_tags[] = $ag['tag'];

	while (true === in_array($tag, $a_tags))
		$tag += 1;

	$pconfig['uuid'] = uuid();
	$pconfig['tag'] = $tag;
	$pconfig['comment'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: services_iscsitarget_ag.php");
		exit;
	}

	// Input validation.
	$reqdfields = ['tag'];
	$reqdfieldsn = [gtext('Tag number')];
	$reqdfieldst = ['numericint'];

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if ($pconfig['tag'] < 1 || $pconfig['tag'] > 65535) {
		$input_errors[] = gtext("The tag range is invalid.");
	}
	if (!(isset($uuid) && (FALSE !== $cnid))) {
		$index = array_search_ex($pconfig['tag'], $config['iscsitarget']['authgroup'], "tag");
		if ($index !== FALSE) {
			$input_errors[] = gtext("This tag already exists.");
		}
	}

	$auths = [];
	for ($i = 1; $i <= $MAX_AUTHUSERS; $i++) {
		$delete = isset($_POST["delete$i"]) ? true : false;
		$user = $_POST["user$i"];
		$secret = $_POST["secret$i"];
		$secret2 = $_POST["secret2$i"];
		$muser = $_POST["muser$i"];
		$msecret = $_POST["msecret$i"];
		$msecret2 = $_POST["msecret2$i"];
		if (strlen($user) != 0
			|| strlen($secret) != 0 || strlen($secret2) != 0) {
			if (strlen($user) == 0) {
				$input_errors[] = sprintf("%s%d: %s", gtext("User"), $i, sprintf(gtext("The attribute '%s' is required."), gtext("User")));
			}
			if (strcmp($secret, $secret2) != 0) {
				$input_errors[] = sprintf("%s%d: %s", gtext("User"), $i, gtext("Password don't match."));
			}
		}
		if (strlen($muser) != 0
			|| strlen($msecret) != 0 || strlen($msecret2) != 0) {
			if (strlen($user) == 0) {
				$input_errors[] = sprintf("%s%d: %s", gtext("User"), $i, sprintf(gtext("The attribute '%s' is required."), gtext("User")));
			}
			if (strlen($muser) == 0) {
				$input_errors[] = sprintf("%s%d: %s", gtext("User"), $i, sprintf(gtext("The attribute '%s' is required."), gtext("Peer User")));
			}
			if (strcmp($msecret, $msecret2) != 0) {
				$input_errors[] = sprintf("%s%d: %s", gtext("User"), $i, gtext("Password don't match."));
			}
		}
		if (strlen($user) != 0
			&& $delete === false) {
			$index = array_search_ex($user, $auths, "authuser");
			if ($index !== false) {
				$input_errors[] = sprintf("%s%d: %s", gtext("User"), $i, gtext("This user already exists."));
			} else {
				$tmp = [];
				$tmp['authuser'] = $user;
				$tmp['authsecret'] = $secret;
				$tmp['authmuser'] = $muser;
				$tmp['authmsecret'] = $msecret;
				$auths[] = $tmp;
			}
		}
	}

	if (empty($input_errors)) {
		$iscsitarget_ag = [];
		$iscsitarget_ag['uuid'] = $_POST['uuid'];
		$iscsitarget_ag['tag'] = $_POST['tag'];
		$iscsitarget_ag['comment'] = $_POST['comment'];
		$iscsitarget_ag['agauth'] = $auths;

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_iscsitarget_ag[$cnid] = $iscsitarget_ag;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_iscsitarget_ag[] = $iscsitarget_ag;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("iscsitarget_ag", $mode, $iscsitarget_ag['uuid']);
		write_config();

		header("Location: services_iscsitarget_ag.php");
		exit;
	}
}

function expand_ipv6addr($v6addr) {
	if (strlen($v6addr) == 0)
		return null;
	$v6str = $v6addr;

	// IPv4 mapped address
	$pos = strpos($v6str, ".");
	if ($pos !== false) {
		$pos = strrpos($v6str, ":");
		if ($pos === false) {
			return null;
		}
		$v6lstr = substr($v6str, 0, $pos);
		$v6rstr = substr($v6str, $pos + 1);
		$v4a = sscanf($v6rstr, "%d.%d.%d.%d");
		$v6rstr = sprintf("%02x%02x:%02x%02x",
						  $v4a[0], $v4a[1], $v4a[2], $v4a[3]);
		$v6str = $v6lstr.":".$v6rstr;
	}

	// replace zero for "::"
	$pos = strpos($v6str, "::");
	if ($pos !== false) {
		$v6lstr = substr($v6str, 0, $pos);
		$v6rstr = substr($v6str, $pos + 2);
		if (strlen($v6lstr) == 0) {
			$v6lstr = "0";
		}
		if (strlen($v6rstr) == 0) {
			$v6rstr = "0";
		}
		$v6lcnt = strlen(preg_replace("/[^:]/", "", $v6lstr));
		$v6rcnt = strlen(preg_replace("/[^:]/", "", $v6rstr));
		$v6str = $v6lstr;
		$v6ncnt = 8 - ($v6lcnt + 1 + $v6rcnt + 1);
		while ($v6ncnt > 0) {
			$v6str .= ":0";
			$v6ncnt--;
		}
		$v6str .= ":".$v6rstr;
	}

	// zero padding
	$v6a = explode(":", $v6str);
	foreach ($v6a as &$tmp) {
		$tmp = str_pad($tmp, 4, "0", STR_PAD_LEFT);
	}
	unset($tmp);
	$v6str = implode(":", $v6a);
	return $v6str;
}

function normalize_ipv6addr($v6addr) {
	if (strlen($v6addr) == 0)
		return null;
	$v6str = expand_ipv6addr($v6addr);

	// suppress prefix zero
	$v6a = explode(":", $v6str);
	foreach ($v6a as &$tmp) {
		$tmp = preg_replace("/^[0]+/", "", $tmp);
		if (strlen($tmp) == 0) {
			$tmp = "0";
		}
	}
	$v6str = implode(":", $v6a);

	// replace first zero as "::"
	$replace_flag = 1;
	$found_zero = 0;
	$v6a = explode(":", $v6str);
	foreach ($v6a as &$tmp) {
		if (strcmp($tmp, "0") == 0) {
			if ($replace_flag) {
				$tmp = "z";
				$found_zero++;
			}
		} else {
			if ($found_zero) {
				$replace_flag = 0;
			}
		}
	}
	unset($tmp);
	$v6str = implode(":", $v6a);
	if ($found_zero > 1) {
		$v6str = preg_replace("/(:?z:?)+/", "::", $v6str);
	} else {
		$v6str = preg_replace("/(z)+/", "0", $v6str);
	}
	return $v6str;
}
?>
<?php include 'fbegin.inc';?>
<form action="services_iscsitarget_ag_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gtext("Settings");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gtext("Targets");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gtext("Portals");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gtext("Initiators");?></span></a></li>
				<li class="tabact"><a href="services_iscsitarget_ag.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Auths");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_media.php"><span><?=gtext("Media");?></span></a></li>
				</ul>
			</td>
	</tr>
		<tr>
		<td class="tabcont">
		<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
			<table width="100%" border="0" cellpadding="6" cellspacing="0">
			<?php html_titleline(gtext("Auth Group Settings"));?>
			<?php html_inputbox("tag", gtext("Tag number"), $pconfig['tag'], gtext("Numeric identifier of the group."), true, 10, (isset($uuid) && (FALSE !== $cnid)));?>
			<?php html_inputbox("comment", gtext("Comment"), $pconfig['comment'], gtext("You may enter a description here for your reference."), false, 40);?>
			<?php for ($i = 1; $i <= $MAX_AUTHUSERS; $i++): ?>
			<?php $ldelete=sprintf("delete%d", $i); ?>
			<?php $luser=sprintf("user%d", $i); ?>
		<?php $lsecret=sprintf("secret%d", $i); ?>
		<?php $lsecret2=sprintf("secret2%d", $i); ?>
		<?php $lmuser=sprintf("muser%d", $i); ?>
		<?php $lmsecret=sprintf("msecret%d", $i); ?>
		<?php $lmsecret2=sprintf("msecret2%d", $i); ?>
		<?php
		if (!isset($pconfig["$luser"]))
			$pconfig["$luser"] = "";
		if (!isset($pconfig["$lsecret"]))
			$pconfig["$lsecret"] = "";
		if (!isset($pconfig["$lsecret2"]))
			$pconfig["$lsecret2"] = "";
		if (!isset($pconfig["$lmuser"]))
			$pconfig["$lmuser"] = "";
		if (!isset($pconfig["$lmsecret"]))
			$pconfig["$lmsecret"] = "";
		if (!isset($pconfig["$lmsecret2"]))
			$pconfig["$lmsecret2"] = "";
		?>
		<?php html_separator();?>
		<?php html_titleline_checkbox("$ldelete", sprintf("%s%d", gtext("User"), $i), false, gtext("Delete"), false);?>
		<?php html_inputbox("$luser", gtext("User"), $pconfig["$luser"], gtext("Target side user name. It is usually the initiator name by default."), false, 60);?>
		<tr>
		<td width="22%" valign="top" class="vncell"><?=gtext("Secret");?></td>
		<td width="78%" class="vtable">
		<input name="<?=$lsecret;?>" type="password" class="formfld" id="<?=$lsecret;?>" size="30" value="<?=htmlspecialchars($pconfig[$lsecret]);?>" /><br />
		<input name="<?=$lsecret2;?>" type="password" class="formfld" id="<?=$lsecret2;?>" size="30" value="<?=htmlspecialchars($pconfig[$lsecret2]);?>" />&nbsp;(<?=gtext("Confirmation");?>)<br />
		<span class="vexpl"><?=gtext("Target side secret.");?></span>
		</td>
		</tr>
		<?php html_inputbox("$lmuser", gtext("Peer User"), $pconfig["$lmuser"], gtext("Initiator side user name. (for mutual CHAP authentication)"), false, 60);?>
		<tr>
		<td width="22%" valign="top" class="vncell"><?=gtext("Peer Secret");?></td>
		<td width="78%" class="vtable">
		<input name="<?=$lmsecret;?>" type="password" class="formfld" id="<?=$lmsecret;?>" size="30" value="<?=htmlspecialchars($pconfig[$lmsecret]);?>" /><br />
		<input name="<?=$lmsecret2;?>" type="password" class="formfld" id="<?=$lmsecret2;?>" size="30" value="<?=htmlspecialchars($pconfig[$lmsecret2]);?>" />&nbsp;(<?=gtext("Confirmation");?>)<br />
		<span class="vexpl"><?=gtext("Initiator side secret. (for mutual CHAP authentication)");?></span>
		</td>
		</tr>
		<?php endfor;?>
		</table>
		<div id="submit">
			<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gtext("Save") : gtext("Add")?>" />
			<input name="Cancel" type="submit" class="formbtn" value="<?=gtext("Cancel");?>" />
			<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
		</div>
	</td>
</tr>
</table>
<?php include 'formend.inc';?>
</form>
<?php include 'fend.inc';?>
