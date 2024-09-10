<?php
// $Id: modifyAgenda.php,v 1.1.1.1.4.7 2003/03/28 10:22:49 tbaron Exp $

// modifyAgenda.php --- modify agenda data
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// modifyAgenda.php is part of CDS Agenda.
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

require_once '../AgeDB.php';
require_once '../AgeLog.php';
require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once '../platform/system/logManager.inc';

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array("mainpage"=>$templateNamesVec["modifyAgenda.php"][$calendarActive],
                          "error"=>"error.ihtml" ));


$Template->set_var( "images", "$IMAGES_WWW" );

if ( $topicFields )
{
    // Enable topic fields
    $Template->set_var( "modifyAgenda_topicFieldsOnEnd", "" );
    $Template->set_var( "modifyAgenda_topicFieldsOnStart", "" );
    // Disable fixed text for topic fields
    $Template->set_var( "modifyAgenda_topicFieldsOffStart", "<!--" );
    $Template->set_var( "modifyAgenda_topicFieldsOffEnd", "-->" );
}
else
{
    // Disable topic fields
    $Template->set_var( "modifyAgenda_topicFieldsOnEnd", "-->" );
    $Template->set_var( "modifyAgenda_topicFieldsOnStart", "<!--" );
    // Enable fixed text for topic fields
    $Template->set_var( "modifyAgenda_topicFieldsOffStart", "" );
    $Template->set_var( "modifyAgenda_topicFieldsOffEnd", "" );
}
if ( $otherFields )
{
}

$Template->set_var("modAg_IDA","$ida");
$Template->set_var("modAg_POSITION","$position");
$Template->set_var("modAg_STYLESHEET","$stylesheet");

$db = &AgeDB::getDB();

$log = &AgeLog::getLog();

// Originally retrieved fields "bld,floor,room,format,confidentiality,apassword,repno,acomments,title,location,chairman,cem,status,stdate,endate,an,stylesheet"

$sql = "SELECT * FROM AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

$log->logExec( __FILE__, __LINE__, " selected agenda with id='$ida' " );

