<?php
/*
	css.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2021 XigmaNAS® <info@xigmanas.com>.
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

namespace gui;

use common\arr;
use const FILTER_REQUIRE_SCALAR,
	FILTER_VALIDATE_REGEXP;
use function file_exists,
	file_get_contents,
	filter_var,
	header,
	is_null,
	sprintf;

final class css {
/**
 *	echo css
 *	@global array $config
 *	@global array $g
 *	@param string $cfg_key_filename The name of the key containing the filename of the custom css in config > system > webgui.
 *	@param string $cfg_key_filemode The name of the key containing the filemode for the custom css (blank,append,replace) in config > system > webgui.
 *	@param string $vanilla_filename The name of the shipped css file.
 */
	public final static function render(string $cfg_key_filename,string $cfg_key_filemode,string $vanilla_filename) {
		global $config,$g;

		header('Content-type: text/css');
		$css_default_filename = sprintf('%s/css/%s',$g['www_path'],$vanilla_filename);
		$webgui_settings = arr::make_branch($config,'system','webgui');
		$css_filemode = filter_var($webgui_settings[$cfg_key_filemode],FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => '','regexp' => '/^(|append|replace)$/']]);
		$css_custom_filename = filter_var($webgui_settings[$cfg_key_filename],FILTER_VALIDATE_REGEXP,['flags' => FILTER_REQUIRE_SCALAR,'options' => ['default' => null,'regexp' => '/\S/']]);
		switch($css_filemode):
			default:
				if(file_exists($css_default_filename)):
					$css_content = file_get_contents($css_default_filename);
					if($css_content !== false):
						echo $css_content;
					endif;
				endif;
				break;
			case 'append':
				if(file_exists($css_default_filename)):
					$css_content = file_get_contents($css_default_filename);
					if($css_content !== false):
						echo $css_content;
					endif;
				endif;
//				append customizations
				if(isset($css_custom_filename) && file_exists($css_custom_filename)):
					$css_content = file_get_contents($css_custom_filename);
					if($css_content !== false):
						echo "/*\n\tCustomizations\n*/\n",$css_content;
					endif;
				endif;
				break;
			case 'replace':
				$css_content = null;
				if(isset($css_custom_filename) && file_exists($css_custom_filename)):
					$css_content = file_get_contents($css_custom_filename);
					if($css_content !== false):
						echo "/*\n\tCustom CSS\n*/\n",$css_content;
					else:
						$css_content = null;
					endif;
				endif;
//				fallback
				if(is_null($css_content)):
					if(file_exists($css_default_filename)):
						$css_content = file_get_contents($css_default_filename);
						if($css_content !== false):
							echo $css_content;
						endif;
					endif;
				endif;
				break;
		endswitch;
	}
}
