--- sys/kern/vfs_acl.c.orig	2023-07-29 14:57:15.415484000 +0200
+++ sys/kern/vfs_acl.c	2023-08-05 20:48:15.000000000 +0200
@@ -434,7 +434,7 @@
 	int error;
 
 	AUDIT_ARG_FD(uap->filedes);
-	error = getvnode(td, uap->filedes,
+	error = getvnode_path(td, uap->filedes,
 	    cap_rights_init_one(&rights, CAP_ACL_GET), &fp);
 	if (error == 0) {
 		error = vacl_get_acl(td, fp->f_vnode, uap->type, uap->aclp);
@@ -567,7 +567,7 @@
 	int error;
 
 	AUDIT_ARG_FD(uap->filedes);
-	error = getvnode(td, uap->filedes,
+	error = getvnode_path(td, uap->filedes,
 	    cap_rights_init_one(&rights, CAP_ACL_CHECK), &fp);
 	if (error == 0) {
 		error = vacl_aclcheck(td, fp->f_vnode, uap->type, uap->aclp);
