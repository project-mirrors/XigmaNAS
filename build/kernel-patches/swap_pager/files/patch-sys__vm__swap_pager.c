--- sys/vm/swap_pager.c.orig	2019-11-05 23:02:47.065992000 +0100
+++ sys/vm/swap_pager.c	2019-11-05 23:09:49.000000000 +0100
@@ -896,7 +896,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* Happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
