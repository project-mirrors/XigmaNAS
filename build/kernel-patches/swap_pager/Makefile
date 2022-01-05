# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2022 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# swap pager patch, needed for XigmaNAS
#

PATCHFILE=patch-sys__vm__swap_pager.c

all:

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE})
