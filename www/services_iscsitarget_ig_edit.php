<?php
/*
	services_iscsitarget_ig_edit.php

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

$pgtitle = [gtext('Services'),gtext('iSCSI Target'),gtext('Initiator Group'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_iscsitarget_ig = &array_make_branch($config,'iscsitarget','initiatorgroup');
if(empty($a_iscsitarget_ig)):
else:
	array_sort_key($a_iscsitarget_ig,'tag');
endif;

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_iscsitarget_ig, "uuid")))) {
	$pconfig['uuid'] = $a_iscsitarget_ig[$cnid]['uuid'];
	$pconfig['tag'] = $a_iscsitarget_ig[$cnid]['tag'];
	$pconfig['initiators'] = "";
	$pconfig['netmasks'] = "";
	$pconfig['comment'] = $a_iscsitarget_ig[$cnid]['comment'];
	foreach ($a_iscsitarget_ig[$cnid]['iginitiatorname'] as $initiator) {
		$pconfig['initiators'] .= $initiator."\n";
	}
	foreach ($a_iscsitarget_ig[$cnid]['ignetmask'] as $netmask) {
		$pconfig['netmasks'] .= $netmask."\n";
	}
} else {
	// Find next unused tag.
	$tag = 1;
	$a_tags = [];
	foreach($a_iscsitarget_ig as $ig)
		$a_tags[] = $ig['tag'];

	while (true === in_array($tag, $a_tags))
		$tag += 1;

	$pconfig['uuid'] = uuid();
	$pconfig['tag'] = $tag;
	$pconfig['initiators'] = "";
	$pconfig['netmasks'] = "";
	$pconfig['comment'] = "";

	// default initiators and netmasks at first initiator group
	if (count($config['iscsitarget']['initiatorgroup']) == 0) {
		$pconfig['initiators'] = "ALL\n";
		//$pconfig['netmasks'] = "ALL\n";

		$use_v4wildcard = 0;
		$v4_netmasks = "";
		$use_v6wildcard = 0;
		$v6_netmasks = "";
		foreach ($config['interfaces'] as $ifs) {
			if (isset($ifs['enable'])) {
				if (strcmp("dhcp", $ifs['ipaddr']) != 0) {
					$addr = $ifs['ipaddr'];
					$mask = $ifs['subnet'];
					$network = get_ipv4network($addr, $mask);
					$v4_netmasks .= $network."/".$mask."\n";
				} else {
					$use_v4wildcard = 1;
				}
			}
			if (isset($ifs['ipv6_enable'])) {
				if (strcmp("auto", $ifs['ipv6addr']) != 0) {
					$addr = normalize_ipv6addr($ifs['ipv6addr']);
					$mask = $ifs['ipv6subnet'];
					$network = get_ipv6network($addr, $mask);
					$v6_netmasks .= "[".$network."]/".$mask."\n";
				} else {
					$use_v6wildcard = 1;
				}
			}
		}
		if ($use_v4wildcard || $use_v6wildcard) {
			$pconfig['netmasks'] = "ALL\n";
		} else {
			$pconfig['netmasks'] .= $v4_netmasks;
			$pconfig['netmasks'] .= $v6_netmasks;
		}
	}
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: services_iscsitarget_ig.php");
		exit;
	}

	// Input validation.
	$reqdfields = ['tag'];
	$reqdfieldsn = [gtext('Tag Number')];
	$reqdfieldst = ['numericint'];

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if ($pconfig['tag'] < 1 || $pconfig['tag'] > 65535) {
		$input_errors[] = gtext("The tag range is invalid.");
	}

	// Check for duplicates.
	$index = array_search_ex($_POST['tag'], $config['iscsitarget']['initiatorgroup'], "tag");
	if (FALSE !== $index) {
		if (!((FALSE !== $cnid) && ($config['iscsitarget']['initiatorgroup'][$cnid]['uuid'] === $config['iscsitarget']['initiatorgroup'][$index]['uuid']))) {
			$input_errors[] = gtext("This tag already exists.");
		}
	}

	$initiators = [];
	foreach (explode("\n", $_POST['initiators']) as $initiator) {
		$initiator = trim($initiator, " \t\r\n");
		if (!empty($initiator)) {
			$initiators[] = sprintf("%s", $initiator);
		}
	}
	if (count($initiators) == 0) {
		$input_errors[] = sprintf(gtext("The attribute '%s' is required."), gtext("Initiators"));
	}

	$netmasks = [];
	foreach (explode("\n", $_POST['netmasks']) as $netmask) {
		$netmask = trim($netmask, " \t\r\n");
		if (!empty($netmask)) {
			if (preg_match("/^\[([0-9a-fA-F:]+)\](\/([0-9]+))?$/", $netmask, $tmp)) {
				// IPv6
				$addr = normalize_ipv6addr($tmp[1]);
				$mask = isset($tmp[3]) ? $tmp[3] : 128;
				if (!is_ipv6addr($addr) || $mask < 0 || $mask > 128) {
					$input_errors[] = sprintf(gtext("The network '%s' is invalid."), $netmask);
				}
				$netmasks[] = sprintf("[%s]/%d", $addr, $mask);
			} else if (preg_match("/^([0-9\.]+)(\/([0-9]+))?$/", $netmask, $tmp)) {
				// IPv4
				$addr = $tmp[1];
				$mask = isset($tmp[3]) ? $tmp[3] : 32;
				if (!is_ipv4addr($addr) || $mask < 0 || $mask > 32) {
					$input_errors[] = sprintf(gtext("The network '%s' is invalid."), $netmask);
				}
				$netmasks[] = sprintf("%s/%d", $addr, $mask);
			} else if (strcasecmp("ALL", $netmask) == 0) {
				$netmasks[] = sprintf("%s", $netmask);
			} else {
				$input_errors[] = sprintf(gtext("The network '%s' is invalid."), $netmask);
			}
		}
	}
	if (count($netmasks) == 0) {
		$input_errors[] = sprintf(gtext("The attribute '%s' is required."), gtext("Authorised Network"));
	}

	if (empty($input_errors)) {
		$iscsitarget_ig = [];
		$iscsitarget_ig['uuid'] = $_POST['uuid'];
		$iscsitarget_ig['tag'] = $_POST['tag'];
		$iscsitarget_ig['comment'] = $_POST['comment'];
		$iscsitarget_ig['iginitiatorname'] = $initiators;
		$iscsitarget_ig['ignetmask'] = $netmasks;

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_iscsitarget_ig[$cnid] = $iscsitarget_ig;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_iscsitarget_ig[] = $iscsitarget_ig;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("iscsitarget_ig", $mode, $iscsitarget_ig['uuid']);
		write_config();

		header("Location: services_iscsitarget_ig.php");
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

function get_ipv4network($v4addr, $mask) {
	if (strlen($v4addr) == 0)
		return null;
	$v4str = $v4addr;

	// compute by 8bits
	$v4a = explode(".", $v4str);
	foreach ($v4a as &$tmp) {
		if ($mask >= 8) {
			$mask -= 8;
		} else if ($mask != 0) {
			$bmask = 0xff;
			$bmask <<= (8 - $mask);
			$tmp = (intval($tmp,10) & $bmask);
			$mask = 0;
		} else {
			$tmp = 0;
		}
	}
	unset($tmp);
	$v4str = implode(".", $v4a);
	return $v4str;
}

function get_ipv6network($v6addr, $mask) {
	if (strlen($v6addr) == 0)
		return null;
	$v6str = expand_ipv6addr($v6addr);

	// compute by 16bits
	$v6a = explode(":", $v6str);
	foreach ($v6a as &$tmp) {
		if ($mask >= 16) {
			$mask -= 16;
		} else if ($mask != 0) {
			$bmask = 0xffff;
			$bmask <<= (16 - $mask);
			$tmp = sprintf("%x", (intval($tmp,16) & $bmask));
			$mask = 0;
		} else {
			$tmp = 0;
		}
	}
	unset($tmp);
	$v6str = implode(":", $v6a);
	$v6str = normalize_ipv6addr($v6str);
	return $v6str;
}
?>
<?php include 'fbegin.inc';?>
<form action="services_iscsitarget_ig_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
		<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gtext("Settings");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gtext("Targets");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gtext("Portals");?></span></a></li>
		<li class="tabact"><a href="services_iscsitarget_ig.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Initiators");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gtext("Auths");?></span></a></li>
		<li class="tabinact"><a href="services_iscsitarget_media.php"><span><?=gtext("Media");?></span></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
		<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<?php html_titleline(gtext("Initiator Group Settings"));?>
		<?php html_inputbox("tag", gtext("Tag Number"), $pconfig['tag'], gtext("Numeric identifier of the group."), true, 10, (isset($uuid) && (FALSE !== $cnid)));?>
		<?php html_textarea("initiators", gtext("Initiators"), $pconfig['initiators'], gtext("Initiator authorised to access to the iSCSI target.  It takes a name or 'ALL' for any initiators."), true, 65, 7, false, false);?>
		<?php html_textarea("netmasks", gtext("Authorised Network"), $pconfig['netmasks'], gtext("Network authorised to access to the iSCSI target. It takes IP or CIDR addresses or 'ALL' for any IPs."), true, 65, 7, false, false);?>
		<?php html_inputbox("comment", gtext("Comment"), $pconfig['comment'], gtext("You may enter a description here for your reference."), false, 40);?>
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
