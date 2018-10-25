--- if_vlan.c.orig	2018-10-21 22:29:51.537478000 +0200
+++ if_vlan.c	2018-10-25 23:37:46.000000000 +0200
@@ -1318,8 +1318,13 @@
 
 	ifv = (struct ifvlan *)arg;
 	ifp = ifv->ifv_ifp;
+
+	CURVNET_SET(ifp->if_vnet);
+
 	/* The ifv_ifp already has the lladdr copied in. */
 	if_setlladdr(ifp, IF_LLADDR(ifp), ifp->if_addrlen);
+
+	CURVNET_RESTORE();
 }
 
 static int
