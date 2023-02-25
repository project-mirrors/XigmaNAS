# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2023 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# Default to always accepting the PHY that's present, see commit: https://github.com/freebsd/freebsd-src/commit/6f3c0d2764f230444818a95009028861493c1594
#

PATCHFILE=patch-igc_i225.c.diff

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE})
