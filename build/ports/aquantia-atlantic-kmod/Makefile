PORTNAME=	aquantia-atlantic-kmod
PORTVERSION=	0.0.5
CATEGORIES=	net

COMMENT=	Aquantia AQtion (Atlantic) Network Driver
MAINTAINER=	zoon01@xigmanas.org

USES=		kmod uidfix
USE_GITHUB=	yes

DEPENDS_TARGET=	 clean build

GH_ACCOUNT=	Aquantia
GH_PROJECT=	aqtion-freebsd
GH_TAGNAME=	4816644

# Strip -Werror from sys/conf/kmod.mk
MAKE_ENV=	WERROR=""

do-install:
# XigmaNAS install
	@echo "install for ${XIGMANAS_ROOTFS}"
	@${INSTALL_KLD} -v ${WRKSRC}/if_atlantic.ko ${XIGMANAS_ROOTFS}/boot/kernel

.include <bsd.port.mk>
