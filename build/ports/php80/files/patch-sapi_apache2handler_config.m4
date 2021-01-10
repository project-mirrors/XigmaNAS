--- sapi/apache2handler/config.m4.orig	2021-01-05 14:54:54.000000000 +0100
+++ sapi/apache2handler/config.m4	2021-01-10 21:43:43.000000000 +0100
@@ -64,7 +64,7 @@
   fi
 
   APXS_LIBEXECDIR='$(INSTALL_ROOT)'`$APXS -q LIBEXECDIR`
-  if test -z `$APXS -q SYSCONFDIR`; then
+  if true; then
     INSTALL_IT="\$(mkinstalldirs) '$APXS_LIBEXECDIR' && \
                  $APXS -S LIBEXECDIR='$APXS_LIBEXECDIR' \
                        -i -n php"
