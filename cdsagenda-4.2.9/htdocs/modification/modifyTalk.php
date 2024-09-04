<?php
// $Id: modifyTalk.php,v 1.1.1.1.4.10 2004/07/29 10:06:08 tbaron Exp $

// modifyTalk.php --- modify talk data
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// modifyTalk.php is part of CDS Agenda.
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

require_once '../AgeDB.php' ;
require_once '../AgeLog.php';
require_once("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once('../platform/system/logManager.inc');

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => "modifyTalk.ihtml", "error" => "error.ihtml" ));

$Template->set_var( "images", "$IMAGES_WWW" );

// FIXME: workaround for authentication
$topicFields = true;

if ( $topicFields )
{
    // Enable topic fields
    $Template->set_var( "modifyTalk_topicFieldsOnEnd", "" );
    $Template->set_var( "modifyTalk_topicFieldsOnStart", "" );
    // Disable fixed text for topic fields
    $Template->set_var( "modifyTalk_topicFieldsOffStart", "<!--" );
    $Template->set_var( "modifyTalk_topicFieldsOffEnd", "-->" );
}
else
{
    // Disable topic fields
    $Template->set_var( "modifyTalk_topicFieldsOnEnd", "-->" );
    $Template->set_var( "modifyTalk_topicFieldsOnStart", "<!--" );
    // Enable fixed text for topic fields
    $Template->set_var( "modifyTalk_topicFieldsOffStart", "" ); 
    $Template->set_var( "modifyTalk_topicFieldsOffEnd", "" );
}	    
if ( $otherFields )
{
}

if ( $TALKKEYWORDSActive )
{
    $Template->set_var("modifyTalk_keywordsStart", "" );
    $Template->set_var("modifyTalk_keywordsEnd", "" );
}
else
{
    $Template->set_var("modifyTalk_keywordsStart", "<!--" );
    $Template->set_var("modifyTalk_keywordsEnd", "-->" );
}
$Template->set_var("modTalk_IDS","$ids");
$Template->set_var("modTalk_IDT","$idt");
$Template->set_var("modTalk_IDA","$ida");
$Template->set_var("modTalk_AN","$AN");
$Template->set_var("modTalk_POSITION","$position");
$Template->set_var("modTalk_STYLESHEET","$stylesheet");
$Template->set_var("modifyTalk_runningAT", $runningAT );

$log = &AgeLog::getLog();

$db = &AgeDB::getDB();
$sql = "select * FROM TALK where ida='$ida' and ids='$ids' and idt='$idt'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

