## $Id: Makefile.am,v 1.1.1.1.4.7 2003/01/27 09:59:26 tbaron Exp $

## This file is part of CDS Agenda. ().
## Copyright (C) 2002 CERN.
##
## The CDSware is free software; you can redistribute it and/or
## modify it under the terms of the GNU General Public License as
## published by the Free Software Foundation; either version 2 of the
## License, or (at your option) any later version.
##
## The CDSware is distributed in the hope that it will be useful, but
## WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
## General Public License for more details.  
##
## You should have received a copy of the GNU General Public License
## along with CDSware; if not, write to the Free Software Foundation, Inc.,
## 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

htdocsdir=$(HTDOCSDIR)

SUBDIRS=config guide images manager menus modification objects overview platform pocket stylesheets template tools

htdocs_SCRIPTS=*.php *.inc agenda.dtd

EXTRA_DIST=*.php *.inc agenda.dtd

install-data-local:
	if [ ! -e $(htdocsdir)/archive ]; then \
		mkdir -p $(htdocsdir)/archive; \
		chmod a+rwx $(htdocsdir)/archive; \
	fi 
	mkdir -p $(htdocsdir)/tmp
	chmod a+rwx $(htdocsdir)/tmp
	mkdir -p $(htdocsdir)/log
	chmod a+rwx $(htdocsdir)/log

CLEANFILES = *~ *.tmp

