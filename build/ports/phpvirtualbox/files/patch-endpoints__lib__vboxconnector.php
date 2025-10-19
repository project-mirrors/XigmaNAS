--- endpoints/lib/vboxconnector.php.orig	2025-10-18 00:06:34.803513000 +0200
+++ endpoints/lib/vboxconnector.php	2025-10-19 11:47:16.000000000 +0200
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
@@ -1875,8 +1878,8 @@
 			$m->Platform->X86->setCPUProperty('LongMode', ($guestOS->is64Bit ? 1 : 0));
 		}
 
-		/* secureBootEnabled reported incorrectly by vboxwebsrv
 		$oldFirmware = (string)$m->FirmwareSettings->firmwareType;
+		/* secureBootEnabled reported incorrectly by vboxwebsrv
 		$oldSecureBoot = ($oldFirmware != 'BIOS' && $m->nonVolatileStore->uefiVariableStore != null ? $m->nonVolatileStore->uefiVariableStore->secureBootEnabled : false);
 		*/
 	
@@ -1891,12 +1894,12 @@
 		$m->Platform->X86->setCPUProperty('LongMode', (strpos($args['OSTypeId'],'_64') > - 1 ? 1 : 0));
 		$m->trustedPlatformModule->type = $args['trustedPlatformModule']['type'];
 
-		/* secureBootEnabled reported incorrectly by vboxwebsrv
 		if($oldFirmware == 'BIOS' && $args['firmwareType'] == 'EFI') {
 			$m->nonVolatileStore->initUefiVariableStore(0);
 			$m->nonVolatileStore->uefiVariableStore->enrollOraclePlatformKey();
 			$m->nonVolatileStore->uefiVariableStore->enrollDefaultMsSignatures();
 		}
+		/* secureBootEnabled reported incorrectly by vboxwebsrv
 		if($args['firmwareType'] != 'BIOS' && $oldSecureBoot != (bool)$args['secureBootEnabled']) {
 			$m->nonVolatileStore->uefiVariableStore->secureBootEnabled = (bool)$args['secureBootEnabled'];
 		}
@@ -1938,11 +1941,13 @@
 			* @remarks This must match GMMR0Init; currently we only support page fusion on
 			 *          all 64-bit hosts except Mac OS X */
 
+			/* Page Fusion does not work properly in VirtualBox 7.2
+			 * returns "Page fusion is only supported on 64-bit hosts"
 			if($this->vbox->host->getProcessorFeature('LongMode')) {
 
 				$m->pageFusionEnabled = $args['pageFusionEnabled'];
 			}
-
+			*/
 			$m->Platform->X86->HPETEnabled = $args['HPETEnabled'];
 			$m->setExtraData("VBoxInternal/Devices/VMMDev/0/Config/GetHostTimeDisabled", $args['disableHostTimeSync']);
 			$m->keyboardHIDType = $args['keyboardHIDType'];
@@ -3859,6 +3864,9 @@
 
 		// Save and register
 		$m->saveSettings();
+		if((string)$m->FirmwareSettings->firmwareType == 'EFI') {
+			$m->nonVolatileStore->initUefiVariableStore(0);
+		}
 		$this->vbox->registerMachine($m->handle);
 		$vm = $m->id;
 		$m->releaseRemote();
@@ -4291,7 +4299,8 @@
 			'snapshotFolder' => $m->snapshotFolder,
 			'ClipboardMode' => (string)$m->ClipboardMode,
 			'monitorCount' => $m->GraphicsAdapter->monitorCount,
-			'pageFusionEnabled' => $m->pageFusionEnabled,
+			// Page Fusion does not work properly in 7.2
+			//'pageFusionEnabled' => $m->pageFusionEnabled,
 			'VRDEServer' => (!$m->VRDEServer ? null : array(
 				'enabled' => $m->VRDEServer->enabled,
 				'ports' => $m->VRDEServer->getVRDEProperty('TCP/Ports'),
