<?php
/*
	disks_init.php

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

$sphere_scriptname = basename(__FILE__);
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_array = [];
$sphere_record = [];
$checkbox_member_name = 'checkbox_member_array';
$checkbox_member_array = [];
$checkbox_member_record = [];
$gt_record_loc = gtext('Record is locked');
$img_path = [
	'add' => 'images/add.png',
	'mod' => 'images/edit.png',
	'del' => 'images/delete.png',
	'loc' => 'images/locked.png',
	'unl' => 'images/unlocked.png',
	'mai' => 'images/maintain.png',
	'inf' => 'images/info.png',
	'ena' => 'images/status_enabled.png',
	'dis' => 'images/status_disabled.png',
	'mup' => 'images/up.png',
	'mdn' => 'images/down.png'
];
$prerequisites_ok = true;

function verify_filesystem_name($arg) {
	$returnvalue = false;
//	verify filesystem name
	switch($arg):
		default:
//			invalid parameter value
			break;
		case 'zfs':
		case 'softraid':
		case 'ufsgpt':
//		case 'ext2':
		case 'msdos':
			$returnvalue = true;
			break;
	endswitch;
	return $returnvalue;
}
$do_format = [];
$a_control_matrix = [
	1 => [
		'zfs'      => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0],
		'softraid' => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0],
		'ufsgpt'   => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0],
		'ext2'     => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0],
		'msdos'    => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0],
		'default'  => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0],
	],
	2 => [
		'zfs'      => ['page' => 2,'filesystem' => 1,'minspace' => 0,'volumelabel' => 2,'aft4k' => 0,'zfsgpt' => 2,'notinitmbr' => 2],
		'softraid' => ['page' => 2,'filesystem' => 1,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 2],
		'ufsgpt'   => ['page' => 2,'filesystem' => 1,'minspace' => 2,'volumelabel' => 2,'aft4k' => 2,'zfsgpt' => 0,'notinitmbr' => 2],
		'ext2'     => ['page' => 2,'filesystem' => 1,'minspace' => 0,'volumelabel' => 2,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 2],
		'msdos'    => ['page' => 2,'filesystem' => 1,'minspace' => 0,'volumelabel' => 2,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 2],
		'default'  => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0]
	],
	3 => [
		'zfs'      => ['page' => 3,'filesystem' => 1,'minspace' => 0,'volumelabel' => 1,'aft4k' => 0,'zfsgpt' => 1,'notinitmbr' => 1],
		'softraid' => ['page' => 3,'filesystem' => 1,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 1],
		'ufsgpt'   => ['page' => 3,'filesystem' => 1,'minspace' => 1,'volumelabel' => 1,'aft4k' => 1,'zfsgpt' => 0,'notinitmbr' => 1],
		'ext2'     => ['page' => 3,'filesystem' => 1,'minspace' => 0,'volumelabel' => 1,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 1],
		'msdos'    => ['page' => 3,'filesystem' => 1,'minspace' => 0,'volumelabel' => 1,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 1],
		'default'  => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0]
	],
	4 => [
		'zfs'      => ['page' => 4,'filesystem' => 1,'minspace' => 0,'volumelabel' => 1,'aft4k' => 0,'zfsgpt' => 1,'notinitmbr' => 1],
		'softraid' => ['page' => 4,'filesystem' => 1,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 1],
		'ufsgpt'   => ['page' => 4,'filesystem' => 1,'minspace' => 1,'volumelabel' => 1,'aft4k' => 1,'zfsgpt' => 0,'notinitmbr' => 1],
		'ext2'     => ['page' => 4,'filesystem' => 1,'minspace' => 0,'volumelabel' => 1,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 1],
		'msdos'    => ['page' => 4,'filesystem' => 1,'minspace' => 0,'volumelabel' => 1,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 1],
		'default'  => ['page' => 1,'filesystem' => 2,'minspace' => 0,'volumelabel' => 0,'aft4k' => 0,'zfsgpt' => 0,'notinitmbr' => 0]
	]
];
$a_button_matrix = [
	1 => ['submit_value' => gtext('Next'  ),'submit_name' => 'action1','submit_control' => 2,'cancel_value' => gtext('Cancel'),'cancel_name' => 'cancel1','cancel_control' => 0,'checkbox_control' => 2],
	2 => ['submit_value' => gtext('Next'  ),'submit_name' => 'action2','submit_control' => 2,'cancel_value' => gtext('Back'  ),'cancel_name' => 'cancel2','cancel_control' => 2,'checkbox_control' => 2],
	3 => ['submit_value' => gtext('Format'),'submit_name' => 'action3','submit_control' => 2,'cancel_value' => gtext('Back'  ),'cancel_name' => 'cancel3','cancel_control' => 2,'checkbox_control' => 1],
	4 => ['submit_value' => gtext('OK'    ),'submit_name' => 'action4','submit_control' => 2,'cancel_value' => gtext('Back'  ),'cancel_name' => 'cancel4','cancel_control' => 0,'checkbox_control' => 1]
];
$l_filesystem = [
	'ufsgpt' => gtext('UFS (GPT and Soft Updates)'),
	'msdos' => gtext('FAT32'),
//	'ext2' => gtext('EXT2'),
	'softraid' => gtext('Software RAID'),
	'zfs' => gtext('ZFS Storage Pool')
];
$l_minspace = [
	'8' => '8%',
	'7' => '7%',
	'6' => '6%',
	'5' => '5%',
	'4' => '4%',
	'3' => '3%',
	'2' => '2%',
	'1' => '1%'
];
$a_option = (isset($_POST) && is_array($_POST)) ? $_POST : [];
if(isset($a_option['filesystem'])):
//	$a_option['filesystem'] = array_key_exists($a_option['filesystem'],$l_filesystem) ? $a_option['filesystem'] : 'zfs';
else:
	$a_option['filesystem'] = 'zfs';
endif;
if(isset($a_option['checkbox_member_array']) && is_array($a_option['checkbox_member_array'])):
else:
	$a_option['checkbox_member_array'] = [];
endif;
if(isset($a_option['volumelabel']) && preg_match('/\S/',$a_option['volumelabel'])):
	$a_option['volumelabel'] = htmlspecialchars(trim($a_option['volumelabel']));
else:
	$a_option['volumelabel'] = '';
endif;
if(isset($a_option['minspace']) && array_key_exists($a_option['minspace'],$l_minspace)):
else:
	$a_option['minspace'] = '8';
endif;
$a_option['aft4k'] = isset($a_option['aft4k']);
$a_option['zfsgpt'] = isset($a_option['zfsgpt']);
$a_option['notinitmbr'] = isset($a_option['notinitmbr']);

// Get OS partition
$bootdevice = trim(file_get_contents("{$g['etc_path']}/cfdevice"));
// Get list of all configured disks (physical and virtual).
$sphere_array = get_conf_all_disks_list_filtered();
// Protect devices which are invalid or in use
foreach($sphere_array as &$sphere_record):
	if(strcmp($sphere_record['size'],'NA') === 0):
		$sphere_record['protected'] = true;
		$sphere_record['protected.reason'] = gtext('Unknown size');
	elseif(disks_exists($sphere_record['devicespecialfile']) === 1):
		$sphere_record['protected'] = true;
		$sphere_record['protected.reason'] = gtext('Device not found');
	elseif(disks_ismounted_ex($sphere_record['devicespecialfile'],"devicespecialfile")):
		$sphere_record['protected'] = true;
		$sphere_record['protected.reason'] = gtext('Device is mounted');
	elseif(preg_match('~\A' . preg_quote($sphere_record['name'],'~') . '(\D+|\z)~',$bootdevice) === 1):
		$sphere_record['protected'] = true;
		$sphere_record['protected.reason'] = gtext('Device contains boot partition');
	else:
		$sphere_record['protected'] = false;
		$sphere_record['protected.reason'] = '';
	endif;

	// Protect devices which are zroot members.
	// This will protect only the member disks present in the cfdevice file.
	// This can be accomplished with various methods but we will use `gpart` to simply match the disk label/id.
	if($g['zroot']):
		$disk_device = $sphere_record['name'];
		$bootdevice_array = array($bootdevice);
		foreach($bootdevice_array as &$bootdevice_record):
			$bootdevice_key = preg_replace('~\/dev\/gpt\/|\/dev\/~', '', $bootdevice_record);
			$disk_locked = exec("/sbin/gpart show -lp $disk_device | /usr/bin/grep -wo '$bootdevice_key'");
		if($disk_locked):
			$sphere_record['protected'] = true;
			$sphere_record['protected.reason'] = gtext("Operating system disk ($disk_locked)");
		endif;
		endforeach;
	endif;
endforeach;
unset($sphere_record); // release pass by reference

// cleanup checkbox_member_array
// Remove checkbox_member_array records which are protected in $sphere_array
// Set enabled property in $sphere_array for those who can be selected
$a_member_update = [];
foreach($a_option['checkbox_member_array'] as $checkbox_member_record):
	$index = arr::search_ex($checkbox_member_record,$sphere_array,'uuid');
	if($index !== false):
		if(!$sphere_array[$index]['protected']):
			$sphere_array[$index]['enabled'] = true;
			$a_member_update[] = $checkbox_member_record;
		endif;
	endif;
endforeach;
$a_option['checkbox_member_array'] = $a_member_update;

$page_index = 1;
$a_control = $a_control_matrix[$page_index]['default'];
$a_button = $a_button_matrix[$page_index];

if(isset($a_option['cancel1']) && $a_option['cancel1']):
	// cancel button has been pressed on page 1, we want to stay on page 1
elseif(isset($a_option['cancel2']) && $a_option['cancel2']):
	// back button has been pressed on page 2, return to page 1
elseif(isset($a_option['cancel3']) && $a_option['cancel3']):
	// back button has been pressed on page 3, return to page 2
	if($prerequisites_ok):
		$page_index = 2;
		$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
		$a_button = $a_button_matrix[$page_index];
	endif;
elseif(isset($a_option['cancel4']) && $a_option['cancel4']):
	// back button has been pressed on page 4, return to page 1
elseif(isset($a_option['action1']) && $a_option['action1']):
	// next button has been pressed on page 1, we want to display page 2
	// expectation: filesystem has been chosen.
	if($prerequisites_ok): // verify filesystem type
		$prerequisites_ok = (isset($a_option['filesystem']) && verify_filesystem_name($a_option['filesystem']));
		// filesystem type could be invalid, we need to return to page 1 to be able to select a valid filesystem. Nothing to do here because page 1 is set by default
	endif;
	if($prerequisites_ok):
		$page_index = 2;
		$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
		$a_button = $a_button_matrix[$page_index];
	endif;
elseif(isset($a_option['action2']) && $a_option['action2']):
	// next button has been pressed on page 2, we want to display page 3
	// expectation: filesystem has been chosen, disks have been selected.
	if($prerequisites_ok):  // verify filesystem type
		$prerequisites_ok = (isset($a_option['filesystem']) && verify_filesystem_name(htmlspecialchars($a_option['filesystem'])));
		// filesystem type could be invalid, we need to return to page 1 to be able to select a valid filesystem. Nothing to do here because page 1 is set by default
	endif;
	if($prerequisites_ok): // verify selected disks
		$prerequisites_ok = (isset($a_option['checkbox_member_array']) && is_array($a_option['checkbox_member_array']) && (count($a_option['checkbox_member_array']) > 0));
		if($prerequisites_ok === false):
//			no disks selected, we stay on page 2
			$page_index = 2;
			$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
			$a_button = $a_button_matrix[$page_index];
		endif;
	endif;
	if($prerequisites_ok):
		if(preg_match('/^(ufsgpt|msdos)/',$a_option['filesystem']) && preg_match('/\S/',$a_option['volumelabel'])):
			$helpinghand = preg_quote('[%', '/');
			if(preg_match('/^[a-z\d' . $helpinghand . ']+$/i',$a_option['volumelabel'])):
//				additional check is required for adding serial number information to the label
				$label_serial = [];
				$label_serial['trigger'] = '[';
				$label_serial['match'] = '([1-9]\d?)';
				$label_serial['regex'] = '/' . preg_quote($label_serial['trigger']) . $label_serial['match'] . '/';
				$label_serial['count'] = substr_count($a_option['volumelabel'],$label_serial['trigger']); // count occurrences of the initiating character
				if($label_serial['count'] > 0): // one or more occurrences found?
					if($label_serial['count'] !== preg_match_all($label_serial['regex'],$a_option['volumelabel'])): // count must match, otherwise something went wrong
						$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z] and [0-9]."),gtext('Volume Label'));
						$prerequisites_ok = false;
						// invalid volume label pattern, we stay on page 2
						$page_index = 2;
						$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
						$a_button = $a_button_matrix[$page_index];
					endif;
				endif;
			else: // invalid volume label pattern, we stay on page 2
				$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z] and [0-9]."),gtext('Volume Label'));
				$prerequisites_ok = false;
				$page_index = 2;
				$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
				$a_button = $a_button_matrix[$page_index];
			endif;
		endif;
	endif;
	if($prerequisites_ok):
		if(preg_match('/^(zfs)/',$a_option['filesystem']) && preg_match('/\S/',$a_option['volumelabel'])):
			$helpinghand = preg_quote('[%.-_','/');
			if(preg_match('/^[a-z\d' . $helpinghand . ']+$/i',$a_option['volumelabel'])):
				// additional check is required for adding serial number information to the label
				$label_serial = [];
				$label_serial['trigger'] = '[';
				$label_serial['match'] = '([1-9]\d?)';
				$label_serial['regex'] = '/' . preg_quote($label_serial['trigger']) . $label_serial['match'] . '/';
				$label_serial['count'] = substr_count($a_option['volumelabel'],$label_serial['trigger']); // count occurrences of the initiating character
				if($label_serial['count'] > 0): // one or more occurrences found?
					if($label_serial['count'] !== preg_match_all($label_serial['regex'],$a_option['volumelabel'])): // count must match, otherwise something went wrong
						$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z], [0-9] and [._-]."),gtext('Volume Label'));
						$prerequisites_ok = false;
						// invalid volume label defined, we stay on page 2
						$page_index = 2;
						$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
						$a_button = $a_button_matrix[$page_index];
					endif;
				endif;
			else: // invalid volume label pattern, we stay on page 2
				$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z], [0-9] and [._-]."),gtext('Volume Label'));
				$prerequisites_ok = false;
				$page_index = 2;
				$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
				$a_button = $a_button_matrix[$page_index];
			endif;
		endif;
	endif;
	if($prerequisites_ok):
		$page_index = 3;
		$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
		$a_button = $a_button_matrix[$page_index];
	endif;
elseif(isset($a_option['action3']) && $a_option['action3']):
	// format button has been pressed on page 3, we want to format
	// expectation: filesystem has been chosen, disks have been selected, options have been set.
	if($prerequisites_ok): // verify filesystem type
		$prerequisites_ok = (isset($a_option['filesystem']) && verify_filesystem_name($a_option['filesystem']));
		// filesystem type could be invalid, we need to return to page 1 to be able to select a valid filesystem. Nothing to do here because page 1 is set by default
	endif;
	if($prerequisites_ok): // verify selected disks
		$prerequisites_ok = (isset($a_option['checkbox_member_array']) && is_array($a_option['checkbox_member_array']) && (count($a_option['checkbox_member_array']) > 0));
		if($prerequisites_ok === false):
			// no disks selected, we need to return to page 2 to be able to select disks
			$page_index = 2;
			$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
			$a_button = $a_button_matrix[$page_index];
		endif;
	endif;
	if($prerequisites_ok):
		if(preg_match('/^(ufsgpt|msdos)/',$a_option['filesystem']) && preg_match('/\S/',$a_option['volumelabel'])):
			$helpinghand = preg_quote('[%','/');
			if(preg_match('/^[a-z\d' . $helpinghand . ']+$/i',$a_option['volumelabel'])):
				// additional check is required for adding serial number information to the label
				$label_serial = [];
				$label_serial['trigger'] = '[';
				$label_serial['match'] = '([1-9]\d?)';
				$label_serial['regex'] = '/' . preg_quote($label_serial['trigger']) . $label_serial['match'] . '/';
				$label_serial['count'] = substr_count($a_option['volumelabel'],$label_serial['trigger']); // count occurrences of the initiating character
				if($label_serial['count'] > 0): // one or more occurrences found?
					if($label_serial['count'] !== preg_match_all($label_serial['regex'],$a_option['volumelabel'])): // count must match, otherwise something went wrong
						$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z] and [0-9]."),gtext('Volume Label'));
						$prerequisites_ok = false;
						// invalid volume label pattern, we stay on page 2
						$page_index = 2;
						$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
						$a_button = $a_button_matrix[$page_index];
					endif;
				endif;
			else: // invalid volume label defined, we stay on page 3
				$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z] and [0-9]."),gtext('Volume Label'));
				$prerequisites_ok = false;
				$page_index = 3;
				$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
				$a_button = $a_button_matrix[$page_index];
			endif;
		endif;
	endif;
	if($prerequisites_ok):
		if(preg_match('/^(zfs)/',$a_option['filesystem']) && preg_match('/\S/',$a_option['volumelabel'])):
			$helpinghand = preg_quote('[%.-_','/');
			if(preg_match('/^[a-z\d' . $helpinghand . ']+$/i',$a_option['volumelabel'])):
				// additional check is required when adding serial number information to the label
				$label_serial = [];
				$label_serial['trigger'] = '[';
				$label_serial['match'] = '([1-9]\d?)';
				$label_serial['regex'] = '/' . preg_quote($label_serial['trigger']) . $label_serial['match'] . '/';
				$label_serial['count'] = substr_count($a_option['volumelabel'],$label_serial['trigger']); // count occurrences of the initiating character
				if($label_serial['count'] > 0): // one or more occurrences found?
					if($label_serial['count'] !== preg_match_all($label_serial['regex'],$a_option['volumelabel'])): // count must match, otherwise something went wrong
						$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z], [0-9] and [._-]."),gtext('Volume Label'));
						$prerequisites_ok = false;
						// invalid volume label pattern, we stay on page 2
						$page_index = 2;
						$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
						$a_button = $a_button_matrix[$page_index];
					endif;
				endif;
			else: // invalid volume label defined, we stay on page 2
				$input_errors[] = sprintf(gtext("The attribute '%s' may only consist of the characters [a-z], [A-Z], [0-9] and [._-]."),gtext('Volume Label'));
				$prerequisites_ok = false;
				$page_index = 2;
				$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
				$a_button = $a_button_matrix[$page_index];
			endif;
		endif;
	endif;
	if($prerequisites_ok):
		$page_index = 4 ;
		$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
		$a_button = $a_button_matrix[$page_index];
		// gather options and format selected disks
		$disk_options = [];
		$disk_options['zfsgpt'] = $a_option['zfsgpt'] ? 'p1' : ''; // set_conf_disk_fstype_opt knows how to deal with it if filesystem is not zfs
		// check for allowed characters, otherwise reset volumelabel
		$volumelabel_pattern = (preg_match('/(ufsgpt|msdos|zfs)/',$a_option['filesystem'])) ? $a_option['volumelabel'] : '';
		// check if counters are part of the volume label
		$label_counter = [];
		if(preg_match('/\S/',$volumelabel_pattern)): // do we have a volumelabel pattern?
			$label_counter['trigger'] = '%';
			$label_counter['match'] = '(\d*)';
			$label_counter['regex'] = '/' . preg_quote($label_counter['trigger']) . $label_counter['match'] . '/';
			$label_counter['count'] = substr_count($volumelabel_pattern,$label_counter['trigger']); // count occurrences of the initiating character
			if($label_counter['count'] > 0): // one or more occurrences found?
				if($label_counter['count'] === preg_match_all($label_counter['regex'],$volumelabel_pattern,$helpinghand)): // count must match, otherwise something went wrong
					$label_counter['needle'] = $helpinghand[0];
					$label_counter['origin'] = $helpinghand[1];
					$label_counter['replacement'] = [];
					$label_counter['pattern'] = [];
					for($i = 0; $i < $label_counter['count']; $i++):
						$label_counter['pattern'][$i] = '/' . preg_quote($label_counter['needle'][$i],'/') . '/'; // make regex pattern
						if(empty($label_counter['origin'][$i])):  // using empty is ok
							$label_counter['replacement'][$i] = 0; // value of replacement if origin is empty
						else:
							$label_counter['replacement'][$i] = $label_counter['origin'][$i]; // value of replacement if origin is not empty (starting number)
						endif;
					endfor;
				else:
					$label_counter = [];
					$volumelabel_pattern = '';
				endif;
				unset($helpinghand);
			else:
				$label_counter = [];
			endif;
		endif;
		// check if the drive's serial number is part of the volume label
		$label_serial = [];
		if(preg_match('/\S/',$volumelabel_pattern)): // do we have a volumelabel pattern?
			$label_serial['trigger'] = '[';
			$label_serial['match'] = '([1-9]\d?)';
			$label_serial['regex'] = '/' . preg_quote($label_serial['trigger']) . $label_serial['match'] . '/';
			$label_serial['count'] = substr_count($volumelabel_pattern,$label_serial['trigger']); // count occurrences of the initiating character
			if($label_serial['count'] > 0): // one or more occurrences found?
				if($label_serial['count'] === preg_match_all($label_serial['regex'],$volumelabel_pattern,$helpinghand)): // count must match, otherwise something went wrong
					$label_serial['needle'] = $helpinghand[0];
					$label_serial['origin'] = $helpinghand[1];
					$label_serial['replacement'] = [];
					$label_serial['pattern'] = [];
					for($i = 0; $i < $label_serial['count']; $i++):
						$label_serial['pattern'][$i] = '/' . preg_quote($label_serial['needle'][$i],'/') . '/'; // make regex pattern
						if(empty($label_serial['origin'][$i])):  // using empty is ok
							$label_serial['replacement'][$i] = ''; // value of replacement if origin is empty
						else:
							$label_serial['replacement'][$i] = ''; // value of replacement if origin is not empty
						endif;
					endfor;
				else:
					$label_serial = [];
					$volumelabel_pattern = '';
				endif;
				unset($helpinghand);
			else:
				$label_serial = [];
			endif;
		endif;
		foreach($a_option['checkbox_member_array'] as $checkbox_member_record):
			$index = arr::search_ex($checkbox_member_record,$sphere_array,'uuid');
			if($index !== false):
				if(!$sphere_array[$index]['protected']):
					set_conf_disk_fstype_opt($sphere_array[$index]['devicespecialfile'],$a_option['filesystem'],$disk_options);
					$volumelabel = $volumelabel_pattern;
					// apply counter to label
					if(!empty($label_counter)):
						$volumelabel = preg_replace($label_counter['pattern'],$label_counter['replacement'],$volumelabel,1);
						// increase counter;
						for($i = 0; $i < $label_counter['count']; $i++):
							$label_counter['replacement'][$i]++;
						endfor;
					endif;
					// apply serial number to label
					if(!empty($label_serial)):
						for($i = 0; $i < $label_serial['count']; $i++):
							$label_serial['replacement'][$i] = substr($sphere_array[$index]['serial'],-$label_serial['origin'][$i],$label_serial['origin'][$i]);
							if($label_serial['replacement'][$i] === false):
								$label_serial['replacement'][$i] = '';
							endif;
						endfor;
						$volumelabel = preg_replace($label_serial['pattern'],$label_serial['replacement'],$volumelabel,1);
					endif;
					// prepare format
					$do_format[] = [
						'devicespecialfile' => $sphere_array[$index]['devicespecialfile'],
						'filesystem' => $a_option['filesystem'],
						'notinitmbr' => $a_option['notinitmbr'],
						'minspace' => $a_option['minspace'],
						'volumelabel' => $volumelabel,
						'aft4k' => $a_option['aft4k'],
						'zfsgpt' => $a_option['zfsgpt']
					];
				endif;
			endif;
		endforeach;
		write_config();
	endif;
elseif(isset($a_option['action4']) && $a_option['action4']):
//	$page_index = 1;
//	$a_control = $a_control_matrix[$page_index][$a_option['filesystem']];
//	$a_button = $a_button_matrix[$page_index];
endif;
$pgtitle = [gtext('Disks'),gtext('Management'),gtext('HDD Format'),sprintf('%1$s %2$d',gtext('Step'),$page_index)];
include 'fbegin.inc';
?>
<script>
//<![CDATA[
$(window).on("load", function() {
	// Init toggle checkbox
	$("#togglemembers").click(function() { togglecheckboxesbyname(this, "<?=$checkbox_member_name;?>[]"); });
	// Init spinner onsubmit()
	$("#iform").submit(function() { spinner(); });
});
function togglecheckboxesbyname(ego, triggerbyname) {
	var a_trigger = document.getElementsByName(triggerbyname);
	var n_trigger = a_trigger.length;
	var i = 0;
	for (; i < n_trigger; i++) {
		if (a_trigger[i].type == 'checkbox') {
			if (!a_trigger[i].disabled) {
				a_trigger[i].checked = !a_trigger[i].checked;
			}
		}
	}
	if (ego.type == 'checkbox') { ego.checked = false; }
}
//]]>
</script>
<table id="area_navigator"><tbody>
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabinact"><a href="disks_manage.php"><span><?=gtext('HDD Management');?></span></a></li>
		<li class="tabact"><a href="<?=$sphere_scriptname;?>" title="<?=gtext('Reload page');?>"><span><?=gtext('HDD Format');?></span></a></li>
		<li class="tabinact"><a href="disks_manage_smart.php"><span><?=gtext('S.M.A.R.T.');?></span></a></li>
		<li class="tabinact"><a href="disks_manage_iscsi.php"><span><?=gtext('iSCSI Initiator');?></span></a></li>
	</ul></td></tr>
</tbody></table>
<form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform" class="pagecontent"><div class="area_data_top"></div><div id="area_data_frame">
<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
	if(!empty($errormsg)):
		print_error_box($errormsg);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext('Format Options'));
?>
		</thead>
		<tbody>
<?php
			switch($a_control['filesystem']):
				case 2:
					html_combobox2('filesystem',gettext('File System'),$a_option['filesystem'],$l_filesystem,gettext('Select file system format.'),true,false);
					break;
				case 1:
					html_combobox2('filesystem',gettext('File System'),$a_option['filesystem'],$l_filesystem,'',false,true);
				case 0:
					echo '<tr><td style="padding:0;"></td><td style="padding:0;"><input name="filesystem" type="hidden" value="',$a_option['filesystem'],'"/></td></tr>',"\n";
					break;
			endswitch;
			switch($a_control['volumelabel']):
				case 2:
					html_inputbox2('volumelabel',gettext('Volume Label'),$a_option['volumelabel'],gettext('Volume label of the new file system. Use % for a counter or %n for a counter starting at number n, Use [n for the rightmost n characters of the device serial number.'),false,40,false);
					break;
				case 1:
					html_inputbox2('volumelabel',gettext('Volume Label'),$a_option['volumelabel'],'',false,100,true);
					break;
				case 0:
					echo '<tr><td style="padding:0;"></td><td style="padding:0;"><input name="volumelabel" type="hidden" value="',$a_option['volumelabel'],'"/></td></tr>',"\n";
					break;
			endswitch;
			switch($a_control['minspace']):
				case 2:
					html_combobox2('minspace',gettext('Minimum Free Space'),$a_option['minspace'],$l_minspace,gettext('Specifiy the percentage of disk space to be held back from normal usage. Lowering this threshold can adversely affect performance and auto-defragmentation!'),true,false);
					break;
				case 1:
					html_combobox2('minspace',gettext('Minimum Free Space'),$a_option['minspace'],$l_minspace,'',false,true);
				case 0:
					echo '<tr><td style="padding:0;"></td><td style="padding:0;"><input name="minspace" type="hidden" value="',$a_option['minspace'],'"/></td></tr>',"\n";
					break;
			endswitch;
			switch($a_control['aft4k']):
				case 2:
					html_checkbox2('aft4k',gettext('Advanced Format'),$a_option['aft4k'],gettext('Enable Advanced Format (4KB Sector Size).'),'',false,false);
					break;
				case 1:
					html_checkbox2('aft4k',gettext('Advanced Format'),$a_option['aft4k'],gettext('Enable Advanced Format (4KB Sector Size).'),'',false,true);
				case 0:
					if($a_option['aft4k'] === true):
						echo '<tr><td style="padding:0;"></td><td style="padding:0;"><input name="aft4k" type="hidden" value="yes"/></td></tr>',"\n";
					endif;
					break;
			endswitch;
			switch($a_control['zfsgpt']):
				case 2:
					html_checkbox2('zfsgpt',gettext('GPT Partition'),$a_option['zfsgpt'],gettext('Create ZFS on a GPT partition.'),'',false,false);
					break;
				case 1:
					html_checkbox2('zfsgpt',gettext('GPT Partition'),$a_option['zfsgpt'],gettext('Create ZFS on a GPT partition.'),'',false,true);
				case 0:
					if($a_option['zfsgpt'] === true):
						echo '<tr><td style="padding:0;"></td><td style="padding:0;"><input name="zfsgpt" type="hidden" value="yes"/></td></tr>',"\n";
					endif;
					break;
			endswitch;
			switch($a_control['notinitmbr']):
				case 2:
					html_checkbox2('notinitmbr',gettext('Keep MBR'),$a_option['notinitmbr'],gettext('Do not erase the Master Boot Record (useful for some RAID controller cards).'),'',false,false);
					break;
				case 1:
					html_checkbox2('notinitmbr',gettext('Keep MBR'),$a_option['notinitmbr'],gettext('Do not erase the Master Boot Record (useful for some RAID controller cards).'),'',false,true);
				case 0:
					if($a_option['notinitmbr'] === true):
						echo '<tr><td style="padding:0;"></td><td style="padding:0;"><input name="notinitmbr" type="hidden" value="yes"/></td></tr>',"\n";
					endif;
					break;
			endswitch;
?>
		</tbody>
		<tfoot>
<?php
			html_separator2();
?>
		</tfoot>
	</table>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:5%">
			<col style="width:10%">
			<col style="width:15%">
			<col style="width:10%">
			<col style="width:15%">
			<col style="width:15%">
			<col style="width:20%">
			<col style="width:10%">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext('Disk Selection'),8);
?>
			<tr>
<?php
				switch($a_button['checkbox_control']):
					case 2:
						echo '<th class="lhelc"><input type="checkbox" id="togglemembers" name="togglemembers" title="',gtext('Invert Selection'),'"/></th>',"\n";
						break;
					case 1:
						echo '<th class="lhelc"><input type="checkbox" id="togglemembers" name="togglemembers" title="',gtext('Invert Selection'),'" disabled="disabled"/></th>',"\n";
						break;
				endswitch;
?>
				<th class="lhell"><?=gtext('Device');?></th>
				<th class="lhell"><?=gtext('Serial Number');?></th>
				<th class="lhell"><?=gtext('Size');?></th>
				<th class="lhell"><?=gtext('Path');?></th>
				<th class="lhell"><?=gtext('Filesystem');?></th>
				<th class="lhell"><?=gtext('Reason Code');?></th>
				<th class="lhebc"><?=gtext('Toolbox');?></th>
			</tr>
		</thead>
		<tbody>
<?php
			foreach($sphere_array as $sphere_record):
?>
			<tr>
<?php
				$enabled      = isset($sphere_record['enabled']);
				$notprotected = !$sphere_record['protected'];
				$tag_id       = ' id="'  . $sphere_record['uuid'] . '"';
				$tag_name     = ' name="' . $checkbox_member_name . '[]"';
				$tag_value    = ' value="' . $sphere_record['uuid'] . '"';
				$tag_disabled = ' disabled="disabled"';
				if(empty($sphere_record['fstype'])):
					$gt_fstype =  gtext('Unknown or Unformatted');
				else:
					$gt_fstype = htmlspecialchars(get_fstype_shortdesc($sphere_record['fstype']));
				endif;
				if($notprotected):
					$tag_checked = $enabled ? ' checked="checked"' : '';
					switch($a_button['checkbox_control']):
						case 2:
							echo '<td class="lcelc"><input type="checkbox"',$tag_name,$tag_value,$tag_id,$tag_checked,'/></td>',"\n";
							break;
						case 1:
							echo '<td class="lcelc"><input type="checkbox"',$tag_name,$tag_value,$tag_id,$tag_disabled,$tag_checked,'/></td>',"\n";
							if($enabled):
								echo '<input type="hidden"',$tag_name,$tag_value,'/>',"\n";
							endif;
							break;
						case 0:
							echo '<td></td>',"\n";
							if($enabled):
								echo '<input type="hidden"',$tag_name,$tag_value,'/>',"\n";
							endif;
							break;
					endswitch;
				else:
					echo '<td class="lcelcd"><input type="checkbox"',$tag_name,$tag_value,$tag_id,$tag_disabled,'/></td>',"\n";
				endif;
?>
				<td class="<?=$notprotected ? 'lcell' : 'lcelld';?>"><?=htmlspecialchars($sphere_record['name']);?></td>
				<td class="<?=$notprotected ? 'lcell' : 'lcelld';?>"><?=htmlspecialchars($sphere_record['serial'] ?? gtext('N/A'));?></td>
				<td class="<?=$notprotected ? 'lcell' : 'lcelld';?>"><?=htmlspecialchars($sphere_record['size']);?></td>
				<td class="<?=$notprotected ? 'lcell' : 'lcelld';?>"><?=htmlspecialchars($sphere_record['devicespecialfile']);?></td>
				<td class="<?=$notprotected ? 'lcell' : 'lcelld';?>"><?=$gt_fstype;?></td>
				<td class="<?=$notprotected ? 'lcell' : 'lcelld';?>"><?=htmlspecialchars($sphere_record['protected.reason']);?></td>
				<td class="lcebld"><table class="area_data_selection_toolbox"><tbody><tr>
					<td>
<?php
						if($notprotected):
						else:
							echo '<img src="',$img_path['loc'],'" title="',$gt_record_loc,'" alt="',$gt_record_loc . '"/>',"\n";
						endif;
?>
					</td>
					<td></td>
					<td></td>
				</tr></tbody></table></td>
			</tr>
<?php
			endforeach;
?>
		</tbody>
	</table>
	<div id="submit">
<?php
		switch($a_button['submit_control']):
			case 2: echo '<input type="submit" class="formbtn" name="',$a_button['submit_name'],'" value="',$a_button['submit_value'],'"/>',"\n"; break;
			case 1: echo '<input type="submit" class="formbtn" name="',$a_button['submit_name'],'" value="',$a_button['submit_value'],'" disabled="disabled"/>',"\n"; break;
		endswitch;
		switch($a_button['cancel_control']):
			case 2: echo '<input type="submit" class="formbtn" name="',$a_button['cancel_name'],'" value="',$a_button['cancel_value'],'"/>',"\n"; break;
			case 1: echo '<input type="submit" class="formbtn" name="',$a_button['cancel_name'],'" value="',$a_button['cancel_value'],'" disabled="disabled"/>',"\n"; break;
		endswitch;
?>
	</div>
<?php
	if(count($do_format) > 0):
		foreach($do_format as $do_format_disk):
			echo(sprintf("<div id='cmdoutput'>%s</div>",sprintf(gtext("Command output") . " for disk %s :",$do_format_disk['devicespecialfile'])));
			echo('<pre class="cmdoutput">');
			disks_format($do_format_disk['devicespecialfile'],$do_format_disk['filesystem'],$do_format_disk['notinitmbr'],$do_format_disk['minspace'],$do_format_disk['volumelabel'],$do_format_disk['aft4k'],$do_format_disk['zfsgpt']);
			echo('</pre><br/>');
		endforeach;
	endif;
	include 'formend.inc';
?>
</div><div class="area_data_pot"></div></form>
<?php
include 'fend.inc';
