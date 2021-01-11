PORTNAME=	php80-APCu
PORTREVISION=	0
CATEGORIES=	devel

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	APC User Caching

DISTFILES?=
NO_BUILD?=	         yes
NO_PKG_REGISTER?=	 yes
DEPENDS_TARGET=	clean build stage

DEP_STAGEDIR=	${PORTSDIR}/devel/pecl-APCu/work-php80/stage
BUILD_DEPENDS=	${NONEXISTENT}:devel/pecl-APCu

do-install:
# XigmaNAS® install
	@echo "Install to ${XIGMANAS_ROOTFS}"
	@${CP} -pv ${FILESDIR}/apcu.ini ${XIGMANAS_ROOTFS}${PREFIX}/etc/php

	@${MKDIR} ${XIGMANAS_ROOTFS}${PREFIX}/lib/php/extensions/no-debug-non-zts-20200930
	${INSTALL_DATA} ${DEP_STAGEDIR}/usr/local/lib/php/20200930/apcu.so ${XIGMANAS_ROOTFS}${PREFIX}/lib/php/extensions/no-debug-non-zts-20200930

.include <bsd.port.mk>
