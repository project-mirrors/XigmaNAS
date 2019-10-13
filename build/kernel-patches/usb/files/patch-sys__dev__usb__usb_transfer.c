--- usb/usb_transfer.c.orig	2019-10-13 13:47:17.854017000 +0200
+++ usb/usb_transfer.c	2019-10-13 14:18:43.000000000 +0200
@@ -105,6 +105,33 @@
 	},
 };
 
+static const struct usb_config usb_control_ep_quirk_cfg[USB_CTRL_XFER_MAX] = {
+
+	/* This transfer is used for generic control endpoint transfers */
+
+	[0] = {
+		.type = UE_CONTROL,
+		.endpoint = 0x00,	/* Control endpoint */
+		.direction = UE_DIR_ANY,
+		.bufsize = 65535,	/* bytes */
+		.callback = &usb_request_callback,
+		.usb_mode = USB_MODE_DUAL,	/* both modes */
+	},
+
+	/* This transfer is used for generic clear stall only */
+
+	[1] = {
+		.type = UE_CONTROL,
+		.endpoint = 0x00,	/* Control pipe */
+		.direction = UE_DIR_ANY,
+		.bufsize = sizeof(struct usb_device_request),
+		.callback = &usb_do_clear_stall_callback,
+		.timeout = 1000,	/* 1 second */
+		.interval = 50,	/* 50ms */
+		.usb_mode = USB_MODE_HOST,
+	},
+};
+
 /* function prototypes */
 
 static void	usbd_update_max_frame_size(struct usb_xfer *);
@@ -1020,7 +1047,8 @@
 			 * context, else there is a chance of
 			 * deadlock!
 			 */
-			if (setup_start == usb_control_ep_cfg)
+			if (setup_start == usb_control_ep_cfg ||
+			    setup_start == usb_control_ep_quirk_cfg)
 				info->done_p =
 				    USB_BUS_CONTROL_XFER_PROC(udev->bus);
 			else if (xfer_mtx == &Giant)
@@ -3148,7 +3176,8 @@
 	 */
 	iface_index = 0;
 	if (usbd_transfer_setup(udev, &iface_index,
-	    udev->ctrl_xfer, usb_control_ep_cfg, USB_CTRL_XFER_MAX, NULL,
+	    udev->ctrl_xfer, udev->bus->control_ep_quirk ?
+	    usb_control_ep_quirk_cfg : usb_control_ep_cfg, USB_CTRL_XFER_MAX, NULL,
 	    &udev->device_mtx)) {
 		DPRINTFN(0, "could not setup default "
 		    "USB transfer\n");
