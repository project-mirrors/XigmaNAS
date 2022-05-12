--- monitor_kqueue.c.orig	2021-03-19 07:31:03.000000000 +0100
+++ monitor_kqueue.c	2022-05-13 00:10:29.000000000 +0200
@@ -69,6 +69,7 @@
 		close(ev->fd);
 		free(wt);
 		monitor_remove_directory(0, path);
+		free(path);
 		return;
 	} else if ((fflags & (NOTE_WRITE | NOTE_LINK)) ==
 	    (NOTE_WRITE | NOTE_LINK)) {
