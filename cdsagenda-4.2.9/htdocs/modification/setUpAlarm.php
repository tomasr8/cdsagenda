<?php
// $Id: setUpAlarm.php,v 1.1.1.1.4.8 2004/07/29 10:06:08 tbaron Exp $

// setUpAlarm.php --- alarm setup
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// setUpAlarm.php is part of CDS Agenda.
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

require_once("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once('../AgeDB.php');
require_once('../AgeLog.php');
require_once('../platform/system/logManager.inc');

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "error"=>"error.ihtml",
                           "mainpage"=> "setUpAlarm.ihtml" ));

$thisFound = true;

$Template->set_var("images","$IMAGES_WWW");

$log = &AgeLog::getLog();

$db = &AgeDB::getDB();

$sql = "select stdate from AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
$row = $res->fetchRow();

$stdate = explode("-", $row['stdate'] );
$lstdate = date ("l d M Y",mktime(0,0,0,$stdate[1],$stdate[2],$stdate[0]));

$Template->set_var("setUpAlarm_LSTDATE","$lstdate");

$str_help="";
if ($request == "Add this Alarm") {
    if (!ereg("@",$newaddress) && $AUTOCOMPLETEEMAIL) {
        $newaddress .= $standardEMAILDomain;
    }
    $sql = "select id,address,delay,text from ALERT where id='$ida' and address='$newaddress' and delay=$period";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    $numRows = $res->numRows();

    $sql = "select id from AGENDA where id='$ida' and CURDATE() < SUBDATE(stdate,INTERVAL $period DAY)";
    $res3 = $db->query($sql);
    if (DB::isError($res3)) {
        die ($res3->getMessage());
    }
    $numRows3 = $res3->numRows();

    if ( $numRows3 != 0 ) {
        if ( $numRows == 0 && $newaddress != "" && $ida != "") {
            $sql = "insert into ALERT values('$ida','$newaddress',$period,'$newnote','$include_agenda')";
            $res2 = $db->query($sql);
            if (DB::isError($res2)) {
                die ($res2->getMessage());
            }

            $str_help="<B>alarm added...</B><BR>";

            mail("${alarm_email}","AM: alert added.","for agenda $ida");
        }
        else {
            if ( ERRORLOG )
                $log->logError( __FILE__ . "." . __LINE__, "main", " This alarm already exists id='$ida' " );

            $str_help="<BR><B>this alarm already exists...</B><BR>";
        }
    }
    else {
        if ( ERRORLOG )
            $log->logError( __FILE__ . "." . __LINE__, "main", " Alarm cannot be set (the email sending date is past) id='$ida' " );

        $str_help="<BR><B>Sorry, this alarm cannot be set (the email sending date is past)...</B><BR>";
    }
}

$Template->set_var("str_help","$str_help");

if ($request == "delete") {
    $sql = "delete from ALERT where id='$ida' and address='$address' and delay=$period";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
}

$str_help2="";
if ($request == "Send me the email NOW" && $address != "") {
    require_once ("SendAlertEmail.php");

    if (Send_Alert_Email()) {
        if ( EXECLOG )
            $log->logExec( __FILE__ . "." . __LINE__, "main", " An email has been sent to '$address' " );

        $str_help2="<BR><B><font color=green>An email has been sent to you...</font></B><BR>";
    }
    else {
        $str_help2="<BR><B><font color=red>Email not sent!</font> Please specify the meeting chairperson email first</B><BR>";
    }
}

$Template->set_var("str_help2","$str_help2");

$str_help3="";

if ($request == "Correct this Alarm") {
    $sql = "select id from AGENDA where id='$ida' and CURDATE() < SUBDATE(stdate,INTERVAL $newperiod DAY)";
    $res3 = $db->query($sql);
    if (DB::isError($res3)) {
        die ($res3->getMessage());
    }
    $numRows3 = $res3->numRows();

    if ( $numRows3 != 0 ) {
        $sql = "update ALERT set text='$newnote',delay=$newperiod,include_agenda='$include_agenda' where id='$ida' and address='$address' and delay=$period";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
    }
    else {
        $str_help3="<BR><B>Sorry, wrong delay (the email sending date would be past)...</B><BR>";
    }
}

$Template->set_var("str_help3","$str_help3");

$sql = "select DISTINCT delay from ALERT where id='$ida' order by delay DESC";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

$str_help4="";
if ( $numRows != 0 ) {
    for ( $i = 0; $numRows > $i; $i++ ) {
        $row = $res->fetchRow();

        $sql = "select SUBDATE(stdate,INTERVAL " . $row['delay'] . " DAY) from AGENDA where id='$ida'";
        $res2 = $db->query($sql);
        if (DB::isError($res2)) {
            die ($res2->getMessage());
        }
        $numRows2 = $res2->numRows();

        $row2 = $res2->fetchRow(DB_FETCHMODE_ORDERED);
        $stdate = explode("-", $row2[ 0 ]);
        $lstdate = date ("l d M Y",mktime(0,0,0,$stdate[1],$stdate[2],$stdate[0]));
        if (ereg("Sunday",$lstdate)) {
            $lstdate = str_replace("Sunday","<font color=red><U>Sunday</U></font>",$lstdate);
        }
        $str_help4.="<BR><LI><B>D - ".$row['delay']." alarm: on $lstdate, 2PM an email will be sent to:</B><BR><TABLE bgcolor=#DDDDDD align=center><TR><TD align=left>\n";
    
        $sql = "select id,address,delay,text from ALERT where id='$ida' and delay=" . $row['delay'];
        $res3 = $db->query($sql);
        if (DB::isError($res3)) {
            die ($res3->getMessage());
        }
        $numRows3 = $res3->numRows();

        if ( $numRows3 != 0 ) {
            for ( $j = 0; $numRows3 > $j; $j++ ) {
                $row3 = $res3->fetchRow();
                $str_help4.="<IMG SRC=\"$IMAGES_WWW/mail2.gif\" ALT=\"\" valign=\"middle\"> " . $row3['address'] ;

                $str_help4.=" <A HREF=\"correctAlarm.php?ida=$ida&delay=" . $row['delay'] . "&address=" . $row3['address'] . "\"><SMALL><font color=green>(edit)</font></SMALL></A><A HREF=\"setUpAlarm.php?ida=$ida&address=" . $row3['address'] . "&period=" . $row['delay'] . "&request=delete\"><SMALL><font color=red>(delete)</font></SMALL></A><BR>\n";
            }
            $str_help4.="</TD></TR></TABLE>\n";
        }
    }
}
else {
    $str_help4="<BR><TABLE bgcolor=#DDDDDD align=center><TR><TD align=left>No Alarm set yet</TD></TR></TABLE>\n";
}

$Template->set_var("str_help4", $str_help4 );
$Template->set_var("setUpAlarm_IDA", $ida );
$Template->set_var("setUpAlarm_FID", $fid );
$Template->set_var("setUpAlarm_LEVEL", $level );
$Template->set_var("setUpAlarm_POSITION", $position );
$Template->set_var("setUpAlarm_STYLESHEET", $stylesheet );

$str_help5="";
if(isset($HTTP_COOKIE_VARS["SuE"]))
{
    // cutted print
    $str_help5="<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"Javascript\">\n";
    $SuE = $HTTP_COOKIE_VARS["SuE"];

    // cutted print
    $str_help5.="document.forms[0].address.value = '$SuE'\n";
    $str_help5.="</SCRIPT>\n";
}
$Template->set_var("str_help5","$str_help5");

$Template->pparse( "final-page" , "mainpage" );
?>