--- sys/modules/aqtion/aq_main.c.orig	2019-09-24 16:45:34.000000000 +0200
+++ sys/modules/aqtion/aq_main.c	2021-12-09 01:14:28.000000000 +0100
@@ -117,10 +117,12 @@
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC107, "Aquantia AQtion 10Gbit Network Adapter"),
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC108, "Aquantia AQtion 5Gbit Network Adapter"),
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC109, "Aquantia AQtion 2.5Gbit Network Adapter"),
+	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC100, "Aquantia AQtion 10Gbit Network Adapter"),
 
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC107S, "Aquantia AQtion 10Gbit Network Adapter"),
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC108S, "Aquantia AQtion 5Gbit Network Adapter"),
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC109S, "Aquantia AQtion 2.5Gbit Network Adapter"),
+	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC100S, "Aquantia AQtion 10Gbit Network Adapter"),
 
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC111, "Aquantia AQtion 5Gbit Network Adapter"),
 	PVID(AQUANTIA_VENDOR_ID, AQ_DEVICE_ID_AQC112, "Aquantia AQtion 2.5Gbit Network Adapter"),
@@ -282,8 +284,6 @@
 	.isc_ntxd_default = {PAGE_SIZE / sizeof(aq_txc_desc_t) * 4},
 };
 
-if_shared_ctx_t aq_sctx = &aq_sctx_init;
-
 /*
  * TUNEABLE PARAMETERS:
  */
@@ -300,7 +300,7 @@
  */
 static void *aq_register(device_t dev)
 {
-	return (aq_sctx);
+	return (&aq_sctx_init);
 }
 
 static int aq_if_attach_pre(if_ctx_t ctx)
@@ -735,6 +735,23 @@
 	}
 }
 
+#if __FreeBSD_version >= 1300054
+static u_int aq_mc_filter_apply(void *arg, struct sockaddr_dl *dl, int count)
+{
+	struct aq_dev *softc = arg;
+	struct aq_hw *hw = &softc->hw;
+	u8 *mac_addr = NULL;
+
+	if (count == AQ_HW_MAC_MAX)
+		return (0);
+
+	mac_addr = LLADDR(dl);
+	aq_hw_mac_addr_set(hw, mac_addr, count + 1);
+
+	aq_log_detail("set %d mc address %6D", count + 1, mac_addr, ":");
+	return (1);
+}
+#else
 static int aq_mc_filter_apply(void *arg, struct ifmultiaddr *ifma, int count)
 {
 	struct aq_dev *softc = arg;
@@ -752,6 +769,7 @@
 	aq_log_detail("set %d mc address %6D", count + 1, mac_addr, ":");
 	return (1);
 }
+#endif
 
 static bool aq_is_mc_promisc_required(struct aq_dev *softc)
 {
@@ -764,15 +782,22 @@
 	struct ifnet  *ifp = iflib_get_ifp(ctx);
 	struct aq_hw  *hw = &softc->hw;
 	AQ_DBG_ENTER();
-
+#if __FreeBSD_version >= 1300054
+	softc->mcnt = if_llmaddr_count(iflib_get_ifp(ctx));
+#else
 	softc->mcnt = if_multiaddr_count(iflib_get_ifp(ctx), AQ_HW_MAC_MAX);
+#endif
 	if (softc->mcnt >= AQ_HW_MAC_MAX)
 	{
 		aq_hw_set_promisc(hw, !!(ifp->if_flags & IFF_PROMISC),
 				  aq_is_vlan_promisc_required(softc),
 				  !!(ifp->if_flags & IFF_ALLMULTI) || aq_is_mc_promisc_required(softc));
 	}else{
+#if __FreeBSD_version >= 1300054
+		if_foreach_llmaddr(iflib_get_ifp(ctx), aq_mc_filter_apply, softc);
+#else
 		if_multi_apply(iflib_get_ifp(ctx), aq_mc_filter_apply, softc);
+#endif
 	}
 	AQ_DBG_EXIT(0);
 }
