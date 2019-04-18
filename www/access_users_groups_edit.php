<?php
/*
	access_users_groups_edit.php

	Part of XigmaNAS (https://www.xigmanas.com).
	Copyright (c) 2018-2019 XigmaNAS <info@xigmanas.com>.
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
require_once 'auth.inc';
require_once 'guiconfig.inc';

if (isset($_GET['uuid']))
	$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = [gtext('Access'),gtext('Groups'),isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_group = &array_make_branch($config,'access','group');
if(empty($a_group)):
else:
	array_sort_key($a_group,'name');
endif;
$a_group_system = system_get_group_list();

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_group, "uuid")))) {
	$pconfig['uuid'] = $a_group[$cnid]['uuid'];
	$pconfig['groupid'] = $a_group[$cnid]['id'];
	$pconfig['name'] = $a_group[$cnid]['name'];
	$pconfig['desc'] = $a_group[$cnid]['desc'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['groupid'] = get_nextgroup_id();
	$pconfig['name'] = "";
	$pconfig['desc'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: access_users_groups.php");
		exit;
	}

	// Input validation
	$reqdfields = ['name','groupid','desc'];
	$reqdfieldsn = [
		gtext('Name'),
		gtext('Group ID'),
		gtext('Description')
	];
	$reqdfieldst = ['string','numeric','string'];
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	if (($_POST['name'] && !is_domain($_POST['name']))) {
		$input_errors[] = gtext("The group name contains invalid characters.");
	}

	if (($_POST['desc'] && !is_validdesc($_POST['desc']))) {
		$input_errors[] = gtext("The group description contains invalid characters.");
	}

	// Check for name conflicts. Only check if group is created.
	if (!(isset($uuid) && (FALSE !== $cnid)) &&
		((is_array($a_group_system) && array_key_exists($_POST['name'], $a_group_system)) ||
		(is_array($a_group_system) && in_array($_POST['groupid'], $a_group_system)) ||
		(false !== array_search_ex($_POST['name'], $a_group, "name")))) {
		$input_errors[] = gtext("This group already exists in the group list.");
	}

	// Validate if ID is unique. Only check if user is created.
	if (!(isset($uuid) && (FALSE !== $cnid)) && (false !== array_search_ex($_POST['groupid'], $a_group, "id"))) {
		$input_errors[] = gtext("The unique group ID is already used.");
	}

	if (empty($input_errors)) {
		$groups = [];
		$groups['uuid'] = $_POST['uuid'];
		$groups['id'] = $_POST['groupid'];
		$groups['name'] = $_POST['name'];
		$groups['desc'] = $_POST['desc'];

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_group[$cnid] = $groups;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_group[] = $groups;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("userdb_group", $mode, $groups['uuid']);
		write_config();

		header("Location: access_users_groups.php");
		exit;
	}
}

// Get next group id.
// Return next free user id.
function get_nextgroup_id() {
	global $config;

	// Get next free user id.
	exec("/usr/sbin/pw groupnext", $output);
	$output = explode(":", $output[0]);
	$id = intval($output[0]);

	// Check if id is already in usage. If the user did not press the 'Apply'
	// button 'pw' did not recognize that there are already several new users
	// configured because the user db is not updated until 'Apply' is pressed.
	$a_group = $config['access']['group'];
	if (false !== array_search_ex(strval($id), $a_group, "id")) {
		do {
			$id++; // Increase id until a unused one is found.
		} while (false !== array_search_ex(strval($id), $a_group, "id"));
	}

	return $id;
}
include 'fbegin.inc';
$document = new co_DOMDocument();
$document->
	add_area_tabnav()->
		add_tabnav_upper()->
			ins_tabnav_record('access_users.php',gettext('Users'))->
			ins_tabnav_record('access_publickey.php',gettext('Public Keys'))->
			ins_tabnav_record('access_users_groups.php',gettext('Groups'),gettext('Reload page'),true);
$document->render();
?>
<form action="access_users_groups_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()"><table id="area_data"><tbody><tr><td id="area_data_frame">
<?php
	if(!empty($input_errors)):
		print_input_errors($input_errors);
	endif;
?>
	<table class="area_data_settings">
		<colgroup>
			<col class="area_data_settings_col_tag">
			<col class="area_data_settings_col_data">
		</colgroup>
		<thead>
<?php
			html_titleline2(gettext("Group Settings"));
?>
		</thead>
		<tbody>
<?php
			html_inputbox2('name',gettext('Name'),$pconfig['name'],gettext('Enter a group name.'),true,28,isset($uuid) && (false !== $cnid));
			html_inputbox2('groupid',gettext('Group ID'),$pconfig['groupid'],gettext('Group numeric id.'),true,12,isset($uuid) && (false !== $cnid));
			html_inputbox2('desc',gettext('Description'),$pconfig['desc'],gettext('Enter a group description.'),true,28);
?>
		</tbody>
	</table>
	<div id="submit">
		<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (false !== $cnid)) ? gtext('Save') : gtext('Add')?>" />
		<input name="Cancel" type="submit" class="formbtn" value="<?=gtext('Cancel');?>" />
		<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
	</div>
<?php
	include 'formend.inc';
?>
</td></tr></tbody></table></form>
<?php
include 'fend.inc';
