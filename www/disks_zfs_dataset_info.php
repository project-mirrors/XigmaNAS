<?php
/*
	disks_zfs_dataset_info.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';

/**
 *	Returns basic properties of a single zfs filesystem or all zfs filesystems.
 *	@param string $entity_name If provided, only basic information of this specific zfs filesystem is returned.
 *	@return string An unescaped string.
 */
function clget_zfs_filesystem_list(string $entity_name = NULL): string {
	$a_cmd = ['zfs','list','-t','filesystem','-o','name,used,avail,refer,mountpoint'];
	if(isset($entity_name)):
		$a_cmd[] = \escapeshellarg($entity_name);
	endif;
	$a_cmd[] = '2>&1';
	$cmd = \implode(' ',$a_cmd);
//	unset($output);
	\mwexec2($cmd,$output);
	return \implode("\n",$output);
}
/**
 *	Returns all properties of a single zfs filesystem or all zfs filesystems.
 *	@param string $entity_name If provided, only the properties of this specific zfs filesystem are returned.
 *	@return string An unescaped string.
 */
function clget_zfs_filesystem_properties(string $entity_name = NULL): string {
	$a_cmd = ['zfs','list','-H','-o','name','-t','filesystem'];
	if(isset($entity_name)):
		$a_cmd[] = \escapeshellarg($entity_name);
	endif;
	$a_cmd[] = '2>&1';
	$cmd = \implode(' ',$a_cmd);
//	unset($a_names);
	\mwexec2($cmd,$a_names);
	if(\is_array($a_names) && \count($a_names) > 0):
		$names = \implode(' ',\array_map('escapeshellarg',$a_names));
		$cmd = \sprintf('zfs get all %s 2>&1',$names);
//		unset($output);
		\mwexec2($cmd,$output);
	else:
		$output = [\gettext('No ZFS filesystem information available.')];
	endif;
	return \implode("\n",$output);
}
/**
 *	Returns the full qualified ZFS filesystem name of $_GET['uuid'] or NULL.
 *	@global array $config The global config file.
 *	@return string|null
 */
function cfget_zfs_filesystem_name_of_uuid(string $uuid): ?string {
	global $config;

	$entity_name = NULL;
	$sphere_array = &\array_make_branch($config,'zfs','datasets','dataset');
	$sphere_rowid = \array_search_ex($uuid,$sphere_array,'uuid');
	if($sphere_rowid !== false):
		$sphere_record = $sphere_array[$sphere_rowid];
		$sr_pool = $sphere_record['pool'][0] ?? NULL;
		$sr_name = $sphere_record['name'] ?? NULL;
		if(isset($sr_pool) && isset($sr_name) && \is_string($sr_pool) && \is_string($sr_name)):
			$entity_name = \sprintf('%s/%s',$sr_pool,$sr_name);
		endif;
	endif;
	return $entity_name;
}
if(isset($_GET['uuid']) && \is_string($_GET['uuid']) && \is_uuid_v4($_GET['uuid'])):
//	collect information from a single zfs filesystem
	$uuid = $_GET['uuid'];
	$entity_name = cfget_zfs_filesystem_name_of_uuid($uuid);
	if(isset($entity_name)):
		$status = [
			'area_refresh_list' => clget_zfs_filesystem_list($entity_name),
			'area_refresh_properties' => clget_zfs_filesystem_properties($entity_name)
		];
	else:
		$status = [
			'area_refresh_list' => \gettext('ZFS filesystem not found.'),
			'area_refresh_properties' => \gettext('No ZFS filesystem properties available.')
		];
	endif;
	$json_string = \json_encode(['submit' => 'inform','uuid' => $uuid]);
else:
//	collect information from all zfs filesystems
	$entity_name = NULL;
	$status = [
		'area_refresh_list' => clget_zfs_filesystem_list(),
		'area_refresh_properties' => clget_zfs_filesystem_properties()
	];
	$json_string = 'null';
endif;
if(\is_ajax()):
	\render_ajax($status);
endif;
$pgtitle = [\gettext('Disks'),\gettext('ZFS'),\gettext('Datasets'),\gettext('Information')];
if(isset($entity_name)):
	$pgtitle[] = $entity_name;
endif;
$document = \new_page($pgtitle);
//	add tab navigation
$document->
	add_area_tabnav()->
		push()->
		add_tabnav_upper()->
			ins_tabnav_record('disks_zfs_zpool.php',\gettext('Pools'))->
			ins_tabnav_record('disks_zfs_dataset.php',\gettext('Datasets'),\gettext('Reload page'),true)->
			ins_tabnav_record('disks_zfs_volume.php',\gettext('Volumes'))->
			ins_tabnav_record('disks_zfs_snapshot.php',\gettext('Snapshots'))->
			ins_tabnav_record('disks_zfs_config.php',\gettext('Configuration'))->
			ins_tabnav_record('disks_zfs_settings.php',\gettext('Settings'))->
		pop()->
		add_tabnav_lower()->
			ins_tabnav_record('disks_zfs_dataset.php',\gettext('Dataset'))->
			ins_tabnav_record('disks_zfs_dataset_info.php',\gettext('Information'),\gettext('Reload page'),true);
//	get areas
$body = $document->getElementById('main');
$pagecontent = $document->getElementById('pagecontent');
//	create data area
$content = $pagecontent->add_area_data();
$content->
	add_table_data_settings()->
		ins_colgroup_data_settings()->
		push()->
		addTHEAD()->
			c2_titleline(\gettext('ZFS Filesystem Information & Status'))->
		last()->
		addTBODY()->
			addTR()->
				insTDwC('celltag',\gettext('Information & Status'))->
				addTDwC('celldata')->
					addElement('pre',['class' => 'cmdoutput'])->
						insSPAN(['id' => 'area_refresh_list'],$status['area_refresh_list'])->
		pop()->
		addTFOOT()->
			c2_separator();
$content->
	add_table_data_settings()->
		ins_colgroup_data_settings()->
		push()->
		addTHEAD()->
			c2_titleline(\gettext('ZFS Filesystem Properties'))->
		pop()->
		addTBODY()->
			addTR()->
				insTDwC('celltag',\gettext('Properties'))->
				addTDwC('celldata')->
					addElement('pre',['class' => 'cmdoutput'])->
						insSPAN(['id' => 'area_refresh_properties'],$status['area_refresh_properties']);
//	add additional javascript code
$js_document_ready = <<<EOJ
	var gui = new GUI;
	gui.recall(30000,30000,'disks_zfs_dataset_info.php',$json_string,function(data) {
		if($('#area_refresh_list').length > 0) {
			$('#area_refresh_list').text(data.area_refresh_list);
		}
		if($('#area_refresh_properties').length > 0) {
			$('#area_refresh_properties').text(data.area_refresh_properties);
		}
	});
EOJ;
$body->add_js_document_ready($js_document_ready);
$document->render();
