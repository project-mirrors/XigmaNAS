PORTNAME=		wsdd
DISTVERSIONPREFIX= v
DISTVERSION=	0.7.0
PORTREVISION=	0
CATEGORIES=	net python
PKGNAMEPREFIX=	${PYTHON_PKGNAMEPREFIX}

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Web Service Discovery (WSD) host daemon for Samba

# XigmaNAS® contains license files
NO_LICENSES_INSTALL=	yes
NO_LICENSES_DIALOGS=	yes
LICENSE=	MIT
#LICENSE_FILE=	${WRKSRC}/LICENSE

USES=		python:3.5+,run shebangfix
USE_GITHUB=	yes
GH_ACCOUNT=	christgau
GH_TAGNAME=	c9cffc5e
SHEBANG_FILES=	src/${PORTNAME}.py
NO_ARCH=	yes
NO_BUILD=	yes

USERS=		_wsdd
GROUPS=		_wsdd

post-patch:
	${REINPLACE_CMD} 's|PYTHON|${PYTHON_CMD}|' ${WRKSRC}/etc/rc.d/${PORTNAME}

do-install:
# XigmaNAS® Start Install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
# rc/link
	@${INSTALL_SCRIPT} -v ${FILESDIR}/wsdd.in ${XIGMANAS_ROOTFS}/etc/rc.d/wsdd
#bin
	@${INSTALL_SCRIPT} -v ${WRKSRC}/src/${PORTNAME}.py ${XIGMANAS_ROOTFS}/usr/local/bin/${PORTNAME}
# XigmaNAS® End install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
