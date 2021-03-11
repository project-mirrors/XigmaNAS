<?php
/*
	grid_properties.php

	Part of XigmaNAS® (https://www.xigmanas.com).
	Copyright © 2018-2020 XigmaNAS® <info@xigmanas.com>.
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

namespace services\torrent;

use common\properties as myp;

use function gettext;

class grid_properties extends myp\container {
	protected $x_enable;
	public function init_enable(): myp\property_enable {
		$property = $this->x_enable = new myp\property_enable($this);
		return $property;
	}
	final public function get_enable(): myp\property_enable {
		return $this->x_enable ?? $this->init_enable();
	}
	protected $x_peerport;
	public function init_peerport(): myp\property_int {
		$title = gettext('Peer Port');
		$property = $this->x_peerport = new myp\property_int($this);
		$property->
			set_name('peerport')->
			set_title($title);
		return $property;
	}
	final public function get_peerport(): myp\property_int {
		return $this->x_peerport ?? $this->init_peerport();
	}
	protected $x_configdir;
	public function init_configdir(): myp\property_text {
		$title = gettext('Configuration Folder');
		$property = $this->x_configdir = new myp\property_text($this);
		$property->
			set_name('configdir')->
			set_title($title);
		return $property;
	}
	final public function get_configdir(): myp\property_text {
		return $this->x_configdir ?? $this->init_configdir();
	}
	protected $x_incompletedir;
	public function init_incompletedir(): myp\property_text {
		$title = gettext('Incomplete Folder');
		$property = $this->x_incompletedir = new myp\property_text($this);
		$property->
			set_name('incompletedir')->
			set_title($title);
		return $property;
	}
	final public function get_incompletedir(): myp\property_text {
		return $this->x_incompletedir ?? $this->init_incompletedir();
	}
	protected $x_downloaddir;
	public function init_downloaddir(): myp\property_text {
		$title = gettext('Download Folder');
		$property = $this->x_downloaddir = new myp\property_text($this);
		$property->
			set_name('downloaddir')->
			set_title($title);
		return $property;
	}
	final public function get_downloaddir(): myp\property_text {
		return $this->x_downloaddir ?? $this->init_downloaddir();
	}
	protected $x_watchdir;
	public function init_watchdir(): myp\property_text {
		$title = gettext('Watch Folder');
		$property = $this->x_watchdir = new myp\property_text($this);
		$property->
			set_name('watchdir')->
			set_title($title);
		return $property;
	}
	final public function get_watchdir(): myp\property_text {
		return $this->x_watchdir ?? $this->init_watchdir();
	}
	protected $x_portforwarding;
	public function init_portforwarding(): myp\property_bool {
		$title = gettext('Port Forwarding');
		$property = $this->x_portforwarding = new myp\property_bool($this);
		$property->
			set_name('portforwarding')->
			set_title($title);
		return $property;
	}
	final public function get_portforwarding(): myp\property_bool {
		return $this->x_portforwarding ?? $this->init_portforwarding();
	}
	protected $x_pex;
	public function init_pex(): myp\property_bool {
		$title = gettext('Peer Exchange');
		$property = $this->x_pex = new myp\property_bool($this);
		$property->
			set_name('pex')->
			set_title($title);
		return $property;
	}
	final public function get_pex(): myp\property_bool {
		return $this->x_pex ?? $this->init_pex();
	}
	protected $x_dht;
	public function init_dht(): myp\property_bool {
		$title = gettext('Distributed Hash Table');
		$property = $this->x_dht = new myp\property_bool($this);
		$property->
			set_name('dht')->
			set_title($title);
		return $property;
	}
	final public function get_dht(): myp\property_bool {
		return $this->x_dht ?? $this->init_dht();
	}
	protected $x_lpd;
	public function init_lpd(): myp\property_bool {
		$title = gettext('Local Peer Discovery');
		$property = $this->x_lpd = new myp\property_bool($this);
		$property->
			set_name('lpd')->
			set_title($title);
		return $property;
	}
	final public function get_lpd(): myp\property_bool {
		return $this->x_lpd ?? $this->init_lpd();
	}
	protected $x_utp;
	public function init_utp(): myp\property_bool {
		$title = gettext('uTP');
		$property = $this->x_utp = new myp\property_bool($this);
		$property->
			set_name('utp')->
			set_title($title);
		return $property;
	}
	final public function get_utp(): myp\property_bool {
		return $this->x_utp ?? $this->init_utp();
	}
	protected $x_upload;
	public function init_upload(): myp\property_int {
		$title = gettext('Upload Bandwidth');
		$property = $this->x_upload = new myp\property_int($this);
		$property->
			set_name('upload')->
			set_title($title);
		return $property;
	}
	final public function get_upload(): myp\property_int {
		return $this->x_upload ?? $this->init_upload();
	}
	protected $x_download;
	public function init_download(): myp\property_int {
		$title = gettext('Download Bandwidth');
		$property = $this->x_download = new myp\property_int($this);
		$property->
			set_name('download')->
			set_title($title);
		return $property;
	}
	final public function get_download(): myp\property_int {
		return $this->x_download ?? $this->init_download();
	}
	protected $x_umask;
	public function init_umask(): myp\property_octal {
		$title = gettext('User Mask');
		$property = $this->x_umask = new myp\property_octal($this);
		$property->
			set_name('umask')->
			set_title($title);
		return $property;
	}
	final public function get_umask(): myp\property_octal {
		return $this->x_umask ?? $this->init_umask();
	}
	protected $x_preallocation;
	public function init_preallocation(): myp\property_list {
		$title = gettext('Preallocation');
		$property = $this->x_preallocation = new myp\property_list($this);
		$property->
			set_name('preallocation')->
			set_title($title);
		return $property;
	}
	final public function get_preallocation(): myp\property_list {
		return $this->x_preallocation ?? $this->init_preallocation();
	}
	protected $x_encryption;
	public function init_encryption(): myp\property_list {
		$title = gettext('Encryption');
		$property = $this->x_encryption = new myp\property_list($this);
		$property->
			set_name('encryption')->
			set_title($title);
		return $property;
	}
	final public function get_encryption(): myp\property_list {
		return $this->x_encryption ?? $this->init_encryption();
	}
	protected $x_messagelevel;
	public function init_messagelevel(): myp\property_list {
		$title = gettext('Message Level');
		$property = $this->x_messagelevel = new myp\property_list($this);
		$property->
			set_name('messagelevel')->
			set_title($title);
		return $property;
	}
	final public function get_messagelevel(): myp\property_list {
		return $this->x_messagelevel ?? $this->init_messagelevel();
	}
	protected $x_extraoptions;
	public function init_extraoptions(): myp\property_text {
		$title = gettext('Extra Options');
		$property = $this->x_extraoptions = new myp\property_text($this);
		$property->
			set_name('extraoptions')->
			set_title($title);
		return $property;
	}
	final public function get_extraoptions(): myp\property_text {
		return $this->x_extraoptions ?? $this->init_extraoptions();
	}
	protected $x_port;
	public function init_port(): myp\property_int {
		$title = gettext('Port');
		$property = $this->x_port = new myp\property_int($this);
		$property->
			set_name('port')->
			set_title($title);
		return $property;
	}
	final public function get_port(): myp\property_int {
		return $this->x_port ?? $this->init_port();
	}
	protected $x_authrequired;
	public function init_authrequired(): myp\property_bool {
		$title = gettext('Authentication');
		$property = $this->x_authrequired = new myp\property_bool($this);
		$property->
			set_name('authrequired')->
			set_title($title);
		return $property;
	}
	final public function get_authrequired(): myp\property_bool {
		return $this->x_authrequired ?? $this->init_authrequired();
	}
	protected $x_username;
	public function init_username(): myp\property_text {
		$title = gettext('Username');
		$property = $this->x_username = new myp\property_text($this);
		$property->
			set_name('username')->
			set_title($title);
		return $property;
	}
	final public function get_username(): myp\property_text {
		return $this->x_username ?? $this->init_username();
	}
	protected $x_password;
	public function init_password(): myp\property_text {
		$title = gettext('Password');
		$property = $this->x_password = new myp\property_text($this);
		$property->
			set_name('password')->
			set_title($title);
		return $property;
	}
	final public function get_password(): myp\property_text {
		return $this->x_password ?? $this->init_password();
	}
	protected $x_rpchostwhitelistenabled;
	public function init_rpchostwhitelistenabled(): myp\property_list {
		$title = gettext('DNS Rebind Protection');
		$property = $this->x_rpchostwhitelistenabled = new myp\property_list($this);
		$property->
			set_name('rpchostwhitelistenabled')->
			set_title($title);
		return $property;
	}
	final public function get_rpchostwhitelistenabled(): myp\property_list {
		return $this->x_rpchostwhitelistenabled ?? $this->init_rpchostwhitelistenabled();
	}
	protected $x_rpchostwhitelist;
	public function init_rpchostwhitelist(): myp\property_text {
		$title = gettext('Domain Names');
		$property = $this->x_rpchostwhitelist = new myp\property_text($this);
		$property->
			set_name('rpchostwhitelist')->
			set_title($title);
		return $property;
	}
	final public function get_rpchostwhitelist(): myp\property_text {
		return $this->x_rpchostwhitelist ?? $this->init_rpchostwhitelist();
	}
}
