<?php
/*
	grid_properties.php

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

namespace services\inadyn;

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
	protected $x_verifyaddress;
/**
 *	verify-address = <true | false>
 *		By default inadyn verifies both IPv4 and IPv6 addresses, making
 *		sure the address is a valid Internet address. Invalid addresses
 *		are, e.g., link local, loopback, multicast and known experimental
 *		addresses. For more information, see RFC3330.
 *
 *		IP address validation can be disabled by setting this option to
 *		false.
 *	@return myp\property_bool
 */
	public function init_verifyaddress(): myp\property_bool {
		$title = gettext('Verify Address');
		$property = $this->x_verifyaddress = new myp\property_bool($this);
		$property->
			set_name('verifyaddress')->
			set_title($title);
		return $property;
	}
	final public function get_verifyaddress(): myp\property_bool {
		return $this->x_verifyaddress ?? $this->init_verifyaddress();
	}
	protected $x_fakeaddress;
/**
 *	fake-address = <true | false>
 *		When using SIGUSR1, to do a forced update, this option can be used
 *		to fake an address update with a "random" address in the
 *		203.0.113.0/24 range, example address range from RFC5737, before
 *		updating with the actual IP address. This is completely outside
 *		spec., but can be useful for people who very rarely, if ever, get
 *		an IP address change. Because some DDNS service providers will not
 *		register even a forced update if the IP is the same. As a result
 *		the user could be deregistered as an inactive user.
 *	@return myp\property_bool
 */
	public function init_fakeaddress(): myp\property_bool {
		$title = gettext('Fake Address');
		$property = $this->x_fakeaddress = new myp\property_bool($this);
		$property->
			set_name('fakeaddress')->
			set_title($title);
		return $property;
	}
	final public function get_fakeaddress(): myp\property_bool {
		return $this->x_fakeaddress ?? $this->init_fakeaddress();
	}
	protected $x_allowipv6;
/**
 *	allow-ipv6 = <true | false>
 *		Inadyn can get an IPv6 address from an interface, or with an exter-
 *		nal checkip script. This option controls if IPv6 addresses should
 *		be allowed or discarded. By default this option is false, i.e.
 *		IPv6 addresses are discarded.
 *	@return myp\property_bool
 */
	public function init_allowipv6(): myp\property_bool {
		$title = gettext('Allow IPv6');
		$property = $this->x_allowipv6 = new myp\property_bool($this);
		$property->
			set_name('allowipv6')->
			set_title($title);
		return $property;
	}
	final public function get_allowipv6(): myp\property_bool {
		return $this->x_allowipv6 ?? $this->init_allowipv6();
	}
	protected $x_iface;
/**
 *	iface = IFNAME
 *		Use network interface IFNAME as source of IP address changes in-
 *		stead of querying an external server. With this option is enabled,
 *		the external IP check is disabled and inadyn will send DDNS updates
 *		using the IP address of the IFNAME network interface to all DDNS
 *		providers listed in the configuration file. This can be useful to
 *		register LAN IP addresses, or, when connected directly to a public
 *		IP address, to speed up the IP check if the DDNS provider's check-
 *		ip servers are slow to respond.
 *
 *		This option can also be given as a command line option to
 *		inadyn(8), both serve a purpose, use whichever one works for you.
 */
	public function init_iface(): myp\property_list {
		$title = gettext('Network Interface');
		$property = $this->x_iface = new myp\property_list($this);
		$property->
			set_name('iface')->
			set_title($title);
		return $property;
	}
	final public function get_iface(): myp\property_list {
		return $this->x_iface ?? $this->init_iface();
	}
	protected $x_iterations;
/**
 *	iterations = <NUM | 0>
 *		Set the number of DNS updates. The default is 0, which means infin-
 *		ity.
 */
	public function init_iterations(): myp\property_int {
		$title = gettext('Iterations');
		$property = $this->x_iterations = new myp\property_int($this);
		$property->
			set_name('iterations')->
			set_title($title);
		return $property;
	}
	final public function get_iterations(): myp\property_int {
		return $this->x_iterations ?? $this->init_iterations();
	}
	protected $x_period;
