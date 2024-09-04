<?php
// $Id: adminGroup.php,v 1.1.2.2 2004/07/29 10:06:06 tbaron Exp $

// adminGroup.php --- group management
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): T. Baron
//
// adminGroup.php is part of CDS Agenda.
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

if ($request == "cancel") {
    Header("Location: $nextpage?$nextquery");
}

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array( "mainpage" => "adminGroup.ihtml",
                           "error" => "error.ihtml"));

if (getGroupName($groupid) == "") {
    outError("Unknown group...","01", &$Template );
    exit;
}

if ($request == "addusertogroup") {
    $users = array();
    $users = split(",",$item);
    while ($user = current($users)) {
        addUserToGroup($user,$groupid);
        next($users);
    }
}

$type = getGroupType($groupid);

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
if ($type != "LDAP") {
    $listmembers .= "<br><a href='' onclick=\"javascript:window.open('$AGE_WWW/modification/addUser.php?request=addusertogroup','addUser','scrollbars=no,menubar=no,width=300,height=350');return false;\"><img src=$IMAGES_WWW/adduser.gif border=0 align=right></a>";
}

$agendas = listAgendasGroupCanModify($groupid);
$listagendas = "";
if (count($agendas) == 0) {
    $listagendas = "none";
}
else {
    while ($agenda = current($agendas)) {
        $listagendas .= "<a href=\"\" onClick=\"window.open('$AGE_WWW/access.php?ida=".$agenda[id]."','modification');return false;\">".$agenda[title]."</a>(".$agenda[stdate].")<br>";
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
        $listcategories .= "<a href='$AGE_WWW/manager/admin.php?fid=".$category[0]."'>".$category[1]."</a><br>";
        next($categories);
    }
}

$Template->set_var("name", getGroupName($groupid) );
$Template->set_var("listagendas", $listagendas );
$Template->set_var("listmembers", $listmembers );
$Template->set_var("listcategories", $listcategories );
$Template->set_var("images", $IMAGES_WWW );
$Template->set_var("nextpage","$nextpage");
$Template->set_var("nextquery","$nextquery");
$Template->set_var("type","$type");
$Template->set_var("groupid","$groupid");
$Template->set_var("numusers", $numusers );

$Template->pparse( "final-page", "mainpage" );
?>