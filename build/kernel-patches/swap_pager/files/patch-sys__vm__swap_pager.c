--- sys/vm/swap_pager.c.orig	2020-12-14 01:04:08.424197000 +0100
+++ sys/vm/swap_pager.c	2020-12-14 01:10:18.000000000 +0100
@@ -904,7 +904,7 @@
 	VM_OBJECT_WLOCK(object);
 	while (size) {
 		if (n == 0) {
-			n = BLIST_MAX_ALLOC;
+			n = min(BLIST_MAX_ALLOC, size); /* We are happy with small size */
 			while ((blk = swp_pager_getswapspace(n)) == SWAPBLK_NONE) {
 				n >>= 1;
 				if (n == 0) {
