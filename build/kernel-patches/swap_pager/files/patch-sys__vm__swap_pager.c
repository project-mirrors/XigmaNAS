--- /usr/src/sys/vm/swap_pager.c.orig	2020-06-30 21:25:26.465587000 +0200
+++ /usr/src/sys/vm/swap_pager.c	2020-06-30 23:11:43.000000000 +0200
@@ -860,7 +860,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* Happy with small size for XigmaNAS*/
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
