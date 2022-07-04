PORTNAME=	dmidecode
PORTVERSION=	3.3
PORTREVISION?=	0
CATEGORIES=	sysutils
MASTER_SITES=	SAVANNAH

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	A tool for dumping DMI (SMBIOS) contents in human-readable format

EXTRACT_SUFX= .tar.xz

.include <bsd.port.pre.mk>

do-install:
# XigmaNAS® Install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${WRKSRC}/dmidecode ${XIGMANAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/biosdecode ${XIGMANAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/ownership ${XIGMANAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/vpddecode ${XIGMANAS_ROOTFS}/usr/local/sbin
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.post.mk>
