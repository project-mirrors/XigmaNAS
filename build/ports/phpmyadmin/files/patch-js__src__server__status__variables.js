--- js/src/server/status/variables.js.orig	2022-05-11 06:39:14.000000000 +0200
+++ js/src/server/status/variables.js	2023-01-30 12:48:03.000000000 +0100
@@ -43,7 +43,7 @@
 
     $('#filterText').on('keyup', function () {
         var word = $(this).val().replace(/_/g, ' ');
-        if (word.length === 0) {
+        if (word.length === 0 || word.length >= 32768) {
             textFilter = null;
         } else {
             try {
