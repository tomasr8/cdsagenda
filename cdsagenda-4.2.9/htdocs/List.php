<?php
//$Id: List.php,v 1.1.1.1.4.8 2002/11/22 16:07:34 tbaron Exp $

// List.php --- default file
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// List.php is part of CDS Agenda.
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
require_once 'overview/hierarchy.inc';

$db = &AgeDB::getDB();

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => "List.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));	

if ($alertText != "") {
	$alertText = stripslashes($alertText);
}
$Template->set_var("list_alerttext", $alertText );
$Template->set_var("list_supportEmail", $support_email);
$Template->set_var("list_runningAT", $runningAT);
$Template->set_var("images", $IMAGES_WWW );
$Template->parse( "list_jsmenutools", "JSMenuTools", true );


// Prepare menu creation
$topbarStr .= 
"<table border=0 cellspacing=1 cellpadding=0 width=\"100%\">
<tr>";
include 'menus/topbar.php';
$menuArray = array("MainMenu",
				   "ToolMenu",
				   "UserMenu",
				   "HelpMenu");
$topbarStr .= CreateMenuBar($menuArray);
$topbarStr .= " </tr> </table>\n\n";
$Template->set_var("list_topmenubar", $topbarStr);


// Now we create the list of all top categories
$strHelp="";
$sql = " 
    SELECT uid,
           title 
    FROM  LEVEL 
    WHERE fid='0' 
    ORDER BY categorder,title
    ";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}
$numRows = $res->numRows();
for ($i = 0; $i < $numRows; $i++) {
    $row   = $res->fetchRow();
    $levelid   = $row['uid'];
    $title = $row['title'];

    // create the set of levels in which the agendas may be searched
    $levelset = array();
    $levelset = getChildren($levelid);
    array_push($levelset,$levelid);
    $agendaNumber = getAgendaNumber($levelset);

    $strHelp .= "<TR><TD class=headerselected>&nbsp;</TD><TD><small>"
        . "<A HREF=\"displayLevel.php?fid=" 
        . $levelid 
        . "\">" 
        . $title 
        . "</A>&nbsp;<font color=grey>($agendaNumber)</font>&nbsp;<A HREF=\"overview/overview.php?fid=" 
        . $levelid 
        . "\"><IMG SRC=\"images/iconeye.gif\" WIDTH=16 HEIGHT=10 "
        . "ALT=\"Overview of all meetings in this category for today\""
        . "BORDER=0></A>"
    	. "<A HREF=\"monitor.php?categories=" . $levelid . "\">"
    	. "<IMG SRC=\"images/smallcalendar.gif\" WIDTH=16 HEIGHT=12 ALT=\"Calendar with all forthcoming meetings\" BORDER=0></A></small></TD></TR>";
}
$Template->set_var("list_table", $strHelp);


// Create Menus
$menuText = CreateMenus($menuArray);
$Template->set_var("list_menus", $menuText);


// Create Footer
$Template->set_var("AGEfooter_shortURL", "");
$Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
$Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
$Template->parse("AGEfooter_template", "AGEfooter", true);


$Template->pparse("final-page", "mainpage");
?>