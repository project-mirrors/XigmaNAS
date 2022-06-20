--- endpoints/api.php.orig	2022-06-16 01:45:37.260051000 +0200
+++ endpoints/api.php	2022-06-20 21:09:49.000000000 +0200
@@ -334,6 +334,9 @@
 
 	// Just append to $vbox->errors and let it get
 	// taken care of below
+	if(!isset($vbox)) {
+		$vbox = new stdClass();
+	}
 	if(!$vbox || !$vbox->errors) {
 		$vbox->errors = array();
 	}
@@ -342,7 +345,7 @@
 
 
 // Add any messages
-if($vbox && count($vbox->messages)) {
+if($vbox && isset($vbox->messages)?count($vbox->messages):false) {
 	foreach($vbox->messages as $m)
 		$response['messages'][] = 'vboxconnector('.$request['fn'] .'): ' . $m;
 }
@@ -360,7 +363,7 @@
 		if($e->getCode() == vboxconnector::PHPVB_ERRNO_CONNECT && isset($vbox->settings))
 			$d .= "\n\nLocation:" . $vbox->settings->location;
 
-		$response['messages'][] = htmlentities($e->getMessage()).' ' . htmlentities($details);
+		$response['messages'][] = htmlentities($e->getMessage()). htmlentities(' '. $details);
 
 		$response['errors'][] = array(
 			'error'=> ($e->getCode() & vboxconnector::PHPVB_ERRNO_HTML ? $e->getMessage() : htmlentities($e->getMessage())),
