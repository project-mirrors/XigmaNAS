<?php
/*
	disks_mount_fsck.php

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

$gt_remark_1 = gtext('A mounted disk or partition will be unmounted temporarily to perform the requested action.')
	. '<br />'
	. gtext('Users will notice a service disruption when trying to access this mount while the file system check is running.');

$pgtitle = [gtext('Disks'),gtext('Mount Point'),gtext('Fsck')];

$a_mount = &array_make_branch($config,'mounts','mount');
if(empty($a_mount)):
else:
	array_sort_key($a_mount,'devicespecialfile');
endif;

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_action);

	// Input validation
	$reqdfields = ['disk'];
	$reqdfieldsn = [gtext('Disk')];
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, $input_errors);

	if (empty($input_errors)) {
		$do_action = true;
		$disk = $_POST['disk'];
		$umount = isset($_POST['umount']) ? true : false;
	}
}

if (!isset($do_action)) {
	$do_action = false;
	$disk = '';
	$umount = false;
}
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_mount.php"><span><?=gtext("Management");?></span></a></li>
				<li class="tabinact"><a href="disks_mount_tools.php"><span><?=gtext("Tools");?></span></a></li>
				<li class="tabact"><a href="disks_mount_fsck.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Fsck");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<?php if ($input_errors) print_input_errors($input_errors);?>
			<form action="disks_mount_fsck.php" method="post" name="iform" id="iform" onsubmit="spinner()">
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<?php html_titleline(gtext("Mount Point Fsck"));?>
					<tr>
						<td valign="top" class="vncellreq"><?=gtext("Disk");?></td>
						<td class="vtable">
							<select name="disk" class="formfld" id="disk">
								<option value=""><?=gtext("Must choose one");?></option>
								<?php foreach ($a_mount as $mountv):?>
									<?php if (strcmp($mountv['fstype'],"cd9660") == 0) continue;?>
									<option value="<?=$mountv['devicespecialfile'];?>" <?php if ($mountv['devicespecialfile'] === $disk) echo "selected=\"selected\"";?>>
										<?php echo htmlspecialchars($mountv['sharename'] . ": " . $mountv['devicespecialfile']);?>
									</option>
								<?php endforeach;?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"></td>
						<td width="78%" class="vtable">
							<input name="umount" type="checkbox" id="umount" value="yes" <?php if (!empty($umount)) echo "checked=\"checked\""; ?> />
								<strong><?=gtext("Unmount Disk/Partition");?></strong><span class="vexpl"><br /><?=$gt_remark_1;?></span>
						</td>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Execute");?>" />
				</div>
				<?php
				if($do_action) {
					echo(sprintf("<div id='cmdoutput'>%s</div>", gtext("Command output:")));
					echo('<pre class="cmdoutput">');
					//ob_end_flush();
					/* Check filesystem */
					$result = disks_fsck($disk,$umount);
					/* Display result */
					echo((0 == $result) ? gtext("Successful") : gtext("Failed"));
					echo('</pre>');
				}
				?>
				<div id="remarks">
					<?php html_remark("note", gtext("Note"), gtext("You can't unmount a drive which is used by swap file, a iSCSI-target file or any other running process!"));?>
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
