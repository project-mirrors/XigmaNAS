		README & CHANGELOG FOR XIGMANAS® 12.3.0.4 - Artisia

		IMPORTANT - PLEASE READ CAREFULLY


Welcome to the new XigmaNAS® 12.3.0.4 series.
This release also can be used for updating current running XigmaNAS® 12.2.0.4 servers.


General upgrade note:
=====================
Download your configuration file from the backup/restore page and store it in a safe location before upgrading your system.


GEOM RAID-5 note
================
XigmaNAS® 12.3.0.4 releases are the last series supporting GEOM RAID-5.
We kindly ask users to switch to ZFS RaidZ on new installations.
In the upcomming XigmaNAS® 13.x releases, GEOM RAID-5 can no longer be used.
You will no longer be able to access the data of the RAID!

Install from scratch instructions:
==================================
- Download the LiveUSB file, extract the image, and write the image to a USB media. Alternatevely, you can download the LiveCD ISO file and write it to a CD/DVD.
- Boot from the LiveCD/USB device and perform a XigmaNAS® installation onto a new boot media.

Upgrade note!
=============
In case you upgrade any current server with running service "Dynamic DNS", please disable this service first!
After the upgrade you need to setup Dynamic DNS again. The 3th party software inadyn-mt has been replaced for inadyn.
Please make a backup of your current server configuration, the configuration file will be upgraded to v51.

Warning:
========
Do not shortcut the installation procedure!
The LiveCD/USB image contains a file called embedded.img.xz. Do not extract this file and write the extracted image to a media.

Pre-upgrade task for "Full" RootOnZFS installations with a revision older than 6625:
====================================================================================
A one-off task must be performed when you are running a XigmaNAS® installation with a revision older than 6625 before upgrading.
- Goto "Tools > Execute Command" and execute the below command to upgrade to the latest rc.firmware script:

fetch --no-verify-peer -o /etc/rc.firmware "https://sourceforge.net/p/xigmanas/code/HEAD/tree/trunk/etc/rc.firmware?format=raw"

- Then goto "System > Firmware Update" and upgrade your installation as usual.

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

UPS note:
=============
We have added improved output voltage to graphs data.
Ups users need to reset current ups graph data!

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

BUILD 12.3.0.4.9047
===================
Changes:
- Upgrade to FreeBSD 12.3-RELEASE P5.
- Upgrade inadyn to v2.9.1.
- Upgrade unison to v2.51.5.
- Upgrade bash to v5.1 p16.
- Upgrade e2fsprog to v1.46.5.
- Upgrade samba to v4.13.17.
- Upgrade phpmyadmin to v5.1.3.
- Upgrade lighttpd to v1.4.64.
- Upgrade zoneinfo to 2022a.
- Upgrade sudo to v1.9.10.
- Upgrade msmtp to v1.8.20.
- Upgrade smartmontools to v7.3.
- Upgrade cdialog to v1.3-20220414.
- Upgrade php to v7.4.29.
- Upgrade virtualbox-ose to v6.1.34.
- Upgrade nano to v6.3.
- Upgrade syncthing to v1.20.1.
- Upgrade iperf3 to v3.11.
- Upgrade devcpu-data-amd to v20220414.
- Upgrade devcpu-data-intel to v20220419.
- Upgrade gzip to v1.12.
- Upgrade unison to v2.52.1.
- Upgrade rsync to v3.2.4.
- Upgrade arcconf to v4.01.24763.
- Upgrade proftpd to v1.3.7d.
- Upgrade nut to v2022.05.05.

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