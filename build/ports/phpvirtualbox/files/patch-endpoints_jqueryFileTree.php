--- endpoints/jqueryFileTree.php.orig	2021-11-17 08:10:37.421036000 +0100
+++ endpoints/jqueryFileTree.php	2021-11-19 14:21:30.000000000 +0100
@@ -227,33 +227,33 @@
 
 	$entries = getDirEntries($dir, $dirsOnly);
 
-    if(!count($entries))
+    	if(!is_countable($entries))
     	return array();
 
-    $dirents = array();
-    foreach($entries as $path => $type) {
+    	$dirents = array();
+    	foreach($entries as $path => $type) {
 
-        if($type == 'folder' && count($recurse) && (strcasecmp($recurse[0],vbox_basename($path)) == 0)) {
+	if($type == 'folder' && is_countable($recurse) && (strcasecmp($recurse[0],vbox_basename($path)) == 0)) {
 
-        	$entry = folder_entry($path, false, true);
+        $entry = folder_entry($path, false, true);
 
-            $entry['children'] = getdir($dir.DSEP.array_shift($recurse), $dirsOnly, $recurse);
+        $entry['children'] = getdir($dir.DSEP.array_shift($recurse), $dirsOnly, $recurse);
 
-            array_push($dirents, $entry);
+	array_push($dirents, $entry);
 
-        } else {
+    } else {
 
-        	// Push folder on to stack
-        	if($type == 'folder') {
+	// Push folder on to stack
+	if($type == 'folder') {
 
-        	   array_push($dirents, folder_entry($path));
+        array_push($dirents, folder_entry($path));
 
-        	// Push file on to stack
-        	} else {
+        // Push file on to stack
+        } else {
 
-        		$ext = strtolower(preg_replace('/^.*\./', '', $file));
+           $ext = strtolower(preg_replace('/^.*./', '', $file));
 
-                if(count($allowed) && !$allowed['.'.$ext]) continue;
+              if(is_countable($allowed) && !$allowed['.'.$ext]) continue;
 
                 array_push($dirents, file_entry($path));
         	}
