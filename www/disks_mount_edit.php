<?php
/*
	disks_mount_edit.php

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

require_once 'autoload.php';
require_once 'auth.inc';
require_once 'guiconfig.inc';

use common\arr;
use common\uuid;

if(isset($_GET['uuid'])):
	$uuid = $_GET['uuid'];
endif;
if(isset($_POST['uuid'])):
	$uuid = $_POST['uuid'];
endif;
$pgtitle = [gtext('Disks'),gtext('Mount Point'),isset($uuid) ? gtext('Edit') : gtext('Add')];
$a_mount = &arr::make_branch($config,'mounts','mount');
if(empty($a_mount)):
else:
	arr::sort_key($a_mount,'devicespecialfile');
endif;
function get_all_hast() {
	$a = [];
	$a[''] = gtext('Must choose one');
	$use_si = is_sidisksizevalues();
	mwexec2('hastctl dump | grep resource',$rawdata);
	foreach($rawdata as $line):
		$hast = preg_split('/\s/',$line);
		$name = $hast[1];
		$file = "/dev/hast/$name";
		if(file_exists($file)):
			$diskinfo = disks_get_diskinfo($file);
			$size = format_bytes($diskinfo['mediasize_bytes'],2,true,$use_si);
		else:
			$size = '(secondary)';
		endif;
		$a[$file] = htmlspecialchars("$name: $size");
	endforeach;
	return $a;
}
$a_hast = get_all_hast();
//	Get list of all configured disks (physical and virtual).
$a_disk = get_conf_all_disks_list_filtered();
//	Load the /etc/cfdevice file to find out on which disk the OS is installed.
$cfdevice = trim(file_get_contents("{$g['etc_path']}/cfdevice"));
$cfdevice = "/dev/{$cfdevice}";
$cnid = isset($uuid) ? arr::search_ex($uuid,$a_mount,'uuid') : false;
if(isset($uuid) && ($cnid !== false)):
	$pconfig['uuid'] = $a_mount[$cnid]['uuid'];
	$pconfig['type'] = $a_mount[$cnid]['type'];
	$pconfig['mdisk'] = $a_mount[$cnid]['mdisk'];
	$pconfig['partition'] = $a_mount[$cnid]['partition'];
	$pconfig['devicespecialfile'] = $a_mount[$cnid]['devicespecialfile'];
	$pconfig['fstype'] = $a_mount[$cnid]['fstype'];
	$pconfig['sharename'] = $a_mount[$cnid]['sharename'];
	$pconfig['desc'] = $a_mount[$cnid]['desc'];
	$pconfig['readonly'] = isset($a_mount[$cnid]['readonly']);
	$pconfig['fsck'] = isset($a_mount[$cnid]['fsck']);
	$pconfig['owner'] = $a_mount[$cnid]['accessrestrictions']['owner'];
	$pconfig['group'] = $a_mount[$cnid]['accessrestrictions']['group'][0];
	$pconfig['mode'] = $a_mount[$cnid]['accessrestrictions']['mode'];
	$pconfig['filename'] = !empty($a_mount[$cnid]['filename']) ? $a_mount[$cnid]['filename'] : '';
	$pconfig['hvol'] = $pconfig['mdisk'];
	$pconfig['devname'] = $pconfig['mdisk'];
else:
	$pconfig['uuid'] = uuid::create_v4();
	$pconfig['type'] = 'disk';
	$pconfig['devname'] = '';
	$pconfig['partition'] = 'p1';
	$pconfig['readonly'] = false;
	$pconfig['fsck'] = true;
	$pconfig['owner'] = 'root';
	$pconfig['group'] = 'wheel';
	$pconfig['mode'] = '0777';
endif;
//	Split partition string
$pconfig['partitiontype'] = substr($pconfig['partition'],0,1);
$pconfig['partitionnum'] = substr($pconfig['partition'],1);
$pconfig['partitionnum'] = preg_replace('/(\d+).*/','\1',$pconfig['partitionnum']);
if(array_key_exists('fstype',$pconfig) && $pconfig['fstype'] == 'exfat' && $pconfig['partitiontype'] == ''):
	$pconfig['partitiontype'] = 'p';
	$pconfig['partitionnum'] = 1;