// tspeaker,ttitle,category,tcomment,repno,tday,stime,location,bld,floor,room,type,duration,affiliation 
if ( $numRows == 0 )
{
    if ( ERRORLOG )
        $log->logError( __FILE__ . "." . __LINE__, "main", " Cannot find talk with ida='$ida' and ids='$ids' and idt='$idt' " );
    $str_help="Cannot find this talk";
    $Template->set_var( "modifyTalk_bld", "" );
    $Template->set_var( "modifyTalk_floor", "" );
    $Template->set_var( "modifyTalk_room", "" );
    $Template->pparse( "final", "mainpage" );
    exit();
}
else
{
    $row = $res->fetchRow();

    $str_help="<SCRIPT>";
		
    if ( $TALKKEYWORDSActive )
        {
            $row['keywords']=stripslashes($row['keywords']);
            $row['keywords']=str_replace("'","\'",$row['keywords']);
            $row['keywords']=ereg_replace("\r","",$row['keywords']);
            $row['keywords']=ereg_replace("\n","",$row['keywords']);
            $Template->set_var( "modifyTalk_keywords", $row['keywords'] );
        }

    $row['tspeaker']=stripslashes($row['tspeaker']);
    $row['tspeaker']=str_replace("'","\'",$row['tspeaker']);
    $row['tspeaker']=ereg_replace("\r","",$row['tspeaker']);
    $row['tspeaker']=ereg_replace("\n","",$row['tspeaker']);
		
    $Template->set_var( "modifyTalk_speaker", $row['tspeaker'] );

    $row['ttitle']=stripslashes($row['ttitle']);
    $row['ttitle']=str_replace("'","\'",$row['ttitle']);
    $row['ttitle']=ereg_replace("\r","",$row['ttitle']);
    $row['ttitle']=ereg_replace("\n","",$row['ttitle']);
    $Template->set_var( "modifyTalk_title", "document.forms[0].title.value='".$row['ttitle']."';\n" );

    $row['category']=ereg_replace("\r","",$row['category']);
    $row['category']=ereg_replace("\n","",$row['category']);
    $row['category']=stripslashes($row['category']);
    $row['category']=str_replace("'","\'",$row['category']);

    $str_help3="
       document.forms[0].category.value='" . $row['category'] . "';";

    $row['tcomment']=str_replace("\\","\\\\",$row['tcomment']);
    $row['tcomment']=addslashes($row['tcomment']);
    $row['tcomment']=ereg_replace("\r","",$row['tcomment']);
    $row['tcomment']=ereg_replace("\n","\\n",$row['tcomment']);

    $str_help4="
       document.forms[0].tcomment.value='" . $row['tcomment'] . "';";

    $Template->set_var( "modifyTalk_repno", $row['repno'] );
    
    if ( $row['repno'] != "" )
        $str_help5="
       document.forms[0].repno.value='" . ereg_replace("'","\'",$row['repno']) . "';";
    else
        $str_help5="";
    
    $sinfo = split("-",$row['tday'],5);
    $sday=$sinfo[2];
    if ($sday == 0) { $sday=1; }
    $smonth=$sinfo[1];
    if ($smonth == 0) { $smonth=1; }
    $syear=$sinfo[0];
    if ($syear == 0) { $syear=1998; }

    $date = " <b>" 
        . date ("l d F Y",mktime(0,0,0,$smonth,$sday,$syear)) . "</b>";
    $Template->set_var( "modifyTalk_date", $date);

    $startingtime = split(":",$row['stime'],3);
    $startinghour = ereg_replace("^0","",$startingtime[0]);
    $startingminutes = $startingtime[1];
    $startingminutesIndex = (int)($startingminutes/5);
    $str_help7 = "";

    if ( !$topicFields ) {
        $Template->set_var( "modifyTalk_tmstart", $startingminutes );
        $Template->set_var( "modifyTalk_thstart", $startinghour );
    }
    else {
        $str_help7="
       document.forms[0].thstart.selectedIndex=$startinghour;
       document.forms[0].tmstart.selectedIndex=$startingminutesIndex;";
    }

    $row['location']=stripslashes($row['location']);
    $row['location']=str_replace("'","\'",$row['location']);
		
    $Template->set_var( "modifyTalk_location", $row['location'] );

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
    $foundROOM = FALSE;
    if ( $topicFields )
        {
            $str_Rooms = "<OPTION> Select:\n";
            for ( $iSes = 0; count( $roomsVector ) > $iSes; $iSes++ )
                if ( $row['room'] == $roomsVector[ $iSes ] )
                    {
                        $str_Rooms .= "<OPTION selected > " . $roomsVector[ $iSes ] . "\n";
                        $foundROOM = TRUE;
                    }
                else
                    $str_Rooms .= "<OPTION>" . $roomsVector[ $iSes ] . "\n";
        }
    else 
        $str_Rooms = $row['room'];
    $Template->set_var( "modifyTalk_rooms", $str_Rooms );
    $Template->set_var( "modifyTalk_bld", ( $foundROOM ? "" : $row['bld'] ));
    $Template->set_var( "modifyTalk_floor", ( $foundROOM ? "" : $floorvalue ));
    $Template->set_var( "modifyTalk_room", ( $foundROOM ? "" : $roomvalue ));

    if ($row['type'] == "1") {
	$Template->set_var( "ttype_talk", "selected");
	$Template->set_var( "ttype_break", "");
    }
    else {
	$Template->set_var( "ttype_talk", "");
	$Template->set_var( "ttype_break", "selected");
    }
	
    $str_help20 = "";
    if ( !$topicField )
        $Template->set_var( "modifyTalk_type", ( $row['type'] == "1" ? "Talk" : "Break" ));
    else
        {
            $str_help20="
       document.forms[0].room.value='$room[2]';";
        }
    $array=split(":",$row['duration'],3);
    $durationh = "$array[0]";
    $durationm = "$array[1]";
    $durationmIndex = (int)($durationm/5);
    $str_duration = "";
    if ( !$topicFields )
        {
            $Template->set_var( "modifyTalk_durationm", $durationm );
            $Template->set_var( "modifyTalk_durationh", $durationh );
        }
    else
        {
            $str_duration="
       document.forms[0].durationh.selectedIndex=$durationh;
       document.forms[0].durationm.selectedIndex=$durationmIndex;";
        }
    $row['affiliation']=stripslashes($row['affiliation']);
    $row['affiliation']=str_replace("'","\'",$row['affiliation']);
    $row['affiliation']=ereg_replace("\r","",$row['affiliation']);
    $row['affiliation']=ereg_replace("\n","",$row['affiliation']);

    $Template->set_var( "modifyTalk_affiliation", $row['affiliation'] );

    $row['email']=stripslashes($row['email']);
    $row['email']=str_replace("'","\'",$row['email']);
    $row['email']=ereg_replace("\r","",$row['email']);
    $row['email']=ereg_replace("\n","",$row['email']);
		
    $Template->set_var( "modifyTalk_temail", ( $row['email'] != "" ? $row['email'] : "" ));

}
	
if ( $calendarActive )
{
    $Template->set_var("modifyTalk_reportNoStart", "<!--" );
    $Template->set_var("modifyTalk_reportNoEnd", "-->" );
}
else
{
    $Template->set_var("modifyTalk_reportNoStart", "" );
    $Template->set_var("modifyTalk_reportNoEnd", "" );
}	

$Template->set_var("str_help","$str_help");
$Template->set_var("str_help3","$str_help3");
$Template->set_var("str_help4","$str_help4");
$Template->set_var("str_help5","$str_help5");
$Template->set_var("str_help6","$str_help6");
$Template->set_var("str_help7","$str_help7");
$Template->set_var("str_help9","$str_help9");
$Template->set_var("str_help10","$str_help10");
$Template->set_var("str_help11","$str_help11");
$Template->set_var("str_help12","$str_help12");
$Template->set_var("str_help13","$str_help13");
$Template->set_var("str_help14","$str_help14");
$Template->set_var("str_help15","$str_help15");
$Template->set_var("str_help16","$str_help16");
$Template->set_var("str_help17","$str_help17");
$Template->set_var("str_help18","$str_help18");
$Template->set_var("str_help19","$str_help19");
$Template->set_var("str_help20","$str_help20");
$Template->set_var("str_duration","$str_duration");

$Template->pparse( "final-page" , "mainpage" );
?>