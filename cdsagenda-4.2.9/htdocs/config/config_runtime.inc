<?
#$Id: config_runtime.inc,v 1.1.1.1.4.19 2004/07/29 10:06:04 tbaron Exp $

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

$runningAT = "CERN";

###############################################################
#                                                             #
# Part I - LOGGING, DEBUGGING                                 #
#                                                             #
###############################################################

// enable logging for the executions
define( "EXECLOG", FALSE );
// enable logging for the errors
define( "ERRORLOG", FALSE );
// enable logging of the platform code
define( "PLATFORMLOG", FALSE );
// Enable debugging mode
define( "DEBUGGING", FALSE );

if ( EXECLOG || ERRORLOG || PLATFORMLOG || DEBUGGING ) {
    define( "LOGGING", TRUE );
}
else {
    define( "LOGGING", FALSE );
}

if ( EXECLOG || ERRORLOG ) {
    define( "SOMELOG", TRUE );
}
else {
    define( "SOMELOG", FALSE );
}
  
#LOGGING
#################################################################
$logLog = false;
$logDebug = false;
$logError = false;
$logExec = false;
$logSession = false;
$logSystem = false;
$sysLogActive = false;
// log files location if $logSystem == true
$logSysFile = "$AGE/log/logPHP_${runningAT}_";
$logFilePath = $logSysFile; 	
// log files location if $logSystem == false
$logNotSysFile = "$AGE/Logs/"; 
  
###############################################################
#                                                             #
# PART II - DEFAULT VALUES                                    #
#                                                             #
###############################################################

// default location in the agenda creation forms
$baseLocationName = "CERN";
// Display (fil.php fileSession.php fileTalk.php) data about permission and file description
$displayFilePermissionsAndDescription = FALSE;
// Inside the upload pages activate the link that allow the user to display the file
$displayFileActive = TRUE;
// Link to create postscript
$createPSActive = TRUE;
// Link to create a plain text from a pdf
$createTXTActive = TRUE
;// Link to create PDF
$createPDFActive = TRUE;
// URL to a bug collecting interface
// If you don't have such an interface, just leave this variable
// empty 
$support_submit = "";
// put here the list of IP addresses which have the right to access
// an agenda in case of domain restriction
$restrictAddress = array( 0 => 
                          "128.141", 
                          "128.142", 
                          "137.138", 
                          "192.91", 
                          "194.12", 
                          "192.16" );
// this variable contains the list of all stylesheets written and used
// in this local installation
// If you have created a new stylesheet, add it to the stylesheets
// directory then to this list
$stylesheets = array (
                      "standard" => "Standard&nbsp;Meeting&nbsp;(multi&nbsp;sessions)",
                      "nosession" => "Standard&nbsp;Meeting&nbsp;(single&nbsp;session)",
                      "standard2" => "Standard&nbsp;Meeting&nbsp;(inline&nbsp;minutes)",
                      "minutes" => "All&nbsp;minutes&nbsp;in&nbsp;one&nbsp;file",
                      "administrative" => "Administrative",
                      "nicecompact" => "Compact",
                      "lecture" => "Lecture",
                      "event" => "Simple&nbsp;Event",
                      "tools/printable" => "Printable&nbsp;Output");
// File upload default values
$maxfileuploadsize = "80000000";
// List of years to use in the overview and search interfaces
$years = array("1995",
               "1996",
               "1997",
               "1998",
               "1999",
               "2000",
               "2001",
               "2002",
               "2003",
               "2004");
// Starting year of the statistics in the tools/stats.php page
$startingyear = 1999;
// Links to appear in the main menu
$mainmenulinks = array("CDS&nbsp;Home"=>"http://cds.cern.ch",
                       "CDS&nbsp;Submit"=>"http://doc.cern.ch/Submit",
                       "CDS&nbsp;Search"=>"http://weblib.cern.ch",
                       "CDS&nbsp;Convert"=>"http://doc.cern.ch/Convert",
                       "CDS&nbsp;Scan"=>"http://doc.cern.ch/Scan",
                       "Library&nbsp;WebSite"=>"http://library.cern.ch");