endif;
initmodectrl($pconfig,$pconfig['mode']);
if($_POST):
	unset($input_errors);
	$pconfig = $_POST;
	if(isset($_POST['Cancel'])):
		header('Location: disks_mount.php');
		exit;
	endif;
//	Rebuild partition string
	$_POST['partition'] = '';
	if('disk' === $_POST['type'] || 'hvol' === $_POST['type']):
		switch($_POST['partitiontype']):
			case 'p':
			case 's':
				$_POST['partition'] = $_POST['partitiontype'].trim($_POST['partitionnum']);
				break;
		endswitch;
	endif;
//	Input validation
	switch ($_POST['type']):
		case 'disk':
			$reqdfields = ['mdisk','partitiontype','fstype','sharename'];
			$reqdfieldsn = [gtext('Disk'),gtext('Partition type'),gtext('File system'),gtext('Mount point name')];
			$reqdfieldst = ['string','string','string','string'];
			switch ($_POST['partitiontype']):
				case 'p':
				case 's':
					$reqdfields = array_merge($reqdfields,['partitionnum']);
					$reqdfieldsn = array_merge($reqdfieldsn,[gtext('Partition number')]);
					$reqdfieldst = array_merge($reqdfieldst,['numeric']);
					break;
			endswitch;
			break;
		case 'hvol':
			$reqdfields = ['hvol','partitiontype','fstype','sharename'];
			$reqdfieldsn = [gtext('HAST volume'),gtext('Partition type'),gtext('File system'),gtext('Mount point name')];
			$reqdfieldst = ['string','string','string','string'];
			switch ($_POST['partitiontype']):
				case 'p':
				case 's':
					$reqdfields = array_merge($reqdfields,['partitionnum']);
					$reqdfieldsn = array_merge($reqdfieldsn,[gtext('Partition number')]);
					$reqdfieldst = array_merge($reqdfieldst,['numeric']);
					break;
			endswitch;
			break;
		case 'iso':
			$reqdfields = ['filename','sharename'];
			$reqdfieldsn = [gtext('Filename'),gtext('Mount point name')];
			$reqdfieldst = ['string','string'];
			break;
		case 'custom':
			$reqdfields = ['devname','fstype','sharename'];
			$reqdfieldsn = [gtext('Device / Label'),gtext('File system'),gtext('Mount point name')];
			$reqdfieldst = ['string','string','string'];
			break;
	endswitch;
	do_input_validation($_POST,$reqdfields,$reqdfieldsn,$input_errors);
	do_input_validation_type($_POST,$reqdfields,$reqdfieldsn,$reqdfieldst,$input_errors);
	if(($_POST['sharename'] && !is_validsharename($_POST['sharename']))):
		$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters a-z, A-Z, 0-9, _ , -."),gtext('Name'));
	endif;
	if(($_POST['desc'] && !is_validdesc($_POST['desc']))):
		$input_errors[] = sprintf(gtext("The attribute '%s' contains invalid characters."),gtext('Description'));
	endif;
//	Do some 'disk' specific checks.
	if($_POST['type'] === 'disk'):
		if(($_POST['partition'] == 'p1') && (($_POST['fstype'] == 'msdosfs') || ($_POST['fstype'] == 'cd9660'))) :
			$input_errors[] = gtext('EFI/GPT partition can be use with UFS only.');
		endif;
		$device = "{$_POST['mdisk']}{$_POST['partition']}";
		if(($_POST['fstype'] == 'ufs') && preg_match('/s\d+$/',$_POST['partition'])):
			// MBR/UFS
			if(file_exists("{$device}a")):
				$_POST['partition'] = "{$_POST['partition']}a";
				$device = "{$device}a";
			endif;
		endif;
		if($device === $cfdevice):
			$input_errors[] = gtext("Can't mount the system partition 1, the DATA partition is the 2.");
		endif;
//		Check if partition exist
		if(($_POST['fstype'] == 'exfat') && (($_POST['partition'] == 'p1') || ($_POST['partition'] == 's1'))):
