--- src/mod_alias.c.orig	2021-10-29 00:58:31.000000000 +0200
+++ src/mod_alias.c	2021-11-12 01:23:10.000000000 +0100
@@ -128,7 +128,7 @@
     if (0 == path_len || path_len < basedir_len) return HANDLER_GO_ON;
 
     const uint32_t uri_len = path_len - basedir_len;
-    const char * const uri_ptr = r->physical.path.ptr + basedir_len;
+    char * uri_ptr = r->physical.path.ptr + basedir_len;
     data_string * const ds = (data_string *)
       (!r->conf.force_lowercase_filenames
         ? array_match_key_prefix_klen(aliases, uri_ptr, uri_len)
@@ -159,8 +159,10 @@
      * (though resulting r->physical.basedir would not be a dir))*/
     if (vlen != basedir_len + alias_len) {
         const uint32_t nlen = vlen + uri_len - alias_len;
-        if (path_len + buffer_string_space(&r->physical.path) < nlen)
+        if (path_len + buffer_string_space(&r->physical.path) < nlen) {
             buffer_string_prepare_append(&r->physical.path, nlen - path_len);
+            uri_ptr = r->physical.path.ptr + basedir_len;
+        }
         memmove(r->physical.path.ptr + vlen,
                 uri_ptr + alias_len, uri_len - alias_len);
         buffer_truncate(&r->physical.path, nlen);
