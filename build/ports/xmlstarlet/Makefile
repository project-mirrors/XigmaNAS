PORTNAME=		xmlstarlet
PORTVERSION=	1.6.1
PORTREVISION=	1
CATEGORIES=	textproc

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Command line XML toolkit
WWW=		https://xmlstar.sourceforge.net/

MASTER_SITES=		SF/xmlstar/${PORTNAME}/${PORTVERSION}

USE_GNOME=	libxslt
USE_ICONV=	yes
GNU_CONFIGURE=	yes
NO_PKG_REGISTER=yes

CONFIGURE_ARGS=	--with-libxml-prefix=${LOCALBASE} \
			--with-libxslt-prefix=${LOCALBASE} \
			--with-libiconv-prefix=${LOCALBASE}

post-patch:
	@${REINPLACE_CMD} -e "s|%%PTHREAD%%|${PTHREAD_LIBS}|g" \
		${WRKSRC}/configure

do-install:
# XigmaNAS® install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	@${INSTALL_PROGRAM} -v ${WRKSRC}/xml ${XIGMANAS_ROOTFS}/${PREFIX}/bin
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>