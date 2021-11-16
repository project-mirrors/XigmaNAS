--- usr.sbin/cpucontrol/intel.c.orig	2021-11-16 22:35:15.852137000 +0100
+++ usr.sbin/cpucontrol/intel.c	2021-11-16 22:53:14.000000000 +0100
@@ -242,8 +242,8 @@
 			    (flags & ext_table[i].cpu_flags) != 0)
 				goto matched;
 		}
-	} else
-		goto fail;
+	}
+	goto fail;
 
 matched:
 	if (revision >= fw_header->revision) {