//			no partition is OK
			if(!file_exists($device)):
				$_POST['partition'] = '';
				$device = "{$_POST['mdisk']}{$_POST['partition']}";
			endif;
		elseif(!file_exists($device)):
			$input_errors[] = gtext('Wrong partition type or partition number.');
		endif;
//		get rawuuid
		if($_POST['partitiontype'] == 'p'):
			$rawuuid = disks_get_rawuuid($device);
		else:
			$rawuuid = ''; // should be fixed
		endif;
//		convert to UFSID
		if($_POST['fstype'] == 'ufs'):
			$out = [];
			$ufsid = disks_get_ufsid($device,$out);
			if(empty($ufsid)):
				$input_errors[] = sprintf('%s: %s',$device,gtext("Can't get UFS ID.")) . '<br />' . join('<br />',$out);
			else:
				$device = "/dev/ufsid/$ufsid";
			endif;
		endif;
	endif;
//	HAST volume specific
	if($_POST['type'] === 'hvol'):
		if($_POST['partitiontype'] != 'p'):
			$input_errors[] = gtext('HAST volume can be use with GPT/UFS only.');
		endif;
		$device = "{$_POST['hvol']}{$_POST['partition']}";
		if($device === $cfdevice):
			$input_errors[] = gtext("Can't mount the system partition 1, the DATA partition is the 2.");
		endif;
//		Check if partition exist
		if(!file_exists($device)):
			$input_errors[] = gtext('Wrong partition type or partition number.');
		endif;
//		get rawuuid
		if($_POST['partitiontype'] == 'p'):
			$rawuuid = disks_get_rawuuid($device);
		else:
			$rawuuid = ''; // should be fixed
		endif;
//		convert to UFSID
		if($_POST['fstype'] == 'ufs'):
			$out = [];
			$ufsid = disks_get_ufsid($device,$out);
			if(empty($ufsid)):
				$input_errors[] = sprintf('%s: %s',$device,gtext("Can't get UFS ID.")) . '<br />' . join('<br />',$out);
			else:
				$device = "/dev/ufsid/$ufsid";
			endif;
		endif;
	endif;
//	Check if it is a valid ISO image.
	if(('iso' === $_POST['type']) && (false === util_is_iso_image($_POST['filename']))):
		$input_errors[] = gtext("Selected file isn't an valid ISO file.");
	endif;
//	Check if custom device exists.
	if(($_POST['type'] === 'custom') && !file_exists($_POST['devname'])):
		$input_errors[] = gtext('Selected device or label does not exist.');
	endif;
//	Check for duplicates.
	if($_POST['type'] === 'disk'):
		foreach($a_mount as $mount):
			if (isset($uuid) && ($cnid !== false) && ($mount['uuid'] === $uuid)):
				continue;
			endif;
			if(($mount['mdisk'] === $_POST['mdisk']) && ($mount['partition'] === $_POST['partition'])):
				$input_errors[] = gtext('The disk/partition is already configured.');
				break;
			endif;
		endforeach;
	endif;
	if('hvol' === $_POST['type']):
		foreach($a_mount as $mount):
			if(isset($uuid) && ($cnid !== false) && ($mount['uuid'] === $uuid)):
				continue;
			endif;
			if(($mount['mdisk'] === $_POST['hvol']) && ($mount['partition'] === $_POST['partition'])):
				$input_errors[] = gtext('The disk/partition is already configured.');
				break;
			endif;
		endforeach;
	endif;
//	Check whether the mount point name is already in use.
	$index = arr::search_ex($_POST['sharename'],$a_mount,'sharename');
	if($index !== false):
