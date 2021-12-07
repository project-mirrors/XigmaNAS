--- sys/vm/swap_pager.c.orig	2021-12-07 01:01:45.874553000 +0100
+++ sys/vm/swap_pager.c	2021-12-07 01:31:09.000000000 +0100
@@ -904,7 +904,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* We are happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
