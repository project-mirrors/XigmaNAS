--- lib/readline/display.c.orig	2018-10-01 03:37:48.000000000 +0200
+++ lib/readline/display.c	2019-01-12 22:12:24.000000000 +0100
@@ -837,7 +837,10 @@
      the line breaks in the prompt string in expand_prompt, taking invisible
      characters into account, and if lpos exceeds the screen width, we copy
      the data in the loop below. */
+  if (local_prompt)
   lpos = prompt_physical_chars + modmark;
+  else
+    lpos = 0;
 
 #if defined (HANDLE_MULTIBYTE)
   memset (line_state_invisible->wrapped_line, 0, line_state_invisible->wbsize * sizeof (int));
