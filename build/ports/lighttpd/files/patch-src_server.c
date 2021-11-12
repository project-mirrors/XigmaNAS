--- src/server.c.orig	2021-10-29 00:58:31.000000000 +0200
+++ src/server.c	2021-11-12 01:43:28.000000000 +0100
@@ -962,7 +962,7 @@
             close(pid_fd);
             pid_fd = -1;
     }
-    if (srv->srvconf.pid_file) buffer_clear(srv->srvconf.pid_file);
+    srv->srvconf.pid_file = NULL;
 
     /* (original process is backgrounded -- even if no active connections --
      *  to allow graceful shutdown tasks to be run by server and by modules) */
@@ -1024,7 +1024,7 @@
         server_sockets_close(srv);
         remove_pid_file(srv);
         /*(prevent more removal attempts)*/
-        if (srv->srvconf.pid_file) buffer_clear(srv->srvconf.pid_file);
+        srv->srvconf.pid_file = NULL;
     }
 }
 
@@ -1163,7 +1163,7 @@
 	}
 
 	if (test_config) {
-		if (srv->srvconf.pid_file) buffer_clear(srv->srvconf.pid_file);
+		srv->srvconf.pid_file = NULL;
 		if (1 == test_config) {
 			printf("Syntax OK\n");
 		} else { /*(test_config > 1)*/
@@ -1186,7 +1186,7 @@
 		graceful_shutdown = 1;
 		srv->sockets_disabled = 2;
 		srv->srvconf.dont_daemonize = 1;
-		if (srv->srvconf.pid_file) buffer_clear(srv->srvconf.pid_file);
+		srv->srvconf.pid_file = NULL;
 		if (srv->srvconf.max_worker) {
 			srv->srvconf.max_worker = 0;
 			log_error(srv->errh, __FILE__, __LINE__,
@@ -1707,7 +1707,7 @@
 			close(pid_fd);
 			pid_fd = -1;
 		}
-		if (srv->srvconf.pid_file) buffer_clear(srv->srvconf.pid_file);
+		srv->srvconf.pid_file = NULL;
 
 		fdlog_pipes_abandon_pids();
 		srv->pid = getpid();
