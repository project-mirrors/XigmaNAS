       README & CHANGELOG 11.4.0.4 - Accadia

 	  IMPORTANT - PLEASE READ!!!


Important information for users running on a lower version than XigmaNAS® 11.2.0.4.6625
=======================================================================================
Please download the LiveCD/USB image to create a new boot media.
We do not recommend an in-place upgrade via the firmware update page because of recent changes to the partition layout.
First, download your configuration file from the backup/restore page and save it. Then backup all other data partitions that exist on the boot media.
Now you can perform a fresh installation from the LiveCD/USB. After a successful installation, you should restore your previously saved configuration.

Note for Full install users
===========================
Make sure you first upgraded to XigmaNAS-x64-full-11.3.0.4.7272.tgz before you flash full-.txz files.
The new (.txz) full upgrade files are compressed images.

Notice for RootOnZFS platform users, the `beadm` utility to manage boot environments has been replaced by the `bectl` which behaves the same way as the predecessor
`beadm`, users with existing scripting on `beadm` can create an alias or a symlink for `/usr/local/sbin/beadm` to point to `/sbin/bectl`, sorry for inconvenience.

Install from scratch
====================
Download XigmaNAS® LiveCD or LiveUSB and boot.
The compressed LiveUSB.img.gz needs to be extracted first before it can be written to a USB media,
You can use it with Win32DiskImager or other tool.
Press 9 for the install menu. We recommand an embedded installation, never a direct dd.

** Embedded.img.xz is only for upgrades by WEBGUI, do not extract and write to any media for fast installs! **
It will be problematic as it doesn't make the required partitions that you need!


Upgrade note to "Full" RootOnZFS Platform users
===============================================
Users upgrading RootOnZFS from a revision lower than 6625, please go to: "Tools > Execute Command" and execute the below command
to upgrade to the latest rc.firmware script, this is a one-time only and not required beyond this revision unless noted:
~~~
fetch --no-verify-peer -o /etc/rc.firmware "https://sourceforge.net/p/xigmanas/code/HEAD/tree/branches/11.2.0.4/etc/rc.firmware?format=raw"
~~~
Then go to: "System > Firmware Update" and Upgrade your platform Firmware to latest as usual.


Upgrade note
============
After upgrade you may need to clear your browser's cache to avoid display issues.

** After the upgrade please do make a new backup of your configuration! see: System|Backup/Restore on menu **

Dynamic DNS note
================
Existing users of service "Dynamic DNS" need to setup this service again.
On 11.4.0.4.8282 we have inadyn-mt replaced by inadyn, for this new webgui pages with more options are introduced.

ZFS Upgrade note
================
Upgrade ZFS and add all supported feature flags on a pool only if you are ready for it!
Those upgrades can not be undone.

cifs/smb note
=============
From Samba 4.5.0 and up the default value for the "ntlm auth" option has changed from "yes" to "no".
This may have an impact on very old clients that don't support NTLMv2 yet.
Only if you really need NTLMv1 Authentication you can set in Services > CIFS/SMB > Settings on
Additional Parameters: "ntlm auth = yes".

UPS note
=============
We have added output voltage to graphs. 
Ups users need to reset current ups graph data!

ExtendedGUI note
================
Warning! Extended GUI is not compatible with this release.
Disable and remove the extension before upgrade.

Permanent Restrictions
======================
- It is not possible to format a SoftRAID disk with MSDOS FAT16/32.
- It is not possible to encrypt a disk partition, only entire disks are supported.
- AFP shares with TimeMachine support are limited to max. 4 shares.
- Tool iperf3 is not backwards compatible with iperf2.x. you must upgrade your client.


Minimum System Requirements: 11.4.0.4 series
============================================
Browser: Chrome, Edge or Firefox browser. (IE11 limited supported)
Processor: Multicore 64-bit processor or better.
Boot device: 4GB minimum for "Embedded" platform, 4GB for Full platform.
Memory: "Embedded" 2GB minimum RAM, "Full" platform 2GB minimum RAM.
We always advice to use an emmbedded install on USB/CF/SSD media.

 *Note: LiveCD is not supported on swap mode except installation and upgrading.
