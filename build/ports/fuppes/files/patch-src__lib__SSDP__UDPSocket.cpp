--- src/lib/SSDP/UDPSocket.cpp.orig	2011-08-15 10:25:54.000000000 +0200
+++ src/lib/SSDP/UDPSocket.cpp	2021-04-18 22:07:58.000000000 +0200
@@ -132,7 +132,7 @@
 	memset(&(m_LocalEndpoint.sin_zero), '\0', 8); // fill the rest of the structure with zero
 	
 	/* Bind socket */
-	ret = bind(m_Socket, (struct sockaddr*)&m_LocalEndpoint, sizeof(m_LocalEndpoint)); 
+	ret = (m_Socket, (struct sockaddr*)&m_LocalEndpoint, sizeof(m_LocalEndpoint)); 
   if(ret == -1) {
     throw fuppes::Exception(__FILE__, __LINE__, "failed to bind udp socket %s", p_sIPAddress.c_str());
   }