###############################################################
#                                                             #
# Part III - EMAILS                                           #
#                                                             #
###############################################################

// Is the tool authorized to send emails?
$MAILSERVERON = TRUE;
// If this flag is set, the program will automatically create 
// the email address of the user entering his/her first/last
// names ( it uses $standardEMAILDomain for this)
$AUTOCREATEEMAIL = TRUE;
// If this flag is set, the program will automatically complete
// partially submitted email addresses (addresses without "@")
// (it uses $standardEMAILDomain for this)
$AUTOCOMPLETEEMAIL = TRUE;
// Default email domain (used to automatically create the email
// address when a user enters his first and last names
// This is also used to complete partial email addresses
// (addresses without "@")
$standardEMAILDomain = "@cern.ch";
// email address to the support team
$support_email = "";
// email address to the administrator(s)
// receive emails when an agenda is created
$admin_email = "";
// email to the scanning service if any...
$scan_email = "";
// this email address with receive copies of all alerts triggered by
// the system.
$alarm_email = "";

###############################################################
#                                                             #
# Part IV - FEATURE ACTIVATION/DESACTIVATION                  #
#                                                             #
###############################################################

# ARCHIVE
###############################################################

// Activate the event hit stats
$EVENTHITSTATSActive = FALSE;
// Activate the download stats
$DOCUMENTSTATSActive = FALSE;   
// Activate the authorization access stats
$ACCESSSTATSActive = FALSE;
// Activate the display of file names instead of category name
// when only one file is in a category
$directDisplaySingleFiles = TRUE;

# Cache options
###############################################################
// set this variable to TRUE if you want to use
// the caching feature
$PHPCacheACTIVE = FALSE;
// Here you can setup the duration of the cached page in
// seconds (604800 => 1 week)
$cachetimeout = 604800;

# FTP-UPLOAD SETTINGS
################################################################
// How links are saved, with NEW are saved only the http string, then is
// retrieved and sent to the
// client with header location statement, with OLD is created a complete
// php file with the redirection
$LINK_MANAGMENT = "OLD";

# COMMON UPLOAD SETTINGS
############################################################################
// Lists of recognized types by extensions
$RECOGNIZED_MIME_TYPES = array ( 
                     "css" => "text/css",
                     "rtx" => "text/richtext",
                     "asc" => "text/plain",
                     "xml" => "text/xml",
                     "" => "text/plain",
                     "txt" => "text/plain",
                     "text" => "text/plain",
                     "pdf" => "application/pdf",
                     "ps" => "application/postscript",
                     "rtf" => "application/rtf",
                     "pps" => "application/vnd.ms-powerpoint",
                     "ppt" => "application/vnd.ms-powerpoint",
                     "doc" => "application/msword",
                     "gif" => "image/gif",
                     "jpg" => "image/jpeg",
                     "jpeg" => "image/jpeg",
                     "jpe" => "image/jpeg",
                     "bmp" => "image/bmp",
                     "tif" => "image/tiff",
                     "tiff" => "image/tiff",
                     "mpeg" => "video/x-mpeg",
                     "mpg" => "video/x-mpeg",
                     "rm" => "audio/x-pn-realaudio",
                     "ram" => "audio/x-pn-realaudio",
                     "mp3" => "audio/x-mp3",
                     "wav" => "audio/x-pn-wav",
                     "avi" => "video/anim",
                     "link" => "unknown",
                     "htm" => "text/html",
                     "html" => "text/html",
                     "smil" => "application/smil",
                     "smi" => "application/smil",
                     "dvi" => "application/x-dvi",
                     "bin" => "application/octet-stream",
                     "pgp" => "application/pgp",
                     "eps" => "application/postscript",
                     "xlw" => "application/vnd.ms-excel",
                     "xls" => "application/vnd.ms-excel",
                     "arj" => "application/x-arj",
                     "asp" => "application/x-asp",
                     "bz" => "application/x-bzip",
                     "bz2" => "application/x-bzip",
                     "tgz" => "application/x-compressed-tar",
                     "gtar" => "application/x-gtar",
                     "gz" => "application/x-gzip",
                     "tar" => "application/x-tar",
                     "tcl" => "application/x-tcl",
                     "tex" => "application/x-tex",
                     "zip" => "application/zip",
                     "swf" => "application/x-shockwave-flash2-preview",
                     "rf" => "application/x-shockwave-flash2-preview",
                     "sa" => "application/x-unix-archive",
                     "a" => "application/x-unix-archive",
                     "af" => "audio/aiff",
                     "aiff" => "audio/aiff",
                     "aifc" => "audio/aiff",
                     "mod" => "audio/x-mod");
             
