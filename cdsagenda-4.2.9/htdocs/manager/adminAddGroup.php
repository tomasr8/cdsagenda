<?php
// $Id: adminAddGroup.php,v 1.1.2.1 2003/03/28 10:27:14 tbaron Exp $

// adminAddGroup.php --- create new group
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s):
//
// adminAddGroup.php is part of CDS Agenda.
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
$Template->set_file(array( "mainpage" => "adminAddGroup.ihtml",
                           "error" => "error.ihtml"));

if (!isSuperuser($userid)) {
    outError("Action forbidden. You are not administrator", "02", $Template);
    exit;
}

$Template->set_var("images", $IMAGES_WWW );
$Template->set_var("nextpage","$nextpage");
$Template->set_var("nextquery","$nextquery");

$Template->pparse( "final-page", "mainpage" );
?>