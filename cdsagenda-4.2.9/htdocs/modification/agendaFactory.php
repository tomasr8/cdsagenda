<?php
// $Id: agendaFactory.php,v 1.3.4.24 2004/07/29 10:06:07 tbaron Exp $

// agendaFactory.php --- functions which deal with the agenda life
// cycle (creation, modification...)
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// agendaFactory.php is part of CDS Agenda.
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
// required scripts

require_once '../AgeDB.php';
require_once '../AgeLog.php';
require_once '../config/config.php';
require_once '../platform/template/template.inc';
require_once '../platform/system/logManager.inc';
require_once '../platform/system/archive.inc';
require_once '../platform/authentication/sessinit.inc';

$eId = $_REQUEST["eId"];
$ida = $_REQUEST["ida"];
$ids = $_REQUEST["ids"];
$idt = $_REQUEST["idt"];

// new template
$Template = new Template($PathTemplate);
// Template set-up
$Template->set_file(array("mainpage" => $templateNamesVec["modifyAgenda.php"][$calendarActive], "error" =>"error.ihtml", "addUser" =>"addUserConfirm.ihtml"));

$db = &AgeDB::getDB();
$log = &AgeLog::getLog();

// the creator email is stored in a cookie
if ($agendaCreatorEmail != "") {
    setcookie("agendaEmail", "$agendaCreatorEmail", time() + 31104000);
}

if ((($request == "CANCEL") || ($request == "Go Back to Agenda")) &&
    $ida != "") {
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
} else if ($submit == "CANCEL" && $fid != "") {
    Header("Location: ../displayLevel.php?fid=$fid");
}

$submit = $_POST["submit"];
$request = $_POST["request"];
##########################################################
#                                                        #
#	Part I: DELETE AN AGENDA                         #
#                                                        #
##########################################################

if ($request == "DELETE AGENDA") {
    // adminDel.php returns deleted parameter set to done
    if ($deleted != "done") {
        // Delete all authorizations if any
        if ($authentication) {
            deleteAllAgendaAuthorizations($ida);
        }
        Header("Location: $AGE_WWW/manager/adminDel.php?ida[]=$ida&refreshURL=".urlencode("$AGE_WWW/modification/agendaFactory.php?ida=$ida&fid=$fid&request=DELETE+AGENDA&deleted=done"));
    }
    else {
        // Deletion was a success
        print "<SCRIPT>window.opener.location='$AGE_WWW/displayLevel.php?fid=$fid';this.window.close();</SCRIPT>";
        exit;
    }
}

#########################################################
#                                                       #
#       Part II: Agenda creation                        #
#                                                       #
#########################################################

#########################################################
#                CLONE AN AGENDA                        #
#########################################################

require_once 'agendaFactory_clone.php';

###############################
# MULTISESSION MEETING
###############################

