PORTNAME=		gzip
PORTVERSION=	1.12
PORTREVISION?=  0
CATEGORIES=	archivers
COMMENT=	A compression utility designed to be a replacement for compress
MAINTAINER=	zoon01@xigmanas.com

MASTER_SITES=				${MASTER_SITE_GNU}
MASTER_SITE_SUBDIR=	${PORTNAME}

GNU_CONFIGURE=	yes

post-configure:
	@${REINPLACE_CMD} -e 's|^SHELL =.*|SHELL=/bin/sh|' ${WRKSRC}/Makefile

do-install:
# XigmaNAS® start install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${WRKSRC}/gzip ${XIGMANAS_ROOTFS}/usr/bin
	@${INSTALL_SCRIPT} -v ${WRKSRC}/gunzip ${XIGMANAS_ROOTFS}/usr/bin
	@${INSTALL_SCRIPT} -v ${WRKSRC}/zcat ${XIGMANAS_ROOTFS}/usr/bin
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>