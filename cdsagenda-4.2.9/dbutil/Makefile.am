## $Id: Makefile.am,v 1.1.1.1.4.4 2003/03/28 10:40:33 tbaron Exp $

## This file is part of the CERN Document Server Software (CDSware).
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

dbutildir = $(UTILDIR)

dbutil_SCRIPTS=dbexec
noinst_SCRIPTS=dbtest tabfill.sql

EXTRA_DIST = tabcreate.sql tabdrop.sql synchronizefiles.php

install-data-local:
	chmod a+x ./dbexec
	chmod a+x ./dbtest
	./dbtest

CLEANFILES = $(dbutil_SCRIPTS) $(noinst_SCRIPTS) *~ *.tmp tabfill.sql tabdrop.sql tabcreate.sql
