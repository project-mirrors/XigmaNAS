# Part of XigmaNAS (https://www.xigmanas.com).
# Copyright (c) 2018-2019 XigmaNAS <info@xigmanas.com>.
# XigmaNAS (r) is a registered trademark of Michael Zoon.
# All rights reserved.
#
# swap pager patch, needed for XigmaNAS
#

PATCHFILE=patch-sys__vm__swap_pager.c

all:

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE})
