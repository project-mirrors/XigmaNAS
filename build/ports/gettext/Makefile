PORTNAME=		xigmanas-gettext
PORTVERSION=	1.0
PORTREVISION=	0
CATEGORIES=	devel

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	GNU gettext runtime libraries and programs
WWW=		https://www.gnu.org/software/gettext/

DISTFILES?=
NO_BUILD?=	yes
NO_PKG_REGISTER?= yes
DEPENDS_TARGET=	clean build stage

DEP_STAGEDIR=	${PORTSDIR}/devel/gettext-runtime/work/stage
BUILD_DEPENDS=	${NONEXISTENT}:devel/gettext-runtime

do-install:
# XigmaNAS® install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
# bin
	@${CP} -Rpv ${DEP_STAGEDIR}${PREFIX}/bin ${XIGMANAS_ROOTFS}${PREFIX}
# lib
	@${RM} -rf  ${XIGMANAS_ROOTFS}${PREFIX}/lib
	@${CP} -Rpv ${DEP_STAGEDIR}${PREFIX}/lib ${XIGMANAS_ROOTFS}${PREFIX}/lib
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
