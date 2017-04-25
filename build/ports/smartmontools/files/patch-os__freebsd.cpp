--- os_freebsd.cpp.orig	2016-03-28 01:32:54.000000000 +0200
+++ os_freebsd.cpp	2017-04-25 13:51:40.000000000 +0200
@@ -321,39 +321,6 @@
   out.out_regs.error = request.error;
   out.out_regs.sector_count_16 = request.u.ata.count;
   out.out_regs.lba_48 = request.u.ata.lba;
-
-
-  // Command specific processing
-  if (in.in_regs.command == ATA_SMART_CMD
-       && in.in_regs.features == ATA_SMART_STATUS
-       && in.out_needed.lba_high)
-  {
-    unsigned const char normal_lo=0x4f, normal_hi=0xc2;
-    unsigned const char failed_lo=0xf4, failed_hi=0x2c;
-
-    // Cyl low and Cyl high unchanged means "Good SMART status"
-    if (!(out.out_regs.lba_mid==normal_lo && out.out_regs.lba_high==normal_hi)
-    // These values mean "Bad SMART status"
-        && !(out.out_regs.lba_mid==failed_lo && out.out_regs.lba_high==failed_hi))
-
-    {
-      // We haven't gotten output that makes sense; print out some debugging info
-      char buf[512];
-      snprintf(buf, sizeof(buf),
-        "CMD=0x%02x\nFR =0x%02x\nNS =0x%02x\nSC =0x%02x\nCL =0x%02x\nCH =0x%02x\nRETURN =0x%04x\n",
-        (int)request.u.ata.command,
-        (int)request.u.ata.feature,
-        (int)request.u.ata.count,
-        (int)((request.u.ata.lba) & 0xff),
-        (int)((request.u.ata.lba>>8) & 0xff),
-        (int)((request.u.ata.lba>>16) & 0xff),
-        (int)request.error);
-      printwarning(BAD_SMART,buf);
-      out.out_regs.lba_high = failed_hi; 
-      out.out_regs.lba_mid = failed_lo;
-    }
-  }
-
   return true;
 }
 
@@ -1936,6 +1903,8 @@
   int i;
   const char * test_name = name;
 
+  memset(&ccb, 0, sizeof(ccb));
+
   // if dev_name null, or string length zero
   if (!name || !*name)
     return 0;
