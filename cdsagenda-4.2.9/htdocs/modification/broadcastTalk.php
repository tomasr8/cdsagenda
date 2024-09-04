<?php
// $Id: broadcastTalk.php,v 1.1.1.1.4.4 2002/10/23 08:56:33 tbaron Exp $

// broadcastTalk.php --- makes a link to a webcast
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// broadcastTalk.php is part of CDS Agenda.
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

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array("error"    => "error.ihtml",
						  "mainpage" => "broadcastTalk.ihtml"));

$Template->set_var( "images", $IMAGES_WWW );

// Retrieve data about this event
// $event = new event( $ida );
// $Template->set_var( "broadcastTalk_title", $event->title );
// // Retrieve data about this event
// $event->retrieveEvent( "$ida$ids" );
// $Template->set_var( "broadcastTalk_stitle", $event->stitle );
// // Retrieve data about this event
// $event->retrieveEvent( "$ida$ids$idt" );
// $Template->set_var( "broadcastTalk_ttitle", $event->ttitle ); 

$Template->set_var("ids",$ids);
$Template->set_var("idt",$idt);
$Template->set_var("ida",$ida);
$Template->set_var("an",$AN);
$Template->set_var("pos",$position);
$Template->set_var("styl",$stylesheet);

$db = &AgeDB::getDB();

$log = &AgeLog::getLog();

$hlp = "";

$sql = "SELECT broadcasturl FROM TALK 
        where ida='$ida' and ids='$ids' and idt='$idt'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ($numRows == 0) {
	if ( ERRORLOG )	{		
		$log->logError( __FILE__ . "." . __LINE__, 
                        "main", 
                        " Cannot find talk with ida='$ida' " );
	}						
	$hlp .= "Cannot find this talk";
	exit();
}
else {
	$row = $res->fetchRow();
	$hlp .= "<SCRIPT>";
	$hlp .= "document.forms[0].broadcasturl.value='" . 
		$row['broadcasturl'] . 
		"';\n";
	$hlp .= "</SCRIPT>";
}

$Template->set_var("hlp",$hlp);

$Template->pparse( "final-page" , "mainpage" );

?>