//		Ensure we do not check the current processed mount point itself.
		if(!(($cnid !== false) && ($a_mount[$cnid]['uuid'] === $a_mount[$index]['uuid']))):
			$input_errors[] = gtext('Duplicate mount point name.');
		endif;
	endif;
	if(empty($input_errors)):
		$mount = [];
		$mount['uuid'] = $_POST['uuid'];
		$mount['type'] = $_POST['type'];
		switch($_POST['type']):
			case 'disk':
				$mount['mdisk'] = $_POST['mdisk'];
				$mount['partition'] = $_POST['partition'];
				$mount['fstype'] = $_POST['fstype'];
				$mount['gpt'] = ($_POST['partitiontype'] == 'p');
				$mount['rawuuid'] = $rawuuid;
				if($mount['fstype'] == 'ufs'):
					$mount['devicespecialfile'] = $device;
				else:
					$mount['devicespecialfile'] = trim("{$mount['mdisk']}{$mount['partition']}");
				endif;
				$mount['readonly'] = isset($_POST['readonly']);
				$mount['fsck'] = isset($_POST['fsck']);
				break;
			case 'hvol':
				$mount['mdisk'] = $_POST['hvol'];
				$mount['partition'] = $_POST['partition'];
				$mount['fstype'] = $_POST['fstype'];
				$mount['voltype'] = 'hast';
				$mount['gpt'] = ($_POST['partitiontype'] == 'p');
				$mount['rawuuid'] = $rawuuid;
				if($mount['fstype'] == 'ufs'):
					$mount['devicespecialfile'] = $device;
				else:
					$mount['devicespecialfile'] = trim("{$mount['mdisk']}{$mount['partition']}");
				endif;
				$mount['readonly'] = isset($_POST['readonly']);
				$mount['fsck'] = isset($_POST['fsck']);
				break;
			case 'iso':
				$mount['filename'] = $_POST['filename'];
				$mount['fstype'] = util_is_iso_image($_POST['filename']);
				break;
			case 'custom':
				$mount['mdisk'] = $_POST['devname'];
				$mount['partition'] = '';
				$mount['fstype'] = $_POST['fstype'];
				$mount['gpt'] = false;
				$mount['rawuuid'] = '';
				$mount['devicespecialfile'] = trim("{$mount['mdisk']}");
				$mount['readonly'] = isset($_POST['readonly']);
				$mount['fsck'] = isset($_POST['fsck']);
				break;
		endswitch;
		$mount['sharename'] = $_POST['sharename'];
		$mount['desc'] = $_POST['desc'];
		$mount['accessrestrictions']['owner'] = $_POST['owner'];
		$mount['accessrestrictions']['group'] = $_POST['group'];
		$mount['accessrestrictions']['mode'] = getmodectrl($pconfig['mode_owner'],$pconfig['mode_group'],$pconfig['mode_others']);
		if(isset($uuid) && ($cnid !== false)):
			$mode = UPDATENOTIFY_MODE_MODIFIED;
			$a_mount[$cnid] = $mount;
		else:
			$mode = UPDATENOTIFY_MODE_NEW;
			$a_mount[] = $mount;
		endif;
		updatenotify_set('mountpoint',$mode,$mount['uuid']);
		write_config();
		header('Location: disks_mount.php');
		exit;
	endif;
