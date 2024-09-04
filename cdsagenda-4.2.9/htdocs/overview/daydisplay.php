<?php
// $Id: daydisplay.php,v 1.1.1.1.4.4 2004/07/29 10:06:09 tbaron Exp $

// daydisplay.php --- display one day in the overview
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// daydisplay.php is part of CDS Agenda.
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

require_once("getSTime.inc");
require_once("displayfuncs.php");

function daydisplay($printable)
{
    global $daytime,
        $fid,
        $IMAGES,
        $display,
        $thislevel,
        $levelsettext,
        $mapServerACTIVE,
        $mapServerAddress,
        $nbDisplayedIcons;

    $thisday = date ("Y-m-d", $daytime );

    $outStr = "";

    // this day from 8AM to 2PM
    $outStr .= "
<TABLE width=\"100%\" border=1 cellspacing=2>
<TR>
        <td valign=\"top\" align=\"center\" bgcolor=\"green\">
                <small><font color=white>
                        <b>AM</b>
                </font>
                </small>
        </td>";
	$outStr .= displayAgendaAM($thisday,$printable,$display,$levelsettext);


    // from 2PM to 12PM
    $outStr .= "</TR><TR>";
    $outStr .= "
<td valign=\"top\" align=\"center\" bgcolor=\"green\">
        <small><font color=white>
        <b>PM</b>
        </font>
        </small>
</td>";
	$outStr .= displayAgendaPM($thisday,$printable,$display,$levelsettext);	
    $outStr .= "</TR></TABLE>";

    return $outStr;
}
?>