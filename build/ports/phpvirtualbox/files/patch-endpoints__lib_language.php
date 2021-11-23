--- endpoints/lib/language.php.orig	2021-11-23 21:13:55.000000000 +0100
+++ endpoints/lib/language.php	2021-11-23 21:14:14.000000000 +0100
@@ -72,7 +72,7 @@
 
 		$xmlObj = simplexml_load_string(@file_get_contents(VBOX_BASE_LANG_DIR.'/'.$lang.'.xml'));
 		$arrXml = $this->objectsIntoArray($xmlObj);
-
+		if(!array_key_exists('context',$arrXml)) return;
 		$lang = array();
 		if(!@$arrXml['context'][0]) $arrXml['context'] = array($arrXml['context']);
 		foreach($arrXml['context'] as $c) {
