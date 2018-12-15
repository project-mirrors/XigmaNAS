<?php
/*
	properties_iscsi_initiator.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018 XigmaNAS <info@xigmanas.com>.
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
require_once 'properties.php';

class iscsi_initiator_properties extends co_property_container_param {
	protected $x_name;

	public function init_name() {
		$description = gettext('This is a nickname and is for information only.');
		$placeholder = gettext('Enter Name');
		$title = gettext('Name');
		$property = $this->x_name = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('name')->
			set_name('name')->
			set_size(20)->
			set_title($title)->
			filter_use_default();
		return $property;
	}
	public function get_name() {
		return $this->x_name ?? $this->init_name();
	}
	protected $x_targetname;
	public function init_targetname() {
		$description = gettext('The name of the target.');
		$title = gettext('Target Name');
		$property = $this->x_targetname = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('targetname')->
			set_name('targetname')->
			set_size(60)->
			set_title($title)->
			filter_use_default();
		return $property;
	}
	public function get_targetname() {
		return $this->x_targetname ?? $this->init_targetname();
	}
	protected $x_targetaddress;
	public function init_targetaddress() {
		$description = gettext('The IP address or the DNS name of the iSCSI target.');
		$title = gettext('Target Address');
		$property = $this->x_targetaddress = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('targetaddress')->
			set_name('targetaddress')->
			set_size(60)->
			set_title($title)->
			filter_use_default();
		return $property;
	}
	public function get_targetaddress() {
		return $this->x_targetaddress ?? $this->init_targetaddress();
	}
	protected $x_initiatorname;
	public function init_initiatorname() {
		$description = gettext('The name of the initiator. By default, the name is concatenation of "iqn.1994-09.org.freebsd:" with the hostname.');
		$placeholder = 'iqn.1994-09.org.freebsd:hostname';
		$title = gettext('Initiator Name');
		$property = $this->x_initiatorname = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('initiatorname')->
			set_name('initiatorname')->
			set_placeholder($placeholder)->
			set_size(60)->
			set_title($title)->
			filter_use_default_or_empty();
		return $property;
	}
	public function get_initiatorname() {
		return $this->x_initiatorname ?? $this->init_initiatorname();
	}
	protected $x_auxparam;
	public function init_auxparam() {
		$property = $this->x_auxparam = new property_auxparam($this);
		$description = gettext('These parameters will be added to the configuration of this initiator.');
		$property->
			set_description($description);
		return $property;
	}
	public function get_auxparam() {
		return $this->x_auxparam ?? $this->init_auxparam();
	}
	protected $x_authmethod;
	public function init_authmethod() {
		$property = $this->x_authmethod = new property_list($this);
		$description = gettext('Sets the authentication type. Type can be either "None", or "CHAP". Default is "None". When set to CHAP, both chapIName and chapSecret must be defined.');
		$options = [
			'' => gettext('Default'),
			'None' => gettext('None'),
			'CHAP' => gettext('CHAP')
		];
		$title = gettext('Authentication Method');
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('authmethod')->
			set_name('authmethod')->
			set_options($options)->
			set_title($title)->
			filter_use_default();
		return $property;
	}
	public function get_authmethod() {
		return $this->x_authmethod ?? $this->init_authmethod();
	}
	protected $x_chapiname;
	public function init_chapiname() {
		$description = gettext('Login for CHAP authentication.');
		$title = $placeholder = gettext('CHAP Name');
		$regexp = '/^\S{0,32}$/';
		$property = $this->x_chapiname = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('chapiname')->
			set_maxlength(32)->
			set_name('chapiname')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_title($title)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp]);
		return $property;
	}
	public function get_chapiname() {
		return $this->x_chapiname ?? $this->init_chapiname();
	}
	protected $x_chapsecret;
	public function init_chapsecret() {
		$description = gettext('Secret for CHAP authentication.');
		$title = $placeholder = gettext('CHAP Secret');
		$regexp = '/^[^"]{0,32}$/';
		$property = $this->x_chapsecret = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('chapsecret')->
			set_maxlength(32)->
			set_name('chapsecret')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_title($title)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			filter_use_empty()->
			set_filter_group('ui',['empty','ui']);
		return $property;
	}
	public function get_chapsecret() {
		return $this->x_chapsecret ?? $this->init_chapsecret();
	}
	protected $x_tgtchapname;
	public function init_tgtchapname() {
		$description = gettext('Target login for Mutual CHAP authentication.');
		$title = $placeholder = gettext('Mutual CHAP Name');
		$regexp = '/^\S{0,32}$/';
		$property = $this->x_tgtchapname = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('tgtchapname')->
			set_maxlength(32)->
			set_name('tgtchapname')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_title($title)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp]);
		return $property;
	}
	public function get_tgtchapname() {
		return $this->x_tgtchapname ?? $this->init_tgtchapname();
	}
	protected $x_tgtchapsecret;
	public function init_tgtchapsecret() {
		$description = gettext('Target secret for Mutual CHAP authentication.');
		$title = $placeholder = gettext('Mutual CHAP Secret');
		$regexp = '/^[^"]{0,32}$/';
		$property = $this->x_tgtchapsecret = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('tgtchapsecret')->
			set_maxlength(32)->
			set_name('tgtchapsecret')->
			set_placeholder($placeholder)->
			set_size(40)->
			set_title($title)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp])->
			filter_use_empty()->
			set_filter_group('ui',['empty','ui']);
		return $property;
	}
	public function get_tgtchapsecret() {
		return $this->x_tgtchapsecret ?? $this->init_tgtchapsecret();
	}
	protected $x_headerdigest;
	public function init_headerdigest() {
		$property = $this->x_headerdigest = new property_list($this);
		$description = gettext('Sets the header digest; a checksum calculated over the header of iSCSI PDUs, and verified on receive. Digest can be either "None", or "CRC32C". Default is "None".');
		$options = [
			'' => gettext('Default'),
			'None' => gettext('None'),
			'CRC32C' => gettext('CRC32C')
		];
		$title = gettext('Header Digest');
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('headerdigest')->
			set_name('headerdigest')->
			set_options($options)->
			set_title($title)->
			filter_use_default();
		return $property;
	}
	public function get_headerdigest() {
		return $this->x_headerdigest ?? $this->init_headerdigest();
	}
	protected $x_datadigest;
	public function init_datadigest() {
		$property = $this->x_datadigest = new property_list($this);
		$description = gettext('Sets the data digest; a checksum calculated over the Data Section of iSCSI PDUs, and verified on receive. Digest can be either "None", or "CRC32C". Default is "None".');
		$options = [
			'' => gettext('Default'),
			'None' => gettext('None'),
			'CRC32C' => gettext('CRC32C')
		];
		$title = gettext('Data Digest');
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('datadigest')->
			set_name('datadigest')->
			set_options($options)->
			set_title($title)->
			filter_use_default();
		return $property;
	}
	public function get_datadigest() {
		return $this->x_datadigest ?? $this->init_datadigest();
	}
	protected $x_protocol;
	public function init_protocol() {
		$property = $this->x_protocol = new property_list($this);
		$description = gettext('Name of selected protocol. It can be either "iSER", for iSCSI over RDMA, or "iSCSI". Default is "iSCSI".');
		$options = [
			'' => gettext('Default'),
			'iSCSI' => gettext('iSCSI'),
			'iSER' => gettext('iSER - iSCSI over RDMA')
		];
		$title = gettext('Protocol');
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('protocol')->
			set_name('protocol')->
			set_options($options)->
			set_title($title)->
			filter_use_default();
		return $property;
	}
	public function get_protocol() {
		return $this->x_protocol ?? $this->init_protocol();
	}
	protected $x_offload;
	public function init_offload() {
		$description = gettext('Define iSCSI hardware offload driver. The default is "None".');
		$title = $placeholder = gettext('Offload Driver');
		$regexp = '/^\S{0,60}$/';
		$property = $this->x_offload = new property_text($this);
		$property->
			set_defaultvalue('')->
			set_description($description)->
			set_editableonadd(true)->
			set_editableonmodify(true)->
			set_id('offload')->
			set_maxlength(60)->
			set_name('offload')->
			set_placeholder($placeholder)->
			set_size(60)->
			set_title($title)->
			set_filter(FILTER_VALIDATE_REGEXP)->
			set_filter_flags(FILTER_REQUIRE_SCALAR)->
			set_filter_options(['default' => NULL,'regexp' => $regexp]);
		return $property;
	}
	public function get_offload() {
		return $this->x_offload ?? $this->init_offload();
	}
/*
	SessionType
		Sets the session type. Type can be either "Discovery", or "Normal". Default is "Normal".
		For normal sessions, the TargetName must be defined. Discovery sessions
		result in the initiator connecting to all the targets
		returned by SendTargets iSCSI discovery with the defined TargetAddress.
	Enable
		Enable or disable the session. State can be either "On", or "Off". Default is "On".
 */
}
