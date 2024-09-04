<?php
// $Id: calendar.php,v 1.1.1.1.4.2 2002/10/23 08:56:33 tbaron Exp $

// calendar.php --- calendar
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// calendar.php is part of CDS Agenda.
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

require ("../config/config.php");

$thismonth=date('m',time());
$thisyear=date('Y',time());
if ($month == "") { $month = "$thismonth"; }
if ($month < 10) { $month = "0$month"; }
if ($year == "") { $year = "$thisyear"; }
if ($daystring == "") { $daystring="stdd"; }
if ($monthstring == "") { $monthstring="stdm"; }
if ($yearstring == "") { $yearstring="stdy"; }

$firstdaytime = mktime(12,0,0,$month,1,$year);
$daytime = $firstdaytime;
?>

<HTML>
<HEAD>
<TITLE>Choose a Date</TITLE>
<?
print "
	<link rel=\"stylesheet\" href=\"$IMAGES_WWW/stylemodify.css\">";
?>

<SCRIPT language=javascript>

function SetDate(d,m,y)
{
    <?
    print "
	window.opener.document.forms[0].$daystring.selectedIndex=d-1;
	window.opener.document.forms[0].$yearstring.selectedIndex=y-1995;
	window.opener.document.forms[0].$monthstring.selectedIndex=m-1;
	";
    ?>
	
    window.close();
}

	
</SCRIPT>
</HEAD>
<BODY TEXT=black BGCOLOR="#FFFFFF" LINK="#009900" VLINK="#006600" ALINK="#FF0000">
<FORM action="calendar.php">
<?
print "
<INPUT name=daystring type=hidden value=$daystring>
<INPUT name=monthstring type=hidden value=$monthstring>
<INPUT name=yearstring type=hidden value=$yearstring>\n";


$smonth=ereg_replace("^0","",date('M',$daytime));
	
print "<CENTER><FONT size=-2>\n";
print "
<SELECT name=month onChange=\"document.forms[0].submit();\">
	<OPTION value=\"01\">Jan
	<OPTION value=\"02\">Feb
	<OPTION value=\"03\">Mar
	<OPTION value=\"04\">Apr
	<OPTION value=\"05\">May
	<OPTION value=\"06\">Jun
	<OPTION value=\"07\">Jul
	<OPTION value=\"08\">Aug
	<OPTION value=\"09\">Sep
	<OPTION value=\"10\">Oct
	<OPTION value=\"11\">Nov
	<OPTION value=\"12\">Dec
</SELECT>
<SELECT NAME=year onChange=\"document.forms[0].submit();\">
	<OPTION>1995
	<OPTION>1996
	<OPTION>1997
	<OPTION>1998
	<OPTION>1999
	<OPTION>2000
	<OPTION>2001
	<OPTION>2002
	<OPTION>2003
</SELECT>
</FONT>
<SCRIPT>
	document.forms[0].month.selectedIndex=$month-1;
	document.forms[0].year.selectedIndex=$year-1995;
</SCRIPT>
\n";
?>

<TABLE border=0 bgcolor=silver cellspacing=1>
<TR>
<TH class=headerselected><FONT size=-2>Mon</FONT></TH>
<TH class=headerselected><FONT size=-2>Tue</FONT></TH>
<TH class=headerselected><FONT size=-2>Wed</FONT></TH>
<TH class=headerselected><FONT size=-2>Thu</FONT></TH>
<TH class=headerselected><FONT size=-2>Fri</FONT></TH>
<TH class=headerselected><FONT size=-2>Sat</FONT></TH>
<TH class=headerselected><FONT size=-2>Sun</FONT></TH>
</TR>
<TR>
<?

print "<TR>\n";
$weekday = date('w',$firstdaytime);
if ($weekday == 0) { $weekday = 7; }
for ( $i=1 ; $i<$weekday ; $i++ ) 
{ 
    print "<TD>&nbsp;</TD>\n";
}

while (date ('m',$daytime) == $month)
{
    $weekday = date ('w',$daytime);
    $monthday = date ('j',$daytime);
    if ($weekday==6 || $weekday==0)
        {
            print "<TD align=center class=header><SMALL><A href=\"\" onClick=\"javascript:SetDate($monthday,$month,$year);return false;\">$monthday</A></SMALL></TD>\n";
        }
    else
        {
            print "<TD align=center><SMALL><A href=\"\" onClick=\"javascript:SetDate($monthday,$month,$year);return false;\">$monthday</A></SMALL></TD>\n";
        }
    if ($weekday == 0) { print "</TR><TR>"; }
    $daytime += 86400;
}

?>
</TR>
</TABLE>
</CENTER>
</FORM>
</BODY>
</HTML>