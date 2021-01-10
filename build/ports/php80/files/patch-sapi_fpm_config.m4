--- sapi/fpm/config.m4.orig	2021-01-05 14:54:54.000000000 +0100
+++ sapi/fpm/config.m4	2021-01-10 21:53:50.000000000 +0100
@@ -315,7 +315,7 @@
     AC_MSG_RESULT([no])
   ])
 
-  if test "$have_lq" = "tcp_info"; then
+  if test "$have_lq" = "so_listenq"; then
     AC_DEFINE([HAVE_LQ_TCP_INFO], 1, [do we have TCP_INFO?])
   fi
 
