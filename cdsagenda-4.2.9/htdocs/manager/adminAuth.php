<?php
// $Id: adminAuth.php,v 1.1.2.2 2004/07/29 10:06:05 tbaron Exp $

// adminAuth.php --- change authorization for category
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
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

require_once "../AgeDB.php";
require_once '../AgeLog.php';
require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once '../platform/system/logManager.inc';
require_once '../platform/system/commonfunctions.inc';
require_once '../platform/authentication/sessinit.inc';

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array("mainpage" => "adminAuth.ihtml",
                          "error"    => "error.ihtml" ));

if ($authentication && !canManageCategory($userid,$fid)) {
    outError("You are not allowed to manage this category","10",$Template);
    exit;
}

$Template->set_var("images","$IMAGES_WWW");
$Template->set_var("adminauth_FID","$fid");

$log = &AgeLog::getLog();
$db = &AgeDB::getDB();

if ($request == "deleteusermanage") {
    unsetUserManageCategoryAuthorization($item,$fid);
}
if ($request == "deletegroupmanage") {
    unsetGroupManageCategoryAuthorization($item,$fid);
}
if ($request == "addgroupmanage") {
    $groups = array();
    $groups = split(",",$item);
    while ($group = current($groups)) {
        setGroupManageCategoryAuthorization($group,$fid);
        next($groups);
    }
}
if ($request == "addusermanage") {
    $users = array();
    $users = split(",",$item);
    while ($user = current($users)) {
        setUserManageCategoryAuthorization($user,$fid);
        next($users);
    }
}

if ($request == "deleteusermodify") {
    unsetUserModifyAgendaInCategoryAuthorization($item,$fid);
}
if ($request == "deletegroupmodify") {
    unsetGroupModifyAgendaInCategoryAuthorization($item,$fid);
}
if ($request == "addgroupmodify") {
    $groups = array();
    $groups = split(",",$item);
    while ($group = current($groups)) {
        setGroupModifyAgendaInCategoryAuthorization($group,$fid);
        next($groups);
    }
}
if ($request == "addusermodify") {
    $users = array();
    $users = split(",",$item);
    while ($user = current($users)) {
        setUserModifyAgendaInCategoryAuthorization($user,$fid);
        next($users);
    }
}


$modif = "<ul><li>users&nbsp;<small>[<a href=''onclick=\"javascript:window.open('$AGE_WWW/modification/addUser.php?request=addusermanage','addUser','scrollbars=no,menubar=no,width=300,height=350');return false;\">add</a>]</small><ul><small>";
$authorizedusers = listUsersManageCategoryAuthorized($fid);
if (count($authorizedusers) == 0) {
    $modif .= "-";
}
while ($authorizeduser = current($authorizedusers)) {
    $modif .= "<a href='' onclick='if (confirm(\"Are you sure you want to delete this user from your authorization file?\")){document.forms[0].request.value=\"deleteusermanage\";document.forms[0].item.value=\"$authorizeduser\";document.forms[0].submit();return false;}else{return false;}'>";
    $modif .= "<img src=$IMAGES_WWW/smallbin.gif alt='delete' border=0></a>&nbsp;";
    $modif .= str_replace(" ","&nbsp;",getEmail($authorizeduser))."<br>";
    next($authorizedusers);
}
$modif .= "</small></ul>";
$modif .= "<li>groups&nbsp;<small>[<a href=''onclick=\"javascript:window.open('$AGE_WWW/modification/addGroup.php?request=addgroupmanage','addGroup','scrollbars=no,menubar=no,width=300,height=350');return false;\">add</a>]</small><ul><small>";
$authorizedgroups = listGroupsManageCategoryAuthorized($fid);
if (count($authorizedgroups) == 0) {
    $modif .= "-";
}
while ($authorizedgroup = current($authorizedgroups)) {
    $modif .= "<a href='' onclick='if (confirm(\"Are you sure you want to delete this group from your authorization file?\")){document.forms[0].request.value=\"deletegroupmanage\";document.forms[0].item.value=\"$authorizedgroup\";document.forms[0].submit();return false;}else{return false;}'>";
    $modif .= "<img src=$IMAGES_WWW/smallbin.gif alt='delete' border=0></a>&nbsp;";
    $modif .= str_replace(" ","&nbsp;",getGroupName($authorizedgroup))."<br>";
    next($authorizedgroups);
}
$modif .= "</small></ul></ul>";
$Template->set_var("adminauth_modif","$modif");




$modifagendas = "<ul><li>users&nbsp;<small>[<a href=''onclick=\"javascript:window.open('$AGE_WWW/modification/addUser.php?request=addusermodify','addUser','scrollbars=no,menubar=no,width=300,height=350');return false;\">add</a>]</small><ul><small>";
$authorizedusers = listUsersModifyAgendaInCategoryAuthorized($fid);
if (count($authorizedusers) == 0) {
    $modifagendas .= "-";
}
while ($authorizeduser = current($authorizedusers)) {
    $modifagendas .= "<a href='' onclick='if (confirm(\"Are you sure you want to delete this user from your authorization file?\")){document.forms[0].request.value=\"deleteusermodify\";document.forms[0].item.value=\"$authorizeduser\";document.forms[0].submit();return false;}else{return false;}'>";
    $modifagendas .= "<img src=$IMAGES_WWW/smallbin.gif alt='delete' border=0></a>&nbsp;";
    $modifagendas .= str_replace(" ","&nbsp;",getEmail($authorizeduser))."<br>";
    next($authorizedusers);
}
$modifagendas .= "</small></ul>";
$modifagendas .= "<li>groups&nbsp;<small>[<a href=''onclick=\"javascript:window.open('$AGE_WWW/modification/addGroup.php?request=addgroupmodify','addGroup','scrollbars=no,menubar=no,width=300,height=350');return false;\">add</a>]</small><ul><small>";
$authorizedgroups = listGroupsModifyAgendaInCategoryAuthorized($fid);
if (count($authorizedgroups) == 0) {
    $modifagendas .= "-";
}
while ($authorizedgroup = current($authorizedgroups)) {
    $modifagendas .= "<a href='' onclick='if (confirm(\"Are you sure you want to delete this group from your authorization file?\")){document.forms[0].request.value=\"deletegroupmodify\";document.forms[0].item.value=\"$authorizedgroup\";document.forms[0].submit();return false;}else{return false;}'>";
    $modifagendas .= "<img src=$IMAGES_WWW/smallbin.gif alt='delete' border=0></a>&nbsp;";
    $modifagendas .= str_replace(" ","&nbsp;",getGroupName($authorizedgroup))."<br>";
    next($authorizedgroups);
}
$modifagendas .= "</small></ul></ul>";
$Template->set_var("adminauth_modifagendas","$modifagendas");




if($alertText) {
	$Template->set_var("alertText", "<p><b>$alertText</b></p>");
}
else {
	$Template->set_var("alertText", "");
}

$Template->pparse( "final-page" , "mainpage" );
?>
