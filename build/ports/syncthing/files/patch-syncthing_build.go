--- syncthing/build.go.orig	2019-11-04 13:33:24.000000000 +0100
+++ syncthing/build.go	2019-11-06 02:29:25.000000000 +0100
@@ -443,7 +443,7 @@ func build(target target, tags []string) {
 	}
 
 	for _, pkg := range target.buildPkgs {
-		args := []string{"build", "-v"}
+		args := []string{"build"}
 		args = appendParameters(args, tags, pkg)
 
 		runPrint(goCmd, args...)
