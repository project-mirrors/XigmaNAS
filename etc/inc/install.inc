<?php
/*
	install.inc

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
require_once 'functions.inc';
require_once 'util.inc';
require_once 'xmlparse.inc';

/**
 *	Check image file
 *	@param string $dev
 *	@param string $pt
 *	@param string $img
 *	@return int Return 0 if successful, otherwise 1
 */
function check_install_image($dev,$pt,$img) {
	$mntpath = '/mnt';
	if(!file_exists("{$dev}{$pt}")):
		return 1;
	endif;
	if(0 != mwexec("/sbin/mount -r {$dev}{$pt} {$mntpath}")):
		return 1;
	endif;
	if(!file_exists("{$mntpath}/{$img}")):
		mwexec("/sbin/umount {$mntpath}");
		return 1;
	endif;
	mwexec("/sbin/umount {$mntpath}");
	return 0;
}
/**
 *	Search the drive that contains upgrade image.
 *	@global array $g
 *	@return array
 */
function get_install_source_drive() {
	global $g;

	$mntpath = '/mnt';
	$imgfile = get_product_name(). "-{$g['arch']}-embedded.xz";
	$cdlist = get_cdrom_list();
	$dalist = get_scsi_disks_list();
	$ptlist = ['a','s1a','s4a','p1','p2','p3'];
	$disklist = $cdlist;
	foreach ($dalist as $disk):
		$dev = $disk['devicespecialfile'];
		foreach($ptlist as $pt):
			if(0 == check_install_image($dev,$pt,$imgfile)):
				$diskname = $disk['name'];
				$disklist[$diskname] = [
					'name' => $diskname,
					'devicespecialfile' => "{$dev}{$pt}",
					'type' => 'UFS',
					'size' => 'NA',
					'desc' => $disk['desc'],
					'serial' => $disk['desc']
				];
				break;
			endif;
		endforeach;
	endforeach;
	return $disklist;
}
/**
 *	Mount the CDROM.
 *	@param array $drive
 *	@return int Return 0 if successful, otherwise 1
 */
function install_mount_cd(array $drive) {
	write_console("Mount CDROM/USB.\n");
	$mntpath = '/mnt/cdrom';
	$cdrom = $drive['name'];
//	Creating temporary directory to mount CDROM.
	if(1 == mwexec("/bin/mkdir -p {$mntpath}")):
		write_console("Error: Failed to create directory '{$mntpath}'!\n");
		return 1;
	endif;
	if($drive['type'] == 'UFS'):
//		Mounting USB stick
		if(1 == mwexec("/sbin/mount -r {$drive['devicespecialfile']} {$mntpath}")):
			write_console("Error: Failed to mount device '{$drive['name']}'!\n");
			@rmdir($mntpath);
			return 1;
		endif;
	else:
//		Mounting the CDROM.
		if(1 == mwexec("/sbin/mount_cd9660 /dev/{$cdrom} {$mntpath}")):
			write_console("Error: Failed to mount device '{$cdrom}'!\n");
			@rmdir($mntpath);
			return 1;
		endif;
	endif;
	return 0;
}
/**
 *	Unmount CDROM.
 *	@return int Return 0 if successful, otherwise 1
 */
function install_unmount_cd() {
	write_console("Unmount CDROM/USB.\n");
	$result = 0;
	$mntpath = '/mnt/cdrom';
//	Unmount CDROM
	if(1 == mwexec("/sbin/umount {$mntpath}")):
		$result = 1;
	endif;
//	Remove temporary mount directory
	@rmdir($mntpath);
	return $result;
}
/**
 *	Add default swap partition.
 *	@global array $g_install
 *	@param string $harddrive
 *	@return int Return 0 if successful, otherwise 1
 */
function add_default_swap(string $harddrive) {
	global $g_install;

	if(mwexec("/sbin/gpart add -s {$g_install['default_swap']}m -t freebsd-swap {$harddrive}")):
		write_console("Error: Failed to add swap.\n");
		return 1;
	endif;
	return 0;
}
/**
 *	Install the IMG on the destination harddrive.
 *	@global array $g
 *	@param string $harddrive
 *	@return int Return 0 if successful, otherwise 1
 */
function install_dd_image(string $harddrive) {
	global $g;

	$imgfilename=get_product_name(). "-{$g['arch']}-embedded.xz";
	$skip = (1 * 1024 * 1024) / (32 * 1024); // 1MB alignment / 32k write
//	Test firmware file integrity.
	if(mwexec("/usr/bin/xz -t /mnt/cdrom/{$imgfilename}")):
		write_console("Error: Firmware file is corrupted.\n");
		return 1;
	endif;
	set_kern_geom_debugflags(0x10);
//	dd image file on the destination disk.
	write_console("Installing system image on device {$harddrive}.\n");
	if(mwexec("/usr/bin/xz -cdv /mnt/cdrom/{$imgfilename} | /bin/dd of=/dev/{$harddrive} ibs=32k obs=32k skip={$skip} > /dev/null 2>&1")):
		set_kern_geom_debugflags(0);
		write_console("Error: Failed to dd image on '/dev/{$harddrive}'!\n");
		return 1;
	endif;
	set_kern_geom_debugflags(0);
//	resize to partition size
	if(mwexec("/sbin/growfs -y /dev/{$harddrive}")):
		write_console("Error: Failed to grow ufs on '/dev/{$harddrive}'!\n");
		return 1;
	endif;
	return 0;
}
/**
 *	Install XigmaNAS® on the given harddrive.
 *	@global array $g
 *	@param string $harddrive
 *	@return int Return 0 if successful, otherwise 1
 */
