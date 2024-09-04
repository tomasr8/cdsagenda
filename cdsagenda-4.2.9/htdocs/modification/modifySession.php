<?php
// $Id: modifySession.php,v 1.1.1.1.4.5 2002/11/14 16:21:25 tbaron Exp $

// modifySession.php --- modify session area
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// modifySession.php is part of CDS Agenda.
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
require_once("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once('../platform/system/logManager.inc');

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => "modifySession.ihtml", "error" => "error.ihtml" ));
	
$Template->set_var( "images", "$IMAGES_WWW" );

//FIXME: this is a workaround to the authentication
$topicFields = true;

// Set the page depending on permissions
if ( $topicFields )
{
    // Enable topic fields
    $Template->set_var( "modifySession_topicFieldsOnEnd", "" );
    $Template->set_var( "modifySession_topicFieldsOnStart", "" );
    // Disable fixed text for topic fields
    $Template->set_var( "modifySession_topicFieldsOffStart", "<!--" );
    $Template->set_var( "modifySession_topicFieldsOffEnd", "-->" );
}
else
{
    // Disable topic fields
    $Template->set_var( "modifySession_topicFieldsOnEnd", "-->" );
    $Template->set_var( "modifySession_topicFieldsOnStart", "<!--" );
    // Enable fixed text for topic fields
    $Template->set_var( "modifySession_topicFieldsOffStart", "" ); 
    $Template->set_var( "modifySession_topicFieldsOffEnd", "" );
}	    
if ( $otherFields )
{
}
 
$Template->set_var( "modSes_IDA", $ida );
$Template->set_var( "modSes_IDS", $ids );
$Template->set_var( "modSes_AN", $AN );
$Template->set_var( "modSes_POSITION", $position );
$Template->set_var( "modSes_STYLESHEET", $stylesheet );
$Template->set_var( "modifySes_runningAT", $runningAT );
 
if ( $calendarActive )
{
    $Template->set_var( "modSes_statusStart", "<!--" );
    $Template->set_var( "modSes_statusEnd", "-->" );
}
else
{
    $Template->set_var( "modSes_statusStart", "" );
    $Template->set_var( "modSes_statusEnd", "" );
}

