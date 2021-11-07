--- ext/intl/breakiterator/codepointiterator_internal.cpp.orig	2021-10-19 17:18:19.000000000 +0200
+++ ext/intl/breakiterator/codepointiterator_internal.cpp	2021-11-07 04:42:07.000000000 +0100
@@ -75,7 +75,11 @@
 	clearCurrentCharIter();
 }
 
+#if U_ICU_VERSION_MAJOR_NUM >= 70
+bool CodePointBreakIterator::operator==(const BreakIterator& that) const
+#else
 UBool CodePointBreakIterator::operator==(const BreakIterator& that) const
+#endif
 {
 	if (typeid(*this) != typeid(that)) {
 		return FALSE;