function install_harddrive_image(string $harddrive) {
	global $g;

	$prdname = get_product_name();
//	Determine if target disk/partition is GPT or MBR
	if(mwexec("/sbin/gpart show {$harddrive} | grep GPT") == false ):
		$part = 'p2';
	else:
		$part = 's1a';
	endif;
	$boot_lowmem = trim(@file_get_contents('/etc/boot_lowmem'));
	$platform = get_platform_type();
	$mntpath = "/mnt/inst_{$harddrive}";
//	Mount disk used to install OS.
	mwexec("/bin/mkdir -p {$mntpath}");
	if(1 == mwexec("/sbin/mount /dev/{$harddrive}{$part} {$mntpath}")):
		write_console("Error: Failed to mount '/dev/{$harddrive}{$part}'!\n");
		return 1;
	endif;
//	Install system files.
	write_console("Installing system files on device {$harddrive}{$part}.\n");
	mwexec("/usr/bin/tar --exclude mnt/ --exclude dev/ --exclude var/ --exclude tmp/ --exclude {$g['cf_path']}/ -c -f - -C / . | tar -xvpf - -C {$mntpath}");
	if($boot_lowmem === '1' && preg_match('/-liveCD$/i',$platform)):
//		under lowmem version
		mwexec("/usr/bin/xz -dcv /mnt/cdrom/mdlocal.xz | dd of={$mntpath}/ftmp/mdlocal bs=1m status=none");
		exec("/sbin/mdconfig -a -t vnode -f {$mntpath}/ftmp/mdlocal",$mdid,$result);
		mwexec("/sbin/mount /dev/{$mdid[0]} {$mntpath}/cf");
		mwexec("/usr/bin/tar -c -f - -C {$mntpath}/cf . | tar -xvpf - -C {$mntpath}/usr/local");
		mwexec("/sbin/umount {$mntpath}/cf");
		mwexec("/sbin/mdconfig -d -u {$mdid[0]}");
		mwexec("/bin/rm {$mntpath}/ftmp/mdlocal");
	endif;
	mwexec("/bin/mkdir -p {$mntpath}/var");
	mwexec("/bin/mkdir -p {$mntpath}/dev");
	mwexec("/bin/mkdir -p {$mntpath}/mnt");
	mwexec("/bin/mkdir -p {$mntpath}/tmp");
	mwexec("/bin/chmod 1777 {$mntpath}/tmp");
	mwexec("/bin/mkdir -p {$mntpath}/boot/defaults");
	mwexec("/bin/cp -r /mnt/cdrom/boot/* {$mntpath}/boot/");
	mwexec("/bin/cp -r /mnt/cdrom/boot/defaults/* {$mntpath}/boot/defaults/");
	mwexec("/bin/cp -r /mnt/cdrom/boot/kernel/* {$mntpath}/boot/kernel/");
	mwexec("/bin/cp -v /etc/rc.d/var {$mntpath}/etc/rc.d/var");
//	Install configuration file
	mwexec("/bin/mkdir -p {$mntpath}{$g['cf_conf_path']}");
	mwexec("/bin/cp -v {$g['conf_default_path']}/config.xml {$mntpath}{$g['conf_path']}");
//	Generate new loader.conf file.
	$loaderconf = <<<EOD
kernel="kernel"
bootfile="kernel"
kernel_options=""
hw.est.msr_info="0"
hw.hptrr.attach_generic="0"
hw.msk.msi_disable="1"
kern.maxfiles="6289573"
kern.cam.boot_delay="12000"
kern.geom.label.disk_ident.enable="0"
kern.geom.label.gptid.enable="0"
hint.acpi_throttle.0.disabled="0"
hint.p4tcc.0.disabled="0"
loader_brand="{$prdname}"
autoboot_delay="3"
hostuuid_load="YES"
hostuuid_name="/etc/hostid"
hostuuid_type="hostuuid"
isboot_load="YES"
zfs_load="YES"

EOD;
	if(false === file_put_contents("{$mntpath}/boot/loader.conf",$loaderconf)):
		write_console("Error: Failed to create '/boot/loader.conf'.\n");
		return 1;
	endif;
//	Set the release type.
	$platform = <<<EOD
{$g['arch']}-full

EOD;
	if(false === file_put_contents("{$mntpath}/etc/platform",$platform)):
		write_console("Error: Failed to create '/etc/platform'.\n");
		return 1;
	endif;
//	Decompress kernel.
	mwexec("/usr/bin/gzip -d -f {$mntpath}/boot/kernel/kernel.gz");
//	Decompress modules.
	mwexec("(cd {$mntpath}/boot/kernel; for f in *.gz; do /usr/bin/gzip -d -f \$f; done)");
//	Generate /etc/fstab.
	$ufsid = disks_get_ufsid("/dev/{$harddrive}{$part}");
	if(empty($ufsid)):
		write_console("Error: Failed to get UFS ID.\n");
		return 1;
	endif;
	$fstab = <<<EOD
# Device                    Mountpoint  FStype  Options         Dump    Pass#
/dev/ufsid/{$ufsid}     /       ufs     rw              1       1

EOD;
	if(false === file_put_contents("{$mntpath}/etc/fstab",$fstab)):
		write_console("Error: Failed to create '/etc/fstab'.\n");
		return 1;
	endif;
//	Generating the /etc/cfdevice (this file is linked in /var/etc at bootup)
//	This file is used by the firmware and mount check and is normally
//	generated with 'liveCD' and 'embedded' during startup, but need to be
//	created during install of 'full'.
	$cfdevice = <<<EOD
{$harddrive}{$part}

EOD;
	if(false === file_put_contents("{$mntpath}/etc/cfdevice",$cfdevice)):
		write_console("Error: Failed to create '/etc/cfdevice'.\n");
		return 1;
	endif;
//	Disable /var md option as it may override default /var directory.
	mwexec("if /usr/sbin/sysrc -f {$mntpath}/etc/rc.conf -qc varmfs; then /usr/sbin/sysrc -f {$mntpath}/etc/rc.conf varmfs=\"NO\" > /dev/null 2>&1; fi");
//	Unmount disk containing OS
	$retry = 10;
	while($retry > 0):
		if(0 == mwexec("/sbin/umount {$mntpath}")):
			break;
		endif;
		$retry--;
		sleep(1);
	endwhile;
	if($retry == 0):
		write_console("Error: Failed to unmount {$mntpath}!\n");
		return 1;
	endif;
	mwexec("/bin/rm -f -r {$mntpath}");
	return 0;
}
/**
 *	Upgrade a 'full' release on the destination harddrive.
 *	@global array $g
 *	@param string $harddrive
 *	@return int Return 0 if successful, otherwise 1
 */
