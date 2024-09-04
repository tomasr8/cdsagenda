<?php
// $Id: yeardisplay.php,v 1.1.1.1.4.7 2003/03/28 10:16:56 tbaron Exp $

// yeardisplay.php --- overview: display one year
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// yeardisplay.php is part of CDS Agenda.
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

function yeardisplay( $printable )
{
	global $daytime,
        $fid,
        $IMAGES,
        $thislevel,
        $levelsettext,
        $nbDisplayedIcons,
        $IMAGES_WWW;
    
    $outStr = "";
            
    $outStr .= "
<TABLE border=1 cellspacing=2 cellpadding=1 width=\"100%\" bgcolor=gray>
<TR bgcolor=green>";

	if (!$printable) {
        $outStr .= "
	<TH><A HREF=\"\" onClick=\"document.forms[0].selectedmonth.value=1;document.forms[0].period.selectedIndex=2;document.forms[0].submit();return false;\"><FONT size=-1 color=white>January</FONT></A></TH>
	<TH><A HREF=\"\" onClick=\"document.forms[0].selectedmonth.value=2;document.forms[0].period.selectedIndex=2;document.forms[0].submit();return false;\"><FONT size=-1 color=white>February</FONT></A></TH>
	<TH><A HREF=\"\" onClick=\"document.forms[0].selectedmonth.value=3;document.forms[0].period.selectedIndex=2;document.forms[0].submit();return false;\"><FONT size=-1 color=white>March</FONT></A></TH>\n";
    }
	else {
        $outStr .= "
	<TH><FONT size=-1 color=white>January</FONT></TH>
	<TH><FONT size=-1 color=white>February</FONT></TH>
	<TH><FONT size=-1 color=white>March</FONT></TH>\n";
    }
    $outStr .= "
</TR>
<TR>";

	$year=date('Y',$daytime);
	$month= 1;
    
    while ( $month < 13 ) {
        $firstday = "$year-$month-1";
        if ($month==1 || 
            $month==3 || 
            $month==5 || 
            $month==7 || 
            $month==8 || 
            $month==10 || 
            $month==12) {
            $lastday = "$year-$month-31";
        }
        else {
            $lastday = "$year-$month-30";
        }
        
        $db = &AgeDB::getDB();
        $request = "
SELECT DISTINCT title,id,stdate,endate,confidentiality,fid,visibility
from
	AGENDA
where
	((TO_DAYS(AGENDA.stdate) BETWEEN TO_DAYS('$firstday') and TO_DAYS('$lastday')) or
	(TO_DAYS(AGENDA.endate) BETWEEN TO_DAYS('$firstday') and TO_DAYS('$lastday')) or
	(AGENDA.stdate <= '$firstday' and AGENDA.endate >= '$lastday')) and
	AGENDA.fid IN $levelsettext and 
	AGENDA.fid != '3l52'
order by
	AGENDA.stdate";

        $res = $db->query($request);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        $numRows = $res->numRows();
        
        $num = 0;
        if ( $numRows != 0 ) {
            $outStr .= "<TD valign=top>
<TABLE border=0 cellspacing=0 cellpadding=2 width=\"100%\">\n";

            for ( $i = 0; $numRows > $i; $i++ ) {
                $row = $res->fetchRow();
                
                $ida = $row['id'];
                $agendafid = $row['fid'];

                $visibility = min($row['visibility'],getCategVisibility($agendafid));
                if (isVisible($ida,$visibility,$agendafid)) {
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
                    
                    $num++;
                    $stdate = $row['stdate'];
                    $array = split("-",$stdate,3);
                    $styear = $array[0];
                    $stmonth = $array[1];
                    $stday = $array[2];
                    $stdate = date ('j M',mktime(12,0,0,$stmonth,$stday,$styear));
                    
                    $endate = $row['endate'];
                    $array = split("-",$endate,3);
                    $enyear = $array[0];
                    $enmonth = $array[1];
                    $enday = $array[2];
                    $endate = date ('j M',mktime(12,0,0,$enmonth,$enday,$enyear));
                
                    // create the upper branch of the hierarchical tree for the current agenda
                    $upperbranch = array();
                    $upperbranch = getFatherTree($agendafid);
                    $imagelevel = getIconLevel($upperbranch,$thislevel);
                    $imagename = getIcon($imagelevel);
                    
                    if ($imagename == "") {
                        $image = "<TD>&nbsp;</TD>";
                    }
                    else {
                        $nbDisplayedIcons[$imagename] ++;
                        if (!$printable) {
                            $image = "<TD valign=top><A HREF=\"\" onclick=\"document.forms[0].fid.value='$imagelevel';document.forms[0].submit();return false;\"><IMG SRC=\"$IMAGES_WWW/levelicons/$imagename.gif\" ALT=\"$array[1]\" border=0></A></TD>"; 
                        }
                        else {
                            $image = "<TD valign=top><IMG SRC=\"$IMAGES_WWW/levelicons/$imagename.gif\" ALT=\"$array[1]\" border=0></TD>"; 
                        }
                    }
                
                    if ($stdate == $endate) { 
                        $date = "$stdate"; 
                    }
                    else { 
                        $date = "$stdate/$endate"; 
                    }
                    if (($num/2) == (int)($num/2)) {
                        $outStr .= "
<TR bgcolor=silver>
	$image
	<TD valign=top><SMALL><B>$date</B></SMALL></TD>
	<TD><SMALL>\n";
                        if (!$printable) {
                            $outStr .= "<A HREF=\"../fullAgenda.php?ida=" . $ida . "\">" . $row['title'] . "</A></SMALL>\n";	
                        }
                        else {
                            $outStr .= $row['title'] . "</SMALL>\n";	
                        }
                    }
                    else {
                        $outStr .= "
<TR bgcolor=\"#D2D2D2\">
	$image
	<TD valign=top><SMALL><B>$date</B></SMALL></TD>
	<TD><SMALL>\n";
                        if (!$printable) {
                            $outStr .= "<A HREF=\"../fullAgenda.php?ida=" . $ida . "\">" . $row['title'] . "</A></SMALL>\n";	
                        }
                        else {
                            $outStr .= $row['title'] . "</SMALL>\n";	
                        }
                    }
                    if ( $confidentiality == "password") {
                        $outStr .= "<IMG SRC=\"$IMAGES_WWW/private.gif\" ALT=\"protected\" border=0>\n";
                    }
                } // end if (isVisible...))
            } //end for ( $i = 0; $numRows > $i; $i++ )
            $outStr .= "</TD>
</TR></TABLE></TD>\n";
        } // end if ( $numRows != 0 )
        else {
            $outStr .= "<TD class=empty bgcolor=white><font color=white></font></TD>\n";
        }
        if ((($month)/3) == (int)(($month)/3) && ($month!=12)) {
            $month1=date('F',mktime(12,0,0,($month+1),1,$year));
            $month2=date('F',mktime(12,0,0,($month+2),1,$year));
            $month3=date('F',mktime(12,0,0,($month+3),1,$year));
            if (!$printable) {
                $outStr .= "</TR><TR bgcolor=green>
	<TH><A HREF=\"\" onClick=\"document.forms[0].selectedmonth.value=$month+1;document.forms[0].period.selectedIndex=2;document.forms[0].submit();return false;\"><FONT size=-1 color=white>$month1</FONT></A></TH>
	<TH><A HREF=\"\" onClick=\"document.forms[0].selectedmonth.value=$month+2;document.forms[0].period.selectedIndex=2;document.forms[0].submit();return false;\"><FONT size=-1 color=white>$month2</FONT></A></TH>
	<TH><A HREF=\"\" onClick=\"document.forms[0].selectedmonth.value=$month+3;document.forms[0].period.selectedIndex=2;document.forms[0].submit();return false;\"><FONT size=-1 color=white>$month3</FONT></A></TH></TR><TR>\n";
            }
            else {
                $outStr .= "</TR><TR bgcolor=green>
	<TH><FONT size=-1 color=white>$month1</FONT></TH>
	<TH><FONT size=-1 color=white>$month2</FONT></TH>
	<TH><FONT size=-1 color=white>$month3</FONT></TH></TR><TR>\n";
            }
        } // end if ((($month)/3) == (int)(($month)/3) && ($month!=12))
        $month++;
    } // end while ( $month < 13 )
    
	$outStr .= "</TR></TABLE>\n";
    
    return $outStr;
}
?>