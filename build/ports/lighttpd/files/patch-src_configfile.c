--- src/configfile.c.orig	2018-10-15 00:35:58.000000000 +0200
+++ src/configfile.c	2018-10-22 01:51:52.000000000 +0200
@@ -1460,7 +1460,7 @@
 			}
 			if (0 != wstatus) {
 				log_error_write(srv, __FILE__, __LINE__, "SSsd",
-						"commaned \"", cmd, "\" exited non-zero:", WEXITSTATUS(wstatus));
+						"command \"", cmd, "\" exited non-zero:", WEXITSTATUS(wstatus));
 				ret = -1;
 			}
 
