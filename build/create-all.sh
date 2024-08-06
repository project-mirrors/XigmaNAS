#!/usr/bin/env bash
set -x # print
# set -o pipefail # propogate errors in the pipeline
# set -e # exit on error

. /usr/local/xigmanas/svn/build/functions.inc
XIGMANAS_LOGDIR="$XIGMANAS_ROOTDIR/logs"
XIGMANAS_PORTS_DIR=/usr/local/xigmanas/svn/build/ports
export BATCH=yes

mkdir -p $XIGMANAS_LOGDIR

# ports to build
BUILT_PORTS=(arcconf ataidle autosnapshot devcpu-data-amd devcpu-data-intel fdisk firefly fuppes isboot lcdproc-devel locale mkpw phpvirtualbox rconf sas2ircu sas3ircu tw_cli)
INSTALLED_PACKAGES="bash bsnmp-ucd ca_root_nss cdialog clog dmidecode e2fsprogs-core fusefs-exfat exfat-utils fusefs-ext2 fusefs-ntfs grub2-bhyve gzip icu inadyn iperf3 ipmitool istgt lighttpd mDNSResponder mariadb114-server mariadb114-client minidlna msmtp nano netatalk3 nss_ldap nut-devel open-vm-tools openssh-portable opie pam_ldap pam_mkhomedir php83 php83-pecl-APCu phpMyAdmin-php83 proftpd python311 py311-wsdd rrdtool rsync samba419 dns/samba-nsupdate scponly sipcalc smartmontools spindown sudo syncthing tftp-hpa tmux transmission-cli transmission-web transmission-daemon transmission-utils unison virtualbox-ose-nox11 virtualbox-ose-additions-nox11 wait_on wol xmlstarlet zoneinfo php83-bcmath php83-bz2 php83-ctype php83-curl php83-dom php83-exif php83-filter php83-ftp php83-gd php83-gettext php83-gmp php83-iconv php83-imap php83-intl php83-ldap php83-mbstring php83-mysqli php83-opcache php83-pdo php83-pdo_mysql php83-pdo_sqlite php83-pear php83-pecl-APCu php83-pecl-mcrypt php83-session php83-simplexml php83-soap php83-sockets php83-sqlite3 php83-sysvmsg php83-sysvsem php83-sysvshm php83-tokenizer php83-xml php83-zip php83-zlib py311-markdown py311-importlib-metadata py311-zipp py311-rrdtool"

### functions ###

# install port dependencies via pkg
pkg_deps() {
    DEPS_FILE="$(dirname "$0")"/pkg-deps.txt
    # delete existing file
    echo -n > ${DEPS_FILE}

    for port in "${BUILT_PORTS[@]}"; do
        echo "installing build deps for $port.." 2>&1 | tee -a ${XIGMANAS_LOGDIR}/pkg-deps.log
        echo cd ${XIGMANAS_PORTS_DIR}/${port}  2>&1 | tee -a ${XIGMANAS_LOGDIR}/pkg-deps.log
        cd ${XIGMANAS_PORTS_DIR}/${port} && \
        make build-depends-list | sed 's=/usr/ports/==' | tee -a ${XIGMANAS_LOGDIR}/pkg-deps.log >> ${DEPS_FILE} 
    done

    # shellcheck disable=SC2046
    pkg install -fyr FreeBSD $( sort "$DEPS_FILE" | uniq ) 2>&1 | tee -a ${XIGMANAS_LOGDIR}/pkg-deps.log
}

# build ports that aren't installed by pkg
pkg_build() {
    echo "Building ports..." | tee ${XIGMANAS_LOGDIR}/pkg-build.log

    for port in "${BUILT_PORTS[@]}"; do
        echo "building $port" 2>&1 | tee -a ${XIGMANAS_LOGDIR}/pkg-build-"${port}".log
        echo cd ${XIGMANAS_PORTS_DIR}/"${port}"  2>&1 | tee -a ${XIGMANAS_LOGDIR}/pkg-build-"${port}".log
        cd ${XIGMANAS_PORTS_DIR}/"${port}" && \
        (make build do-install 2>&1 | tee -a ${XIGMANAS_LOGDIR}/pkg-build-"${port}".log) &
    done

    wait # wait for builds to finish
}