// Map server
// If there is one in your institute and it can be accessed like this:
// $mapServerAddress?buiding, please indicate it here:
$mapServerACTIVE = FALSE;
$mapServerAddress = "";

// Add Event Menu
// Do you want your user to be able to create single-session meetings,
// multi-sessions meetings, events or lectures
$AddMeetingActive = TRUE;
$AddSingleSessionMeetingActive = TRUE;
$AddSimpleEventActive = TRUE;
$AddLectureActive = TRUE;

// Change View Menu
// do you want to provide the various conversions features
// Those conversions concern the agenda display (not the transfered files)
$OtherFormatPSActive = TRUE;
$OtherFormatPDFActive = TRUE;
$OtherFormatExcellActive = TRUE;
$OtherFormatTextActive = FALSE;
$OtherFormatvCalActive = TRUE;

// activate the possibility for the user to set up alarms on a given meeting
$agendaAlarmFlag = TRUE;

// Activate the statistics feature in the help menu
$statsOn = TRUE;

// Activate and deactivate the room booking feature in the Session Tool menu (modification menu)
$roomBookingSessionActive = TRUE;

// Scanning Service
$scanServiceActive = FALSE;


###############################################################
#                                                             #
# Part V - Local Pathes                                       #
#                                                             #
###############################################################

// A temporary directory
$TMPDIR = "/tmp";
// The directory in which the attached files will be stored
// By default this directory is created in the tool htdocs
// directory but you can also choose to move it elsewhere
// In this case, please modify the following value:
$ARCHIVE = "$AGE/archive";
$ARCHIVEURL = "${AGE_WWW}/archive";
// Path to the trash for files
$ARCHIVETRASH = "$AGE/archivetrash";
// Short URL to access directly an agenda
// This is the responsability of the local server administrator
// to create such a script
// If you don't want to, just set this variable to ""
$shortAgendaURL = "";
// Short URL to access directly a category
// This is the responsability of the local server administrator
// to create such a script
// If you don't want to, just set this variable to ""
$shortCategURL = "";
// URL to a feedback collection interface
$feedbackURL = "";


###############################################################
#                                                             #
# Part VI - Room Booking System                               #
#                                                             #
###############################################################

// URL to the local room booking system
$libraryADDRESS = "";
// parameters to be passed to this URL
$withParameters = "w=y&STDCRBOK=";


###############################################################
#                                                             #
# Part VII - Report Page settings                             #
#                                                             #
###############################################################

// List of fields to put into the private report
$privateReportFieldsList = array( 0 =>"cosponsor",
                                  "collaborations", 
                                  "organizers",
                                  "localorganizers",
                                  "deadrequest",
                                  "limitedpartecipations",
                                  "venues", 
                                  "laboratories", 
                                  "secretary",
                                  "expparts", 
                                  "email" );
// The strings that will appear on the output
$privateReportFieldsNames = array( 0 =>"Co-sponsor", 
                                   "Collaboration(s)", 
                                   "Director(s)/Organizer(s)",
                                   "Local Organizer(s)",
                                   "Deadline(s) for requesting participation",
                                   "Limited Participation",
                                   "Venue(s)", 
                                   "Laboratory", 
                                   "Secretary", 
                                   "Expected Participants", 
                                   "E-mail"  );
// Same arrays as the prevoius two but for the hosted activity (to appear only in the internal view .. now)
$privateReportFieldsListHOSTED = array( 0 =>"cosponsor",
                                        "collaborations", 
                                        "organizers",
                                        "localorganizers",
                                        "deadrequest",
                                        "limitedpartecipations",
                                        "venues", 
                                        "laboratories", 
                                        "secretary",
                                        "expparts" );
