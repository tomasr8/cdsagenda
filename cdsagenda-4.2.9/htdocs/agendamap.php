<?php
// $Id: agendamap.php,v 1.1.1.1.4.6 2003/03/28 10:35:19 tbaron Exp $

// agendamap.php --- hierarchical map
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// agendamap.php is part of CDS Agenda.
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

$db = &AgeDB::getDB();

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => "agendamap.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));	

$Template->set_var("map_supportEmail", $support_email);
$Template->set_var("map_runningAT", $runningAT);
$Template->set_var("images", $IMAGES_WWW );
$Template->parse( "map_jsmenutools", "JSMenuTools", true );

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
$Template->set_var("map_topmenubar", $topbarStr);

$core = "
<TABLE cellspacing=1 cellpadding=1 border=0>
<TR>
<TD bgcolor=black> 
<TABLE bgcolor=white width=\"100%\" cellpadding=1 cellspacing=1 border=0>
<TR>
<TD valign=top>";

$db  = &AgeDB::getDB();
$sql = "select uid,title,level from LEVEL where level=1 order by title";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}

$core .= "<UL>\n";

$numRows = $res->numRows();
if ( $numRows != 0 ) {
	for ( $i = 0; $i < $numRows; $i++ ) {
		$row = $res->fetchRow();
		
		$uid   = $row['uid'];
		$title = $row['title'];
		$level = $row['level'] + 1;
 
		if ( strcmp( $uid, $fid ) == 0 ) {
			$core .= "	<LI>
        		<STRONG><A HREF=\"displayLevel.php?fid=" . $uid . "&amp;level=$level\"><font color=red class=header>" . $title . "</font></A></STRONG> <HR> \n";
		}
		else {
			$core .= "	<LI> <STRONG><A HREF=\"displayLevel.php?fid=" . $uid . "&amp;level=$level\">" . $title . "</A></STRONG> <HR> \n";
		}
		displayLevel( $uid );
	}
}
$core .= "
   </UL>
</TD>
</TR>
</TBLE>
</TD></TR></TABLE>
</TD></TR></TABLE>";

// Display core
$Template->set_var("map_core", $core);

// Create Menus
$menuText = CreateMenus($menuArray);
$Template->set_var("map_menus", $menuText);


// Create Footer
$Template->set_var("AGEfooter_shortURL", "");
$Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
$Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
$Template->parse("AGEfooter_template", "AGEfooter", true);


$Template->pparse("final-page", "mainpage");


function displayLevel( $id )
{
	global $fid,$core;

	$db  = &AgeDB::getDB();
	$sql = "select uid,title,level from LEVEL where fid='$id' order by title";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}

	$numRows = $res->numRows();
	if ( $numRows != 0 ) {
		$core .= "	<UL>\n";
		for ( $i = 0; $i < $numRows; $i++ ) {
			$row = $res->fetchRow();

			$level = $row['level'] + 1;
			$uid   = $row['uid'];
			$title = $row['title'];

			if ( $uid == "$fid" ) {
				$core .= ("		<LI><A HREF=\"displayLevel.php?fid=" . $uid . "&amp;level=$level\"><font color=red class=header>" . $title . "</font></A>\n");
			}
			else {
				$core .= ("		<LI><A HREF=\"displayLevel.php?fid=" . $uid . "&amp;level=$level\">" . $title . "</A>\n");
			}
			displayLevel( $uid );
		}
		$core .= "	</UL>\n";
	}
} // end displayLevel

?>