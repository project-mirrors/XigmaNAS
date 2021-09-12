PORTNAME=		iperf
PORTVERSION=    3.10.1
PORTREVISION=   0
CATEGORIES=	benchmarks
COMMENT=	Improved tool to measure TCP and UDP bandwidth
MAINTAINER=	zoon01@xigmanas.com

USE_GITHUB=	yes
GH_ACCOUNT=     esnet 
GH_TAGNAME=     169b0d9

USES=		libtool ssl
GNU_CONFIGURE=	yes
USE_LDCONFIG=	yes

.include <bsd.port.options.mk>

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/iperf3 ${XIGMANAS_ROOTFS}${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/libiperf.so.0 ${XIGMANAS_ROOTFS}${PREFIX}/lib

.include <bsd.port.mk>