function install_upgrade_full(string $harddrive) {
	global $g;

//	directories to search for chflagged files
	$dirs_noschg = 'bin lib libexec sbin usr';
//	Determine if target disk/partition is GPT or MBR
	if(mwexec("/sbin/gpart show {$harddrive} | grep GPT") == false ):
		$part = 'p2';
	else:
		$part = 's1a';
	endif;
	$boot_lowmem = trim(@file_get_contents('/etc/boot_lowmem'));
	$platform = get_platform_type();
	$mntpath = "/mnt/inst_{$harddrive}";
	$sysbackup = "/tmp/inst_{$harddrive}_bak";
//	Mount disk used to install OS
	mwexec("/bin/mkdir -p {$mntpath}");
	if(1 == mwexec("/sbin/mount /dev/{$harddrive}{$part} {$mntpath}")):
		write_console("Error: Failed to mount '/dev/{$harddrive}{$part}'!\n");
		return 1;
	endif;
//	Backup system files
	write_console("Backup system configuration.\n");
	mwexec("/bin/mkdir -p {$sysbackup}");
	mwexec("/bin/cp -p {$mntpath}/boot/loader.conf {$sysbackup}");
	if(file_exists("{$mntpath}/boot.config")):
		mwexec("/bin/cp -p {$mntpath}/boot.config {$sysbackup}");
	endif;
	if(file_exists("{$mntpath}/boot/loader.conf.local")):
		mwexec("/bin/cp -p {$mntpath}/boot/loader.conf.local {$sysbackup}");
	endif;
	mwexec("/bin/cp -p {$mntpath}/etc/platform {$sysbackup}");
	mwexec("/bin/cp -p {$mntpath}/etc/fstab {$sysbackup}");
	mwexec("/bin/cp -p {$mntpath}/etc/cfdevice {$sysbackup}");
//	Start upgrade script to remove obsolete files. This should be done
//	before system is updated because it may happen that some files
//	may be reintroduced in the system.
	write_console("Remove obsolete files.\n");
	mwexec("/etc/install/upgrade.sh clean {$mntpath}");
//	Dump chflags from common directories/* to zipped files.
	write_console("Backup system chflags.\n");
	if(1 == mwexec("cd {$mntpath} && for files in {$dirs_noschg}; do /usr/sbin/mtree -q -P -c -p \$files | /usr/bin/gzip > {$sysbackup}/chflags.full.\$files.gz; done")):
		write_console("Error: Failed to backup chflags!\n");
		return 1;
	endif;
//	Remove chflags from common directories/*.
	write_console("Removing system chflags.\n");
	if(1 == mwexec("cd {$mntpath} && for files in {$dirs_noschg}; do /bin/chflags -R noschg \$files; /bin/chmod -R u+rw \$files; done")):
		write_console("Error: Failed to remove chflags!\n");
		return 1;
	endif;
//	Install system files
	write_console("Installing system files on device {$harddrive}{$part}.\n");
	if(1 == mwexec("/usr/bin/tar --exclude mnt/ --exclude dev/ --exclude var/ --exclude tmp/ --exclude {$g['cf_path']}/ -c -f - -C / . | tar -xvpf - -C {$mntpath}")):
		write_console("Error: Failed to upgrade {$harddrive}{$part}.\n");
		return 1;
	endif;
	if($boot_lowmem === '1' && preg_match('/-liveCD$/i',$platform)):
//		under lowmem version
		mwexec("/usr/bin/xz -dcv /mnt/cdrom/mdlocal.xz | dd of={$mntpath}/ftmp/mdlocal bs=1m status=none");
		exec("/sbin/mdconfig -a -t vnode -f {$mntpath}/ftmp/mdlocal",$mdid,$result);
		mwexec("/sbin/mount /dev/{$mdid[0]} {$mntpath}/cf");
		mwexec("/usr/bin/tar -c -f - -C {$mntpath}/cf . | tar -xvpf - -C {$mntpath}/usr/local");
		mwexec("/sbin/umount {$mntpath}/cf");
		mwexec("/sbin/mdconfig -d -u {$mdid[0]}");
		mwexec("/bin/rm {$mntpath}/ftmp/mdlocal");
	endif;
	mwexec("/bin/mkdir -p {$mntpath}/var");
	mwexec("/bin/mkdir -p {$mntpath}/dev");
	mwexec("/bin/mkdir -p {$mntpath}/mnt");
	mwexec("/bin/mkdir -p {$mntpath}/tmp");
	mwexec("/bin/chmod 1777 {$mntpath}/tmp");
	mwexec("/bin/mkdir -p {$mntpath}/boot/defaults");
	mwexec("/bin/cp -av /mnt/cdrom/boot/* {$mntpath}/boot");
//	Decompress kernel
	mwexec("/usr/bin/gzip -d -f {$mntpath}/boot/kernel/kernel.gz");
//	Decompress modules
	mwexec("(cd {$mntpath}/boot/kernel; for f in *.gz; do /usr/bin/gzip -d -f \$f; done)");
//	Restore system files
	write_console("Restore system configuration.\n");
	mwexec("/bin/cp -p {$sysbackup}/loader.conf {$mntpath}/boot");
	if(file_exists("{$sysbackup}/boot.config")):
		mwexec("/bin/cp -p {$sysbackup}/boot.config {$mntpath}/");
	else:
		mwexec("/bin/rm -f {$mntpath}/boot.config");
	endif;
	if(file_exists("{$sysbackup}/loader.conf.local")):
		mwexec("/bin/cp -p {$sysbackup}/loader.conf.local {$mntpath}/boot/");
	endif;
//	mwexec("/bin/cp -p {$sysbackup}/platform {$mntpath}/etc");
	mwexec("/bin/cp -p {$sysbackup}/fstab {$mntpath}/etc");
	mwexec("/bin/cp -p {$sysbackup}/cfdevice {$mntpath}/etc");
//	Restore chflags from previous zipped files.
	write_console("Restoring system chflags.\n");
	if(1 == mwexec("cd {$mntpath} && for files in {$dirs_noschg}; do /usr/bin/zcat {$sysbackup}/chflags.full.\$files.gz | /usr/sbin/mtree -q -P -U -p /\$files; done")):
		write_console("Error: Failed to restore chflags!\n");
		return 1;
	endif;
//	Set the release type.
	$platform = <<<EOD
{$g['arch']}-full

EOD;
	if(false === file_put_contents("{$mntpath}/etc/platform",$platform)):
		write_console("Error: Failed to create '/etc/platform'.\n");
		return 1;
	endif;
//	Cleanup system backup files
	mwexec("/bin/rm -f -r {$sysbackup}");
//	Unmount disk containing OS
	$retry = 10;
	while($retry > 0):
		if(0 == mwexec("/sbin/umount {$mntpath}")):
			break;
		endif;
		$retry--;
		sleep(1);
	endwhile;
	if($retry == 0):
		write_console("Error: Failed to unmount {$mntpath}!\n");
		return 1;
	endif;
	mwexec("/bin/rm -f -r {$mntpath}");
	return 0;
}
/**
 *	Check version of the hard drive/CF
 *	@global array $g
 *	@param string $disk
 *	@return int Return 0 if successful, otherwise 1
 */
