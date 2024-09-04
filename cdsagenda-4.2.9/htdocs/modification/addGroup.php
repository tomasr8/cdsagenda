<?php
// $Id: addGroup.php,v 1.1.2.1 2003/03/28 10:37:31 tbaron Exp $

// addGroup.php --- add a group in authorization file
//
// Copyright (C) 2003  CDS Team <cds.support@cern.ch>
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
// Code:
//

require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once '../platform/system/logManager.inc';
require_once '../platform/authentication/sessinit.inc';

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array("mainpage" => "addGroup.ihtml",
                          "error"    => "error.ihtml" ));	

$Template->set_var("images","$IMAGES_WWW");
$Template->set_var("addgroup_request","$request");

$groups = listAllGroups();
asort($groups);
if (count($groups) == 0) {
    $body = "No available groups...";
}
else {
    $body .= "<select name=groups multiple size=10>";
    while (list($id,$name) = each($groups)) {
        $body .= "<option value=$id> ".$name;
    }
    $body .= "</select>";
}
$Template->set_var("addgroup_body","$body");

$Template->pparse( "final-page" , "mainpage" );
?>