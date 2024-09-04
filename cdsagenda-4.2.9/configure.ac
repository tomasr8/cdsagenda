## $Id: configure.ac,v 1.5.2.14 2003/03/28 10:40:32 tbaron Exp $
## Purpose: CDS Agenda Software () main configure.ac file.
## Note: If you change this file, please run "autoreconf" to regenerate the "configure" script.

## This file is part of CDS Agenda. ().
## Copyright (C) 2002 CERN.
##
## The  is free software; you can redistribute it and/or
## modify it under the terms of the GNU General Public License as
## published by the Free Software Foundation; either version 2 of the
## License, or (at your option) any later version.
##
## The  is distributed in the hope that it will be useful, but
## WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
## General Public License for more details.  
##
## You should have received a copy of the GNU General Public License
## along with CDS Agenda; if not, write to the Free Software Foundation, Inc.,
## 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

dnl Initialize autoconf and automake:
AC_INIT(cdsagenda, 4.2.9, cds.support@cern.ch)
AM_INIT_AUTOMAKE(cdsagenda, 4.2.9)

dnl Check for install:
AC_PROG_INSTALL

dnl Check for MySQL client:
AC_PATH_PROG(MYSQL, mysql)
if test -z "$MYSQL"; then
   AC_MSG_ERROR([
   MySQL command-line client was not found in your PATH.  Please install it first.
   Available from <http://mysql.com/>.])
fi

dnl Check for PHP:
AC_PATH_PROG(PHP, php)
if test -z "$PHP"; then
   AC_MSG_ERROR([
   PHP standalone command-line executable (i.e. not compiled as Apache module!) 
   was not found in your PATH.  Please install it first.
   Available from <http://www.php.net/manual/en/install.commandline.php>.])
fi

dnl Check for Java:
AC_PATH_PROG(JAVA, java)
if test -z "$JAVA"; then
   AC_MSG_ERROR([
   java was not found in your PATH.  Please install it first.
   Available from <http://java.sun.com/>.])
fi

dnl Check for GhostScript:
AC_PATH_PROG(GS, gs)
if test -z "$GS"; then
   AC_MSG_WARN([
   GhostScript was not found in your path.  It is used in 
   the software for automatic conversion of transfered documents.  
   You can continue without it but you will miss some  
   functionality.  We recommend you to install it first and to rerun 
   the configure script.  GhostScript is available from
   <http://www.cs.wisc.edu/~ghost/doc/AFPL/get704.htm>.])
fi

dnl Check for Acrobat Reader:
AC_PATH_PROG(ACROREAD, acroread)
if test -z "$ACROREAD"; then
   AC_MSG_WARN([
   Acrobat Reader was not found in your path.  It is used in 
   the software for automatic conversion of transfered documents.  
   You can continue without it but you will miss some  
   functionality.  We recommend you to install it first and to rerun 
   the configure script.  Acrobat Reader is available from
   <http://www.adobe.com/products/acrobat/readstep.html>.])
fi

dnl Check for Gzip:
AC_PATH_PROG(GZIP, gzip)
if test -z "$GZIP"; then
   AC_MSG_WARN([
   Gzip was not found in your path.  It is used in 
   the software to compress the files attached to an agenda.
   You can continue without it but you will miss some  
   functionality.  We recommend you to install it first and to rerun 
   the configure script.  Gzip is available from
   <http://www.gzip.org/>.])
fi

dnl Check for Gunzip:
AC_PATH_PROG(GUNZIP, gunzip)
if test -z "$GUNZIP"; then
   AC_MSG_WARN([
   Gunzip was not found in your path.  It is used in 
   the software to correctly deal with transfered compressed
   files.
   You can continue without it but you will miss some 
   functionality.  We recommend you to install it first and to rerun 
   the configure script.  Gunzip is available from
   <http://www.gzip.org/>.])
fi

dnl Check for Ps2pdf:
AC_PATH_PROG(PS2PDF, ps2pdf)
if test -z "$PS2PDF"; then
   AC_MSG_WARN([
   Ps2pdf was not found in your path.  It is used in 
   the software to convert submitted PostScripts into PDF.
   You can continue without it but you will miss some 
   functionality.  We recommend you to install it first and to rerun 
   the configure script.  Ps2pdf is available from
   <http://www.cs.wisc.edu/~ghost/doc/AFPL/6.50/Ps2pdf.htm>.])
fi

dnl Check for ps2ascii:
AC_PATH_PROG(PS2ASCII, ps2ascii)
if test -z "$PS2ASCII"; then
   AC_MSG_WARN([
   Ps2ascii was not found in your path.  It is used in 
   the software to convert submitted PostScripts and PDF to text.
   You can continue without it but you will miss some 
   functionality.  We recommend you to install it first and to rerun 
   the configure script.])
fi

dnl Check for lynx:
AC_PATH_PROG(LYNX, lynx)
if test -z "$LYNX"; then
   AC_MSG_WARN([
   lynx was not found in your path.  It is used in 
   the software to output the agendas in PostScript
   or PDF. If lynx or htmldoc are not installed in your
   path, then this feature will not work.
   <http://lynx.browser.org/>.])
fi

dnl Check for html2ps:
AC_PATH_PROG(HTML2PS, html2ps)
if test -z "$HTML2PS"; then
   AC_MSG_WARN([
   html2ps was not found in your path.  It is used in 
   the software to output the agendas in PostScript
   or PDF. If it is not installed and htmldoc
   is not installed either in your
   path, then this feature will not work.
   <http://www.tdb.uu.se/~jan/html2ps.html>.])
fi

dnl Check for html2text:
AC_PATH_PROG(HTML2TEXT, html2text)
if test -z "$HTML2TEXT"; then
   AC_MSG_WARN([
   html2text was not found in your path. It is used in 
   the software to insert the html minutes files inline in
   the agendas themselves.
   If it is not installed then this feature will not work.
   <ftp://sunsite.unc.edu/pub/Linux/apps/www/converters/>.])
fi

dnl Check for htmldoc:
AC_PATH_PROG(HTMLDOC, htmldoc)
if test -z "$HTMLDOC"; then
   AC_MSG_WARN([
   htmldoc was not found in your path.  It is used in 
   the software to output the agendas in PostScript
   or PDF. If lynx or htmldoc are not installed in your
   path, then this feature will not work.
   <http://www.easysw.com/htmldoc/>.])
fi

dnl Define HTDOCSDIR directory:
AC_ARG_WITH(htdocsdir, AC_HELP_STRING([--with-htdocsdir], [specify where htdocs files go (e.g. /soft/httpd/host/cdsdev/htdocs//)]), HTDOCSDIR=${withval})
if test -z "$HTDOCSDIR"; then
   AC_MSG_ERROR([
   You have not used the "--with-htdocsdir" argument that should define the installation directory for HTML documents.  
   Example: "--with-htdocsdir=/soft/httpd/host/cdsdev/htdocs//".])
fi   

dnl Define CGIBINDIR directory:
AC_ARG_WITH(cgibindir, AC_HELP_STRING([--with-cgibindir], [specify where cgibin files go (e.g. /soft/httpd/host/cdsdev/cgi-bin//)]), CGIBINDIR=${withval})
if test -z "$CGIBINDIR"; then
   AC_MSG_ERROR([
   You have not used the "--with-cgibindir" argument that should define the installation directory for CGI scripts.  
   Example: "--with-cgibindir=/soft/httpd/host/cdsdev/cgi-bin//".])
fi   

dnl Define UTILDIR directory:
AC_ARG_WITH(utildir, AC_HELP_STRING([--with-utildir], [specify where util files go (e.g. /usr/local/bin//)]), UTILDIR=${withval})
if test -z "$UTILDIR"; then
   AC_MSG_ERROR([
   You have not used the "--with-util" argument that should define the installation directory for CDS Agenda utilitites.  
   Example: "--with-utildir=/usr/local/bin//".])
fi   

dnl Define HTDOCSURL URL:
AC_ARG_WITH(htdocsurl, AC_HELP_STRING([--with-htdocsurl], [specify URL where htdocs files will be reached (e.g. http://cdsdev.cern.ch//)]), HTDOCSURL=${withval})
if test -z "$HTDOCSURL"; then
   AC_MSG_ERROR([
   You have not used the "--with-htdocsurl" argument that should define the URL where the HTML documents will be reached.  
   Example: "--with-htdocsurl=http://cdsdev.cern.ch//".])
fi   

dnl Define CGIBINURL URL:
AC_ARG_WITH(cgibinurl, AC_HELP_STRING([--with-cgibinurl], [specify URL where CGI scripts will be reached (e.g. http://cdsdev.cern.ch/cgi-bin//)]), CGIBINURL=${withval})
if test -z "$CGIBINURL"; then
   AC_MSG_ERROR([
   You have not used the "--with-cgibinurl" argument that should define the URL where the HTML documents will be reached.  
   Example: "--with-cgibinurl=http://cdsdev.cern.ch/cgi-bin//".])
fi   

dnl Define DBHOST database host:
AC_ARG_WITH(dbhost, AC_HELP_STRING([--with-dbhost], [specify DB server (e.g. cdsdb.cern.ch)]), DBHOST=${withval})
if test -z "$DBHOST"; then
   AC_MSG_ERROR([
   You have not used the "--with-dbhost" argument that should define which DB server to use.  
   Example: "--with-dbhost=cdsdb.cern.ch".])
fi   

dnl Define DBHOSTPORT (database host port):
DBHOSTPORT=3306
AC_ARG_WITH(dbhostport, AC_HELP_STRING([--with-dbhostport], [specify DB server's port]), DBHOSTPORT=${withval})
if test -z "$DBHOSTPORT"; then
   AC_MSG_ERROR([
   You have not used the "--with-dbhostport" argument that should define which port on DB server to connect to 
   Example: "--with-dbhostport=3456])
fi

dnl Define DBNAME database name:
AC_ARG_WITH(dbname, AC_HELP_STRING([--with-dbname], [specify DB name (e.g. cdsagenda)]), DBNAME=${withval})
if test -z "$DBNAME"; then
   AC_MSG_ERROR([
   You have not used the "--with-dbname" argument that should define DB name.  
   Example: "--with-dbname=cdsagenda".])
fi   

dnl Define DBUSER user name:
AC_ARG_WITH(dbuser, AC_HELP_STRING([--with-dbuser], [specify DB user name (e.g. cdsagenda)]), DBUSER=${withval})
if test -z "$DBUSER"; then
   AC_MSG_ERROR([
   You have not used the "--with-dbuser" argument that should define DB user name.  
   Example: "--with-dbuser=cdsagenda".])
fi   

dnl Define DBPASS user password:
AC_ARG_WITH(dbpass, AC_HELP_STRING([--with-dbpass], [specify DB user password (e.g. myp1ss)]), DBPASS=${withval})
if test -z "$DBPASS"; then
   AC_MSG_ERROR([
   You have not used the "--with-dbpass" argument that should define DB user password.  
   Example: "--with-dbpass=myp1ss".])
fi   

dnl Test supplied DB user credentials:

dnl Substitute variables:
AC_SUBST(VERSION)
AC_SUBST(MYSQL)
AC_SUBST(PHP)
AC_SUBST(HTDOCSDIR)
AC_SUBST(CGIBINDIR)
AC_SUBST(UTILDIR)
AC_SUBST(HTDOCSURL)
AC_SUBST(CGIBINURL)
AC_SUBST(DBHOST)
AC_SUBST(DBNAME)
AC_SUBST(DBUSER)
AC_SUBST(DBPASS)
AC_SUBST(DBHOSTPORT)

dnl Write output files:
AC_OUTPUT(Makefile \
	  dbutil/Makefile \
          dbutil/dbexec \
	  dbutil/dbtest \
	  dbutil/tabdrop.sql \
	  dbutil/tabfill.sql \
	  dbutil/tabcreate.sql \
          utils/xtxsl \
          utils/Makefile \
          utils/XML/Makefile \
	  htdocs/Makefile \
          htdocs/config/Makefile \
          htdocs/config/config.php \
	  htdocs/guide/Makefile \
	  htdocs/guide/english/Makefile \
	  htdocs/images/Makefile \
	  htdocs/images/cds_user_guide.css \
	  htdocs/images/style.css \
	  htdocs/images/stylemodify.css \
	  htdocs/images/xsftStyles.css \
	  htdocs/images/levelicons/Makefile \
	  htdocs/manager/Makefile \
	  htdocs/manager/images/Makefile \
	  htdocs/menus/Makefile \
	  htdocs/modification/Makefile \
	  htdocs/modification/checkAlarm.php \
	  htdocs/objects/Makefile \
	  htdocs/overview/Makefile \
	  htdocs/platform/Makefile \
	  htdocs/platform/system/Makefile \
	  htdocs/platform/template/Makefile \
	  htdocs/platform/authentication/Makefile \
	  htdocs/pocket/Makefile \
	  htdocs/stylesheets/Makefile \
	  htdocs/stylesheets/palm/Makefile \
	  htdocs/stylesheets/simpletext/Makefile \
	  htdocs/stylesheets/tools/Makefile \
	  htdocs/template/Makefile \
	  htdocs/tools/Makefile)

dnl Write help:
AC_CONFIG_COMMANDS_POST([echo "********************************************************************************"])
AC_CONFIG_COMMANDS_POST([echo "** Good, the CDS Agenda Software () is now ready for installation!     **"])
AC_CONFIG_COMMANDS_POST([echo "** You have entered the following parameters:                                 **"])
AC_CONFIG_COMMANDS_POST([echo "**   - HTML documents install directory : $HTDOCSDIR"])
AC_CONFIG_COMMANDS_POST([echo "**   - CGI scripts install directory    : $CGIBINDIR"])
AC_CONFIG_COMMANDS_POST([echo "**   - utilities install directory      : $UTILDIR"])
AC_CONFIG_COMMANDS_POST([echo "**   - HTML documents home URL          : $HTDOCSURL"])
AC_CONFIG_COMMANDS_POST([echo "**   - CGI scripts home URL             : $CGIBINURL"])
AC_CONFIG_COMMANDS_POST([echo "**   - DB server                        : $DBHOST"])
AC_CONFIG_COMMANDS_POST([echo "**   - DB name                          : $DBNAME"])
AC_CONFIG_COMMANDS_POST([echo "**   - DB username                      : $DBUSER"])
AC_CONFIG_COMMANDS_POST([echo "**   - DB password                      : $DBPASS"])
AC_CONFIG_COMMANDS_POST([echo "** Here are the steps to continue the building process:                       **"])
AC_CONFIG_COMMANDS_POST([echo "**   - Firstly, customize the self-explanatory config file                    **"])
AC_CONFIG_COMMANDS_POST([echo "**     './config/config.wml'.                                                 **"])
AC_CONFIG_COMMANDS_POST([echo "**   - Secondly, type 'make' to build your customized CDS Agenda installation.   **"])
AC_CONFIG_COMMANDS_POST([echo "**   - Thirdly, type 'make create-tables' if you have not created      **"])
AC_CONFIG_COMMANDS_POST([echo "**     tables yet.                                                            **"])
AC_CONFIG_COMMANDS_POST([echo "**   - Finally, type 'make install' to install the system.                    **"])
AC_CONFIG_COMMANDS_POST([echo "** Good luck, and thanks for choosing the CDS Agenda Software.                **"])
AC_CONFIG_COMMANDS_POST([echo "**              -- CDS Agenda Development Group <cds.support@cern.ch>         **"])
AC_CONFIG_COMMANDS_POST([echo "********************************************************************************"])

## end of file
