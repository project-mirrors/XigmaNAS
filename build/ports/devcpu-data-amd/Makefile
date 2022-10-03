PORTNAME=	data
PORTVERSION=	20221002
PORTREVISION=	0
CATEGORIES=	sysutils
MASTER_SITES=	https://git.kernel.org/pub/scm/linux/kernel/git/firmware/linux-firmware.git/plain/amd-ucode/
PKGNAMEPREFIX=	devcpu-
PKGNAMESUFFIX=	-amd
DISTFILES=	microcode_amd.bin?id=${_REV} \
		microcode_amd_fam15h.bin?id=${_REV} \
		microcode_amd_fam16h.bin?id=${_REV} \
		microcode_amd_fam17h.bin?id=${_REV} \
		microcode_amd_fam19h.bin?id=${_REV}
DIST_SUBDIR=	${PKGNAME:C/_.*$//}

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	AMD CPUs microcode updates

ONLY_FOR_ARCHS=	amd64

EXTRACT_CMD=	${CP}
EXTRACT_BEFORE_ARGS=	# empty
EXTRACT_AFTER_ARGS=	.
NO_ARCH=	yes
NO_BUILD=	yes

DATADIR=	${PREFIX}/share/cpucontrol
NO_WRKSUBDIR=	yes
PLIST_FILES=	${DISTFILES:S/^/${DATADIR}\//g:C/\?.*//}

_REV=		fdf1a65258522edf18a0a1768fbafa61ed07e598

do-install:
# default install stagedir
	${MKDIR} ${STAGEDIR}${DATADIR}/
.for f in ${DISTFILES}
	${INSTALL_DATA} ${WRKSRC}/${f} ${STAGEDIR}${DATADIR}/${f:C/\?.*//}
.endfor

# XigmaNAS® Install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	${INSTALL_SCRIPT} -v ${FILESDIR}/microcode_update.in ${XIGMANAS_ROOTFS}/etc/rc.d/microcode_update
	${MKDIR} ${XIGMANAS_ROOTFS}${DATADIR}/
.for f in ${DISTFILES}
	${INSTALL_DATA} ${WRKSRC}/${f} ${XIGMANAS_ROOTFS}${DATADIR}/${f:C/\?.*//}
.endfor
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