if ($submit == "ADD AGENDA") {
    $startinghour = $_POST["startinghour"];
    $startingminutes = $_POST["startingminutes"];
    $endinghour = $_POST["endinghour"];
    $endingminutes = $_POST["endingminutes"];
    $stdy = $_POST["stdy"];
    $stdm = $_POST["stdm"];
    $stdd = $_POST["stdd"];
    $endy = $_POST["endy"];
    $endm = $_POST["endm"];
    $endd = $_POST["endd"];
    $title = $_POST["title"];
    $acomment = $_POST["acomment"];
    $mpassword = $_POST["mpassword"];
    $bld = $_POST["bld"];
    $floor = $_POST["floor"];
    $room = $_POST["room"];
    $confRoom = $_POST["confRoom"];
    $stylesheet = $_POST["stylesheet"];
    $cem = $_POST["cem"];
    $chairman = $_POST["chairman"];
    $location = $_POST["location"];
    $format = $_POST["format"];
    $confidentiality = $_POST["confidentiality"];
    $apassword = $_POST["apassword"];
    $repno = $_POST["repno"];
    $visibility = $_POST["visibility"];
    $fid = $_POST["fid"];
    $agendaCreatorEmail = $_POST["agendaCreatorEmail"];
    $warningEmail = $_POST["warningEmail"];

    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $thisyear = strftime("%Y", $time);
    $year = strftime("%y", $time);
    $startd = "$stdy-$stdm-$stdd";
    $endd = "$endy-$endm-$endd";
    $stime = "$startinghour:$startingminutes:0";
    $etime = "$endinghour:$endingminutes:0";

    $sql = "select num from COUNTER where name='noAgenda${thisyear}'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();

    if ($numRows == 0) {
        $sql2 = "INSERT INTO COUNTER
                 VALUES ('noAgenda${thisyear}',2)";
        $res2 = $db->query($sql2);
        $newid = "a${year}1";
    }
    else {
        $row = $res->fetchRow();
        $strHelp = $row['num'];
        $newid = "a${year}$strHelp";
        $newNum = (int)$row['num'] + 1;
        $sql = "update COUNTER set num=$newNum where name='noAgenda${thisyear}'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    $acomment = str_replace("\037", " ", $acomment);

    if ($mpassword == "") {
        $sec = microtime();
        $pid = getmypid();
        $msec = explode(" ", $sec, 2);
        $part1 = substr($msec[0], 2, 11);
    } else {
        $part1 = "$mpassword";
    }

    // Fix the possible presence of SMR inside the smr
    $smr = preg_replace('/smr/i', '', $smr);

    $acomment = addslashes($acomment);
    if ($bld == "") {
        $bld = 0;
    }
    // Works on report fields, if the extension is active
    if ($agendaTABLEExtension_1) {
        // Test the checkboxs for the hosted events and the partial events for events outside main site
        $hostedFlag = ($hosted == "on" ? 1 : 0);
        $partialFlag = ($partial == "on" ? 1 : 0);
        $outsideFlag = ($outside == "on" ? 1 : 0);
        $internalOnlyFlag = ($internalOnly == "on" ? 1 : 0);
        $cosponsoredFlag = ($cosponsored == "on" ? 1 : 0);
        $websiteFlag = ($website != "" ? 1 : 0);
        // The email for the report is the contact email if present
        // otherwise is built by smr and the default domain
        // standard log suffix (es: "@local.domain.com)
        // if no smr is present is left blank
        $email = "";
        if ($cem != "")
            $email = $cem;
        elseif($smr != "")
            $email = "smr".$smr.$standardEMAILDomain;
    }

    if ($confRoom == "Select:") {
        // Check if the TABLE AGENDA is currently used in extended mode
        if ($calendarActive) {
            $sql = "insert into AGENDA
                values('$title', '$newid', '$startd', '$endd',
                           '$location', '$chairman', '$cem', 'open',
                           '$part1', '$today', '$today', '$stylesheet',
                           '$format', '$confidentiality', '$apassword',
                           '$repno', '$fid', '$acomment', '', '', '$bld',
                           '$floor', '$bld-$floor-$room', 1, $hostedFlag,
                           '$collaborations', '$organizers',
                           '$localorganizers', '$limitedpartecipants',
                           '$venues', '$laboratories', '$secretary',
                           '$expparts', '$deadrequest', $partialFlag,
                           '$dayNote', '$cem', '$cosponsor', '$smr',
                           $websiteFlag, '$additionalnotes', $outsideFlag,
                           $internalOnlyFlag, '$directors',
                           '$topPersonal1title', '$topPersonal1value',
                           '$topPersonal2title', '$topPersonal2value',
                           '$bottomPersonal3title',
                           '$bottomPersonal3value',
                           '$bottomPersonal4title',
                           '$bottomPersonal4value', $cosponsoredFlag,
                           '$website', '$directorsandorganizers') ";} else {
                               // Without calendar extension
                               $sql =
                               "insert into AGENDA (title,id,stdate,endate,location,chairman,cem,`status`,an,cd,md,stylesheet,`format`,confidentiality,apassword,repno,fid,acomments,keywords,visibility,bld,floor,room,nbsession,stime,etime) values('$title','$newid','$startd','$endd','$location','$chairman','$cem','open','$part1','$today','$today','$stylesheet','$format','$confidentiality','$apassword','$repno','$fid','$acomment','','$visibility','$bld','$floor','$bld-$floor-$room',0,'$stime','$etime')";
                           }
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    } else {
        // Check if the TABLE AGENDA is currently used in extended mode
        if ($agendaTABLEExtension_1) {
            $sql = "insert into AGENDA values('$title',
                '$newid', '$startd', '$endd', '$location', '$chairman',
                '$cem', 'open', '$part1', '$today', '$today',
                '$stylesheet', '$format', '$confidentiality',
                '$apassword', '$repno', '$fid', '$acomment', '', '', '0',
                '', '$confRoom', 1, $hostedFlag, '$collaborations',
                '$organizers', '$localorganizers', '$limitedpartecipants',
                '$venues', '$laboratories', '$secretary', '$expparts',
                '$deadrequest', $partialFlag, '$dayNote', '$cem',
                '$cosponsor', '$smr', $websiteFlag, '$additionalnotes',
                $outsideFlag, $internalOnlyFlag, '$directors',
                '$topPersonal1title', '$topPersonal1value',
                '$topPersonal2title', '$topPersonal2value',
                '$bottomPersonal3title', '$bottomPersonal3value',
                '$bottomPersonal4title', '$bottomPersonal4value',
                $cosponsoredFlag, '$website',
                '$directorsandorganizers') ";} else {
                    $sql =
                        "insert into AGENDA (title,id,stdate,endate,location,chairman,cem,status,an,cd,md,stylesheet,format,confidentiality,apassword,repno,fid,acomments,keywords,visibility,bld,floor,room,nbsession,stime,etime) values('$title','$newid','$startd','$endd','$location','$chairman','$cem','open','$part1','$today','$today','$stylesheet','$format','$confidentiality','$apassword','$repno','$fid','$acomment','','$visibility','0','','$confRoom',0,'$stime','$etime')";
                }
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    // if single-session meeting, create invisible session
    if ($stylesheet == "nosession") {
        if ($confRoom == "Select:") {
            $room = "$bld-$floor-$room";
        } else {
            $room = "$confRoom";
        }
        $sql =
            "insert into SESSION (ida,ids,speriod1,stime,stitle,snbtalks,cd,md,bld,floor,room,slocation) values('$newid','s1','$startd','$stime','session 1',0,'$today','$today','$bld','$floor','$room','$location')";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    $alertText = "<SCRIPT>
        alert(\"You have just created a new meeting agenda!\\nIts modification will be restricted with the following password:\\n\\n   $part1\\n\\nEnter the password into the MODIFY input box on its display page in order to modify it.\");
         </SCRIPT> ";
    $Submessage =
        "You have just created a new event!\n\nTitle:$title\nAccess: <". ($shortAgendaURL != "" ? $shortAgendaURL : "${AGE_WWW}/fullAgenda.php?ida=") . "$newid>\n\nThe modification password for this event is: $part1\n\nFor any request or comment, please contact: $support_email";
    mail("$agendaCreatorEmail", "new event $newid created", "$Submessage");
    $message =
        "A new event has just been created!\n\nTitle: $title\nDate: $today\nAccess: <". ($shortAgendaURL != "" ? $shortAgendaURL : "${AGE_WWW}/fullAgenda.php?ida=") . "$newid>\nCreator: $agendaCreatorEmail";
    mail("${admin_email}", "new agenda $newid", "$message");
    if ($warningEmail != "") {
	$message = "A new agenda has been created, you can acess it here:\n<". ($shortAgendaURL != "" ? $shortAgendaURL : "${AGE_WWW}/fullAgenda.php?ida=") . "$newid>\nCreator: $agendaCreatorEmail";
        mail("${warningEmail}", "new agenda $newid", "$message");
    }
    Header("Location: ../displayLevel.php?fid=$fid&alertText=".
           urlencode($alertText));
exit;
}
###############################
# SIMPLE EVENT
###############################

if ($submit == "ADD EVENT") {
    setcookie("SuE", "$SuE", time() + 31104000);

    $stdy = $_POST["stdy"];
    $stdm = $_POST["stdm"];
    $stdd = $_POST["stdd"];
    $endy = $_POST["endy"];
    $endm = $_POST["endm"];
    $endd = $_POST["endd"];
    $title = $_POST["title"];
    $acomment = $_POST["acomment"];
    $mpassword = $_POST["mpassword"];
    $bld = $_POST["bld"];
    $floor = $_POST["floor"];
    $room = $_POST["room"];
    $confRoom = $_POST["confRoom"];
    $stylesheet = $_POST["stylesheet"];
    $chairman = $_POST["chairman"];
    $location = $_POST["location"];
    $format = $_POST["format"];
    $confidentiality = $_POST["confidentiality"];
    $apassword = $_POST["apassword"];
    $visibility = $_POST["visibility"];
    $fid = $_POST["fid"];
    $thstart = $_POST["thstart"];
    $tmstart = $_POST["tmstart"];
    $thend = $_POST["thend"];
    $tmend = $_POST["tmend"];
    $SuE = $_POST["SuE"];

    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $thisyear = strftime("%Y", $time);
    $year = strftime("%y", $time);
    $startd = "$stdy-$stdm-$stdd";
    $stime = "$thstart:$tmstart:0";
    $endd = "$endy-$endm-$endd";
    $etime = "$thend:$tmend:0";

    $sql = "select num from COUNTER where name='noAgenda${thisyear}'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();

    if ($numRows == 0) {
        $sql2 = "INSERT INTO COUNTER
                 VALUES ('noAgenda${thisyear}',2)";
        $res2 = $db->query($sql2);
        $newid = "a${year}1";
    }
    else {
        $row = $res->fetchRow();
        $strHelp = $row['num'];
        $newid = "a${year}$strHelp";
        $newNum = (int)$row['num'] + 1;
        $sql = "update COUNTER set num=$newNum where name='noAgenda${thisyear}'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    if ($mpassword == "") {
        $sec = microtime();
        $pid = getmypid();
        $msec = explode(" ", $sec, 2); $part1 = substr($msec[0], 2, 11);}
    else {
        $part1 = "$mpassword";}

    $acomment = addslashes($acomment);
    $acomment = str_replace("\037", " ", $acomment); if ($bld == "") {
        $bld = 0;}

    // Test the checkboxs for the hosted events and the partial events
    $hostedFlag = ($hosted == "on" ? 1 : 0);
    $partialFlag = ($partial == "on" ? 1 : 0);
    $outsideFlag = ($outside == "on" ? 1 : 0);
    $internalOnlyFlag = ($internalOnly == "on" ? 1 : 0);
    $cosponsoredFlag = ($cosponsored == "on" ? 1 : 0);
    $websiteFlag = ($website != "" ? 1 : 0);
    // The email for the report is built by smr and the default domain, that is the
    // standard log suffix (es: "@local.domain.com)
    $email = "smr".$smr.$standardEMAILDomain; if ($confRoom == "Select:") {
        // Check if the TABLE AGENDA is currently used in extended mode
        if ($agendaTABLEExtension_1) {
            $sql = "insert into AGENDA values('$title',
         '$newid', '$startd', '$endd', '$location', '$chairman', '$SuE',
         'open', '$part1', '$today', '$today', 'event', '$format', 'open',
         '$apassword', '', '$fid', '$acomment', '', '', '$bld', '$floor',
         '$bld-$floor-$room', 1, $hostedFlag, '$collaborations',
         '$organizers', '$localorganizers', '$limitedpartecipants', '$venues',
         '$laboratories', '$secretary', '$expparts', '$deadrequest',
         $partialFlag, '$dayNote', '$SuE', '$cosponsor', '$smr', $websiteFlag,
         '$additionalnotes', $outsideFlag, $internalOnlyFlag, '$directors',
         '$topPersonal1title', '$topPersonal1value', '$topPersonal2title',
         '$topPersonal2value', '$bottomPersonal3title',
         '$bottomPersonal3value', '$bottomPersonal4title',
         '$bottomPersonal4value', $cosponsoredFlag, '$website')
                ";}
        else {
            $sql =
                "insert into AGENDA (title,id,stdate,endate,location,chairman,cem,status,an,cd,md,stylesheet,format,confidentiality,apassword,repno,fid,acomments,keywords,visibility,bld,floor,room,nbsession,stime,etime) values('$title','$newid','$startd','$endd','$location','$chairman','$SuE','open','$part1','$today','$today','event','$format','$confidentiality','$apassword','','$fid','$acomment','','$visibility','$bld','$floor','$bld-$floor-$room',0,'$stime','$etime')";
        }
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }
    else {
        // Check if the TABLE AGENDA is currently used in extended mode
        if ($agendaTABLEExtension_1) {
            $sql = "insert into AGENDA values('$title',
                    '$newid', '$startd', '$endd', '$location',
                    '$chairman', '$SuE', 'open', '$part1', '$today',
                    '$today', 'event', '$format', 'open', '$apassword',
                    '', '$fid', '$acomment', '', '', '0', '', '$confRoom',
                    1, $hostedFlag, '$collaborations', '$organizers',
                    '$localorganizers', '$limitedpartecipants', '$venues',
                    '$laboratories', '$secretary', '$expparts',
                    '$deadrequest', $partialFlag, '$dayNote', '$SuE',
                    '$cosponsor', '$smr', $websiteFlag,
                    '$additionalnotes', $outsideFlag, $internalOnlyFlag,
                    '$directors', '$topPersonal1title',
                    '$topPersonal1value', '$topPersonal2title',
                    '$topPersonal2value', '$bottomPersonal3title',
                    '$bottomPersonal3value', '$bottomPersonal4title',
                    '$bottomPersonal4value', $cosponsoredFlag, '$website',
                    '$directorsandorganizers') ";} else {
                        $sql =
                            "insert into AGENDA (title,id,stdate,endate,location,chairman,cem,status,an,cd,md,stylesheet,format,confidentiality,apassword,repno,fid,acomments,keywords,visibility,bld,floor,room,nbsession,stime,etime) values('$title','$newid','$startd','$endd','$location','$chairman','$SuE','open','$part1','$today','$today','event','$format','open','$apassword','','$fid','$acomment','','$visibility','0','','$confRoom',0,'$stime','$etime')";
                    }
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    $alertText = "<SCRIPT>
            alert(\"You have just created a new event!\\nIts modification will be restricted with the following password:\\n\\n   $part1\\n\\nEnter the password into the MODIFY input box on its display page in order to modify it.\");
             </SCRIPT> ";
    $Submessage =
        "You have just created a new event!\n\nTitle:$title\nAccess: ". ($shortAgendaURL != "" ? $shortAgendaURL : "${AGE_WWW}/fullAgenda.php?ida=") . "$newid\n\nThe modification password for this event is: $part1\n\n".$newEventMSG1;
    mail("$SuE", "new event $newid created", "$Submessage");
    $message =
        "A new event has just been created!\n\nTitle: $title\nDate: $today\nAccess: ". ($shortAgendaURL != "" ? $shortAgendaURL : "${AGE_WWW}/fullAgenda.php?ida=") . "$newid\nCreator: $SuE";
    mail("${admin_email}", "new agenda $newid", "$message");
    Header
        ("Location: ../displayLevel.php?fid=$fid&alertText=".
         urlencode($alertText)); exit;}

###############################
# LECTURE
###############################

if ($submit == "ADD LECTURE") {
    setcookie("SuE", "$SuE", time() + 31104000);
    $acomment = str_replace("\037", " ", $acomment);
    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $thisyear = strftime("%Y", $time);
    $year = strftime("%y", $time);
    $startd = "$stdy-$stdm-$stdd";
    $endd = "$startd";

    $sql = "select num from COUNTER where name='noAgenda${thisyear}'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();

    if ($numRows == 0) {
        $sql2 = "INSERT INTO COUNTER
                 VALUES ('noAgenda${thisyear}',2)";
        $res2 = $db->query($sql2);
        $newid = "a${year}1";
    }
    else {
        $row = $res->fetchRow();
        $strHelp = $row['num'];
        $newid = "a${year}$strHelp";
        $newNum = (int)$row['num'] + 1;
        $sql = "update COUNTER set num=$newNum where name='noAgenda${thisyear}'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    if ($mpassword == "") {
        $sec = microtime();
        $pid = getmypid();
        $msec = explode(" ", $sec, 2); $part1 = substr($msec[0], 2, 11);}
    else {
        $part1 = "$mpassword";}

    $acomment = addslashes($acomment);
    $stime = "$thstart:$tmstart:0";
    $etime = "$thend:$tmend:0";

    if ($bld == "") {$bld = 0;}

    // Test the checkboxs for the hosted events and the partial events
    $hostedFlag = ($hosted == "on" ? 1 : 0);
    $partialFlag = ($partial == "on" ? 1 : 0);
    $outsideFlag = ($outside == "on" ? 1 : 0);
    $internalOnlyFlag = ($internalOnly == "on" ? 1 : 0);
    $cosponsoredFlag = ($cosponsored == "on" ? 1 : 0);
    $websiteFlag = ($website != "" ? 1 : 0);
    // The email for the report is built by smr and the default domain, that is the
    // standard log suffix (es: "@local.domain.com)
    $email = "smr".$smr.$standardEMAILDomain;
    if ($confRoom == "Select:") {
        // Check if the TABLE AGENDA is currently used in extended mode
        if ($agendaTABLEExtension_1) {
            $sql = "insert into AGENDA values('$title',
             '$newid', '$startd', '$endd', '$location', '$chairman', '$SuE',
             'open', '$part1', '$today', '$today', '$stylesheet', '$format',
             '$confidentiality', '$apassword', '$repno', '$fid', '', '', '',
             '$bld', '$floor', '$bld-$floor-$room', 1, '$hostedFlag',
             '$collaborations', '$organizers', '$localorganizers',
             '$limitedpartecipants', '$venues', '$laboratories', '$secretary',
             '$expparts', '$deadrequest', $partiaFlag, '$dayNote', '$SuE',
             '$cosponsor', '$smr', $websiteFlag, '$additionalnotes',
             $outsideFlag, $internalOnlyFlag, '$directors',
             '$topPersonal1title', '$topPersonal1value', '$topPersonal2title',
             '$topPersonal2value', '$bottomPersonal3title',
             '$bottomPersonal3value', '$bottomPersonal4title',
             '$bottomPersonal4value', $cosponsoredFlag, '$website',
             '$directorsandorganizers')
                    "; $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());
            }
        }
        else {
            $sql =
                "insert into AGENDA (title,id,stdate,endate,location,chairman,cem,status,an,cd,md,stylesheet,format,confidentiality,apassword,repno,fid,acomments,keywords,visibility,bld,floor,room,nbsession,stime,etime) values('$title','$newid','$startd','$endd','$location','$chairman','$SuE','open','$part1','$today','$today','$stylesheet','$format','$confidentiality','$apassword','$repno','$fid','$acomment','','$visibility','$bld','$floor','$bld-$floor-$room',0,'$stime','$etime')";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());
            }
        }
    }
    else {
        // Check if the TABLE AGENDA is currently used in extended mode
        if ($agendaTABLEExtension_1) {
            $sql = "insert into AGENDA values('$title',
                    '$newid', '$startd', '$endd', '$location', '$SuN', '$SuE',
                    'open', '$part1', '$today', '$today', '$stylesheet',
                    '$format', '$confidentiality', '$apassword', '$repno',
                    '$fid', '$acomment', '', '', '0', '', '$confRoom', 1,
                    '$hostedFlag', '$collaborations', '$organizers',
                    '$localorganizers', '$limitedpartecipants', '$venues',
                    '$laboratories', '$secretary', '$expparts',
                    '$deadrequest', $partialFlag, '$dayNote', '$SuE',
                    '$cosponsor', '$smr', $websiteFlag, '$additionalnotes',
                    $outsideFlag, $internalOnlyFlag, '$directors',
                    '$topPersonal1title', '$topPersonal1value',
                    '$topPersonal2title', '$topPersonal2value',
                    '$bottomPersonal3title', '$bottomPersonal3value',
                    '$bottomPersonal4title', '$bottomPersonal4value',
                    $cosponsoredFlag, '$website',
                    '$directorsandorganizers') "; $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());
            }
        } else {
            $sql =
                "insert into AGENDA (title,id,stdate,endate,location,chairman,cem,status,an,cd,md,stylesheet,format,confidentiality,apassword,repno,fid,acomments,keywords,visibility,bld,floor,room,nbsession,stime,etime) values('$title','$newid','$startd','$endd','$location','$chairman','$SuE','open','$part1','$today','$today','$stylesheet','$format','$confidentiality','$apassword','$repno','$fid','$acomment','','$visibility','0','','$confRoom',0,'$stime','$etime')";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());
            }
        }
    }

    $alertText = "<SCRIPT>
            alert(\"You have just created a new lecture!\\nIts modification will be restricted with the following password:\\n\\n   $part1\\n\\nEnter the password into the MODIFY input box on its display page in order to modify it.\");
             </SCRIPT> ";
    $Submessage =
        "You have just created a new lecture!\n\nTitle:$title\nAccess: ". ($shortAgendaURL != "" ? $shortAgendaURL : "${AGE_WWW}/fullAgenda.php?ida=") . "$newid\n\nThe modification password for this agenda is: $part1\n\n".
        $newLectureMSG1;
    mail("$SuE", "new lecture $newid created", "$Submessage");

    $message =
        "A new lecture has just been created!\n\nTitle: $title\nDate: $today\nAccess: ". ($shortAgendaURL != "" ? $shortAgendaURL : "${AGE_WWW}/fullAgenda.php?ida=") . "$newid\nCreator: $SuE";
    mail("${admin_email}", "new lecture $newid", "$message");

    Header
        ("Location: ../displayLevel.php?fid=$fid&alertText=".
         urlencode($alertText)); exit;}



