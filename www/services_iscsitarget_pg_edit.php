<?php
/*
	services_iscsitarget_pg_edit.php

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

$pgtitle = [gtext('Services'),gtext('iSCSI Target'),gtext('Portal Group'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_iscsitarget_pg = &array_make_branch($config,'iscsitarget','portalgroup');
if(empty($a_iscsitarget_pg)):
else:
	array_sort_key($a_iscsitarget_pg,'tag');
endif;

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_iscsitarget_pg, "uuid")))) {
	$pconfig['uuid'] = $a_iscsitarget_pg[$cnid]['uuid'];
	$pconfig['tag'] = $a_iscsitarget_pg[$cnid]['tag'];
	$pconfig['portals'] = "";
	$pconfig['comment'] = $a_iscsitarget_pg[$cnid]['comment'];
	foreach ($a_iscsitarget_pg[$cnid]['portal'] as $portal) {
		$pconfig['portals'] .= $portal."\n";
	}
} else {
	// Find next unused tag.
	$tag = 1;
	$a_tags = [];
	foreach($a_iscsitarget_pg as $pg)
		$a_tags[] = $pg['tag'];

	while (true === in_array($tag, $a_tags))
		$tag += 1;

	$pconfig['uuid'] = uuid();
	$pconfig['tag'] = $tag;
	$pconfig['portals'] = "";
	$pconfig['comment'] = "";

	// default portals at first portal group
	if (count($config['iscsitarget']['portalgroup']) == 0) {
		$use_v4wildcard = 0;
		$v4_portals = "";
		$use_v6wildcard = 0;
		$v6_portals = "";
		foreach ($config['interfaces'] as $ifs) {
			if (isset($ifs['enable'])) {
				if (strcmp("dhcp", $ifs['ipaddr']) != 0) {
					$addr = $ifs['ipaddr'];
					$v4_portals .= $addr.":3260\n";
				} else {
					$use_v4wildcard = 1;
				}
			}
			if (isset($ifs['ipv6_enable'])) {
				if (strcmp("auto", $ifs['ipv6addr']) != 0) {
					$addr = normalize_ipv6addr($ifs['ipv6addr']);
					$v6_portals .= "[".$addr."]:3260\n";
				} else {
					$use_v6wildcard = 1;
				}
			}
		}
		if ($use_v4wildcard) {
			$v4_portals = "0.0.0.0:3260\n";
		}
		if ($use_v6wildcard) {
			$v6_portals = "[::]:3260\n";
		}
		$pconfig['portals'] .= $v4_portals;
		$pconfig['portals'] .= $v6_portals;
	}
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: services_iscsitarget_pg.php");
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
	$index = array_search_ex($_POST['tag'], $config['iscsitarget']['portalgroup'], "tag");
	if (FALSE !== $index) {
		if (!((FALSE !== $cnid) && ($config['iscsitarget']['portalgroup'][$cnid]['uuid'] === $config['iscsitarget']['portalgroup'][$index]['uuid']))) {
			$input_errors[] = gtext("This tag already exists.");
		}
	}

	$portals = [];
	foreach (explode("\n", $_POST['portals']) as $portal) {
		$portal = trim($portal, " \t\r\n");
		if (!empty($portal)) {
			if (preg_match("/^\[([0-9a-fA-F:]+)\](:([0-9]+))?$/", $portal, $tmp)) {
				// IPv6
				$addr = normalize_ipv6addr($tmp[1]);
				$port = isset($tmp[3]) ? $tmp[3] : 3260;
				if (!is_ipv6addr($addr) || $port < 1 || $port > 65535) {
					$input_errors[] = sprintf(gtext("The portal '%s' is invalid."), $portal);
				}
				$portals[] = sprintf("[%s]:%d", $addr, $port);
			} else if (preg_match("/^([0-9\.]+)(:([0-9]+))?$/", $portal, $tmp)) {
				// IPv4
				$addr = $tmp[1];
				$port = isset($tmp[3]) ? $tmp[3] : 3260;
				if (!is_ipv4addr($addr) || $port < 1 || $port > 65535) {
					$input_errors[] = sprintf(gtext("The portal '%s' is invalid."), $portal);
				}
				$portals[] = sprintf("%s:%d", $addr, $port);
			} else {
				$input_errors[] = sprintf(gtext("The portal '%s' is invalid."), $portal);
			}
		}
	}
	if (count($portals) == 0) {
		$input_errors[] = sprintf(gtext("The attribute '%s' is required."), gtext("Portals"));
	}

	if (empty($input_errors)) {
		$iscsitarget_pg = [];
		$iscsitarget_pg['uuid'] = $_POST['uuid'];
		$iscsitarget_pg['tag'] = $_POST['tag'];
		$iscsitarget_pg['comment'] = $_POST['comment'];
		$iscsitarget_pg['portal'] = $portals;

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_iscsitarget_pg[$cnid] = $iscsitarget_pg;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_iscsitarget_pg[] = $iscsitarget_pg;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("iscsitarget_pg", $mode, $iscsitarget_pg['uuid']);
		write_config();

		header("Location: services_iscsitarget_pg.php");
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
<form action="services_iscsitarget_pg_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gtext("Settings");?></span></a></li>
			<li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gtext("Targets");?></span></a></li>
			<li class="tabact"><a href="services_iscsitarget_pg.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Portals");?></span></a></li>
			<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gtext("Initiators");?></span></a></li>
			<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gtext("Auths");?></span></a></li>
			<li class="tabinact"><a href="services_iscsitarget_media.php"><span><?=gtext("Media");?></span></a></li>
			</ul>
		</td>
	</tr>
<tr>
	<td class="tabcont">
			<?php if (!empty($input_errors)) print_input_errors($input_errors);?>
			<table width="100%" border="0" cellpadding="6" cellspacing="0">
			<?php html_titleline(gtext("Portal Group Settings"));?>
			<?php html_inputbox("tag", gtext("Tag number"), $pconfig['tag'], gtext("Numeric identifier of the group."), true, 10, (isset($uuid) && (FALSE !== $cnid)));?>
			<?php html_textarea("portals", gtext("Portals"), $pconfig['portals'], gtext("The portal takes the form of 'address:port'. for example '192.168.1.1:3260' for IPv4, '[2001:db8:1:1::1]:3260' for IPv6. the port 3260 is standard iSCSI port number. For any IPs (wildcard address), use '0.0.0.0:3260' and/or '[::]:3260'. Do not mix wildcard and other IPs at same address family."), true, 65, 7, false, false);?>
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
