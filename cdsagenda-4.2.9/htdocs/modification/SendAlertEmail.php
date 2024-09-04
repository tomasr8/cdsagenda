<?php
// $Id: SendAlertEmail.php,v 1.1.1.1.4.8 2002/11/12 07:37:36 tbaron Exp $

// SendAlertEmail.php --- alert
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// SendAlertEmail.php is part of CDS Agenda.
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

require_once "$AGE/AgeDB.php";
require_once "$AGE/AgeLog.php";

function Send_Alert_Email() {
	global $ida,
		$address,
		$note,
		$alarm_email,
		${AGE_WWW},
		$include_agenda,
        $AGE,
        $XTXSL,
        $TMPDIR,
        $runningAT;
	
        $db  = &AgeDB::getDB();
        $sql = "select title,stdate,stime,room,cem,chairman,location,id,apassword from AGENDA where id='$ida'";
        $resalert = $db->query($sql);
        if (DB::isError($resalert)) {
            die ($resalert->getMessage());
        }
        $numRows = $resalert->numRows();
	
        if ($numRows != 0) {
            $row = $resalert->fetchRow();
	    $PHP_AUTH_PW = $row['apassword'];
            $stime    = ereg_replace(":00$","",$row['stime']);
            $stdate   = explode("-", $row['stdate'] );
            $location = $row['location'];
            $room     = $row['room'];

            $lstdate = date ("l d M Y",mktime(0,0,0,$stdate[1],$stdate[2],$stdate[0]));

            $title = "Reminder: " . $row['title'];
            $row['chairman'] = ereg_replace('"','\"', $row['chairman'] );
            $body = "Hello,\nPlease note that the meeting \"" . $row['title'] . "\" will begin on $lstdate at $stime.\nLocation: $location $room\n\nYou can access the full agenda here:\n<${AGE_WWW}/fullAgenda.php?ida=$ida>\n\nBest Regards,\n" . $row['chairman'];

            if ($note != "") {
                $body .= "\n\nNote: $note";
            }

	    $emailsentby = $row['cem'];

            if ($include_agenda == "on") {
                $pid = getmypid();
                $access = time() . "_$pid";

                if ($dl == "") { 
                    $dl = "talk"; 
                }
                if ($dd == "") { 
                    $dd = "0000-00-00"; 
                }
                include ("$AGE/XMLoutput.php");
                $agenda = new agenda($ida);
                $xml = $agenda->displayXML();

                $fp = fopen("$TMPDIR/processXML_$access.xml","w+");
                fwrite($fp,$xml);
                fclose($fp);

                ${agenda_txt} = `$XTXSL $TMPDIR/processXML_$access.xml $AGE/stylesheets/simpletext/simpletext.xsl 2>> $TMPDIR/errors_$access`;

                unlink("$TMPDIR/processXML_$access.xml");
                unlink("$TMPDIR/errors_$access");

                ${agenda_txt} = str_replace("<br>","\n",${agenda_txt});
                ${agenda_txt} = str_replace("<BR>","\n",${agenda_txt});

                $body .= "\n\nText version of the agenda:\n\n${agenda_txt}";
            }

            if ($emailsentby != "") {
                mail("$address","$title","$body","from:" . $emailsentby . "\nbcc: ${alarm_email}");
                return true;
            }
            else {
                return false;
            }		
        }
        else if ( ERRORLOG ) {         
            $log = &AgeLog::getLog();
            $log->logError( __FILE__ . "." . __LINE__, 
                            "main", 
                            " Retrieved 0 elements with select '" . 
                            $sql . 
                            "' " );
        }            
}
?>