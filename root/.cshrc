#
# Part of XigmaNAS® (https://www.xigmanas.com).
# Copyright © 2018-2025 XigmaNAS® <info@xigmanas.com>.
# All rights reserved.
#
# .cshrc - csh resource script, read at beginning of execution by each shell
#

alias h		history 25
alias j		jobs -l
alias la	ls -a
alias lf	ls -FA
alias ll	ls -lAF
alias ls	ls -G

# A righteous umask
umask 22

set path = (/sbin /bin /usr/sbin /usr/bin /usr/local/sbin /usr/local/bin $HOME/bin)

setenv LANG en_US.UTF-8
setenv PAGER more
setenv BLOCKSIZE K
setenv EDITOR nano

if ($?prompt) then
	# An interactive shell -- set some stuff up
	set prompt="%{\033[1;32m%}%m: %{\033[1;32m%}%.%{\033[0m%}%# "
	set promptchars = "%#"
	set filec
	set autolist
	set history = 100
	set savehist = 100
	if ( $?tcsh ) then
		bindkey "^W" backward-delete-word
		bindkey -k up     history-search-backward
		bindkey -k down   history-search-forward
		bindkey "\e[1~"   beginning-of-line       # Home
		bindkey "\e[2~"   overwrite-mode          # Insert
		bindkey "\e[3~"   delete-char             # Delete
		bindkey "\e[4~"   end-of-line             # End
		bindkey "\e[5~"   history-search-backward # Page Up
		bindkey "\e[6~"   history-search-forward  # Page Down
		bindkey "\eOc"    forward-word            # ctrl right
		bindkey "\e[1;5C" forward-word            # ctrl right
		bindkey "\eOd"    backward-word           # ctrl left
		bindkey "\e[1;5D" backward-word           # ctrl left
	endif
endif

# Display console menu (only on ttyv0/ttyd0).
if ("ttyu0" == "$tty" && `kenv console | sed -n 's/.*uboot.*/uboot/p'` == "uboot") then
	stty clocal
endif
if ( "ttyv0" == "$tty" || "ttyu0" == "$tty" || "xc0" == "$tty" ) then
	/etc/rc.banner
	/etc/rc.initial
endif
