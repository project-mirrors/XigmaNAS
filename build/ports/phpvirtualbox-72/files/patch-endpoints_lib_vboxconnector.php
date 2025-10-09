--- endpoints/lib/vboxconnector.php.orig	2025-10-08 03:41:46.325272000 +0200
+++ endpoints/lib/vboxconnector.php	2025-10-09 04:59:00.000000000 +0200
@@ -113,6 +113,8 @@
 	 */
 	var $dsep = null;
 
+	var $client = null;
+
 	/**
 	 * Obtain configuration settings and set object vars
 	 * @param boolean $useAuthMaster use the authentication master obtained from configuration class
@@ -388,7 +390,8 @@
 
 			// The amount of time we will wait for events is determined by
 			// the amount of listeners - at least half a second
-			$listenerWait = max(100,intval(500/count($this->persistentRequest['vboxEventListeners'])));
+			$listenerCount = count($this->persistentRequest['vboxEventListeners']);
+			$listenerWait = max(100,intval(500/($listenerCount > 0 ? $listenerCount : 1)));
 		}
 
 		// Get events from each configured event listener
@@ -3859,6 +3862,9 @@
 
 		// Save and register
 		$m->saveSettings();
+		if((string)$m->FirmwareSettings->firmwareType == 'EFI') {
+			$m->nonVolatileStore->initUefiVariableStore(0);
+		}
 		$this->vbox->registerMachine($m->handle);
 		$vm = $m->id;
 		$m->releaseRemote();
