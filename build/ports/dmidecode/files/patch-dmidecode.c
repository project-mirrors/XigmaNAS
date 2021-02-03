--- dmidecode.c.orig	2020-10-14 14:51:11.000000000 +0200
+++ dmidecode.c	2021-02-03 15:15:31.000000000 +0100
@@ -116,7 +116,7 @@
 	size_t i;
 
 	for (i = 0; i < len; i++)
-		if (bp[i] < 32 || bp[i] == 127)
+		if (bp[i] < 32 || bp[i] >= 127)
 			bp[i] = '.';
 }
 
@@ -248,9 +248,9 @@
 			{
 				int j, l = strlen(s) + 1;
 
-				off = 0;
 				for (row = 0; row < ((l - 1) >> 4) + 1; row++)
 				{
+					off = 0;
 					for (j = 0; j < 16 && j < l - (row << 4); j++)
 						off += sprintf(raw_data + off,
 						       j ? " %02X" : "%02X",
