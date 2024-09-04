<?php
// $Id: gotomenu.inc,v 1.1.1.1.4.7 2002/11/22 15:57:19 tbaron Exp $

// gotomenu.inc --- Go To menu creation
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// gotomenu.inc is part of CDS Agenda.
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

require_once 'DB.php';
require_once 'AgeDB.php';

function CreateGoToMenu() {
	global $ida, $fid;

	$textMenu = "
<!-- `gotomenu.inc' starts here -->
<!-- $Id: gotomenu.inc,v 1.1.1.1.4.7 2002/11/22 15:57:19 tbaron Exp $ -->
" . CreateMenuHeader( "GoToMenu" );

	//get first agenda in the same category
	$db = &AgeDB::getDB();

	$sql = "
select 	AGE1.id 
from 	AGENDA as AGE1 
	left join AGENDA as AGE2 on AGE2.id='$ida'
where 	AGE2.fid=AGE1.fid
order by 	AGE1.stdate,AGE1.endate
LIMIT 	1";

	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$row = $res->fetchRow();
	$firstid = $row['id'];
		
	//get last agenda in the same category
	$sql = "
select 	AGE1.id 
from 	AGENDA as AGE1 
	left join AGENDA as AGE2 on AGE2.id='$ida'
where	AGE2.fid=AGE1.fid
order by 	AGE1.stdate DESC,AGE1.endate DESC
LIMIT 	1";

	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$row = $res->fetchRow();
	$lastid = $row['id'];

	//get next agenda in the same category
	$sql = "
select 	AGE1.id 
from 	AGENDA as AGE1 
	left join AGENDA as AGE2 on AGE2.id='$ida'
where 	AGE2.fid=AGE1.fid and
	AGE1.stdate >= AGE2.stdate and
	AGE1.id != AGE2.id
order by 	AGE1.stdate,AGE1.endate 
LIMIT 	1";

	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$row = $res->fetchRow();
	$nextid = $row['id'];

	//get previous agenda
	$sql = "
select 	AGE1.id 
from 	AGENDA as AGE1 
	left join AGENDA as AGE2 on AGE2.id='$ida'
where 	AGE2.fid=AGE1.fid and
	AGE1.stdate <= AGE2.stdate and 
	AGE1.id != AGE2.id
order by 	AGE1.stdate DESC, AGE1.endate DESC 
LIMIT 	1";

	$res = $db->query($sql);
	$row = $res->fetchRow();
	$previd = $row['id'];


	if (   ($previd  != "")
		|| ($nextid  != "")
		|| ($firstid != "")
		|| ($lastid  != "")) {

		$textMenu .= "&nbsp;<font size=-1 class=headline><B>IN&nbsp;THIS&nbsp;CATEGORY</B></font>";
		$textMenu .= "<TABLE cellspacing=0 cellpadding=0 border=0 
        width=\"100%\">
	<SCRIPT TYPE=\"text/javascript\" 
        LANGUAGE=\"JavaScript1.2\">\n";

		if ($previd != "") {
			$textMenu .= "
	if (DOM == 1)
	{
		document.write('<TR onmouseover=\"this.style.backgroundColor=\'#006\';document.getElementById(\'m2text1\').style.color=\'white\';\" onmouseout=\"this.style.backgroundColor=\'#9cf\';document.getElementById(\'m2text1\').style.color=\'blue\';\" onclick=\"document.location=\'fullAgenda.php?ida=$previd\';\"><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;</font><IMG SRC=\"images/backward.gif\" ALT=\"\" align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<SPAN ID=\'m2text1\'>Previous&nbsp;agenda</SPAN></font></TD></TR>');
	}
	else
	{
		document.write('<TR><TD>&nbsp;&nbsp;<IMG SRC=\"images/backward.gif\" ALT=\"\" align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<A HREF=\'fullAgenda.php?ida=$previd\'>Previous&nbsp;agenda</A></font></TD></TR>');
	}\n";
		}

		if ($nextid != "") {
			$textMenu .= "
	if (DOM == 1)
	{
		document.write('<TR onmouseover=\"this.style.backgroundColor=\'#006\';document.getElementById(\'m2text2\').style.color=\'white\';\" onmouseout=\"this.style.backgroundColor=\'#9cf\';document.getElementById(\'m2text2\').style.color=\'blue\';\" onclick=\"document.location=\'fullAgenda.php?ida=$nextid\';\"><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;</font><IMG SRC=\"images/forward.gif\" ALT=\"\"  align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<SPAN ID=\'m2text2\'>Next&nbsp;agenda</SPAN></font></TD></TR>');
	}
	else
	{
		document.write('<TR><TD>&nbsp;&nbsp;<IMG SRC=\"images/forward.gif\" ALT=\"\" align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<A HREF=\'fullAgenda.php?ida=$nextid\'>Next&nbsp;agenda</A></font></TD></TR>');
	}\n";
		}

		if ($firstid != "") {
			$textMenu .= "
	if (DOM == 1)
	{
		document.write('<TR onmouseover=\"this.style.backgroundColor=\'#006\';document.getElementById(\'m2text3\').style.color=\'white\';\" onmouseout=\"this.style.backgroundColor=\'#9cf\';document.getElementById(\'m2text3\').style.color=\'blue\';\" onclick=\"document.location=\'fullAgenda.php?ida=$firstid\';\"><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;</font><IMG SRC=\"images/fbackward.gif\" ALT=\"\"  align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<SPAN ID=\'m2text3\'>First&nbsp;agenda</SPAN></font></TD></TR>');
	}
	else
	{
		document.write('<TR><TD>&nbsp;&nbsp;<IMG SRC=\"images/fbackward.gif\" ALT=\"\" align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<A HREF=\'fullAgenda.php?ida=$firstid\'>First&nbsp;agenda</A></font></TD></TR>');
	}\n";
		}

		if ($lastid != "") {
			$textMenu .= "
	if (DOM == 1)
	{
		document.write('<TR onmouseover=\"this.style.backgroundColor=\'#006\';document.getElementById(\'m2text4\').style.color=\'white\';\" onmouseout=\"this.style.backgroundColor=\'#9cf\';document.getElementById(\'m2text4\').style.color=\'blue\';\" onclick=\"document.location=\'fullAgenda.php?ida=$lastid\';\"><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;</font><IMG SRC=\"images/fforward.gif\" ALT=\"\" align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<SPAN ID=\'m2text4\'>Last&nbsp;agenda</SPAN></font></TD></TR>');
	}
	else
	{
		document.write('<TR><TD>&nbsp;&nbsp;<IMG SRC=\"images/fforward.gif\" ALT=\"\" align=\"middle\" width=\"14\" height=\"13\"></TD><TD><font color=\'blue\' size=-1>&nbsp;&nbsp;<A HREF=\'fullAgenda.php?ida=$lastid\'>Last&nbsp;agenda</A></font></TD></TR>');
	}\n";
		}

		$textMenu .= "</SCRIPT></TABLE>\n";
	}

	$sql = "select level from LEVEL where uid='$fid'";
	$res = $db->query($sql); 
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$row = $res->fetchRow();
	$level = $row['level'] + 1;

	$i = $level;
	$id[$i] = $fid;
	$colspan = $level;
	$textMenu .= "
		&nbsp;<font size=-1 class=headline><B>IN&nbsp;THE&nbsp;HIERARCHY</B></font>
           <UL><LI><font color='blue' size=-1><A HREF='List.php'>CDS Agenda Home</A></font>";
	while ($i != 1) {
		$sql = "select title,fid from LEVEL where uid='$id[$i]'";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
	
		$numtext = $i + 4;
		$row = $res->fetchRow();
		$title = $row['title'];

		$title = ( $res->numRows() == 0 ? ""
				   : ereg_replace("[\n\r\t ]+", "&nbsp;", $title));

		$leaf[$i] = "<font color='blue' size=-1><A HREF='displayLevel.php?fid=$id[$i]'>" . $title . "</A></font>";
		$i--;
		$id[$i] = $row['fid']; // other fid global
	}

	for ($i = 2 ; $i <= $level ; $i++) {
		$j = $level-$i+1;
		$numtext = $i + 4;
		$textMenu .= "<UL>";
		$textMenu .= "<LI>$leaf[$i]";
	} 
    
    
	for ($i = 1 ; $i <= $level ; $i++) {
        $textMenu .= "</UL>";
    }

	$textMenu .= CreateMenuFooter() . "
<!-- `gotomenu.inc' ends here -->";

	return $textMenu;
}
?>
