--- endpoints/screen.php.orig	2022-06-16 01:45:37.265688000 +0200
+++ endpoints/screen.php	2022-06-20 21:33:56.000000000 +0200
@@ -87,13 +87,13 @@
 
 		// Let the browser cache images for 3 seconds
 		$ctime = 0;
-		if(strpos($_SERVER['HTTP_IF_NONE_MATCH'],'_')) {
+		if(strpos($_SERVER['HTTP_IF_NONE_MATCH'] ?? '','_')) {
 			$ctime = preg_replace("/.*_/",str_replace('"','',$_SERVER['HTTP_IF_NONE_MATCH']));
-		} else if(strpos($_ENV['HTTP_IF_NONE_MATCH'],'_')) {
+		} else if(strpos($_ENV['HTTP_IF_NONE_MATCH'] ?? '','_')) {
 			$ctime = preg_replace("/.*_/",str_replace('"','',$_ENV['HTTP_IF_NONE_MATCH']));
-		} else if(strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'],'GMT')) {
+		} else if(strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '','GMT')) {
 			$ctime = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
-		} else if(strpos($_ENV['HTTP_IF_MODIFIED_SINCE'],'GMT')) {
+		} else if(strpos($_ENV['HTTP_IF_MODIFIED_SINCE'] ?? '','GMT')) {
 			$ctime = strtotime($_ENV['HTTP_IF_MODIFIED_SINCE']);
 		}
 		
