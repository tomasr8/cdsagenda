<?php
// $Id: info.php,v 1.1.1.1.4.5 2004/07/29 10:06:04 tbaron Exp $

// info.php --- summary of user information
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s):
//
// info.php is part of CDS Agenda.
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

$db = &AgeDB::getDB();

if ($request == "cancel") {
    Header("Location: $nextpage?$nextquery");
}

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array( "mainpage" => "info.ihtml",
                           "error" => "error.ihtml"));
$userid = $_SESSION['userid'];

if (getEmail($userid) == "guest") {
    outError("User not logged... Cannot display information", "02", $Template);
    exit;
}

$meetings = array();
$listmeetings = "";
$userid_email = getEmail($userid);
// search in subtalk
$sql = "SELECT DISTINCT ida
        FROM SUBTALK
        WHERE email='$userid_email'";
$res = $db->query($sql);
while ($row = $res->fetchRow()) {
    array_push($meetings,$row["ida"]);
}
// search in talk
$sql = "SELECT DISTINCT ida
        FROM TALK
        WHERE email='$userid_email'";
$res = $db->query($sql);
while ($row = $res->fetchRow()) {
    array_push($meetings,$row["ida"]);
}
// search in session
$sql = "SELECT DISTINCT ida
        FROM SESSION
        WHERE scem='$userid_email'";
$res = $db->query($sql);
while ($row = $res->fetchRow()) {
    array_push($meetings,$row["ida"]);
}
// search in agenda
$sql = "SELECT DISTINCT id
        FROM AGENDA
        WHERE cem='$userid_email'";
$res = $db->query($sql);
while ($row = $res->fetchRow()) {
    array_push($meetings,$row["id"]);
}
if (count($meetings)) {
    $meetingtext = "(".createListFromArray($meetings).")";
    $sql = "SELECT DISTINCT id,title,stdate
            FROM AGENDA
            WHERE id IN $meetingtext
            ORDER BY AGENDA.stdate";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        $listmeetings .= "<a href='fullAgenda.php?ida=".$row["id"]."'>".$row["title"]."</a> (".$row["stdate"].")<br>";
    }
}
if ($listmeetings == "") {
    $listmeetings = "none";
}




$groups = getMyGroups($userid);
$listgroups = "";
if (count($groups) == 0) {
    $listgroups = "none";
}
else {
    while ($group = current($groups)) {
        $listgroups .= "<a href='infoGroup.php?groupid=$group&nextpage=".urlencode( $GLOBALS[ "PHP_SELF" ] )."&nextquery=".urlencode( $GLOBALS[ "QUERY_STRING" ] )."'>".getGroupName($group)."</a><br>";
        next($groups);
    }
}

$agendas = listAgendasUserCanModify($userid);
$listagendas = "";
if (count($agendas) == 0) {
    $listagendas = "none";
}
else {
    while ($agenda = current($agendas)) {
        $listagendas .= "<a href=\"\" onClick=\"window.open('$AGE_WWW/access.php?ida=".$agenda["id"]."','modification');return false;\">".$agenda["title"]."</a>(".$agenda["stdate"].")<br>";
        next($agendas);
    }
}

$categories = listCategoriesUserCanManage($userid);
$listcategories = "";
if (count($categories) == 0) {
    $listcategories = "none";
}
else {
    while ($category = current($categories)) {
        $listcategories .= "<a href='manager/admin.php?fid=".$category["uid"]."'>".$category["title"]."</a><br>";
        next($categories);
    }
}

if ($changePassword) {
    $changepassword = "<a href=''onclick=\"javascript:window.open('changePassword.php','changePassword','scrollbars=no,menubar=no,width=300,height=350');return false;\"><IMG SRC=\"${IMAGES_WWW}/changepassword.gif\" border=0 width=110 height=15 align=right></A>";
}
else {
    $changepassword = "";
}

$Template->set_var("info_changepassword", $changepassword );
$Template->set_var("info_listgroups", $listgroups );
$Template->set_var("info_listagendas", $listagendas );
$Template->set_var("info_listcategories", $listcategories );
$Template->set_var("info_listmeetings", $listmeetings );
$Template->set_var("images", $IMAGES_WWW );
$Template->set_var("info_nextpage","$nextpage");
$Template->set_var("info_nextquery","$nextquery");
$Template->set_var("info_logtext", getEmail($userid) );

$Template->pparse( "final-page", "mainpage" );
?>