endif;
function initmodectrl(&$pconfig,$mode) {
	$pconfig['mode_owner'] = [];
	$pconfig['mode_group'] = [];
	$pconfig['mode_others'] = [];
//	Convert octal to decimal
	$mode = octdec($mode);
//	Owner
	if($mode & 0x0100):
		$pconfig['mode_owner'][] = 'r';
	endif;
	if($mode & 0x0080):
		$pconfig['mode_owner'][] = 'w';
	endif;
	if($mode & 0x0040):
		$pconfig['mode_owner'][] = 'x';
	endif;
//	Group
	if($mode & 0x0020):
		$pconfig['mode_group'][] = 'r';
	endif;
	if($mode & 0x0010):
		$pconfig['mode_group'][] = 'w';
	endif;
	if($mode & 0x0008):
		$pconfig['mode_group'][] = 'x';
	endif;
//	Others
	if($mode & 0x0004):
		$pconfig['mode_others'][] = 'r';
	endif;
	if($mode & 0x0002):
		$pconfig['mode_others'][] = 'w';
	endif;
	if($mode & 0x0001):
		$pconfig['mode_others'][] = 'x';
	endif;
}
function getmodectrl($owner,$group,$others) {
	$mode = '';
	$legal = ['r','w','x'];
	foreach($legal as $value):
		$mode .= (is_array($owner) && in_array($value,$owner)) ? $value : '-';
	endforeach;
	foreach($legal as $value):
		$mode .= (is_array($group) && in_array($value,$group)) ? $value : '-';
	endforeach;
	foreach($legal as $value):
		$mode .= (is_array($others) && in_array($value,$others)) ? $value : '-';
	endforeach;
    $realmode = '';
    $legal = ['','w','r','x','-'];
    $attarray = preg_split('//',$mode);
    for($i = 0;$i<count($attarray);$i++):
        if($key = array_search($attarray[$i],$legal)):
            $realmode .= $legal[$key];
        endif;
    endfor;
    $mode = str_pad($realmode,9,'-');
    $trans = ['-'=>'0','r'=>'4','w'=>'2','x'=>'1'];
    $mode = strtr($mode,$trans);
    $newmode = '0';
    $newmode .= $mode[0]+$mode[1]+$mode[2];
    $newmode .= $mode[3]+$mode[4]+$mode[5];
    $newmode .= $mode[6]+$mode[7]+$mode[8];
    return $newmode;
}
include 'fbegin.inc';
?>
<script>
//<![CDATA[
function type_change() {
  switch (document.iform.type.selectedIndex) {
    case 0: /* Disk */
      showElementById('mdisk_tr','show');
      showElementById('hvol_tr','hide');
      showElementById('devname_tr','hide');
      showElementById('partitiontype_tr','show');
      showElementById('partitionnum_tr','show');
      showElementById('fstype_tr','show');
      showElementById('filename_tr','hide');
      showElementById('readonly_tr','show');
      showElementById('fsck_tr','show');
      partitiontype_change();
      break;

    case 1: /* HAST volume */
      showElementById('mdisk_tr','hide');
      showElementById('hvol_tr','show');
      showElementById('devname_tr','hide');
      showElementById('partitiontype_tr','show');
      showElementById('partitionnum_tr','show');
      showElementById('fstype_tr','show');
      showElementById('filename_tr','hide');
      showElementById('readonly_tr','show');
      showElementById('fsck_tr','show');
      partitiontype_change();
      break;

    case 2: /* ISO */
      showElementById('mdisk_tr','hide');
      showElementById('hvol_tr','hide');
      showElementById('devname_tr','hide');
      showElementById('partitiontype_tr','hide');
      showElementById('partitionnum_tr','hide');
      showElementById('fstype_tr','hide');
      showElementById('filename_tr','show');
      showElementById('readonly_tr','hide');
      showElementById('fsck_tr','hide');
      break;

    case 3: /* Custom device / label */
      showElementById('mdisk_tr','hide');
      showElementById('hvol_tr','hide');
      showElementById('devname_tr','show');
      showElementById('partitiontype_tr','hide');
      showElementById('partitionnum_tr','hide');
      showElementById('fstype_tr','show');
      showElementById('filename_tr','hide');
      showElementById('readonly_tr','show');
      showElementById('fsck_tr','show');
      //partitiontype_change();
      break;
  }
}

function partitiontype_change() {
	switch (document.iform.partitiontype.selectedIndex) {
		case 0: /* GPT */
		case 1: /* MBR */
<?php if (!isset($uuid)):?>
			document.iform.fsck.checked = true;
<?php endif;?>
			showElementById('partitionnum_tr','show');
			break;

		case 2: /* CD/DVD */
<?php if (!isset($uuid)):?>
			document.iform.fsck.checked = false;
<?php endif;?>
			showElementById('partitionnum_tr','hide');
			break;
	}
}

var first_fstype_changed = 0;
function fstype_change() {
	var sel = document.iform.fstype.selectedIndex;
	switch (document.iform.fstype.selectedIndex) {
	case 5: /* exFAT */
		if (!first_fstype_changed) {
<?php if (!isset($uuid)):?>
			document.iform.partitiontype.value = "s";
<?php endif;?>
			first_fstype_changed = 1;
		}
		break;
	}
}