# install ports from pkg and copy to rootfs
pkg_copy() {
    echo "Installing files from INSTALLED_PACKAGES: $INSTALLED_PACKAGES" | tee ${XIGMANAS_LOGDIR}/pkg-copy-install.log
    # shellcheck disable=SC2086
    pkg install -yr FreeBSD $INSTALLED_PACKAGES 2>&1 | tee -a ${XIGMANAS_LOGDIR}/pkg-copy-install.log

    # upgrade to latest (starting with latest causes errors)
    pkg upgrade -yr latest | tee ${XIGMANAS_LOGDIR}/pkg-copy-upgrade.log

    make -f "$(dirname "$0")"/pkg-copy-Makefile 2>&1 | tee ${XIGMANAS_LOGDIR}/pkg-copy-make.log 
}

# upgrade system via pkg base (instead of freebsd-update)
# https://wiki.freebsd.org/PkgBase
sys_upgrade() {
    mkdir -p /usr/local/etc/pkg/repos
    # remove finch pkg file
    rm -f /usr/local/etc/pkg/repos/FreeBSD.conf

    # create/enable pkg base repo if doesn't exit/isn't enabled
    grep enabled /usr/local/etc/pkg/repos/base.conf || echo 'base: {
    url: "pkg+https://pkg.FreeBSD.org/${ABI}/base_release_1",
    mirror_type: "srv",
    signature_type: "fingerprints",
    fingerprints: "/usr/share/keys/pkg",
    enabled: yes
    }' >/usr/local/etc/pkg/repos/base.conf

    # create/enable latest pkg repo if doesn't exit/isn't enabled
    grep enabled /usr/local/etc/pkg/repos/latest.conf || echo 'latest: {
    url: "pkg+https://pkg.FreeBSD.org/${ABI}/latest",
    mirror_type: "srv",
    signature_type: "fingerprints",
    fingerprints: "/usr/share/keys/pkg",
    enabled: yes
    }' >/usr/local/etc/pkg/repos/latest.conf

    # update pkg to see packages from base and latest
    pkg update

    # find security patches. filter unnecessary and current patch-level packages
    pkg search -r base -g 'FreeBSD-*p?' | awk '!/-(lib32|dbg|dev|src|tests|mmccam|minimal)-/ {print $1}' | fgrep -v $(uname -r | awk -F- '{ print $1$3}') | xargs pkg install -y -r base 

    # restore conf files overwritten by pkg base upgrade
    cp -p /etc/shells.pkgsave /etc/shells
    cp -p /etc/master.passwd.pkgsave /etc/master.passwd
    pwd_mkdb -p /etc/master.passwd
    cp -p /etc/group.pkgsave /etc/group
    cp -p /etc/profile.pkgsave /etc/profile
    cp -p /etc/hosts.pkgsave /etc/hosts

    #cp -p /etc/rc.conf.pkgsave /etc/rc.conf	
    cp -p /etc/ssh/sshd_config.pkgsave /etc/sshd_config

    find / -name \*.pkgsave -print -delete

    # (linker.hints was recreated at kernel install and we had the old modules as .pkgsave so we need to recreate it, this will be done at the next reboot)
    rm /boot/kernel/linker.hints	
}

