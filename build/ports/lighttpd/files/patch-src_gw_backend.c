--- src/gw_backend.c.orig	2020-12-17 10:11:11.000000000 +0100
+++ src/gw_backend.c	2020-12-26 14:54:34.000000000 +0100
@@ -386,7 +386,8 @@
             errno = EINVAL;
             return -1;
         }
-        else {
+        else if (host->host->size) {
+            /*(skip if constant string set in gw_set_defaults_backend())*/
             /* overwrite host->host buffer with IP addr string so that
              * any further use of gw_host does not block on DNS lookup */
             buffer *h;
@@ -1534,7 +1535,7 @@
                 }
 
                 if (buffer_string_is_empty(host->host)) {
-                    static const buffer lhost = {CONST_STR_LEN("127.0.0.1"), 0};
+                    static const buffer lhost ={CONST_STR_LEN("127.0.0.1")+1,0};
                     host->host = &lhost;
                 }
 
@@ -2106,10 +2107,11 @@
 
             if ((0 != hctx->wb.bytes_in || -1 == hctx->wb_reqlen)
                 && !chunkqueue_is_empty(&r->reqbody_queue)) {
-                if (hctx->stdin_append
-                    && chunkqueue_length(&hctx->wb) < 65536 - 16384) {
-                    handler_t rca = hctx->stdin_append(hctx);
-                    if (HANDLER_GO_ON != rca) return rca;
+                if (hctx->stdin_append) {
+                    if (chunkqueue_length(&hctx->wb) < 65536 - 16384) {
+                        handler_t rca = hctx->stdin_append(hctx);
+                        if (HANDLER_GO_ON != rca) return rca;
+                    }
                 }
                 else
                     chunkqueue_append_chunkqueue(&hctx->wb, &r->reqbody_queue);
