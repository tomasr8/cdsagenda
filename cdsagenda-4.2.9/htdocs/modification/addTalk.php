<?php
// $Id: addTalk.php,v 1.1.1.1.4.7 2002/10/29 15:03:35 tbaron Exp $

// addTalk.php --- add a new talk
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// addTalk.php is part of CDS Agenda.
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
require_once( "../platform/template/template.inc" );

$MainWndName = "AgendaWnd".$ida;

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => "addTalk.ihtml", "error" => "error.ihtml" ));

// if the session id is empty, we create a dummy session (if it does not exist)
if ($ids == "") {
	$db = &AgeDB::getDB();
	$sql = "select ids 
            from SESSION 
            where ida='$ida'";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

	if ($numRows != 0) {
		$row = $res->fetchRow();
		$ids = $row['ids'];
	}
	else {
		$db = &AgeDB::getDB();
		$sql = "select nbsession,stime from AGENDA where id='$ida'";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
		$numRows = $res->numRows();
		$row = $res->fetchRow();
		$ids="s" . $row['nbsession'];
		$newNum = (int) $row['nbsession'] + 1;
		$stime = $row['stime'];

		$sql = "update AGENDA set nbsession=$newNum where id='$ida'";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}

		$today=strftime("%Y-%m-%d",time());

		$sql = "insert into SESSION
                values('$ida',
                       '$ids',
                       '',
                       '$today',
                       '$stime',
                       '',
                       '',
                       'session 1',
                       1,
                       '',
                       '',
                       'open',
                       0,
                       '',
                       '',
                       '',
                       '$today',
                       '$today',
                       '')";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
	}
}

$Template->set_var("images","$IMAGES_WWW");

$db = &AgeDB::getDB();
$sql = "select stitle from SESSION where ida='$ida' and ids='$ids'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
$row = $res->fetchRow();
$title = $row['stitle'];

$Template->set_var("addTalk_TITLE","$title");
$Template->set_var("addTalk_runningAT", $runningAT );
if ( $TALKKEYWORDSActive ) {
	$Template->set_var("addTalk_keywordsStart", "" );
	$Template->set_var("addTalk_keywordsEnd", "" );
}
else {
	$Template->set_var("addTalk_keywordsStart", "<!--" );
	$Template->set_var("addTalk_keywordsEnd", "-->" );   
}

if ( $calendarActive ) {
	$Template->set_var("addTalk_reportNoStart", "<!--" );
	$Template->set_var("addTalk_reportNoEnd", "-->" );
}
else {
	$Template->set_var("addTalk_reportNoStart", "" );
	$Template->set_var("addTalk_reportNoEnd", "" );
}	
$Template->set_var("addTalk_IDS","$ids");
$Template->set_var("addTalk_IDA","$ida");
$Template->set_var("addTalk_AN","$AN");
$Template->set_var("addTalk_POSITION","$position");
$Template->set_var("addTalk_STYLESHEET","$stylesheet");

$sql = "SELECT speriod1 FROM SESSION where ida='$ida' and ids='$ids'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ($numRows == 0) {
	echo("Cannot find this session");
	exit();
}
else {
	//date
	$row = $res->fetchRow();
	$sinfo = split("-",$row['speriod1'],5);
	$sday=$sinfo[2];
	if ($sday == 0) { 
		$sday=1; 
	}
	$smonth=$sinfo[1];
	if ($smonth == 0) { 
		$smonth=1; 
	}
	$syear=$sinfo[0];
	if ($syear == 0) { 
		$syear=1995; 
	}
	$startday = " <b>" 
		. date ("l d F Y",mktime(0,0,0,$smonth,$sday,$syear));
	$startday .= "</b>
<INPUT type=hidden name=stdd value=$sday>
<INPUT type=hidden name=stdm value=$smonth>
<INPUT type=hidden name=stdy value=$syear><br>\n";
}

$Template->set_var("addTalk_startday","$startday");

// TEST-ME
$str_help = "";
$str_help4 = "";
$str_help5 = "";
$str_help2 = "";
$str_help3 = "";
$str_rooms = "";