$db = &AgeDB::getDB();
$sql = "SELECT stitle,
               slocation,
               schairman,
               scem,
               broadcasturl, 
               scomment,
               stime,
               speriod1,
               eperiod1,
               bld,
               floor,
               room,
               sstatus,
               etime 
        FROM SESSION 
        WHERE ida='$ida' and ids='$ids'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ( $numRows == 0 )
{
    if ( ERRORLOG )
        {
            $log = &AgeLog::getLog();
            $log->logError( __FILE__ . "." . __LINE__, 
                            "main", 
                            " Cannot find session with ida='$ida' and ids='$ids' " );
        }

    $str_ifres="Cannot find this session";
    $str_help="";
    $str_help3="";
    $str_help4="";
    $str_help5="";
    $str_help6="";
    $str_help7="";
    $str_help10="";
    $str_help11="";
    $str_help12="";
    $str_help13="";
    $str_help14="";
    $str_help15="";
    $str_help16="";
    $str_help17="";
    $str_help18="";
    $str_help19="";
    $str_help20="";
    $str_help21="";
    $str_help22="";
    $str_help23="";
    $Template->set_var( "modifySession_title", "" );
    $Template->set_var( "modifySession_schairman", "" );
    $Template->set_var( "modifySession_scem", "" );
    $Template->set_var( "modifySession_location", "" );
    $Template->set_var( "modifySession_bld", "" );
    $Template->set_var( "modifySession_room", "" );
    $Template->set_var( "modifySession_rooms", "" );
    $Template->set_var( "modifySession_floor", "" );
    exit();
}
else
{
    $row = $res->fetchRow();

    $str_ifres="<SCRIPT>";
    $row['stitle']=stripslashes($row['stitle']);
    $row['stitle'] = str_replace("'","\'",$row['stitle']);

    $Template->set_var( "modifySession_title", $row['stitle'] );

    $str_help="
       document.forms[0].session.value='" . $row['stitle'] . "';";
    $row['slocation']=stripslashes($row['slocation']);
    $row['slocation'] = str_replace("'","\'",$row['slocation']);

    $Template->set_var( "modifySession_location", $row['slocation'] );

    $row['schairman']=stripslashes($row['schairman']);
    $row['schairman'] = str_replace("'","\'",$row['schairman']);

    $Template->set_var( "modifySession_schairman", $row['schairman'] );

    $Template->set_var( "modifySession_broadcasturl", $row['broadcasturl'] );
		
    $str_help3="
       document.forms[0].schairman.value='" . $row['schairman'] . "';
       document.forms[0].scem.value='" . $row['scem'] . "';";

    $Template->set_var( "modifySession_scem", $row['scem'] );

    $row['scomment']=stripslashes($row['scomment']);
    $row['scomment']=ereg_replace("\r","",$row['scomment']);
    $row['scomment'] = str_replace("'","\'",$row['scomment']);

    $Template->set_var( "modifySession_scomment", $row['scomment'] );

    $startingtime = split(":",$row['stime'],3);
    $startinghour = ereg_replace("^0","",$startingtime[0]);
    $startingminutes = $startingtime[1];
    $startingminutesIndex = (int)($startingminutes/5);
		
    $Template->set_var( "modifySession_duration", 
                        $startinghour.":".$startingminutes );

    $str_help5="
       document.forms[0].startinghour.selectedIndex=$startinghour;
       document.forms[0].startingminutes.selectedIndex=$startingminutesIndex;";

    $endingtime = split(":",$row['etime'],3);
    $endinghour = ereg_replace("^0","",$endingtime[0]);
    $endingminutes = $endingtime[1];
    $endingminutesIndex = (int)($endingminutes/5);
		
    $str_help5.="
       document.forms[0].endinghour.selectedIndex=$endinghour;
       document.forms[0].endingminutes.selectedIndex=$endingminutesIndex;";
 
    $sinfo = split("-",$row['speriod1'],5);
    $sday=$sinfo[2];
    if ($sday == 0 || $sday == "") { $sday=1; }
    $smonth=$sinfo[1];
    if ($smonth == 0 || $smonth == "") { $smonth=1; }
    $syear=$sinfo[0];
    if ($syear == 0 || $syear == "") { $syear=1995; }

    $str_help6="
       var stmonth=$smonth;
       var styear=$syear;
       document.forms[0].stdd.selectedIndex=$sday-1;
       document.forms[0].stdy.selectedIndex=$syear-1995;";

    $Template->set_var( "modifySession_stdate", date("j F Y", mktime( 0 ,0, 0, $smonth, $sday, $syear )));
		
    $smonth--;

    $str_help7="
       document.forms[0].stdm.selectedIndex=$smonth;";

    $einfo = split("-",$row['eperiod1'],5);
    $eday=$einfo[2];
    if ($eday == 0) { $eday=1; }
    $emonth=$einfo[1];
    if ($emonth == 0) { $emonth=1; }
    $eyear=$einfo[0];
    if ($eyear == 0) { $eyear=1995; }


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


    $Template->set_var( "modifySession_rooms", $str_Rooms );
    $Template->set_var( "modifySession_bld", ( $foundROOM ? "" : $bldvalue ));
    $Template->set_var( "modifySession_room", ( $foundROOM ? "" : $roomvalue ));
    $Template->set_var( "modifySession_floor", ( $foundROOM ? "" : $floorvalue ));


    // cutted echo


    $str_help23="</SCRIPT>";
}

$Template->set_var("str_ifres", $str_ifres );
$Template->set_var("str_help", $str_help );
$Template->set_var("str_help3", $str_help3 );
$Template->set_var("str_help5", $str_help5 );
$Template->set_var("str_help6", $str_help6 );
$Template->set_var("str_help7", $str_help7 );

$Template->set_var("str_help23", $str_help23 );
$Template->pparse( "final-page" , "mainpage" );
?>