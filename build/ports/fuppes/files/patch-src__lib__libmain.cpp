--- src/lib/libmain.cpp.orig	2011-11-01 10:50:10.000000000 +0100
+++ src/lib/libmain.cpp	2021-12-12 04:51:12.000000000 +0100
@@ -139,10 +139,6 @@
   int option_index = -1;
   int c;
 
-  CSharedLog::Print("            FUPPES - %s", CSharedConfig::Shared()->GetAppVersion().c_str());
-  CSharedLog::Print("    the Free UPnP Entertainment Service");
-  CSharedLog::Print("      http://fuppes.ulrich-voelkel.de\n");
-
   #ifdef WIN32
   // TODO These windows ones are currently untested so please test them out
   // anything that does not have the \ / : * ? " < > | characters
@@ -176,18 +172,26 @@
         CSharedLog::Shared()->SetLogLevel(nLogLevel, false);
         break;
       case 'm':
+#if 0
         if(file.Search(optarg)) {
           CSharedLog::SetLogFileName(optarg);
         } else {
           fileFail = true;
         }
+#else
+        CSharedLog::Shared()->SetLogFileName(optarg);
+#endif
         break;
       case 'c':
+#if 0
         if(file.Search(optarg)) {
           CSharedConfig::Shared()->SetConfigFileName(optarg);
         } else {
           fileFail = true;
         }
+#else
+        CSharedConfig::Shared()->SetConfigFileName(optarg);
+#endif
         break;
       case 'a':
         if(directory.Search(optarg)) {
