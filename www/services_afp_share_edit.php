<?php
/*
	services_afp_share_edit.php

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
require_once 'auth.inc';
require_once 'guiconfig.inc';

if (isset($_GET['uuid']))
	$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = [gtext('Services'),gtext('AFP'),gtext('Share'), isset($uuid) ? gtext('Edit') : gtext('Add')];

$a_mount = &array_make_branch($config,'mounts','mount');
if(empty($a_mount)):
else:
	array_sort_key($a_mount,'devicespecialfile');
endif;

$a_share = &array_make_branch($config,'afp','share');
if(empty($a_share)):
else:
	array_sort_key($a_share,'name');
endif;

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_share, "uuid")))) {
	$pconfig['uuid'] = $a_share[$cnid]['uuid'];
	$pconfig['name'] = $a_share[$cnid]['name'];
	$pconfig['path'] = $a_share[$cnid]['path'];
	$pconfig['comment'] = $a_share[$cnid]['comment'];
	$pconfig['volpasswd'] = $a_share[$cnid]['volpasswd'];
	$pconfig['volcharset'] = $a_share[$cnid]['volcharset'];
	$pconfig['allow'] = $a_share[$cnid]['allow'];
	$pconfig['deny'] = $a_share[$cnid]['deny'];
	$pconfig['rolist'] = $a_share[$cnid]['rolist'];
	$pconfig['rwlist'] = $a_share[$cnid]['rwlist'];
        if (isset($a_share[$cnid]['auxparam']) && is_array($a_share[$cnid]['auxparam']))
		$pconfig['auxparam'] = implode("\n", $a_share[$cnid]['auxparam']);
	$pconfig['timemachine'] = isset($a_share[$cnid]['timemachine']);
	$pconfig['volsizelimit'] = $a_share[$cnid]['volsizelimit'];

} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['path'] = "";
	$pconfig['comment'] = "";
	$pconfig['volpasswd'] = '';
	$pconfig['volcharset'] = 'UTF8';
	$pconfig['allow'] = '';
	$pconfig['deny'] = '';
	$pconfig['rolist'] = '';
	$pconfig['rwlist'] = '';
        $pconfig['auxparam'] = "";
	$pconfig['timemachine'] = false;
	$pconfig['volsizelimit'] = "";
	
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if (isset($_POST['Cancel']) && $_POST['Cancel']) {
		header("Location: services_afp_share.php");
		exit;
	}

	// Input validation.
	$reqdfields = ['name','comment'];
	$reqdfieldsn = [gtext('Name'),gtext('Comment')];
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);
	$reqdfieldst = ['string','string'];
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors);

	// Verify that the share password is not more than 8 characters.
	if (strlen($_POST['volpasswd']) > 8) {
	    $input_errors[] = gtext("Share passwords can not be more than 8 characters.");
	}

	// Check volume size limit
	if (!empty($_POST['volsizelimit']) && !is_numericint($_POST['volsizelimit'])) {
		$input_errors[] = sprintf(gtext("The attribute '%s' must be a number."), gtext("Volume Size Limit"));
	}

	// Check for duplicates.
	$index = array_search_ex($_POST['name'], $a_share, "name");
	if (FALSE !== $index) {
		if (!((FALSE !== $cnid) && ($a_share[$cnid]['uuid'] === $a_share[$index]['uuid']))) {
			$input_errors[] = gtext("The share name is already used.");
		}
	}

	if (empty($input_errors)) {
		$share = [];
		$share['uuid'] = $_POST['uuid'];
		$share['name'] = $_POST['name'];
		$share['path'] = $_POST['path'];
		$share['comment'] = $_POST['comment'];
		$share['volpasswd'] = $_POST['volpasswd'];
		$share['volcharset'] = $_POST['volcharset'];
		$share['allow'] = $_POST['allow'];
		$share['deny'] = $_POST['deny'];
		$share['rolist'] = $_POST['rolist'];
		$share['rwlist'] = $_POST['rwlist'];
		$share['timemachine'] = isset($_POST['timemachine']) ? true : false;
		$share['volsizelimit'] = $_POST['volsizelimit'];

		// Write additional parameters.
		unset($share['auxparam']);
		foreach (explode("\n", $_POST['auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$share['auxparam'][] = $auxparam;
		}

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_share[$cnid] = $share;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_share[] = $share;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("afpshare", $mode, $share['uuid']);
		write_config();

		header("Location: services_afp_share.php");
		exit;
	}
}
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
<!--
function adisk_change() {
	switch (document.iform.adisk_enable.checked) {
		case false:
			showElementById('adisk_advf_tr','hide');
			break;

		case true:
			showElementById('adisk_advf_tr','show');
			break;
	}
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="services_afp.php"><span><?=gtext("Settings");?></span></a></li>
				<li class="tabact"><a href="services_afp_share.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Shares");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="services_afp_share_edit.php" method="post" name="iform" id="iform" onsubmit="spinner()">
				<?php if (!empty($input_errors)) print_input_errors($input_errors); ?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<?php html_titleline(gtext("Share Settings"));?>
				<tr>
					<td width="22%" valign="top" class="vncellreq"><?=gtext("Name");?></td>
					<td width="78%" class="vtable">
					<input name="name" type="text" class="formfld" id="name" size="30" value="<?=htmlspecialchars($pconfig['name']);?>" />
				</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncellreq"><?=gtext("Comment");?></td>
					<td width="78%" class="vtable">
					<input name="comment" type="text" class="formfld" id="comment" size="30" value="<?=htmlspecialchars($pconfig['comment']);?>" />
				</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncellreq"><?=gtext("Path");?></td>
					<td width="78%" class="vtable">
					<input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlspecialchars($pconfig['path']);?>" />
					<input name="browse" type="button" class="formbtn" id="Browse" onclick='ifield = form.path; filechooser = window.open("filechooser.php?p="+encodeURIComponent(ifield.value)+"&amp;sd=<?=$g['media_path'];?>", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." /><br />
					<span class="vexpl"><?=gtext("Path to be shared.");?></span>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Share Password");?></td>
					<td width="78%" class="vtable">
					<input name="volpasswd" type="text" class="formfld" id="volpasswd" size="16" value="<?=htmlspecialchars($pconfig['volpasswd']);?>" />
					<?=gtext("Set share password.");?><br />
					<span class="vexpl"><?=gtext("This controls the access to this share with an access password.");?></span>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Share Character Set");?></td>
					<td width="78%" class="vtable">
					<input name="volcharset" type="text" class="formfld" id="volcharset" size="16" value="<?=htmlspecialchars($pconfig['volcharset']);?>" /><br />
					<span class="vexpl"><?=gtext("Specifies the share character set. For example UTF8, UTF8-MAC, ISO-8859-15, etc.");?></span>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Allow");?></td>
					<td width="78%" class="vtable">
					<input name="allow" type="text" class="formfld" id="allow" size="60" value="<?=htmlspecialchars($pconfig['allow']);?>" /><br />
					<?=gtext("This option allows the users and groups that access a share to be specified. Users and groups are specified, delimited by commas. Groups are designated by a @ prefix.");?>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Deny");?></td>
					<td width="78%" class="vtable">
					<input name="deny" type="text" class="formfld" id="deny" size="60" value="<?=htmlspecialchars($pconfig['deny']);?>" /><br />
					<?=gtext("The  deny  option specifies users and groups who are not allowed access to the share. It follows the same  format  as  the  allow option.");?>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Read Only Access");?></td>
					<td width="78%" class="vtable">
					<input name="rolist" type="text" class="formfld" id="rolist" size="60" value="<?=htmlspecialchars($pconfig['rolist']);?>" /><br />
					<?=gtext("Allows certain users and groups to have read-only  access  to  a share. This follows the allow option format.");?>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Read/Write Access");?></td>
					<td width="78%" class="vtable">
					<input name="rwlist" type="text" class="formfld" id="rwlist" size="60" value="<?=htmlspecialchars($pconfig['rwlist']);?>" /><br />
					<?=gtext("Allows  certain  users and groups to have read/write access to a share. This follows the allow option format.");?>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Time Machine");?></td>
					<td width="78%" class="vtable">
					<input name="timemachine" type="checkbox" id="timemachine" value="yes" <?php if (!empty($pconfig['timemachine'])) echo "checked=\"checked\""; ?> />
					<?=gtext("Enable Time Machine support");?>
					</td>
			</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gtext("Volume Size Limit");?></td>
					<td width="78%" class="vtable">
					<input name='volsizelimit' type='text' class='formfld' id='volsizelimit' size='10' value="<?=htmlspecialchars($pconfig['volsizelimit']);?>" /> <?=gtext("MiB");?>
					</td>
			</tr>
				<tr>
					<?php
					$helpinghand = '<a href="'
					. 'http://netatalk.sourceforge.net/3.1/htmldocs/afp.conf.5.html'
					. '" target="_blank">'
					. gtext('Please check the documentation')
					. '</a>.';
					html_textarea("auxparam", gtext("Additional Parameters"), $pconfig['auxparam'], sprintf(gtext("Add any supplemental parameters.")) . " " . $helpinghand, false, 65, 5, false, false);
					?>
			</tr>
			</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gtext("Save") : gtext("Add")?>" />
					<input name="Cancel" type="submit" class="formbtn" value="<?=gtext("Cancel");?>" />
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
			</div>
			<?php include 'formend.inc';?>
		</form>
	</td>
</tr>
</table>
<script type="text/javascript">
<!--
adisk_change();
//-->
</script>
<?php include 'fend.inc';?>
