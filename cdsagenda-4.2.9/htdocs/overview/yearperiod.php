<?php
// $Id: yearperiod.php,v 1.1.1.1.4.5 2003/03/28 10:15:59 tbaron Exp $

// yearperiod.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// yearperiod.php is part of CDS Agenda.
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

function yearperiod()
{
    global $daytime,
        $selecteddate,
        $selectedmonth,
        $selectedyear,
        $selectedday,
        $firstdaytime,
        $years;
             
    $smonth=date('M',$daytime);
    $pyear = $selectedyear - 1;
    $nyear = $selectedyear + 1;

    $outStr = "";
    $outStr .= "<FONT size=-2>\n";
    $outStr .="
<INPUT type=hidden name=selectedmonth value=$selectedmonth>
<SELECT NAME=selectedyear onChange=\"document.forms[0].submit();\">";
    for ($i=0;$i<count($years);$i++) {
        $outStr .= "
        <OPTION ". ($selectedyear==$years[$i]?"selected":"").">".$years[$i];
    }
    $outStr .= "
</SELECT>
</FONT>
<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"Javascript1.2\">
	document.forms[0].selectedyear.selectedIndex=$selectedyear-1995;
</SCRIPT>\n";
    $outStr .= "<BR><TABLE width=\"100%\"><TR><TD align=left width=\"50%\"><A HREF=\"\" onClick=\"javascript:SetDate($selectedday,$selectedmonth,$pyear);return false;\"><IMG SRC=\"../images/backward.gif\" ALT=\"previous year\" border=0></A></TD><TD align=right width=\"50%\"></font><A HREF=\"\" onClick=\"javascript:SetDate($selectedday,$selectedmonth,$nyear);return false;\"><IMG SRC=\"../images/forward.gif\" ALT=\"next year\" border=0></A></TD></TR></TABLE>\n";			

    return $outStr;
}
?>