############################################################
#
#        Part III: Agenda Modification
#
############################################################


############################################################
#        ADD SESSION
############################################################

if ($request == "ADD SESSION") {
    $startinghour = $_POST["startinghour"];
    $startingminutes = $_POST["startingminutes"];
    $endinghour = $_POST["endinghour"];
    $endingminutes = $_POST["endingminutes"];
    $stdy = $_POST["stdy"];
    $stdm = $_POST["stdm"];
    $stdd = $_POST["stdd"];
    $stitle = $_POST["stitle"];
    $scomment = $_POST["scomment"];
    $bld = $_POST["bld"];
    $floor = $_POST["floor"];
    $room = $_POST["room"];
    $confRoom = $_POST["confRoom"];
    $scem = $_POST["scem"];
    $chairman = $_POST["chairman"];
    $slocation = $_POST["slocation"];
    $broadcasturl = $_POST["broadcasturl"];
    $position = $_POST["position"];
    $stylesheet = $_POST["stylesheet"];
    $AN = $_POST["AN"];

    $speriod1 = "$stdy-$stdm-$stdd";
    $eperiod1 = "$speriod1";
    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $scomment = str_replace("\037", " ", $scomment);
    $sql = "select nbsession from AGENDA where id='$ida'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows(); if (($numRows == 0) && (ERRORLOG)) {
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' ");}

    $row = $res->fetchRow();
    $ids = "s".$row['nbsession'];
    $newNum = (int)($row['nbsession']) + 1;
    $sql = "update AGENDA set nbsession=$newNum where id='$ida'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    if ($bld == "") {
        $bld = 0;}
    if ($startinghour == "") {
        $startinghour = 0;}
    if ($startingminutes == "") {
        $startingminutes = 0;}
    if ($endinghour == "") {
        $endinghour = 0;}
    if ($endingminutes == "") {
        $endingminutes = 0;}

    if ($endinghour == 0 && $endingminutes == 0) {
        $endinghour = "$startinghour";
        $endingminutes = "$startingminutes";}

    if ($confRoom == "Select:") {
        $sql =
            "insert into SESSION (ida,ids,schairman,speriod1,stime,eperiod1,etime,stitle,snbtalks,slocation,scem,sstatus,bld,floor,room,broadcasturl,cd,md,scomment) values('$ida','$ids','$schairman','$speriod1','$startinghour:$startingminutes:00','$eperiod1','$endinghour:$endingminutes:00','$stitle',1,'$slocation','$scem','open','$bld','$floor','$bld-$floor-$room','$broadcasturl','$today','$today','$scomment')";}
    else {
        $sql =
            "insert into SESSION (ida,ids,schairman,speriod1,stime,eperiod1,etime,stitle,snbtalks,slocation,scem,sstatus,bld,floor,room,broadcasturl,cd,md,scomment) values('$ida','$ids','$schairman','$speriod1','$startinghour:$startingminutes:00','$eperiod1','$endinghour:$endingminutes:00','$stitle',1,'$slocation','$scem','open','0','','$confRoom','$broadcasturl','$today','$today','$scomment')";}

    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    lastModifiedAgenda();
    Header("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

############################################################
#        MODIFY AGENDA
############################################################

if ($request == "MODIFY AGENDA") {
    $topicFields = true;

    $sql = "select stdate,room,an from AGENDA where id='$ida'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' ");
    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $startd = "$stdy-$stdm-$stdd";
    $stime = "$startinghour:$startingminutes:0";
    $etime = "$endinghour:$endingminutes:0";
    $acomment = str_replace("\037", " ", $acomment);
    $GLOBALS["log"]->logDebug(__FILE__, __LINE__, "SMR: '$smr' ");
    $smr = preg_replace('/smr/i', '', $smr);
    $GLOBALS["log"]->logDebug(__FILE__, __LINE__, "SMR: '$smr' ");
    $row = $res->fetchRow();
    $oldroom = $row['room'];
    $oldan = $row['an'];
    //if starting date changes, we also change the sessions and talks.
    if ($row['stdate'] != "$startd" && $globalchange == "on") {
        $sql =
            "update SESSION set speriod1=FROM_DAYS(TO_DAYS(speriod1) + (TO_DAYS('$startd') - TO_DAYS('".$row['stdate']."'))),eperiod1=FROM_DAYS(TO_DAYS(eperiod1) + (TO_DAYS('$startd') - TO_DAYS('".$row['stdate']."'))) where ida='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }

        $sql =
            "update TALK set tday=FROM_DAYS(TO_DAYS(tday) + (TO_DAYS('$startd') - TO_DAYS('".$row['stdate']."'))) where ida='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }

    }
    if ($stylesheet == "nosession") {
        $sql = "
            UPDATE SESSION
            SET speriod1='$startd',
                eperiod1='$startd'
            WHERE ida='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
        $sql ="
            UPDATE TALK
            SET tday='$startd'
            WHERE ida='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }


    if ($stylesheet != "nosession")
        $endd = "$endy-$endm-$endd";
    else
        $endd = "$startd";
    if ($confRoom != "Select:") {
        $bld = 0; $floor = ""; $room = $confRoom;}
    else
        $room = "$bld-$floor-$room";
    if (!$calendarActive) {
        // Not active the calendar
        if ($topicFields) {
            $sql = "update AGENDA set
             title = '$agenda', stdate = '$startd', endate =
             '$endd', location = '$location', chairman = '$chairman', cem =
             '$cem', md = '$today', status = '$status', format =
             '$format', an = '$an', confidentiality =
             '$confidentiality', apassword = '$apassword', repno =
             '$repno', acomments = '$acomment', bld = '$bld', floor =
             '$floor', room = '$room', stylesheet =
             '$newstylesheet', stime = '$stime', etime = '$etime'
             , visibility = '$visibility' where id = '$ida' ";
        }
        else {
            $sql = "update AGENDA set
             md = '$today', status = '$status', format =
             '$format', confidentiality = '$confidentiality', acomments =
             '$acomment', stylesheet = '$newstylesheet', stime = '$stime',
             etime = '$etime', visibility = '$visibility' where id = '$ida' ";
        }
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }

        $sql = "update SESSION set
             bld = '$bld', floor = '$floor', room = '$room' where ida =
             '$ida' and room = '$oldroom' ";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());}

        $sql = "update TALK set
             bld = '$bld', floor = '$floor', room = '$room' where ida =
             '$ida' and room = '$oldroom' ";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());}

        if ($stylesheet == "nosession") {
            $sql = "update SESSION set
             bld = '$bld', floor = '$floor', room = '$room', eperiod1='$startd' where ida =
             '$ida' ";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());}
        }
    }
    else {
        // calendar Active
        $hostedFlag = ($hosted == "on" ? 1 : 0);
        $partialFlag = ($partialevent == "on" ? 1 : 0);
        $outsideFlag = ($outside == "on" ? 1 : 0);
        $internalOnlyFlag = ($internalOnly == "on" ? 1 : 0);
        $cosponsoredFlag = ($cosponsored == "on" ? 1 : 0);
        // Calendar and table extension active
        if ($topicFields) {
            $sql =
                "update AGENDA set outside=$outsideFlag,additionalNotes='$additionalnotes',title='$agenda',stdate='$startd',endate='$endd',location='$location',chairman='$chairman',cem='$cem',md='$today',status='$status',format='$format',an='$an',confidentiality='$confidentiality',apassword='$apassword',repno='$repno',acomments='$acomment',bld='$bld',floor='$floor',room='$room',stylesheet='$newstylesheet', stime = '$stime', etime = '$etime',
             hosted = $hostedFlag, collaborations =
             '$collaborations', organizers = '$organizers', localorganizers =
             '$localorganizers', limitedpartecipations =
             '$limitedpartecipations', venues = '$venues', laboratories =
             '$laboratories', secretary = '$secretary', expparts =
             '$expparts', deadrequest = '$deadrequest', partialEvent =
             $partialFlag, internalOnly = $internalOnlyFlag, dayNote =
             '$daynote', cosponsor = '$cosponsor', smr = '$smr', email =
             '$cem', directors = '$directors', personal1title =
             '$topPersonal1title', personal2title =
             '$topPersonal2title', personal3title =
             '$bottomPersonal3title', personal4title =
             '$bottomPersonal4title', personal1value =
             '$topPersonal1value', personal2value =
             '$topPersonal2value', personal3value =
             '$bottomPersonal3value', personal4value =
             '$bottomPersonal4value', directorsandorganizers =
             '$directorsandorganizers', cosponsored =
             $cosponsoredFlag, websitelink =
             '$websiteLink' ".( $websiteLink != ", webSite =
             1 " ? ", webSite = 0 " : " ")." where id = '$ida' ";}
        else {
            $sql = "update AGENDA set additionalNotes='$additionalnotes',
             md = '$today', status = '$status', format =
             '$format', confidentiality = '$confidentiality', acomments =
             '$acomment', stylesheet = '$newstylesheet', collaborations =
             '$collaborations', organizers = '$organizers', localorganizers =
             '$localorganizers', limitedpartecipations =
             '$limitedpartecipations', laboratories =
             '$laboratories', expparts = '$expparts', deadrequest =
             '$deadrequest', partialEvent = $partialFlag, dayNote =
             '$daynote', cosponsor = '$cosponsor', directors =
             '$directors', personal1title =
             '$topPersonal1title', personal2title =
             '$topPersonal2title', personal3title =
             '$bottomPersonal3title', personal4title =
             '$bottomPersonal4title', personal1value =
             '$topPersonal1value', personal2value =
             '$topPersonal2value', personal3value =
             '$bottomPersonal3value', personal4value =
             '$bottomPersonal4value', directorsandorganizers =
             '$directorsandorganizers', cosponsored =
             $cosponsoredFlag, websitelink =
             '$websiteLink' ".( $websiteLink != ", webSite =
             1 " ? ", webSite = 0 " : " ")." where id = '$ida' ";}
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());}
    }

    if ($status == "close") {
        $sql = "update SESSION set sstatus='$status' where ida='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    lastModifiedAgenda();
    Header("Location: $AGE_WWW/access.php?ida=$ida&AN=$an&position=$position");
    exit;
}


############################################################
#	COMPUTE SESSIONS TIME
############################################################

if ($request == "COMPUTE SESSION TIME") {
    $db->autocommit(false);
    //$GLOBALS[ "table" ]->startTransaction();
    $sql = "select ids from SESSION where ida='$ida'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();
    if ($numRows != 0) {
        for ($i = 0; $numRows > $i; $i++) {
            $row = $res->fetchRow();
            updateSessionStartTime($row['ids']);
        }
    }

    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}


############################################################
#        DELETE SESSION
############################################################

if ($request == "DELETE SESSION") {
    $db->autocommit(false);
    // $GLOBALS[ "table" ]->startTransaction();
    $sql = "delete from SESSION where ida='$ida' and ids='$ids'";
    $dses = $db->query($sql); $err = false; if (DB::isError($dses)) {
        $err = true;}

    if (!$err) {
        $sql = "delete from TALK where ida='$ida' and ids='$ids'";
        $dtalk = $db->query($sql); if (DB::isError($dtalk)) {
            $err = true;}
    }
    // FIXME: MUST DELETE ALSO THE SUBTALKS
    if ($err) {
        //$GLOBALS[ "table" ]->rollbackTransaction();
        $db->rollback(); if (LOGGING)
            $GLOBALS["log"]->logDebug(__FILE__, __LINE__,
                                      " Cannot delete session '$request' with id '$ida$ids' ");
        outError
            (" An Error Occurred while deleting SESSION with id '$ida$ids' ",
             "10b", $Template); exit;}
    $db->commit();
    lastModifiedAgenda();
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}

############################################################
#        MODIFY SESSION
############################################################
if ($request == "MODIFY SESSION") {
    $startinghour = $_POST["startinghour"];
    $startingminutes = $_POST["startingminutes"];
    $endinghour = $_POST["endinghour"];
    $endingminutes = $_POST["endingminutes"];
    $stdy = $_POST["stdy"];
    $stdm = $_POST["stdm"];
    $stdd = $_POST["stdd"];
    $stitle = $_POST["stitle"];
    $scomment = $_POST["scomment"];
    $bld = $_POST["bld"];
    $floor = $_POST["floor"];
    $room = $_POST["room"];
    $confRoom = $_POST["confRoom"];
    $scem = $_POST["scem"];
    $chairman = $_POST["chairman"];
    $slocation = $_POST["slocation"];
    $broadcasturl = $_POST["broadcasturl"];
    $position = $_POST["position"];
    $stylesheet = $_POST["stylesheet"];
    $AN = $_POST["AN"];

    $sql =
        "select speriod1,slocation,room from SESSION where ida='$ida' and ids='$ids'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' "); $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $startd = "$stdy-$stdm-$stdd";
    if ($bld == "") {
        $bld = 0;
    }
    $scomment = str_replace("\037", " ", $scomment);
    $row = $res->fetchRow();
    //If any talk in the session has the same starting day, we modify
    //also its date.
    if ($row['speriod1'] != "$startd") {
        $sql =
            "update TALK set tday='$startd' where ida='$ida' and ids='$ids'";
        $res = $db->query($sql); if (DB::isError($res)) {
            die($res->getMessage());}
    }

    //We also modify the location of all talks being at the same
    //previous location.
    if ($row['slocation'] != "$slocation") {
        $sql = "update TALK set location='$slocation'
             where ida = '$ida' and ids = '$ids' and location = '" .
            $row['slocation']."'";
        $res = $db->query($sql); if (DB::isError($res)) {
            die($res->getMessage());}
    }

    if ($confRoom != "Select:") {
        if ($row['room'] != "$confRoom") {
            $sql =
                "update TALK set bld='0',floor='',room='$confRoom' where ida='$ida' and ids='$ids' and room='".
                $row['room']."'"; $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());}
        }
    }
    else
        if ($row['room'] != "$bld-$floor-$room") {
            $sql =
                "update TALK set bld='$bld',floor='$floor',room='$bld-$floor-$room' where ida='$ida' and ids='$ids' and room='".
                $row['room']."'"; $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());}
        }


    $endd = "$startd";
    if ($startinghour == "") {
        $startinghour = 0;}
    if ($startingminutes == "") {
        $startingminutes = 0;}
    if ($endinghour == "") {
        $endinghour = 0;}
    if ($endingminutes == "") {
        $endingminutes = 0;}

    if ($endinghour == 0 && $endingminutes == 0) {
        $endinghour = $startinghour; $endingminutes = $startingminutes;}


    if ($confRoom != "Select:") {
        $sql =
            "update SESSION set stitle='$session',speriod1='$startd',stime='$startinghour:$startingminutes:00',eperiod1='$endd',etime='$endinghour:$endingminutes:00',slocation='$slocation',schairman='$schairman',scem='$scem',sstatus='$status',bld='0',floor='',room='$confRoom',broadcasturl='$broadcasturl',md='$today',scomment='$scomment' where ida='$ida' and ids='$ids'";
        $res = $db->query($sql); if (DB::isError($res)) {
            die($res->getMessage());}
    }
    else {
        $sql =
            "update SESSION set stitle='$session',speriod1='$startd',stime='$startinghour:$startingminutes:00',eperiod1='$endd',etime='$endinghour:$endingminutes:00',slocation='$slocation',schairman='$schairman',scem='$scem',sstatus='$status',bld='$bld',floor='$floor',room='$bld-$floor-$room',broadcasturl='$broadcasturl',md='$today',scomment='$scomment' where ida='$ida' and ids='$ids'";
        $res = $db->query($sql); if (DB::isError($res)) {
            die($res->getMessage());}
    }

    Header("Location: displayAgenda.php?ida=$ida&position=$position");

    exit;
}


