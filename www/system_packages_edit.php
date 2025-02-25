<?php
/*
	system_packages_edit.php

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

if (isset($_POST) && $_POST) {
	unset($input_errors);

	if (is_uploaded_file($_FILES['ulfile']['tmp_name'])) {
		if (!file_exists($_FILES['ulfile']['tmp_name'])) {
			// Probably out of memory for the MFS.
			$input_errors[] = gtext("Package upload failed (out of memory?)");
		} else {
			// Check whether package is already installed.
			if (0 == packages_is_installed($_FILES['ulfile']['name'])) {
				$input_errors[] = gtext("Package is already installed.");
			} else {
				$packagename = "{$g['tmp_path']}/{$_FILES['ulfile']['name']}";

				// Move the image so PHP won't delete it.
				@rename($_FILES['ulfile']['tmp_name'], $packagename);

				$do_action = true;
			}
		}
	}
}

if(!isset($do_action)) {
	$do_action = false;
	$packagename = "";
}
$pgtitle = [gtext('System'),gtext('Packages'),gtext('Install')];
?>
<?php include 'fbegin.inc';?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl"><ul id="tabnav">
		<li class="tabact"><a href="system_packages.php" title="<?=gtext('Reload page');?>"><span><?=gtext("Packages");?></span></a></li>
	</ul></td></tr>
	<tr>
		<td class="tabcont">
			<form action="system_packages_edit.php" method="post" enctype="multipart/form-data">
				<?php if (!empty($input_errors)) print_input_errors($input_errors); ?>
				<?php if (!empty($savemsg)) print_info_box($savemsg); ?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<?php html_titleline(gtext("Packages Installer"));?>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gtext("Package file");?></td>
						<td width="78%" class="vtable">
							<input name="ulfile" type="file" class="formfld" />
							<br /><?=gtext("Select the FreeBSD package to be installed.");?>
						</td>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gtext("Install")?>" />
				</div>
				<?php
				if($do_action):
					echo(sprintf("<div id='cmdoutput'>%s</div>",gtext('Command output:')));
					echo('<pre class="cmdoutput">');
						//ob_end_flush();
						ob_start();
						// Install package.
						packages_install($packagename);
						// Delete file.
						@unlink($packagename);
						$cmdoutput = ob_get_contents();
						ob_end_clean();
						echo htmlspecialchars($cmdoutput);
					echo('</pre>');
				endif;
				?>
				<div id="remarks">
					<?php
					$helpinghand = gtext('You can also install a package via SSH or console using the pkg install command.')
						. '<br />'
						. gtext('Example: pkg install packagename.');
					html_remark("note", gtext('Note'), $helpinghand);
					?>
				</div>
				<?php include 'formend.inc';?>
			</form>
		</td>
	</tr>
</table>
<?php include 'fend.inc';?>
