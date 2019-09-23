--- sys/vm/swap_pager.c.orig	2019-09-23 17:46:32.489287000 +0200
+++ sys/vm/swap_pager.c	2019-09-23 18:22:22.000000000 +0200
@@ -860,7 +860,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* Happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
