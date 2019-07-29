<?php
/*
	grid_properties.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018-2019 XigmaNAS <info@xigmanas.com>.
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
namespace services\ctld\hub\sub\lun;
use common\properties as myp;

class grid_properties extends myp\container_row {
	protected $x_number;
	public function init_number(): myp\property_int {
		$property = $this->x_number = new myp\property_int($this);
		$property->
			set_name('number')->
			set_title(gettext('LUN Number'));
		return $property;
	}
	final public function get_number(): myp\property_int {
		return $this->x_number ?? $this->init_number();
	}
	protected $x_name;
	public function init_name(): myp\property_list {
		$property = $this->x_name = new myp\property_list($this);
		$property->
			set_name('name')->
			set_title(gettext('LUN Name'));
		return $property;
	}
	final public function get_name(): myp\property_list {
		return $this->x_name ?? $this->init_name();
	}
	protected $x_group;
	public function init_group(): myp\property_list_multi {
		$property = $this->x_group = new myp\property_list_multi($this);
		$property->
			set_name('group')->
			set_title(gettext('Target'));
		return $property;
	}
	final public function get_group(): myp\property_list_multi {
		return $this->x_group ?? $this->init_group();
	}
}
