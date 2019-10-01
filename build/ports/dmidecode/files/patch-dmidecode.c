--- dmidecode.c.orig	2018-09-14 15:52:12.000000000 +0200
+++ dmidecode.c	2019-10-02 00:31:11.000000000 +0200
@@ -2,7 +2,7 @@
  * DMI Decode
  *
  *   Copyright (C) 2000-2002 Alan Cox <alan@redhat.com>
- *   Copyright (C) 2002-2018 Jean Delvare <jdelvare@suse.de>
+ *   Copyright (C) 2002-2019 Jean Delvare <jdelvare@suse.de>
  *
  *   This program is free software; you can redistribute it and/or modify
  *   it under the terms of the GNU General Public License as published by
@@ -66,6 +66,7 @@
 #include <stdlib.h>
 #include <unistd.h>
 #include <arpa/inet.h>
+#include <sys/socket.h>
 
 #ifdef __FreeBSD__
 #include <errno.h>
@@ -2469,10 +2470,11 @@
 		"LPDDR",
 		"LPDDR2",
 		"LPDDR3",
-		"LPDDR4" /* 0x1E */
+		"LPDDR4",
+		"Logical non-volatile device" /* 0x1F */
 	};
 
-	if (code >= 0x01 && code <= 0x1E)
+	if (code >= 0x01 && code <= 0x1F)
 		return type[code - 0x01];
 	return out_of_spec;
 }
@@ -3609,7 +3611,7 @@
 		hname = out_of_spec;
 		hlen = strlen(out_of_spec);
 	}
-	printf("%s\t\tRedfish Service Hostname: %*s\n", prefix, hlen, hname);
+	printf("%s\t\tRedfish Service Hostname: %.*s\n", prefix, hlen, hname);
 }
 
 /*
@@ -4990,7 +4992,7 @@
 			printf("\tVendor ID:");
 			dmi_tpm_vendor_id(data + 0x04);
 			printf("\n");
-			printf("\tSpecification Version: %d.%d", data[0x08], data[0x09]);
+			printf("\tSpecification Version: %d.%d\n", data[0x08], data[0x09]);
 			switch (data[0x08])
 			{
 				case 0x01:
@@ -5013,7 +5015,7 @@
 					 */
 					break;
 			}
-			printf("\tDescription: %s", dmi_string(h, data[0x12]));
+			printf("\tDescription: %s\n", dmi_string(h, data[0x12]));
 			printf("\tCharacteristics:\n");
 			dmi_tpm_characteristics(QWORD(data + 0x13), "\t\t");
 			if (h->length < 0x1F) break;
@@ -5534,7 +5536,7 @@
 	off_t fp;
 	size_t size;
 	int efi;
-	u8 *buf;
+	u8 *buf = NULL;
 
 	/*
 	 * We don't want stdout and stderr to be mixed up if both are
@@ -5638,7 +5640,7 @@
 			printf("Failed to get SMBIOS data from sysfs.\n");
 	}
 
-	/* Next try EFI (ia64, Intel-based Mac) */
+	/* Next try EFI (ia64, Intel-based Mac, arm64) */
 	efi = address_from_efi(&fp);
 	switch (efi)
 	{
@@ -5671,6 +5673,7 @@
 	goto done;
 
 memory_scan:
+#if defined __i386__ || defined __x86_64__
 	if (!(opt.flags & FLAG_QUIET))
 		printf("Scanning %s for entry point.\n", opt.devmem);
 	/* Fallback to memory scan (x86, x86_64) */
@@ -5713,6 +5716,7 @@
 			}
 		}
 	}
+#endif
 
 done:
 	if (!found && !(opt.flags & FLAG_QUIET))
