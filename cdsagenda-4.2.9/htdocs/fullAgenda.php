<?php
//$Id: fullAgenda.php,v 1.1.1.1.4.18 2004/07/29 10:06:04 tbaron Exp $

// fullAgenda.php --- display the specified agenda
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// fullAgenda.php is part of CDS Agenda.
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
require_once 'AgeDB.php';
require_once 'AgeLog.php';
require_once 'config/config.php';
require_once 'platform/template/template.inc';
require_once 'platform/system/logManager.inc';
require_once 'platform/system/commonfunctions.inc';
require_once "platform/system/archive.inc";

$thisScriptName = "fullAgenda.php";
$ida = $_GET["ida"];

$Template = new Template($PathTemplate);
$Template->set_file(array("error" => "error.ihtml",
                          "mainpage"  => "fullAgenda.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));

$Template->set_var("display_supportEmail", $support_email);
$Template->set_var("display_runningAT", $runningAT);
$Template->set_var("display_ida",$ida);
$Template->set_var("images", $IMAGES_WWW);

$thisFound = false;

$db  = & AgeDB::getDB();

$sql = "select fid,title,DATE_FORMAT(stdate,'%M %D %Y') as fdate  from AGENDA where id='$ida'";
$res = $db->query($sql);

if (DB::isError($res)) {
	die ($res->getMessage());
}

if ($res->numRows() == 0) {
    outError( "$ida: Agenda does not exist", "01", $Template );
	exit;
}

// To make the calendar always on the correct category (the category
// of this agenda, in the top menu is used the variable $fid to set
// the correct category)
$row = $res->fetchRow();
$fid = $row['fid'];
$title = $row['title'] . " (" . $row['fdate'] . ")";
$Template->set_var("title",$title);

// get default values from father category
$sql = "SELECT accessPassword
        FROM LEVEL
        WHERE uid='$fid'";
$res = $db->query($sql);
$row = $res->fetchRow();
$globalprotect = $row['accessPassword'];

$sql = "select confidentiality,apassword from AGENDA where id='$ida'";
$res = $db->query($sql);

if (DB::isError($res)) {
	die ($res->getMessage());
}

$row = $res->fetchRow();
$confidentiality = $row['confidentiality'];
$apassword       = $row['apassword'];

// ACCESS WITH A PASSWORD
if ( $confidentiality == "password" || $globalprotect != "") {
	if ( $PHP_AUTH_PW == "" ) {
		Header( "WWW-Authenticate: Basic realm=\"agenda\"" );
		Header( "HTTP/1.0 401 Unauthorized" );
		echo "Access denied.\n";
		exit;
	}
	else if ( $PHP_AUTH_PW != $apassword && $PHP_AUTH_PW != $globalprotect) {
		Header( "WWW-Authenticate: Basic realm=\"agenda\"" );
		Header( "HTTP/1.0 401 Unauthorized" );
		echo "Access denied.\n";
		exit;
	}
}

// Restricted access
if ($confidentiality == "cern-only") {
	$allowed = false;
	// Now checks using an array from config/config.php
	for ( $indX = 0; $indX < count( $restrictAddress ); $indX++ ) {
		if ( preg_match( "/{$restrictAddress[$indX]}/", $REMOTE_ADDR ) ) {
            $allowed = true;
            continue;
        }
	}
	if ( !$allowed ) {
		Header("WWW-Authenticate: Basic realm=\"agenda\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "Access restricted to $runningAT users!";
		exit();
	}
}

$host=getenv("REMOTE_HOST");
$date=date( "l dS of F Y h:i:s A" );

$sql = "select title,stylesheet from AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}
$row = $res->fetchRow();

//calculate default stylesheet
if ($stylesheet == "" && $view != "") {
	$stylesheet = $view;
}

if ($stylesheet == "") {
	if ( isset( $row['stylesheet'] )) {
		$stylesheet = $row['stylesheet'];
		if ($stylesheet == "") {
			$stylesheet = $standardViewStylesheet;
		}
	}
	else {
		// $standardViewStylesheet from config files
		$stylesheet = $standardViewStylesheet;
	}
}

//////////////////////////////////////////////////////////////////////////
//        Display header
//////////////////////////////////////////////////////////////////////////
if ($header != "none") {
    // Javascript functions
    $Template->parse( "display_jsmenutools", "JSMenuTools", true );

    // compute some variables for use in the menus
    // stylesheet description
    $stylesheetdesc = $stylesheets[$stylesheet];
    if ($stylesheetdesc == "") {
        $stylesheetdesc = "special";
    }
	// display level indicator
	if ($dl == "session") {
		$leveltext = "sessions&nbsp;only";
	}
	else {
		$leveltext = "full&nbsp;display";
	}
	// day focus indicator
	if ($dd == "" || $dd == "0000-00-00") {
		$focustext = "all&nbsp;days";
	}
	else {
		$focustext = "$dd";
	}
	$makePDFparam = "${AGE_WWW}/fullAgenda.php?ida%3D$ida%26printable=1%26header%3Dnone%26stylesheet%3Dtools/printable";

    // Prepare menu creation
    $topbarStr =
        "<table border=0 cellspacing=1 cellpadding=0 width=\"100%\">
<tr>";
    include 'menus/topbar.php';
    $menuArray = array("MainMenu",
                       "ToolMenu",
                       "ViewMenu",
                       "ModifyMenu",
                       "UserMenu",
                       "GoToMenu",
                       "InfoText",
                       "HelpMenu");
    $topbarStr .= CreateMenuBar($menuArray);
    $topbarStr .= " </tr> </table>\n\n";
    $Template->set_var("display_topmenubar", $topbarStr);

    // Create footer
    $Template->set_var("AGEfooter_shortURL", ($shortAgendaURL==""?"":"$shortAgendaURL$ida - "));
    $Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
    $Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
    $Template->parse("AGEfooter_template", "AGEfooter", true);

    // Create Menus
    $menuText = CreateMenus($menuArray);
    // Create PS Setup
    include ("menus/psmenu.inc");
    $menuText .= createPSMenu();
    // Create PDF Setup
    include ("menus/pdfmenu.inc");
    $menuText .= createPDFMenu();
    // Display Menus
    $Template->set_var("display_menus", $menuText);
}
else {
    $Template->set_var("display_topmenubar", "");
    $Template->set_var("display_menus", "");
    $Template->set_var("display_jsmenutools", "");
    $Template->set_var("AGEfooter_template", "");
}

// If active store the stats about event hits
if ( $EVENTHITSTATSActive ) {
	// Add this hit to the stats
	addHit( $ida );
}

if ($header != "none") {
    $coreadd = "<FORM action=\"\" method=POST>\n";
    $coreadd .= "<INPUT type=hidden name=ida value=\"$ida\">\n";
    $coreadd .= "<INPUT type=hidden name=fid value=\"$fid\">\n";
    $coreadd .= "<INPUT type=hidden name=header value=\"full\">\n";
    $coreadd .= "<INPUT type=hidden name=AN value=\"\">\n";
    $coreadd .= "</FORM>\n";
}

//////////////////////////////////////////////////////////////////////////
// cache job - only the standard output is cached
//////////////////////////////////////////////////////////////////////////
if ($stylesheet == $row['stylesheet'] && $header == "" && $dd == "" && $dl == "" && $PHPCacheACTIVE) {
	include "jpcache.inc";
    // Initialize cache
    jpCacheInit();
    if ($et=cache_all($CACHE_TIME)) {
        // Cache is valid: flush it!
        $core = jpCacheFlush($gzcontent, $size, $crc32);
    }
    else {
        // Check garbagecollection
        jpCacheGC();
        // Create the content
        $core = createCore();
        // Cache it
        jpCacheEnd($core);
    }
}
else {
    $core = createCore();
}

$Template->set_var("display_core", $coreadd . $core);
$Template->pparse("final-page", "mainpage");



function addHit( $eventID )
{
	// Add the check for the current user
	$dateStr = date("D M j G:i:s T Y");
	$db  = & AgeDB::getDB();
	$sql = "INSERT INTO " . HITSTAT . " (eventID,time,remoteAddress,browserType) values ('$eventID','" . date("D M j G:i:s T Y") . "','" . $GLOBALS[ "REMOTE_ADDR" ] . ":" . $GLOBALS[ "REMOTE_PORT" ] . "','" . $GLOBALS[ "HTTP_USER_AGENT" ] . "')";

	$result = $db->query($sql);
	if (DB::isError($result)) {
		die ($result->getMessage());
	}
}

function createCore() {
    global $AGE,$dl,$dd,$ida,$stylesheet,$XTXSL,$AGE_WWW,$support_email,$PHP_AUTH_PW;

    // Initialise page body text
    $htmltext = "";
    $pid = uniqid("");
    $starttime = time();

    if (!is_dir("$AGE/tmp")) {
        mkdir("$AGE/tmp",0700);
    }

    // Set special parameters
    if ($dl == "") { $dl = "talk"; }
    if ($dd == "") { $dd = "0000-00-00"; }

    $param = new parameter;
    $endtime1 = time();

    $log = &AgeLog::getLog();

    // create XML output
    //////////////////////////////////////////////////////
    include 'XMLoutput.php';

    $agenda = new agenda($ida);
    $xml = $agenda->displayXML();
    $fp = fopen("$AGE/tmp/processXML_$pid.xml","w+");
    fwrite($fp,$xml);
    fclose($fp);

    $help = "$AGE/stylesheets/$stylesheet.xsl";

    $htmltext = `$XTXSL $AGE/tmp/processXML_$pid.xml $help 2>> $AGE/tmp/errors_$pid`;

    if ( DEBUGGING ) {
        $log->logDebug( __FILE__, __LINE__, " $XTXSL $AGE/tmp/processXML_$pid.xml $help 2>> $AGE/tmp/errors_$pid " );
    }

    // modify the html output
    //////////////////////////////////////////////////////

    // insert the full image path into this stylesheet (PostScript + PDF creation)
    $htmltext = str_replace("<img src=\"images/","<img src=\"${AGE_WWW}/images/",$htmltext);
    // agenda modification
    $htmltext = str_replace("[modifyagenda]","",$htmltext);
    $htmltext = str_replace("[/modifyagenda]","",$htmltext);
    // session modification
    $htmltext = preg_replace("/\[modifysession ids=\"(s[0-9]*)\"\]/", "", $htmltext);
    $htmltext = str_replace("[/modifysession]","",$htmltext);
    // talk modification
    $htmltext = preg_replace("/\[modifytalk ids=\"(s[0-9]*)\" idt=\"(t[0-9]*)\"\]/", "", $htmltext);
    $htmltext = str_replace("[/modifytalk]","",$htmltext);
    // subtalk modification
    $htmltext = preg_replace("/\[modifysubtalk ids=\"(s[0-9]*)\" idt=\"(t[0-9]*)\"\]/", "", $htmltext);
    $htmltext = str_replace("[/modifysubtalk]","",$htmltext);
    // lecture modification
    $htmltext = preg_replace("/\[modifylecture\]/", "", $htmltext);
    $htmltext = str_replace("[/modifylecture]","",$htmltext);

    $endtime2 = time();
    $time1 = $endtime1 - $starttime;
    $time2 = $endtime2 - $endtime1;

    $errors = join(" ",file("$AGE/tmp/errors_$pid"));

    if (preg_match("/\.xsl/", $errors)) {
        outError( "<b>An error has occured during the XML processing (in the XSL stylesheet):</b><br><i>An email has been sent to the support team, the problem will be corrected as soon as possible</i>", "01", $Template );
        mail ($support_email,"error in agenda $ida display","$errors");
        exit;
    }
    else if (preg_match("/\.xml/", $errors)) {
        outError( "<b>An error has occured during the XML processing (probably in the XML output)</b><br><i>An email has been sent to the support team, the problem will be corrected as soon as possible</i>", "01", $Template );
        mail ($support_email,"error in agenda $ida display","$errors");
        exit;
    }
    else if (filesize("$AGE/tmp/errors_$pid") != 0) {
        outError( "<b>An error has occured during the Agenda display:</b><br>An email has been automatically sent to the support team.", "01", $Template );
        mail ($support_email,"error in agenda $ida display","$errors");
        exit;
    }
    else {
        @unlink("$AGE/tmp/processXML_$pid.xml");
        @unlink("$AGE/tmp/errors_$pid");
    }

    return $htmltext;
}

?>
