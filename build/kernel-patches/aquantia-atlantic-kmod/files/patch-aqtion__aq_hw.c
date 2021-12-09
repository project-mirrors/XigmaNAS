--- sys/modules/aqtion/aq_hw.c.orig	2019-09-24 16:45:34.000000000 +0200
+++ sys/modules/aqtion/aq_hw.c	2021-12-08 23:52:13.000000000 +0100
@@ -160,7 +160,7 @@
             unsigned int rnd = 0;
             unsigned int ucp_0x370 = 0;
 
-            rnd = random();
+            rnd = arc4random();
 
             ucp_0x370 = 0x02020202 | (0xFEFEFEFE & rnd);
             AQ_WRITE_REG(hw, AQ_HW_UCP_0X370_REG, ucp_0x370);
@@ -307,7 +307,6 @@
 
     /* Couldn't get MAC address from HW. Use auto-generated one. */
     if ((mac[0] & 1) || ((mac[0] | mac[1] | mac[2]) == 0)) {
-        u64 seed = get_cyclecount();
         u16 rnd;
         u32 h = 0;
         u32 l = 0;
@@ -315,7 +314,6 @@
         printf("atlantic: HW MAC address %x:%x:%x:%x:%x:%x is multicast or empty MAC", mac[0], mac[1], mac[2], mac[3], mac[4], mac[5]);
         printf("atlantic: Use random MAC address");
 
-        srandom(seed);
         rnd = random();
 
         /* chip revision */
