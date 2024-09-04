<?php
//$Id: displayLevel.php,v 1.1.1.1.4.12 2004/07/29 10:06:03 tbaron Exp $

// displayLevel.php --- display a level in the hierarchy
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// displayLevel.php is part of CDS Agenda.
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
require_once 'platform/authentication/sessinit.inc';
require_once 'overview/hierarchy.inc';

$db = &AgeDB::getDB();

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => "displayLevel.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));	

if ($alertText != "") {
	$alertText = stripslashes($alertText);
}
$Template->set_var("displaylevel_alerttext", $alertText );
$Template->set_var("images", $IMAGES_WWW );
$Template->parse( "displaylevel_jsmenutools", "JSMenuTools", true );

// Are there any subcategories?
$sql = "
      SELECT uid,
             title 
      FROM   LEVEL 
      WHERE  fid='$fid' 
      ORDER BY categorder,title";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}
if ($res->numRows() == 0) {
	$deadend = 1;
}


// Prepare menu creation
$topbarStr .=  "
<table border=0 cellspacing=1 cellpadding=0 width=\"100%\">
<tr>";
include ("menus/topbar.php");
$menuArray = array("MainMenu",
				   "ToolMenu",
				   "UserMenu",
				   "GoToMenu",
				   "HelpMenu");
if ($deadend) {
	array_push($menuArray,"AddMenu");
}
$topbarStr .= CreateMenuBar( $menuArray );
$topbarStr .= " </tr> </table>\n\n";
$Template->set_var("displaylevel_topmenubar", $topbarStr);


// Add level title and description
$sql = "SELECT abstract,
               title,
               fid
        FROM LEVEL 
        WHERE uid='$fid'";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}
if ($res->numRows() != 0) {
	$row = $res->fetchRow();
	$abstract = $row['abstract'];
	$title    = $row['title'];
    $topid    = $row['fid'];
    $category = createCategText($fid);
	if ( $abstract != "" ) {
        $description = "<BR><SMALL>" . $abstract . "</SMALL>\n";
	}
    else {
        $description = "";
    }
    
    //Any managers?
    $managers = listUsersManageCategoryAuthorized($fid);
    $managertext = createUsersTextFromArray($managers);
    if ($managertext) {
        $managertext = "<font size=-1>[manager:$managertext]</font>";
    }

    $Template->set_var("displaylevel_category", $category);
    $Template->set_var("displaylevel_manager", $managertext);
    $Template->set_var("displaylevel_description", $description);
}


// Add the top level URL
if ($topid != "0") {
    $Template->set_var("displaylevel_topurl", "${AGE_WWW}/displayLevel.php?fid=$topid");
}
else {
    $Template->set_var("displaylevel_topurl", "${AGE_WWW}/List.php");
}
		


$sql = "SELECT uid,
               title 
        FROM LEVEL 
        WHERE fid='$fid' 
        ORDER BY categorder,title";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}
