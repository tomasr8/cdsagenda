<?php
// $Id: eventprotection.php,v 1.1.2.1 2002/11/22 16:01:35 tbaron Exp $

//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// eventprotection.php is part of CDS Agenda.
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

require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once '../AgeDB.php';
$ida = $_REQUEST[ "ida" ];

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file( array( 
                           "mainpage"=> "eventprotection.ihtml", 
                           "error"=>"error.ihtml" ));

// instantiate the db object if not initialized
$db  = &AgeDB::getDB();

$Template->set_var( "images", $IMAGES_WWW );
$Template->set_var("eventprotection_IDA","$ida");
$Template->set_var("eventprotection_IDS","$ids");
$Template->set_var("eventprotection_IDT","$idt");
$Template->set_var("eventprotection_AN","$AN");
$Template->set_var("eventprotection_POSITION","$position");
$Template->set_var("eventprotection_STYLESHEET","$stylesheet");
$Template->set_var("from",$from);
$Template->set_var("action",$from);

$core = "<hr>";

$sql = "SELECT confidentiality,apassword
        FROM AGENDA
        WHERE id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$row = $res->fetchRow();
if ($row && $row['confidentiality'] == "password" && $row['apassword'] != "") {
    $core = "<INPUT TYPE=radio NAME='protectiontype' value='global'> Global agenda protection with access key<br><hr>";
}
else {
    $core = "<INPUT TYPE=radio NAME='protectiontype' value='no'> No Protection<br><hr>";
}

$sql = "SELECT *
        FROM EVENT_PASSWORD
        WHERE eventID='$ida$ids$idt'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
if ($row = $res->fetchRow()) {
    $core .= "<INPUT TYPE=radio NAME='protectiontype' value='password' checked> Protection with a specific access key: <INPUT TYPE=password NAME=eventkey size=15 onFocus='document.forms[0].protectiontype[1].checked=1;' value='".$row['password']."'>";
}
else {
    $core .= "<INPUT TYPE=radio NAME='protectiontype' value='password'> Protection with a specific access key: <INPUT TYPE=password NAME=eventkey size=25 onFocus='document.forms[0].protectiontype[1].checked=1;'>";
}

$Template->set_var("eventprotection_core",$core);

$Template->pparse( "final-page" , "mainpage" );
?>