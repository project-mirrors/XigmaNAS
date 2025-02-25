<?php
/*
	co_zpool_info.inc

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

require_once 'autoload.php';
require_once 'config.inc';
require_once 'util.inc';

use common\uuid;

class co_zpool_info {
/*
 *	object to collect and provide the current zpool configuration.
 *	Several get methods return filtered information from the configuration.
 *	This object uses SimpleXML to store configuration information and
 *	it makes heavily use of xpath.
 *
 */
	private $a_xml;
	private $poolname_filter;
	private $is_poolname_filter_set;
	private $devicepath_strip_regex;
	private $is_devicepath_strip_regex;
	private $exclude_devices_not_present;

	public function __construct() {
		$this->scan();
		$this->set_poolname_filter();
		$this->set_devicepath_strip_regex();
		$this->set_exclude_devices_not_present(false);
	}
	public function configuration_loaded() {
		$b_ret = ($this->a_xml !== false);
		return $b_ret;
	}
	public function pool_state_to_name($n_state = 0) {
		switch($n_state):
			case 0: return gtext('Active');
			case 1: return gtext('Exported');
			case 2: return gtext('Destroyed');
			case 3: return gtext('Spare');
			case 4: return gtext('L2 Cache');
			case 5: return gtext('Uninitialized');
			case 6: return gtext('Unavailable');
			case 7: return gtext('Potentially Active');
		endswitch;
		return gtext('Unknown');
	}
	public function set_poolname_filter($value = '') {
		if(preg_match('/\S/',$value)):
			$this->poolname_filter = htmlspecialchars($value,ENT_XML1 || ENT_NOQUOTES,'UTF-8');
			$this->is_poolname_filter_set = true;
		else:
			$this->poolname_filter = '';
			$this->is_poolname_filter_set = false;
		endif;
	}
	public function set_devicepath_strip_regex($value = '') {
		if(preg_match('/\S/',$value)):
			$this->devicepath_strip_regex = $value;
			$this->is_devicepath_strip_regex = true;
		else:
			$this->devicepath_strip_regex = '';
			$this->is_devicepath_strip_regex = false;
		endif;
	}
	public function set_exclude_devices_not_present(bool $exclude_devices_not_present = false) {
		$this->exclude_devices_not_present = $exclude_devices_not_present;
	}
	protected function pool_info_to_array($r_pool,&$r_ret = []) {
		$r_ret['pool.guid'] = htmlspecialchars_decode((string)$r_pool->pool_guid,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['pool.name'] = htmlspecialchars_decode((string)$r_pool->name,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['pool.version'] = htmlspecialchars_decode((string)$r_pool->version,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['pool.state'] = htmlspecialchars_decode((string)$r_pool->state,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['pool.state_name'] = $this->pool_state_to_name(htmlspecialchars_decode($r_ret['pool.state'],ENT_XML1 || ENT_NOQUOTES));
		return $r_ret;
	}
	protected function vdev_info_to_array($r_vdev,&$r_ret = []) {
		$r_ret['vdev.type'] = htmlspecialchars_decode((string)$r_vdev->type,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.id'] = htmlspecialchars_decode((string)$r_vdev->id,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.guid'] = htmlspecialchars_decode((string)$r_vdev->guid,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.ashift'] = htmlspecialchars_decode((string)$r_vdev->ashift,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.asize'] = htmlspecialchars_decode((string)$r_vdev->asize,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.is_log'] = htmlspecialchars_decode((string)$r_vdev->is_log,ENT_XML1 || ENT_NOQUOTES);
		return $r_ret;
	}
	protected function devicevdev_info_to_array($r_device,&$r_ret = []) {
		$r_ret['vdev.type'] = htmlspecialchars_decode((string)$r_device->type,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.id'] = htmlspecialchars_decode((string)$r_device->id,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.guid'] = uuid::create_v4();
		$r_ret['vdev.ashift'] = htmlspecialchars_decode((string)$r_device->ashift,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.asize'] = htmlspecialchars_decode((string)$r_device->asize,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['vdev.is_log'] = htmlspecialchars_decode((string)$r_device->is_log,ENT_XML1 || ENT_NOQUOTES);
		return $r_ret;
	}
	protected function device_info_to_array($r_device,&$r_ret = []) {
		$r_ret['device.type'] = htmlspecialchars_decode((string)$r_device->type,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['device.id'] = htmlspecialchars_decode((string)$r_device->id,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['device.guid'] = htmlspecialchars_decode((string)$r_device->guid,ENT_XML1 || ENT_NOQUOTES);
		if($this->is_devicepath_strip_regex):
			$r_ret['device.path'] = preg_replace($this->devicepath_strip_regex,'',htmlspecialchars_decode((string)$r_device->path,ENT_XML1 || ENT_NOQUOTES));
		else:
			$r_ret['device.path'] = htmlspecialchars_decode((string)$r_device->path,ENT_XML1 || ENT_NOQUOTES);
		endif;
		$r_ret['device.phys_path'] = htmlspecialchars_decode((string)$r_device->phys_path,ENT_XML1 || ENT_NOQUOTES);
		$r_ret['device.not_present'] = (bool)$r_device->not_present;
		return $r_ret;
	}
	public function get_all_pools(&$a_ret = []) {
/*
 *		returns all pools (based on zpool list)
 */
		$r_ret =[];
		$x_pool = '/pools/pool';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_all_data_devices(&$a_ret = []) {
/*
 *		locate all data members and prepare one row per member. return an array of records with disks, vdev and pool information
 */
		$this->get_single_data_devices($a_ret);
		$this->get_mirrored_data_devices($a_ret);
		$this->get_raidz_data_devices($a_ret);
		return $a_ret;
	}
	public function get_single_data_devices(&$a_ret = []) {
/*
 *		locate single data members and prepare one row per member. return an array of records with disks, vdev and pool information
 */
		$r_ret = [];
		if($this->is_poolname_filter_set): // single pool. sequence: pool -> device
			$x_pool = sprintf('/pools/pool[name="%s"]',$this->poolname_filter); // query pool
		else:
			$x_pool = '/pools/pool[vdev_tree/children[is_log="0" and type="disk"]]';
		endif;
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				if(!$this->exclude_devices_not_present):
					$x_device = './vdev_tree/children[is_log="0" and type="disk" and not(not_present)]';
				else:
					$x_device = './vdev_tree/children[is_log="0" and type="disk"]';
				endif;
				$a_device = $r_pool->xpath($x_device);
				if($a_device !== false):
					foreach($a_device as $r_device):
						$this->devicevdev_info_to_array($r_device,$r_ret);
						$this->device_info_to_array($r_device,$r_ret);
						$a_ret[(string)$r_device->guid] = $r_ret;
					endforeach;
				endif;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_mirrored_data_devices(&$a_ret = []) {
/*
 *		locate mirrored data members and prepare one row per member. return an array of records with disks, vdev and pool
 */
		$r_ret = [];
		if($this->is_poolname_filter_set): // is a pool filter set?
			$x_pool = sprintf('/pools/pool[name="%s"]',$this->poolname_filter);
		else:
			$x_pool = '/pools/pool[vdev_tree/children[is_log="0" and type="mirror"]]';
		endif;
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$x_vdev = './vdev_tree/children[is_log="0" and type="mirror"]';
				$a_vdev = $r_pool->xpath($x_vdev);
				if($a_vdev !== false):
					foreach($a_vdev as $r_vdev):
						$this->vdev_info_to_array($r_vdev,$r_ret);
						if($this->exclude_devices_not_present):
							$x_device = './/children[is_log="0" and type="disk" and not(not_present)]';
						else:
							$x_device = './/children[is_log="0" and type="disk"]';
						endif;
						$a_device = $r_vdev->xpath($x_device);
						if($a_device !== false):
							foreach($a_device as $r_device):
								$this->device_info_to_array($r_device,$r_ret);
								$a_ret[(string)$r_device->guid] = $r_ret;
							endforeach;
						endif;
					endforeach;
				endif;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_raidz_data_devices(&$a_ret = []) {
/*
 *		locate raidz data members and prepare one row per member. return an array of records with disks, vdev and pool
 */
		$r_ret = [];
		if($this->is_poolname_filter_set): // is a pool filter set?
			$x_pool = sprintf('/pools/pool[name="%s"]',$this->poolname_filter);
		else:
			$x_pool = '/pools/pool[vdev_tree/children[is_log="0" and type="raidz"]]';
		endif;
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$x_vdev = './vdev_tree/children[is_log="0" and type="raidz"]';
				$a_vdev = $r_pool->xpath($x_vdev);
				if($a_vdev !== false):
					foreach($a_vdev as $r_vdev):
						$this->devicevdev_info_to_array($r_vdev,$r_ret);
						if($this->exclude_devices_not_present):
							$x_device = './/children[is_log="0" and type="disk" and not(not_present)]';
						else:
							$x_device = './/children[is_log="0" and type="disk"]';
						endif;
						$a_device = $r_vdev->xpath($x_device);
						if($a_device !== false):
							foreach($a_device as $r_device):
								$this->device_info_to_array($r_device,$r_ret);
								$a_ret[(string)$r_device->guid] = $r_ret;
							endforeach;
						endif;
					endforeach;
				endif;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_all_log_devices(&$a_ret = []) {
		$this->get_single_log_devices($a_ret);
		$this->get_mirrored_log_devices();
		return $a_ret;
	}
	public function get_single_log_devices(&$a_ret = []) {
/*
 *		locate single log members and prepare one row per member. return an array of records with disks, vdev and pool
 */
		$r_ret = [];
		if($this->is_poolname_filter_set): // single pool. sequence: pool -> device
			$x_pool = sprintf('/pools/pool[name="%s"]',$this->poolname_filter); // query pool
		else:
			$x_pool = '/pools/pool[vdev_tree/children[is_log="1" and type="disk"]]';
		endif;
		$x_device = './vdev_tree/children[is_log="1" and type="disk"]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_device = $r_pool->xpath($x_device);
				if($a_device !== false):
					foreach($a_device as $r_device):
						$this->devicevdev_info_to_array($r_device,$r_ret);
						$this->device_info_to_array($r_device,$r_ret);
						$a_ret[(string)$r_device->guid] = $r_ret;
					endforeach;
				endif;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_mirrored_log_devices(&$a_ret = []) {
/*
 *		locate mirrored log members and prepare one row per member. return an array of records with disks, vdev and pool
 */
		$r_ret = [];
		if($this->is_poolname_filter_set): // is a pool filter set?
			$x_pool = sprintf('/pools/pool[name="%s"]',$this->poolname_filter);
		else:
			$x_pool = '/pools/pool[vdev_tree/children[is_log="1" and type="mirror"]]';
		endif;
		$x_vdev = './vdev_tree/children[is_log="1" and type="mirror"]';
		$x_device = './/children[is_log="1" and type="disk"]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_vdev = $r_pool->xpath($x_vdev);
				if($a_vdev !== false):
					foreach($a_vdev as $r_vdev):
						$this->devicevdev_info_to_array($r_vdev,$r_ret);
						$a_device = $r_vdev->xpath($x_device);
						if($a_device !== false):
							foreach($a_device as $r_device):
								$this->device_info_to_array($r_device,$r_ret);
								$a_ret[(string)$r_device->guid] = $r_ret;
							endforeach;
						endif;
					endforeach;
				endif;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_all_cache_devices(&$a_ret = []) {
		$this->get_single_cache_devices($a_ret);
		return $a_ret;
	}
	public function get_single_cache_devices(&$a_ret = []) {
/*
 *		locate single cache members and prepare one row per member. return an array of records with disks and pool
 */
		$r_ret = [];
		if($this->is_poolname_filter_set):
			$x_pool = sprintf('/pools/pool[name="%s"]',$this->poolname_filter);
		else:
			$x_pool = '/pools/pool[caches]';
		endif;
		$x_device = './caches/.//children[is_cache="1"]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_device = $r_pool->xpath($x_device);
				if($a_device !== false):
					foreach($a_device as $r_device):
						$this->devicevdev_info_to_array($r_device,$r_ret);
						$this->device_info_to_array($r_device,$r_ret);
						$a_ret[(string)$r_device->guid] = $r_ret;
					endforeach;
				endif;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_all_spare_devices(&$a_ret = []) {
		$this->get_single_spare_devices($a_ret);
		return $a_ret;
	}
	public function get_single_spare_devices(&$a_ret = []) {
/*
 *		locate single spare members and prepare one row per member. return an array of records with disks, vdev and pool
 */
		$r_ret = [];
		if($this->is_poolname_filter_set):
			$x_pool = sprintf('/pools/pool[name="%s"]',$this->poolname_filter);
		else:
			$x_pool = '/pools/pool[spares]';
		endif;
		$x_device = './spares/.//children[is_spare="1"]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_device = $r_pool->xpath($x_device);
				if($a_device !== false):
					foreach($a_device as $r_device):
						$this->devicevdev_info_to_array($r_device,$r_ret);
						$this->device_info_to_array($r_device,$r_ret);
						$a_ret[(string)$r_device->guid] = $r_ret;
					endforeach;
				endif;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_all_devices(&$a_ret = []) {
		$this->get_all_cache_devices($a_ret);
		$this->get_all_data_devices($a_ret);
		$this->get_all_log_devices($a_ret);
		$this->get_all_spare_devices($a_ret);
		return $a_ret;
	}
	public function get_all_devices_except_spare_devices(&$a_ret = []) {
		$this->get_all_cache_devices($a_ret);
		$this->get_all_data_devices($a_ret);
		$this->get_all_log_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_with_single_data_devices(&$a_ret = []) {
		$r_ret = [];
		$x_pool = '/pools/pool[vdev_tree/children[is_log="0" and type="disk"]]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_pools_with_mirrored_data_devices(&$a_ret = []) {
		$r_ret = [];
		$x_pool = '/pools/pool[vdev_tree/children[is_log="0" and type="mirror"]]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_pools_with_raidz_data_devices(&$a_ret = []) {
		$r_ret = [];
		$x_pool = '/pools/pool[vdev_tree/children[is_log="0" and type="raidz"]]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_pools_with_cache_devices(&$a_ret = []) {
		$this->get_pools_with_single_cache_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_with_single_cache_devices(&$a_ret = []) {
		$r_ret = [];
		$x_pool = '/pools/pool[caches/children[is_cache="1" and type="disk"]]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_pools_with_log_devices(&$a_ret = []) {
		$this->get_pools_with_mirrored_log_devices($a_ret);
		$this->get_pools_with_single_log_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_with_single_log_devices(&$a_ret = []) {
		$r_ret = [];
		$x_pool = '/pools/pool[vdev_tree/children[is_log="1" and type="disk"]]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_pools_with_mirrored_log_devices(&$a_ret = []) {
		$r_ret = [];
		$x_pool = '/pools/pool[vdev_tree/children[is_log="1" and type="mirror"]]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_pools_with_spare_devices(&$a_ret = []) {
		$this->get_pools_with_single_spare_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_with_single_spare_devices(&$a_ret = []) {
		$r_ret = [];
		$x_pool = '/pools/pool[spares/children[is_spare="1"]]';
		$a_pool = $this->a_xml->xpath($x_pool);
		if($a_pool !== false):
			foreach($a_pool as $r_pool):
				$this->pool_info_to_array($r_pool,$r_ret);
				$a_ret[(string)$r_pool->pool_guid] = $r_ret;
			endforeach;
		endif;
		return $a_ret;
	}
	public function get_pools_for_attach_data(&$a_ret = []) {
		$this->get_pools_with_single_data_devices($a_ret);
		$this->get_pools_with_mirrored_data_devices($a_ret);
		return $a_ret;
	}
	public function get_pool_devices_for_attach_data(&$a_ret = []) {
		$this->get_single_data_devices($a_ret);
		$this->get_mirrored_data_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_for_attach_log(&$a_ret = []) {
		$this->get_pools_with_single_log_devices($a_ret);
		$this->get_pools_with_mirrored_log_devices($a_ret);
		return $a_ret;
	}
	public function get_pool_devices_for_attach_log(&$a_ret = []) {
		$this->get_single_log_devices($a_ret);
		$this->get_mirrored_log_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_for_offline_data(&$a_ret = []) {
		$this->get_pools_with_mirrored_data_devices($a_ret);
		$this->get_pools_with_raidz_data_devices($a_ret);
		return $a_ret;
	}
	public function get_pool_devices_for_offline_data(&$a_ret = []) {
		$this->get_mirrored_data_devices($a_ret);
		$this->get_raidz_data_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_for_online_data(&$a_ret = []) {
		$this->get_pools_with_single_data_devices($a_ret);
		$this->get_pools_with_mirrored_data_devices($a_ret);
		$this->get_pools_with_raidz_data_devices($a_ret);
		return $a_ret;
	}
	public function get_pool_devices_for_online_data(&$a_ret = []) {
		$this->get_single_data_devices($a_ret);
		$this->get_mirrored_data_devices($a_ret);
		$this->get_raidz_data_devices($a_ret);
		return $a_ret;
	}
	public function get_pools_for_replace_data(&$a_ret = []) {
		$this->get_pools_with_mirrored_data_devices($a_ret);
		$this->get_pools_with_raidz_data_devices($a_ret);
		return $a_ret;
	}
	public function get_pool_devices_for_replace_data(&$a_ret = []) {
		$this->get_mirrored_data_devices($a_ret);
		$this->get_raidz_data_devices($a_ret);
		return $a_ret;
	}
	private function zdb_row_analyze(string $row) {
		$indent = 0;
		$tag = '';
		$value = '';
		$a_ret = [];
		$a_brackets = null;
		$a_prop = null;
		$a_split = null;
		$a_tag = null;
		$a_tagval = null;
		if(preg_match('~\A(\s*)(.+)\z~',$row,$a_split)): // split string into leading blanks and rest of string, ignore empty strings
			$indent = strlen($a_split[1]);
			if(preg_match('~\A(\S+):\s(.*)\z~',$a_split[2],$a_tagval)): // search for tag with val
				$tag = $a_tagval[1];
				$value = trim($a_tagval[2],"'"); // strip surrounding singe quotes
			elseif(preg_match('~\A(.+):\z~',$a_split[2],$a_tag)): // seach for tag without val
				if(preg_match('~\A([^\[]+)~',$a_tag[1],$a_brackets)): // remove brackets
					$tag = $a_brackets[1];
				endif;
			elseif(preg_match('~\A(.*)(?<!:)\z~',$a_split[2],$a_prop)): // search for property
				$tag = $a_prop[1];
			endif;
		endif;
		$a_ret['indent'] = $indent;
		$a_ret['tag'] = htmlspecialchars($tag,ENT_XML1);
		$a_ret['value'] = htmlspecialchars($value,ENT_XML1);
		return $a_ret;
	}
	private function inject_more_pool_info(array $a_pool) {
//		read pool status and add additional information to the pool
		foreach($a_pool as $r_pool):
			$x_pool = sprintf('/pools/pool[pool_guid="%s"]',$r_pool['pool.guid']);
			$a_pool = $this->a_xml->xpath($x_pool);
			if($a_pool === false):
				continue;
			endif;
			$cmd = sprintf('zpool status %s',escapeshellarg($r_pool['pool.name']));
			$a_row = [];
			$retval = 0;
			mwexec2($cmd,$a_row,$retval);
//			amend pool details
			foreach($a_pool as $r_pool):
//				add spares
				$n_spare = 0;
				$spares_root = $r_pool->addChild('spares');
				foreach($a_row as $r_row):
					$a_match = [];
					if(preg_match_all('/^\t(\s*)spares$/',$r_row,$a_match)):
						$n_spare = strlen($a_match[1][0]) + 2;
						continue;
					endif;
					if($n_spare > 0):
//						read all lines until indent no longer matches
						$a_match = [];
						if(preg_match_all('/^\t(\s+)(\S+)/',$r_row,$a_match)):
							if($n_spare === strlen($a_match[1][0])):
								$spare = htmlspecialchars($a_match[2][0],ENT_XML1,'UTF-8');
								$dev_spare = sprintf('/dev/%s',$spare);
//								temporary ID, to collect guid run zdb -l $dev_spare
								$guid = uuid::create_v4();
								$child_root = $spares_root->addChild('children');
//								it is not confirmed that the device type of the spare device is a disk
								$child_root->addChild('type','disk');
								$child_root->addChild('guid',$guid);
								if(is_numeric($spare[0])):
//									assuming string is a GUID if it starts with a digit
									$child_root->addChild('path',$spare);
									$child_root->addChild('phys_path',$spare);
								else:
									$child_root->addChild('path',$dev_spare);
									$child_root->addChild('phys_path',$dev_spare);
								endif;
								$child_root->addChild('is_spare','1');
							endif;
						else:
							break;
						endif;
					endif;
				endforeach;
//				add cache
				$n_cache = 0;
				$caches_root = $r_pool->addChild('caches');
				foreach($a_row as $r_row):
					$a_match = [];
					if(preg_match_all('/^\t(\s*)cache$/',$r_row,$a_match)):
						$n_cache = strlen($a_match[1][0]) + 2;
						continue;
					endif;
					if($n_cache > 0):
//						read all lines until indent no longer matches
						$a_match = [];
						if(preg_match_all('/^\t(\s+)(\S+)/',$r_row,$a_match)):
							if($n_cache === strlen($a_match[1][0])):
								$cache = htmlspecialchars($a_match[2][0],ENT_XML1,'UTF-8');
								$dev_cache = sprintf('/dev/%s',$cache);
//								temporary ID, to collect guid run zdb -l $dev_cache
								$guid = uuid::create_v4();
								$child_root = $caches_root->addChild('children');
//								it is not confirmed that the device type of the cache device is a disk
								$child_root->addChild('type','disk');
								$child_root->addChild('guid',$guid);
								if(is_numeric($cache[0])):
//									assuming string is a GUID if it starts with a digit
									$child_root->addChild('path',$cache);
									$child_root->addChild('phys_path',$cache);
								else:
									$child_root->addChild('path',$dev_cache);
									$child_root->addChild('phys_path',$dev_cache);
								endif;
								$child_root->addChild('is_cache','1');
								$child_root->addChild('x_type','CACHE');
							endif;
						else:
							break;
						endif;
					endif;
				endforeach;
			endforeach;
		endforeach;
		return true;
	}
	private function adjust_is_log() {
//		vdev_tree: set is_log = 0 on all vdev that are not log vdevs or single log disks
		if($this->configuration_loaded()):
			$x_device = '/pools/pool/vdev_tree/children[is_log="0"]/.//children[not(is_log)]';
			$a_device = $this->a_xml->xpath($x_device);
			if($a_device !== false):
				foreach($a_device as $r_device):
					$r_device->addChild('is_log','0');
				endforeach;
			endif;
		endif;
//		vdev_tree: set is_log = 1 on all vdev that are log vdevs or single log disks
		if($this->configuration_loaded()):
			$x_device = '/pools/pool/vdev_tree/children[is_log="1"]/.//children[not(is_log)]';
			$a_device = $this->a_xml->xpath($x_device);
			if($a_device !== false):
				foreach($a_device as $r_device):
					$r_device->addChild('is_log','1');
				endforeach;
			endif;
		endif;
		return true;
	}
	public function scan_cache() {
//		init object related information
		$this->a_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><pools/>');
		$a_stack = [];
		$n_stack = 0;
		$a_stack[$n_stack] = ['element' => $this->a_xml,'indent' => -1];
		$candidate = [];
//		read cached zfs configuration
		$cmd = '/usr/sbin/zdb -C';
		$a_row = [];
		$retval = 0;
		mwexec2($cmd,$a_row,$retval);
		if($retval !== 0):
//			zdb command error, no pool information is available
			return false;
		endif;
		$old_indent = 0;  // 1st row is expected to have an indent of 0
		foreach($a_row as $r_row):
			$new_item = $this->zdb_row_analyze($r_row);
			if(!preg_match('~\S~',$new_item['tag'])):
				continue;
			endif;
			if(0 === $new_item['indent']):
				$new_item['tag'] = 'pool';
			endif;
			switch($new_item['indent'] <=> $old_indent):
				case 1:
//					indent is greater than old indent
					$n_stack++;
					$a_stack[$n_stack] = $candidate;
					break;
				case -1:
//					indent is lesser than old indent
					while(($n_stack >= 0) && ($a_stack[$n_stack]['indent'] >= $new_item['indent'])):
						$n_stack--;
					endwhile;
					break;
			endswitch;
			$new_element = $a_stack[$n_stack]['element']->addChild($new_item['tag'],$new_item['value']);
			$candidate = ['element' => $new_element,'indent' => $new_item['indent']];
			$old_indent = $new_item['indent'];
		endforeach;
	return true;
	}
	public function scan_ondisk() {
		$tag_open = [];
		$tag_index = -1;
		$c_xml = '';
		$c_xml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$c_xml .= sprintf("<%s>\n",'pools'); // everything starts with pools
		$ap_pool = cli_zpool_info('','name,guid,health'); // get all available pools
		foreach($ap_pool as $rp_pool):
//			get structure of each pool (zdb lists data vdevs and log vdevs but not spare and cache devices)
			$cmd = sprintf('/usr/sbin/zdb -C %s',escapeshellarg($rp_pool['name']));
			$a_row = [];
			$retval = 0;
			mwexec2($cmd,$a_row,$retval);
			if($retval === 0):
//				zdb command successful, pool information is available
				$c_xml .= sprintf("<%s>\n",'pool'); // open pool tag
				$indent_previous = 0;
				foreach($a_row as $r_row):
					$tag = '';
					$b_tag = false;
					$val = '';
					$b_val = false;
//					if(!$b_tag): // check for tag:
/*
 *	Tag detection searches for a string that matches the following criteria:
 *	- starts with one or more blank characters
 *	. followed by a sequence of non-blank characters except colon (:)
 *	- ends with a colon (:)
 */
						unset($a_matches);
						if(preg_match_all('/^(\s+)([^\s:]+):$/',$r_row,$a_matches)): // tag detected
							$indent_current = strlen($a_matches[1][0]);
							$tag = $a_matches[2][0];
							$val = '';
							$b_tag = true;
						endif;
//					endif;
					if(!$b_tag):
//					no tag without value detected, now check for [tag: value]
/*
 * 	the following tags of [tag: value] pairs are currently used:
 *  Summary:	pool_guid, name, version, state, type, id, guid, ashift, asize, is_log, path, phys_path, not_present
 *	pool:		pool_guid, name, version, state
 * 	vdev:		type, id, guid, ashift, asize, is_log
 *	devicevdev:	type, id, ashift, asize, is_log
 *	device		type, id, guid, path, phys_path, not_present
 */
						unset($a_matches);
						if(preg_match_all('/^(\s+)(pool_guid|name|version|state|type|id|guid|ashift|asize|is_log|path|phys_path|not_present):\s(\S.*)$/',$r_row,$a_matches)):
							$indent_current = strlen($a_matches[1][0]);
							$tag = $a_matches[2][0];
							$val = str_replace("'","",trim($a_matches[3][0])); // trim and remove single quotes from value
							$val = htmlspecialchars($val,ENT_XML1 || ENT_NOQUOTES,'UTF-8');
							$b_val = true;
						endif;
					endif;
					if($b_tag || $b_val):
//						remove [(number)] from tag
						unset($a_matches);
						if(preg_match_all('/(\S+)\[\d+\]/',$tag,$a_matches)): // extract tag from tag{index]
							$tag = $a_matches[1][0];
						endif;
						switch($indent_current <=> $indent_previous):
							case -1:
//								level up, we need to close some tags before we process the line
//								determine how many levels must be closed
								$indent_diff = intdiv(($indent_previous - $indent_current),4);
								for($index = 0; $index < $indent_diff; $index++):
									$c_xml .= sprintf("</%s>\n",$tag_open[$tag_index]);
									unset($tag_open[$tag_index]);
									$tag_index--;
								endfor;
								if($b_val): // it's a tag:value pair
									$c_xml .= sprintf("<%s>%s</%s>\n",$tag,$val,$tag);
								else:  // it's a new object
									$tag_index++;
									$tag_open[$tag_index] = $tag;
									$c_xml .= sprintf("<%s>\n",$tag);
								endif;
								$indent_previous = $indent_current;
								break;
							case 0:
//								same level, check for new objects / tag -> value, create tags
								if($b_val): // it's a tag:value pair
									$c_xml .= sprintf("<%s>%s</%s>\n",$tag,$val,$tag);
								else: // it's a new object
									$tag_index++;
									$tag_open[$tag_index] = $tag;
									$c_xml .= sprintf("<%s>\n",$tag);
								endif;
								$indent_previous = $indent_current;
								break;
							case 1:
								if($b_val): // it's a tag:value pair
									$c_xml .= sprintf("<%s>%s</%s>\n",$tag,$val,$tag);
								else: // it's a new object
									$tag_open[$tag_index] = $tag;
									$tag_index++;
									$c_xml .= sprintf("<%s>\n",$tag);
								endif;
								$indent_previous = $indent_current;
								break;
						endswitch;
					endif;
				endforeach;
//				close tags
				for($index = $tag_index; $index > -1; $index--):
					$c_xml .= sprintf("</%s>\n",$tag_open[$tag_index]);
					unset($tag_open[$tag_index]);
					$tag_index--;
				endfor;
//				read pool status and add additional information to the pool
				$cmd = sprintf('zpool status %s',escapeshellarg($rp_pool['name']));
				$a_row = [];
				$retval = 0;
				mwexec2($cmd,$a_row,$retval);
//				add spares
				$c_xml .= sprintf("<%s>\n",'spares');
				$a_spare = [];
				$n_spare = 0;
				foreach($a_row as $r_row):
					$a_match = [];
					if(preg_match_all('/^\t(\s*)spares$/',$r_row,$a_match)):
						$n_spare = strlen($a_match[1][0]) + 2;
						continue;
					endif;
					if($n_spare > 0):
//						read all lines until indent no longer matches
						$a_match = [];
						if(preg_match_all('/^\t(\s+)(\S+)/',$r_row,$a_match)):
							if($n_spare === strlen($a_match[1][0])):
								$a_spare[] = htmlspecialchars($a_match[2][0],ENT_XML1 || ENT_NOQUOTES,'UTF-8');
							endif;
						else:
							break;
						endif;
					endif;
				endforeach;
				foreach($a_spare as $r_spare):
//					it is not confirmed that the spare device is a disk
					$c_xml .= "<children>\n";
					$c_xml .= sprintf("<type>%s</type>\n",'disk');
					$c_xml .= sprintf("<guid>%s</guid>\n",uuid::create_v4()); // temporary ID
					if(is_numeric($r_spare[0])):
						$c_xml .= sprintf("<path>%s</path>\n",$r_spare);
						$c_xml .= sprintf("<phys_path>%s</phys_path>\n",$r_spare);
					else:
//						/dev/ is an assumption
						$c_xml .= sprintf("<path>%s%s</path>\n",'/dev/',$r_spare);
						$c_xml .= sprintf("<phys_path>%s%s</phys_path>\n",'/dev/',$r_spare);
					endif;
					$c_xml .= sprintf("<is_spare>%s</is_spare>\n",'1');
					$c_xml .= '</children>';
				endforeach;
				$c_xml .= sprintf("</%s>\n",'spares');
//				add cache
				$c_xml .= sprintf("<%s>\n",'caches');
				$a_cache = [];
				$n_cache = 0;
				foreach($a_row as $r_row):
					$a_match = [];
					if(preg_match_all('/^\t(\s*)cache$/',$r_row,$a_match)):
						$n_cache = strlen($a_match[1][0]) + 2;
						continue;
					endif;
					if($n_cache > 0):
//						read all lines until indent no longer matches
						$a_match = [];
						if(preg_match_all('/^\t(\s+)(\S+)/',$r_row,$a_match)):
							if($n_cache === strlen($a_match[1][0])):
								$a_cache[] = htmlspecialchars($a_match[2][0],ENT_XML1 || ENT_NOQUOTES,'UTF-8');
							endif;
						else:
							break;
						endif;
					endif;
				endforeach;
				foreach($a_cache as $r_cache):
//					it is not confirmed that the cache device is a disk
					$c_xml .= "<children>\n";
					$c_xml .= sprintf("<type>%s</type>\n",'disk');
					$c_xml .= sprintf("<guid>%s</guid>\n",uuid::create_v4()); // temporary ID
					if(is_numeric($r_cache[0])):
						$c_xml .= sprintf("<path>%s</path>\n",$r_cache);
						$c_xml .= sprintf("<phys_path>%s</phys_path>\n",$r_cache);
					else:
//						/dev/ is an assumption
						$c_xml .= sprintf("<path>%s%s</path>\n",'/dev/',$r_cache);
						$c_xml .= sprintf("<phys_path>%s%s</phys_path>\n",'/dev/',$r_cache);
					endif;
					$c_xml .= sprintf("<is_cache>%s</is_cache>\n",'1');
					$c_xml .= sprintf("<x_type>%s</x_type>\n",'CACHE');
					$c_xml .= '</children>';
				endforeach;
				$c_xml .= sprintf("</%s>\n",'caches'); // end tag of cache
				$c_xml .= sprintf("</%s>\n",'pool'); // end tag of pool
			endif;
		endforeach;
		$c_xml .= sprintf("</%s>\n",'pools'); // final end tag
//		load the string into xml
//		suppress exeptions
		$previous_libxml_use_internal_errors = libxml_use_internal_errors(true);
		libxml_clear_errors();
		$this->a_xml = simplexml_load_string($c_xml);
		if(!$this->configuration_loaded()):
			write_log('zpool tools: An error occured during collecting zpool configuration information, execution aborted.');
			foreach(libxml_get_errors() as $o_error):
				write_log(sprintf('zpool tools: - %s',$o_error->message));
			endforeach;
		endif;
		libxml_clear_errors();
		libxml_use_internal_errors($previous_libxml_use_internal_errors);
		return true;
	}
	public function scan() {
		global $config;
		if(isset($config['zfs']['settings']['scanondisk'])):
			$this->scan_ondisk();
		else:
			$this->scan_cache();
			if($this->configuration_loaded()):
				$a_pool = $this->get_all_pools();
				$this->inject_more_pool_info($a_pool);
			endif;
		endif;
		$this->adjust_is_log();
	}
}