$numRows = $res->numRows();
if ($numRows != 0) {
	
	//////////////////////////////////////////////////////////
	//                                                       //
	//        IF NOT DEAD-END, DISPLAY SUBCATEGORIES         //
	//                                                       //
	//////////////////////////////////////////////////////////

	$outputStr .=  "Subcategories:</font>";
	$outputStr .=  "</TD></TR></TABLE>";
	$outputStr .=  "<TABLE border=0 align=center>";
	
	$numRows = $res->numRows();
	for ($i = 0; $i < $numRows; $i++) {
		$row = $res->fetchRow();
		$uid   = $row['uid'];
		$title = $row['title'];

    		// create the set of levels in which the agendas may be searched
    		$levelset = array();
    		$levelset = getChildren($uid);
    		array_push($levelset,$uid);
    		$agendaNumber = getAgendaNumber($levelset);

		$outputStr .= "
  <TR>
  <TD class=headerselected>&nbsp;</TD>
  <TD><font size=-1>
    <A HREF=\"${AGE_WWW}/displayLevel.php?fid=" . $uid . "\">" 
			. $title . "
    </A>&nbsp;<font color=grey>($agendaNumber)</font>&nbsp;
    <A HREF=\"overview/overview.php?fid=" . $uid . "\">
    <IMG SRC=\"images/iconeye.gif\" WIDTH=16 HEIGHT=10 ALT=\"Overview of all meetings in this category for today\" BORDER=0></A>
    <A HREF=\"monitor.php?categories=" . $uid . "\">
    <IMG SRC=\"images/smallcalendar.gif\" WIDTH=16 HEIGHT=12 ALT=\"Calendar with all forthcoming meetings\" BORDER=0></A>
        </font></TD>
        </TR>";
	}
	$outputStr .= "</TABLE>\n";
}
else {
	//////////////////////////////////////////////////////////
	//                                                       //
	//        IF DEAD-END, DISPLAY LIST OF AGENDAS           //
	//                                                       //
	////////////////////////////////////////////////////////////

    // get default values from category
    $sql = "SELECT accessPassword
            FROM LEVEL
            WHERE uid='$fid'";
    $res = $db->query($sql);
    $row = $res->fetchRow();
    $globalprotect = $row['accessPassword'];

    $outputStr .= "Available Agendas in this category:</font>";

	$outputStr .= "</TD><TD align=\"center\"><TABLE><TR><TD><img src=images/note.gif width=20 height=20 border=0 alt='Add to my personal scheduler' hspace=0 vspace=0></TD><TD><font size=-1>export to personal scheduler</font></TD><TD class=header width=25><font size=-1>&nbsp;</font></TD><TD><font size=-1>not yet finalized</font></TD><TD class=headerselected width=25><font size=-1>&nbsp;</font></TD><TD><font size=-1>finalized</font></TD></TR></TABLE></TD><TD align=\"right\"><font size=\"-2\">\n";
	$outputStr .= $newAgenda;
	
	$sql = "
select YEAR(stdate) AS year 
from AGENDA 
where fid='$fid' 
order by stdate";

	$res = $db->query($sql);
	$lastyear = "pipo";
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	
	$numRows = $res->numRows();
	if ($numRows != 0) {
		for ($i = 0; $i < $numRows; $i++) {
			$row = $res->fetchRow();
			$year = $row['year'];
			if ($year != $lastyear) {
				$outputStr .= "&nbsp;<a href=\"#" 
					. $year . "\">" 
					. $year . "</a>&nbsp;\n";
			}
			$lastyear = $year;
		}
		
		$outputStr .= "</font></TD></TR></TABLE>\n";
		
		// previously used list of fields:
		// stdate,endate,cd,status,title,confidentiality,id,
		// cem,deadrequest,expparts,secretary,laboratories,venues,
		// limitedpartecipations,localorganizers,organizers,
		// collaborations,hosted,chairman
		$sql = "
select * from AGENDA 
where fid='$fid' 
order by stdate DESC,endate DESC";
		
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
		
		$numRows = $res->numRows();
		if ($numRows == 0) {
			$outputStr .= "No agenda yet";
		}

		$Template->set_var( "report_meeting", "" );

		$outputStr .= "<CENTER><TABLE border=0>";
		$date="pipo";
		$lastmonth="pipo";
		$lastyear="pipo";
		if ($numRows != 0) {
			for ($i = 0; $i < $numRows; $i++) {
				$row = $res->fetchRow();

				$stdate          = $row['stdate'];
				$endate          = $row['endate'];
				$cd              = $row['cd'];
				$smr             = $row['smr'];
				$status          = $row['status'];
				$title           = $row['title'];
				$confidentiality = $row['confidentiality'];
				$id              = $row['id'];

				$sinfo = split("-", $stdate, 5 );
				$smonth = $sinfo[1];
				$syear = $sinfo[0];
				$sday = $sinfo[2];
				$einfo = split("-", $endate, 5 );
				$emonth = $einfo[1];
				$eday = $einfo[2];
				$eyear = $einfo[0];
				$cd = split ("-", $cd, 3);
				$cdate = mktime(0,0,0, $cd[1], $cd[2], $cd[0]);
				$today = time();
				if ( ($today - $cdate) <= 604800 ) {
					$new = "<IMG src=\"images/newleft.gif\" alt=\"new!\">";
				}
				else {
					$new = "";
				}
				if (substr($stdate,0,4) != $lastyear) {
					$outputStr .= "<TR><TD colspan=2><BR><BR><a name=$syear></a><B><font size=-1>$syear</font></b><HR></TD></TR>";
				}
				if ( substr($stdate, 0, 7 ) != $date ) {
					if ( $smonth == "00" ) { $month="Unknown"; }
					if ( $smonth == "01" ) { $month="January"; }
					if ( $smonth == "02" ) { $month="February"; }
					if ( $smonth == "03" ) { $month="March"; }
					if ( $smonth == "04" ) { $month="April"; }
					if ( $smonth == "05" ) { $month="May"; }
					if ( $smonth == "06" ) { $month="June"; }
					if ( $smonth == "07" ) { $month="July"; }
					if ( $smonth == "08" ) { $month="August"; }
					if ( $smonth == "09" ) { $month="September"; }
					if ( $smonth == "10" ) { $month="October"; }
					if ( $smonth == "11" ) { $month="November"; }
					if ( $smonth == "12" ) { $month="December"; }
					$outputStr .= "<TR><TD></TD></TR><TR><TD colspan=3 class=dayheader align=left><font size=-1><B>$month $syear</B></font></TD></TR>";
				}
				$text = "";
				if ( $stdate == $endate ) {
					$text="$sday";
				}
				else if ($syear != "$eyear") {
					$text = "$sday/$smonth/$syear - $eday/$emonth/$eyear";
				}
				else if ($smonth == "$emonth") {
					$text="$sday - $eday";
				}
				else {
					$text = "$sday/$smonth - $eday/$emonth";
				}
				if (( $displayAgendas_SMR) && ( $smr != "" )) {
					$text .= " | smr ".$smr;
				}
                $outputStr .= "<TR><TD><A HREF=makevCal.php?id=$id><IMG src=images/note.gif width=20 height=20 border=0 alt='Add to my personal scheduler' hspace=0 vspace=0></A></TD>";
				if ( $status == "open") {
					$outputStr .= "<TD class=header ALIGN=CENTER><font size=-1><b>&nbsp;$text&nbsp;</b></font></TD>";
				}
				else {
					$outputStr .= "<TD class=headerselected ALIGN=CENTER><font size=-1><b>&nbsp;$text&nbsp;</b></font></TD>";
				}
				$ncateg = ereg_replace(" ","+",$categ);
				$ntype = ereg_replace(" ","+",$type);
				$nbase = ereg_replace(" ","+",$base);
				$atitle=stripslashes( $title );
				if ( $globalprotect != "" || $confidentiality == "password") {
					// Classical output (not report)
					$ctext="(protected)";
				}
				else if ( $confidentiality == "cern-only") {
					$ctext="($runningAT only)";
				}
				else {
					$ctext="";
				}
				$lastmonth=substr($stdate,0,7);
				$lastyear=substr($stdate,0,4);

				$outputStr .= "<TD><font size=-1><A HREF=\"fullAgenda.php?ida=" . $id . "\">$atitle</A> $ctext $new</font size=-1></TD></TR>";
				$date = substr( $stdate, 0, 7 );
			} // end for count( $res )
		}

		$outputStr .= "</TABLE></CENTER><BR>\n";
		// Now the link is in the Agenda Maker menu

	}
}