# setup/install prerequisites to build Xigmanas
prereq() {
    XIGMANAS_ROOTDIR="/usr/local/xigmanas"
    XIGMANAS_LOGDIR="$XIGMANAS_ROOTDIR/logs"
    mkdir -p "$XIGMANAS_LOGDIR"
    ln -s /usr/local/xigmanas /root/xigmanas

    # fetch latest ports
    (fetch https://download.freebsd.org/ftp/ports/ports/ports.tar.xz ; \
        tar xf ports.tar.xz -C /usr/) 2>&1 | tee ${XIGMANAS_LOGDIR}/prereq-ports.log & 

    pkg install -y bash subversion cdrtools pigz 


    # ===> Options unchanged                                                                         
    # /!\ WARNING /!\                                                                                

    # WITHOUT_X11 is unsupported, use WITHOUT=X11 on the command line, or one of
    # these in /etc/make.conf, OPTIONS_UNSET+=X11 to set it globally, or
    # emulators_virtualbox-ose_UNSET+=X11 for only this port.

    grep WITHOUT_X11=yes /etc/make.conf || echo WITHOUT_X11=yes >> /etc/make.conf
    grep WITHOUT=X11  /etc/make.conf || echo WITHOUT=X11  >> /etc/make.conf


    date; ls -l /boot/kernel/kernel
    sys_upgrade 2>&1 | tee ${XIGMANAS_LOGDIR}/prereq-sys_upgrade.log 

    # install kernel source
    pkg install -yr base FreeBSD-src-sys 2>&1 | tee ${XIGMANAS_LOGDIR}/prereq-sys_upgrade-src.log &


    cd /usr/local/xigmanas/
    svn co https://svn.code.sf.net/p/xigmanas/code/trunk svn 2>&1 | tee ${XIGMANAS_LOGDIR}/prereq-svn.log 
    #cd svn; svn up -r10142 # 14.1.0.5.10142 RC1

    mkdir -p /usr/ports/distfiles
    cp /usr/local/xigmanas/svn/build/ports/distfiles/*.{gz,zip} /usr/ports/distfiles/

    # wait for pkg
    wait
}

# create all images using the build process from make.sh
build() {
    svn help > /dev/null || pkg install -y subversion &

    XIGMANAS_LOGDIR="$XIGMANAS_ROOTDIR/logs"
    mkdir -p "$XIGMANAS_LOGDIR"
    mkdir -p /usr/local/xigmanas/work

    ### Create Filesystem Structure
    create_rootfs | tee "${XIGMANAS_LOGDIR}/build-create_rootfs.log"

    # install port dependencies via pkg
    pkg_deps 

    # build ports that aren't installed by pkg
    pkg_build &
    pkg_build_pid=$!

    ### Install Kernel + Modules
    mkdir -p /usr/local/xigmanas/work
    ### build/compress kernel 
    compress_kernel 2>&1 | tee "${XIGMANAS_LOGDIR}/build-compress_kernel.log"
    ### install kernel modules
    install_kernel_modules | tee "${XIGMANAS_LOGDIR}/build-install_kernel_modules.log"


    ### Install World
    build_world | tee "${XIGMANAS_LOGDIR}/build-build_world.log"&

    # install ports from pkg and copy to rootfs
    pkg_copy
    # pkg copy test
    if [ ! -f /usr/local/xigmanas/rootfs/usr/local/bin/xml ]; then
        echo 'Missing usr/local/bin/xml, error in pkg_copy' | tee -a ${XIGMANAS_LOGDIR}/pkg-copy-install.log
        return 1
    fi

    ### Build Bootloader
    opt="-f -m"; 
    # shellcheck disable=SC2086
    "$XIGMANAS_SVNDIR"/build/xigmanas-create-bootdir.sh $opt "$XIGMANAS_BOOTDIR" | tee "${XIGMANAS_LOGDIR}/build-create-bootdir.log"

    wait ${pkg_build_pid} # wait for pkg-build
    # build tests    
    if [ ! -f /usr/local/xigmanas/rootfs/usr/local/bin/fuppesd ]; then
        echo 'Missing usr/local/bin/fuppesd, error in pkg_build' | tee -a ${XIGMANAS_LOGDIR}/pkg-build.log
        return 1
    fi

    ### 8 add libs
    add_libs | tee "${XIGMANAS_LOGDIR}/build-add_libs.log"

    ### 8.1 reinstall subversion (may have been deleted during pkg-copy)
    svn || pkg install -y subversion &

    ### 8.2 delete static libs
    del_libs | tee "${XIGMANAS_LOGDIR}/build-del_libs.log"

    ### 8.5 sym-link duplicate libs. ie: librrd.so -> librrd.so.8.3.0 
    sym_libs | tee "${XIGMANAS_LOGDIR}/build-sym_libs.log"

    ### 9 Modify permissions
    "$XIGMANAS_SVNDIR"/build/xigmanas-modify-permissions.sh "$XIGMANAS_ROOTFS"  | tee "${XIGMANAS_LOGDIR}/build-modify-permissions.log"

    ### 11 create MBR usb
    create_usb | tee "${XIGMANAS_LOGDIR}/build-create_usb.log"
    create_usb_gpt | tee "${XIGMANAS_LOGDIR}/build-create_usb_gpt.log"
    create_iso | tee "${XIGMANAS_LOGDIR}/build-create_iso.log"
    create_full | tee "${XIGMANAS_LOGDIR}/build-create_full.log"
    create_embedded | tee "${XIGMANAS_LOGDIR}/build-create_embedded.log"

    echo "success"

}
### end functions ###

date; prereq;
date; build; date; 
