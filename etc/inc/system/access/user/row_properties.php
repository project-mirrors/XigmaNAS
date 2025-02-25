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

namespace system\access\user;

use common\lang;
use common\properties as myp;
use common\timezone;

class row_properties extends grid_properties {
	public function init_name(): myp\property_text {
		$description = gettext('Enter login name.');
		$placeholder = gettext('Name');
		$property = parent::init_name();
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonmodify(false)->
			set_id('login')->
			set_maxlength(16)->
			set_placeholder($placeholder)->
			set_size(18)->
			set_filter(FILTER_CALLBACK)->
			set_filter_options(function($subject) {
//				FreeBSD reference for regular expression: usr.sbin/pw/pw_user.c -> pw_checkname
				$regexp = '/^[0-9A-Za-z\.;\[\]_\{\}][0-9A-Za-z\-\.;\[\]_\{\}]*\$?$/';
				$reservedlogin = ['root','toor','daemon','operator','bin','tty','kmem','www','nobody','ftp','sshd'];
				$result = null;
				if(is_string($subject)):
					if(strlen($subject) > 0 && strlen($subject) < 17):
						if(preg_match($regexp,$subject) === 1 && !in_array($subject,$reservedlogin)):
							return $subject;
						endif;
					endif;
				endif;
				return $result;
			});
		return $property;
	}
	public function init_fullname(): myp\property_text {
//		gecos: fullname cannot contain ':' and \x7F
		$description = gettext('Enter the full user name.');
		$placeholder = gettext('Full Name');
		$regexp = '/^[^:\x7F]*$/';
		$property = parent::init_fullname();
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_id('fullname')->
			set_maxlength(128)->
			set_placeholder($placeholder)->
			set_size(40)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => null,'regexp' => $regexp]);
		return $property;
	}
	public function init_password(): myp\property_text {
		$description = gettext('Set user password.');
		$placeholder = gettext('Password');
		$property = parent::init_password();
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonmodify(false)->
			set_id('password')->
			set_input_type($property::INPUT_TYPE_PASSWORD)->
			set_maxlength(128)->
			set_placeholder($placeholder)->
			set_size(40)->
			filter_use_default();
		return $property;
	}
	public function init_uid(): myp\property_int {
		$caption = gettext('Specify the user/account numeric id.');
		$description = gettext('The recommended range for the user id is between 1000 and 32000.');
		$placeholder = gettext('1000');
		$property = parent::init_uid();
		$property->
			set_caption($caption)->
			set_defaultvalue('')->
			set_description($description)->
			set_id('id')->
			set_maxlength(10)->
			set_min(0)->
			set_max(2147483647)->
			set_placeholder($placeholder)->
			set_size(12)->
			filter_use_default();
		return $property;
	}
	public function init_usershell(): myp\property_list {
		$description = gettext('Set user login shell.');
		$options = [
			'nologin' => gettext('No Login'),
			'scponly' => gettext('scponly - Secure Copy Protocol only'),
			'sh' => gettext('sh - Bourne Shell'),
			'bash' => gettext('bash - Bourne Again Shell'),
			'csh' => gettext('csh - C Shell'),
			'tcsh' => gettext('tcsh - TENEX C Shell')
		];
		$property = parent::init_usershell();
		$property->
			set_defaultvalue('nologin')->
			set_description($description)->
			set_id('shell')->
			set_options($options)->
			filter_use_default();
		return $property;
	}
	public function init_primary_group(): myp\property_list {
		$description = gettext("Set the account's primary group.");
		$property = parent::init_primary_group();
		$property->
			set_defaultvalue('31')->
			set_description($description)->
			set_id('primarygroup')->
			set_input_type($property::INPUT_TYPE_SELECT)->
			set_options([])->
			filter_use_default();
		return $property;
	}
	public function init_additional_groups(): myp\property_list_multi {
		$description = gettext('Add user to additional groups.');
		$property = parent::init_additional_groups();
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_id('group')->
			set_options([])->
			filter_use_default();
		return $property;
	}
	public function init_homedir(): myp\property_text {
		global $g;

		$property = parent::init_homedir();
		$description = gettext('Enter the path to the home directory of the user. Leave this field empty to use default path /mnt.');
		$placeholder = gettext('/mnt');
		$property->
			set_defaultvalue($g['media_path'])->
			set_description($description)->
			set_id('homedir')->
			set_input_type($property::INPUT_TYPE_FILECHOOSER)->
			set_maxlength(1024)->
			set_placeholder($placeholder)->
			set_placeholderv($placeholder)->
			set_size(60)->
			filter_use_default_or_empty();
		return $property;
	}
	public function init_user_portal_access(): myp\property_list {
		$description = gettext('User portal permission.');
		$options = [
			'' => gettext('No access to the user portal'),
			'1' => gettext('User access'),
			'admin' => gettext('Admin access')
		];
		$property = parent::init_user_portal_access();
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_id('userportal')->
			set_options($options)->
			filter_use_default();
		return $property;
	}
	public function init_language(): myp\property_list {
		$description = gettext('Select the language of the user portal for this user.');
		$options = lang::get_options();
		$property = parent::init_language();
		$property->
			set_defaultvalue('auto')->
			set_description($description)->
			set_id('language')->
			set_input_type($property::INPUT_TYPE_SELECT)->
			set_options($options)->
			filter_use_default();
		return $property;
	}
	public function init_timezone(): myp\property_list {
		$description = gettext('Select the timezone for this user.');
		$options = timezone::get_timezones_for_select();
		$property = parent::init_timezone();
		$property->
			set_defaultvalue('UTC')->
			set_description($description)->
			set_id('timezone')->
			set_input_type($property::INPUT_TYPE_SELECT)->
			set_options($options)->
			filter_use_default();
		return $property;
	}
	public function init_fm_enable(): myp\property_bool {
		$caption = gettext('Grant access to the file manager.');
		$property = parent::init_fm_enable();
		$property->
			set_id('fm_enable')->
			set_caption($caption)->
			set_defaultvalue(false)->
			filter_use_default();
		return $property;
	}
	public function init_fmp_show_hidden_items(): myp\property_bool {
		$caption = gettext('Show hidden items');
		$property = parent::init_fmp_show_hidden_items();
		$property->
			set_id('fmp_show_hidden_items')->
			set_caption($caption)->
			set_defaultvalue(false)->
			filter_use_default();
		return $property;
	}
	public function init_fmp_read(): myp\property_bool {
		$caption = gettext('Grant read permission');
		$property = parent::init_fmp_read();
		$property->
			set_id('fmp_read')->
			set_caption($caption)->
			set_defaultvalue(false)->
			filter_use_default();
		return $property;
	}
	public function init_fmp_create(): myp\property_bool {
		$caption = gettext('Grant create permission');
		$property = parent::init_fmp_create();
		$property->
			set_id('fmp_create')->
			set_caption($caption)->
			set_defaultvalue(false)->
			filter_use_default();
		return $property;
	}
	public function init_fmp_change(): myp\property_bool {
		$caption = gettext('Grant modify permission');
		$property = parent::init_fmp_change();
		$property->
			set_id('fmp_change')->
			set_caption($caption)->
			set_defaultvalue(false)->
			filter_use_default();
		return $property;
	}
	public function init_fmp_delete(): myp\property_bool {
		$caption = gettext('Grant delete permission');
		$property = parent::init_fmp_delete();
		$property->
			set_id('fmp_delete')->
			set_caption($caption)->
			set_defaultvalue(false)->
			filter_use_default();
		return $property;
	}
}
