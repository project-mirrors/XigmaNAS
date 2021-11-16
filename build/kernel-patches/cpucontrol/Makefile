# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2021 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# cpucontrol patch, needed for microcode firmware PR: 256502
#

PATCHFILE=patch-usr.sbin__cpucontrol__intel.c

all:

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE})
