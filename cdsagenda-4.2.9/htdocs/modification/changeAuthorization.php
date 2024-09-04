<?php
// $Id: changeAuthorization.php,v 1.1.2.1 2003/03/28 10:37:32 tbaron Exp $

// changeAuthorization.php --- change authorization
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
require_once '../platform/authentication/sessinit.inc';

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array("mainpage" => "changeAuthorization.ihtml",
                          "error"    => "error.ihtml" ));	

$Template->set_var("images","$IMAGES_WWW");

$Template->set_var("changeauth_IDA","$ida");
$Template->set_var("changeauth_FID","$fid");
$Template->set_var("changeauth_LEVEL","$level");
$Template->set_var("changeauth_POSITION","$position");
$Template->set_var("changeauth_STYLESHEET","$stylesheet");

$log = &AgeLog::getLog();
$db = &AgeDB::getDB();

if ($request == "deleteusermodify") {
    unsetUserModifyAgendaAuthorization($item,$ida);
}
if ($request == "deletegroupmodify") {
    unsetGroupModifyAgendaAuthorization($item,$ida);
}
if ($request == "addgroupmodify") {
    $groups = array();
    $groups = split(",",$item);
    while ($group = current($groups)) {
        setGroupModifyAgendaAuthorization($group,$ida);
        next($groups);
    }
}
if ($request == "addusermodify") {
    $users = array();
    $users = split(",",$item);
    while ($user = current($users)) {
        setUserModifyAgendaAuthorization($user,$ida);
        next($users);
    }
}

$body = "<ul><li>users&nbsp;<small>[<a href=''onclick=\"javascript:window.open('addUser.php?request=addusermodify','addUser','scrollbars=no,menubar=no,width=300,height=350');return false;\">add</a>]</small><ul><small>";
$authorizedusers = listUsersModifyAgendaAuthorized($ida);
if (count($authorizedusers) == 0) {
    $body .= "-";
}
while ($authorizeduser = current($authorizedusers)) {
    $body .= "<a href='' onclick='if (confirm(\"Are you sure you want to delete this user from your authorization file?\")){document.forms[0].request.value=\"deleteusermodify\";document.forms[0].item.value=\"$authorizeduser\";document.forms[0].submit();return false;}else{return false;}'>";
    $body .= "<img src=$IMAGES_WWW/smallbin.gif alt='delete' border=0></a>&nbsp;";
    $body .= str_replace(" ","&nbsp;",getEmail($authorizeduser))."<br>";
    next($authorizedusers);
}
$body .= "</small></ul>";
$body .= "<li>groups&nbsp;<small>[<a href=''onclick=\"javascript:window.open('addGroup.php?request=addgroupmodify','addGroup','scrollbars=no,menubar=no,width=300,height=350');return false;\">add</a>]</small><ul><small>";
$authorizedgroups = listGroupsModifyAgendaAuthorized($ida);
if (count($authorizedgroups) == 0) {
    $body .= "-";
}
while ($authorizedgroup = current($authorizedgroups)) {
    $body .= "<a href='' onclick='if (confirm(\"Are you sure you want to delete this group from your authorization file?\")){document.forms[0].request.value=\"deletegroupmodify\";document.forms[0].item.value=\"$authorizedgroup\";document.forms[0].submit();return false;}else{return false;}'>";
    $body .= "<img src=$IMAGES_WWW/smallbin.gif alt='delete' border=0></a>&nbsp;";
    $body .= str_replace(" ","&nbsp;",getGroupName($authorizedgroup))."<br>";
    next($authorizedgroups);
}
$body .= "</small></ul></ul>";
$Template->set_var("changeauth_modif","$body");


if($alertText) {
	$Template->set_var("alertText", "<p><b>$alertText</b></p>");
}
else {
	$Template->set_var("alertText", "");
}

$Template->pparse( "final-page" , "mainpage" );
?>