<?php
/*
	cli_toolbox.php

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

namespace disks\geom\raid5;

use function escapeshellarg,
	implode,
	mwexec2;

/**
 *	Wrapper class for autoloading functions
 */
final class cli_toolbox {
/**
 *	Returns details of a single graid5 or all graid5s.
 *	@param string $entity_name If provided, only details of this specific graid5 are returned.
 *	@return string An unescaped string.
 */
	public static function get_list(string $entity_name = null): string {
		$a_cmd = ['/sbin/geom','raid5','list'];
		if(isset($entity_name)):
			$a_cmd[] = escapeshellarg($entity_name);
		endif;
		$a_cmd[] = '2>&1';
		$cmd = implode(' ',$a_cmd);
		mwexec2($cmd,$output);
		return implode("\n",$output);
	}
/**
 *	Returns the status of a single graid5 or all graid5s.
 *	@return string An unescaped string.
 */
	public static function get_status(string $entity_name = null): string {
		$a_cmd = ['/sbin/geom','raid5','status'];
		if(isset($entity_name)):
			$a_cmd[] = escapeshellarg($entity_name);
		endif;
		$a_cmd[] = '2>&1';
		$cmd = implode(' ',$a_cmd);
		mwexec2($cmd,$output);
		return implode("\n",$output);
	}
}
