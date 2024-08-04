		README & CHANGELOG FOR XIGMANAS® 13.3.0.5 - Hesterion

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

UNISON note:
=============
 unison has been updated to version 2.52. The new version introduces
 a new wire protocol and on disk archive format. This new version
 is compatible with 2.51 clients for communication, so it's now
 possible to upgrade one side and then the other.

 The archive files are automatically converted to the new version,
 but once they are converted they are incompatible with the
 previous versions of XigmaNAS version 12.3.0.4 revision 9009 or lower.

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

BUILD 13.3.0.5.10xxx
====================
Changes:
- Upgrade underlying OS to FreeBSD 13.3-RELEASE P4.
- Upgrade jquery to v3.7.1.
- Upgrade zoneinfo to v2024a.
- Upgrade pecl-APCu to v5.1.23.
- Upgrade sudo to v1.9.15p5.
- Upgrade virtualbox-ose to v6.1.50.
- Upgrade dmidecode to v3.5.
- Upgrade gzip to v1.13.
- Upgrade inadyn to v2.12.0.
- Upgrade smartmontools to v7.4.
- Upgrade proftpd to v1.3.8b.
- Upgrade minidlna to v1.3.3.
- Upgrade lighttpd to v1.4.76.
- Upgrade nano to v8.0.
- Upgrade msmtp to v1.8.26.
- Upgrade arconf to B26540.
- Upgrade cdialog to v1.3-20240307.
- Upgrade rsync to v3.3.0.
- Upgrade py-wsdd to v0.8.0.
- Upgrade tmux to v3.4.
- Upgrade transmission to v4.0.6.
- Upgrade mariadb to v10.11.7.
- Upgrade e2fsprogs to v1.47.1.
- Upgrade nut to v20240603.
- Upgrade devcpu-data-intel to v20240531.
- Upgrade python to v3.11.9.
- Upgrade samba to v4.19.7.
- Upgrade iperf3 to v3.17.1.
- Upgrade mDNSResponder to v2200.120.24.
- Upgrade syncthing to v1.27.8.
- Upgrade netatalk to v3.2.1.
- Upgrade devcpu-data-amd to v20240710.
- Upgrade bash to v5.2p32.
- Upgrade php8 to v8.3.10.
- New samba use its own log file.
- New SMB Server Signing and Server/Client Encryption added to WebGUI.


Fixes:
- Fix WebGUI language issue.
- Fix missing certs.
- Fix configure smb

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
XigmaNAS® is copyright © 2018-2024 XigmaNAS® (info@xigmanas.com). All Rights Reserved.
XigmaNAS® is a registered trademark of Michael Zoon (zoon01@xigmanas.com). All Rights Reserved.
