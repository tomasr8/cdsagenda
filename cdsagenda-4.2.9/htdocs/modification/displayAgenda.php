<?php
// $Id: displayAgenda.php,v 1.1.1.1.4.20 2004/07/29 10:06:08 tbaron Exp $

// displayAgenda.php --- modification main interface
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// displayAgenda.php is part of CDS Agenda.
//
// CDS Agenda is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// CDS Agenda is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with ; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// Commentary:
//
// 
//

$ida = $_REQUEST[ "ida" ];

require_once '../AgeDB.php';
require_once '../AgeLog.php';
require_once( "../config/config.php" );
require_once( "../platform/template/template.inc" );
require_once '../platform/system/logManager.inc';
require_once "../platform/system/archive.inc";
require_once "../platform/system/commonfunctions.inc";
require_once "../platform/authentication/sessinit.inc";
        
$thisScriptName = "modification/displayAgenda.php";
	
// new template
$Template = new Template( $PathTemplate );
$Template->set_file(array("error" => "error.ihtml",
                          "mainpage"  => "displayAgenda.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml"));	

$Template->set_var("display_supportEmail", $support_email);
$Template->set_var("display_runningAT", $runningAT);
$Template->set_var("display_ida",$ida);
$Template->set_var( "images", "$IMAGES_WWW" );
$Template->parse( "display_jsmenutools", "JSMenuTools", true );

$canAddSubEvent   = 
$canEditAgenda    = 
$canDeleteAgenda  =
$canCloneAgenda   =
$canEditTimeTable = 
$canManageFiles   = 
$canUpload        = true;

deletecache();

$db = &AgeDB::getDB();
$sql = "select confidentiality,apassword,fid,stylesheet,an from AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}
$numRows = $res->numRows();
if ( $numRows == 0 ) {
	if ( ERRORLOG ) {
		$log->logError( __FILE__ . "." . __LINE__, "main", " Retrieved 0 elements with select '" . $sql . "' " );
	}
    outError( "$ida: Agenda does not exist", "01", &$Template );
	exit;
}
$row = $res->fetchRow();

$confidentiality = $row['confidentiality'];
$apassword       = $row['apassword'];
$fid             = $row['fid'];
$stylesheet      = $row['stylesheet'];
$an              = $row['an'];

// ACCESS WITH A PASSWORD
if ( $confidentiality == "password") {
    if ( $PHP_AUTH_PW == "" ) {
        Header( "WWW-Authenticate: Basic realm=\"agenda\"" );
        Header( "HTTP/1.0 401 Unauthorized" );
        echo "Access denied.\n";
        exit;
    }
    else if ( $PHP_AUTH_PW != $apassword ) {
        Header( "WWW-Authenticate: Basic realm=\"agenda\"" );
        Header( "HTTP/1.0 401 Unauthorized" );
        echo "Access denied.\n";
        exit;
    }
}

$log = &AgeLog::getLog();
$MainWndName = "AgendaWnd".$ida;
$ModWndName = "modifyWnd".$ida;

$Template->set_var("display_ModWndName",$ModWndName);
$Template->set_var("display_MainWndName",$MainWndName);

// get global protection
$sql2 = "SELECT modifyPassword
         FROM LEVEL
         WHERE uid='$fid'";
$res2 = $db->query($sql2);
$row2 = $res2->fetchRow();
$globalprotect = $row2['modifyPassword'];

$AN = $HTTP_COOKIE_VARS["AN$ida"];
if ($an != $AN) {
    if ($globalprotect != "" && $globalprotect != $AN) {
        outError( "Access to agenda $ida refused: Bad Password", "01", &$Template );
        exit;
    }
}


$sql = "select level from LEVEL where uid='$fid'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if (( $numRows == 0 ) && ( ERRORLOG )) {
	$log->logError( __FILE__ . "." . __LINE__, "main", " Retrieved 0 elements with select '" . $sql . "' " );
}
$row = $res->fetchRow();
$level = $row['level'] + 1;

