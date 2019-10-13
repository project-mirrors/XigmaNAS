--- usb/controller/xhci_pci.c.orig	2019-10-13 13:47:17.668583000 +0200
+++ usb/controller/xhci_pci.c	2019-10-13 14:04:36.000000000 +0200
@@ -147,7 +147,7 @@
 		return ("Intel Lewisburg USB 3.0 controller");
 	case 0xa2af8086:
 		return ("Intel Union Point USB 3.0 controller");
-	case 0x36d88086:
+	case 0xa36d8086:
 		return ("Intel Cannon Lake USB 3.1 controller");
 
 	case 0xa01b177d:
