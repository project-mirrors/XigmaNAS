<?php
/*
	tui.inc

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
	All rights reserved.

	Copyright (c) 2006-2008 Volker Theile (votdev@gmx.de)
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
require_once 'util.inc';

// Display a yes|no dialog.
// $text - The dialog text
// $width - Width of the dialog
// $height - Height of the dialog
// Returns 0 if yes, 1 if no or 255 if aborted via ESC.
function tui_display_yesno($text, $width, $height, $defaultyes = true) {
	$param = "";
	if (false === $defaultyes) $param = "--defaultno";
	passthru("/usr/local/bin/cdialog --clear {$param} --yesno \"{$text}\" {$height} {$width}", $retval);
	return $retval;
}

// Display a message box.
// $title - The dialog title
// $text - The dialog text
// $width - Width of the dialog
// $height - Height of the dialog
// Returns 0 if ok has been pressed or 255 if aborted via ESC.
function tui_display_message($title, $text, $width, $height) {
	passthru("/usr/local/bin/cdialog --clear --title \"{$title}\" --msgbox \"{$text}\" {$height} {$width}", $retval);
	return $retval;
}

// Display a message box.
// $title - The dialog title
// $text - The dialog text
// $width - Width of the dialog
// $height - Height of the dialog
// Returns 0 if OK, 1 if Cancel or 255 if aborted via ESC.
function tui_display_message_ex($title, $text, $width, $height) {
	passthru("/usr/local/bin/cdialog --clear --yes-label \"OK\" --no-label \"Cancel\" --title \"{$title}\" --yesno \"{$text}\" {$height} {$width}", $retval);
	return $retval;
}

// Display a input box.
// $text - The dialog text
// $width - Width of the dialog
// $height - Height of the dialog
// $value [in|out]- Initial value. Also contains result.
// Returns 0 if ok has been pressed or 255 if aborted via ESC.
function tui_display_input($text, $width, $height, &$value) {
	$tmpfile = "/tmp/tui" . rand();
	passthru("/usr/local/bin/cdialog --input-fd 2 --output-fd 1 --clear --inputbox \"{$text}\" {$height} {$width} \"{$value}\" 1>{$tmpfile}", $retval);
	$value = @file_get_contents($tmpfile);
	@unlink($tmpfile);
	return $retval;
}

// Display a menu.
// $title - The dialog title
// $menutitle - The menu title
// $width - Width of the dialog
// $height - Height of the dialog
// $menuheight - Height of the menu
// $amenuitem - The menu item array => Array
// (
// 	[tag]  => "x"
// 	[item] => "xy zxy yxz"
// )
// $value [out] - Contains the selected item tag.
// Returns 0 if ok has been pressed or 255 if aborted via ESC.
function tui_display_menu($title, $menutitle, $width, $height, $menuheight, $amenuitem, &$value) {
	$tmpfile = "/tmp/tui" . rand();
	$cmd = "/usr/local/bin/cdialog --input-fd 2 --output-fd 1 --clear --title \"{$title}\" --menu \"{$menutitle}\" {$height} {$width} {$menuheight}";
	foreach($amenuitem as $menuitemv) {
		$cmd .= " \"{$menuitemv['tag']}\" \"{$menuitemv['item']}\"";
	}
	$cmd .= " 1>{$tmpfile}";
	passthru($cmd, $retval);
	$value = @file_get_contents($tmpfile);
	@unlink($tmpfile);
	return $retval;
}

// Wait until user has pressed 'RETURN'.
function tui_wait_keypress($message = "") {
	if (!empty($message))
		write_console("$message\n");
	write_console("\nPress ENTER to continue.");
	$fp = fopen('php://stdin', 'r');
	fgets($fp);
	fclose($fp);
}
/**
 *	TUI dialog for flexible partitions
 *		the partition layout is an array with partition records:
 *
 *		'partition'     => 4,           // Partition Number
 *		'volname'       => 'data',      // Volume Name
 *		'fstype'        => 'ufs',       // File System Type
 *		'size'          => '*',         // Size
 *		'uom'           => 'M',         // Unit of Measure
 *		'minimum'       => 0,           // Minimum Size
 *		'maximum'       => PHP_INT_MAX, // Maximum Size
 *		'recommended'   => PHP_INT_MAX, // Recommended Size
 *		'allow_edit'    => true,        // Is editing allowed?
 *		'allow_size'    => true,        // Allowed changing size?
 *		'allow_zero'    => true,        // Allowed to enter 0?
 *		'allow_star'    => true,        // Allowed to enter *?
 *		'friendly_name' => 'data',      // Displayed Name
 *		'has_next'      => false        // End Of Table?
 *
 *	@param array $partlayout The preconfigured partition table
 *	@param int $destination_media_capacity Capacity of the destination media in bytes.
 *	@return array Updated partition layout or false on failure
 */
