PORTNAME=		isboot
PORTVERSION=	0.2.14
PORTREVISION=   0
CATEGORIES=		net
MASTER_SITES=		https://sourceforge.net/p/xigmanas/code/HEAD/tree/trunk/build/ports/distfiles/

MAINTAINER=		zoon01@xigmanas.com
COMMENT=		iSCSI boot driver

WRKSRC=                 ${WRKDIR}/${DISTNAME}/src
USES=			kmod
ONLY_FOR_ARCHS=		i386 amd64

.include <bsd.port.pre.mk>
.if ${ARCH} == "amd64"
CFLAGS+=	-DVIMAGE
.endif

do-install:
# XigmaNAS® Install
	@echo "Install to ${XIGMANAS_ROOTFS}"
	@${INSTALL_KLD} -v ${WRKSRC}/isboot.ko ${XIGMANAS_ROOTFS}/boot/kernel
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.post.mk>