// The strings that will appear on the output
$privateReportFieldsNamesHOSTED = array( 0 =>"Co-sponsor", 
                                         "Collaboration(s)", 
                                         "Director(s)/Organizer(s)",
                                         "Local Organizer(s)",
                                         "Deadline(s) for requesting participation",
                                         "Limited Participation",
                                         "Venue(s)", 
                                         "Laboratory", 
                                         "Secretary", 
                                         "Expected Participants" ); 
// List of fields to put into the public report
$publicReportFieldsList = array( 0 =>"cosponsor",
                                 "collaborations", 
                                 "organizers",
                                 "localorganizers",
                                 "deadrequest",
                                 "limitedpartecipations", 
                                 "email" );
// The strings that will appear on the output
$publicReportFieldsNames = array( 0 =>"Co-sponsor", 
                                  "Collaboration(s)", 
                                  "Director(s)/Organizer(s)",
                                  "Local Organizer(s)",
                                  "Deadline(s) for requesting participation",
                                  "Limited Participation", 
                                  "E-mail" );
// Note under the title in the internal version of the calendar
$INTERNAL_VERSION_HEADNOTE = "(FOR INTERNAL USE ONLY)";

    
###############################################################
#                                                             #
# Part VII - STYLESHEET SETTINGS                              #
#                                                             #
###############################################################

// Standard stylesheet use to view meetings
$standardViewStylesheet = "standard";

###############################################################
#                                                             #
# Part VIII - ROOMS SECTION                                   #
#                                                             #
###############################################################

$roomsVector = array( 
                     0 => "Main Auditorium", 
                     "Room A", 
                     "Room B", 
                     "Council Chamber", 
                     "AT Auditorium",
                     "IT Auditorium", 
                     "LHC Auditorium", 
                     "PE Auditorium", 
                     "PS Auditorium", 
                     "SL Auditorium", 
                     "TH Auditorium",
                     "Video Conference" );

###############################################################
#                                                             #
# Part IX - MESSAGES                                          #
#                                                             #
###############################################################

// about document scanning
$scanningSendMsg1 = "documents will be scanned at 10CHF per document";
$scanningSendMsg2 = "Your request has been submitted: an email has been sent to the scanning service.<P> You <b>MUST</b> now send the paper copy by internal mail to the <b>Printshop, Scanning Service</b>.<P><b>IMPORTANT</b>: <UL><LI>1/ Attach to each document the name of its corresponding scanned file (see above TABLE).<LI>2/ Join a copy of the present page to your request.</UL><P><HR><font size=-1> The document will be made available in TIFF during the coming week.<BR>The scanning work will be charged by TID, like a print request.</font>";

// new agenda/event/lecture creation	
$newEventMSG1 = "For any request or comment, please contact: $support_email";
$newLectureMSG1 = "For any request or comment, please contact: $support_email";

// AGE_footer.inc messages
$AGE_FOOTER_MSG1 = " 	If you want to use this conference management tool<BR>for your own meetings/conferences, please <a href=\"mailto:$support_email\">contact us</a>.";
$AGE_FOOTER_MSG2 = "Maintained by: The CDS Support Team (<A HREF=\"$feedbackURL\">Bugs and reports</A>)<br>";

// EMAIL INTERFACE MESSAGES
$emailHeader1 = 
"What follows is the description and/or the agenda of the event you requested.
The documents associated to this event are listed under the
header \"Documents\". By now, only the documents associated to the
meeting as a whole, not those associated to individual lectures, can be
retrieved via e-mail (this restriction will be removed).\n";


###############################################################
#                                                             #
# Part X - AUTHENTICATION                                     #
#                                                             #
###############################################################

// Authentication features on/off
$authentication = TRUE;
$changePassword = TRUE;

// Login using an IMAP server
// see file htdocs/platform/authentication/imapfunctions.inc for
// configuring this feature
$useIMAP = FALSE;

// Group checking using an LDAP server
// see file htdocs/platform/authentication/ldapfunctions.inc for
// configuring this feature
$useLDAP = FALSE;

// Administrator (superuser)
$superuser_email = "thomas.baron@cern.ch";


?>
