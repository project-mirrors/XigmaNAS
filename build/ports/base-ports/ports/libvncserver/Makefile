PORTNAME=	libvncserver
PORTVERSION=	0.9.14
DISTVERSIONPREFIX=	LibVNCServer-
PORTREVISION=	1
CATEGORIES=	net devel

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Provide an easy API to a custom vnc server
WWW: http://libvnc.github.io/

LICENSE=	GPLv2

LIB_DEPENDS=	libpng.so:graphics/png \
		liblzo2.so:archivers/lzo2

USES=		cmake cpe jpeg pathfix pkgconfig
USE_LDCONFIG=	yes

CMAKE_ON=	WITH_24BPP WITH_JPEG WITH_LZO WITH_PNG \
		WITH_THREADS WITH_TIGHTVNC_FILETRANSFER \
		WITH_WEBSOCKETS WITH_ZLIB
CMAKE_OFF=	WITH_FFMPEG WITH_SDL WITH_SYSTEMD

USE_GITHUB=	yes
GH_ACCOUNT=	LibVNC

OPTIONS_DEFINE=		GCRYPT IPV6 SASL SSL
SSL_USE_OFF=		CMAKE_OFF=WITH_GNUTLS WITH_OPENSSL
OPTIONS_SINGLE=		SSL
OPTIONS_SINGLE_SSL=	GNUTLS OPENSSL
OPTIONS_DEFAULT=	GCRYPT SSL
GCRYPT_DESC=		libgcrypt support
SASL_DESC=		SASL support

GCRYPT_CMAKE_BOOL=	WITH_GCRYPT
GCRYPT_LIB_DEPENDS=	libgcrypt.so:security/libgcrypt \
			libgpg-error.so:security/libgpg-error

IPV6_CMAKE_BOOL=	WITH_IPv6

SASL_CMAKE_BOOL=	WITH_SASL
SASL_LIB_DEPENDS=	libsasl2.so:security/cyrus-sasl2

GNUTLS_IMPLIES=		GCRYPT
GNUTLS_LIB_DEPENDS=	libgnutls.so:security/gnutls
GNUTLS_CMAKE_BOOL=	WITH_GNUTLS

OPENSSL_CMAKE_BOOL=	WITH_OPENSSL
OPENSSL_USES=		ssl

post-install:
	${INSTALL_DATA} ${WRKSRC}/rfb/default8x16.h ${STAGEDIR}${PREFIX}/include/rfb

.include <bsd.port.mk>
