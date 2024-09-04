<?php
// $Id: fileprotection.php,v 1.1.2.1 2003/01/17 14:31:38 tbaron Exp $

//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// fileprotection.php is part of CDS Agenda.
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
                           "mainpage"=> "fileprotection.ihtml", 
                           "error"=>"error.ihtml" ));

// instantiate the db object if not initialized
$db  = &AgeDB::getDB();

$Template->set_var( "images", $IMAGES_WWW );
$Template->set_var("fileprotection_IDA","$ida");
$Template->set_var("fileprotection_IDS","$ids");
$Template->set_var("fileprotection_IDT","$idt");
$Template->set_var("fileprotection_AN","$AN");
$Template->set_var("fileprotection_fileID","$fileID");
$Template->set_var("fileprotection_POSITION","$position");
$Template->set_var("fileprotection_STYLESHEET","$stylesheet");
$Template->set_var("from",$from);
$Template->set_var("action",$from);

$core = "<hr>";

$core = "<INPUT TYPE=radio NAME='protectiontype' value='no'> No Protection<br><hr>";

$sql = "SELECT *
        FROM FILE_PASSWORD
        WHERE fileID='$fileID'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
if ($row = $res->fetchRow()) {
    $core .= "<INPUT TYPE=radio NAME='protectiontype' value='password' checked> Protection with a specific access key: <INPUT TYPE=password NAME=filekey size=15 onFocus='document.forms[0].protectiontype[1].checked=1;' value='".$row['password']."'>";
}
else {
    $core .= "<INPUT TYPE=radio NAME='protectiontype' value='password'> Protection with a specific access key: <INPUT TYPE=password NAME=filekey size=25 onFocus='document.forms[0].protectiontype[1].checked=1;'>";
}

$Template->set_var("fileprotection_core",$core);

$Template->pparse( "final-page" , "mainpage" );
?>