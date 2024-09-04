<?
#$Id: config.php.in,v 1.1.1.1.4.3 2002/10/23 08:56:25 tbaron Exp $

## This file is part of CDS Agenda..
## Copyright (C) 2002 CERN.
##
## The CDS Agenda Software is free software; you can redistribute it and/or
## modify it under the terms of the GNU General Public License as
## published by the Free Software Foundation; either version 2 of the
## License, or (at your option) any later version.
##
## The CDS Agenda Software is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
## General Public License for more details.  
##
## You should have received a copy of the GNU General Public License
## along with CDS Agenda Software; if not, write to the Free Software
## Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

###############################################################
# AUTOCONF GENERATED CONFIG FILE (*do not edit*)
###############################################################

// base installation directory for the tool
$AGE = "~/Downloads/cdsagenda/www/data/cdsagenda";

// base URL for the tool
$AGE_WWW = "http://localhost:5555/cdsagenda";

// cgi directory
$CGIBIN = "~/Downloads/cdsagenda/www/cgi-bin/cdsagenda";

// cgi-bin url
$CGIBIN_WWW = "http://localhost:5555/cgi-bin/cdsagenda";

// util scripts
$UTIL = "~/Downloads/cdsagenda";

// version number
$VERSION = "4.2.9";

// acrobat reader
$ACROREAD = "";

// gzip
$GZIP = "/usr/bin/gzip";

// gunzip
$GUNZIP = "/usr/bin/gunzip";

// PS2PDF
$PS2PDF = "/usr/bin/ps2pdf";

// PS2ASCII
$PS2ASCII = "/usr/bin/ps2ascii";

// HTML2TEXT
$HTML2TEXT = "";

// lynx
$LYNX = "";

// htmldoc
$HTMLDOC = "";

// html2ps
$HTML2PS = "";

// database settings
$dbName = "cdsagenda";

// server running the mysql db
$mysql_machine = "localhost";

// port with mysql service
$mysql_port = "3306";

// user for mysql requests on the agendas db
$mysql_userid = "root";

// db password
$mysql_password = "pass";

###############################################################
# ENGINE CONFIG (*do not edit*)
###############################################################
include( "$AGE/config/config_engine.inc" );

###############################################################
# RUNTIME CONFIGURATION (editable)
###############################################################
include( "$AGE/config/config_runtime.inc" );

?>
