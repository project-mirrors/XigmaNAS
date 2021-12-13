PORTNAME=		isboot
PORTVERSION=	0.3.2
PORTREVISION=   0
CATEGORIES=		net
COMMENT=		iSCSI boot driver by Daisuke Aoyama (aoyama@peach.ne.jp)

MAINTAINER=		aoyama@peach.ne.jp
#MASTER_SITES=		http://www.peach.ne.jp/archives/isboot/

WRKSRC=                 ${WRKDIR}/${DISTNAME}/src
USES=			kmod
ONLY_FOR_ARCHS=		i386 amd64

.include <bsd.port.pre.mk>
.if ${ARCH} == "amd64"
CFLAGS+=	-DVIMAGE
.endif

do-install:
	@${INSTALL_KLD} -v ${WRKSRC}/isboot.ko ${XIGMANAS_ROOTFS}/boot/kernel

.include <bsd.port.post.mk>
