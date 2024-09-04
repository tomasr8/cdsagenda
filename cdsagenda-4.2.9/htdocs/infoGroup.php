<?php
// $Id: infoGroup.php,v 1.1.2.1 2003/03/28 10:36:56 tbaron Exp $

// infoGroup.php --- summary of group information
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s):
//
// infoGroup.php is part of CDS Agenda.
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

require_once 'AgeDB.php';
require_once 'config/config.php';
require_once 'platform/template/template.inc';
require_once 'platform/system/commonfunctions.inc';
require_once( "platform/authentication/sessinit.inc" );

if ($request == "cancel") {
    Header("Location: $nextpage?$nextquery");
}

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array( "mainpage" => "infoGroup.ihtml",
                           "error" => "error.ihtml"));

if (getGroupName($groupid) == "") {
    outError("Unknown group...","01", &$Template );
    exit;
}

$members = listGroupMembers($groupid);
sort($members);
$numusers = count($members);
$listmembers = "";
if (count($members) == 0) {
    $listmembers = "none";
}
else {
    while ($member = current($members)) {
        $listmembers .= $member."<br>";
        next($members);
    }
}

$agendas = listAgendasGroupCanModify($groupid);
$listagendas = "";
if (count($agendas) == 0) {
    $listagendas = "none";
}
else {
    while ($agenda = current($agendas)) {
        $listagendas .= "<a href=\"\" onClick=\"window.open('access.php?ida=".$agenda[0]."','modification');return false;\">".$agenda[3]."</a>(".$agenda[2].")<br>";
        next($agendas);
    }
}

$categories = listCategoriesGroupCanManage($groupid);
$listcategories = "";
if (count($categories) == 0) {
    $listcategories = "none";
}
else {
    while ($category = current($categories)) {
        $listcategories .= "<a href='manager/admin.php?fid=".$category[0]."'>".$category[1]."</a><br>";
        next($categories);
    }
}

$Template->set_var("infogroup_name", getGroupName($groupid) );
$Template->set_var("infogroup_listagendas", $listagendas );
$Template->set_var("infogroup_listmembers", $listmembers );
$Template->set_var("infogroup_listcategories", $listcategories );
$Template->set_var("numusers", $numusers );
$Template->set_var("images", $IMAGES_WWW );
$Template->set_var("infogroup_nextpage","$nextpage");
$Template->set_var("infogroup_nextquery","$nextquery");

$Template->pparse( "final-page", "mainpage" );
?>