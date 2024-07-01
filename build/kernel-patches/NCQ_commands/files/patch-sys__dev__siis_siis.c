--- sys/dev/siis/siis.c.orig	2024-06-23 22:39:58.464044000 +0200
+++ sys/dev/siis/siis.c	2024-07-02 00:46:25.000000000 +0200
@@ -1398,7 +1398,7 @@
 	}
 	xpt_setup_ccb(&ccb->ccb_h, ch->hold[i]->ccb_h.path,
 	    ch->hold[i]->ccb_h.pinfo.priority);
-	if (ccb->ccb_h.func_code == XPT_ATA_IO) {
+	if (ch->hold[i]->ccb_h.func_code == XPT_ATA_IO) {
 		/* READ LOG */
 		ccb->ccb_h.recovery_type = RECOVERY_READ_LOG;
 		ccb->ccb_h.func_code = XPT_ATA_IO;
