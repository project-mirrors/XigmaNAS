# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2022 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# vfs_acl patch, needed for XigmaNAS samba416 on 13.2
#

PATCHFILE1=patch-lib__libc__sys__open.2
PATCHFILE2=patch-sys__kern__vfs_acl.c

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE1})
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE2})
