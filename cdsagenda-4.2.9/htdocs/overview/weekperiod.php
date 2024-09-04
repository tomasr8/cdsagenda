<?php
// $Id: weekperiod.php,v 1.1.1.1.4.5 2003/03/28 10:15:59 tbaron Exp $

// weekperiod.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// weekperiod.php is part of CDS Agenda.
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

function weekperiod()
{
   global $daytime,
       $selecteddate,
       $selectedmonth,
       $selectedyear,
       $selectedday,
       $firstdaytime,
       $years;

    $smonth=date('M',$daytime);
    $nextday = $selecteddate + 604800;
    $nday = date ('j',$nextday);
    $nmonth = date ('n',$nextday);
    $nyear = date ('Y',$nextday);
    $previousday = $selecteddate - 604800;
    $pday = date ('j',$previousday);
    $pmonth = date ('n',$previousday);
    $pyear = date ('Y',$previousday);
	
    $outStr = "";
    $outStr .="<FONT size=-2>\n";
    $outStr .="
<SELECT name=selectedmonth onChange=\"document.forms[0].submit();\">
        <OPTION value=\"01\" ".($selectedmonth==1?"selected":"").">Jan
        <OPTION value=\"02\" ".($selectedmonth==2?"selected":"").">Feb
        <OPTION value=\"03\" ".($selectedmonth==3?"selected":"").">Mar
        <OPTION value=\"04\" ".($selectedmonth==4?"selected":"").">Apr
        <OPTION value=\"05\" ".($selectedmonth==5?"selected":"").">May
        <OPTION value=\"06\" ".($selectedmonth==6?"selected":"").">Jun
        <OPTION value=\"07\" ".($selectedmonth==7?"selected":"").">Jul
        <OPTION value=\"08\" ".($selectedmonth==8?"selected":"").">Aug
        <OPTION value=\"09\" ".($selectedmonth==9?"selected":"").">Sep
        <OPTION value=\"10\" ".($selectedmonth==10?"selected":"").">Oct
        <OPTION value=\"11\" ".($selectedmonth==11?"selected":"").">Nov
        <OPTION value=\"12\" ".($selectedmonth==12?"selected":"").">Dec
</SELECT>
<SELECT NAME=selectedyear onChange=\"document.forms[0].submit();\">";
    for ($i=0;$i<count($years);$i++) {
        $outStr .= "
        <OPTION ". ($selectedyear==$years[$i]?"selected":"").">".$years[$i];
    }
    $outStr .= "
</SELECT>
</FONT>
<TABLE border=0 bgcolor=silver cellspacing=1 align=center>
<TR>
	<TH class=headerselected><FONT size=-2>Mon</FONT></TH>
	<TH class=headerselected><FONT size=-2>Tue</FONT></TH>
	<TH class=headerselected><FONT size=-2>Wed</FONT></TH>
	<TH class=headerselected><FONT size=-2>Thu</FONT></TH>
	<TH class=headerselected><FONT size=-2>Fri</FONT></TH>
	<TH class=headerselected><FONT size=-2>Sat</FONT></TH>
	<TH class=headerselected><FONT size=-2>Sun</FONT></TH>
</TR>
<TR>\n";
    $weekday = date('w',$firstdaytime);
    $weekdate = date('w',$selecteddate);
    if ($weekdate == 0) { 
        $weekdate = 7; 
    }
    if ($weekday == 0) { 
        $weekday = 7; 
    }
    for ( $i=1 ; $i<$weekday ; $i++ ) { 
        $outStr .= "<TD>&nbsp;</TD>\n";
    }

    while (date ('m',$daytime) == $selectedmonth) {
        $weekday = date ('w',$daytime);
        $monthday = date ('j',$daytime);
        if (($monthday > $selectedday-$weekdate)
            &&($monthday <= $selectedday + 7-$weekdate)) {
            $outStr .= "<TD align=right bgcolor=green><SMALL><A href=\"\" onClick=\"javascript:SetDate($monthday,$selectedmonth,$selectedyear);return false;\"><FONT color=white>$monthday</FONT></A></SMALL></TD>\n";
        }
        else if ($weekday==6 || $weekday==0) {
            $outStr .= "<TD align=right bgcolor=\"#70a0d0\"><SMALL><A href=\"\" onClick=\"javascript:SetDate($monthday,$selectedmonth,$selectedyear);return false;\">$monthday</A></SMALL></TD>\n";
        }
        else {
            $outStr .= "<TD align=right class=header><SMALL><A href=\"\" onClick=\"javascript:SetDate($monthday,$selectedmonth,$selectedyear);return false;\">$monthday</A></SMALL></TD>\n";
        }
        if ($weekday == 0) { 
            $outStr .= "</TR><TR>"; 
        }
        $daytime += 86400;
    }
    if ($weekday != 0) {
        for ( $i=7 ; $i>$weekday ; $i-- ) { 
            $outStr .= "<TD>&nbsp;</TD>\n";
        }
    }

    $outStr .= "</TR></TABLE>\n";
    
    $outStr .= "<BR><TABLE width=\"100%\"><TR><TD align=left width=\"50%\"><A HREF=\"\" onClick=\"javascript:SetDate($pday,$pmonth,$pyear);return false;\"><IMG SRC=\"../images/backward.gif\" ALT=\"previous week\" border=0></A></TD><TD align=right width=\"50%\"></font><A HREF=\"\" onClick=\"javascript:SetDate($nday,$nmonth,$nyear);return false;\"><IMG SRC=\"../images/forward.gif\" ALT=\"next week\" border=0></A></TD></TR></TABLE>\n";

    return $outStr;
}
?>