/**
 *	period = SEC
 *		How often the IP is checked, in seconds. Default: apxrox. 1 minute.
 *		Max: 10 days.
 */
	public function init_period(): myp\property_int {
		$title = gettext('Check Interval');
		$property = $this->x_period = new myp\property_int($this);
		$property->
			set_name('period')->
			set_title($title);
		return $property;
	}
	final public function get_period(): myp\property_int {
		return $this->x_period ?? $this->init_period();
	}
	protected $x_forcedupdate;
/**
 *	forced-update = SEC
 *		How often the IP should be updated even if it is not changed. The
 *		time should be given in seconds. Default is equal to 30 days.
 */
	public function init_forceupdate(): myp\property_int {
		$title = gettext('Force Update Interval');
		$property = $this->x_forceupdate = new myp\property_int($this);
		$property->
			set_name('forceupdate')->
			set_title($title);
		return $property;
	}
	final public function get_forceupdate(): myp\property_int {
		return $this->x_forceupdate ?? $this->init_forceupdate();
	}
	protected $x_securessl;
/**
 *	secure-ssl = < true | false >
 *		If the HTTPS certificate validation fails for a provider inadyn
 *		aborts the DDNS update before sending any credentials. When this
 *		setting is disabled, i.e. false, then inadyn will only issue a
 *		warning. By default this setting is enabled, because security mat-
 *		ters.
 *	@return myp\property_bool
 */
	public function init_securessl(): myp\property_bool {
		$title = gettext('Secure SSL');
		$property = $this->x_securessl = new myp\property_bool($this);
		$property->
			set_name('securessl')->
			set_title($title);
		return $property;
	}
	final public function get_securessl(): myp\property_bool {
		return $this->x_securessl ?? $this->init_securessl();
	}
	protected $x_brokenrtc;
/**
 *	broken-rtc = < true | false >
 *		HTTPS certificates are only valid within specified time windows, so
 *		on systems without hardware real-time clock and default bootup time
 *		far in the past, false-positive validation fail is expected. When
 *		this setting is enabled, i.e. true, then inadyn will only issue a
 *		warning that the certificate is not valid yet. By default this set-
 *		ting is disabled, because security matters.
 *	@return myp\property_bool
 */
	public function init_brokenrtc(): myp\property_bool {
		$title = gettext('Broken RTC');
		$property = $this->x_brokenrtc = new myp\property_bool($this);
		$property->
			set_name('brokenrtc')->
			set_title($title);
		return $property;
	}
	final public function get_brokenrtc(): myp\property_bool {
		return $this->x_brokenrtc ?? $this->init_brokenrtc();
	}
	protected $x_catrustfile;
/**
 *	ca-trust-file = FILE
 *	By default inadyn uses the built-in path to the system's trusted CA
 *	certificates, both GnuTLS and Open/LibreSSL support this. As a
 *	fall-back, in case the API's to load CA certificates from the
 *	built-in path fails, inadyn also supports common default paths to
 *	Debian and RedHat CA bundles.
 *
 *	This setting overrides the built-in paths and fallback locations
 *	and provides a way to specify the path to a trusted set of CA cer-
 *	tificates, in PEM format, bundled into one file.
 */
	public function init_catrustfile(): myp\property_text {
		$title = gettext('CA Trusted Certificates');
		$property = $this->x_catrustfile = new myp\property_text($this);
		$property->
			set_name('catrustfile')->
			set_title($title);
		return $property;
	}
	final public function get_catrustfile(): myp\property_text {
		return $this->x_catrustfile ?? $this->init_catrustfile();
	}
	protected $x_useragent;
