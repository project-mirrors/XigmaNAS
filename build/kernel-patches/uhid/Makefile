# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2023 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# Stop read-ahead from USB IN endpoint PR: 263995.
#

PATCHFILE=patch-usb_input_uhid.c.diff

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE})