$core = "";
$core = "
<FORM method=\"POST\" action=\"agendaFactory.php\">\n";

if (isset($newstylesheet)) {
	$stylesheet = $newstylesheet;
}
if ($stylesheet == "") {
	$stylesheet = "standard";
}
     
$core .= "<INPUT type=hidden name=ida value='$ida'>\n";
$core .= "<INPUT type=hidden name=ids value=''>\n";
$core .= "<INPUT type=hidden name=idt value=''>\n";
$core .= "<INPUT type=hidden name=session value=''>\n";
$core .= "<INPUT type=hidden name=add value=''>\n";
$core .= "<INPUT type=hidden name=scale value='5'>\n";
$core .= "<INPUT type=hidden name=sessionscale value='0'>\n";
$core .= "<INPUT type=hidden name=request value=''>\n";
$core .= "<INPUT type=hidden name=newsession value='$newsession'>\n";
$core .= "<INPUT type=hidden name=sday value=''>\n";
$core .= "<INPUT type=hidden name=fid value='$fid'>\n";
$core .= "<INPUT type=hidden name=level value='$level'>\n";
$core .= "<INPUT type=hidden name=position value=0>\n";
$core .= "<INPUT type=hidden name=from value=''>\n";
$core .= "<INPUT type=hidden name=stylesheet value='$stylesheet'>\n";
$core .= "</FORM>";

//###########################################################################
//       Display top header
//###########################################################################

$pid = uniqid("");
$starttime = time();

if (!is_dir("$AGE/tmp")) {
	mkdir("$AGE/tmp",0700);
}

// create XML output
//////////////////////////////////////////////////////

require_once "../XMLoutput.php";

if ( DEBUGGING ) {
   $log->logDebug( __FILE__, __LINE__, " color style '$stylesheet' " );
}
 
$agenda = new agenda($ida);   
$xml = $agenda->displayXML();

$fp = fopen("$AGE/tmp/processXML_$pid.xml","w+");
fwrite($fp,$xml);
fclose($fp);

$endtime1 = time();
// use the stylesheet to create the html output
//////////////////////////////////////////////////////
$help = "$AGE/stylesheets/$stylesheet.xsl";
       
$htmltext = `$XTXSL $AGE/tmp/processXML_$pid.xml $help 2>> $AGE/tmp/errors_$pid`;

// insert the full image path into this stylesheet (PostScript + PDF creation)
$htmltext = ereg_replace("<img([^>]*) src=\"images/","<img\\1 src=\"${AGE_WWW}/images/",$htmltext);

if ( DEBUGGING ) {
	 $log->logDebug( __FILE__, __LINE__, " $XTXSL $AGE/tmp/processXML_$pid.xml $help 2>> $AGE/tmp/errors_$pid " );
}
	       
// modify the html output (insert modif access points)
//////////////////////////////////////////////////////

// agenda modification
$htmltext = preg_replace("/\[modifyagenda\]/","<a href=\"\" onClick=\"document.forms[0].ids.value='';event.cancelBubble = true;javascript:closeAll();javascript:popUp('ToolAgenda',true,event);return false;\"><img hspace=\"5\" vspace=\"0\" border=\"0\" src=\"../images/link.gif\" alt=\"click here\" width=\"11\" height=\"11\"/>",$htmltext);
$htmltext = str_replace("[/modifyagenda]","</a>",$htmltext);
	 
// session modification
$htmltext = preg_replace("/\[modifysession ids=\"(s[0-9]*)\"\]/","<a href=\"\" onClick=\"document.forms[0].idt.value='';document.forms[0].ids.value='\\1';event.cancelBubble = true;javascript:closeAll();javascript:popUp('ToolSession',true,event);return false;\"><img hspace=\"5\" vspace=\"0\" border=\"0\" src=\"../images/link.gif\" alt=\"click here\" width=\"11\" height=\"11\"/>",$htmltext);
$htmltext = str_replace("[/modifysession]","</a>",$htmltext);

