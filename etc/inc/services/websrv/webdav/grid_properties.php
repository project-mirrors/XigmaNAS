<?php
/*
	grid_properties.php

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

namespace services\websrv\webdav;

use common\properties as myp;

class grid_properties extends myp\container_row {
	protected $x_name;
	public function init_name(): myp\property_text {
		$property = $this->x_name = new myp\property_text($this);
		$property->
			set_name('name')->
			set_title(gettext('WebDAV Name'));
		return $property;
	}
	final public function get_name(): myp\property_text {
		return $this->x_name ?? $this->init_name();
	}
	protected $x_folderpattern;
	public function init_folderpattern(): myp\property_text {
		$property = $this->x_folderpattern = new myp\property_text($this);
		$property->
			set_name('folderpattern')->
			set_title(gettext('Folder Pattern'));
		return $property;
	}
	final public function get_folderpattern(): myp\property_text {
		return $this->x_folderpattern ?? $this->init_folderpattern();
	}
	protected $x_isreadonly;
	public function init_isreadonly(): myp\property_bool {
		$property = $this->x_isreadonly = new myp\property_bool($this);
		$property->
			set_name('isreadonly')->
			set_title(gettext('Read-Only'));
		return $property;
	}
	final public function get_isreadonly(): myp\property_bool {
		return $this->x_isreadonly ?? $this->init_isreadonly();
	}
	protected $x_usesqlite;
	public function init_usesqlite(): myp\property_bool {
		$property = $this->x_usesqlite = new myp\property_bool($this);
		$property->
			set_name('usesqlite')->
			set_title(gettext('Use Sqlite'));
		return $property;
	}
	final public function get_usesqlite(): myp\property_bool {
		return $this->x_usesqlite ?? $this->init_usesqlite();
	}
	protected $x_auxparam;
	public function init_auxparam(): myp\property_auxparam {
		$property = $this->x_auxparam = new myp\property_auxparam($this);
		return $property;
	}
	final public function get_auxparam(): myp\property_auxparam {
		return $this->x_auxparam ?? $this->init_auxparam();
	}
}
