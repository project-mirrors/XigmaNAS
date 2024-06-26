PORTNAME=	netatalk
PORTVERSION=	3.2.0
PORTREVISION=	0
PORTEPOCH=	1
CATEGORIES=	net
MASTER_SITES=	https://github.com/${PORTNAME}/${PORTNAME}/releases/download/netatalk-${PORTVERSION:S/./-/g}/
PKGNAMESUFFIX=	3

MAINTAINER=	zoon01@xigmanas.com
COMMENT=	File server for Mac OS X
WWW=		https://netatalk.io/

# XigmaNAS® contains license files
NO_LICENSES_DIALOGS= yes
LICENSE=	GPLv2

LIB_DEPENDS=	libgcrypt.so:security/libgcrypt \
		libevent.so:devel/libevent

USES=		bdb:18 gettext iconv meson perl5 pkgconfig shebangfix ssl tar:xz
USE_LDCONFIG=	yes
USE_RC_SUBR=	netatalk
#INSTALL_TARGET=install-strip
CPE_VENDOR=	netatalk_project

MESON_ARGS=	-Dwith-libgcrypt-dir=${LOCALBASE} \
			-Dwith-bdb=${LOCALBASE} \
			-Dwith-libiconv=${LOCALBASE} \
			-Dwith-ssl-dir=${OPENSSLBASE} \
			-Dwith-dtrace=false \
			-Ddisable-init-hooks=true \
			-Dpkg_config_path=${LOCALBASE}/libdata/pkgconfig \
			-Dwith-shadow=disabled \
			-Denable-pgp-uam=enabled \
			--localstatedir=/var/etc \
			--infodir=${PREFIX}/share/info

OPTIONS_DEFINE= PAM KERBEROS5 LDAP SENDFILE KERBEROS DBUS MYSQL ACL LIBWRAP SPOTLIGHT
OPTIONS_DEFAULT= ACL MDNSRESPONDER PAM
OPTIONS_RADIO=ZEROCONF
OPTIONS_RADIO_ZEROCONF= AVAHI MDNSRESPONDER
ZEROCONF_DESC=		Zeroconf(Bonjour) support
MYSQL_DESC=		Enable MySQL CNID backend support
SENDFILE_DESC=		Enable Sendfile support
ACL_DESC=		Enable ACL support
SPOTLIGHT_DESC=		Enable Spotlight search support
OPTIONS_SUB=	yes
KERBEROS5_MESON_ON=	-Denable-krbV-uam=enabled \
			-Dwith-kerberos=enabled
KERBEROS5_MESON_OFF=	-Denable-krbV-uam=disabled \
			-Dwith-kerberos=disabled
KERBEROS_LIB_DEPENDS=libgpg-error.so:security/libgpg-error
PAM_MESON_ON=		-Dwith-pam=${LOCALBASE}
PAM_MESON_OFF=		-Dwithout-pam=true
AVAHI_CFLAGS=		-I${LOCALBASE}/include -L${LOCALBASE}/lib
AVAHI_LIB_DEPENDS=		libavahi-client.so:net/avahi-app
AVAHI_SUB_LIST=		ZEROCONF="avahi_daemon"
MDNSRESPONDER_CFLAGS=		-I${LOCALBASE}/include -L${LOCALBASE}/lib
MDNSRESPONDER_LIB_DEPENDS=		libdns_sd.so:net/mDNSResponder
MDNSRESPONDER_SUB_LIST=	ZEROCONF="mdnsd"
LDAP_MESON_ON=	-Dwith-ldap=${LOCALBASE}
LDAP_CFLAGS=		-I${LOCALBASE}/include -L${LOCALBASE}/lib
LDAP_USES=	ldap
SENDFILE_MESON_OFF=-Ddisable-sendfile=true
DBUS_USES=	gnome python
DBUS_USE=	GNOME=glib20
DBUS_LIB_DEPENDS=	libdbus-glib-1.so:devel/dbus-glib \
			libdbus-1.so:devel/dbus