// talk modification
$htmltext = preg_replace("/\[modifytalk ids=\"(s[0-9]*)\" idt=\"(t[0-9]*)\"\]/","<a href=\"\" onClick=\"document.forms[0].ids.value='\\1';document.forms[0].idt.value='\\2';event.cancelBubble = true;javascript:closeAll();javascript:popUp('ToolTalk',true,event);return false;\"><img hspace=\"5\" vspace=\"0\" border=\"0\" src=\"../images/link.gif\" alt=\"click here\" width=\"11\" height=\"11\"/>",$htmltext);
$htmltext = str_replace("[/modifytalk]","</a>",$htmltext);

// subtalk modification
$htmltext = preg_replace("/\[modifysubtalk ids=\"(s[0-9]*)\" idt=\"(t[0-9]*)\"\]/","<a href=\"\" onClick=\"document.forms[0].ids.value='\\1';document.forms[0].idt.value='\\2';event.cancelBubble = true;javascript:closeAll();javascript:popUp('ToolSubPoint',true,event);return false;\"><img hspace=\"5\" vspace=\"0\" border=\"0\" src=\"../images/link.gif\" alt=\"click here\" width=\"11\" height=\"11\"/>",$htmltext);
$htmltext = str_replace("[/modifysubtalk]","</a>",$htmltext);

// lecture modification
$htmltext = preg_replace("/\[modifylecture\]/","<a href=\"\" onClick=\"event.cancelBubble = true;javascript:closeAll();javascript:popUp('MenuLecture',true,event);return false;\"><img hspace=\"5\" vspace=\"0\" border=\"0\" src=\"../images/link.gif\" alt=\"click here\" width=\"11\" height=\"11\"/>",$htmltext);
$htmltext = str_replace("[/modifylecture]","</a>",$htmltext);

// display output
///////////////////////////////////////////////////////
$core .= $htmltext;

$endtime2 = time();

$time1 = $endtime1 - $starttime;
$time2 = $endtime2 - $endtime1;

$core .= "<BR><BR><SMALL>XML creation in $time1 seconds<BR>";
$core .= "XSLt processing in $time2 seconds</SMALL><BR>";

$errors = `cat $AGE/tmp/errors_$pid`;
if (ereg(".xsl",$errors)) {
    outError( "<b>An error has occured during the XML processing (in the XSL stylesheet):</b><br><i>$errors</i>", "01", &$Template );
	exit;
}
else if (ereg(".xml",$errors)) {
    outError( "<b>An error has occured during the XML processing (probably in the XML output, please contact $support_email):</b><br><i>$errors</i>", "01", &$Template );
	exit;
}
if ( filesize( "$AGE/tmp/errors_$pid" ) == 0 ) {
    @unlink("$AGE/tmp/errors_$pid");
    @unlink("$AGE/tmp/processXML_$pid.xml");
}

// Create other Menus
include '../menus/modifymenus.php';
$menuText .= CreateModifyMenus();

// Display Menus
$Template->set_var("display_menus", $menuText);

// Scrolling Main window
if ($position != "") {
    $scrollText = "window.scrollTo(0,0);window.scrollTo(0,$position);";
}
else {
    $scrollText = "";
}
$Template->set_var("display_scrollTo","$scrollText");

$Template->set_var("display_core", $core);
$Template->pparse( "final-page" , "mainpage" );

function deletecache()
{
	// this function deletes the cache set up by jpcache.
	global $ida,$TMPDIR,$AGE_WWW;

    $url_array = parse_url($AGE_WWW);
    $path = $url_array['path'];
	$cachekey = md5("POST=a:0:{} GET=a:1:{s:3:\"ida\";s:".strlen("$ida").":\"$ida\";}");
	$masterkey = ereg_replace("_$","",str_replace("/","_",str_replace("//","/","$path"))) . "_fullAgenda_php";
	$key = "jpcache-$masterkey:$cachekey";
	if (file_exists("$TMPDIR/$key"))
	{
		unlink("$TMPDIR/$key");
	}
}
?>