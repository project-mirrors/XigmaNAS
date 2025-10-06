# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2025 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# Fix incomplete speed coverage in link status logging
# Originally ixgbe_if_update_admin_status() only handled 1G and 10G speeds,
# causing any other speeds to display as "1 Gbps" in link status logs.
# 
# satable/14 sys/dev/ixgbe commit/7dc494e6377451469763a8a687032dee2b8324b2
#

PATCHFILE1=patch-if_ix.c.diff

install:
	(cd /usr/src; patch < ${.CURDIR}/files/${PATCHFILE1})
