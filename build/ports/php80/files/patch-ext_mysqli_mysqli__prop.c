--- ext/mysqli/mysqli_prop.c.orig	2021-01-05 14:54:54.000000000 +0100
+++ ext/mysqli/mysqli_prop.c	2021-01-10 21:33:05.000000000 +0100
@@ -24,7 +24,9 @@
 #include "php.h"
 #include "php_ini.h"
 #include "ext/standard/info.h"
+#if defined(MYSQLI_USE_MYSQLND)
 #include "php_mysqli_structs.h"
+#endif
 #include "mysqli_priv.h"
 
 #define CHECK_STATUS(value, quiet) \
