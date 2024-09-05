<?php
//$Id: monitor.php,v 1.1.2.1 2004/07/29 10:07:19 tbaron Exp $

// monitor.php --- monitor creation script
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch> - Mostly based on a
// perl script provided by Yves Perrin (EP-SFT)
//
// monitor.php is part of CDS Agenda.
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

# This script produces a dynamic web page showing a calendar starting from
# the current day and expanding for the N coming months as set in the script.
# The calendar is 'populated' according to the meetings found in the
# CERN Document Server (CDS) for the categories declared (by their fid) in the script.
# Identification of the meeting category follows a color coding set in the script.
# This script extracts the meeting info from CDS for the desired categories
# over the desired period. This info consist of an XML description of the
# agendas and result from the execution of an other script written by Thomas Baron.
# The page layout can be customized by adapting the calendar page template.
# Days with multiple meetings have rollovers displaying a menu to list the meetings.
# Code originally developped in the EP-SFT group by Yves Perrin
# The menu package was originally written for the CERN Public web pages

$meetings = array();

# ---------------------- customize this part to your needs ---------------------
if ($nbofmonths == "") {
    $nbofmonths = 6; // including the current month (but not more than
                     // 12!)
}
if ($categories == "") {
    $categories = array( '3l13','3l160','2l64' );
}
elseif (!is_array($categories)) {
    $categories = split(" ",$categories);
}
$colorcodes = array( "FFCC99",
                     "99CC99",
                     "99CCFF",
                     "FF9900",
                     "FFCCCC",
                     "CCFFCC",
                     "FFFFCC",
                     "000000" );
$colormultiple = "FF9999";

// -------------------------------------------------------------------------------

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => "monitor.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));

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
$Template->set_var("categories", implode(" ",$categories));
$Template->set_var("colormultiple",$colormultiple);

CreateMonthNbSelector();
CreateColorCodeList();
CreateCalendar();

// Create Menus
$menuText = CreateMenus($menuArray);
$Template->set_var("list_menus", $menuText);

// Create Footer
$Template->set_var("AGEfooter_shortURL", "");
$Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
$Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
$Template->parse("AGEfooter_template", "AGEfooter", true);

$Template->pparse("final-page", "mainpage");



function getCategoryName($id)
{
    global $db;

    $sql = "SELECT title
            FROM LEVEL
            WHERE uid='$id'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        return $row['title'];
    }
    else {
        return "Multiple";
    }
}

function CreateMonthNbSelector()
{
    global $Template,$nbofmonths;

    $text = "";
    for ($i=1;$i<=12;$i++) {
        $text .= "<OPTION ".($i==$nbofmonths?"selected":"").">$i\n";
    }

    $Template->set_var("nbmonths", $text);
}

function CreateColorCodeList()
{
    global $Template,$categories,$colorcodes;

    // Create color codes line
    $colorcodes_text = "";
    for ($i=0;$i<sizeof($categories);$i++)
        {
            $colorcodes_text .= ColorCodeText($colorcodes[$i],$categories[$i]);
        }
    $Template->set_var("list_colorcodes", $colorcodes_text);
}

function ColorCodeText($color,$categoryID)
{
    global $AGE_WWW;
    $name = getCategoryName($categoryID);
    return "
                <td>
                  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                    <tr>
                      <td width=\"30\" bgcolor=\"#$color\">&nbsp;</td>
                      <td class=\"calcell\">&nbsp;<a href=\"$AGE_WWW/displayLevel.php?fid=$categoryID\">$name</a></td>
                    </tr>
                  </table>
                </td>
                <td width=\"2%\">&nbsp;</td>";
}

