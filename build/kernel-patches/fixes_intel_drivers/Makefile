# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2020 XigmaNAS® <info@xigmanas.com>.
# XigmaNAS is a registered trademark of Michael Zoon.
# All rights reserved.
#
# Add Revision 356302 and 354021 from stable 12 for intel nic drivers.
#
install:
	# patch-if_em.c
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-if_em.c.diff
	# patch-if_ix.c
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-if_ix.c.diff
	# patch-ixl_pf_main.c
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-ixl_pf_main.c.diff
