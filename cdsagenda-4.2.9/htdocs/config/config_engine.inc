<?
#$Id: config_engine.inc,v 1.1.1.1.4.4 2002/10/23 08:56:25 tbaron Exp $

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

## Please *do not edit* this file
## These are not local settings!

###############################################################
#LOGGING, DEBUGGING
###############################################################

# ERROR CODE PREFIX ASSOCIATED TO THE FILE NAMES
###############################################################
$CODE_ERROR = array ( 1=>"$AGE/displayLevel.php", 
                      2=>"$AGE/List.php", 
                      3=>"$AGE/agendamap.php", 
                      4=>"$AGE/fullAgenda.php", 
                      5=>"$AGE/makePDF.php",
                      6=>"$AGE/makePS.php",
                      7=>"$AGE/reportLog.php",
                      8=>"$AGE/search.php",
                      9=>"$AGE/showSMR.php",
                      10=>"$AGE/XMLoutput.php",
                      11=>"$AGE/askarchive.php",
                      12=>"$AGE/manager/Main.php",
                      13=>"$AGE/manager/modify.php",
                      14=>"$AGE/manager/age.php",
                      15=>"$AGE/manager/displayLevel.php",
                      16=>"$AGE/modification/access.php",
                      17=>"$AGE/modification/addSession.php",
                      18=>"$AGE/modification/calendar.php",
                      19=>"$AGE/modification/fil.php",
                      20=>"$AGE/modification/linkAgenda.php",
                      21=>"$AGE/modification/modifyAgenda.php",
                      22=>"$AGE/modification/modifyXMLoutput.php",
                      23=>"$AGE/modification/SendAlertEmail.php",
                      24=>"$AGE/modification/addAgenda.php",
                      25=>"$AGE/modification/addSubTalk.php",
                      26=>"$AGE/modification/checkAlarm.php",
                      27=>"$AGE/modification/fileSession.php",
                      28=>"$AGE/modification/linkSession.php",
                      29=>"$AGE/modification/modifyLecture.php",
                      31=>"$AGE/modification/setUpAlarm.php",
                      32=>"$AGE/modification/addEvent.php",
                      33=>"$AGE/modification/addTalk.php",
                      34=>"$AGE/modification/clon.php",
                      35=>"$AGE/modification/fileTalk.php",
                      36=>"$AGE/modification/linkTalk.php",
                      37=>"$AGE/modification/modifySession.php",
                      38=>"$AGE/modification/roomBook.php",
                      39=>"$AGE/modification/uploadAgenda.php",
                      40=>"$AGE/modification/addLecture.php",
                      41=>"$AGE/modification/authorize.php",
                      42=>"$AGE/modification/correctAlarm.php",
                      44=>"$AGE/modification/MinWritSession.php",
                      45=>"$AGE/modification/modifySubTalk.php",
                      46=>"$AGE/modification/scanning.php",
                      47=>"$AGE/modification/uploadSession.php",
                      48=>"$AGE/modification/broadcastTalk.php",
                      49=>"$AGE/modification/displayAgenda.php",
                      50=>"$AGE/modification/MinWritTalk.php",
                      51=>"$AGE/modification/modifyTalk.php",
                      52=>"$AGE/modification/scanningSend.php",
                      53=>"$AGE/modification/uploadTalk.php",
                      54=>"$AGE/modification/fileTalk.php",
                      55=>"$AGE/overview/dayperiod.php", 
                      56=>"$AGE/overview/getSTime.inc", 
                      57=>"$AGE/overview/monthdisplay.php",
                      58=>"$AGE/overview/overview.php",
                      60=>"$AGE/overview/weekperiod.php",
                      61=>"$AGE/overview/yearperiod.php",
                      62=>"$AGE/overview/daydisplay.php",
                      63=>"$AGE/overview/getRoom.inc",
                      64=>"$AGE/overview/monthperiod.php",
                      65=>"$AGE/overview/PDFparam.php",
                      66=>"$AGE/overview/PSparam.php",
                      67=>"$AGE/overview/weekdisplay.php",
                      68=>"$AGE/overview/yeardisplay.php" );