function install_check_version(string $disk) {
	global $g;

	$mnt_cf_disk = "/mnt/chkver_{$disk}";
	write_console("Check version.\n");
//	Creating temporary directory for the disk containing the version file.
	if(1 == mwexec("/bin/mkdir -p {$mnt_cf_disk}")):
		write_console("Error: Failed to create '{$mnt_cf_disk}'!\n");
		return 1;
	endif;
//	Mount the drive to be upgraded (readonly mode).
	if(1 == mwexec("/sbin/mount -r /dev/{$disk} {$mnt_cf_disk}")):
		write_console("Error: Failed to mount device '{$disk}'!\n");
		return 1;
	endif;
//	Load configuration file into array.
	$raw_config = parse_xml_config_raw("{$g['conf_path']}/config.xml",'');
	if(false === $raw_config):
		write_console("Error: Failed to load config.xml.\n");
		return 1;
	endif;
	$keys = array_keys($raw_config);
	$tmp_config = $raw_config[$keys[0]];
	$upgrade = true;
//	Check config version
	if($keys[0] !== $g['xml_rootobj']):
		if((float)$tmp_config['version'] < (float)8.9): // r5246(old)
			$upgrade = false;
		endif;
	else:
		if((float)$tmp_config['version'] < (float)1.0): // r1
			$upgrade = false;
		endif;
	endif;
	if(false === $upgrade):
		write_console("Error: Does not support upgrading form this version.\n");
		mwexec("/sbin/umount {$mnt_cf_disk}");
		mwexec("/bin/rm -f -r {$mnt_cf_disk}");
		return 1;
	endif;
//	Unmount the drive to be upgraded
	if(1 == mwexec("/sbin/umount {$mnt_cf_disk}")):
		write_console("Error: Failed to unmount '{$mnt_cf_disk}'!\n");
		return 1;
	endif;
//	Cleanup
	if(1 == mwexec("/bin/rm -f -r {$mnt_cf_disk}")):
		write_console("Error: Failed to remove temporary data!\n");
		return 1;
	endif;
	return 0;
}
/**
 *	Backup the configuration file on the hard drive/CF and unmount this drive.
 *	@global array $g
 *	@param string $disk
 *	@return int Return 0 if successful, otherwise 1
 */
