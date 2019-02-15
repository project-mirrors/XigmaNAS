<?php
/*
	common\sphere\level2.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright © 2018-2019 XigmaNAS <info@xigmanas.com>.
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
namespace common\sphere;
/**
 *	sphere object basement for row and grid
 */
class level2 extends level1 {
	public $row_id = NULL;
	protected $x_notifier = NULL;
	protected $x_row_identifier = NULL;
	protected $x_lock = false;
/**
 *	Enable/disable record lock support
 *	@param bool $flag
 *	@return $this
 */
	public function set_lock(bool $flag = false) {
		$this->x_lock = $flag;
		return $this;
	}
/**
 *	Returns true when record lock support is enabled
 *	@return bool
 */
	public function is_lock_enabled() {
		return $this->x_lock;
	}
	public function set_notifier(string $notifier = NULL) {
		$this->x_notifier = $notifier;
		return $this;
	}
	public function get_notifier() {
		return $this->x_notifier;
	}
	public function row_identifier(string $row_identifier = NULL) {
		if(isset($row_identifier)):
			if(1 === preg_match('/^[a-z]+$/',$row_identifier)):
				$this->x_row_identifier = $row_identifier;
			endif;
		endif;
		return $this->x_row_identifier ?? false;
	}
	public function set_row_key($key = NULL) {
		$this->row_id = $key;
		return $this;
	}
	public function get_row_key() {
		return $this->row_id;
	}
	public function get_row_identifier() {
		return $this->x_row_identifier ?? false;
	}
	public function set_row_identifier(string $row_identifier = NULL) {
		$this->x_row_identifier = $row_identifier;
		return $this;
	}
	public function get_row_identifier_value() {
		return $this->row[$this->x_row_identifier] ?? NULL;
	}
}
