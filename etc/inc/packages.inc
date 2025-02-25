<?php
/*
	packages.inc

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
require_once 'functions.inc';
require_once 'util.inc';

// Get list of installed packages.
// Result is in the form:
// [1] => Array
// (
// 	[name] => "xyz"
// 	[desc]  => "xyz"
// )
// Return array of installed packages
function packages_get_installed() {
	$packages = [];
	$id = 0;

	exec("/usr/local/sbin/pkg info", $rawdata);
	foreach($rawdata as $line) {
		$pos = strpos($line," ");
		$pkgname = substr($line,0,$pos);
		$pkgdesc = substr($line,$pos+1);

		$packages[$id] = [];
		$packages[$id]['name'] = $pkgname;
		$packages[$id]['desc'] = $pkgdesc;

		$id++;
  }
  unset($line);

	return $packages;
}

// Uninstall package.
// $pkgname - The package name or filename (e.g. xyz or xyz.txz)
// Return 0 if successful, 1 if error
function packages_uninstall($pkgname) {
	// Remove '.txz' if necessary.
	$pkgname = basename($pkgname, ".txz");

	$result = mwexec("/usr/local/sbin/pkg delete -y {$pkgname}");

	if (0 == $result) {
		write_log("Successfully uninstalled package '{$pkgname}'");
	} else {
		write_log("Failed to uninstall package '{$pkgname}'");
	}

	return $result;
}

// Check whether package is already installed.
// $pkgname - The package name or filename (e.g. xyz or xyz.txz)
// Return 0 if already installed, otherwise 1
function packages_is_installed($pkgname) {
	// Remove '.txz' if necessary.
	$pkgname = basename($pkgname, ".txz");

	// Check whether package is already installed.
	$result = mwexec("/usr/local/sbin/pkg info {$pkgname}");

	return $result;
}

// Install package.
// $pkgname - The package filename to be installed (e.g. xyz.tbz)
// Return 0 if successful, 1 if error
function packages_install($pkgname) {
	system("/usr/local/sbin/pkg add  {$pkgname}", $result);
	if (0 == $result) {
		write_log("Successfully installed package '{$pkgname}'");
	} else {
		write_log("Failed to install package '{$pkgname}'");
	}

	return $result;
}
?>
