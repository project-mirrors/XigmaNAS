PORTNAME=		syncthing
PORTVERSION=	1.19.0
PORTREVISION=	0
DISTVERSIONPREFIX= v
CATEGORIES=	net
MASTER_SITES=	https://github.com/syncthing/syncthing/releases/download/v${PORTVERSION}/
DISTNAME=	${PORTNAME}-source-${DISTVERSIONPREFIX}${PORTVERSION}

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Encrypted file sync tool

BUILD_DEPENDS=	${LOCALBASE}/bin/go:lang/go

BINS=		syncthing stdiscosrv strelaysrv strelaypoolsrv

.for x in ${BINS}
# XigmaNAS
PLIST_FILES=	bin/syncthing
#PLIST_FILES+=   bin/${x} 
.endfor 

PORTDOCS=	*

USERS=		syncthing
GROUPS=		syncthing

# Anonymize
MAKE_ENV+=	BUILD_USER=ports BUILD_HOST=XigmaNAS

STRIP=		# stripping can break go binaries

NO_WRKSUBDIR=   yes
OPTIONS_DEFINE=	DOCS
# XigmaNAS
NO_PKG_REGISTER= yes

.include <bsd.port.pre.mk>

do-build:
	@( cd ${WRKSRC}/${PORTNAME} ; \
		${SETENV} ${MAKE_ENV} CGO_ENABLED=0 GOFLAGS="-mod=vendor" go run build.go -version v${PORTVERSION} -no-upgrade build ; \
		${SETENV} ${MAKE_ENV} CGO_ENABLED=0 GOFLAGS="-mod=vendor" go run build.go -version v${PORTVERSION} -no-upgrade build stdiscosrv ; \
		${SETENV} ${MAKE_ENV} CGO_ENABLED=0 GOFLAGS="-mod=vendor" go run build.go -version v${PORTVERSION} -no-upgrade build strelaysrv ; \
		${SETENV} ${MAKE_ENV} CGO_ENABLED=0 GOFLAGS="-mod=vendor" go run build.go -version v${PORTVERSION} -no-upgrade build strelaypoolsrv ; \
		)

do-install:
.for x in ${BINS}
	${INSTALL_PROGRAM} ${WRKSRC}/${PORTNAME}/${x} \
		${STAGEDIR}${PREFIX}/bin/
.endfor
	${MKDIR} ${STAGEDIR}${DOCSDIR}
.for x in AUTHORS LICENSE README.md
	${INSTALL_DATA} ${WRKSRC}/${PORTNAME}/${x} \
		${STAGEDIR}${DOCSDIR}
.endfor

# XigmaNAS® install
	@echo "Start install for ${XIGMANAS_ROOTFS}"
# rc
	@${INSTALL_SCRIPT} -v ${FILESDIR}/syncthing.in ${XIGMANAS_ROOTFS}/etc/rc.d/syncthing
# bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/${PORTNAME}/syncthing \
		${XIGMANAS_ROOTFS}${PREFIX}/bin/
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.post.mk>
