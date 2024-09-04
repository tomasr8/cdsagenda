<?php
// $Id: makevCal.php,v 1.1.2.4 2004/07/29 10:06:04 tbaron Exp $

// makevCal.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// makevCal.php is part of CDS Agenda.
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

require_once 'AgeDB.php';
require_once 'config/config.php';

$db = &AgeDB::getDB();

function parse_command($argv)
{
   global $resourceIDs;
   $i = 1;

   if (count($argv) <= 1)
   {
      print "Bad parameter count!\n";
      displayhelp();
      exit;
   }
   else
   {
      while (substr($argv[$i],0,1) == "-")
      {
         if ($argv[$i] == "-h" || $argv[$i] == "--help")
         {
            displayhelp();
            exit;
         }
         else
         {
            print "Unrecognized option " . $argv[$i] . "!\n";
            displayhelp();
            exit;
         }
         $i++;
      }
   }

   $resourceIDs = array_slice($argv, $i);
   if (count($resourceIDs) == 0)
   {
	print "Missing Resource ID";
	displayHelp();
	exit;
   }
}

function displayhelp()
{
   print "Usage: makevCal [options] resourceID 
  Options: 
       -h, --help      print this help\n\n";
}

function testResource($ida,$ids,$idt)
{
	global $db;
	if ($idt != "" && $idt != "no")
	{
		$sql = "
SELECT * 
FROM   TALK 
WHERE  ida='$ida' and
       ids='$ids' and
       idt='$idt'";
	}
	elseif ($ids != "" && $ids != "no")
	{
   		$sql = "
SELECT * 
FROM   SESSION
WHERE  ida='$ida' and
       ids='$ids'";
 	}
	else
	{
		$sql = "
SELECT * 
FROM   AGENDA
WHERE  id='$ida'";
 	}
	$res = $db->query($sql);

   if ($res->numRows() == 0)
	return false;
   else
	return true;
}

function printvCalHeader()
{
    global $VERSION;

   print "BEGIN:VCALENDAR
PRODID:-//CERN//CDS Agenda $VERSION//EN
VERSION:1.0
TZ:+01";
}

function printvCalTail()
{
   print "
END:VCALENDAR\n";
}

function printvCalBody($ida,$ids,$idt)
{
    global $AGE_WWW,$db;

    if ($idt != "no") {
        $sql = "
SELECT ttitle,
       DATE_FORMAT(tday,'%Y%m%d') as sday,
       TIME_FORMAT(stime,'%H%i%S') as stime,
       TIME_FORMAT(SEC_TO_TIME(TIME_TO_SEC(stime)+TIME_TO_SEC(duration)),'%H%i%S') as etime,
       tcomment,
       location,
       room,
       category,
       tspeaker,
       email
FROM   TALK 
WHERE  ida='$ida' and
       ids='$ids' and
       idt='$idt'";
	$res = $db->query($sql);

        $row = $res->fetchRow();
        $title = $row[ttitle];
        $sday = $eday = $row[sday];
        $stime = $row[stime];
        $etime = $row[etime];
        $description = "[${AGE_WWW}/fullAgenda.php?ida=$ida#$ids$idt]";
        $location = $row[location];
        $room = $row[room];
        $category = ereg_replace("[\n\r]+"," ",$row[category]);
        $speaker = $row[tspeaker];
        $email = $row[email];
        $summary = $category . ($category == "" ? "" : " - ").ereg_replace("[\n\r]+"," ",$title);
        $summary = ereg_replace("[\\]*;","\\;",$summary);
    }
    elseif ($ids != "no") {
	$sql = "
SELECT stitle,
       DATE_FORMAT(eperiod1,'%Y%m%d') as sday,
       TIME_FORMAT(stime,'%H%i%S') as stime,
       TIME_FORMAT(etime,'%H%i%S') as etime,
       scomment,
       slocation,
       room,
       schairman,
       email
FROM   SESSION
WHERE  ida='$ida' and
       ids='$ids'";
	$res = $db->query($sql);

        $row = $res->fetchRow();
        $title = $row[stitle];
        $sday = $eday = $row[sday];
        $stime = $row[stime];
        $etime = $row[etime];
        $description = "[${AGE_WWW}/fullAgenda.php?ida=$ida#$ids]";
        $location = $row[location];
        $room = $row[room];
        $category = "";
        $speaker = $row[schairman];
        $email = $row[email];
        $summary = $category . ($category == "" ? "" : " - ").ereg_replace("[\n\r]+"," ",$title);
        $summary = ereg_replace("[\\]*;","\\;",$summary);
    }
    else {
	$sql = "
SELECT title,
       DATE_FORMAT(stdate,'%Y%m%d') as sday,
       TIME_FORMAT(stime,'%H%i%S') as stime,
       TIME_FORMAT(etime,'%H%i%S') as etime,
       acomments,
       location,
       room,
       chairman,
       cem,
       DATE_FORMAT(endate,'%Y%m%d') as eday
FROM   AGENDA
WHERE  id='$ida'";
	$res = $db->query($sql);
        $row = $res->fetchRow();
        $title = $row[title];
        $sday = $row[sday];
        $eday = $row[eday];
        $stime = $row[stime];
        $etime = $row[etime];
        $description = "[${AGE_WWW}/fullAgenda.php?ida=$ida]";
        $location = $row[location];
        $room = $row[room];
        $category = "";
        $speaker = $row[chairman];
        $email = $row[cem];
        $summary = $category . ($category == "" ? "" : " - ").ereg_replace("[\n\r]+"," ",$title);
        $summary = ereg_replace("[\\]*;","\\;",$summary);
    }

   print "
BEGIN:VEVENT
UID:CDSAgenda$now-".uniqid("")."@cern.ch";
   if ($speaker != "" || $email != "")
       print "
ATTENDEE;ROLE=ORGANIZER:$speaker $email";
   print "
DTSTART:${sday}T${stime}
DTEND:${eday}T${etime}
DESCRIPTION:$description";
   if ($room != "" || $room != "")
       print "
LOCATION:$location ($room)";
   if ($summary != "")
       print "
SUMMARY:".utf8_encode($summary);
   print "
END:VEVENT";
}