function install_backup_config(string $disk) {
	global $g;

	$tmp_cf_bak = '/tmp/cf_bak';
	$mnt_cf_disk = "/mnt/inst_{$disk}";
	write_console("Backup configuration.\n");
//	Creating temporary directory for the configuration file.
	if(1 == mwexec("/bin/mkdir -p {$tmp_cf_bak}")):
		write_console("Error: Failed to create '{$tmp_cf_bak}'!\n");
		return 1;
	endif;
//	Creating temporary directory for the disk containing the configuration file.
	if(1 == mwexec("/bin/mkdir -p {$mnt_cf_disk}")):
		write_console("Error: Failed to create '{$mnt_cf_disk}'!\n");
		return 1;
	endif;
//	Mount the drive to be upgraded (readonly mode).
	if(1 == mwexec("/sbin/mount -r /dev/{$disk} {$mnt_cf_disk}")):
		write_console("Error: Failed to mount device '{$disk}'!\n");
		return 1;
	endif;
//	Backup the configuration file
	if(1 == mwexec("/bin/cp -p {$mnt_cf_disk}/conf/config.xml {$tmp_cf_bak}")):
		write_console("Error: Failed to backup the configuration file!\n");
		return 1;
	endif;
	$files = glob("{$mnt_cf_disk}/conf/ssh_host_*") or [];
	foreach($files as $file):
		if(1 == mwexec("/bin/cp -p {$file} {$tmp_cf_bak}")):
			write_console("Error: Failed to backup the configuration file!\n");
			return 1;
		endif;
	endforeach;
	if(file_exists("{$mnt_cf_disk}/boot.config") && 1 == mwexec("/bin/cp -p {$mnt_cf_disk}/boot.config {$tmp_cf_bak}")):
		write_console("Error: Failed to backup the configuration file!\n");
		return 1;
	endif;
	if(file_exists("{$mnt_cf_disk}/boot/loader.conf.local") && 1 == mwexec("/bin/cp -p {$mnt_cf_disk}/boot/loader.conf.local {$tmp_cf_bak}")):
		write_console("Error: Failed to backup the configuration file!\n");
		return 1;
	endif;
//	Unmount the drive to be upgraded
	if(1 == mwexec("/sbin/umount {$mnt_cf_disk}")):
		write_console("Error: Failed to unmount '{$mnt_cf_disk}'!\n");
		return 1;
	endif;
	return 0;
}
/**
 *
 *	@global array $g
 *	@param string $disk
 *	@return int Return 0 if successful, otherwise 1
 */
function install_restore_config(string $disk) {
	global $g;

	$tmp_cf_bak = '/tmp/cf_bak';
	$mnt_cf_disk = "/mnt/inst_{$disk}";
	write_console("Restore configuration.\n");
//	Mount the drive to be upgraded
	if(1 == mwexec("/sbin/mount /dev/{$disk} {$mnt_cf_disk}")):
		write_console("Error: Failed to mount device '{$disk}'!\n");
		return 1;
	endif;
//	Restore the configuration file
	if(1 == mwexec("/bin/cp -p {$tmp_cf_bak}/config.xml {$mnt_cf_disk}/conf")):
		write_console("Error: Failed to restore the configuration file! (config.xml)\n");
		return 1;
	endif;
	$files = glob("{$tmp_cf_bak}/ssh_host_*") or [];
	foreach($files as $file):
		if(1 == mwexec("/bin/cp -p {$file} {$mnt_cf_disk}/conf")):
			write_console("Error: Failed to restore the configuration file!({$file})\n");
			return 1;
		endif;
	endforeach;
	if(file_exists("{$tmp_cf_bak}/boot.config")):
		if(1 == mwexec("/bin/cp -p {$tmp_cf_bak}/boot.config {$mnt_cf_disk}/")):
			write_console("Error: Failed to restore the configuration file!(boot.config)\n");
			return 1;
		endif;
	else:
		if(1 == mwexec("/bin/rm -f {$mnt_cf_disk}/boot.config")):
			write_console("Error: Failed to remove the configuration file!\n");
			return 1;
		endif;
	endif;
	if(file_exists("{$tmp_cf_bak}/loader.conf.local")):
		if(1 == mwexec("/bin/cp -p {$tmp_cf_bak}/loader.conf.local {$mnt_cf_disk}/boot")):
			write_console("Error: Failed to restore the configuration file! (loader.conf.local)\n");
			return 1;
		endif;
	endif;
//	Unmount the upgraded drive
	if(1 == mwexec("/sbin/umount {$mnt_cf_disk}")):
		write_console("Error: Failed to unmount '{$mnt_cf_disk}'!\n");
		return 1;
	endif;
//	Cleanup
	if(1 == mwexec("/bin/rm -f -r {$mnt_cf_disk} {$tmp_cf_bak}")):
		write_console("Error: Failed to remove temporary backup data!\n");
		return 1;
	endif;
	return 0;
}
/**
 *	Create two partitions on the destination hard drive.
 *	@param string $disk Disk name (e.g. ad0)
 *	@param array $partlayout The layout description of the disk, e.g.
 *	[
 *		['slice' => 1,'fstype' => '4.2BSD','size' => 64,'volname' => 'root'],
 *		['slice' => 2,'fstype' => '4.2BSD','size' => '*','volname' => 'data'],
 *		['slice' => 3,'fstype' => 'swap','size' => 200,'volname' => '']
 *		['slice' => 4,'fstype' => 'unused','size' => 0,'volname' => '']
 *	]
 *	@return int Return 0 if successful, otherwise 1
 */
