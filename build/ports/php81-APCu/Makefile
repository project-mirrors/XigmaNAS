PORTNAME=	php81-APCu
PORTREVISION=	0
CATEGORIES=	devel

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	APC User Caching

DISTFILES?=
NO_BUILD?=	         yes
NO_PKG_REGISTER?=	 yes
DEPENDS_TARGET=	clean build stage

DEP_STAGEDIR=	${PORTSDIR}/devel/pecl-APCu/work-php81/stage
BUILD_DEPENDS=	${NONEXISTENT}:devel/pecl-APCu

do-install:
# XigmaNAS® start install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	@${CP} -pv ${FILESDIR}/apcu.ini ${XIGMANAS_ROOTFS}${PREFIX}/etc/php

	@${MKDIR} ${XIGMANAS_ROOTFS}${PREFIX}/lib/php/extensions/no-debug-non-zts-20210902
	${INSTALL_DATA} ${DEP_STAGEDIR}/usr/local/lib/php/20210902/apcu.so ${XIGMANAS_ROOTFS}${PREFIX}/lib/php/extensions/no-debug-non-zts-20210902
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
