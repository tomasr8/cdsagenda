# Makefile.in generated automatically by automake 1.4-p4 from Makefile.am

# Copyright (C) 1994, 1995-8, 1999 Free Software Foundation, Inc.
# This Makefile.in is free software; the Free Software Foundation
# gives unlimited permission to copy and/or distribute it,
# with or without modifications, as long as this notice is preserved.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY, to the extent permitted by law; without
# even the implied warranty of MERCHANTABILITY or FITNESS FOR A
# PARTICULAR PURPOSE.


SHELL = /bin/bash

srcdir = .
top_srcdir = ../..

prefix = /usr/local
exec_prefix = ${prefix}

bindir = ${exec_prefix}/bin
sbindir = ${exec_prefix}/sbin
libexecdir = ${exec_prefix}/libexec
datadir = ${prefix}/share
sysconfdir = ${prefix}/etc
sharedstatedir = ${prefix}/com
localstatedir = ${prefix}/var
libdir = ${exec_prefix}/lib
infodir = ${prefix}/info
mandir = ${prefix}/man
includedir = ${prefix}/include
oldincludedir = /usr/include

DESTDIR =

pkgdatadir = $(datadir)/cdsagenda
pkglibdir = $(libdir)/cdsagenda
pkgincludedir = $(includedir)/cdsagenda

top_builddir = ../..

ACLOCAL = /home/troun/Downloads/cdsagenda-4.2.9/missing aclocal
AUTOCONF = /home/troun/Downloads/cdsagenda-4.2.9/missing autoconf
AUTOMAKE = /home/troun/Downloads/cdsagenda-4.2.9/missing automake
AUTOHEADER = /home/troun/Downloads/cdsagenda-4.2.9/missing autoheader

INSTALL = /usr/bin/install -c
INSTALL_PROGRAM = ${INSTALL} $(AM_INSTALL_PROGRAM_FLAGS)
INSTALL_DATA = ${INSTALL} -m 644
INSTALL_SCRIPT = ${INSTALL}
transform = s,x,x,

NORMAL_INSTALL = :
PRE_INSTALL = :
POST_INSTALL = :
NORMAL_UNINSTALL = :
PRE_UNINSTALL = :
POST_UNINSTALL = :
ACROREAD = 
CGIBINDIR = ~/Downloads/cdsagenda/www/cgi-bin/cdsagenda
CGIBINURL = http://localhost:5555/cgi-bin/cdsagenda
DBHOST = localhost
DBHOSTPORT = 3306
DBNAME = cdsagenda
DBPASS = pass
DBUSER = root
GS = /usr/bin/gs
GUNZIP = /usr/bin/gunzip
GZIP = /usr/bin/gzip
HTDOCSDIR = ~/Downloads/cdsagenda/www/data/cdsagenda
HTDOCSURL = http://localhost:5555/cdsagenda
HTML2PS = 
HTML2TEXT = 
HTMLDOC = 
JAVA = /usr/bin/java
LYNX = 
MAKEINFO = /home/troun/Downloads/cdsagenda-4.2.9/missing makeinfo
MYSQL = /usr/bin/mysql
PACKAGE = cdsagenda
PHP = /usr/bin/php
PS2ASCII = /usr/bin/ps2ascii
PS2PDF = /usr/bin/ps2pdf
UTILDIR = ~/Downloads/cdsagenda
VERSION = 4.2.9

htdocsdir = $(HTDOCSDIR)/config

htdocs_SCRIPTS = *.php *.inc

EXTRA_DIST = *.inc

CLEANFILES = *~ config.php
mkinstalldirs = $(SHELL) $(top_srcdir)/mkinstalldirs
CONFIG_CLEAN_FILES =  config.php
SCRIPTS =  $(htdocs_SCRIPTS)

DIST_COMMON =  Makefile.am Makefile.in config.php.in


DISTFILES = $(DIST_COMMON) $(SOURCES) $(HEADERS) $(TEXINFOS) $(EXTRA_DIST)

TAR = tar
GZIP_ENV = --best
all: all-redirect
.SUFFIXES:
$(srcdir)/Makefile.in: Makefile.am $(top_srcdir)/configure.ac $(ACLOCAL_M4) 
	cd $(top_srcdir) && $(AUTOMAKE) --gnu --include-deps htdocs/config/Makefile

Makefile: $(srcdir)/Makefile.in  $(top_builddir)/config.status
	cd $(top_builddir) \
	  && CONFIG_FILES=$(subdir)/$@ CONFIG_HEADERS= $(SHELL) ./config.status

