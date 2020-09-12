--- libtransmission/session-id.c.orig	2020-05-22 13:04:23.391804861 +0200
+++ libtransmission/session-id.c	2020-09-12 22:09:28.000000000 +0200
@@ -75,22 +75,6 @@
 
     lock_file = tr_sys_file_open(lock_file_path, TR_SYS_FILE_READ | TR_SYS_FILE_WRITE | TR_SYS_FILE_CREATE, 0600, &error);
 
-    if (lock_file != TR_BAD_SYS_FILE)
-    {
-        if (tr_sys_file_lock(lock_file, TR_SYS_FILE_LOCK_EX | TR_SYS_FILE_LOCK_NB, &error))
-        {
-#ifndef _WIN32
-            /* Allow any user to lock the file regardless of current umask */
-            fchmod(lock_file, 0644);
-#endif
-        }
-        else
-        {
-            tr_sys_file_close(lock_file, NULL);
-            lock_file = TR_BAD_SYS_FILE;
-        }
-    }
-
     if (error != NULL)
     {
         tr_logAddError("Unable to create session lock file (%d): %s", error->code, error->message);
