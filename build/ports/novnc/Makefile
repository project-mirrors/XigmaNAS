PORTNAME=       noVNC
PORTVERSION=    20181101
PORTREVISION=   0
CATEGORIES=     net

MAINTAINER=     zoon01@xigmanas.com
COMMENT=        HTML5 VNC Client

LICENSE=	MPL20

USE_GITHUB=	yes
GH_ACCOUNT=	novnc
GH_TAGNAME=	cffb42e

NO_BUILD=		yes
NO_PKG_REGISTER=	yes

WWWDIR=${PREFIX}/www/novnc

do-install:
# XigmaNAS install
	@echo "install for ${XIGMANAS_ROOTFS}"
	@${MKDIR} -p ${XIGMANAS_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${WRKSRC}/app ${XIGMANAS_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${WRKSRC}/core ${XIGMANAS_ROOTFS}${WWWDIR}
	@${CP} -Rpv ${WRKSRC}/vendor ${XIGMANAS_ROOTFS}${WWWDIR}
	@${CP} -pv ${WRKSRC}/vnc.html ${XIGMANAS_ROOTFS}${WWWDIR}
	@${CP} -pv ${WRKSRC}/vnc_lite.html ${XIGMANAS_ROOTFS}${WWWDIR}

.include <bsd.port.mk>
