--- js/utils.js.orig	2021-02-01 15:01:59.008683000 +0100
+++ js/utils.js	2021-02-01 16:01:28.000000000 +0100
@@ -1111,7 +1111,7 @@
  * @param {Date} expire - when cookie should expire
  */
 function vboxSetCookie(k,v,expire) {
-	var exp = (v ? (expire ? expire : new Date(2020,12,24)) : new Date().setDate(new Date().getDate() - 1));
+	var exp = (v ? (expire ? expire : new Date(Date.now() + 7 * 1000 * 60 * 60 * 24)) : new Date().setDate(new Date().getDate() - 1));
 	document.cookie = k+"="+v+"; expires="+exp.toGMTString()+"; path=/";
 	$('#vboxPane').data('vboxCookies')[k] = v;
 }
