--- ext/intl/breakiterator/codepointiterator_internal.h.orig	2021-10-19 17:18:19.000000000 +0200
+++ ext/intl/breakiterator/codepointiterator_internal.h	2021-11-07 04:45:01.000000000 +0100
@@ -39,7 +39,11 @@
 
 		virtual ~CodePointBreakIterator();
 
+#if U_ICU_VERSION_MAJOR_NUM >= 70
+		virtual bool operator==(const BreakIterator& that) const;
+#else
 		virtual UBool operator==(const BreakIterator& that) const;
+#endif
 
 		virtual CodePointBreakIterator* clone(void) const;
 
