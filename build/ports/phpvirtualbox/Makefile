PORTNAME=		phpvirtualbox
DISTVERSION=	6.1
PORTREVISION=	0
CATEGORIES=	www

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	Web Interface for VirtualBox

# XigmaNAS® contains license files
#LICENSE=	GPLv3

USES=		dos2unix php
# XigmaNAS
#USE_PHP=	json session soap simplexml xml

NO_ARCH=	yes
NO_BUILD=	yes

USE_GITHUB=	yes
GH_TAGNAME=	cf82d58f2

DOS2UNIX_REGEX=	.*\.(php|txt|js|css|html)
ETCDIR=		${WWWDIR}

post-patch:
	@${REINPLACE_CMD} -e 's#/usr/share/phpvirtualbox#${WWWDIR}#g' \
		${WRKSRC}/phpvirtualbox.conf
do-install:
	${MKDIR} ${STAGEDIR}${WWWDIR}
	(cd ${WRKSRC}/ && ${COPYTREE_SHARE} "*" ${STAGEDIR}${WWWDIR} \
	    "-not -name *.orig -not -name config.php-example")
	${INSTALL_DATA} ${WRKSRC}/config.php-example \
	    ${STAGEDIR}${WWWDIR}/config.php.sample

# XigmaNAS® install
	@echo "Start install for ${XIGMANAS_ROOTFS}"
	${RM} -f ${STAGEDIR}${WWWDIR}/*.txt
	${RM} -f ${STAGEDIR}${WWWDIR}/*.bak
	${CP} -Rpv ${STAGEDIR}${WWWDIR} ${XIGMANAS_ROOTFS}${PREFIX}/www
# XigmaNAS® end install
	@echo "End install for ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
