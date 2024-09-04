<?php
// $Id: cloneAgenda.php,v 1.2.2.4 2002/08/13 13:07:59 simon Exp $

// cloneAgenda.php --- clone module
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// cloneAgenda.php is part of CDS Agenda.
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
// Allows to clone agendas. See:
//    modification/agendaFactory_clone2.inc
//    template/$runningAT/clon2.ihtml 
//
// The number of clones is limited to avoid overfilling the database.
//
// Code:
//

require_once "../AgeDB.php";
require_once '../AgeLog.php';
require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once '../platform/system/logManager.inc';

// new template
$Template = new Template( $PathTemplate );
// Template set-up
// Changed to new clone capabilities
$Template->set_file(array("mainpage" => "cloneAgenda.ihtml",
                          "error"    => "error.ihtml" ));	

$Template->set_var("images","$IMAGES_WWW");

$Template->set_var("clAg_IDA","$ida");
$Template->set_var("clAg_FID","$fid");
$Template->set_var("clAg_LEVEL","$level");
$Template->set_var("clAg_POSITION","$position");
$Template->set_var("clAg_STYLESHEET","$stylesheet");

$log = &AgeLog::getLog();

$db = &AgeDB::getDB();

$sql = "SELECT stdate,endate,title FROM AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ( $numRows == 0 ) {
	if ( ERRORLOG )
		$log->logError( __FILE__ . "." . __LINE__, 
									 "main", 
									 " Cannot find talk with ida='$ida' " );
	$str_help="Cannot find this agenda: ida=$ida";
}
else {
	$row = $res->fetchRow();

	//$sinfo = split("-",$res[ 0 ]->stdate,5);
	$sinfo = split("-", date("Y-m-d", time()));
	$sday=$sinfo[2];
	if ($sday == 0) { $sday=1; }
	$smonth=$sinfo[1];
	if ($smonth == 0) { $smonth=1; }
	$syear=$sinfo[0];
	if ($syear == 0) { $syear=1995; }
		
	$str_help="var stmonth=$smonth;\n";
	$str_help.="var styear=$syear;\n";
	$str_help.="document.forms[0].stdd.selectedIndex=$sday-1;\n";
	$str_help.="document.forms[0].stdy.selectedIndex=$syear-1995;\n";
	$str_help.="document.forms[0].stdm.selectedIndex=$smonth-1;\n";
		
	$einfo = split("-", $row['endate'], 5);
	$eday = $einfo[2];
	if ($eday == 0) { 
		$eday=1; 
	}
	$emonth = $einfo[1];
	if ($emonth == 0) { 
		$emonth=1; 
	}
	$eyear = $einfo[0];
	if ($eyear == 0) { 
		$eyear=1995; 
	}
		
	$str_help.="var enmonth=$emonth;\n";
	$str_help.="var enyear=$eyear;\n";
	$str_help.="document.forms[0].stdd2.selectedIndex=$sday-1;\n";
	$str_help.="document.forms[0].stdy2.selectedIndex=$syear-1995;\n";
	$str_help.="document.forms[0].stdm2.selectedIndex=$smonth-1;\n";
	$str_help.="document.forms[0].stdd3.selectedIndex=$sday-1;\n";
	$str_help.="document.forms[0].stdy3.selectedIndex=$syear-1995;\n";
	$str_help.="document.forms[0].stdm3.selectedIndex=$smonth-1;\n";
	$str_help.="document.forms[0].indd2.selectedIndex=$sday-1;\n";
	$str_help.="document.forms[0].indy2.selectedIndex=$syear-1995;\n";
	$str_help.="document.forms[0].indm2.selectedIndex=$smonth-1;\n";
	$str_help.="document.forms[0].indd3.selectedIndex=$sday-1;\n";
	$str_help.="document.forms[0].indy3.selectedIndex=$syear-1995;\n";
	$str_help.="document.forms[0].indm3.selectedIndex=$smonth-1;\n";
	$str_help.="</SCRIPT>\n";

	$title = $row['title'];
}
	
$Template->set_var("str_help","$str_help");
$Template->set_var("clAg_TITLE","$title");
if($alertText) {
	$Template->set_var("alertText", "<p><b>$alertText</b></p>");
}
else {
	$Template->set_var("alertText", "");
}

$Template->pparse( "final-page" , "mainpage" );
?>