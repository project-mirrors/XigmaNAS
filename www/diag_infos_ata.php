<?php
/*
	diag_infos_ata.php

	Part of NAS4Free (http://www.nas4free.org).
	Copyright (c) 2012-2016 The NAS4Free Project <info@nas4free.org>.
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
	of the authors and should not be interpreted as representing official policies,
	either expressed or implied, of the NAS4Free Project.
*/
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext_gen2("Diagnostics"), gettext_gen2("Information"), gettext_gen2("Disks (ATA)"));

$a_disk = &$config['disks']['disk'];
$disk_error = gettext_gen2("No disks configured, please add disks to see the diagnostic information of disks!");
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="diag_infos.php"><span><?=gettext_gen2("Disks");?></span></a></li>
				<li class="tabact"><a href="diag_infos_ata.php" title="<?=gettext_gen2("Reload page");?>"><span><?=gettext_gen2("Disks (ATA)");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_part.php"><span><?=gettext_gen2("Partitions");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_smart.php"><span><?=gettext_gen2("S.M.A.R.T.");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_space.php"><span><?=gettext_gen2("Space Used");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_mount.php"><span><?=gettext_gen2("Mounts");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_raid.php"><span><?=gettext_gen2("Software RAID");?></span></a></li>
		  </ul>
	  </td>
	</tr>
  <tr>
		<td class="tabnavtbl">
		  <ul id="tabnav2">
				<li class="tabinact"><a href="diag_infos_iscsi.php"><span><?=gettext_gen2("iSCSI Initiator");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_ad.php"><span><?=gettext_gen2("MS Domain");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_samba.php"><span><?=gettext_gen2("CIFS/SMB");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_ftpd.php"><span><?=gettext_gen2("FTP");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_rsync_client.php"><span><?=gettext_gen2("RSYNC Client");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_swap.php"><span><?=gettext_gen2("Swap");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_sockets.php"><span><?=gettext_gen2("Sockets");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_ipmi.php"><span><?=gettext_gen2('IPMI Stats');?></span></a></li>
				<li class="tabinact"><a href="diag_infos_ups.php"><span><?=gettext_gen2("UPS");?></span></a></li>
			</ul>
		</td>
	</tr>
  <tr>
    <td class="tabcont">
    	<table width="100%" border="0">
			<?php 
				if ($a_disk == "") {
    				html_titleline(gettext_gen2("Disks (ATA) information"));
					echo "<tr><td><br />".$disk_error."</td></tr>\n";
				} else 
					{
					foreach($a_disk as $diskk => $diskv):
					html_titleline(sprintf(gettext_gen2("Device /dev/%s - %s"), $diskv['name'], $diskv['desc']));
					echo "<tr>\n";
						echo "<td>\n";
							echo "<pre>\n";
								$name = $diskv['name'];
								$device = $diskv['devicespecialfile'];

								// Display more information
								exec("/usr/local/sbin/ataidle {$device}", $rawdata);
								$rawdata = array_slice($rawdata, 1);
								echo htmlspecialchars(implode("\n", $rawdata));
								unset($rawdata);
							echo "</pre>\n";
						echo "</td>\n";
					echo "</tr>\n";
					endforeach;
				}
			?>
    	</table>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
