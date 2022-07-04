PORTNAME=		xigmanas-virtualbox-ose-additions
PORTVERSION=	1.0
#PORTREVISION=	1
CATEGORIES=		emulators
MAINTAINER=		zoon01@xigmanas.com
COMMENT=		VirtualBox additions for FreeBSD guests

DISTFILES=
NO_BUILD=		yes
NO_PKG_REGISTER=	yes
ALWAYS_BUILD_DEPENDS=	yes
DEPENDS_TARGET=	clean build

BUILD_DEPENDS=	${NONEXISTENT}:emulators/virtualbox-ose-additions:build

BLDSRC=${PORTSDIR}/emulators/virtualbox-ose-additions/work/VirtualBox-*/
WRKSRC=${WRKDIR}/virtualbox-ose-additions

.include <../xigmanas.mk>
.if ${XIGMANAS_ARCH} == amd64
ARCH_DIR=	freebsd.amd64
.else
ARCH_DIR=	freebsd.x86
.endif

do-install:
# XigmaNAS® install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	@${INSTALL_KLD} -v ${BLDSRC}/out/${ARCH_DIR}/release/bin/additions/src/vboxguest.ko ${XIGMANAS_ROOTFS}/boot/modules
	@${INSTALL_PROGRAM} -v ${BLDSRC}/out/${ARCH_DIR}/release/bin/additions/VBoxControl ${XIGMANAS_ROOTFS}${PREFIX}/sbin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/out/${ARCH_DIR}/release/bin/additions/VBoxService ${XIGMANAS_ROOTFS}${PREFIX}/sbin
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
