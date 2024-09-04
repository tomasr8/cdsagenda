<?php
// $Id: addSession.php,v 1.1.1.1.4.7 2002/10/29 15:03:35 tbaron Exp $

// addSession.php --- add a session
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// addSession.php is part of CDS Agenda.
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

$ida = $_REQUEST[ "ida" ];
$MainWndName = "AgendaWnd".$ida;

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => "addSession.ihtml", "error" => "error.ihtml" ));

/***************************************************************************************************
 * Fill in template page                                                               *************
 ***************************************************************************************************/


$Template->set_var("images","$IMAGES_WWW");

$db = &AgeDB::getDB();
$sql = "select title from AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
if ($numRows <= 0) {
	die ("Following statement will be to retrieve a row that doesn't exist");
}

$row = $res->fetchRow();
$title = $row['title'];
	
$Template->set_var("addSes_TITLE","$title");
$Template->set_var("addSes_domain","");
$Template->set_var("addSes_IDA","$ida");
$Template->set_var("addSes_AN","$AN");
$Template->set_var("addSes_POSITION","$position");
$Template->set_var("addSes_STYLESHEET","$stylesheet");
	
// TEST-ME
$Template->set_var("str_help","");
            
$sql = "SELECT stdate,location,bld,floor,room FROM AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ( $numRows == 0 ) {
	if ( ERRORLOG ) {		
// 		if ( !isset( $GLOBALS[ "log" ] ))
// 			$GLOBALS[ "log" ] = new logManager();
        $log = &AgeLog::getLog();
		$log->logError( __FILE__ . "." . __LINE__, "main", " Cannot find agenda with id='$ida' , lastSelect: '" . $sql . "' " );
	}
		
	// cutted echo

	$str_help="Cannot find this agenda";
		
	$Template->set_var("str_help","$str_help");
		
	exit;
}
else {
	$row = $res->fetchRow();
	// cutted echo
	
	$str_help2="<SCRIPT>";
		
	$Template->set_var("str_help2","$str_help2");

	$sinfo = split("-", $row['stdate'], 5);
	$day=$sinfo[2];
	if ($day == 0) { 
		$day=1; 
	}
	$month=$sinfo[1];
	if ($month == 0) { 
		$month=1; 
	}
	$year=$sinfo[0];
	if ($year == 0) { 
		$year=1995; 
	}
		
	// cutted echo
		
	$str_help3="document.newage.stdd.selectedIndex=$day-1;\n
		document.newage.stdm.selectedIndex=$month-1;\n
		var month = $month;
		var year = $year;
		document.newage.stdy.selectedIndex=$year-1995;\n";
		
	$Template->set_var("str_help3","$str_help3");
		
	$row['location'] = stripslashes($row['location']);
	$row['location'] = ereg_replace("'","\'", $row['location']);
		
	// cutted echo
		
	$str_help4 = "document.newage.slocation.value='" . $row['location'] . "';\n
		document.forms[0].bld.value='" . $row['bld'] . "';\n
		document.forms[0].floor.value='" . $row['floor'] . "';\n";
		
	$Template->set_var("str_help4","$str_help4");
		
	$room = $row['room'];

	// scans all the rooms (roomsVector from config_rooms.inc)
	$str_help5 = "";
	$str_rooms = "";
        
	for ( $iSes = 0; count( $roomsVector ) >$iSes; $iSes++ ) {
        if ( $room == $roomsVector[ $iSes ] )
            $str_help5.="document.forms[0].confRoom.selectedIndex=" . ($iSes + 1) . ";\n";
        // build the lists of predefined rooms
        $str_rooms .= "<OPTION> " . $roomsVector[ $iSes ] . "\n";
    }
        
	$Template->set_var( "addSessionRooms", $str_rooms );
	$Template->set_var( "str_help5", $str_help5 );
	$room = split("-", $row['room'], 3);
	$str_help6 = "document.forms[0].room.value='$room[2]';\n";
	$Template->set_var("str_help6","$str_help6");
}
$Template->set_var("addSession_target",$MainWndName);

// TEST-ME
// $baseLocationName from config.php
$Template->set_var( "addSession_baselocation", $baseLocationName );

$Template->pparse( "final-page" , "mainpage" );
?>