function install_init_disk($disk,$partlayout) {
	set_kern_geom_debugflags(0x10);
//	force delete existing partitions (error is OK)
	mwexec("/sbin/gpart destroy -F {$disk}");
//	initialize the destination using fdisk.
	write_console("Create slice/partition table on device {$disk}.\n");
	$slicesize = [0,0,0,0];
	foreach($partlayout as $partlayoutv):
//		add 4MiB alignment
		if($partlayoutv['size'] !== '*'):
			$slicesize[$partlayoutv['slice'] - 1] = $partlayoutv['size'] + 4;
		else:
			$slicesize[$partlayoutv['slice'] - 1] = $partlayoutv['size'];
		endif;
	endforeach;
	if(install_fdisk($disk,$slicesize)):
		set_kern_geom_debugflags(0);
		write_console("Error: Failed to create slice/partition table!\n");
		return 1;
	endif;
//	get partition information
//	$partinfo = disks_get_partition_info($disk);
//	AFT support
//	$alignsector = 8; // 4KB/AFT
//	$alignsector = 64; // 32KB/blocksize
	$newfs_sector = 4096;
	$newfs_fsize = 4096;
	$newfs_bsize = $newfs_fsize * 8;
//	create disk labels.
	write_console("Create BSD label on slice/partition:\n");
	foreach($partlayout as $partlayoutv):
		$diskdevicename = "{$disk}s{$partlayoutv['slice']}";
//		$startsector = $partinfo[$partlayoutv['slice']]['start'];
//		$slicesize = $partinfo[$partlayoutv['slice']]['size'];
		write_console("  {$diskdevicename}\n");
//		force delete existing partitions (error is OK)
		mwexec("/sbin/gpart destroy -F {$diskdevicename}");
//		create BSD partition with alignment (UFS or swap)
		if(mwexec("/sbin/gpart create -s bsd {$diskdevicename}")):
			set_kern_geom_debugflags(0);
			write_console("\nError: Failed to create BSD label on {$diskdevicename}!\n");
			return 1;
		endif;
		if($partlayoutv['slice'] === 1):
//			install boot code
//			write_console("    /sbin/gpart bootcode -b /boot/boot {$diskdevicename}\n");
			if(mwexec("/sbin/gpart bootcode -b /boot/boot {$diskdevicename}")):
				set_kern_geom_debugflags(0);
				write_console("Error: Failed to install boot on {$diskdevicename}!\n");
				return 1;
			endif;
		endif;
		if($partlayoutv['fstype'] === 'swap'):
			$flags = '-i2 -a 4m -t freebsd-swap';
		else:
			$flags = '-a 4m -t freebsd-ufs';
		endif;
		if($partlayoutv['size'] !== '*'):
			$flags .= " -s {$partlayoutv['size']}m";
		endif;
//		write_console("    /sbin/gpart add {$flags} {$diskdevicename}\n");
		if(mwexec("/sbin/gpart add {$flags} {$diskdevicename}")):
			set_kern_geom_debugflags(0);
			write_console("\nError: Failed to add BSD label on {$diskdevicename}!\n");
			return 1;
		endif;
	endforeach;
//	create file systems and set volume label.
	write_console("Create file system on slice/partition:\n");
	foreach($partlayout as $partlayoutv):
//		do not create a file system for swap/unused partitions.
		if('swap' === $partlayoutv['fstype'] || 'unused' === $partlayoutv['fstype']):
			continue;
		endif;
		$diskdevicename = "{$disk}s{$partlayoutv['slice']}a";
		write_console("  {$diskdevicename}\n");
		if(install_newfs($diskdevicename,$partlayoutv['volname'],$newfs_sector,$newfs_fsize,$newfs_bsize)):
			set_kern_geom_debugflags(0);
			write_console("\nError: Failed to create file system on {$diskdevicename}!\n");
			return 1;
		endif;
	endforeach;
	set_kern_geom_debugflags(0);
	return 0;
}
/**
 *	Initialize harddrive. It creates the root (p1), data (p2) and swap (p3) partitions.
 *	@global array $g
 *	@param string $disk Disk name (e.g. ad0)
 *	@param array $slicesize Array containing the size of each slice in MB, e.g. [64,100,*,0]
 *	@return int Return 0 if successful, otherwise 1
 */