function enable_change(enable_change) {
	document.iform.type.disabled = !enable_change;
	document.iform.mdisk.disabled = !enable_change;
	document.iform.hvol.disabled = !enable_change;
	document.iform.filename.disabled = !enable_change;
}
//]]>
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_mount.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Management');?></span></a></li>
				<li class="tabinact"><a href="disks_mount_tools.php"><span><?=gtext('Tools');?></span></a></li>
				<li class="tabinact"><a href="disks_mount_fsck.php"><span><?=gtext('Fsck');?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_mount_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
<?php
				if(!empty($input_errors)):
					print_input_errors($input_errors);
				endif;
?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
<?php
					html_titleline2(gettext('Settings'));
					html_combobox2('type',gettext('Type'),$pconfig['type'],['disk' => gettext('Disk'),'hvol' => gettext('HAST volume'),'iso' => 'ISO','custom' => gettext('Custom device')],'',true,false,'type_change()');
?>
					<tr id="mdisk_tr">
						<td width="22%" valign="top" class="vncellreq"><?=gtext('Disk');?></td>
						<td class="vtable">
							<select name="mdisk" class="formfld" id="mdisk">
								<option value=""><?=gtext('Must choose one');?></option>
<?php
								foreach($a_disk as $diskv):
									if($diskv['type'] == 'HAST'):
										continue;
									endif;
?>
									<option value="<?=$diskv['devicespecialfile'];?>"<?php if($pconfig['mdisk'] === $diskv['devicespecialfile']) echo ' selected="selected"';?>>
<?php
										$diskinfo = disks_get_diskinfo($diskv['devicespecialfile']);
										$helpinghand = format_bytes($diskinfo['mediasize_bytes'],2,true,is_sidisksizevalues());
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
					html_combobox2('hvol',gettext('HAST volume'),$pconfig['hvol'] ?? '',$a_hast,'',true);
					html_inputbox2('devname',gettext('Device / Label'),!empty($pconfig['devname']) ? $pconfig['devname'] : '',gettext('You may enter a device file or label path.'),true,60);
?>
					<tr id="partitiontype_tr">
						<td width="22%" valign="top" class="vncellreq"><?=gtext('Partition type');?></td>
						<td class="vtable">
							<select name="partitiontype" class="formfld" id="partitiontype" onclick="partitiontype_change()">
								<option value="p"<?php if($pconfig['partitiontype'] === 'p') echo ' selected="selected"';?>><?=gtext('GPT partition');?></option>
								<option value="s"<?php if($pconfig['partitiontype'] === 's') echo ' selected="selected"';?>><?=gtext('MBR partition');?></option>
								<option value=" "<?php if(empty($pconfig['partitiontype'])) echo ' selected="selected"';?>><?=gtext('CD/DVD');?></option>
							</select><br />
<?php
							$helpinghand = gtext("Select 'GPT partition' if you want to mount a GPT formatted drive") . '<br />'
								. gtext("Select 'MBR partition' default partition if you want to mount a UFS formatted drive or if you want to import disks from other OS.") . '<br />'
								. gtext("Select 'CD/DVD' if you want to mount a CD/DVD volume.");
?>
							<span class="vexpl"><?=$helpinghand;?></span>
						</td>
					</tr>
