--- ext/mysqli/php_mysqli_structs.h.orig	2021-01-05 14:54:54.000000000 +0100
+++ ext/mysqli/php_mysqli_structs.h	2021-01-10 21:39:54.000000000 +0100
@@ -34,7 +34,7 @@
 #define FALSE 0
 #endif
 
-#ifdef MYSQLI_USE_MYSQLND
+#if defined(MYSQLI_USE_MYSQLND)
 #include "ext/mysqlnd/mysqlnd.h"
 #include "mysqli_mysqlnd.h"
 #else