function install_fdisk(string $disk,array $slicesize) {
	global $g;

	$slicetable = [
		['slice' => 1,'type' => 0,'start' => 0,'length' => 0],
		['slice' => 2,'type' => 0,'start' => 0,'length' => 0],
		['slice' => 3,'type' => 0,'start' => 0,'length' => 0],
		['slice' => 4,'type' => 0,'start' => 0,'length' => 0]
	];
	$activeslice = 0;
//	Get disk informations
	$diskinfo = disks_get_diskinfo($disk);
//	Get valid CHS information. 'diskinfo' gets values from harddisk firmware that
//	are maybe unusable by 'fdisk' (e.g. sectors = 255, fdisk requires <= 63).
	disk_sanitize_bios_geom($diskinfo);
//	Calculate the size of the slices
	$usedsectors = $diskinfo['sectors']; // MBR start here
	$align = $diskinfo['heads'] * $diskinfo['sectors'];
	foreach($slicesize as $slicesizek => $slicesizev):
		if(is_numeric($slicesizev)):
			$bytes = $slicesizev * 1024 * 1024;
			$slicetable[$slicesizek]['length'] = $bytes / $diskinfo['sectorsize'];
//			$slicetable[$slicesizek]['length'] = floor($slicetable[$slicesizek]['length'] / $align) * $align;
			$slicetable[$slicesizek]['length'] = ceil($slicetable[$slicesizek]['length'] / $align) * $align;
			if($slicesizek == 0):
				$slicetable[$slicesizek]['length'] -= $diskinfo['sectors'];
			endif;
//			Sum total used sectors
			$usedsectors += $slicetable[$slicesizek]['length'];
		endif;
	endforeach;
	foreach($slicesize as $slicesizek => $slicesizev):
		if('*' === $slicesizev): // Use the rest of the disk space for this slice.
			$slicetable[$slicesizek]['length'] = $diskinfo['mediasize_sectors'] - $usedsectors;
			$slicetable[$slicesizek]['length'] = floor($slicetable[$slicesizek]['length'] / $align) * $align;
			if($slicesizek == 0):
				$slicetable[$slicesizek]['length'] -= $diskinfo['sectors'];
			endif;
//			Stop processing here (only one '*' is accepted and processed)
			break;
		endif;
	endforeach;
//	Check if partition layout fit disk size
	if($diskinfo['mediasize_sectors'] <= $usedsectors):
		write_console("Error: Partition layout does not fit on disk {$diskinfo['name']}.\n");
		write_console("  Requested sectors: {$usedsectors}\n");
		write_console("  Available sectors: {$diskinfo['mediasize_sectors']}\n");
		write_console('  Layout: ');
		foreach($slicesize as $slicesizev):
			write_console(" {$slicesizev}");
		endforeach;
		write_console("\n");
		return 1;
	endif;
//	Calculate slice start sectors and type
	$startsector = $diskinfo['sectors'];
	foreach($slicesize as $slicesizek => $slicesizev):
		if( 0 >= $slicetable[$slicesizek]['length']):
			continue;
		endif;
//		Set the first found slice with length > 0 as active
		if(0 == $activeslice):
			$activeslice = $slicesizek + 1;
		endif;
		$slicetable[$slicesizek]['type'] = 165;
		$slicetable[$slicesizek]['start'] = $startsector;
//		Calculate start of next slice
		$startsector += $slicetable[$slicesizek]['length'];
	endforeach;
//	Create fdisk config file (fdisk.conf)
	$fdiskconf = "g c{$diskinfo['cylinders']} h{$diskinfo['heads']} s{$diskinfo['sectors']}\n";
	for($i = 0; $i < 4; ++$i):
		$fdiskconf .= "p {$slicetable[$i]['slice']} {$slicetable[$i]['type']} {$slicetable[$i]['start']} {$slicetable[$i]['length']}\n";
	endfor;
	$fdiskconf .= "a {$activeslice}\n";
	if(false === file_put_contents("{$g['tmp_path']}/fdisk.conf",$fdiskconf)):
		write_console("Error: Failed to create '{$g['tmp_path']}/fdisk.conf'.\n");
		return 1;
	endif;
//	Delete MBR and partition table.
	$count = (1024*1024) / $diskinfo['sectorsize'];
	if(mwexec("/bin/dd if=/dev/zero of=/dev/{$disk} bs={$diskinfo['sectorsize']} count={$count}")):
		write_console("Error: Failed to delete MBR and partition table on disk {$disk}.\n");
		return 1;
	endif;
//	Delete GEOM metadata.
	$oseek = $diskinfo['mediasize_sectors'] - 1;
	if(mwexec("/bin/dd if=/dev/zero of=/dev/{$disk} bs={$diskinfo['sectorsize']} oseek={$oseek} count=1")):
		write_console("Error: Failed to delete GEOM metadata on disk {$disk}.\n");
		return 1;
	endif;
//	Set slice values
	if(mwexec("/sbin/fdisk -f {$g['tmp_path']}/fdisk.conf {$disk}")):
		write_console("Error: Failed to write slice table on disk {$disk}.\n");
		return 1;
	endif;
//	Reinitialize the boot code contained in sector 0 of the disk
	if(mwexec("(/bin/echo y; /bin/echo y) | /sbin/fdisk -B -b /boot/mbr {$disk}")):
		write_console("Error: Failed to reinitialize the boot code on disk {$disk}!\n");
		return 1;
	endif;
	return 0;
}
/**
 *	Create disk label.
 *	@global array $g
 *	@param string $disk Disk name (e.g. ad0s1)
 *	@param string $fstype The file system type (e.g. 4.2BSD or swap)
 *	@param int $startsector Start sector of the slice
 *	@param int $slicesize Size of the slice
 *	@param int $alignsector alignment for the sector of the partition (e.g. 8=4KB)
 *	@return int Return 0 if successful, otherwise 1
 */
function install_bsdlabel(string $disk,string $fstype,int $startsector,int $slicesize,int $alignsector) {
	global $g;

//	Check sectors
	if($startsector == '' || $slicesize == '' || $alignsector == '' || $alignsector < 1 || $slicesize < $alignsector):
		write_console("\nError: Invalid parameter on {$disk}.\n");
		return 1;
	endif;
	$poffset = 16;
	$aligned_start = ceil(($startsector + $poffset) / $alignsector) * $alignsector;
	$aligned_start -= $startsector;
	$aligned_counts = $slicesize - $aligned_start;
	$aligned_counts = floor($aligned_counts / $alignsector) * $alignsector;
//	Completely wipe any prior information on the partition
	if(mwexec("/bin/dd if=/dev/zero of=/dev/{$disk} bs=512 count=2048")):
		write_console("\nError: Failed to wipe any prior information on {$disk}.\n");
		return 1;
	endif;
//	Do an automatic label first
	if(mwexec("/sbin/bsdlabel -w -B {$disk}")):
		write_console("\nError: Failed to write standard label.\n");
		return 1;
	endif;
//	Read the label to manipulate it
	exec("/sbin/bsdlabel {$disk} > {$g['tmp_path']}/label.tmp",$output,$result);
	if($result):
		write_console("\nError: Failed to backup label.\n");
		return 1;
	endif;
//	Put the file content into an array
	$label = file("{$g['tmp_path']}/label.tmp");
//	Open this file in add mode
	$fd = fopen("{$g['tmp_path']}/label.tmp",'w');
	foreach($label as $val):
		if((false !== strpos($val,'unused')) && (false === strpos($val,'c:'))):
//			Replace 'unused' with '$fstype'
			$val = str_replace('unused',$fstype,$val);
//			Replace with aligned size
			$val = preg_replace("/([a-z]:)\s+\d+\s+\d+/","\\1 {$aligned_counts} {$aligned_start}",$val);
		endif;
		fwrite($fd,$val);
	endforeach;
//	Closing file
	fclose($fd);
//	Injecting modified label
	if(mwexec("/sbin/bsdlabel -R -B {$disk} {$g['tmp_path']}/label.tmp")):
		write_console("\nError: Failed to write modified label.\n");
		return 1;
	endif;
//	Cleanup
	unlink("{$g['tmp_path']}/label.tmp");
	return 0;
}
/**
 *	Create new file system on disk.
 *	@param string $disk Disk name (e.g. ad0s1)
 *	@param string $volname The new volume label (e.g. 'data')
 *	@param int $sector The size of a sector in bytes.
 *	@param int $fsize The fragment size of the file system in bytes.
 *	@param int $bsize The block size of the file system, in bytes.
 *	@return int Return 0 if successful, otherwise 1
 */