function tui_partlayout(array $partlayout,int $destination_media_capacity,array $base_settings = []) {
	$base_uom = $base_settings['uom'] ?? 'M';
	$base_uom_powerof2 = $base_settings['uom_powerof2'] ?? 20; // MiB
	$base_align = $base_uom_powerof2 + $base_settings['align'] ?? 2; // align partitions to 4MiB
	$destination_media_capacity_left = $destination_media_capacity >> $base_uom_powerof2;
	$destination_media_capacity_reserved = 0;
	//	initialize variables
	foreach($partlayout as $key => &$row):
		$row['uom_powerof2'] = 10 * ((false !== ($test = stripos('kmgtpezy',$row['uom']))) ? 1 + $test : 0);
		$row['minimum_aligned'] = (($row['minimum'] << $row['uom_powerof2']) + (1 << $base_align) - 1) >> $base_align << $base_align >> $base_uom_powerof2;
		$destination_media_capacity_reserved += $row['minimum_aligned'];
	endforeach;
	//	test capacity
	if($destination_media_capacity_reserved > $destination_media_capacity_left):
		tui_wait_keypress('The capacity of the selected device is not sufficient enough.');
		return false;
	endif;
	//	cycle through partitions
	foreach($partlayout as $key => &$row):
		$destination_media_capacity_reserved -= $row['minimum_aligned'];
		$entered_size = $row['size'];
		if($destination_media_capacity_left > 0):
			if($row['allow_star']):
				$row['maximum'] = $destination_media_capacity_left - $destination_media_capacity_reserved;
			else:
				$row['maximum'] = min($row['maximum'],$destination_media_capacity_left - $destination_media_capacity_reserved);
			endif;
			$row['recommended'] = max($row['recommended'],$row['minimum']); // recommended must be >= minimum
			$row['recommended'] = min($row['recommended'],$row['maximum']); // recommended must be <= maximum
			if('*' !== $entered_size):
				$entered_size = min($entered_size,$row['recommended']);
			endif;
			if($row['allow_edit']):
				$text = sprintf('Enter the size of the %1$s partition in %2$sB' . PHP_EOL . '(minimum: %3$s, maximum: %4$s, recommended: %5$s)',$row['friendly_name'],$row['uom'],$row['minimum'],$row['maximum'],$entered_size);
			endif;
			do {
				if($row['allow_edit']):
					$result = tui_display_input($text,60,8,$entered_size);
					if(0 != $result):
						return false;
					endif;
				endif;
				if($row['allow_size'] && is_numeric($entered_size) && $entered_size >= $row['minimum'] && $entered_size <= $row['maximum']):
					$row['size'] = $entered_size;
					$nocigar = false;
				elseif($row['allow_zero'] && is_numeric($entered_size) && 0 === $entered_size):
					//	record will be unset
//					$entered_size = 0;
//					$row['size'] = $entered_size;
					$nocigar = false;
				elseif($row['allow_star'] && is_string($entered_size) && '*' === $entered_size):
					$entered_size = $row['maximum'];
					if(0 === $destination_media_capacity_reserved):
						//	no reserved partitions left
						$row['size'] = '*';
					elseif($row['has_next']):
						$row['size'] = $row['maximum'];
					else:
						$row['size'] = '*';
					endif;
					$nocigar = false;
				elseif($row['allow_edit']):
					$nocigar = true;
				else:
					$no_cigar = true;
					tui_wait_keypress('There is a problem with the partition configuration.');
					return false;
				endif;
			} while ($nocigar);
		else:
			$entered_size = 0;
		endif;
		//	calculate remaining capacity
		$entered_size_aligned = (($entered_size << $row['uom_powerof2']) + (1 << $base_align) - 1) >> $base_align << $base_align >> $base_uom_powerof2;
		$destination_media_capacity_left -= $entered_size_aligned;
		if(0 === $entered_size):
			unset($partlayout[$key]);
		endif;
	endforeach;
	return $partlayout;
}
