# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2022 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# Aquantia Multigigabit NIC Driver driver for FreeBSD 12/13
#
# Taken from: https://github.com/Aquantia/aqtion-freebsd/releases
#

ARCHIVE=	Aquantia-aqtion-freebsd-0.0.5-4816644_GH0.tar.gz
SRCDIR=		${XIGMANAS_WORKINGDIR}/aquantia
PATCHFILE1=	patch-aqtion__aq_hw.c
PATCHFILE2=	patch-aqtion__aq_main.c

install:
	patch -i files/patch-modules__aquantia-aqtion_makefile.diff /usr/src/sys/modules/Makefile

	@mkdir -pv ${SRCDIR}
	@tar -xvf ${.CURDIR}/files/${ARCHIVE} -C ${SRCDIR}

	mkdir /usr/src/sys/modules/aqtion
	@cp -Rpv ${SRCDIR}/aqtion-freebsd-4816644/ /usr/src/sys/modules/aqtion

	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE1})
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE2})