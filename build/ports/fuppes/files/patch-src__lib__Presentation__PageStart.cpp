--- src/lib/Presentation/PageStart.cpp.orig	2011-09-22 10:45:44.000000000 +0200
+++ src/lib/Presentation/PageStart.cpp	2021-12-12 15:04:53.000000000 +0100
@@ -34,26 +34,17 @@
 {
   std::stringstream result;
   
-  result << "<h1>system information</h1>" << endl;  
+  result << "<h1>System Information</h1>" << endl;  
   
   result << "<p>" << endl;
-  result << "Version: " << CSharedConfig::Shared()->GetAppVersion() << "<br />" << endl;
   result << "Hostname: " << CSharedConfig::Shared()->networkSettings->GetHostname() << "<br />" << endl;
-  result << "OS: " << CSharedConfig::Shared()->GetOSName() << " " << CSharedConfig::Shared()->GetOSVersion() << "<br />" << endl;
   //result << "SQLite: " << CContentDatabase::Shared()->GetLibVersion() << endl;
-  result << "</p>" << endl;
-  
-  result << "<p>" << endl;
-  result << "build at: " << __DATE__ << " - " << __TIME__ << "<br />" << endl;
-  result << "build with: " << __VERSION__ << "<br />" << endl;
-  result << "</p>" << endl;
-  
+  result << "</p>" << endl;  
 
   // uptime
   fuppes::DateTime now = fuppes::DateTime::now();
   int uptime = now.toInt() - CSharedConfig::Shared()->GetFuppesInstance(0)->startTime().toInt();
 
-
   int days;
   int hours;
   int minutes;
@@ -65,7 +56,6 @@
   minutes = minutes % 60;
   days = hours / 24;
   hours = hours % 24;
-
   
   result << "<p>" << endl;
   result << "uptime: " << days << " days " << hours << " hours " << minutes << " minutes " << seconds << " seconds" << "<br />" << endl;
@@ -79,20 +69,15 @@
   result << "cfgfile: " << CSharedConfig::Shared()->filename() << "<br />" << endl;
   result << "dbfile: " << CSharedConfig::Shared()->databaseSettings->dbConnectionParams().filename << "<br />" << endl;
   result << "thumbnaildir: " <<  PathFinder::findThumbnailsDir() << "<br />" << endl;
-  result << "</p>" << endl;
-
-  result << "<p>" << endl;
-  result << "<a href=\"http://sourceforge.net/projects/fuppes/\">http://sourceforge.net/projects/fuppes/</a><br />" << endl;
-  result << "</p>" << endl;
+  result << "</p>" << endl;  
   
-  
-  result << "<h1>database status</h1>" << endl;  
+  result << "<h1>Database Status</h1>" << endl;  
   result << buildObjectStatusTable() << endl;
 
   result << "<ul>";
-  result << "<li><a href=\"javascript:fuppesCtrl('DatabaseRebuild');\">rebuild database</a></li>";
-  result << "<li><a href=\"javascript:fuppesCtrl('DatabaseUpdate');\">update database</a></li>";
-  result << "<li><a href=\"javascript:fuppesCtrl('VfolderUpdate');\">update virtual folders</a></li>";
+  result << "Database Options:" << endl;
+  result << "<li><a href=\"javascript:fuppesCtrl('DatabaseRebuild');\">Build/Rebuild Database</a></li>";
+  result << "<li><a href=\"javascript:fuppesCtrl('DatabaseUpdate');\">Update Existing Database Items</a></li>";

   result << "</ul>";
   
   return result.str().c_str();
