--- src/VBox/Main/src-client/ConsoleImpl.cpp.orig	2018-11-08 20:42:03.000000000 +0100
+++ src/VBox/Main/src-client/ConsoleImpl.cpp	2018-11-29 00:47:09.000000000 +0100
@@ -9859,7 +9859,7 @@
 
         alock.acquire();
 
-        /* Enable client connections to the server. */
+        /* Enable client connections to the VRDP server. */
         pConsole->i_consoleVRDPServer()->EnableConnections();
 
 #ifdef VBOX_WITH_AUDIO_VRDE
@@ -9877,9 +9877,6 @@
         }
 #endif
 
-        /* Enable client connections to the VRDP server. */
-        pConsole->i_consoleVRDPServer()->EnableConnections();
-
 #ifdef VBOX_WITH_VIDEOREC
         BOOL fVideoRecEnabled = FALSE;
         rc = pConsole->mMachine->COMGETTER(VideoCaptureEnabled)(&fVideoRecEnabled);
