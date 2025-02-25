<?php
/*
	system_packages.php

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
require_once 'packages.inc';

$a_packages = packages_get_installed();

if (isset($_GET['act']) && $_GET['act'] == "del") {
	if ($a_packages[$_GET['id']]) {
		packages_uninstall($a_packages[$_GET['id']]['name']);
		header("Location: system_packages.php");
		exit;
	}
}
$pgtitle = [gtext('System'),gtext('Packages')];
include 'fbegin.inc';
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="system_packages.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Packages");?></span></a></li>
	</ul></td></tr>
	<tr><td class="tabcont">
		<form action="system_packages.php" method="post" name="iform" id="iform">
<?php
			if(!empty($input_errors)):
				print_input_errors($input_errors);
			endif;
			if(!empty($savemsg)):
				print_info_box($savemsg);
			endif;
			if(file_exists($d_packagesconfdirty_path)):
				print_config_change_box();
			endif;
?>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
				html_titleline2(gettext('Overview'), 3);
?>
				<tr>
					<td width="40%" class="listhdrlr"><?=gtext("Package Name");?></td>
					<td width="50%" class="listhdrr"><?=gtext("Description");?></td>
					<td width="10%" class="list"></td>
				</tr>
<?php
				$i = 0;
				foreach($a_packages as $packagev):
?>
					<tr>
						<td class="listr"><?=htmlspecialchars($packagev['name']);?>&nbsp;</td>
						<td class="listbg"><?=htmlspecialchars($packagev['desc']);?>&nbsp;</td>
						<td valign="middle" nowrap="nowrap" class="list"> <a href="system_packages.php?act=del&amp;id=<?=$i;?>" onclick="return confirm('<?=gtext("Do you really want to uninstall this package?"); ?>')"><img src="images/delete.png" title="<?=gtext("Uninstall package"); ?>" border="0" alt="<?=gtext("Uninstall package"); ?>" /></a></td>
					</tr>
<?php
				$i++;
				endforeach;
?>
				<tr>
					<td class="list" colspan="2"></td>
					<td class="list"> <a href="system_packages_edit.php"><img src="images/add.png" title="<?=gtext("Install package"); ?>" border="0" alt="<?=gtext("Install package"); ?>" /></a></td>
				</tr>
			</table>
<?php
			include 'formend.inc';
?>
		</form>
	</td></tr>
</table>
<?php
include 'fend.inc';
