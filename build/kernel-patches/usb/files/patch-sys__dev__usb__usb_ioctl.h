--- usb/usb_ioctl.h.orig	2019-10-13 13:47:17.850666000 +0200
+++ usb/usb_ioctl.h	2019-10-13 14:08:11.000000000 +0200
@@ -220,7 +220,7 @@
 } USB_IOCTL_STRUCT_ALIGN(1);
 
 struct usb_fs_open {
-#define	USB_FS_MAX_BUFSIZE (1 << 18)
+#define USB_FS_MAX_BUFSIZE (1 << 25)    /* 32 MBytes */
 	uint32_t max_bufsize;
 #define	USB_FS_MAX_FRAMES		(1U << 12)
 #define	USB_FS_MAX_FRAMES_PRE_SCALE	(1U << 31)	/* for ISOCHRONOUS transfers */