if ( $numRows == 0 )
{
    if ( ERRORLOG )
        {
            $log->logError( __FILE__ . "." . __LINE__, "main", " Cannot find agenda with id='$ida' " );
        }

    outError( " Cannot Find this Agenda ", "02", $Template );
    exit;

    // cutted echo

    $str_ifres="Cannot find this agenda";
    $str_help="";
    $str_help2="";
    $str_help3="";
    $str_help4="";
    $str_help5="";
    $str_help6="";
    $str_help7="";

    $JSrooms = "";
    $HTMLrooms = "";
    $str_help18bis = "";
    $str_help21="";
    $str_help24="";
    $str_help27="";
    $str_help28="";
    $Template->set_var( "modifyAgenda_roomList", "" );
}
else
{
    $row = $res->fetchRow();
    // Fill calendar fields if active
    if ( $calendarActive )
        {
            $Template->set_var( "modifyAgenda_stillPartial", ( $row['partialEvent'] == "1" ? "checked" : "" ));
            $Template->set_var( "modifyAgenda_hosted", ( $row['hosted'] == "1" ? "checked" : "" ));
            $Template->set_var( "modifyAgenda_hostedFlag", ( $row['hosted'] == "1" ? "Yes" : "No" ));
            $Template->set_var( "modifyAgenda_outside", ( $row['outside'] == "1" ? "checked" : "" ));
            $Template->set_var( "modifyAgenda_outsideFlag", ( $row['outside'] == "1" ? "Yes" : "No" ));
            $Template->set_var( "modifyAgenda_daynote", $row['dayNote'] );
            $Template->set_var( "modifyAgenda_cosponsor", $row['cosponsor'] );
            $Template->set_var( "modifyAgenda_localorganizers", $row['localorganizers'] );
            $Template->set_var( "modifyAgenda_organizers", $row['organizers'] );
            $Template->set_var( "modifyAgenda_directors", $row['directors'] );
            $Template->set_var( "modifyAgenda_collaborations", $row['collaborations'] );
            $Template->set_var( "modifyAgenda_expectedpartecipants", $row['expparts'] );
            $Template->set_var( "modifyAgenda_limitedpartecipations", $row['limitedpartecipations'] );
            $Template->set_var( "modifyAgenda_laboratories", $row['laboratories'] );
            $Template->set_var( "modifyAgenda_secretary", $row['secretary'] );
            $Template->set_var( "modifyAgenda_deadrequest", $row['deadrequest'] );
            $Template->set_var( "modifyAgenda_smr", $row['smr'] );
            $Template->set_var( "modifyAgenda_venues", $row['venues'] );
            $Template->set_var( "modifyAgenda_additionalnotes", $row['additionalNotes'] );
            $Template->set_var( "modifyAgenda_internalOnly", ( $row['internalOnly'] == "1" ? "checked" : "" ));
            $Template->set_var( "modifyAgenda_internalOnlyFlag", ( $row['internalOnlyFlag'] == "1" ? "Yes" : "No" ));
            $Template->set_var( "modifyAgenda_websiteLink", ( $row['website'] == "1" ? $row['websitelink'] : "" ));
            $Template->set_var( "modifyAgenda_cosponsored", ( $row['cosponsored'] == "1" ? "checked" : "" ));
            $Template->set_var( "modifyAgenda_oldStart", "<!--" );
            $Template->set_var( "modifyAgenda_oldStart", "-->" );
            $Template->set_var( "modifyAgenda_newStart", "" );
            $Template->set_var( "modifyAgenda_newEnd", "" );
            $Template->set_var( "modifyAgenda_title", $row['title'] );
            $Template->set_var( "modifyAgenda_topPersonal1title", $row['personal1title'] );
            $Template->set_var( "modifyAgenda_topPersonal1value", $row['personal1value'] );
            $Template->set_var( "modifyAgenda_topPersonal2title", $row['personal2title'] );
            $Template->set_var( "modifyAgenda_topPersonal2value", $row['personal2value'] );
            $Template->set_var( "modifyAgenda_bottomPersonal3title", $row['personal3title'] );
            $Template->set_var( "modifyAgenda_bottomPersonal3value", $row['personal3value'] );
            $Template->set_var( "modifyAgenda_bottomPersonal4title", $row['personal4title'] );
            $Template->set_var( "modifyAgenda_bottomPersonal4value", $row['personal4value'] );
            $Template->set_var( "modifyAgenda_directorsandorganizers", $row['directorsandorganizers'] );
            $Template->set_var("modifyAgenda_reportNoStart", "<!--" );
            $Template->set_var("modifyAgenda_reportNoEnd", "-->" );
        }
    else
        {
            $Template->set_var("modifyAgenda_reportNoStart", "" );
            $Template->set_var("modifyAgenda_reportNoEnd", "" );
        }

    $newstylesheet = $row['stylesheet'];
    if ($newstylesheet == "" )
        $newstylesheet = "standard";

    reset($stylesheets);
    while (list ($key, $val) = each ($stylesheets))
        {
            if ($key == $newstylesheet)
                $str_Stylesheets .= "<OPTION value=\"$key\" selected>$val\n";
            else
                $str_Stylesheets .= "<OPTION value=\"$key\">$val\n";
        }
    $Template->set_var( "modifyAgenda_stylesheetList", $str_Stylesheets );

    // bld/floor/room fields
    ////////////////////////
    $bldvalue = "";
    $floorvalue = "";
    $roomvalue = "";
    $room=split("-",$row['room'],3);
    if (ereg(".*-.*-.*",$row['room']))
        {
            $bldvalue = "$room[0]";
            $floorvalue = "$room[1]";
            $roomvalue = "$room[2]";
        }
    else
        {
            $bldvalue = $row['bld'];
            $floorvalue = $row['floor'];
        }


    // scans all the rooms (roomsVector from config_rooms.inc)
    $str_Rooms = "<OPTION> Select:\n";
    $foundROOM = FALSE;
    for ( $iSes = 0; count( $roomsVector ) > $iSes; $iSes++ )
        if ( $row['room'] == $roomsVector[ $iSes ] )
            {
                $str_Rooms .= "<OPTION selected > " . $roomsVector[ $iSes ] . "\n";
                $foundROOM = TRUE;
            }
        else
            $str_Rooms .= "<OPTION>" . $roomsVector[ $iSes ] . "\n";

    $Template->set_var( "modifyAgenda_roomList", $str_Rooms );
    $Template->set_var( "modifyAgenda_bld", ( $foundROOM ? "" : $bldvalue ));
    $Template->set_var( "modifyAgenda_room", ( $foundROOM ? "" : $roomvalue ));
    $Template->set_var( "modifyAgenda_floor", ( $foundROOM ? "" : $floorvalue ));

    // cutted echo
    $str_help18bis = "document.forms[0].room.value='" . $room[2] . "';\n";
    if ($row['format'] == "timetable")
        {
            $Template->set_var( "modifyAgenda_timetable", "checked" );
            $Template->set_var( "modifyAgenda_olist", "" );
        }
    else
        {
            $Template->set_var( "modifyAgenda_timetable", "" );
            $Template->set_var( "modifyAgenda_olist", "checked" );
        }
    if ($row['confidentiality'] == "open")
        {
            $Template->set_var( "modifyAgenda_confOpen", "checked" );
            $Template->set_var( "modifyAgenda_confCernonly", "" );
            $Template->set_var( "modifyAgenda_confPassword", "" );
        }
    else if ($row['confidentiality'] == "cern-only")
        {
            $Template->set_var( "modifyAgenda_confOpen", "" );
            $Template->set_var( "modifyAgenda_confCernonly", "checked" );
            $Template->set_var( "modifyAgenda_confPassword", "" );
        }
    else if ($row['confidentiality'] == "password")
        {
            $Template->set_var( "modifyAgenda_confOpen", "" );
            $Template->set_var( "modifyAgenda_confCernonly", "" );
            $Template->set_var( "modifyAgenda_confPassword", "checked" );
        }
    else
        {
            $Template->set_var( "modifyAgenda_confOpen", "" );
            $Template->set_var( "modifyAgenda_confCernonly", "" );
            $Template->set_var( "modifyAgenda_confPassword", "" );
        }
    $row['apassword'] = stripslashes( $row['apassword'] );
    $row['apassword'] = ereg_replace("'","\'",$row['apassword'] );

    // cutted echo
    $Template->set_var( "agendaModify_accessPassword", $row['apassword'] );

    // Currently not active the report number with the calendar extensions
    if ( !$calendarActive )
        $str_help21="document.forms[0].repno.value='" . $row['repno'] . "';\n";

    $Template->set_var( "modifyAgenda_acomments", $row['acomments'] );

    $row['title']=stripslashes($row['title']);
    $row['title']=ereg_replace("\r","",$row['title']);
    $row['title']=ereg_replace("\n","",$row['title']);
    $row['title']=ereg_replace("'","\'",$row['title']);

    $Template->set_var( "modifyAgenda_title", $row['title']);

    $row['location']=stripslashes($row['location']);
    $row['location']=ereg_replace("'","\'",$row['location']);
    $Template->set_var( "modifyAgenda_location", ( $row['location'] == "" ? "" : $row['location'] ));

    $row['chairman']=stripslashes($row['chairman']);
    $row['chairman']=ereg_replace("'","\'",$row['chairman']);
    $Template->set_var( "modifyAgenda_chairman", ( $row['chairman'] == "" ? "" : $row['chairman'] ));
    $Template->set_var( "modifyAgenda_cem", ( $row['cem'] == "" ? "" : $row['cem'] ));
    if ($row['status'] == "open")
        {
            $Template->set_var("modifyAgenda_open", "checked" );
            $Template->set_var("modifyAgenda_close", "" );
        }
    else
        {
            $Template->set_var("modifyAgenda_open", "" );
            $Template->set_var("modifyAgenda_close", "checked" );
        }
    $sinfo = split("-",$row['stdate'],5);
    $sday=$sinfo[2];
    if ($sday == 0) { $sday=1; }
    $smonth=$sinfo[1];
    if ($smonth == 0) { $smonth=1; }
    $syear=$sinfo[0];
    if ($syear == 0) { $syear=1995; }
    // Fixed text, used when no topic field permission is on
    $Template->set_var( "modifyAgenda_stdate", date("j F Y", mktime( 0 ,0, 0, $smonth, $sday, $syear )));
    // cutted echo
    $str_help27="var stmonth=$smonth;\n
                         var styear=$syear;\n
                         document.forms[0].stdd.selectedIndex=$sday-1;\n
                         document.forms[0].stdy.selectedIndex=$syear-1995;\n
                         document.forms[0].stdm.selectedIndex=$smonth-1;\n";

    $einfo = split("-",$row['endate'],5);
    $eday=$einfo[2];
    if ($eday == 0) { $eday=1; }
    $emonth=$einfo[1];
    if ($emonth == 0) { $emonth=1; }
    $eyear=$einfo[0];
    if ($eyear == 0) { $eyear=1995; }
    // Fixed text, used when no topic field permission is on
    $Template->set_var( "modifyAgenda_endate", date("j F Y", mktime( 0 ,0, 0, $emonth, $eday, $eyear )));
    $Template->set_var( "modifyAgenda_endy", $eyear );
    $Template->set_var( "modifyAgenda_endm", $emonth );
    $str_help28="
        var enmonth=$emonth;\n
        var enyear=$eyear;\n
        document.forms[0].endd.selectedIndex=$eday-1;\n
        document.forms[0].endy.selectedIndex=$eyear-1995;\n
        document.forms[0].endm.selectedIndex=$emonth-1;\n";
    $row['an']=stripslashes($row['an']);
    $row['an']=ereg_replace("'","\'",$row['an']);
    $Template->set_var("modifyAgenda_AN", $row['an'] );

    $stime = split(":",$row['stime'],3);
    $shour = ereg_replace("^0","",$stime[0]);
    $sminutes = $stime[1];
    $modifyAgenda_JS_startingtime = "
        document.forms[0].startinghour.selectedIndex=$shour;\n
        document.forms[0].startingminutes.selectedIndex=$sminutes/5;\n";
    $etime = split(":",$row['etime'],3);
    $ehour = $etime[0];
    $eminutes = $etime[1];
    $modifyAgenda_JS_endingtime = "
        document.forms[0].endinghour.selectedIndex=$ehour;\n
        document.forms[0].endingminutes.selectedIndex=$eminutes/5;\n";

    // Deal with visibility level
    $visibility = $row['visibility'];
    if ($visibility == '') {
        $visibility = 999;
    }
    $visibilitytext = "<OPTION value=0 ".
        ($visibility == 0 ? "selected" : "").
        ">nowhere";
    $currentcateg = $row['fid'];
    $currentvisibility = 1;
    while ($currentcateg != "0") {
        $sql = "SELECT title,fid
                FROM LEVEL
                WHERE uid='$currentcateg'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $currentcateg = $row['fid'];
            $title = $row['title'];
            $visibilitytext .= "<OPTION value=$currentvisibility ".
                ($visibility == $currentvisibility ? "selected" : "").
                ">$title";
            $currentvisibility++;
        }
        else {
            $currentcateg = "0";
        }
    }
    $visibilitytext .= "<OPTION value=999 ".
        ($visibility == 999 ? "selected" : "").
        ">everywhere";
    $Template->set_var("modifyAgenda_visibility",$visibilitytext);
}
$Template->set_var("str_ifres","$str_ifres");

$Template->set_var("HTMLrooms", $HTMLrooms );
$Template->set_var("str_help18bis","$str_help18bis");
$Template->set_var("str_help21","$str_help21");
$Template->set_var("str_help24","$str_help24");
$Template->set_var("str_help27","$str_help27");
$Template->set_var("str_help28","$str_help28");
$Template->set_var("modifyAgenda_JS_startingtime","$modifyAgenda_JS_startingtime");
$Template->set_var("modifyAgenda_JS_endingtime","$modifyAgenda_JS_endingtime");
// $baseLocationName from config.php
$Template->set_var( "modifyAgenda_baselocation", $OUTSIDE_STRING );
$Template->set_var( "modifyAgenda_runningAT", $runningAT );
$Template->pparse( "final-page" , "mainpage" );
?>