function sendCGIHeader()
{
   Header("Content-type: text/x-vcalendar");
   Header("Content-Disposition: inline; filename=\"talk.vcs\"");

}


///////////////////////////////////////////////////////////////////////
// MAIN SCRIPT                                                       //
///////////////////////////////////////////////////////////////////////

// called as CGI
if ($id != "")
{
    if (ereg("a(.*)s(.*)t(.*)",$id,$itemIDs)) {
        $ida = "a$itemIDs[1]";
        $ids = "s$itemIDs[2]";
        $idt = "t$itemIDs[3]";
    }
    elseif (ereg("a(.*)s(.*)",$id,$itemIDs)) {
        $ida = "a$itemIDs[1]";
        $ids = "s$itemIDs[2]";
        $idt = "no";
    }
    elseif (ereg("a(.*)",$id,$itemIDs)) {
        $ida = "a$itemIDs[1]";
        $ids = "no";
        $idt = "no";
    }
    else {
        print "Bad resource ID: $id \n";
        exit;
    }
}

if ($ida != "" && $ids != "" && $idt != "")
{
    if (!testResource($ida,$ids,$idt)) {
        print "Cannot find resource: $ida$ids$idt \n";
        exit;
    }
    else {
        sendCGIHeader();
        printvCalHeader();
        printvCalBody($ida,$ids,$idt);
        printvCalTail();
    }
}

//command-line call
elseif(is_array($argv))
{
    // Analyse of the command-line arguments
    // This program should take the path to the record file as parameter
    parse_command($argv);
    
    printvCalHeader();
    foreach ($resourceIDs as $resourceID) {
        if (ereg("a(.*)s(.*)t(.*)",$resourceID,$itemIDs)) {
            $ida = "a$itemIDs[1]";
            $ids = "s$itemIDs[2]";
            $idt = "t$itemIDs[3]";
      	}
        else {
            print "Bad resource ID: $resourceID \n";
            continue;
    	}

      	if (!testResource($ida,$ids,$idt)) {
            print "Cannot find resource: $resourceID \n";
            continue;
    	}
        else {
            printvCalBody($ida,$ids,$idt);
            print "\n\n";
        }
    }
    printvCalTail();
}