<?php
					html_inputbox2('partitionnum',gettext('Partition number'),$pconfig['partitionnum'],'',true,3);
					html_combobox2('fstype',gettext('File system'),!empty($pconfig['fstype']) ? $pconfig['fstype'] : '',['ufs' => 'UFS','msdosfs' => 'FAT','cd9660' => 'CD/DVD','ntfs' => 'NTFS','ext2fs' => 'EXT2/3','fusefs-ext2' => 'EXT4','exfat' => 'exFAT'],'',true,false,'fstype_change()');
					html_filechooser2('filename','Filename',!empty($pconfig['filename']) ? $pconfig['filename'] : '',gettext('ISO file to be mounted.'),$g['media_path'],true);
					html_inputbox2('sharename',gettext('Mount point name'),!empty($pconfig['sharename']) ? $pconfig['sharename'] : '','',true,20);
					html_inputbox2('desc',gettext('Description'),!empty($pconfig['desc']) ? $pconfig['desc'] : '',gettext('You may enter a description here for your reference.'),false,40);
					html_checkbox2('readonly',gettext('Read only'),!empty($pconfig['readonly']),gettext('Mount the file system read-only (even the super-user may not write it).'),'',false);
					html_checkbox2('fsck',gettext('File system check'),!empty($pconfig['fsck']),gettext('Enable foreground/background file system consistency check during boot process.'),'',false);
					html_separator2();
					html_titleline2(gettext('Access Restrictions'));
					$a_owner = [];
					foreach(system_get_user_list() as $userk => $userv):
						$a_owner[$userk] = $userk;
					endforeach;
					html_combobox2('owner',gettext('Owner'),$pconfig['owner'],$a_owner,'',false);
					$a_group = [];
					foreach(system_get_group_list() as $groupk => $groupv):
						$a_group[$groupk] = $groupk;
					endforeach;
					html_combobox2('group',gettext('Group'),$pconfig['group'],$a_group,'',false);
?>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gtext('Mode');?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="20%" class="listhdrlr">&nbsp;</td>
									<td width="20%" class="listhdrc"><?=gtext('Read');?></td>
									<td width="50%" class="listhdrc"><?=gtext('Write');?></td>
									<td width="20%" class="listhdrc"><?=gtext('Execute');?></td>
									<td width="10%" class="list"></td>
								</tr>
								<tr>
									<td class="listlr"><?=gtext('Owner');?>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_owner[]" id="owner_read" value="r"<?php if(in_array('r',$pconfig['mode_owner'])) echo ' checked="checked"';?> />&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_owner[]" id="owner_write" value="w"<?php if(in_array('w',$pconfig['mode_owner'])) echo ' checked="checked"';?> />&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_owner[]" id="owner_execute" value="x"<?php if(in_array('x',$pconfig['mode_owner'])) echo ' checked="checked"';?> />&nbsp;</td>
								</tr>
								<tr>
									<td class="listlr"><?=gtext('Group');?>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_group[]" id="group_read" value="r"<?php if(in_array('r',$pconfig['mode_group'])) echo ' checked="checked"';?> />&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_group[]" id="group_write" value="w"<?php if(in_array('w',$pconfig['mode_group'])) echo ' checked="checked"';?> />&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_group[]" id="group_execute" value="x"<?php if(in_array('x',$pconfig['mode_group'])) echo ' checked="checked"';?> />&nbsp;</td>
								</tr>
								<tr>
									<td class="listlr"><?=gtext("Others");?>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_others[]" id="others_read" value="r"<?php if(in_array('r',$pconfig['mode_others'])) echo ' checked="checked"';?> />&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_others[]" id="others_write" value="w"<?php if(in_array('w',$pconfig['mode_others'])) echo ' checked="checked"';?> />&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_others[]" id="others_execute" value="x"<?php if(in_array('x',$pconfig['mode_others'])) echo ' checked="checked"';?> />&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && ($cnid !== false)) ? gtext('Save') : gtext('Add')?>" onclick="enable_change(true)" />
					<input name="Cancel" type="submit" class="formbtn" value="<?=gtext('Cancel');?>" />
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
				</div>
				<div id="remarks">
<?php
					$helpinghand = sprintf(gettext("You can not mount partition '%s' where the config file is stored."),$cfdevice)
						. '<br />'
						. sprintf(gettext('UFS and variants are the NATIVE file format for FreeBSD (the underlying OS of %s).'),get_product_name())
						. ' '
						. gettext('Attempting to use other file formats such as FAT, FAT32, EXT2, EXT3, EXT4 or NTFS can result in unpredictable results, file corruption and the loss of data!');
					html_remark2('warning',gettext('Warning'),$helpinghand);
?>
				</div>
<?php
				include 'formend.inc';
?>
			</form>
	</td></tr>
</table>
<script>
//<![CDATA[
type_change();
<?php
if(isset($uuid) && ($cnid !== false)):
//	Disable controls that should not be modified anymore in edit mode.
?>
enable_change(false);
<?php
endif;
?>
//]]>
</script>
<?php
include 'fend.inc';
