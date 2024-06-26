#!/usr/bin/env bash
# create an archive from github repository of transmission 
# - include the missing submodules for 4.0.6 release
# - include REVISION file with full hash

PKG_NAME=transmission
PKG_VERS=4.0.6
PKG_GIT_HASH=38c1649
PKG_DIST_SITE=https://github.com/transmission/transmission.git

localFolder=${PKG_NAME}.part
localFile=${PKG_NAME}-${PKG_VERS}.1.tar.gz
archiveFolder=${PKG_NAME}-${PKG_VERS}
REVISION=${archiveFolder}/${archiveFolder}/REVISION

create_source_archive ()
{
   echo "Clear folders"
   rm -rf ${localFolder}
   rm -rf ${archiveFolder}
   
   echo "clone ${PKG_DIST_SITE} into ${localFolder}"
   git clone --quiet ${PKG_DIST_SITE} ${localFolder}
   
   echo "checkout ${PKG_GIT_HASH}"
   git -C ${localFolder} checkout --quiet ${PKG_GIT_HASH}
   
   echo "update submodules"
   git -C ${localFolder} --work-tree=. submodule update --quiet --init --recursive

   echo "copy repository files"
   mkdir -p ${archiveFolder}/${archiveFolder}
   cd ${localFolder}
   git ls-files --recurse-submodules | tar -cf- -T- | tar -xf - -C ../${archiveFolder}/${archiveFolder}
   cd ..
   
   git -C ${localFolder} rev-parse HEAD > ${REVISION}
   echo "add REVISON file with hash: $(cat ${REVISION})"
   
   echo "create archive ${localFile} with folder ${archiveFolder}"
   tar -caf ${localFile} -C ${archiveFolder} ${archiveFolder}
}


create_source_archive

exit 0