function CreateCalendar()
{
    global $Template,$categories,$colorcodes,$nbofmonths,$meetings,$sdate,$edate,$colormultiple,$AGE_WWW;

    list($sec, $min, $hours, $mday, $mon, $year, $wday, $yday, $isdst) = localtime();
    $today = $mday;
    $thismonth = $mon + 1;
    $thisyear = $year + 1900;
    $endmonth = $thismonth + $nbofmonths;
    $endyear = $thisyear;
    if ($endmonth > 12) {
        $endmonth = $endmonth - 12;
        $endyear++;
    }

    $sdate = "01/" . (strlen($thismonth) < 2 ? "0$thismonth" : $thismonth) ."/".$thisyear;
    $edate = "01/" . (strlen($endmonth) < 2 ? "0$endmonth" : $endmonth) ."/".$endyear;

    for ($i=0;$i<sizeof($categories);$i++) {
        ReadAgendaCategory($categories[$i]);
    }

    $sec = 0;
    $min = 0;
    $hours = 0;
    $mday = 1;
    $curmonthtime = mktime($hours,$min,$sec,$mon+1,$mday,$year);
    list($sec, $min, $hours, $mday, $mon, $year, $wday, $yday, $isdst) = localtime($curmonthtime);
    // change reference for week days numbering to get sunday last instead of first
    if ($wday == 0) {$wday = 6;} else {$wday = $wday - 1;}
    $calendartext = "";
    $divtext = "";

    if ($nbofmonths > 0) {
        $done = 0;
    } else {
        $done = 1;
    }

    $lastmonth = $mon + $nbofmonths - 1;
    if ($lastmonth > 11) {
        $lastmonth = $lastmonth - 12;
    }
    while ($done == 0) {
        $calendartext .= "
  <tr>";
        for ($monthcol = 0; (($monthcol < 3) && ($done == 0)); $monthcol++) {
            // get week day of following month start
            $fmon = $mon + 1;
            $fmyear = $year;
            if ($fmon == 12) {
                $fmon = 0;
                $fmyear = $year + 1;
            }
            $nextmonthtime = mktime($hours,$min,$sec,$fmon+1,$mday,$fmyear);
            list($sec, $min, $hours, $mday, $fmon, $fmyear, $fmwday, $yday, $isdst) = localtime($nextmonthtime);

            // change reference for week days numbering to get sunday last instead of first
            if ($fmwday == 0) {$fmwday = 6;} else {$fmwday = $fmwday - 1;}

            $monthyearstr = strftime("%B %Y", $curmonthtime);
            $calendartext .= "
    <td align=\"center\" valign=\"top\">
      <table border=\"0\" cellspacing=\"2\" cellpadding=\"2\">
        <tr>
          <td align=\"center\" colspan=\"7\" class=\"calcell\"><b><font color=\"#006699\">$monthyearstr</font></b></td>
        </tr>
        <tr>
          <td align=\"right\" width=\"22\" bgcolor=\"#CCCCCC\" class=\"calcell\">Mo</td>
          <td align=\"right\" width=\"22\" bgcolor=\"#CCCCCC\" class=\"calcell\">Tu</td>
          <td align=\"right\" width=\"22\" bgcolor=\"#CCCCCC\" class=\"calcell\">We</td>
          <td align=\"right\" width=\"22\" bgcolor=\"#CCCCCC\" class=\"calcell\">Th</td>
          <td align=\"right\" width=\"22\" bgcolor=\"#CCCCCC\" class=\"calcell\">Fr</td>
          <td align=\"right\" width=\"22\" bgcolor=\"#CCCCCC\" class=\"calcell\">Sa</td>
          <td align=\"right\" width=\"22\" bgcolor=\"#CCCCCC\" class=\"calcell\">Su</td>
        </tr>";

            $monthday = $mday;
            $inmonth = 0;
            $monthover = 0;
            while (($monthday < 32) && ($monthover == 0)) {
                $calendartext .= "        <tr>";
                for ($weekday = 0; $weekday < 7; $weekday++) {
                    $todaysevents = array();
                    $daynb = $monthday;

                    $daybgcolor ="#FFFFFF";
                    $dayfontcolor ="#FFFFFF";
                    if (($inmonth == 0) && ($weekday == $wday) && ($monthover == 0)) {
                        $inmonth = 1;
                    }
                    if ($inmonth > 0) {
                        if (($monthday > 28) && ($weekday == $fmwday)) {
                            $inmonth = 0;
                            $monthover = 1;
                            $daybgcolor ="#FFFFFF";
                            $dayfontcolor ="#FFFFFF";
                        } else {
                            if (($mon+1 == $thismonth) && ($monthday < $today)) {
                                $dayfontcolor ="#CCCCCC";
                            } else {
                                $dayfontcolor ="#000000";

                                $monthrealnb = $mon+1;
                                $yearrealnb = $year + 1900;
                                $todaystr = $yearrealnb."-";
                                if ($monthrealnb < 10) {$leader = "0";} else {$leader = "";}
                                $todaystr .= $leader.$monthrealnb."-";
                                if ($monthday < 10) {$leader = "0";} else {$leader = "";}
                                $todaystr .= $leader.$monthday;
                                $daybgcolor ="#FFFFFF";

                                foreach(array_keys($meetings) as $meetcategory) {
                                    foreach(array_keys($meetings[$meetcategory]) as  $meetst) {
                                        if ( $meetst == $todaystr ) {
                                            $todaysevents[$meetcategory] = array();
                                            foreach(array_keys($meetings[$meetcategory][$meetst]) as $meetid) {
                                                array_push($todaysevents[$meetcategory],array( "categ" => $meetcategory, "link" => "$AGE_WWW/fullAgenda.php?ida=$meetid", "title" => $meetings[$meetcategory][$meetst][$meetid]));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $events = array_keys($todaysevents);
                    if (sizeof($events) == 0) {
                        $daybgcolor ="#FFFFFF";
                        $calendartext .= "
          <td align=\"right\" width=\"22\" bgcolor=\"$daybgcolor\" class=\"calcell\"><font color=\"$dayfontcolor\"><b>$daynb</b></font></td>";
                    }
                    else {
                        if (sizeof($events) == 1) {
                            $daybgcolor = $colorcodes[array_search($events[0],$categories)];
                            $daynb = $monthday;
                        }
                        else {
                            $daybgcolor = $colormultiple;
                            $daynb = $monthday;
                        }
                        $dateid = $yearrealnb.$monthrealnb.$monthday;
                        $calendartext .= "
          <TD align=\"right\" width=\"22\" bgColor=\"$daybgcolor\" class=\"calcell\"><FONT color=\"$dayfontcolor\"><B><a class=navigation name=\"a$dateid\" id=\"a$dateid\" onMouseOver=\"setOnAnchor('d$dateid')\" onMouseOut=\"clearOnAnchor('d$dateid')\" href=\"#\"><span id=\"s$dateid\" class=navigation>$daynb</span></A></B></FONT></TD>
<script language=javascript>
<!--
menudivs[\"d$dateid\"]              = new Object();
menudivs[\"d$dateid\"].id           = \"d$dateid\";
menudivs[\"d$dateid\"].anchorId     = \"a$dateid\";
menudivs[\"d$dateid\"].spanId       = \"s$dateid\";
menudivs[\"d$dateid\"].menuOn       = false;
menudivs[\"d$dateid\"].onAnchor     = false;
//-->
</script>";


                        $divtext .= "
<div id=d$dateid style=\"Z-INDEX: 1; LEFT: 180px; TOP: 380; VISIBILITY: hidden; POSITION: absolute\">
  <layer>
  <table cellSpacing=0 cellPadding=1 bgcolor=\"#A0A0A0\" border=0>
    <tr>
      <td valign=top>
        <table cellSpacing=0 cellPadding=4 bgcolor=\"#FFFFFF\" border=0>";

                        foreach(array_keys($todaysevents) as $categ ) {
                            foreach(array_keys($todaysevents[$categ]) as $ev ) {
                                $evlink = $todaysevents[$categ][$ev]['link'];
                                $evtitle = $todaysevents[$categ][$ev]['title'];
                                $bgcolor = $colorcodes[array_search($todaysevents[$categ][$ev]['categ'],$categories)];
                                $divtext .= "
          <tr bgcolor=$bgcolor>
            <td valign=top><a href=\"$evlink\">$evtitle</a></td>
          </tr>";
                            }
                        }

                        $divtext .= "
        </table>
      </td>
    </tr>
  </table>
  </layer>
</div>";
                    }
                    if ($inmonth > 0) {
                        $monthday++;
                    }
                }
                $calendartext .= " </tr>";
            }
            $calendartext .= "
      </table>
    </td>";
            if ($monthcol < 2) {
                $calendartext .= "
    <td>&nbsp;</td>";
            }

            if ($mon == $lastmonth) {$done = 1;}
            $mon = $mon + 1;
            if ($mon == 12) {
                $mon = 0;
                $year = $year + 1;
            }
            $wday = $fmwday;
            $curmonthtime = $nextmonthtime;
        }
        $calendartext .= "  <tr>\n";
    }

    $Template->set_var("list_months", $calendartext);
    $Template->set_var("list_divs", $divtext);
}

function ReadAgendaCategory($categ)
{
    global $sdate,$edate,$meetings,$db;

    chop($categ);

    // build internal variables
    $sdaterequest = "";
    $edaterequest = "";
    if ($sdate != "") {
        list($sday,$smonth,$syear) = split("/",$sdate,3);
        $sdaterequest = "and stdate >= '$syear-$smonth-$sday'";
    }
    if ($edate != "") {
        list($eday,$emonth,$eyear) = split("/",$edate,3);
        $edaterequest = "and endate <= '$eyear-$emonth-$eday'";
    }

    // create the set of levels in which the agendas may be searched
    $levelset = array();
    $levelset = getChildren($categ);
    array_push($levelset,$categ);
    $levelsettext = createTextFromArray($levelset);

    $query = "SELECT id,stdate,endate,title
	  FROM AGENDA
	  WHERE fid IN $levelsettext
		$sdaterequest
		$edaterequest
	  ORDER BY stdate";
    $res = $db->query($query);
    while ($row = $res->fetchRow()){
        $title = $row['title'];
        $endate = $row['endate'];
        $stdate = $row['stdate'];
        $id = $row['id'];
        $meetings[$categ][$stdate][$id] = $title;
        if ($endate != $stdate) {
            list($y,$m,$d) = split ("-",$stdate,3);
            $cdate = $stdate;
            while ($cdate != $endate) {
                $d++;
                if ($d > 31) {
                    $d = "1";
                    $m++;
                    if ($m > 12) {
                        $m = 1;
                        $y++;
                    }
                }
                $cdate = $y."-";
                if (strlen($m) < 2) {$cdate .= "0";}
                $cdate .= $m."-";
                if (strlen($d) < 2) {$cdate .= "0";}
                $cdate .= $d;
                $meetings[$categ][$cdate][$id] = $title;
            }
            $meetings[$categ][$endate][$id] = $title;
        }
    }
}
?>

