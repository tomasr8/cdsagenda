<?php
// $Id: link.php,v 1.1.1.1.4.9 2003/03/28 10:23:19 tbaron Exp $

// link.php --- make a link to a file
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// link.php is part of CDS Agenda.
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
                           "mainpage"=> "link.ihtml", 
                           "error"=>"error.ihtml" ));

// instantiate the db object if not initialized
$db  = &AgeDB::getDB();

// fill in array structure with list of categories;
$sql = "SELECT categoryID,
               shortName,
               longName,
               preselected
        FROM CATEGORY
        ORDER BY categoryID";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
while ($row = $res->fetchRow()) {
    $FILE_TYPES_ARRAY[$row['shortName']] = $row['longName'];
    if ($row['preselected'] == 1) {
        $FILE_TYPES_ARRAY_PRESELECTED = $row['categoryID'];
    }
}

$Template->set_var( "images", $IMAGES_WWW );

$Template->set_var("link_IDA","$ida");
$Template->set_var("link_IDS","$ids");
$Template->set_var("link_IDT","$idt");
$Template->set_var("link_AN","$AN");
$Template->set_var("link_POSITION","$position");
$Template->set_var("link_STYLESHEET","$stylesheet");
$Template->set_var("linkAgenda_runningAT", $runningAT );
$Template->set_var("from",$from);
$Template->set_var("action",$from);

if ($nbFiles == "") {
    $nbFiles = 1;
}

$nbfilestext = "";
for ($i=1;$i<21;$i++) {
    if ($i == $nbFiles) {
        $nbfilestext .= "<OPTION selected>$i\n";
    }
    else {
        $nbfilestext .= "<OPTION>$i\n";
    }
}

$Template->set_var("nbfiles",$nbfilestext);

$filelist = "";
// Now we display the form fields for each requested file upload
for ($i=1;$i<=$nbFiles;$i++)
{
    if (($i/2) == (int)($i/2))
        $bgcolor = "#cccccc";
    else
        $bgcolor = "#dddddd";
    $filelist .= "<tr bgcolor=$bgcolor><Td valign=top><small>URL to file #$i:<br><INPUT TYPE=text NAME=URL$i VALUE=\"\" SIZE=40></small></td><td><small>Destination name:<br><input type=text name=destname$i size=20></small>";
    $filelist .= "<br><SMAll>Type of material:<br><SELECT name=\"newTypeSelect$i\">";
    reset($FILE_TYPES_ARRAY);
    while (list ($key, $val) = each ($FILE_TYPES_ARRAY)) {
        if ($key == "moreinfo")
            $filelist .= "	<OPTION value='$key' selected> $val\n";
        else
            $filelist .= "	<OPTION value='$key'> $val\n";
    }
    $filelist .= "</SELECT></td>";
    $filelist .= "</tr>";
}

$Template->set_var("link_core",$filelist);

$Template->pparse( "final-page" , "mainpage" );
?>