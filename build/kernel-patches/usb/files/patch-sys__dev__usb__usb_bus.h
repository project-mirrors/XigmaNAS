--- usb/usb_bus.h.orig	2019-10-13 13:47:17.844736000 +0200
+++ usb/usb_bus.h	2019-10-13 14:12:31.000000000 +0200
@@ -129,6 +129,7 @@
 	uint8_t	do_probe;		/* set if USB should be re-probed */
 	uint8_t no_explore;		/* don't explore USB ports */
 	uint8_t dma_bits;		/* number of DMA address lines */
+	uint8_t control_ep_quirk;       /* need 64kByte buffer for data stage */
 };
 
 #endif					/* _USB_BUS_H_ */
