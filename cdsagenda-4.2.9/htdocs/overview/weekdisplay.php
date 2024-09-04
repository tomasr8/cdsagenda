<?php
// $Id: weekdisplay.php,v 1.1.1.1.4.7 2004/07/29 10:06:10 tbaron Exp $

// weekdisplay.php --- display one week in the overviw
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// weekdisplay.php is part of CDS Agenda.
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
require_once("getSTime.inc");
require_once("displayfuncs.php");

function weekdisplay($printable)
{
	global $firstday,$fid,$IMAGES,$display,$thislevel,$levelsettext,$mapServerACTIVE,$mapServerAddress,$nbDisplayedIcons;

    $outStr = "";
	$outStr .=  "<TABLE width=\"100%\" border=1 cellspacing=2>";
	$outStr .=  "<TR bgcolor=green><TD></TD>";
	for ( $day=0 ; $day<=6 ; $day++ )
        {
            $thisday = date ("Y-m-d", $firstday + $day * 86400 );
            if (Test_Day($thisday))
                {
                    $dayname = date ("D d/m", $firstday + $day * 86400 );
                    $monthday = date ("d", $firstday + $day * 86400 );
                    if (!$printable)
                        {
                            $outStr .=  "<TH><A HREF=\"\" onClick=\"document.forms[0].selectedday.value=$monthday;document.forms[0].period.selectedIndex=0;document.forms[0].submit();return false;\"><font color=white size=-1>$dayname</font></A></TH>";
                        }
                    else
                        {
                            $outStr .=  "<TH><font color=white size=-1>$dayname</font></TH>";
                        }
                }
        }
	$outStr .=  "</TR><TR>\n";
	
    $outStr .=  "
<td valign=\"top\" align=\"center\" bgcolor=green>
	<small><font color=white>
        <b>AM</b>
	</font>
        </small>
</td>";
	
#from Monday to Friday 8AM to 2PM
	for ( $day=0 ; $day<=6 ; $day++ )
        {
            $thisday = date ("Y-m-d", $firstday + $day * 86400 );
            if (Test_Day($thisday))
                {
                    $outStr .= displayAgendaAM($thisday,$printable,$display,$levelsettext);
                }
        }

	$outStr .=  "</TR><TR>";

	$outStr .=  "
<td valign=\"top\" align=\"center\" bgcolor=\"green\">
	<small><font color=white>
        <b>PM</b>
	</font>
        </small>
</td>";

#from Monday to Friday 8AM to 2PM
	for ( $day=0 ; $day<=6 ; $day++ )
        {
            $thisday = date ("Y-m-d", $firstday + $day * 86400 );
            if (Test_Day($thisday))
                $outStr .= displayAgendaPM($thisday,$printable,$display,$levelsettext);
        }

	$outStr .=  "</TR></TABLE>";
    
//     if ( !isset( $GLOBALS[ "log" ] ))
//         $GLOBALS[ "log" ] = new logManager;

    $log = &AgeLog::getLog();                
    $log->logDebug( __FILE__, __LINE__, "_$outStr" . "_" );
            
    return $outStr;
}


function Test_Day($day)
{
	global $fid,$levelsettext;

    $db = &AgeDB::getDB();

	$request = "
SELECT DISTINCT
	AGENDA.id
from
	AGENDA
where
	(AGENDA.stdate <= '$day' and AGENDA.endate>='$day') and
	AGENDA.fid IN $levelsettext and 
	AGENDA.fid != '3l52'
order by
	AGENDA.id";

    $res = $db->query($request);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    $numRows = $res->numRows();

	if ( $numRows != 0 )
        {
            return true;
        }
	else
        {
            return false;
        }	
}

?>
