<?php
// $Id: adminGroups.php,v 1.1.2.1 2003/03/28 10:27:15 tbaron Exp $

// adminGroups.php --- management of groups
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s):
//
// adminGroups.php is part of CDS Agenda.
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
require_once '../config/config.php';
require_once '../platform/template/template.inc';
require_once '../platform/system/commonfunctions.inc';
require_once '../platform/authentication/sessinit.inc';

$db = &AgeDB::getDB();

if ($request == "cancel") {
    Header("Location: $nextpage?$nextquery");
}

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array( "mainpage" => "adminGroups.ihtml",
                           "error" => "error.ihtml"));
$logtext = "";

if (!isSuperuser($userid)) {
    outError("Action forbidden. You are not administrator", "02", &$Template);
    exit;
}

if ($request == "addgroup") {
    if (!addGroup($name,$description,$special)) {
        $logtext = "This group already exists";
    }
}

if ($request == "delgroup") {
    if (!deleteGroup($name)) {
        $logtext = "This group cannot be deleted";
    }
}

$groups = listAllGroups();
asort($groups);
$listgroups = "";
if (count($groups) == 0) {
    $listgroups = "none";
}
else {
    while (list($id,$name) = each($groups)) {
        $listgroups .= "<a href='' onclick='document.forms[0].request.value=\"delgroup\";document.forms[0].name.value=\"".$name."\";document.forms[0].submit();'><img src=$IMAGES_WWW/smallbin.gif border=0></a>&nbsp;";
        $listgroups .= "<a href='adminGroup.php?groupid=$id&nextpage=".urlencode( $GLOBALS[ "PHP_SELF" ] )."&nextquery=".urlencode( $GLOBALS[ "QUERY_STRING" ] )."'>".$name."</a> <font color=green>(".getGroupType($id).")</font><br>";
    }
}

$Template->set_var("listgroups", $listgroups );
$Template->set_var("images", $IMAGES_WWW );
$Template->set_var("nextpage","$nextpage");
$Template->set_var("nextquery","$nextquery");
$Template->set_var("logtext","$logtext");

$Template->pparse( "final-page", "mainpage" );
?>