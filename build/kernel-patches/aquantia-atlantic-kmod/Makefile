# Part of XigmaNAS (https://www.xigmanas.com).
# Copyright © 2018-2020 XigmaNAS®<info@xigmanas.com>.
# XigmaNAS (r) is a registered trademark of Michael Zoon.
# All rights reserved.
#
# Aquantia AQC107 Multigigabit NIC Driver driver for FreeBSD 11/12/13
#
# Taken from: https://github.com/Aquantia/aqtion-freebsd/releases
#

ARCHIVE=	Aquantia-aqtion-freebsd-0.0.5-4816644_GH0.tar.gz
SRCDIR=			${XIGMANAS_WORKINGDIR}/aquantia


install:
	patch -i files/aquantia-aqtion.patch /usr/src/sys/modules/Makefile

	@mkdir -pv ${SRCDIR}
	@tar -xvf ${.CURDIR}/files/${ARCHIVE} -C ${SRCDIR}

	mkdir /usr/src/sys/modules/aqtion
	@cp -Rpv ${SRCDIR}/aqtion-freebsd-4816644/ /usr/src/sys/modules/aqtion
