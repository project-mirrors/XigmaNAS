--- endpoints/jqueryFileTree.php.orig	2022-05-06 14:23:41.328996000 +0200
+++ endpoints/jqueryFileTree.php	2022-05-06 14:29:59.000000000 +0200
@@ -223,6 +223,8 @@
  */
 function getdir($dir, $dirsOnly=false, $recurse=array()) {
 
+	global $allowed_exts;
+
 	if(!$dir) $dir = DSEP;
 
 	$entries = getDirEntries($dir, $dirsOnly);
@@ -251,9 +253,9 @@
         	// Push file on to stack
         	} else {
 
-        		$ext = strtolower(preg_replace('/^.*\./', '', $file));
+        		$ext = strtolower(preg_replace('/^.*\./', '', $path));
 
-                if(count($allowed) && !$allowed['.'.$ext]) continue;
+                if(count($allowed_exts) && !$allowed_exts['.'.$ext]) continue;
 
                 array_push($dirents, file_entry($path));
         	}
