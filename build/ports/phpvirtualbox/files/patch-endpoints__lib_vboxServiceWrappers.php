--- endpoints/lib/vboxServiceWrappers.php.orig	2022-06-16 01:45:37.264417000 +0200
+++ endpoints/lib/vboxServiceWrappers.php	2022-06-20 21:28:38.000000000 +0200
@@ -108,7 +108,7 @@
     }
 
     /** ArrayAccess Functions **/
-    public function offsetSet($offset, $value)
+    public function offsetSet($offset, $value): void
     {
         if ($value instanceof $this->_interfaceName)
         {
@@ -127,49 +127,50 @@
         }
     }
 
-    public function offsetExists($offset)
+    public function offsetExists($offset): bool
     {
         return isset($this->_objects[$offset]);
     }
 
-    public function offsetUnset($offset)
+    public function offsetUnset($offset): void
     {
         unset($this->_objects[$offset]);
     }
 
-    public function offsetGet($offset)
+    public function offsetGet($offset): mixed
     {
         return isset($this->_objects[$offset]) ? $this->_objects[$offset] : null;
     }
 
     /** Iterator Functions **/
-    public function rewind()
+    public function rewind(): void
     {
         reset($this->_objects);
     }
 
-    public function current()
+    public function current(): mixed
     {
         return current($this->_objects);
     }
 
-    public function key()
+    public function key(): mixed
     {
         return key($this->_objects);
     }
 
+#[\ReturnTypeWillChange]
     public function next()
     {
         return next($this->_objects);
     }
 
-    public function valid()
+    public function valid(): bool
     {
         return ($this->current() !== false);
     }
 
     /** Countable Functions **/
-    public function count()
+    public function count(): int
     {
         return count($this->_objects);
     }
