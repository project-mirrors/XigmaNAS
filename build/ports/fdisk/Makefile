PORTNAME=		fdisk
PORTVERSION=	1.1
CATEGORIES=		sysutils
COMMENT=		PC slice table maintenance utility
MAINTAINER=		zoon01@xigmanas.01
DISTFILES=

NO_PKG_REGISTER=yes
do-extract:
	@${RM} -rf ${WRKDIR}
	@${MKDIR} ${WRKDIR}
	@${TAR} -xf ${FILESDIR}/${DISTNAME}.tar.gz -C ${WRKDIR}

do-build:
	@${MAKE} -C ${WRKSRC}

do-install:
# XigmaNAS® Install
	@echo "Install to ${XIGMANAS_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${WRKSRC}/fdisk ${XIGMANAS_ROOTFS}/sbin
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>