// output main cell
$Template->set_var("displaylevel_maincell", $outputStr);


// Create Menus
$menuText = CreateMenus($menuArray);
$Template->set_var("displaylevel_menus", $menuText);


// Create Footer
$Template->set_var("AGEfooter_shortURL", ($shortCategURL==""?"":"$shortCategURL$fid - "));
$Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
$Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
$Template->parse("AGEfooter_template", "AGEfooter", true);


$Template->pparse("final-page", "mainpage");

// Recursively retrieve all the fid of a specified uid from table LEVEL
function &retrieveFields( $uid, &$outList, $tag ) {
	$outList .= "$tag'$uid'";
	$tag = ",";
	$db  = &AgeDB::getDB();
	$sql = " select uid from LEVEL where fid='$uid' ";
	$retFid = $db->query($sql);
	if (DB::isError($retFid)) {
		die ($retFid->getMessage());
    }

    $numRows = $retFid->numRows();
	if ($numRows != 0) {
		for ($indF = 0; $indF < $numRows; $indF++) {
			$row = $retFid->getRow();
			retrieveFields($row['uid'], &$outList, $tag );
		}
	}
	return $outList;
}

// from an array passed as parameter, this function returns a
// bracket-enclosed, comma-separated list of all elements in the array
function createUsersTextFromArray($array)
{
    reset($array);
    $text = "";
    while (list($key,$value) = each($array)) {
        $text .= getEmail($value).", ";
    }
    $text = ereg_replace(", $","",$text);
    return $text;
}

function createCategText($uid)
{
    global $db,$fid,$AGE_WWW,$shortCategURL;

    $sql = "SELECT title,
                   fid
            FROM LEVEL 
            WHERE uid='$uid'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    if ($row = $res->fetchRow()) {
        $title    = $row['title'];
        $topid    = $row['fid'];
        $category = ereg_replace("[\n\r ]+","&nbsp;",$title);
        $category = eregi_replace("<br>","&nbsp;",$category);

        // current category
        if ($uid != $fid) {
            $category = ($shortCategURL==""?"":"<a href=$shortCategURL$uid>") . $category . ($shortCategURL==""?"":"</a>");
        }
        else {
            $category = "<B>$category</B>";
        }
        $category = createCategText($topid) . ">" . $category;
        return $category;
    }
    else {
        return "<a href=$AGE_WWW>Home</a>";
    }
}
?>
