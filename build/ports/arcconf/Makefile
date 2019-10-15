PORTNAME=	arcconf
DISTVERSION=	v3_02_23600
PORTREVISION=	0
CATEGORIES=	sysutils
COMMENT=	Adaptec SCSI/SAS RAID administration tool
MAINTAINER=	zoon01@xigmanas.com

MASTER_SITES=	http://download.adaptec.com/raid/storage_manager/

DISTNAME=	arcconf_${DISTVERSION}
USES=		zip

ONLY_FOR_ARCHS=	amd64
RESTRICTED=	May not be redistributed in binary form
NO_CDROM=	May not be redistributed in binary form

.include <bsd.port.pre.mk>

DIST_TYPE=	11_x64

NO_BUILD=	yes
NO_WRKSUBDIR=	yes


do-install:
	${INSTALL_PROGRAM} -v ${WRKSRC}/freebsd${DIST_TYPE}/cmdline/arcconf ${XIGMANAS_ROOTFS}/${PREFIX}/sbin

.include <bsd.port.post.mk>