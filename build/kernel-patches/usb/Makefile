install:
	# xhci_pci.c (Rev. 351019)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-sys__dev__usb__controller__xhci_pci.c

	# xhci.c (Rev. 353170+353178)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-sys__dev__usb__controller__xhci.c

	# usb_ioctl.h (Rev. 353174)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-sys__dev__usb__usb_ioctl.h

	# usb_bus.h (Rev. 353178)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-sys__dev__usb__usb_bus.h

	# usb_transfer.c (Rev. 353178)
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-sys__dev__usb__usb_transfer.c