############################################################
#        DELETE TALK
############################################################
if ($request == "DELETE TALK") {
    //$GLOBALS[ "table" ]->startTransaction();
    $db->autocommit(false);
    $sql =
        "delete from SUBTALK where ida='$ida' and ids='$ids' and fidt='$idt'";
    $res = $db->query($sql); $err = false; if (DB::isError($res)) {
        $err = true;}

    if (!$err) {
        $sql =
            "delete from TALK where ida='$ida' and ids='$ids' and idt='$idt'";
        $res = $db->query($sql); if (DB::isError($res)) {
            $err = true;}
    }
    if ($err) {
        //$GLOBALS[ "table" ]->rollbackTransaction();
        $db->rollback(); if (LOGGING)
            $GLOBALS["log"]->logDebug(__FILE__, __LINE__,
                                      " Cannot delete talk or subtalk '$request' with id '$idaids$idt'");
        outError
            (" An Error Occurred while deleting TALK/SUBTALK with id '$ida$ids$idt' ",
             "12b", $Template); exit;}

    $db->commit();
    lastModifiedSession($ids);
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}

############################################################
#        DELETE SUB TALK
############################################################

if ($request == "DELETE SUB TALK") {

    //$GLOBALS[ "table" ]->startTransaction();
    $db->autocommit(false);
    $sql =
        "delete from SUBTALK where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql); if (DB::isError($res)) {
        //$GLOBALS[ "table" ]->rollbackTransaction();
        $db->rollback(); if (LOGGING)
            $GLOBALS["log"]->logDebug(__FILE__, __LINE__,
                                      " Error removing Subtalk '$request' ");
        outError
            (" An Error occurred while removing the SubTalk with id '$ida$ids$idt' ",
             "13b", $Template); exit;}
    $db->commit();
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}