$sql = "SELECT slocation,bld,floor,room,speriod1,stime FROM SESSION where ida='$ida' and ids='$ids'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ( $numRows == 0 ) {
	if ( ERRORLOG ) {
        $log = &AgeLog::getLog();
		$log->logError( __FILE__ . "." . __LINE__, "main", " Cannot find session with ida='$ida' and ids='$ids' " );
	}

	$str_help="Cannot find this session";

	exit;
}
else {
	$row = $res->fetchRow();

	$row['slocation'] = stripslashes($row['slocation']);
	$row['slocation'] = ereg_replace("'", "\'", $row['slocation']);
	$str_help="
       document.forms[0].location.value='" . $row['slocation'] . "';
       document.forms[0].bld.value='" . $row['bld'] . "';
       document.forms[0].floor.value='" . $row['floor'] . "';";

	$room = $row['room'];

	// scans all the rooms (roomsVector from config_rooms.inc)
	for ( $iSes = 0; count( $roomsVector ) >$iSes; $iSes++ ) {
		if ( $room == $roomsVector[ $iSes ] )
			$str_help5.="document.forms[0].confRoom.selectedIndex=" . ($iSes + 1) . ";\n";
		// build the lists of predefined rooms, preselect the same room of the session
		$str_rooms .= "<OPTION " . ( $row['room'] == $roomsVector[ $iSes ] ? "selected" : "" ) .  "> " . $roomsVector[ $iSes ] . "\n";
	}


	$room=split("-", $row['room'] ,3);
	$str_help3="document.forms[0].room.value='$room[2]';\n";

	//computation of starting time and date
	$sql = "SELECT stime,duration,tday FROM TALK where ida='$ida' and ids='$ids' order by tday DESC,stime DESC LIMIT 1";
	$res2 = $db->query($sql);
	if (DB::isError($res2)) {
		die ($res2->getMessage());
	}
	$numRows = $res2->numRows();

	if ($numRows != 0) {
		//time
		$row2 = $res2->fetchRow();
		$stime = "" . $row2['stime'];
		$duration = "" . $row2['duration'];
		// TESTME: as val
		$sql = "SELECT SEC_TO_TIME(TIME_TO_SEC('$stime')+TIME_TO_SEC('$duration')) as val ";
		$res3 = $db->query($sql);
		if (DB::isError($res3)) {
			die ($res3->getMessage());
		}
		$row3 = $res3->fetchRow();
		$newstime = $row3['val'];
		$array = split(":",$newstime,3);
		$minutes = "$array[1]";
		$minutesIndex = (int)($minutes/5);
		$hours = ($array[0] != "" ? ereg_replace("^0","",$array[0]) : "0");

		$str_help4="
           document.forms[0].thstart.selectedIndex=$hours;
           document.forms[0].tmstart.selectedIndex=$minutesIndex;";

        //date
		$tday = "" . $row2['tday'];
		$sinfo = split("-",$tday,5);
		$sday=$sinfo[2];
		if ($sday == 0) { 
			$sday=1; 
		}
		$smonth=$sinfo[1];
		if ($smonth == 0) { 
			$smonth=1; 
		}
		$syear=$sinfo[0];
		if ($syear == 0) { 
			$syear=1995; 
		}

		$str_help5="
           month=$smonth;
           year=$syear;
           document.forms[0].stdd.selectedIndex=$sday-1;
           document.forms[0].stdm.selectedIndex=$smonth-1;
           document.forms[0].stdy.selectedIndex=$syear-1995;";

	}
	else {
		//date
		$sinfo = split("-", $row['speriod1'], 5);
		$sday=$sinfo[2];
		if ($sday == 0) { 
			$sday=1; 
		}
		$smonth=$sinfo[1];
		if ($smonth == 0) { 
			$smonth=1; 
		}
		$syear=$sinfo[0];
		if ($syear == 0) { 
			$syear=1995; 
		}

		$str_help5="
           var month=$smonth;
           var year=$syear;
           document.forms[0].stdd.selectedIndex=$sday-1;
           document.forms[0].stdm.selectedIndex=$smonth-1;
           document.forms[0].stdy.selectedIndex=$syear-1995;";

		//time
		$stime = $row['stime'];
		$array = split(":",$stime,3);
		$minutes = "$array[1]";
		$minutesIndex = (int)($minutes/5);
		$hours = ereg_replace("^0","",$array[0]);

		$str_help4="
           document.forms[0].thstart.selectedIndex=$hours;
           document.forms[0].tmstart.selectedIndex=$minutesIndex;";

	}
}

// from if
$Template->set_var("str_help", $str_help );
$Template->set_var("str_help2","$str_help2");
$Template->set_var( "addTalkRooms", $str_rooms );
$Template->set_var("str_help3","$str_help3");
$Template->set_var( "addTalkRooms", $str_rooms );
// from else
$Template->set_var("str_help","$str_help");
$Template->set_var("str_help4","$str_help4");
$Template->set_var("str_help5","$str_help5");
$Template->set_var("addTalk_target",$MainWndName);
// END TEST-ME

$Template->pparse( "final-page" , "mainpage" );

// cutted HTML
?>