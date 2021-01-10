--- ext/mysqli/mysqli_api.c.orig	2021-01-05 14:54:54.000000000 +0100
+++ ext/mysqli/mysqli_api.c	2021-01-10 17:18:29.000000000 +0100
@@ -29,7 +29,9 @@
 #include "zend_smart_str.h"
 #include "php_mysqli_structs.h"
 #include "mysqli_priv.h"
+#if defined(MYSQLI_USE_MYSQLND)
 #include "ext/mysqlnd/mysql_float_to_double.h"
+#endif
 
 #define ERROR_ARG_POS(arg_num) (getThis() ? (arg_num-1) : (arg_num))
 