/**
 *	user-agent = STRING
 *		Specify the User-Agent string to send to the DDNS provider on
 *		checkip and update requests. Some providers require this field to
 *		be set to a specific string, some may be OK with "Mozilla/4.0".
 *		The default is to send "inadyn/VERSION SUPPORTURL", where VERSION
 *		is the current inadyn version, and SUPPORTURL is the upstream sup-
 *		port URL.
 *
 *		This can also be set on a per-provider basis, see below custom and
 *		provider section description.
 */
	public function init_useragent(): myp\property_text {
		$title = gettext('User Agent');
		$property = $this->x_useragent = new myp\property_text($this);
		$property->
			set_name('useragent')->
			set_title($title);
		return $property;
	}
	final public function get_useragent(): myp\property_text {
		return $this->x_useragent ?? $this->init_useragent();
	}
	protected $x_configfile;
/**
 *	-f, --config FILE
 *		Use FILE for configuration. By default
 *		/usr/local/etc/inadyn.conf, is used. See inadyn.conf(5) for examples.
 *	@return myp\property_text
 */
	public function init_configfile(): myp\property_text {
		$title = gettext('Configuration File');
		$property = $this->x_configfile = new myp\property_text($this);
		$property->
			set_name('configfile')->
			set_title($title);
		return $property;
	}
	final public function get_configfile(): myp\property_text {
		return $this->x_configfile ?? $this->init_configfile();
	}
	protected $x_cachedir;
/**
 *
 *	--cache-dir PATH
 *		Set directory for persistent cache files, defaults to /var/cache/inadyn
 *
 *		The cache files are used to keep track of which addresses have
 *		been successfully sent to their respective DDNS provider and
 *		when. The latter 'when' is important to prevent inadyn from ban-
 *		ning you for excessive updates.
 *
 *		When restarting inadyn or rebooting your server, or embedded de-
 *		vice, inadyn reads the cache files to seed its internal data
 *		structures with the last sent IP address and when the update was
 *		performed. It is therefore very important to both have a cache
 *		file and for it to have the correct time stamp. The absence of a
 *		cache file will currently cause a forced update.
 *
 *		On an embedded device with no RTC, or no battery backed RTC, it
 *		is strongly recommended to pair this setting with the
 *		--startup-delay SEC command line option.
 *	@return myp\property_text
 */
	public function init_cachedir(): myp\property_text {
		$title = gettext('Cache Directory');
		$property = $this->x_cachedir = new myp\property_text($this);
		$property->
			set_name('cachedir')->
			set_title($title);
		return $property;
	}
	final public function get_cachedir(): myp\property_text {
		return $this->x_cachedir ?? $this->init_cachedir();
	}
	protected $x_startupdelay;
/**
 *	-t, --startup-delay SEC
 *		Initial startup delay. Default is 0 seconds. Any signal can be
 *		used to abort the startup delay early, but SIGUSR2 is the recom-
 *		mended to use. See SIGNALS below for full details of how inadyn
 *		responds to signals.
 *
 *		Intended to allow time for embedded devices without a battery
 *		backed real time clock to set their clock via NTP at bootup.
 *		This is so that the time since the last update can be calculated
 *		correctly from the inadyn cache file and the forced-update SEC
 *		setting honored across reboots, avoiding unnecessary IP address
 *		updates.
 *	@return myp\property_int
 */
	public function init_startupdelay(): myp\property_int {
		$title = gettext('Startup Delay');
		$property = $this->x_startupdelay = new myp\property_int($this);
		$property->
			set_name('startupdelay')->
			set_title($title);
		return $property;
	}
	final public function get_startupdelay(): myp\property_int {
		return $this->x_startupdelay ?? $this->init_startupdelay();
	}
	protected $x_loglevel;
/**
 *	-l, --loglevel LEVEL
 *		Set log level: none, err, info, notice, debug. The default is
 *		notice, but you might want to set this to -l warning.
 *	@return myp\property_list
 */
	public function init_loglevel(): myp\property_list {
		$title = gettext('Log Level');
		$property = $this->x_loglevel = new myp\property_list($this);
		$property->
			set_name('loglevel')->
			set_title($title);
		return $property;
	}
	final public function get_loglevel(): myp\property_list {
		return $this->x_loglevel ?? $this->init_loglevel();
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
