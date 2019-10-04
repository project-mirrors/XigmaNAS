--- ./getifaddr.c.orig	2018-12-20 04:06:26.000000000 +0100
+++ ./getifaddr.c	2019-10-04 03:43:29.000000000 +0200
@@ -87,7 +87,7 @@
 		if (ifname && strcmp(p->ifa_name, ifname) != 0)
 			continue;
 		addr_in = (struct sockaddr_in *)p->ifa_addr;
-		if (!ifname && (p->ifa_flags & (IFF_LOOPBACK | IFF_SLAVE)))
+		if (!ifname && (p->ifa_flags & IFF_LOOPBACK))
 			continue;
 		memcpy(&lan_addr[n_lan_addr].addr, &addr_in->sin_addr, sizeof(lan_addr[n_lan_addr].addr));
 		if (!inet_ntop(AF_INET, &addr_in->sin_addr, lan_addr[n_lan_addr].str, sizeof(lan_addr[0].str)) )
