# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2024 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# Fix SATA NCQ error recovery after 25375b1415 for 14.1
# https://cgit.freebsd.org/src/commit/?id=87085c12ba8fa51f777bc636df67008b45e20d1c
#

PATCHFILE1=patch-sys__dev__ahci_ahci.c
PATCHFILE2=patch-sys__dev__mvs_mvs.c
PATCHFILE3=patch-sys__dev__siis_siis.c

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE1})
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE2})
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE3})
