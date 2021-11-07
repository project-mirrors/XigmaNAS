--- ext/intl/locale/locale_methods.c.orig	2021-10-19 17:18:19.000000000 +0200
+++ ext/intl/locale/locale_methods.c	2021-11-07 04:46:44.000000000 +0100
@@ -1326,7 +1326,7 @@
 		if( token && (token==cur_lang_tag) ){
 			/* check if the char. after match is SEPARATOR */
 			chrcheck = token + (strlen(cur_loc_range));
-			if( isIDSeparator(*chrcheck) || isEndOfTag(*chrcheck) ){
+			if( isIDSeparator(*chrcheck) || isKeywordSeparator(*chrcheck) || isEndOfTag(*chrcheck) ){
 				efree( cur_lang_tag );
 				efree( cur_loc_range );
 				if( can_lang_tag){
