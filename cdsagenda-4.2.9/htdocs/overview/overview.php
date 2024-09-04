<?php
// $Id: overview.php,v 1.1.1.1.4.11 2004/07/29 10:06:10 tbaron Exp $

// overview.php --- main framework of the overview display
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// overview.php is part of CDS Agenda.
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
require_once '../AgeLog.php';
require_once "../config/config.php";
require_once '../platform/template/template.inc';

// Functions for dealing with hierarchical structure
include("hierarchy.inc");
	
$log = &AgeLog::getLog();
$db = &AgeDB::getDB();

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => ($printable == 1 ? "overviewprintable.ihtml" : "overview.ihtml"),
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));	

// If no $display specified,  we display all the event headers
// display=1 => only agenda headers
// display=2 => agenda+session
if ($display == "" || ($display == 3 && $period == 2)) { 
    if ($period != 2) {
        $display = 3; 
    }
    else {
        $display = 1;
    }
}

// If no fid specified, we start the display in top category
if ($fid == "") { 
    $fid = 0; 
}

if ($printable != 1) {
    $printable = 0;
}

// determine the current level
$sql = "select level from LEVEL where uid='$fid'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
if ( $numRows != 0 ) {
    $row = $res->fetchRow();
    $thislevel = $row['level'];
}
else {	
    $thislevel == "0";
}

// create the set of levels in which the agendas may be searched
$levelset = array();
$levelset = getChildren($fid);
array_push($levelset,$fid);
$levelsettext = createTextFromArray($levelset);

$Template->parse( "overview_jsmenutools", "JSMenuTools", true );
$Template->set_var("images","$IMAGES_WWW");

// Create the top bar menu
$topMenuText = "<table border=0 cellspacing=1 cellpadding=0 width=\"100%\"><tr><td>";
include ("../menus/topbar.php");
$menuArray = array("MainMenu",
                   "ToolMenu",
                   "UserMenu",
                   "HelpMenu");
$topMenuText .= CreateMenuBar( $menuArray );
$topMenuText .= " </td></tr></table>\n\n";
$Template->set_var("overview_topmenubar", $topMenuText);

// Display the list of categories
$numlevel = 0;
$sql = "select fid,title,level,uid from LEVEL where uid='$fid'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
if ( $numRows != 0 ) {
    $row = $res->fetchRow();
    $numlevel++;
    $text[$numlevel]  = $row['title'];
    $id[$numlevel]    = $row['uid'];
    $topid[$numlevel] = $row['fid'];
    $level[$numlevel] = $row['level'] + 1;
    
    while ($topid[$numlevel] != "0") {
        $sql = "select title,fid,level,uid from LEVEL where uid='$topid[$numlevel]'";
        $res2 = $db->query($sql);
        if (DB::isError($res2)) {
            die ($res2->getMessage());
        }
        $numRows2 = $res2->numRows();
        
        $numlevel++;
        if ( $numRows2 != 0 ) {
            $row2 = $res2->fetchRow();
            $text[$numlevel]  = $row2['title'];
            $topid[$numlevel] = $row2['fid'];
            $id[$numlevel]    = $row2['uid'];
            $level[$numlevel] = $row2['level'] + 1;
        }
        else {
            $topid[$numlevel] = "0";
        }
    } // end while ($topid[$numlevel] != "0")
} // end if ( $numRows != 0 )
else
if ( ERRORLOG ) {
    $log->logError( __FILE__ . "." . __LINE__, "main", " Cannot find LEVEL with uid '$fid' " );
}
$categlistText = "";
for ($i=$numlevel;$i>0;$i--) {
    $categlistText .= "<TD class=results valign=top> <SMALL><B><A HREF=\"../displayLevel.php?fid=$id[$i]&amp;level=$level[$i]\">$text[$i]</A></B></SMALL> </TD><TD valign=top> <SMALL><B>></B></SMALL> </TD>";
}
$Template->set_var("overview_categlist", $categlistText);


if ( $period == "" )  { 
    $period="0"; 
}
$periodText ="
	 <SELECT name=period onChange=\"document.forms[0].submit();\">
	 <OPTION value=0 ".($period==0?"selected":"")."> day
	 <OPTION value=1 ".($period==1?"selected":"")."> week
	 <OPTION value=2 ".($period==2?"selected":"")."> month
	 <OPTION value=3 ".($period==3?"selected":"")."> year
	 </SELECT>\n";
$Template->set_var("overview_periodselectorform", $periodText);



