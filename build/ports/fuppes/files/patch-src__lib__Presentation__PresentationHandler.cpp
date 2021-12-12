--- src/lib/Presentation/PresentationHandler.cpp.orig	2011-11-01 10:50:10.000000000 +0100
+++ src/lib/Presentation/PresentationHandler.cpp	2021-12-12 16:24:08.000000000 +0100
@@ -367,7 +367,7 @@
   sResult << "<div id=\"logo\"></div>" << endl;
 
   sResult << "<div id=\"header-text\">" << endl <<
-    "FUPPES - Free UPnP Entertainment Service<br />" << endl <<
+    "FUPPES - XigmaNAS DLNA/UPnP Service<br />" << endl <<
     "<span>" <<
     "Version: " << CSharedConfig::Shared()->GetAppVersion() << " &bull; " <<
     "Host: "    << CSharedConfig::Shared()->networkSettings->GetHostname() << " &bull; " <<
@@ -443,11 +443,6 @@
   sResult << "<div class=\"clear\"></div>" << endl;
   
   sResult << "</div>" << endl; // wrapper
-  
-  sResult << "<div id=\"footer\">" << endl;
-  sResult << "<span>copyright &copy; 2005-2011 Ulrich V&ouml;lkel</span>";
-  sResult << "</div>" << endl; // footer
-
   
   sResult << "</body>";
   sResult << "</html>";
