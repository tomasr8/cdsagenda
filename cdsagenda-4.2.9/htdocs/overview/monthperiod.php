<?php
// $Id: monthperiod.php,v 1.1.1.1.4.5 2003/03/28 10:15:59 tbaron Exp $

// monthperiod.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// monthperiod.php is part of CDS Agenda.
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

    
function monthperiod()
{
    global $daytime,
        $selecteddate,
        $selectedmonth,
        $selectedyear,
        $selectedday,
        $firstdaytime,
        $years;
         
    $smonth=date('M',$daytime);
	
    $nmonth = $selectedmonth + 1;
    $nyear = $selectedyear;
    if ($nmonth == 13) {
        $nmonth = 1;
        $nyear ++;
    }
    $pmonth = $selectedmonth - 1;
    $pyear = $selectedyear;
    if ($pmonth == 0) {
        $pmonth = 12;
        $pyear --;
    }

    $outStr = "";
    $outStr .= "<FONT size=-2>\n";
    $outStr .= "
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
</FONT>\n";
    $outStr .= "<BR><TABLE width=\"100%\"><TR><TD align=left width=\"50%\"><A HREF=\"\" onClick=\"javascript:SetDate($selectedday,$pmonth,$pyear);return false;\"><IMG SRC=\"../images/backward.gif\" ALT=\"previous month\" border=0></A></TD><TD align=right width=\"50%\"></font><A HREF=\"\" onClick=\"javascript:SetDate($selectedday,$nmonth,$nyear);return false;\"><IMG SRC=\"../images/forward.gif\" ALT=\"next month\" border=0></A></TD></TR></TABLE>\n";	

    return $outStr;
}
?>