function install_newfs(string $disk,string $volname,int $sector,int $fsize,int $bsize) {
//	$param = '-U -b 16384 -f 2048';
	$a_param = [];
	$a_param[] = '/sbin/newfs';
	$a_param[] = '-U';
	$a_param[] = '-S';
	$a_param[] = sprintf('%u',$sector);
	$a_param[] = '-b';
	$a_param[] = sprintf('%u',$bsize);
	$a_param[] = '-f';
	$a_param[] = sprintf('%u',$fsize);
	if(!empty($volname)):
		$a_param[] = '-L';
		$a_param[] = escapeshellarg($volname);
	endif;
	$a_param[] = escapeshellarg(sprintf('/dev/%s',$disk));
	$cmd = implode(' ',$a_param);
	if(mwexec($cmd)):
		return 1;
	endif;
	return 0;
}
/**
 *	Detect device where the OS is installed.
 *	@global array $g
 *	@param string $mode String 'embedded' or 'full'
 *	@param array $devinfo Return array containing ['device'], ['partition'] and ['size'] information
 *	@return int Return 0 if successful, otherwise 1
 */
function install_detect_installation(string $mode,&$devinfo) {
	global $g;

	$imgfile = get_product_name(). "-{$g['arch']}-embedded.xz";
	$result = 1;
	$devinfo = [];
	echo "Detecting installation...\n";
//	Create temporary mountpoint
	$mnt_cf_disk = '/mnt/inst_cf_tmp';
	mwexec("/bin/mkdir -p {$mnt_cf_disk}");
//	Scan all disks detected from kernel
	$adisk = explode(' ',trim(preg_replace('/kern.disks: /','',exec('/sbin/sysctl kern.disks'))));
	foreach($adisk as $diskv):
//		Skip mfs-mounted filesystems
		if(false != preg_match('/md[\d]/',$diskv)):
			continue;
		endif;
//		Search on slice 1 for OS installed on disk with install script
		if(0 == mwexec("/sbin/mount -r /dev/{$diskv}s1a {$mnt_cf_disk}")):
//			Check if configuration file exists
			if(file_exists("{$mnt_cf_disk}/conf/config.xml")):
				$devinfo['device'] = $diskv;
				$devinfo['partition'] = 's1a';
//				Get partition information
				$partinfo = disks_get_partition_info($diskv);
				$devinfo['size'] = $partinfo[1]['size'];
//				Check install mode
				switch($mode):
					case 'embedded':
						if(file_exists("{$mnt_cf_disk}/mfsroot.gz") && !file_exists("{$mnt_cf_disk}/{$imgfile}")):
							$result = 0;
						endif;
						break;
					case 'full':
						$platform = chop(@file_get_contents("{$mnt_cf_disk}{$g['etc_path']}/platform"));
						if(false !== strpos($platform,'-full')):
							$result = 0;
						endif;
						break;
				endswitch;
			endif;
//			Unmount device
			mwexec("/sbin/umount {$mnt_cf_disk}");
			if(0 == $result):
				break;
			endif;
		endif;
//		Search on partition 2 for OS installed on disk with install script
		if(0 == mwexec("/sbin/mount -r /dev/{$diskv}p2 {$mnt_cf_disk}")):
//			Check if configuration file exists
			if(file_exists("{$mnt_cf_disk}/conf/config.xml")):
				$devinfo['device'] = $diskv;
				$devinfo['partition'] = 'p2';
//				Get partition information
				$partinfo = disks_get_partition_info($diskv);
				$devinfo['size'] = $partinfo[1]['size'];
//				Check install mode
				switch($mode):
					case 'embedded':
						if(file_exists("{$mnt_cf_disk}/mfsroot.gz") && !file_exists("{$mnt_cf_disk}/{$imgfile}")):
							$result = 0;
						endif;
						break;
					case 'full':
						$platform = chop(@file_get_contents("{$mnt_cf_disk}{$g['etc_path']}/platform"));
						if(false !== strpos($platform,'-full')):
							$result = 0;
						endif;
						break;
				endswitch;
			endif;
//			Unmount device
			mwexec("/sbin/umount {$mnt_cf_disk}");
			if(0 == $result):
				break;
			endif;
		endif;
//		Search on partition 'a' for OS IMG image written on disk
		if(mwexec("/sbin/mount -r /dev/{$diskv}a {$mnt_cf_disk}") == 0):
//			Check if configuration file exists
			if(file_exists("{$mnt_cf_disk}/conf/config.xml")):
				$devinfo['device'] = $diskv;
				$devinfo['partition'] = 'a';
//				Get disk information
				$diskinfo = disks_get_diskinfo($diskv);
				$devinfo['size'] = $diskinfo['mediasize_sectors'];
//				Check install mode
				switch($mode):
					case 'embedded':
						if(file_exists("{$mnt_cf_disk}/mfsroot.gz") && !file_exists("{$mnt_cf_disk}/{$imgfile}")):
							$result = 0;
						endif;
						break;
					case 'full':
						$platform = chop(@file_get_contents("{$mnt_cf_disk}{$g['etc_path']}/platform"));
						if(false !== strpos($platform,'-full')):
							$result = 0;
						endif;
						break;
				endswitch;
			endif;
//			Unmount device
			mwexec("/sbin/umount {$mnt_cf_disk}");
			if(0 == $result):
				break;
			endif;
		endif;
	endforeach;
//	Cleanup
	mwexec("/bin/rm -f -r {$mnt_cf_disk}");
	return $result;
}
