<?php
// $Id: monthdisplay.php,v 1.1.1.1.4.7 2004/07/29 10:06:10 tbaron Exp $

// monthdisplay.php --- display one month in the overview
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// monthdisplay.php is part of CDS Agenda.
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
require_once 'displayfuncs.php';
require_once 'getSTime.inc';

function monthdisplay($printable)
{
	global $daytime,
        $firstdaytime,
        $fid,
        $IMAGES,
        $selectedmonth,
        $thislevel,
        $levelsettext,
        $IMAGES_WWW,
        $nbDisplayedIcons;
    
    $db = &AgeDB::getDB();
    $outStr = "";

    $outStr .= "
<TABLE border=1 cellspacing=2 cellpadding=1 width=\"100%\" bgcolor=gray>
<TR class=headerselected>
	<TD></TD><TD></TD>
	<TH><FONT size=-1 color=white>Monday</FONT></TH>
	<TH><FONT size=-1 color=white>Tuesday</FONT></TH>
	<TH><FONT size=-1 color=white>Wednesday</FONT></TH>
	<TH><FONT size=-1 color=white>Thursday</FONT></TH>
	<TH><FONT size=-1 color=white>Friday</FONT></TH>
	<TH><FONT size=-1 color=white>Saturday</FONT></TH>
	<TH><FONT size=-1 color=white>Sunday</FONT></TH>
</TR>
<TR><TD> </TD></TR>
<TR>";

    $weeknumber = strftime('%W',$daytime);
    $monthday = date ('j',$daytime);
    if (!$printable) {
			$outStr .= "<TD class=headerselected align=center><A HREF=\"\" onclick=\"document.forms[0].selectedday.value=$monthday;document.forms[0].period.selectedIndex=1;document.forms[0].submit();return false;\"><font color=white size=-1><B>Week $weeknumber</B></font></A></TD><TD></TD>\n";
    }
    else {
        $outStr .= "<TD class=headerselected align=center><font color=white size=-1><B>Week $weeknumber</B></font></TD><TD></TD>\n";
    }

    $smonth=date('M',$daytime);
	
    $weekday = date('w',$firstdaytime);
    if ($weekday == 0) { 
        $weekday = 7; 
    }
    $weekday--;

    if ($weekday != 0) {
        $outStr .= "<TD bgcolor=\"white\" class=empty colspan=\"$weekday\"></TD>\n";
    }

    while (date ('m',$daytime) == $selectedmonth) {
        $weekday = date ('w',$daytime);
        $monthday = date ('j',$daytime);
        $monthdaytxt = date ('jS',$daytime);
        $thisdate = date ('Y-m-d',$daytime);

        if ($weekday == 1 && $monthday != 1) {
            $weeknumber = strftime('%W',$daytime);
            if (!$printable) {
                $outStr .= "<TD class=headerselected align=center><A HREF=\"\" onclick=\"document.forms[0].selectedday.value=$monthday;document.forms[0].period.selectedIndex=1;document.forms[0].submit();return false;\"><font color=white size=-1><B>Week $weeknumber</B></font></A></TD><TD></TD>\n";
            }
            else {
                $outStr .= "<TD class=headerselected align=center><font color=white size=-1><B>Week $weeknumber</B></font></TD><TD></TD>\n";
            }
        }

        $request = "
SELECT DISTINCT title,id,confidentiality,fid,visibility
from
	AGENDA
where
	(AGENDA.stdate <= '$thisdate' and AGENDA.endate>='$thisdate') and
	AGENDA.fid IN $levelsettext and 
	AGENDA.fid != '3l52'
order by
	AGENDA.id";

        $res = $db->query($request);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        $numRows = $res->numRows();

        $outStr .= "<TD valign=top><TABLE border=0 cellspacing=0 cellpadding=2 width=\"100%\" bgcolor=white><TR>\n";
        if ($fid == 0) {
            $colspan=2;
        } 
        else {
            $colspan=1;
        }
			
        if ($weekday==6 || $weekday==0) {
            $outStr .= "<TD align=center bgcolor=#006600 valign=top colspan=2>";
        }
        else {
            $outStr .= "<TD align=center valign=top bgcolor=green colspan=2>";
        }
        if (!$printable) {
            $outStr .= "<SMALL><B><A HREF=\"\" onclick=\"document.forms[0].selectedday.value=$monthday;document.forms[0].period.selectedIndex=0;document.forms[0].submit();return false;\"><FONT color=white>$monthdaytxt</FONT></A></B></SMALL></TD></TR>\n";
        }
        else {
            $outStr .= "<SMALL><B><FONT color=white>$monthdaytxt</FONT></B></SMALL></TD></TR>\n";
        }
				
        $num = 0;
        if ( $numRows != 0 ) {
            for ( $i = 0; $numRows > $i; $i++ ) {
                $row = $res->fetchRow();
                $num++;
                
                $ida = $row['id'];
                $confidentiality = $row['confidentiality'];
                if ($confidentiality != "password") {
                    // check with the global settings
                    $sql2 = "SELECT accessPassword
                            FROM LEVEL
                            WHERE uid='$fid'";
                    $res2 = $db->query($sql2);
                    $row2 = $res2->fetchRow();
                    if ($row2['accessPassword']) {
                        $confidentiality = "password";
                    }
                }

                $agendafid = $row['fid'];
                $visibility = min ($row['visibility'],getCategVisibility($agendafid));

                if (isVisible($ida,$visibility,$agendafid)) {
                   $outStr .= "<TR bgcolor=\"gray\"><TD colspan=\"2\"><IMG SRC=\"$IMAGES_WWW/transparent_height.gif\" ALT=\"\" HSPACE=0 VSPACE=0></TD></TR>\n";
                   $outStr .= displayAgendaHeader($ida,$printable,$thisdate,"AM");

                   $requestsession = "
SELECT 	
	ids
FROM	SESSION
WHERE	ida='$ida' and
	speriod1<='$thisdate' and 
	eperiod1>='$thisdate' and
	stitle!='dummy session'
order by stime";
                   $res_sess = $db->query($requestsession);
                   if (DB::isError($res_sess)) {
                       die ($res_sess->getMessage());
                   }
                   $numRows_sess = $res_sess->numRows();
                   
                   for ( $j = 0; $numRows_sess > $j; $j++ ) {
                       $row_sess = $res_sess->fetchRow();
                       $ids = $row_sess['ids'];
                       $outStr .= displaySessionHeader($ida,$ids,$printable,$thisdate);
                   }
                }
            }
        }
        else {
            $outStr .= "<TR><TD bgcolor=gray align=center valign=middle>&nbsp;</TD></TR>\n";
        }
        $outStr .= "</TABLE>\n";

        $outStr .= "</TD>";
        if ($weekday == 0) { 
            $outStr .= "</TR><TR>"; 
        }

        $daytime += 86400;
    }
    $weekday = date ('w',$daytime);
    if ($weekday == 0) { 
        $weekday = 7; 
    }
    if ($weekday != 1) {
        $colspan = 8 - $weekday;
        $outStr .= "<TD bgcolor=white class=empty colspan=\"$colspan\"></TD>\n";
    }
    $outStr .= "</TR></TABLE>\n";
    
    return $outStr;
}	

?>