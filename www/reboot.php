<?php
/*
	reboot.php

	Part of XigmaNAS (http://www.xigmanas.com).
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
require_once 'auth.inc';
require_once 'guiconfig.inc';

$sphere_scriptname = basename(__FILE__);
$sphere_header = 'Location: '.$sphere_scriptname;
$sphere_header_parent = 'Location: index.php';
$gt_reboot = gtext('The server is rebooting now.');
$gt_reboot_confirm = gtext('Are you sure you want to reboot the server?');
$gt_yes = gtext('Yes');
$gt_no = gtext('No');
$cmd_system_reboot = false;
if($_POST):
	if($_POST['submit']):
		switch($_POST['submit']):
			case 'save':
				if(file_exists($d_sysrebootreqd_path)):
					unlink($d_sysrebootreqd_path);
				endif;
				$cmd_system_reboot = true;
				break;
			case 'cancel':
				header($sphere_header_parent);
				exit;
				break;
			default:
				header($sphere_header_parent);
				exit;
				break;
		endswitch;
	endif;
endif;
$pgtitle = [gtext('System'),gtext('Reboot'),gtext('Now')];
?>
<?php include 'fbegin.inc';?>
<script type="text/javascript">
//<![CDATA[
$(window).on("load", function() {
<?php // Init spinner onsubmit().?>
	$("#iform").submit(function() { spinner(); });
});
//]]>
</script>
<table id="area_navigator"><tbody><tr><td class="tabnavtbl">
	<ul id="tabnav">
		<li class="tabact"><a href="reboot.php" title="<?=gtext('Reload page');?>"><span><?=gtext('Now');?></span></a></li>
		<li class="tabinact"><a href="reboot_sched.php"><span><?=gtext('Scheduled');?></span></a></li>
	</ul>
</td></tr></tbody></table>
<table id="area_data"><tbody><tr><td id="area_data_frame"><form action="<?=$sphere_scriptname;?>" method="post" name="iform" id="iform">
	<?php
	if($cmd_system_reboot):
		echo print_info_box($gt_reboot);
	endif;
	?>
	<table class="area_data_selection">
		<colgroup>
			<col style="width:100%">
		</colgroup>
		<thead>
			<?php html_titleline2(gtext('Reboot'),1);?>
		</thead>
	</table>
	<?php
	if(!$cmd_system_reboot):
		echo print_warning_box($gt_reboot_confirm);
		echo '<div id="submit">';
			echo html_button('save',$gt_yes);
			echo html_button('cancel',$gt_no);
		echo '</div>';
	endif;
	include 'formend.inc';
	?>
</form></td></tr></tbody></table>
<?php
include 'fend.inc';
if($cmd_system_reboot):
	ob_flush();
	flush();
	sleep(5);
	system_reboot();
endif;
?>
