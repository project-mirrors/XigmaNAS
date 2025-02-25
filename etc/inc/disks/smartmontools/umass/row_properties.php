<?php
/*
	row_properties.php

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

namespace disks\smartmontools\umass;

use common\properties as myp;

final class row_properties extends grid_properties {
	public function init_name(): myp\property_text {
		$description = gettext('The identifier reported as unknown by smartctl, including brackets.');
		$placeholder = gettext('Enter Identifier');
		$property = parent::init_name();
		$property->
			set_id('name')->
			set_description($description)->
			set_defaultvalue('')->
			set_size(60)->
			set_maxlength(64)->
			set_placeholder($placeholder)->
			filter_use_default();
		return $property;
	}
	public function init_type(): myp\property_text {
		$placeholder = gettext('Enter smartctl pass-through type.');
		$property = parent::init_type();
		$property->
			set_id('type')->
			set_description('')->
			set_defaultvalue('sat')->
			set_size(60)->
			set_maxlength(64)->
			set_placeholder($placeholder)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => null,'regexp' => '/.*/']);
		return $property;
	}
}