// Dir separator, it should work with both ... but ...
$dirSeparator = "/";
     
###############################################################
# ARCHIVE
###############################################################
// Tables and definitions about archive module
define ( "ORIGINALDATATABLE", "ORIGINALDATA" );
define ( "ACTUALDATATABLE", "ACTUALDATA" );
define ( "COMPOSEDBYTABLE", "COMPOSEDBY" );
define ( "STATTABLE", "DOCUMENTSTATS" );
define ( "ACCESSSTAT", "ACCESSSTATS" );
define ( "FORMATTABLE", "FORMAT" );
define ( "HITSTAT", "EVENTHITSTATS" );


###############################################################
# BUILTIN ACCESS PATHES
###############################################################
// XML parser executable
$XTXSL = "$UTIL/xtxsl";
// Access to the file script (link manager)
$SETLINK = "${AGE_WWW}/askArchive.php";
// Images directory
$IMAGES = "images/";
// Images URL
$IMAGES_WWW = "${AGE_WWW}/images";
// path to the agenda dtd
$agendaDTD = $AGE . "/agenda.dtd";
// directory containing the template files
$PathTemplate = "$AGE/template";

###############################################################
# COOKIES NAME
###############################################################
// cookie for login
$reportPageCookieName = "repCk_CERN__lgEm";
// Name of the prefix var that store the id of the agenda the current client is allowed to modify
$agendaPWPREFIXName = "repCK__CERN__nPW";  
// Cookie used for the generic user-password access
$userAccessCookieName = $reportPageCookieName; 

###############################################################
# UNCHANGEABLE DEFAULT VALUES
###############################################################
// It enables or disable the extensions over table AGENDA
// These extension involve all the fields used for the creation of a report
$agendaTABLEExtension_1 = FALSE;
// This settings activate the 'report' page (additional fields in the agenda)
$calendarActive = FALSE && $agendaTABLEExtension_1;
// Activate the keyword field in the TALK table
$TALKKEYWORDSActive = FALSE;
// New Search mode of operation
$newSearchMode = FALSE;
// If true the blank into the uploded files are replaced with $replaceBlankINFileNameChar
$replaceBlankINFileName = FALSE;
$replaceBlankINFileNameChar = "_";
# TEMPLATE NAMES
##################################################################
define( "REPORT_ON", 1 );
define( "REPORT_OFF", 0 );
$templateNamesVec[ "addAgenda.php" ][REPORT_OFF] = "addAgendaNoReport.ihtml";
$templateNamesVec[ "addAgenda.php" ][REPORT_ON] = "addAgenda.ihtml";
$templateNamesVec[ "modifyAgenda.php" ][REPORT_OFF] = "modifyAgendaNoReport.ihtml";
$templateNamesVec[ "modifyAgenda.php" ][REPORT_ON] = "modifyAgenda.ihtml";   
# EXTENSION ACTIVATION ABOUT AUTHORIZATION METHOD
###############################################################    
$activeCookiesForAgendaPW = TRUE; 
$activeCookiesForUserPW = FALSE; 
// Path where is located the trash for the old-style events (used only with the new archive managment with database support)
$oldEventTrashPath = "/tmp/";
# EMAIL INTERFACE
############################################################### 
$EMAIL_SYSTEM_SUBJECT = "automatic meeting info. delivering system";
$EMAIL_NO_DOCUMENT_MSG = "No documents of type $type associated to this event";

###############################################################
# AUTHORIZATION
###############################################################

# USERS GROUP SPECIFICATION ( AUTHENTICATION MODULE )
################################################################
// Can administrate everything about agenda
define( "AGENDA_ADMIN", 1 );
// Administrate some section of agenda, like creation of new events
// and so on
define( "OFFICE_ADMIN", 2 );
// Employees of the organization, without specific administrative
// tasks over agenda
define( "CENTRE_ADMIN", 4 );
// All the other registered users
define( "GUESTS", 8 );

// Groups allowed to see the "internal" version of the report
$reportInternalGroups = AGENDA_ADMIN + OFFICE_ADMIN + CENTRE_ADMIN;

###############################################################
# DATABASE
###############################################################
${"dbType-${dbName}"} = "mysql";
?>