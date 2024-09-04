<?php
// $Id: modifyLecture.php,v 1.1.1.1.4.8 2003/03/28 10:21:51 tbaron Exp $

// modifyLecture.php --- modify a simple lecture
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// modifyLecture.php is part of CDS Agenda.
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
require_once("../platform/template/template.inc");
require_once('../platform/system/logManager.inc');

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => "modifyLecture.ihtml", 
                           "error" => "error.ihtml" ));
	
$Template->set_var( "images", "$IMAGES_WWW" );

$Template->set_var( "modLec_IDS", $ids );
$Template->set_var( "modLec_IDT", $idt );
$Template->set_var( "modLec_IDA", $ida );
$Template->set_var( "modLec_AN", $AN );
$Template->set_var( "modLec_POSITION", $position );
$Template->set_var( "modLec_STYLESHEET", $stylesheet );
$Template->set_var( "runningAT", $runningAT );

$db = &AgeDB::getDB();

$sql = "select * FROM AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
	
if ( $numRows != 1 )
{
    if ( ERRORLOG )
		{	
            $log = &AgeLog::getLog();
			$log->logError( __FILE__ . "." . __LINE__, 
                            "main", 
                            " Cannot find talk with ida='$ida' and ids='$ids' and idt='$idt' " );
		}
		
    $str_ifres="Cannot find this talk";
    $str_help="";
    $str_help2="";
    $str_help3="";
    $str_help4="";
    $str_help5="";
    $str_help6="";
    $str_help7="";
    $str_help19="";
    $str_help20="";
    $str_help21="";
    $str_help22="";
    $str_help23="";
    $modifyLecture_JS_confRoom="";
		
    exit();
}
else
{
    $row = $res->fetchRow();
    $str_ifres="<SCRIPT>";

    $row['chairman']=stripslashes($row['chairman']);
    $row['chairman']=str_replace("'","\'",$row['chairman']);
    $row['chairman']=ereg_replace("\r","",$row['chairman']);
    $row['chairman']=ereg_replace("\n","",$row['chairman']);
		
    $str_help="document.forms[0].tspeaker.value='" . $row['chairman'] . "';\n";

    $row['title']=stripslashes($row['title']);
    $row['title']=str_replace("'","\'",$row['title']);
    $row['title']=ereg_replace("\r","",$row['title']);
    $row['title']=ereg_replace("\n","",$row['title']);
		
    $str_help2="document.forms[0].title.value='" . $row['title'] . "';\n";

    $row['acomments']=stripslashes($row['acomments']);
    $row['acomments']=str_replace("'","\'",$row['acomments']);
    $row['acomments']=ereg_replace("\r","",$row['acomments']);
    $row['acomments']=ereg_replace("\n","\\n",$row['acomments']);
		
    $str_help4="document.forms[0].tcomment.value='" . $row['acomments'] . "';\n";

    $sinfo = split("-",$row['stdate'],5);
    $sday=$sinfo[2];
    if ($sday == 0) { $sday=1; }
    $smonth=$sinfo[1];
    if ($smonth == 0) { $smonth=1; }
    $syear=$sinfo[0];
    if ($syear == 0) { $syear=1998; }
    $str_help5="
		 document.forms[0].stdd.selectedIndex=$sday-1;
		 document.forms[0].stdy.selectedIndex=$syear-1995;
		 document.forms[0].stdm.selectedIndex=$smonth-1;";

    $startingtime = split(":",$row['stime'],3);
    $startinghour = $startingtime[0];
    $startingminutes = $startingtime[1];
		
    $modifyLecture_JS_startingtime="
         document.forms[0].thstart.selectedIndex=$startinghour;
		 document.forms[0].tmstart.selectedIndex=".($startingminutes/5).";";

    $endingtime = split(":",$row['etime'],3);
    $endinghour = $endingtime[0];
    $endingminutes = $endingtime[1];
		
    $modifyLecture_JS_endingtime="
         document.forms[0].thend.selectedIndex=$endinghour;
		 document.forms[0].tmend.selectedIndex=".($endingminutes/5).";";


    $row['location']  = stripslashes( $row['location'] );
    $row['location'] = str_replace( "'","\'", $row['location'] );
		
    $str_help7="
         document.forms[0].location.value='" . $row['location'] . "';
		 document.forms[0].bld.value='" . $row['bld'] . "';
		 document.forms[0].floor.value='" . $row['floor'] . "';";
		
    $room=split("-",$row['room'],3);

    // scans all the rooms (roomsVector from config_rooms.inc)
    $str_Rooms = "<OPTION> Select:\n";
    for ( $iSes = 0; count( $roomsVector ) > $iSes; $iSes++ )
        if ( $row['room'] == $roomsVector[ $iSes ] )
            {
                $str_Rooms .= "<OPTION selected > " . $roomsVector[ $iSes ] . "\n";
            }
        else
            $str_Rooms .= "<OPTION>" . $roomsVector[ $iSes ] . "\n";
	  
    $str_help19="
          document.forms[0].room.value='$room[2]';";

    $str_help22="
          document.forms[0].SuE.value='" . $row['cem'] . "';
		  document.forms[0].mpassword.value='" . $row['an'] . "';";
		
    $str_help23="</SCRIPT>";

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
        $sql2 = "SELECT title,fid
                FROM LEVEL
                WHERE uid='$currentcateg'";
        $res2 = $db->query($sql2);
        if (DB::isError($res2)) {
            die ($res2->getMessage());
        }
        if ($row2 = $res2->fetchRow()) {
            $currentcateg = $row2['fid'];
            $title = $row2['title'];
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
    $Template->set_var("modifyLecture_visibility",$visibilitytext);


    if ($row['confidentiality'] == "open")
        {
            $Template->set_var( "confOpen", "checked" );
            $Template->set_var( "confCernonly", "" );
            $Template->set_var( "confPassword", "" );
        }
    else if ($row['confidentiality'] == "cern-only")
        {
            $Template->set_var( "confOpen", "" );
            $Template->set_var( "confCernonly", "checked" );
            $Template->set_var( "confPassword", "" );
        }
    else if ($row['confidentiality'] == "password")
        {
            $Template->set_var( "confOpen", "" );
            $Template->set_var( "confCernonly", "" );
            $Template->set_var( "confPassword", "checked" );
        }
    else
        {
            $Template->set_var( "modifyAgenda_confOpen", "" );
            $Template->set_var( "modifyAgenda_confCernonly", "" );
            $Template->set_var( "modifyAgenda_confPassword", "" );
        }
    $row['apassword'] = stripslashes( $row['apassword'] );
    $row['apassword'] = ereg_replace("'","\'",$row['apassword'] );

    $Template->set_var( "accessPassword", $row['apassword'] );
}

$Template->set_var("str_ifres",$str_ifres);
$Template->set_var("str_help",$str_help);
$Template->set_var("str_help2",$str_help2);
$Template->set_var("str_help3",$str_help3);
$Template->set_var("str_help4",$str_help4);
$Template->set_var("str_help5",$str_help5);
$Template->set_var("modifyLecture_JS_startingtime",$modifyLecture_JS_startingtime);
$Template->set_var("modifyLecture_JS_endingtime",$modifyLecture_JS_endingtime);
$Template->set_var("modifyLecture_listRooms",$str_Rooms);
$Template->set_var("str_help7",$str_help7);

$Template->set_var("str_help19",$str_help19);
$Template->set_var("str_help20",$str_help20);
$Template->set_var("str_help21",$str_help21);
$Template->set_var("str_help22",$str_help22);
$Template->set_var("str_help23",$str_help23);
    
$Template->pparse( "final-page" , "mainpage" );
?>