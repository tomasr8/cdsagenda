<?php
// $Id: addSubTalk.php,v 1.1.1.1.4.4 2002/10/29 15:03:35 tbaron Exp $

// addSubTalk.php --- add a new subtalk
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// addSubTalk.php is part of CDS Agenda.
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

$MainWndName = "AgendaWnd".$ida;

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => "addSubTalk.ihtml", "error" => "error.ihtml" ));

$Template->set_var("images","$IMAGES_WWW");

$db = &AgeDB::getDB();

$sql = "SELECT tday,ttitle
        FROM TALK
        where ida='$ida' and ids='$ids' and idt='$idt'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
if ($numRows <= 0) {
	die ('Next statement attempts to retrieve a row that does not exist.');
}
$row = $res->fetchRow();
$date = $row['tday'];
list($stdy,$stdm,$stdd) = explode("-",$date);

$title = $row['ttitle'];
$Template->set_var("addSub_TITLE","$title");
$Template->set_var("addSub_POSITION","$position");
$Template->set_var("addSub_STYLESHEET","$stylesheet");
$Template->set_var("addSub_IDS","$ids");
$Template->set_var("addSub_IDT","$idt");
$Template->set_var("addSub_IDA","$ida");
$Template->set_var("addSub_AN","$AN");
	
if ( $calendarActive ) {
	$Template->set_var("addSubTalk_reportNoStart", "<!--" );
	$Template->set_var("addSubTalk_reportNoEnd", "-->" );
}
else {
	$Template->set_var("addSubTalk_reportNoStart", "" );
	$Template->set_var("addSubTalk_reportNoEnd", "" );
}	
$Template->set_var("addSubTalk_target",$MainWndName);

$Template->pparse( "final-page" , "mainpage" );
?>