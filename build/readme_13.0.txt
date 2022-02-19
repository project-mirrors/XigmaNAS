		README & CHANGELOG FOR XIGMANAS® 13.0.0.5 - Glowpanel

		IMPORTANT - PLEASE READ CAREFULLY

General note:
=============
Download your configuration file from the backup/restore page and store it in a safe location before upgrading your system.

Install from scratch instructions:
==================================
- Download the LiveUSB file, extract the image, and write the image to a USB media. Alternatevely, you can download the LiveCD ISO file and write it to a CD/DVD.
- Boot from the LiveCD/USB device and perform a XigmaNAS® installation onto a new boot media.

Warning:
========
Do not shortcut the installation procedure!
The LiveCD/USB image contains a file called embedded.img.xz. Do not extract this file and write the extracted image to a media.

Upgrade notes!
==============

GEOM RAID5
==========
This is for current GEOM RAID5 users running on 12.x series!
XigmaNAS 13.x releases will no longer support GEOM RAID5, code was removed from the code base.
You will NOT have access to your DATA!
Migrate your data to ZFS first before upgrade.

Post-upgrade tasks:
===================
- You may need to clear your browser's cache to circumvent display issues.
- Make a backup of your configuration file and store it in a safe location.
- You may need to backup your databases and update your database tables to the latest version.

ZFS feature flags upgrade warning:
==================================
Enabling all supported feature flags on a pool can make the pool inaccessible on systems that do not support these feature flags.

CIFS/SMB note:
==============
From Samba 4.5.0 onwards, the "ntlm auth" option has been disabled by default.
This may have an impact on very old clients that support NTLMv1 only.
Only if really required, NTLMv1 authentication can be enabled by adding "ntlm auth = yes" in the additional parameter field in Services > CIFS/SMB > Settings.

Community extension "Extended GUI" warning:
===========================================
Warning! Extended GUI is not compatible with this release. Disable and remove the extension before upgrading.

Permanent restrictions:
=======================
- It is not possible to format a SoftRAID disk with MSDOS FAT16/32.
- It is not possible to encrypt a disk partition, the encryption of entire disks is supported.
- AFP shares with TimeMachine support are limited to a maximum of 4 shares.
- iperf3 is not backwards compatible with iperf2.x. Plese upgrade your client.

Minimum system requirements:
============================
Browser: Chrome, Firefox, Edge, Safari, Opera or any other recent browser.
Processor: Multicore 64-bit x86 processor or better.
Boot device: 4GB minimum for "Embedded" platform, 4GB for Full platform.
System memory: "Embedded" 2GB minimum RAM, "Full" platform 2GB minimum RAM.
An embedded installation on a USB/CF/SSD media is recommended.

 *Note: LiveCD is not supported on swap mode except installation and upgrading.
Swapless install (not recommended): physical memory 2GB RAM or higher required.
With swap*: physical memory 512MB RAM + swap 512MB minimum (swap 1024MB is recommended).

Restore configuration from backup instructions:
===============================================
1. Go to System > Backup/Restore > Restore and restore your configuration.
2. Go to Disks > Management > HDD Management
3. Select option "Clear configuration information before importing disks" and click "Import" button in the Import Disks section.
4. Re-activate S.M.A.R.T. monitoring and configure other settings on each disk.

 *Note: If you have RAID controllers but you cannot parse S.M.A.R.T. info properly, please add
  variables in System > Advanced > loader.conf to load the required kernel modules for controller
  support. Reboot and perform the above steps again.

Login error 403:
================
If you are presented with a login error 403 in the WebGUI, ensure your PC is on the same network.
By default, the Hosts Allow field under System > General is left empty. This setting allows anyone on the same network of the LAN interface to access the WebGUI.
With a space-delimited set of IP or CIDR notation, you can add computers from other networks.

HAST (Highly Available Storage):
================================
HAST configuration is still experimental in the WebGUI. You need CLI for some tasks.
To evaluate HAST, you need two of the same-configured XigmaNAS® servers.
iSCSI, CIFS, NFS, GPTUFS and ZFS on HAST is currently supported.
For master node of WebGUI, carp advskew is assumed as 0 or 1.

BUILD 13.0.0.5.xxxx
===================
Changes:
- Upgrade to FreeBSD 13.0-RELEASE P7.
- Upgrade arcconf to v3_10_24700.
- Upgrade isboot to v0.2.14.
- Upgrade dialog to v1.3-20211107.
- Upgrade php to v8.0.13.
- Upgrade inadyn to v2.9.0.
- Upgrade wsdd to v0.7.0.
- Upgrade gzip to v1.11.
- Upgrade inadyn to v2.9.1.
- Upgrade unison to v2.51.5.
- Upgrade sudo to v1.9.9.
- Upgrade virtualbox-ose to v6.1.32.
- Upgrade bash to v5.1 p16.
- Upgrade e2fsprog to v1.46.5.
- Upgrade nano to v6.1.
- Upgrade samba to v4.13.17.
- Upgrade syncthing to v1.19.0.
- Upgrade devcpu-data-intel to v20220207.
- Upgrade phpmyadmin to v5.1.3.
- Upgrade lighttpd to v1.4.64.
- Upgrade nut to v2022.02.17.
- Upgrade cdialog to v1.3-20220117.

Fixes:
-

Default login credentials:
==========================

 **** Default login credentials for the XigmaNAS® WebGUI ****
 username: admin
 password: xigmanas

 **** Default login credentials for the XigmaNAS® Console (CLI) ****
 username: root or [WebGUI username]
 password: xigmanas

 **** Default login credentials for phpVirtualBox ****
 username: admin
 password: admin

 **** Default login credentials for phpMyAdmin ****
 username: root
 password: leave blank, change password after first login!

It is recommended to change the default passwords as soon as possible.

Copyright:
==========
XigmaNAS® is copyright © 2018-2022 XigmaNAS® (info@xigmanas.com). All Rights Reserved.
XigmaNAS® is a registered trademark of Michael Zoon (zoon01@xigmanas.com). All Rights Reserved.
