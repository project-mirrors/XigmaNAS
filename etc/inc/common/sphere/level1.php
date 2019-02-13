<?php
/*
	common\sphere\level1.php

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
/*
 *	sphere level 1 object for settings, services, row and grid
 */
class level1 extends scriptname {
	public $grid = [];
	public $row = [];
	public $row_default = [];
	protected $x_parent = NULL;
	protected $x_enadis = false;
	protected $x_class_button = 'formbtn';
	public function __destruct() {
		unset($this->x_parent);
	}
	public function get_parent() {
		if(!is_object($this->x_parent)):
			$this->x_parent = new scriptname($this->get_basename(),$this->get_extension());
		endif;
		return $this->x_parent;
	}
/**
 *	Enable/disable enable/disable option
 *	@param bool $flag
 *	@return $this
 */
	public function set_enadis(bool $flag = false) {
		$this->x_enadis = $flag;
		return $this;
	}
/**
 *	Returns the status of the enable/disable option.
 *	@return bool
 */
	public function is_enadis_enabled() {
		return $this->x_enadis;
	}
	public function escape_javascript(string $data = '') {
		return str_replace(['"',"'"],['\u0022','\u0027'],$data);
	}
	public function doj() {
		$output = [];
		$output[] = '';
		return implode(PHP_EOL,$output);
	}
	public function get_js_on_load() {
		return '';
	}
	public function get_js_document_ready() {
		return '';
	}
	public function get_js() {
		return '';
	}
	public function html_button(string $value = NULL,string $content = NULL,string $id = NULL) {
		$element = 'button';
		if(is_null($value)):
			$value = 'cancel';
		endif;
		if(is_null($id)):
			$id = sprintf('%1$s_%2$s',$element,$value);
		endif;
		if(is_null($content)):
			$content = gettext('Cancel');
		endif;
		$button_attributes = [
			'name' => 'submit',
			'type' => 'submit',
			'class' => $this->x_class_button,
			'value' => $value,
			'id' => $id
		];
		if($value === 'cancel'):
			$button_attributes['formnovalidate'] = 'formnovalidate';
		endif;
		$root = new \co_DOMDocument();
		$o_button = $root->addElement($element,$button_attributes,$content);
		return $root->get_html();
	}
}