############################################################
#        ADD TALK
############################################################

if ($request == "ADD TALK") {

    $tday = "$stdy-$stdm-$stdd";
    $time = time();
    $today = strftime("%Y-%m-%d", $time); if ($thstart == "") {
        $thstart = "0";}
    if ($tmstart == "") {
        $tmstart = "0";}
    $stime = "$thstart:$tmstart:0"; if ($bld == "") {
        $bld = "0";}
    $duration = "$durationh:$durationm:0";
    $tcomment = str_replace("\037", " ", $tcomment);
    $sql =
        "select snbtalks from SESSION where ida='$ida' and ids='$ids'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows(); if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' "); $row = $res->fetchRow();
    $idt = "t".$row['snbtalks'];
    $newNum = (int)($row['snbtalks']) + 1;
    $sql =
        "update SESSION set snbtalks=$newNum where ida='$ida' and ids='$ids'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    if ($confRoom != "Select:") {
        $sql =
            "insert into TALK (ida,ids,idt,ttitle,tspeaker,tday,tcomment,location,bld,floor,room,broadcasturl,type,cd,md,category,stime,repno,affiliation,duration,email".
            ($TALKKEYWORDSActive ? ",keywords" : "").
            ") values('$ida','$ids','$idt','$ttitle','$tspeaker','$tday','$tcomment','$location','$bld','$floor','$confRoom','',$ttype,'$today','$today','$category','$stime','$repno','$affiliation','$duration','$temail'".
            ($TALKKEYWORDSActive ? ",'$keywords'" : "").")";}
    else {
        $sql =
            "insert into TALK (ida,ids,idt,ttitle,tspeaker,tday,tcomment,location,bld,floor,room,broadcasturl,type,cd,md,category,stime,repno,affiliation,duration,email".
            ($TALKKEYWORDSActive ? ",keywords" : "").
            ") values('$ida','$ids','$idt','$ttitle','$tspeaker','$tday','$tcomment','$location','$bld','$floor','$bld-$floor-$room','',$ttype,'$today','$today','$category','$stime','$repno','$affiliation','$duration','$temail'".
            ($TALKKEYWORDSActive ? ",'$keywords'" : "").")";}
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    lastModifiedSession($ids);
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}