Swap less install (not recommended): physical memory 2GB RAM or higher required.
With swap*: physical memory 512MB RAM + swap 512MB minimum (swap 1024MB is recommended).


Don't forget to backup the current system configuration before upgrading!

1. System|Backup/Restore Restore your config.
2. Disks|Management  click on Clear config and Import disks to update configuration.
3. Disks|Management|Disk Edit  After step 2, you will need to re-activate S.M.A.R.T.
   monitoring for every device.

 *Note  If you have RAID controllers but cannot parse S.M.A.R.T. info properly,
  please add correct variables in System|Advanced|loader.conf to load the correct
  kernel modules for controller support. Then reboot and Clear config and Import disks
  again to update the configuration.


Login error 403
===============
Do you have WebGUI Login error 403?
Ensure the PC is on the same network!
By default the System|General Setup Hosts allow field is empty so anyone on
the same network of the LAN interface can access the WebGUI.
With a space-delimited set of IP or CIDR notation you can add computers from outer network.
As an example the outer IP address and LAN address for remote access.


HAST (Highly Available Storage)
===============================
It is still experimental in the WebGUI. You need CLI for some tasks.
To evaluate HAST, you need two of the same-configured XigmaNAS® servers.
Currently iSCSI, CIFS, NFS, GPTUFS and ZFS on HAST is supported.
For master node of WebGUI, carp advskew is assumed as 0 or 1.


BUILD 11.4.0.4.8453
===================
Changes:
- Upgrade to FreeBSD 11.4-RELEASE-P13.
- WebGUI code & framework improvements.
- Update translations.
- Upgrade mariadb to 10.5.
- Upgrade rsync to 3.2.3.
- Upgrade iperf3 to 3.9.
- Upgrade transmission to 3.00.
- Upgrade pecl-APCu to 5.1.19.
- Upgrade gzip to 1.10.
- Upgrade unison to 2.51.3.
- Upgrade tmux to 3.1c.
- Upgrade smartmontools to 7.2.
- Upgrade minidlna 1.3.0.
- Upgrade dmidecode to 3.3.
- Upgrade zoneinfo to 2021a.
- Upgrade jQuery to 3.6.0.
- Upgrade devcpu-data to 1.38.
- Upgrade mDNSResponder to 1310.80.1.
- Upgrade msmtp to 1.8.15.
- Upgrade e2fsprogs to 1.46.2.
- Upgrade open-vm-tools to 11.2.5.
- Upgrade sudo to 1.9.6p1.
- Upgrade arcconf to 3_07_23971.
- Upgrade phpmyadmin to 5.1.0.
- Upgrade python to 3.8.x.
- Upgrade virtualbox-ose to 6.1.22.
- Upgrade nano to 5.7.
- Upgrade syncthing to 1.16.1.
- Upgrade samba to 4.13.8.
- Upgrade nut to 2021.05.24.
- Upgrade dialog to 1.3-20210509.
- Upgrade bash to 5.1 p8.
- Upgrade proftpd to 1.3.7c.
- Upgrade php to 7.4.23.

Fixes:
- Fix only first lagg member is brought up.
- Fix reload issue.
- Fix cookie expire phpvirtualbox.
- Fix stop torrents before stopping service.
- Fix edit user, the default group kept resetting.

Default Login
=============

 **** The default login for XigmaNAS® Console ****
 username: root
 password: xigmanas

 **** The default login for XigmaNAS® WebGUI ****
 username: admin
 password: xigmanas

 **** The default login for phpVirtualBox ****
 username: admin
 password: admin

 **** The default login for phpMyAdmin ****
 username: root
 password: Leave blank, change password after first login!

 * It is recommended to change default passwords after the initial setup is completed.

XigmaNAS® is copyright © 2018 - 2021 XigmaNAS® (info@xigmanas.com). All Rights Reserved.
XigmaNAS® is a registered trademark of Michael Zoon (zoon01@xigmanas.com). All Rights Reserved.