config.php: $(top_builddir)/config.status config.php.in
	cd $(top_builddir) && CONFIG_FILES=$(subdir)/$@ CONFIG_HEADERS= $(SHELL) ./config.status

install-htdocsSCRIPTS: $(htdocs_SCRIPTS)
	@$(NORMAL_INSTALL)
	$(mkinstalldirs) $(DESTDIR)$(htdocsdir)
	@list='$(htdocs_SCRIPTS)'; for p in $$list; do \
	  if test -f $$p; then \
	    echo " $(INSTALL_SCRIPT) $$p $(DESTDIR)$(htdocsdir)/`echo $$p|sed '$(transform)'`"; \
	    $(INSTALL_SCRIPT) $$p $(DESTDIR)$(htdocsdir)/`echo $$p|sed '$(transform)'`; \
	  else if test -f $(srcdir)/$$p; then \
	    echo " $(INSTALL_SCRIPT) $(srcdir)/$$p $(DESTDIR)$(htdocsdir)/`echo $$p|sed '$(transform)'`"; \
	    $(INSTALL_SCRIPT) $(srcdir)/$$p $(DESTDIR)$(htdocsdir)/`echo $$p|sed '$(transform)'`; \
	  else :; fi; fi; \
	done

uninstall-htdocsSCRIPTS:
	@$(NORMAL_UNINSTALL)
	list='$(htdocs_SCRIPTS)'; for p in $$list; do \
	  rm -f $(DESTDIR)$(htdocsdir)/`echo $$p|sed '$(transform)'`; \
	done
tags: TAGS
TAGS:


distdir = $(top_builddir)/$(PACKAGE)-$(VERSION)/$(subdir)

subdir = htdocs/config

distdir: $(DISTFILES)
	@for file in $(DISTFILES); do \
	  d=$(srcdir); \
	  if test -d $$d/$$file; then \
	    cp -pr $$d/$$file $(distdir)/$$file; \
	  else \
	    test -f $(distdir)/$$file \
	    || ln $$d/$$file $(distdir)/$$file 2> /dev/null \
	    || cp -p $$d/$$file $(distdir)/$$file || :; \
	  fi; \
	done
info-am:
info: info-am
dvi-am:
dvi: dvi-am
check-am: all-am
check: check-am
installcheck-am:
installcheck: installcheck-am
install-exec-am:
install-exec: install-exec-am

install-data-am: install-htdocsSCRIPTS
install-data: install-data-am

install-am: all-am
	@$(MAKE) $(AM_MAKEFLAGS) install-exec-am install-data-am
install: install-am
uninstall-am: uninstall-htdocsSCRIPTS
uninstall: uninstall-am
all-am: Makefile $(SCRIPTS)
all-redirect: all-am
install-strip:
	$(MAKE) $(AM_MAKEFLAGS) AM_INSTALL_PROGRAM_FLAGS=-s install
installdirs:
	$(mkinstalldirs)  $(DESTDIR)$(htdocsdir)


mostlyclean-generic:

clean-generic:
	-test -z "$(CLEANFILES)" || rm -f $(CLEANFILES)

distclean-generic:
	-rm -f Makefile $(CONFIG_CLEAN_FILES)
	-rm -f config.cache config.log stamp-h stamp-h[0-9]*

maintainer-clean-generic:
mostlyclean-am:  mostlyclean-generic

mostlyclean: mostlyclean-am

clean-am:  clean-generic mostlyclean-am

clean: clean-am

distclean-am:  distclean-generic clean-am

distclean: distclean-am

maintainer-clean-am:  maintainer-clean-generic distclean-am
	@echo "This command is intended for maintainers to use;"
	@echo "it deletes files that may require special tools to rebuild."

maintainer-clean: maintainer-clean-am

.PHONY: uninstall-htdocsSCRIPTS install-htdocsSCRIPTS tags distdir \
info-am info dvi-am dvi check check-am installcheck-am installcheck \
install-exec-am install-exec install-data-am install-data install-am \
install uninstall-am uninstall all-redirect all-am all installdirs \
mostlyclean-generic distclean-generic clean-generic \
maintainer-clean-generic clean mostlyclean distclean maintainer-clean


# Tell versions [3.59,3.63) of GNU make to not export all variables.
# Otherwise a system limit (for SysV at least) may be exceeded.
.NOEXPORT:
