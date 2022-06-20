--- endpoints/lib/language.php.orig	2022-06-16 01:45:37.261110000 +0200
+++ endpoints/lib/language.php	2022-06-20 21:15:58.000000000 +0200
@@ -73,6 +73,8 @@
 		$xmlObj = simplexml_load_string(@file_get_contents(VBOX_BASE_LANG_DIR.'/'.$lang.'.xml'));
 		$arrXml = $this->objectsIntoArray($xmlObj);
 
+		if(!array_key_exists('context',$arrXml)) return;
+
 		$lang = array();
 		if(!@$arrXml['context'][0]) $arrXml['context'] = array($arrXml['context']);
 		foreach($arrXml['context'] as $c) {
