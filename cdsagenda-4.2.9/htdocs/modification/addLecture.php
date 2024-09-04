<?php
// $Id: addLecture.php,v 1.1.1.1.4.4 2003/03/28 10:25:08 tbaron Exp $

// addLecture.php --- add a simple lecture
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// addLecture.php is part of CDS Agenda.
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
$Template->set_file(array( "mainpage" => "addLecture.ihtml", "error" => "error.ihtml" ));


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
$Template->set_var( "runningAT", $runningAT );

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


$Template->set_var("images","$IMAGES_WWW");
	
$Template->set_var("addLec_FID","$fid");
$Template->set_var("addLec_LEVEL","$level");
$time=time();
$thisday=strftime("%d",$time); 
$thismonth=strftime("%m",$time); 
$thisyear=strftime("%Y",$time); 
$str_help="document.forms[0].endm.selectedIndex = $thismonth - 1;\n
	document.forms[0].stdm.selectedIndex = $thismonth - 1;\n
	document.forms[0].endd.selectedIndex = $thisday - 1;\n
	document.forms[0].stdd.selectedIndex = $thisday - 1;\n
	document.forms[0].endy.selectedIndex = $thisyear - 1995;\n
	document.forms[0].stdy.selectedIndex = $thisyear - 1995;\n";
	
$Template->set_var("str_help","$str_help");
	
if(isset($HTTP_COOKIE_VARS["SuE"])) {
	$SuE = $HTTP_COOKIE_VARS["SuE"];
	$str_help2="document.forms[0].SuE.value = '$SuE'\n";
	$Template->set_var("str_help2","$str_help2");
}
$str_rooms = "";
// roomsVector from config_rooms.inc
for ( $iSes = 0; count( $roomsVector ) >$iSes; $iSes++ ) {
	// build the lists of predefined rooms
	$str_rooms .= "<OPTION> " . $roomsVector[ $iSes ] . "\n";
}

$Template->set_var( "addLectureRooms", $str_rooms );
     
// $baseLocationName from config.php
$Template->set_var( "addLecture_baselocation", $baseLocationName );

$Template->pparse( "final-page" , "mainpage" );
?>