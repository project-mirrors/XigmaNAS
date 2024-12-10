PORTNAME=		arcconf
DISTVERSION=	B26842
PORTREVISION=	0
CATEGORIES=	sysutils
MASTER_SITES=	https://download.adaptec.com/raid/storage_manager/
DISTNAME=	arcconf_${DISTVERSION}

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Adaptec SCSI/SAS RAID administration tool
WWW=		https://storage.microsemi.com/en-us/support/

# XigmaNAS® contains license files
NO_LICENSES_INSTALL= yes
NO_LICENSES_DIALOGS= yes
LICENSE=	Microsemi
LICENSE_NAME=	Microsemi License
LICENSE_FILE=	${WRKSRC}/freebsd${DIST_TYPE}/cmdline/License.txt
LICENSE_PERMS=	no-pkg-sell no-dist-sell no-dist-mirror auto-accept

ONLY_FOR_ARCHS=	amd64
USES=		zip
LIB_DEPENDS+=    libstdc++.so.6:lang/gcc${GCC_DEFAULT}
USE_GCC=        yes

.include <bsd.port.pre.mk>

DIST_TYPE=	14_x86_64
NO_BUILD=	yes
NO_WRKSUBDIR=	yes

do-install:
# XigmaNAS® Install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	${INSTALL_PROGRAM} -v ${WRKSRC}/freebsd${DIST_TYPE}/cmdline/arcconf ${XIGMANAS_ROOTFS}${PREFIX}/sbin
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.post.mk>
