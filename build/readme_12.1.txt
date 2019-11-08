		README & CHANGELOG FOR XIGMANAS® 12.1.0.4 - Ingva

		IMPORTANT - PLEASE READ CAREFULLY

General note:
=============
Download your configuration file from the backup/restore page and store it in a safe location before upgrading your system.

Important information for users running a XigmaNAS® installation with a revision older than 6625:
================================================================================================
It is not recommended to perform an in-place upgrade via the firmware update page because of recent changes to the partition layout.
- Download the LiveUSB file, extract the image, and write the image to a USB media. Alternatevely, you can download the LiveCD ISO file and write it to a CD/DVD.
- Download your configuration file from the backup/restore page and store it in a safe location.
- Backup all other data partitions that exist on the current XigmaNAS® boot media.
- Boot from the newly created LiveCD/USB and perform a XigmaNAS® installation onto a new boot media.
- After a successful installation, remove the LiveCD/USB device, boot from your new XigmaNAS® boot media and restore your previously saved configuration.

Install from scratch instructions:
==================================
- Download the LiveUSB file, extract the image, and write the image to a USB media. Alternatevely, you can download the LiveCD ISO file and write it to a CD/DVD.
- Boot from the LiveCD/USB device and perform a XigmaNAS® installation onto a new boot media.

Warning:
========
Do not shortcut the installation procedure!
The LiveCD/USB image contains a file called embedded.img.xz. Do not extract this file and write the extracted image to a media.

Pre-upgrade task for "Full" RootOnZFS installations with a revision older than 6625
===================================================================================
A one-off task must be performed when you are running a XigmaNAS® installation with a revision older than 6625 before upgrading.
- Goto "Tools > Execute Command" and execute the below command to upgrade to the latest rc.firmware script:

fetch --no-verify-peer -o /etc/rc.firmware "https://sourceforge.net/p/xigmanas/code/HEAD/tree/trunk/etc/rc.firmware?format=raw"

- Then goto "System > Firmware Update" and upgrade your installation as usual.

Post-upgrade tasks:
===================
- You may need to clear your browser's cache to circumvent display issues.
- Make a backup of your configuration file and store it in a safe location.

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

BUILD 12.1.0.4.7091
===================
Changes:
- Upgrade to FreeBSD 12.1-RELEASE P0.
- Add sesutil.
- Add sas2ircu.
- Add sas3ircu.
- Add Aquantia AQtion (Atlantic) AQC107 Network Driver.
- Upgrade arcconf to 3.02.23600.
- Upgrade php to 7.3.11.
- Upgrade virtualbox-ose to 5.2.34.
- Upgrade sudo to 1.8.29.
- Upgrade samba to 4.10.10.
- pecl-APCu to 5.1.18.
- Upgrade syncthing to 1.3.1.

Fixes:
- Display releases of minor/major OS upgrades and minor/major product upgrades, beta versions and nightlies.


Default login credentials:
==========================

 **** Default login credentials for the XigmaNAS® WEBGUI ****
 username: admin
 password: xigmanas

 **** Default login credentials for the XigmaNAS® Console (CLI) ****
 username: root
 password: xigmanas

 **** Default login credentials for phpVirtualBox ****
 username: admin
 password: admin

 **** Default login credentials for phpMyAdmin ****
 username: root
 password: leave blank, change password after first login!

It is recommended to change the default passwords as soon as possible.

XigmaNAS® is copyright © 2018 - 2019 XigmaNAS® (info@xigmanas.com. All Rights Reserved.
XigmaNAS® is a registered trademark of Michael Zoon (zoon01@xigmanas.com). All Rights Reserved.