DBUS_RUN_DEPENDS=	${PYTHON_SITELIBDIR}/dbus/_dbus.py:devel/py-dbus@${PY_FLAVOR}
DBUS_MESON_ON=		-Dwith-dbus-daemon=${LOCALBASE}/bin/dbus-daemon \
			-Dwith-dbus-sysconf-dir=${LOCALBASE}/etc/dbus-1/system.d
DBUS_MESON_OFF=		-Dwith-afpstats=disabled
MYSQL_USES=	mysql
MYSQL_MESON_OFF=-Dwith-mysql-config=/nonexistent
ACL_LIB_DEPENDS=	libsunacl.so:sysutils/libsunacl
ACL_MESON_ENABLED=with-acls
LIBWRAP_MESON_OFF=-Ddisable-tcp-wrappers=true
SPOTLIGHT_LIB_DEPENDS=	libtracker-sparql-3.0.so:sysutils/tracker3
SPOTLIGHT_MESON_OFF=	-Dwith-spotlight=disabled

SHEBANG_GLOB=	afpstats

CONFLICTS=	jday yudit # bin/dbd man/man1/uniconv.1.gz

.include <bsd.port.pre.mk>

.if ${PORT_OPTIONS:MAVAHI}=="" && ${PORT_OPTIONS:MMDNSRESPONDER}==""
SUB_LIST+=	ZEROCONF=""
MESON_ARGS+=-Denable-zeroconf=disabled
.endif

post-patch:
	@${REINPLACE_CMD} -e 's|%%DB_NAME%%|${BDB_INCLUDE_DIR:T}|' \
		${WRKSRC}/meson.build

post-install:
	${INSTALL_SCRIPT} ${BUILD_WRKSRC}/contrib/macusers/macusers \
		${STAGEDIR}${PREFIX}/bin/macusers

# XigmaNAS® install
	@echo "Start install to ${XIGMANAS_ROOTFS}"
	@${INSTALL_SCRIPT} -v ${FILESDIR}/netatalk.in ${XIGMANAS_ROOTFS}/etc/rc.d/netatalk
# bin
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/bin/dbd ${XIGMANAS_ROOTFS}${PREFIX}/bin
# sbin
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/sbin/afpd ${XIGMANAS_ROOTFS}${PREFIX}/sbin
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/sbin/cnid_dbd ${XIGMANAS_ROOTFS}${PREFIX}/sbin
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/sbin/cnid_metad ${XIGMANAS_ROOTFS}${PREFIX}/sbin
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/sbin/netatalk ${XIGMANAS_ROOTFS}${PREFIX}/sbin
# lib
	@${INSTALL_LIB} -v ${STAGEDIR}${PREFIX}/lib/libatalk.so.* ${XIGMANAS_ROOTFS}${PREFIX}/lib
	@${LN} -f -v -s libatalk.so.18 ${XIGMANAS_ROOTFS}${PREFIX}/lib/libatalk.so
# etc
	@${INSTALL_DATA} -v ${STAGEDIR}${PREFIX}/etc/extmap.conf.sample ${XIGMANAS_ROOTFS}${PREFIX}/etc/extmap.conf
# lib
	@${MKDIR} -v ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_dhx_pam.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_dhx_passwd.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_dhx2_pam.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_dhx2_passwd.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_gss.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_guest.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_pam.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_passwd.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_pgp.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@${INSTALL_PROGRAM} -v ${STAGEDIR}${PREFIX}/libexec/netatalk-uams/uams_randnum.so ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams
	@cd ${XIGMANAS_ROOTFS}${PREFIX}/libexec/netatalk-uams && ${LN} -fsv uams_passwd.so uams_clrtxt.so && ${LN} -fsv uams_dhx_passwd.so uams_dhx.so && ${LN} -fsv uams_dhx2_passwd.so uams_dhx2.so
# XigmaNAS® end install
	@echo "End install to ${XIGMANAS_ROOTFS}"

.include <bsd.port.mk>
