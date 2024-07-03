--- libtransmission/net.c.orig	2020-05-22 13:04:23.389804846 +0200
+++ libtransmission/net.c	2022-12-13 00:24:36.000000000 +0100
@@ -21,6 +21,7 @@
  *****************************************************************************/
 
 #include <errno.h>
+#include <limits.h>
 #include <string.h>
 
 #include <sys/types.h>
@@ -473,7 +474,12 @@
 
 #endif
 
-    if (listen(fd, 128) == -1)
+#ifdef _WIN32
+    if (listen(fd, SOMAXCONN) == -1)
+#else /* _WIN32 */
+    /* Listen queue backlog will be capped to the operating system's limit. */
+    if (listen(fd, INT_MAX) == -1)
+#endif /* _WIN32 */
     {
         *errOut = sockerrno;
         tr_netCloseSocket(fd);
