<?php
/*
	services_iscsitarget_extent_edit.php

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

/*
TODO: 	1) Script to creat file based extend in existing(mounted) File System e.g.(/mnt/$mountpoint/.../$filename) 
		with automaticaly formatting in necessary structure ( e.g. http://www.freebsd.org/doc/en_US.ISO8859-1/books/handbook/disks-virtual.html). 
		2) Insert changes to GUI for script.
		3) row 196.
*/
require_once 'auth.inc';
require_once 'guiconfig.inc';

if (isset($_GET['uuid']))
	$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];
if (!isset($uuid))
	$uuid = null;

$pgtitle = [gtext('Services'),gtext('iSCSI Target'),gtext('Extent'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_iscsitarget_extent = &array_make_branch($config,'iscsitarget','extent');
if(empty($a_iscsitarget_extent)):
else:
	array_sort_key($a_iscsitarget_extent,'name');
endif;

function get_all_device($a_extent,$uuid) {
	$a = [];
	$a[''] = gtext("Must choose one");
	foreach (get_conf_all_disks_list_filtered() as $diskv) {
		$file = $diskv['devicespecialfile'];
		$size = $diskv['size'];
		$name = $diskv['name'];
		$desc = $diskv['desc'];
		if (strcmp($size, "NA") == 0) continue;
		if (disks_exists($file) == 1) continue;
		$index = array_search_ex($file, $a_extent, "path");
		if (FALSE !== $index) {
			if (!isset($uuid)) continue;
			if ($a_extent[$index]['uuid'] != $uuid) continue;
		}
		if (disks_ismounted_ex($file, "devicespecialfile")) continue;
		$a[$file] = htmlspecialchars("$name: $size ($desc)");
	}
	return $a;
}

// TODO: handle SCSI pass-through device
function get_all_scsi_device($a_extent,$uuid) {
	$a = [];
	$a[''] = gtext("Must choose one");
	foreach (get_conf_all_disks_list_filtered() as $diskv) {
		$file = $diskv['devicespecialfile'];
		$size = $diskv['size'];
		$name = $diskv['name'];
		$desc = $diskv['desc'];
		if (strcmp($size, "NA") == 0) continue;
		if (disks_exists($file) == 1) continue;
		$index = array_search_ex($file, $a_extent, "path");
		if (FALSE !== $index) {
			if (!isset($uuid)) continue;
			if ($a_extent[$index]['uuid'] != $uuid) continue;
		}
		if (!preg_match("/^(da|cd|sa|ch)[0-9]/", $name)) continue;
		$a[$file] = htmlspecialchars("$name: $size ($desc)");
	}
	return $a;
}

function get_all_zvol($a_extent,$uuid) {
	$a = [];
	$a[''] = gtext("Must choose one");
	mwexec2("zfs list -H -t volume -o name,volsize,sharenfs,org.freebsd:swap", $rawdata);
	foreach ($rawdata as $line) {
		$zvol = explode("\t", $line);
		$name = $zvol[0];
		$file = "/dev/zvol/$name";
		$size = $zvol[1];
		$sharenfs = $zvol[2];
		$swap = $zvol[3];
		if ($sharenfs !== "-") continue;
		if ($swap !== "-") continue;
		$index = array_search_ex($file, $a_extent, "path");
		if (FALSE !== $index) {
			if (!isset($uuid)) continue;
			if ($a_extent[$index]['uuid'] != $uuid) continue;
		}
		$a[$file] = htmlspecialchars("$name: $size");
	}
	return $a;
}

function get_all_hast($a_extent,$uuid) {
	$a = [];
	$a[''] = gtext('Must choose one');
	$use_si = is_sidisksizevalues();
	mwexec2("hastctl dump | grep resource", $rawdata);
	foreach ($rawdata as $line) {
		$hast = preg_split("/\s/", $line);
		$name = $hast[1];
		$file = "/dev/hast/$name";
		if (file_exists($file)) {
			$diskinfo = disks_get_diskinfo($file);
			$size = format_bytes($diskinfo['mediasize_bytes'],2,true,$use_si);
		} else {
			$size = "(secondary)";
		}
		$index = array_search_ex($file, $a_extent, "path");
		if (FALSE !== $index) {
			if (!isset($uuid)) continue;
			if ($a_extent[$index]['uuid'] != $uuid) continue;
		}
		$a[$file] = htmlspecialchars("$name: $size");
	}
	return $a;
}

$a_device = get_all_device($a_iscsitarget_extent,$uuid);
$a_scsi_device = get_all_scsi_device($a_iscsitarget_extent,$uuid);
$a_zvol = get_all_zvol($a_iscsitarget_extent,$uuid);
$a_hast = get_all_hast($a_iscsitarget_extent,$uuid);

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_iscsitarget_extent, "uuid")))) {
	$pconfig['uuid'] = $a_iscsitarget_extent[$cnid]['uuid'];
	$pconfig['name'] = $a_iscsitarget_extent[$cnid]['name'];
	$pconfig['path'] = $a_iscsitarget_extent[$cnid]['path'];
	$pconfig['size'] = $a_iscsitarget_extent[$cnid]['size'];
	$pconfig['sizeunit'] = $a_iscsitarget_extent[$cnid]['sizeunit'];
	$pconfig['type'] = $a_iscsitarget_extent[$cnid]['type'];
	$pconfig['comment'] = $a_iscsitarget_extent[$cnid]['comment'];

	if (!isset($pconfig['sizeunit']))
		$pconfig['sizeunit'] = "MB";
} else {
	// Find next unused ID.
	$extentid = 0;
	$a_id = [];
	foreach($a_iscsitarget_extent as $extent)
		$a_id[] = (int)str_replace("extent", "", $extent['name']); // Extract ID.
	while (true === in_array($extentid, $a_id))
		$extentid += 1;

	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "extent{$extentid}";
	$pconfig['path'] = "";
	$pconfig['size'] = "";
	$pconfig['sizeunit'] = "MB";
	$pconfig['type'] = "file";
	$pconfig['comment'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: services_iscsitarget_target.php");
		exit;
	}

	// Input validation.
	if ($_POST['type'] == 'device') {
		$pconfig['sizeunit'] = "auto";
		$_POST['sizeunit'] = "auto";
		$pconfig['size'] = "";
		$_POST['size'] = "";
		$reqdfields = ['name','device'];
		$reqdfieldsn = [gtext('Extent name'),gtext('Device')];
		$reqdfieldst = ['string','string'];
	} else if ($_POST['type'] == 'zvol') {
		$pconfig['sizeunit'] = "auto";
		$_POST['sizeunit'] = "auto";
		$pconfig['size'] = "";
		$_POST['size'] = "";
		$reqdfields = ['name','zvol'];
		$reqdfieldsn = [gtext('Extent name'),gtext('ZFS volume')];
		$reqdfieldst = ['string','string'];
	} else if ($_POST['type'] == 'hast') {
		$pconfig['sizeunit'] = "auto";
		$_POST['sizeunit'] = "auto";
		$pconfig['size'] = "";
		$_POST['size'] = "";
		$reqdfields = ['name','hast'];
		$reqdfieldsn = [gtext('Extent name'),gtext('HAST volume')];
		$reqdfieldst = ['string','string'];
	} else {
		if ($pconfig['sizeunit'] == 'auto'){
			$pconfig['size'] = "";
			$_POST['size'] = "";
			$reqdfields = ['name','path','sizeunit'];
			$reqdfieldsn = [gtext('Extent Name'),gtext('Path'),gtext('Auto size')];
			$reqdfieldst = ['string','string','string'];
		}else{
			$reqdfields = ['name','path','size','sizeunit'];
			$reqdfieldsn = [gtext('Extent name'),gtext('Path'),gtext('Size'),gtext('File sizeunit')];
			$reqdfieldst = ['string','string','numericint','string'];
		}
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	// Check for duplicates.
	$index = array_search_ex($_POST['name'], $a_iscsitarget_extent, "name");
	if (FALSE !== $index) {
		if (!((FALSE !== $cnid) && ($a_iscsitarget_extent[$cnid]['uuid'] === $a_iscsitarget_extent[$index]['uuid']))) {
			$input_errors[] = gtext("The extent name already exists.");
		}
	}

	// Check if path exists and match directory.
	if ($_POST['type'] == 'file') {
		$dirname = dirname($_POST['path']);
		$basename = basename($_POST['path']);
		if ($dirname !== "/") {
			$path = "$dirname/$basename";
		} else {
			$path = "/$basename";
		}
		if (!file_exists($dirname)) {
			$input_errors[] = sprintf(gtext("The path '%s' does not exist."), $dirname);
		}
		if (!is_dir($dirname)) {
			$input_errors[] = sprintf(gtext("The path '%s' is not a directory."), $dirname);
		}
		if (is_dir($path)) {
			$input_errors[] = sprintf(gtext("The path '%s' is a directory."), $path);
		}
	} else if ($_POST['type'] == 'zvol') {
		$path = $_POST['zvol'];
	} else if ($_POST['type'] == 'hast') {
		$path = $_POST['hast'];
	} else {
		$path = $_POST['device'];
	}
	$pconfig['path'] = $path;

	if (empty($input_errors)) {
		$iscsitarget_extent = [];
		$iscsitarget_extent['uuid'] = $_POST['uuid'];
		$iscsitarget_extent['name'] = $_POST['name'];
		$iscsitarget_extent['path'] = $path;
		$iscsitarget_extent['size'] = $_POST['size'];
		$iscsitarget_extent['sizeunit'] = $_POST['sizeunit'];
		$iscsitarget_extent['type'] = $_POST['type'];
		$iscsitarget_extent['comment'] = $_POST['comment'];

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_iscsitarget_extent[$cnid] = $iscsitarget_extent;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_iscsitarget_extent[] = $iscsitarget_extent;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("iscsitarget_extent", $mode, $iscsitarget_extent['uuid']);
		write_config();

		header("Location: services_iscsitarget_target.php");
		exit;
	}
}
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
<!--
function type_change() {
	switch (document.iform.type.value) {
	case "file":
		showElementById("path_tr", 'show');
		showElementById("size_tr", 'show');
		showElementById("device_tr", 'hide');
		showElementById("zvol_tr", 'hide');
		showElementById("hast_tr", 'hide');
		break;
	case "device":
		showElementById("path_tr", 'hide');
		showElementById("size_tr", 'hide');
		showElementById("device_tr", 'show');
		showElementById("zvol_tr", 'hide');
		showElementById("hast_tr", 'hide');
		break;
	case "zvol":
		showElementById("path_tr", 'hide');
		showElementById("size_tr", 'hide');
		showElementById("device_tr", 'hide');
		showElementById("zvol_tr", 'show');
		showElementById("hast_tr", 'hide');
		break;
	case "hast":
		showElementById("path_tr", 'hide');
		showElementById("size_tr", 'hide');
		showElementById("device_tr", 'hide');
		showElementById("zvol_tr", 'hide');
		showElementById("hast_tr", 'show');
		break;
	}
}

function sizeunit_change() {
	switch (document.iform.sizeunit.value) {
	case "auto":
		document.iform.size.disabled = true;
		break;
	default:
		document.iform.size.disabled = false;
		break;
	}
}
//-->
</script>
<form action="services_iscsitarget_extent_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
				<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gtext("Settings");?></span></a></li>
				<li class="tabact"><a href="services_iscsitarget_target.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Targets");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gtext("Portals");?></span></a></li>
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
		<?php html_titleline(gtext("Extent Settings"));?>
			<?php html_inputbox("name", gtext("Extent Name"), $pconfig['name'], gtext("String identifier of the extent."), true, 30, (isset($uuid) && (FALSE !== $cnid)));?>
			<?php html_combobox("type", gtext("Type"), $pconfig['type'], ['file' => gtext('File'), 'device' => gtext('Device'), 'zvol' => gtext('ZFS volume'), 'hast' => gtext('HAST volume')], gtext("Type used as extent."), true, false, "type_change()");?>
			<?php html_filechooser("path", gtext("Path"), $pconfig['path'], sprintf(gtext("File path (e.g. /mnt/sharename/extent/%s) used as extent."), $pconfig['name']), $g['media_path'], true);?>
			<?php html_combobox("device", gtext("Device"), $pconfig['path'], $a_device, "", true);?>
			<?php html_combobox("zvol", gtext("ZFS volume"), $pconfig['path'], $a_zvol, "", true);?>
			<?php html_combobox("hast", gtext("HAST volume"), $pconfig['path'], $a_hast, "", true);?>
			<tr id="size_tr">
			<td width="22%" valign="top" class="vncellreq"><?=gtext("Size");?></td>
			<td width="78%" class="vtable">
			<input name="size" type="text" class="formfld" id="size" size="10" value="<?=htmlspecialchars($pconfig['size']);?>" />
			<select name="sizeunit" onclick="sizeunit_change()"> 
			<option value="MB" <?php if ($pconfig['sizeunit'] === "MB") echo "selected=\"selected\"";?>><?=gtext("MiB");?></option>
			<option value="GB" <?php if ($pconfig['sizeunit'] === "GB") echo "selected=\"selected\"";?>><?=gtext("GiB");?></option>
			<option value="TB" <?php if ($pconfig['sizeunit'] === "TB") echo "selected=\"selected\"";?>><?=gtext("TiB");?></option>
			<option value="auto" <?php if ($pconfig['sizeunit'] === "auto") echo "selected=\"selected\"";?>><?=gtext("Auto");?></option>
	</select><br />
			<span class="vexpl"><?=gtext("Size offered to the initiator. (up to 8EiB=8388608TiB).");?><br><?=gtext("The actual size is depend on your disks.");?></br>
		</td>
		</tr>
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
<script type="text/javascript">
<!--
type_change();
sizeunit_change();
//-->
</script>
<?php include 'fend.inc';?>