// Display Time Selector Form
$selectorformText = "";
$selectorformText .= "
<INPUT type=hidden name=fid value=$fid>
<INPUT type=hidden name=printable value=0>
<INPUT type=hidden name=selectedday value=\"$selectedday\">\n";
if ($selectedmonth == "") { $selectedmonth = date('m',time()); }
if ($selectedyear == "") { $selectedyear = date('Y',time()); }
if ($selectedday == "") { $selectedday = date('d',time()); }
if ($daystring == "") { $daystring="stdd"; }
if ($monthstring == "") { $monthstring="stdm"; }
if ($yearstring == "") { $yearstring="stdy"; }
$selecteddate = mktime(12,0,0,$selectedmonth,$selectedday,$selectedyear);
$firstdaytime = mktime(12,0,0,$selectedmonth,1,$selectedyear);
$daytime = $firstdaytime;
$selectorformText .= "
<INPUT name=daystring type=hidden value=$daystring>
<INPUT name=monthstring type=hidden value=$monthstring>
<INPUT name=yearstring type=hidden value=$yearstring>\n";


// initialise icon table	
$nbDisplayedIcons = array();

// Overview spreads over one day
if ($period == 0) {
    include ("dayperiod.php");
    $selectorformText .= dayperiod();
    $daytime = mktime(12,0,0,$selectedmonth,$selectedday,$selectedyear);
    $daystring = date('l jS F Y',$daytime);
    include ("daydisplay.php");
    $daydisplay = daydisplay($printable);
}
// Overview spreads over one week
if ($period == 1) {
    include ("weekperiod.php");
    $selectorformText .= weekperiod();
    $weekdate = date('w',$selecteddate);
    if ($weekdate == 0) { $weekdate = 7; }
    $firstday = ($selecteddate - (($weekdate-1)*60*60*24));
    $lastday = ($selecteddate + (7-$weekdate)*60*60*24);
    $firstdaystring = date('l jS F Y',$firstday);
    $lastdaystring = date('l jS F Y',$lastday);
    include ( "weekdisplay.php" );
    $weekdisplay = weekdisplay($printable);
}
// Overview spreads over one month
if ($period == 2) {
    include ("monthperiod.php");
    $selectorformText .= monthperiod();
    $daytime = mktime(12,0,0,$selectedmonth,1,$selectedyear);
    $daystring = date('F Y',$daytime);
    include ("monthdisplay.php");
    $monthdisplay = monthdisplay($printable);
}
// Overview spreads over one year
if ($period == 3) {
    include ("yearperiod.php");
    $selectorformText .= yearperiod();
    $daytime = mktime(12,0,0,$selectedmonth,$selectedday,$selectedyear);
    $daystring = date('Y',$daytime);
    include ("yeardisplay.php");
    $yeardisplay = yeardisplay($printable);
}
$Template->set_var("overview_timeselectorform", $selectorformText);



// Display the key table
$keyText = "";
if ($period == 0 || $period == 1) {
    $keyText .= "
		<TR><TD class=headerselected><SMALL>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</SMALL></TD><TD><SMALL> meeting</SMALL></TD></TR>
		<TR><TD class=header><SMALL>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</SMALL></TD><TD><SMALL> session</SMALL></TD></TR>
		<TR><TD BGCOLOR=\"#FFcccc\"><SMALL>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</SMALL></TD><TD><SMALL> break</SMALL></TD></TR>
\n";
}
//retrieve the list of all available icons
reset($nbDisplayedIcons);
$uidsetarray = array_keys($nbDisplayedIcons);
$uidsettext = createTextFromArray($uidsetarray);
$sql = "
select stitle,
       uid 
from   LEVEL 
where  uid IN $uidsettext
order by stitle";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
for ( $i = 0; $numRows > $i; $i++ ) {
    $row = $res->fetchRow();
    if ($row['stitle'] != "" &&
        is_file("$AGE/$IMAGES/levelicons/".$row['uid'].".gif")) {
        $keyText .= "
		<TR><TD align=center><A HREF=\"\" onclick=\"document.forms[0].fid.value='" . $row['uid'] . "';document.forms[0].submit();return false;\"><IMG SRC=\"$IMAGES_WWW/levelicons/" . $row['uid'] . ".gif\" ALT=\"" . $row['stitle'] . "\" border=0></A></TD><TD><SMALL> <A HREF=\"\" onclick=\"document.forms[0].fid.value='" . $row['uid'] . "';document.forms[0].submit();return false;\">" . $row['stitle'] . "</A></SMALL></TD></TR>\n";
    }
}
$Template->set_var("overview_keytable", $keyText);


