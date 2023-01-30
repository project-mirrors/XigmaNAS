--- src/h2.c.orig	2023-01-03 14:05:30.000000000 +0100
+++ src/h2.c	2023-01-30 13:23:27.000000000 +0100
@@ -2042,6 +2042,20 @@
 
     headers.u[2] = htonl(r->h2id);
 
+    if (flags & H2_FLAG_END_STREAM) {
+        /* step r->h2state
+         *   H2_STATE_OPEN -> H2_STATE_HALF_CLOSED_LOCAL
+         * or
+         *   H2_STATE_HALF_CLOSED_REMOTE -> H2_STATE_CLOSED */
+      #if 1
+        ++r->h2state;
+      #else
+        r->h2state = (r->h2state == H2_STATE_HALF_CLOSED_REMOTE)
+          ? H2_STATE_CLOSED
+          : H2_STATE_HALF_CLOSED_LOCAL;
+      #endif
+    }
+
     /* similar to h2_send_data(), but unlike DATA frames there is a HEADERS
      * frame potentially followed by CONTINUATION frame(s) here, and the final
      * HEADERS or CONTINUATION frame here has END_HEADERS flag set.
@@ -2701,9 +2715,11 @@
 }
 
 
+__attribute_noinline__
 static void
 h2_send_end_stream_data (request_st * const r, connection * const con)
 {
+  if (r->h2state != H2_STATE_HALF_CLOSED_LOCAL) {
     union {
       uint8_t c[12];
       uint32_t u[3];          /*(alignment)*/
@@ -2720,6 +2736,7 @@
     /*(ignore window updates when sending 0-length DATA frame with END_STREAM)*/
     chunkqueue_append_mem(con->write_queue,  /*(+3 to skip over align pad)*/
                           (const char *)dataframe.c+3, sizeof(dataframe)-3);
+  }
 
     if (r->h2state != H2_STATE_HALF_CLOSED_REMOTE) {
         /* set timestamp for comparison; not tracking individual stream ids */
