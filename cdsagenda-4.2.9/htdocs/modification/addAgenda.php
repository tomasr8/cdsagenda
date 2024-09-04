<?php
// $Id: addAgenda.php,v 1.1.1.1.4.9 2004/07/29 10:06:07 tbaron Exp $
// addAgenda.php --- add an agenda
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// addAgenda.php is part of CDS Agenda.
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
require_once "../config/config.php";
require_once( "../platform/template/template.inc" );

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => $templateNamesVec[ "addAgenda.php" ][ $calendarActive ], "error" => "error.ihtml" ));

// Retrieve default category values
$db = &AgeDB::getDB();
$sql = "SELECT visibility, modifyPassword, accessPassword
        FROM LEVEL
        WHERE uid='$fid'";
$res = $db->query($sql);
$row = $res->fetchRow();
$defaultvisibility = $row['visibility'];
$defaultmodifyPassword = $row['modifyPassword'];
$defaultaccessPassword = $row['accessPassword'];

$Template->set_var( "defaultmodifypassword", $defaultmodifyPassword);
$Template->set_var( "defaultaccesspassword", $defaultaccessPassword);

// Deal with default visibility level
if ($defaultvisibility == '') {
    $defaultvisibility = 999;
}
$defaultvisibilitytext = "<OPTION value=0 ".
($defaultvisibility == 0 ? "selected" : "").
">nowhere";
$currentcateg = $fid;
$currentdefaultvisibility = 1;
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
        $defaultvisibilitytext .= "<OPTION value=$currentdefaultvisibility ".
            ($defaultvisibility == $currentdefaultvisibility ? "selected" : "").
            ">$title";
        $currentdefaultvisibility++;
    }
    else {
        $currentcateg = "0";
    }
}
$defaultvisibilitytext .= "<OPTION value=999 ".
($defaultvisibility == 999 ? "selected" : "").
">everywhere";
$Template->set_var("visibility",$defaultvisibilitytext);


$Template->set_var( "images", "$IMAGES_WWW" );

if ( $smr != "" ) {
	$Template->set_var( "addAgenda_smr", " value=\"$smr\"" );
}
else {
	$Template->set_var( "addAgenda_smr", "" );
}
	    
$Template->set_var("addAG_FID",$fid );
$Template->set_var("addAG_LEVEL",$level );
$Template->set_var("addAG_stylesheet",$stylesheet );

$time=time();
$thisday=strftime("%d",$time);
$thismonth=strftime("%m",$time);
$thisyear=strftime("%Y",$time);

$str_help="document.newage.endm.selectedIndex = $thismonth - 1;\n
                   document.newage.stdm.selectedIndex = $thismonth - 1;\n
                   document.newage.endd.selectedIndex = $thisday - 1;\n
                   document.newage.stdd.selectedIndex = $thisday - 1;\n
                   document.newage.endy.selectedIndex = $thisyear - 1995;\n
                   document.newage.stdy.selectedIndex = $thisyear - 1995;\n";

$Template->set_var("str_help", $str_help );

if ( isset( $HTTP_COOKIE_VARS["agendaEmail"] )) {
	$agendaCreatorEmail = $HTTP_COOKIE_VARS["agendaEmail"];
	// cutted print
	$str_help2="document.forms[0].agendaCreatorEmail.value = '$agendaCreatorEmail';document.forms[0].cem.value = '$agendaCreatorEmail';\n";

	$Template->set_var("str_help2", $str_help2 );
}
else {
	$Template->set_var("str_help2", "" );
}

// $baseLocationName from config.php
$Template->set_var( "addAgenda_runningAT", $runningAT );

// roomsVector from config_rooms.inc
$str_rooms = "";

for ( $iSes = 0; count( $roomsVector ) >$iSes; $iSes++ ) {
	// build the lists of predefined rooms
	$str_rooms .= "<OPTION> " . $roomsVector[ $iSes ] . "\n";
}

$Template->set_var( "addAgendaRooms", $str_rooms );

$Template->pparse( "final-page" , "mainpage" );
?>