############################################################
#        ADD SUB TALK
############################################################

if ($request == "ADD SUB TALK") {

    $tday = "$stdy-$stdm-$stdd";
    $time = time();
    $today = strftime("%Y-%m-%d", $time); if ($thstart == "") {
        $thstart = "0";}
    if ($tmstart == "") {
        $tmstart = "0";}
    $duration = "$durationh:$durationm:0";
    $tcomment = str_replace("\037", " ", $tcomment);

    // retrieve subtalk fake start time (ordering issue)
    $sql = "SELECT MAX(TIME_TO_SEC(stime)) as stimemax
            FROM SUBTALK
            WHERE ida='$ida' and ids='$ids' and fidt='$fidt'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        $stime = $row['stimemax'] + 1;
    }
    else {
        $stime = 1;
    }

    //get new talk id
    $sql =
        "select snbtalks from SESSION where ida='$ida' and ids='$ids'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows(); if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' "); $row = $res->fetchRow();
    $idt = "t".$row['snbtalks'];
    $newNum = (int)($row['snbtalks']) + 1;
    $sql =
        "update SESSION set snbtalks=$newNum where ida='$ida' and ids='$ids'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    $sql =
        "insert into SUBTALK (ida,ids,idt,ttitle,tspeaker,tday,tcomment,type,cd,md,stime,repno,affiliation,duration,fidt,email) values('$ida','$ids','$idt','$ttitle','$tspeaker','$tday','$tcomment',$ttype,'$today','$today',SEC_TO_TIME('$stime'),'$repno','$affiliation','$duration','$fidt','$temail')";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}