// Displays the display level
$displaylevelText = "";
if ($period != 3) {
    $displaylevelText .= "		
		<BR><BR>
		<TABLE border=\"0\" bgcolor=\"silver\" cellspacing=\"3\" cellpadding=\"1\" width=\"100%\">
		<TR><TD><TR><TD><TABLE border=\"0\" bgcolor=\"white\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">
		<TR><TD><SMALL>
		<B><U>Display level:</U></B><BR>
		<TABLE>\n";
    if ($display == 1) {
        $displaylevelText .= "
		<TR><TD align=center><SMALL><SELECT name=display onChange=\"document.forms[0].submit();\"><OPTION value=1 selected>Agendas<OPTION value=2>Sessions" . ($period != 2 ? "<OPTION value=3>Talks" : "") . "</SELECT></SMALL></TD></TR>\n";
    }
    elseif ($display == 2) {
        $displaylevelText .= "
		<TR><TD align=center><SMALL><SELECT name=display onChange=\"document.forms[0].submit();\"><OPTION value=1>Agendas<OPTION selected value=2>Sessions" . ($period != 2 ? "<OPTION value=3>Talks" : "") . "</SELECT></SMALL></TD></TR>\n";
    }
    elseif ($period != 2) {
        $displaylevelText .= "
		<TR><TD align=center><SMALL><SELECT name=display onChange=\"document.forms[0].submit();\"><OPTION value=1>Agendas<OPTION value=2>Sessions<OPTION selected value=3>Talks</SELECT></SMALL></TD></TR>\n";
    }
    $displaylevelText .= "		
		</TABLE></SMALL>
		</TD></TR>
		</TABLE>
		</TD></TR>
		</TABLE>\n";
} // end if ($period == 0 || $period == 1)
$Template->set_var("overview_displaylevel", $displaylevelText);


// Display Main overview cell
$mainText = "";
if ($period == 0) {
    $mainText .= "<B><SMALL class=headline>$daystring</SMALL></B><BR><BR>";
    $mainText .= $daydisplay;
}
if ($period == 1) {
    $mainText .= "<B><SMALL class=headline>$firstdaystring -> $lastdaystring</SMALL></B><BR><BR>";
    $mainText .= $weekdisplay;
}
if ($period == 2) {
    $mainText .= "<B><SMALL class=headline>$daystring</SMALL></B><BR><BR>";
    // monthdisplay implicitly return his output into the variable ${monthdisplay.php}
    $mainText .= $monthdisplay;
}
if ($period == 3) {
    $mainText .= "<B><SMALL class=headline>$daystring</SMALL></B><BR><BR>";
    $mainText .= $yeardisplay;
}
$Template->set_var("overview_maincell", $mainText);


// Display other useful links
$otherlinksText = "<BR><SMALL><SMALL>(<A HREF=\"\" onclick=\"document.forms[0].action='overview.php';document.forms[0].printable.value=1;document.forms[0].submit();return false;\">printable HTML version</A>)</SMALL></SMALL>";
$otherlinksText .= "<BR><SMALL><SMALL>(<A HREF=\"\" onclick=\"window.open('PSparam.php?period=$period&amp;selectedday=$selectedday&amp;selectedmonth=$selectedmonth&amp;selectedyear=$selectedyear&amp;fid=$fid','postscript','scrollbars=yes,menubar=no,width=250,height=300,alwaysRaised=yes');return false;\">PostScript version</A>)</SMALL></SMALL>";
$otherlinksText .= "<BR><SMALL><SMALL>(<A HREF=\"\" onclick=\"window.open('PDFparam.php?period=$period&amp;selectedday=$selectedday&amp;selectedmonth=$selectedmonth&amp;selectedyear=$selectedyear&amp;fid=$fid','pdf','scrollbars=yes,menubar=no,width=250,height=300,alwaysRaised=yes');return false;\">PDF version</A>)</SMALL></SMALL><BR>";
$Template->set_var("overview_otherlinks", $otherlinksText);


// Create Menus
$menuText = CreateMenus( $menuArray );
$Template->set_var("overview_menus", $menuText);


// Images directory
$Template->set_var("overview_images", $IMAGES_WWW);


//Footer
$Template->set_var("AGEfooter_shortURL", "");
$Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
$Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
$Template->parse("AGEfooter_template", 
                 "AGEfooter", 
                 true);


$Template->pparse("final-page", 
                  "mainpage");


// This function retrieves the global visibility level associated with
// the category the agenda belongs to
function getCategVisibility($categid)
{
    global $db;
    $sql = "SELECT visibility
            FROM LEVEL
            WHERE uid='$categid'";
    $res = $db->query($sql);
    $row = $res->fetchRow();
    return $row['visibility'];
}


// This function decides whether the given agenda is visible
// inside this overview
function isVisible ($ida,$visibility,$mycateg)
{
    global $fid,$db;

    if ($visibility == '') {
        $visibility = 999;
    }

    // The possible values of $visibility indicate in which overview
    // level the meeting will appear
    // 0 means the meeting will never appear in an overview screen
    // 1 means the meeting will appear only in the overview of the
    // category it belongs to
    // 999 means the meeting will appear in all overviews
    $currentlevel = $mycateg;
    for ($i=0;$i<$visibility;$i++) {
        if ($currentlevel == "0" || $currentlevel == "$fid") {
            // we reached the top level OR the level of the overview
            // is included in the visibility of this agenda: we
            // display it
            return true;
        }
        $sql = "SELECT uid,fid 
                FROM LEVEL 
                WHERE uid='$currentlevel'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $currentlevel = $row['fid'];
        }
        else {
            // this should never happen...
            return false;
        }
    }

    return false;
}


?>