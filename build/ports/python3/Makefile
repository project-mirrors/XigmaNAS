PORTNAME=		python3
PORTVERSION=		${PYTHON_MAJOR_VER}
PORTREVISION=		0
CATEGORIES=		lang python

COMMENT=		Interpreted object-oriented programming language
MAINTAINER=		zoon01@xigmanas.com

USES=			python:run

DISTFILES=
NO_BUILD=		yes
NO_PKG_REGISTER=	yes
#ALWAYS_BUILD_DEPENDS=	yes
DEPENDS_TARGET=	clean build stage

BUILD_DEPENDS=		${NONEXISTENT}:lang/python37
DEP_STAGEDIR=		${PORTSDIR}/lang/python37/work/stage

# To test python, run samba-tool in XigmaNAS

do-install:
# XigmaNAS® install
	@echo "Start install for ${XIGMANAS_ROOTFS}"
# bin
.for file in python3.7
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/bin/${file} ${XIGMANAS_ROOTFS}/usr/local/bin
.endfor
# lib
.for file in python3.7
	find ${DEP_STAGEDIR}${PREFIX}/lib/libpython3.7m.so.1.0-gdb.py -delete
.endfor
	@${INSTALL_PROGRAM} -v ${DEP_STAGEDIR}${PREFIX}/lib/libpython3.7m.so.* ${XIGMANAS_ROOTFS}/usr/local/lib
	${CP} -Rpv ${DEP_STAGEDIR}${PREFIX}/lib/python3.7 ${XIGMANAS_ROOTFS}/usr/local/lib
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