############################################################
#        ADD BROADCAST LINK TO TALK
############################################################
if ($request == "SET BROADCAST LINK ON TALK") {

    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $sql =
        "update TALK set broadcasturl='$broadcasturl' where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }

    lastModifiedTalk($ids, $idt);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

############################################################
#        MODIFY TALK
############################################################
if ($request == "MODIFY TALK") {

    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $startd = "$stdy-$stdm-$stdd"; if ($thstart == "") {
        $thstart = "0";}
    if ($tmstart == "") {
        $tmstart = "0";}
    $stime = "$thstart:$tmstart:0"; if ($bld == "") {
        $bld = 0;}
    $duration = "$durationh:$durationm:0";
    $sql =
        "select ttitle,tday,stime from TALK where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' ");
    if ($numRows != 0) {
        $row = $res->fetchRow();
        if ($row['ttitle'] != "") {
            $sql =
                "update TALK set tday='$startd',stime='$stime' where ttitle='' and tday='".
                $row['tday']."' and stime='".$row['stime']."'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());
            }
        }
    }
    $keywordsStr =
        ($TALKKEYWORDSActive ? ",keywords='$keywords' " : "");
    if ($confRoom == "Select:") {
        $sql =
            "update TALK set ttitle='$title'$keywordsStr,tspeaker='$tspeaker',tcomment='$tcomment',location='$location',bld='$bld',floor='$floor',room='$bld-$floor-$room',type=$ttype,md='$today',repno='$repno',affiliation='$affiliation',stime='$stime',category='$category',duration='$duration',email='$temail' where ida='$ida' and ids='$ids' and idt='$idt'";
    }
    else {
        $sql =
            "update TALK set ttitle='$title'$keywordsStr,tspeaker='$tspeaker',tcomment='$tcomment',location='$location',bld='0',floor='',room='$confRoom',type=$ttype,md='$today',repno='$repno',affiliation='$affiliation',stime='$stime',category='$category',duration='$duration',email='$temail' where ida='$ida' and ids='$ids' and idt='$idt'";
    }
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    lastModifiedSession($ids);
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
    Header("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

############################################################
#        MODIFY LECTURE
############################################################

if ($request == "MODIFY LECTURE") {
    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $startd = "$stdy-$stdm-$stdd";
    if ($thstart == "") {
        $thstart = "0";}
    if ($tmstart == "") {
        $tmstart = "0";}
    if ($thend == "") {
        $thend = "0";}
    if ($tmend == "") {
        $tmend = "0";}
    $stime = "$thstart:$tmstart:0";
    $etime = "$thend:$tmend:0";
    if ($bld == "") {
        $bld = 0;
    }

    $sql =
        "update AGENDA set title='$title',an='$mpassword',chairman='$tspeaker',cem='$SuE',bld='$bld',floor='$floor',room='".($confRoom != "Select:" ? $confRoom : "$bld-$floor-$room")."',stdate='$startd',stime='$stime',etime='$etime',location='$location',acomments='$tcomment',md='$today',visibility='$visibility',confidentiality='$confidentiality',apassword='$apassword' where id='$ida'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    Header("Location: displayAgenda.php?ida=$ida&position=$position");

    exit;
}

############################################################
#        MODIFY SUB TALK
############################################################

if ($request == "MODIFY SUB TALK") {

    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $duration = "$durationh:$durationm:0";
    $tcomment = str_replace("\037", " ", $tcomment);
    $sql =
        "update SUBTALK set ttitle='$title',tspeaker='$tspeaker',tcomment='$tcomment',md='$today',repno='$repno',affiliation='$affiliation',duration='$duration',email='$temail' where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }

    Header("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}


############################################################
#        COMPUTE DURATION
############################################################
if ($request == "COMPUTE DURATION") {
    if (!$ids) {
	$sql = "SELECT ids
                FROM SESSION
	        WHERE ida='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
	while ($row = $res->fetchRow()) {
	   ComputeTalkDuration($row['ids'],$ida,$sessionscale);
	}
    }
    else {
	ComputeTalkDuration($ids,$ida,$sessionscale);
    }
    lastModifiedSession($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

function ComputeTalkDuration($ids,$ida,$sessionscale)
{
    global $stylesheet;
    $db = &AgeDB::getDB();

    $sql =
        "select idt,tday,TIME_TO_SEC(stime) AS stimeinsec from TALK where ida='$ida' and ids='$ids'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' ");
    while( $row = $res->fetchRow() ) {
        $idt = $row['idt'];
        $stime = $row['stimeinsec'];
        $tday = $row['tday'];
        $sql =
            "select TIME_TO_SEC(stime) AS stimeinsec from TALK where ida='$ida' and ids='$ids' and idt!='$idt' and tday='$tday' and TIME_TO_SEC(stime)>$stime order by stime LIMIT 1";
        $res2 = $db->query($sql);
        if (DB::isError($res2)) {
            die($res2->getMessage());
        }
        $numRows2 = $res2->numRows();
        if ($numRows2 != 0) {
            $row2 = $res2->fetchRow();
            $etime = $row2['stimeinsec'];
            if ($etime - $stime - 60 * $sessionscale > 0) {
                $sql =
                    "update TALK set duration=SEC_TO_TIME($etime-$stime-60*$sessionscale) where ida='$ida' and ids='$ids' and idt='$idt'";
                $res3 = $db->query($sql);
                if (DB::isError($res3)) {
                    die($res3->getMessage());
                }
            }
        }
    }
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
}

############################################################
#        COMPUTE TIME
############################################################

if ($request == "COMPUTE TIME") {
    if (!$ids) {
	$sql = "SELECT ids
                FROM SESSION
	        WHERE ida='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
	while ($row = $res->fetchRow()) {
	   ComputeTalkStartingTime($row['ids'],$ida,$sessionscale);
	}
    }
    else {
	ComputeTalkStartingTime($ids,$ida,$sessionscale);
    }
    // update session's starting time and modification date
    lastModifiedSession($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

function ComputeTalkStartingTime($ids,$ida,$sessionscale)
{
    global $stylesheet;
    $db = &AgeDB::getDB();

    $sql =
        "select tday,stime,duration,idt from TALK where ida='$ida' and ids='$ids' order by tday,stime";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' ");
    while ($row = $res->fetchRow()) {
        $sql =
            "select TIME_TO_SEC(stime) AS stimeinsec,TIME_TO_SEC(duration) AS durationinsec from TALK where tday='".
            $row['tday']."' and ida='$ida' and ids='$ids' and idt!='".
            $row['idt']."' and stime<='".$row['stime'].
            "' order by stime DESC LIMIT 1";
        $res2 = $db->query($sql);
        if (DB::isError($res2)) {
            die($res2->getMessage());}
        $numRows = $res2->numRows();
        if ($numRows != 0) {
            $row2 = $res2->fetchRow();
            $stimeinsec = $row2['stimeinsec'];
            $durationinsec = $row2['durationinsec'];
            $sql =
                "update TALK set stime=SEC_TO_TIME($stimeinsec+$durationinsec+60*$sessionscale) where ida='$ida' and ids='$ids' and idt='".
                $row['idt']."'";
            $res3 = $db->query($sql);
            if (DB::isError($res3)) {
                die($res3->getMessage());}
        }
    }
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
}

############################################################
#        MOVE TALK
############################################################

if ($request == "moveto") {

    if ( $ids != $newsession ) {
        // New archive object
        $archive = new archive();
        $files = array();

        $sql =
        "select snbtalks,speriod1 from SESSION where ida='$ida' and ids='$newsession'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());}
        $numRows = $res->numRows();
        if (($numRows == 0) && (ERRORLOG))
            $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                      " Retrieved 0 elements with select '".
                                  $sql."' ");
        $row = $res->fetchRow();
        $newdate = $row['speriod1'];
        $newmainidt = "t".$row['snbtalks'];
        $newNum = (int)($row['snbtalks']) + 1;
        $sql =
            "update SESSION set snbtalks=$newNum where ida='$ida' and ids='$newsession'";
        $res2 = $db->query($sql);
        if (DB::isError($res2)) {
            die($res2->getMessage());}
        $sql =
            "update TALK set ids='$newsession',idt='$newmainidt',tday='$newdate' where ida='$ida' and ids='$ids' and idt='$idt'";
        $res3 = $db->query($sql);
        if (DB::isError($res3)) {
            die($res3->getMessage());}

        if (is_dir("$ARCHIVE/$ida/$ida$ids$idt")) {
            `mv $ARCHIVE/$ida/$ida$ids$idt $ARCHIVE/$ida/$ida$newsession$newmainidt `;
            // delete old files from database
            $files = $archive->listFiles("$ida$ids$idt");
            for ($i=0;$i<count($files);$i++) {
                $file = $files[$i];
                $fileID = $file->getField('id');
                $archive->deleteFile($fileID);
            }
            // add new ones
            $archive->synchronize("$ida$newsession$newmainidt");
        }

        $sql =
            "select idt from SUBTALK where ida='$ida' and ids='$ids' and fidt='$idt'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());}
        $numRows = $res->numRows();
        if (($numRows == 0) && (ERRORLOG))
            $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                      " Retrieved 0 elements with select '".
                                      $sql."' ");
        while ($row = $res->fetchRow()) {
            $oldidt = $row['idt'];
            $sql =
                "select snbtalks from SESSION where ida='$ida' and ids='$newsession'";
            $res2 = $db->query($sql);
            if (DB::isError($res2)) {
                die($res2->getMessage());}
            $numRows = $res2->numRows();
            $row2 = $res2->fetchRow();
            $newidt = "t".$row2['snbtalks'];
            $newNum = (int)($row2['snbtalks']) + 1;
            $sql =
                "update SESSION set snbtalks=$newNum where ida='$ida' and ids='$newsession'";
            $res3 = $db->query($sql);
            if (DB::isError($res3)) {
                die($res3->getMessage());}

            $sql =
                "update SUBTALK set ids='$newsession',idt='$newidt',fidt='$newmainidt' where ida='$ida' and ids='$ids' and idt='$oldidt' and fidt='$idt'";
            $res4 = $db->query($sql);
            if (DB::isError($res4)) {
                die($res4->getMessage());}
            if (is_dir("$ARCHIVE/$ida/$ida$ids$oldidt")) {
                `mv $ARCHIVE/$ida/$ida$ids$oldidt $ARCHIVE/$ida/$ida$newsession$newidt `;
                // delete old files from database
                $files = $archive->listFiles("$ida$ids$oldidt");
                for ($i=0;$i<count($files);$i++) {
                    $file = $files[$i];
                    $fileID = $file->getField('id');
                    $archive->deleteFile($fileID);
                }
                // add new ones
                $archive->synchronize("$ida$newsession$newidt");
            }
        }

        lastModifiedTalk($newsession, $newmainidt);
        if ($stylesheet == "nosession") {
            updateSessionStartTime($ids);
            updateSessionStartTime($newsession);}
    }

    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

############################################################
#        MOVE SUBTALK UP
############################################################

if ($request == "subtalkup")
{
    // First get father id
    $sql = "SELECT fidt,ida,ids
            FROM SUBTALK
            WHERE ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        $fidt = $row['fidt'];
        $ids = $row['ids'];
        $ida = $row['ida'];
        // get all brother subtalks in array
        $brothers = array();
        $sql = "SELECT idt
                FROM SUBTALK
                WHERE ida='$ida' and ids='$ids' and fidt='$fidt'
                ORDER BY stime, ttitle";
        $res = $db->query($sql);
        while ($row = $res->fetchRow()) {
            array_push($brothers,$row['idt']);
        }
        $myplace = array_search($idt,$brothers);
        // if the subtalk is not the first
        if ($myplace != 0) {
            $brothers[$myplace] = $brothers[$myplace-1];
            $brothers[$myplace-1] = "$idt";

            // then update the stimes
            for ($i=0;$i<count($brothers);$i++) {
                $sql = "UPDATE SUBTALK
                        SET stime=SEC_TO_TIME('$i')
                        WHERE ida='$ida' and ids='$ids' and idt='".$brothers[$i]."'";
                $res = $db->query($sql);
            }
        }
    }

    Header ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

############################################################
#        MOVE SUBTALK DOWN
############################################################

if ($request == "subtalkdown")
{
    // First get father id
    $sql = "SELECT fidt,ida,ids
            FROM SUBTALK
            WHERE ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        $fidt = $row['fidt'];
        $ids = $row['ids'];
        $ida = $row['ida'];
        // get all brother subtalks in array
        $brothers = array();
        $sql = "SELECT idt
                FROM SUBTALK
                WHERE ida='$ida' and ids='$ids' and fidt='$fidt'
                ORDER BY stime, ttitle";
        $res = $db->query($sql);
        while ($row = $res->fetchRow()) {
            array_push($brothers,$row['idt']);
        }
        $myplace = array_search($idt,$brothers);
        // if the subtalk is not the last
        if ($myplace != count($brothers)-1) {
            $brothers[$myplace] = $brothers[$myplace+1];
            $brothers[$myplace+1] = "$idt";

            // then update the stimes
            for ($i=0;$i<count($brothers);$i++) {
                $sql = "UPDATE SUBTALK
                        SET stime=SEC_TO_TIME('$i')
                        WHERE ida='$ida' and ids='$ids' and idt='".$brothers[$i]."'";
                $res = $db->query($sql);
            }
        }
    }

    Header ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

############################################################
#        DELAY TALK
############################################################

if ($request == "delay") {
    $sql =
        "select stime from TALK where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    $row = $res->fetchRow();
    $stime = $row['stime'];
    $array = explode(":", $stime, 3);
    $hours = $array[0];
    $minutes = $array[1];
    $newmin = $scale + $minutes; if ($newmin >= 60) {
        $newmin = $newmin - 60; $newhour = $hours + 1;}
    else {
        $newhour = $hours;}
    if ($newhour >= 24) {
        $newhour = $hours; $newmin = $minutes;}
    $newtime = "$newhour:$newmin:0";
    $sql =
        "update TALK set stime='$newtime' where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}

    lastModifiedTalk($ids, $idt);
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

############################################################
#        DELAY ALL TALKS
############################################################

if ($request == "delayall") {

    $sql =
        "select stime,tday from TALK where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' "); $row = $res->fetchRow();
    $day = "".$row['tday']; $stime = "".$row['stime'];
    $sql =
        "select stime,idt from TALK where ida='$ida' and ids='$ids' and tday='$day' and stime>='$stime' ";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' ");

    while ($row = $res->fetchRow()) {
        $stime = $row['stime'];
        $array = explode(":", $stime, 3);
        $hours = $array[0];
        $minutes = $array[1];
        $newmin = $scale + $minutes;
        if ($newmin >= 60) {
            $newmin = $newmin - 60; $newhour = $hours + 1;}
        else {
            $newhour = $hours;}
        if ($newhour >= 24) {
            $newhour = $hours; $newmin = $minutes;}
        $newtime = "$newhour:$newmin:0";
        $sql =
            "update TALK set stime='$newtime' where ida='$ida' and ids='$ids' and idt='".
            $row['idt']."'";
        $res2 = $db->query($sql);
        if (DB::isError($res2)) {
            die($res2->getMessage());}

        lastModifiedTalk($ids, $row['idt']);
    }

    // update the starting time of the session
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}


############################################################
#        ADVANCE TALK
############################################################
if ($request == "advance") {

    $sql =
        "select stime from TALK where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' "); $row = $res->fetchRow();
    $stime = $row['stime']; $array = explode(":", $stime, 3);
    $hours = $array[0]; $minutes = $array[1];
    $newmin = $minutes - $scale; if ($newmin < 0) {
        $newmin = 60 + $newmin; $newhour = $hours - 1;}
    else {
        $newhour = $hours;}
    if ($newhour < 0) {
        $newhour = $hours; $newmin = $minutes;}
    $newtime = "$newhour:$newmin:0";
    $sql =
        "update TALK set stime='$newtime' where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    lastModifiedTalk($ids, $idt);
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;}

############################################################
#        ADVANCE ALL TALKS
############################################################

if ($request == "advanceall") {

    $sql =
        "select stime,tday from TALK where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' "); $row = $res->fetchRow();
    $day = "".$row['tday']; $stime = "".$row['stime'];
    $sql =
        "select stime,idt from TALK where ida='$ida' and ids='$ids' and tday='$day' and stime>='$stime' ";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());}
    $numRows = $res->numRows();
    if (($numRows == 0) && (ERRORLOG))
        $GLOBALS["log"]->logError(__FILE__.".".__LINE__, "main",
                                  " Retrieved 0 elements with select '".
                                  $sql."' ");
    while ($row = $res->fetchRow()) {
        $stime = $row['stime'];
        $array = explode(":", $stime, 3);
        $hours = $array[0];
        $minutes = $array[1];
        $newmin = $minutes - $scale; if ($newmin < 0) {
            $newmin = 60 + $newmin; $newhour = $hours - 1;}
        else {
            $newhour = $hours;}
        if ($newhour < 0) {
            $newhour = $hours; $newmin = $minutes;}
        $newtime = "$newhour:$newmin:0";
        $sql =
            "update TALK set stime='$newtime' where ida='$ida' and ids='$ids' and idt='".
            $row['idt']."'";
        $res2 = $db->query($sql);
        if (DB::isError($res2)) {
            die($res2->getMessage());
        }
        lastModifiedTalk($ids, $res[$i]->idt);
    }

    // update the starting time of the session
    if ($stylesheet == "nosession")
        updateSessionStartTime($ids);
    Header
        ("Location: displayAgenda.php?ida=$ida&position=$position");
    exit;
}

##########################################################
#                                                        #
#	Part IV:  DOCUMENT CHANGES                       #
#                                                        #
##########################################################

// changes to user documents
if ($request == "checkDoc") {
    $msgs = "";
    // Adjust the query to the previous script
    $QUERY = preg_replace("/__/", "&", $QUERY);
    //print "loc '$thisScript' '$QUERY' ";exit;
    Header("Location: $thisScript?$QUERY");
}

if ($request == "descriptionDoc") {
        // Get the eventID
        $eventID = $archive->getEventID($idu, $archive->getEventID());
        // test if can change the description field giving the permission value for the current user over the event with ID $eventID
        //                      if ( $authManager->canModifyOtherFieldsSet( $authManager->properties( $idu, $eventID )))
        $archive->updateDescription($idd, $ {
            "description".$idx}
                                    );
    // Adjust the query to the previous script
    $QUERY = preg_replace("/__/", "&", $QUERY);
    Header("Location: $thisScript?$QUERY");}

if ($request == "deleteDoc") {
    $msgs = "";
    // Adjust the query to the previous script
    $QUERY = preg_replace("/__/", "&", $QUERY);
    preg_replace("/&msgs=/", "&old_msgs=", $QUERY);
    Header("Location: $thisScript?$QUERY&msgs=$msgs");}


#######################################################
// Functions to update the modification time of items
#######################################################

function lastModifiedAgenda() {
    global $ida,$PHPCacheACTIVE;
    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $db = &AgeDB::getDB();
    $sql = "update AGENDA set md='$today' where id='$ida'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    if ($PHPCacheACTIVE)
        deletecache();}

function lastModifiedSession($ids) {
    global $ida,
        $stylesheet,
        $PHPCacheACTIVE;
    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $db = &AgeDB::getDB();
    $sql =
        "update SESSION set md='$today' where ida='$ida' and ids='$ids'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    if ($stylesheet == "nosession") {
        $sql =
            "select FROM_DAYS(MIN(TO_DAYS(tday))) AS date from TALK where ida='$ida' and ids='$ids'";
        $res = $db->query($sql); if (DB::isError($res)) {
            die($res->getMessage());}
        $numRows = $res->numRows(); if ($numRows != 0) {
            $row = $res->fetchRow();
            $sql =
                "update SESSION set speriod1='".$row['date'].
                "' where ida='$ida' and ids='$ids'"; $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());}

        }
        $sql =
            "select SEC_TO_TIME(MIN(TIME_TO_SEC(stime))) AS date from TALK where ida='$ida' and ids='$ids'";
        $res = $db->query($sql); if (DB::isError($res)) {
            die($res->getMessage());}
        $numRows = $res->numRows(); if ($numRows != 0) {
            $row = $res->fetchRow();
            $sql =
                "update SESSION set stime='".$row['date'].
                "' where ida='$ida' and ids='$ids'"; $res = $db->query($sql);
            if (DB::isError($res)) {
                die($res->getMessage());}

        }
    }
    lastModifiedAgenda(); if ($PHPCacheACTIVE)
        deletecache();}

