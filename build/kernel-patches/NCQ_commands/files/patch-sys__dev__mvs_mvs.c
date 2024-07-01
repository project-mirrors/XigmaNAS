--- sys/dev/mvs/mvs.c.orig	2024-06-23 22:39:58.257153000 +0200
+++ sys/dev/mvs/mvs.c	2024-07-02 00:43:34.000000000 +0200
@@ -1799,7 +1799,7 @@
 	}
 	xpt_setup_ccb(&ccb->ccb_h, ch->hold[i]->ccb_h.path,
 	    ch->hold[i]->ccb_h.pinfo.priority);
-	if (ccb->ccb_h.func_code == XPT_ATA_IO) {
+	if (ch->hold[i]->ccb_h.func_code == XPT_ATA_IO) {
 		/* READ LOG */
 		ccb->ccb_h.recovery_type = RECOVERY_READ_LOG;
 		ccb->ccb_h.func_code = XPT_ATA_IO;