function lastModifiedTalk($ids, $idt) {
    global $ida,
        $PHPCacheACTIVE;
    $time = time();
    $today = strftime("%Y-%m-%d", $time);
    $db = &AgeDB::getDB();
    $sql =
        "update TALK set md='$today' where ida='$ida' and ids='$ids' and idt='$idt'";
    $res = $db->query($sql); if (DB::isError($res)) {
        die($res->getMessage());}

    lastModifiedSession($ids); if ($PHPCacheACTIVE)
        deletecache();}

function updateSessionStartTime($ids) {
    global $ida;
    // Retrieve and save the session starting/ending day and time
    $db = &AgeDB::getDB();
    $sql = "
             SELECT tday,
             stime
             FROM TALK
             WHERE ida = '$ida' and
             ids = '$ids'
             ORDER BY tday, stime
             LIMIT 1 ";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows();
    if ($numRows != 0) {
        $row = $res->fetchRow();
        $sql = "
             UPDATE SESSION
             SET speriod1 = '".$row['tday']."',
             stime = '".$row['stime']."'
             WHERE ida = '$ida' and
             ids = '$ids' ";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }

    $sql = "
             SELECT tday,
             stime,
             duration
             FROM TALK
             WHERE ida = '$ida' and
             ids = '$ids'
             ORDER BY tday DESC, stime DESC
             LIMIT 1 ";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die($res->getMessage());
    }
    $numRows = $res->numRows(); if ($numRows != 0) {
        $row = $res->fetchRow();
        $sql = "
             UPDATE SESSION
             SET eperiod1 = '".$row['tday']."',
             etime =
             SEC_TO_TIME(TIME_TO_SEC('".$row['stime']."') +
                         TIME_TO_SEC('".$row['duration']."'))
             WHERE ida = '$ida' and ids = '$ids' ";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die($res->getMessage());
        }
    }
}


function deletecache()
{
	// this function deletes the cache set up by jpcache.
	global $ida,$TMPDIR,$AGE_WWW;

    $url_array = parse_url($AGE_WWW);
    $path = $url_array['path'];
	$cachekey = md5("POST=a:0:{} GET=a:1:{s:3:\"ida\";s:".strlen("$ida").":\"$ida\";}");
	$masterkey = str_replace("/","_",str_replace("//","/","/$AGE_WWW")) . "_fullAgenda_php";
	$key = "jpcache-$masterkey:$cachekey";
	if (file_exists("$TMPDIR/$key"))
	{
		unlink("$TMPDIR/$key");
	}
}

?>
