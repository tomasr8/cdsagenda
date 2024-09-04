<?php
// $Id: agendaFactory_clone.php,v 1.2.4.10 2004/07/29 10:06:08 tbaron Exp $

// agendaFactory_clone.php --- clone module
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//            Erik Simon   <erik.simon@cern.ch>
//
// agendaFactory_clone.php is part of CDS Agenda.
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
// Erik Simon: refactoring of the original code and addition of new
// functionalities for repeating agendas.
//

require_once '../AgeDB.php';
require_once '../AgeLog.php';

define("MAX_CLONED_AGENDAS" , 20);
define("DATE_FORMAT"        , "Y-m-d");
 // to convert Unix timestamps to dates
define("DAY_LENGTH"         , 86400);

/**
 * Increment the counter in the database.
 */
function incrementCounter($time, $thisyear) {
	global $newid;

	$fieldValue = "noAgenda" . $thisyear;

	$db = &AgeDB::getDB();
	$sql = "select num from COUNTER where name='noAgenda$thisyear'";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();
	$row = $res->fetchRow();
	$num = $row['num'];

	$year = strftime("%y", $time);
	$newid = "a$year" . $num;

	$newNum = (int) $num + 1;

	// Transaction for the clone action
	//$GLOBALS[ "table" ]->startTransaction();
	$db->autocommit(false);

	$sql = "update COUNTER set num=$newNum where name='noAgenda$thisyear' ";
	if (DB::isError($db->query($sql)))
		return false;
	else
		return true;
}

/**
 * Perform the insertion of the new agenda.
 *
 * The agenda is defined by the arguments.
 */
function insertIntoAgenda($title, 
						  $newid, 
						  $startdate, 
                          $stime,
						  $enddate, 
                          $etime,
						  $location, 
						  $chairman,
						  $cem, 
						  $an, 
						  $today, 
						  $stylesheet, 
						  $format, 
						  $confidentiality,
						  $apassword, 
						  $repno, 
						  $fid, 
						  $acomments, 
						  $keywords, 
						  $visibility, 
						  $bld, 
						  $floor, 
						  $room, 
						  $nbsession, 
						  $row) {
	// an agenda object would be welcome to get rid of all those
	// parameters

	$db = &AgeDB::getDB();

	// Check if the TABLE AGENDA is currently used in extended mode
	if ($agendaTABLEExtension_1) {
		// If contact email not specified is equal to smr___@localdomain
		if (($cem == "") && ($smr != ""))
			$cem = $smr . $standardLogSuffix;

		$sql = "insert into AGENDA values('$title','$newid','$startdate','$enddate','$location','$chairman','$cem','open','$an','$today','$today','$stylesheet','$format','$confidentiality','$apassword','$repno','$fid','$acomments','$keywords','$visibility','$bld','$floor','$room',$nbsession," . 
			$row['hosted'] . ",'" . 
			$row['collaborations'] . "','" . 
			$row['organizers'] . "','" . 
			$row['localorganizers'] . "','" .
			$row['limitedpartecipants'] . "','" . 
			$row['room'] . "','" . 
			$row['laboratories'] . "','" . 
			$row['secretary'] . "','" . 
			$row['expparts'] . "','" . 
			$row['deadrequest'] . "'," . 
			$row['partialEvent'] . ",'" . 
			$row['dayNote'] . "','" . 
			$row['smr'] . 
			$standardLogSuffix . "','" . 
			$row['cosponsor'] . "','" . 
			$row['smr'] . "'," . 
			$row['website'] . ",'" . 
			$row['additionalnotes'] . "'," . 
			$row['outside'] . "," . 
			$row['internalOnly'] . ",'".
			$row['directors']."','".
			$row['personal1title']."','".
			$row['personal1value']."','".
			$row['personal2title']."','".
			$row['personal2value']."','".
			$row['personal3title']."','".
			$row['personal3value']."','".
			$row['personal4title']."','".
			$row['personal4value']."',".
			$row['cosponsored'].",'".
			$row['websitelink']."','".
			$row['directorsandorganizers']."')";

		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
	}
	else {
		$sql = "insert into AGENDA (title,id,stdate,endate,location,chairman,cem,status,an,cd,md,stylesheet,format,confidentiality,apassword,repno,fid,acomments,keywords,visibility,bld,floor,room,nbsession,stime,etime) values('$title','$newid','$startdate','$enddate','$location','$chairman','$cem','open','$an','$today','$today','$stylesheet','$format','$confidentiality','$apassword','$repno','$fid','$acomments','$keywords','$visibility','$bld','$floor','$room','$nbsession','$stime','$etime')";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
	}
	return true; //it dies otherwise
}

/**
 * Copy the associated alerts.
 */
function copyAlerts($newid) {
	$db = &AgeDB::getDB();
	$sql = "select address,delay,text from ALERT where id='$ida'";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

	for ($i = 0; $i < $numRows; $i++) {
		$row = $res->fetchRow();
		$address = $row['address'];
		$delay   = $row['delay'];
		$text    = $row['text'];

		$sql = "insert into ALERT (id,address,delay,text) " .
			"values ('$newid','" . 
			$address . "','" . 
			$delay . "','" .
			$text . "')";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
	}
}

/**
 * Copy the associated sessions.
 */
function copySessions($ida, $diffdays, $newid, $today) {
	$db = &AgeDB::getDB();
	$sql = "select ids,schairman,".
		"FROM_DAYS(TO_DAYS(speriod1) + $diffdays) AS newsperiod1,".
		"stime,FROM_DAYS(TO_DAYS(eperiod1) + $diffdays) ".
		"AS neweperiod1,".
		"etime,stitle,snbtalks,slocation,scem,bld,floor,room,".
		"broadcasturl,scomment ".
		"from SESSION where ida='$ida'";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

    $success = true;

	for ($i = 0; $i < $numRows; $i++) {
		$row = $res->fetchRow();
		$ids          = $row['ids'];
		$schairman    = $row['schairman'];
		$newsperiod1  = $row['newsperiod1'];
		$stime        = $row['stime'];
		$neweperiod1  = $row['neweperiod1'];
		$etime        = $row['etime'];
		$stitle       = $row['stitle'];
		$snbtalks     = $row['snbtalks'];
		$slocation    = $row['slocation'];
		$scem         = $row['scem'];
		$bld          = $row['bld'];
		$floor        = $row['floor'];
		$room         = $row['room'];
		$broadcasturl = $row['broadcasturl'];
		$scomment     = $row['scomment'];

		$schairman    = str_replace("'", "\'", $schairman);
		$stitle       = str_replace("'", "\'", $stitle);
		$slocation    = str_replace("'", "\'", $slocation);
		$scem         = str_replace("'", "\'", $scem);
		$floor        = str_replace("'", "\'", $floor);
		$room         = str_replace("'", "\'", $room);
		$broadcasturl = str_replace("'", "\'", $broadcasturl);
		$scomment     = str_replace("'", "\'", $scomment);
        
		$sql = "insert into SESSION ".
			"(ida,ids,schairman,speriod1,stime,".
			" eperiod1,etime,stitle,snbtalks,slocation,".
			" scem,sstatus,bld,floor,room,broadcasturl,".
			" cd,md,scomment) ".
			"values('$newid','" . 
			$ids . "','" . 
			$schairman . "','" . 
			$newsperiod1 . "','" . 
			$stime . "','" . 
			$neweperiod1 . "','" . 
			$etime . "','" . 
			$stitle . "'," . 
			$snbtalks . ",'" . 
			$slocation . "','" . 
			$scem . "','open','" . 
			$bld . "','" . 
			$floor . "','" . 
			$room . "','" . 
			$broadcasturl . 
			"','$today','$today','" . 
			$scomment . "')";

        if (DB::isError($db->query($sql))) {
			$success = false;
		}
	}

	return $success; //success or failure
}

/**
 * Copy the associated talks and subtalks.
 */
function copyTalks($ida, $diffdays, $newid, $today) {
    $db = &AgeDB::getDB();
	$sql = "select ids,idt,ttitle,tspeaker,email,".
		"FROM_DAYS(TO_DAYS(tday) + $diffdays) AS newtday,".
		"tcomment,location,bld,floor,room,broadcasturl,".
		"type,category,stime,repno,affiliation,duration ".
		"from TALK where ida='$ida'";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

	$err2 = false;

	for ($i = 0; $i < $numRows; $i++ ) {
		$row = $res->fetchRow();
		$ids            = $row['ids'];
		$idt            = $row['idt'];
		$ttitle         = $row['ttitle'];
		$tspeaker       = $row['tspeaker'];
		$email          = $row['email'];
		$newtday        = $row['newtday'];
		$tcomment       = $row['tcomment'];
		$location       = $row['location'];
		$bld            = $row['bld'];
		$floor          = $row['floor'];
		$room           = $row['room'];
		$broadcasturl   = $row['broadcasturl'];
		$type           = $row['type'];
		$category       = $row['category'];
		$stime          = $row['stime'];
		$repno          = $row['repno'];
		$affiliation    = $row['affiliation'];
		$duration       = $row['duration'];
		
		$ttitle         = str_replace("'", "\'", $ttitle);
		$tspeaker       = str_replace("'", "\'", $tspeaker);
		$tcomment       = str_replace("'", "\'", $tcomment);
		$location       = str_replace("'", "\'", $location);
		$floor          = str_replace("'", "\'", $floor);
		$room           = str_replace("'", "\'", $room);
		$broadcasturl   = str_replace("'", "\'", $broadcasturl);
		$category       = str_replace("'", "\'", $category);
		$repno          = str_replace("'", "\'", $repno);
		$affiliation    = str_replace("'", "\'", $affiliation);
		$email          = str_replace("'", "\'", $email);

		$sql = "insert into TALK ".
			"(ida,ids,idt,ttitle,tspeaker,tday,".
			"tcomment,location,bld,floor,room,".
			"broadcasturl,type,cd,md,category,".
			"stime,repno,affiliation,duration,email) ".
			"values('$newid','" . 
			$ids . "','" . 
			$idt . "','" . 
			$ttitle . "','" . 
			$tspeaker . "','" . 
			$newtday . "','" . 
			$tcomment . "','" . 
			$location . "','" . 
			$bld . "','" . 
			$floor . "','" . 
			$room . "','" . 
			$broadcasturl . "'," . 
			$type . ",'$today','$today','" . 
			$category . "','" . 
			$stime . "','" . 
			$repno . "','" . 
			$affiliation . "','" . 
			$duration . "','" . 
			$email . "')";

		if (DB::isError($db->query($sql)))
			$err2 = true;

		
		$sql = "select ids,idt,ttitle,tspeaker,email,".
			"FROM_DAYS(TO_DAYS(tday) + $diffdays) AS newtday,".
			"tcomment,type,stime,repno,affiliation,duration ".
			"from SUBTALK where ida='$ida' ".
			"and ids='" . 
			$ids . 
			"' and fidt='" . 
			$idt . "'";

		$resj = $db->query($sql);
		if (DB::isError($resj)) {
			die ($resj->getMessage());
		}
		$numRowsj = $resj->numRows();

		// For each retrieved records
		for ($j = 0; $j < $numRowsj; $j++) {
			$rowj = $resj->fetchRow();
			$ids            = $rowj['ids'];
			$idt2           = $rowj['idt'];
			$ttitle         = $rowj['ttitle'];
			$tspeaker       = $rowj['tspeaker'];
			$email          = $rowj['email'];
			$newtday        = $rowj['newtday'];
			$tcomment       = $rowj['tcomment'];
			$type           = $rowj['type'];
			$stime          = $rowj['stime'];
			$repno          = $rowj['repno'];
			$affiliation    = $rowj['affiliation'];
			$duration       = $rowj['duration'];

			$ttitle         = str_replace("'", "\'", $ttitle);
			$tspeaker       = str_replace("'", "\'", $tspeaker);
			$tcomment       = str_replace("'", "\'", $tcomment);
			$repno          = str_replace("'", "\'", $repno);
			$affiliation    = str_replace("'", "\'", $affiliation);
			$email          = str_replace("'", "\'", $email);
	      
			// Insert into table with current values
			$sql = "insert into SUBTALK ".
				"(ida,ids,idt,ttitle,tspeaker,".
				"tday,tcomment,type,cd,md,stime,".
				"repno,affiliation,duration,".
				"fidt,email) ".
				"values('$newid','" . 
				$ids . "','" . 
				$idt2 . "','" .
				$ttitle . "','" . 
				$tspeaker . "','" . 
				$newtday . "','" . 
				$tcomment . "'," . 
				$type . 
				",'$today','$today','" . 
				$stime . "','" . 
				$repno . "','" . 
				$affiliation . "','" . 
				$duration . "','" . 
				$idt . "','" . 
				$email . "')";
			
			if (DB::isError($db->query($sql))) {
				$err2 = true; 
			}
		}
	}
	return !$err2; // success or failure
}

/**
 * Commit the changes and inform about result.
 */
function changesCommitted($fid, $num, $today, $access, $title) {
	global $AGE_WWW, $access;

	$alertText = "<SCRIPT> alert(\"You have just created $num agenda(s)!\\n".
		"Modification will be restricted with the same number as the old one:\\n\\n   ".
		"$access\\n\\nEnter the access number into the MODIFY input box on the display page.\");".
		"</SCRIPT>";

	$message="$num new agenda(s) (clone(s)) has/have just been created!\n\nTitle: $title\nDate: ".
		"$today\nAccess: ${AGE_WWW}/fullAgenda.php?ida=$newid&stylesheet=$standardViewStylesheet";

	if ($MAILSERVERON) {
		mail("${admin_email}","new agenda (clone) $newid","$message");
	}

	Header("Location: ${AGE_WWW}/displayLevel.php?fid=$fid&alertText="
		   . urlencode($alertText));

	return true; // ideally: return whether success or not
}

/**
 * Create an agenda with the appropriate values at startdate.
 *
 * All the necessary information is in the global table.
 */
function creat($startdate) {
	global $newid;    // the same as in incrementCounter()
	global $ida;      // always the same
	global $access;
    global $message;

	$time=time();
	$today=strftime("%Y-%m-%d",$time);
	$thisyear=strftime("%Y",$time);

	$db = &AgeDB::getDB();

    $log = &AgeLog::getLog();

	if(incrementCounter($time, $thisyear)) {
		$sql = "select *,".
			"TO_DAYS('$startdate') - TO_DAYS(stdate) AS diffdate,".
			"FROM_DAYS(TO_DAYS(endate) + TO_DAYS('$startdate') ".
			"- TO_DAYS(stdate)) AS newenddate ".
			"from AGENDA where id='$ida'";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
		$numRows = $res->numRows();

		if (($numRows == 0) && (ERRORLOG)) {
            $log->logError( __FILE__ . "." . __LINE__, 
                                         "main", 
                                         " Retrieved 0 elements with".
                                         " select '" . $sql . "' " );
		}

		$row = $res->fetchRow();
		$access            = $row['an'];
		$title             = str_replace("'", "\'", $row['title']);
		$initialdate       = $row['stdate'];
		$location          = str_replace("'", "\'", $row['location']);
		$chairman          = str_replace("'", "\'", $row['chairman']);
		$cem               = str_replace("'", "\'", $row['cem']);
		$status            = $row['status'];
		$an                = str_replace("'", "\'", $row['an']);
		$stylesheet        = str_replace("'", "\'", $row['stylesheet']);
		$format            = $row['format'];
		$confidentiality   = $row['confidentiality'];
		$apassword         = str_replace("'", "\'", $row['apassword']);
		$repno             = str_replace("'", "\'", $row['repno']);
		$fid               = $row['fid'];
		$acomments         = str_replace("'", "\'", $row['acomments']);
		$keywords          = str_replace("'", "\'", $row['keywords']);
		$visibility        = str_replace("'", "\'", $row['visibility']);
		$bld               = str_replace("'", "\'", $row['bld']);
		$floor             = str_replace("'", "\'", $row['floor']);
		$room              = str_replace("'", "\'", $row['room']);
		$nbsession         = $row['nbsession'];
		$diffdays          = $row['diffdate'];
		$enddate           = $row['newenddate'];
        $stime             = $row['stime'];
        $etime             = $row['etime'];

		if(insertIntoAgenda($title, 
							$newid, 
							$startdate, 
                            $stime,
							$enddate, 
                            $etime,
							$location, 
							$chairman,
							$cem, 
							$an, 
							$today, 
							$stylesheet, 
							$format, 
							$confidentiality,
							$apassword, 
							$repno, 
							$fid, 
							$acomments, 
							$keywords, 
							$visibility, 
							$bld, 
							$floor, 
							$room, 
							$nbsession, 
							$row)) {
			copyAlerts($newid);
			if(copySessions($ida, $diffdays, $newid, $today)) {
				if(copyTalks($ida, $diffdays, $newid, $today)) {
					$db->commit();
					return true; // success
				}
				else
					$roll = true;
			}
			else
				$roll = true;
		}
		else
			$roll = true;
	}
	else
		$roll = true;

	if ($roll) {
		//$GLOBALS[ "table" ]->rollbackTransaction();
		$db->rollback();
		$alertText = "<SCRIPT>alert(\"An error occurred while cloning ".
            "an agenda!\");</SCRIPT>";
		// $message = "No new agenda created";
        Header("Location: ${AGE_WWW}/displayLevel.php?fid=$fid&alertText="
               . urlencode($alertText));

	}

	return false;
}

/**
 * Increment the date by a number of days.
 */
function incrementDay($date, $inc) {
	$dateArray = split("-", $date); // depends on the format, bad idea
	$y = $dateArray[0];
	$m = $dateArray[1];
	$d = $dateArray[2];
	return date(DATE_FORMAT, mktime(0,0,0, $m, $d+$inc, $y));

	// Why does this not work?
	//   $uts = strtotime($date);
	//   $nd = date(DATE_FORMAT, $uts + $inc*DAY_LENGTH);
	//   return $nd;
}

/**
 * Increment the date by a number of weeks.
 */
function incrementWeek($date, $inc) {
	$dateArray = split("-", $date); // depends on the format, bad idea
	$y = $dateArray[0];
	$m = $dateArray[1];
	$d = $dateArray[2];
	return date(DATE_FORMAT, mktime(0,0,0, $m, $d+$inc*7, $y));
}

/**
 * Increment the date by a number of months.
 */
function incrementMonth($date, $inc) {
	$dateArray = split("-", $date); // depends on the format, bad idea
	$y = $dateArray[0];
	$m = $dateArray[1];
	$d = $dateArray[2];
	return date(DATE_FORMAT, mktime(0,0,0, $m+$inc, $d, $y));
}

/**
 * Increment the date by a number of years.
 */
function incrementYear($date, $inc) { 
	$dateArray = split("-", $date); // depends on the format, bad idea
	$y = $dateArray[0];
	$m = $dateArray[1];
	$d = $dateArray[2];
	return date(DATE_FORMAT, mktime(0,0,0, $m, $d, $y+$inc));
}

/**
 * Return whether the date range is valid.
 *
 * Valid only if startdate <= startdate2
 */
function isValidDateRange($startdate, $startdate2) {
	return strtotime($startdate) <= strtotime($startdate2);
}

/**
 * Increment the date.
 *
 * The date is incremented by freq periods, eg. if freq="month" and
 * period="3", then the date is incremented of 3 months.
 */
function incrementFixed($startdate, $period, $freq) {
	switch($freq) {
	case "day":
		return incrementDay($startdate, $period);
		break;
	case "week":
		return incrementWeek($startdate, $period);
		break;
	case "month":
		return incrementMonth($startdate, $period);
		break;
	case "year":
		return incrementYear($startdate, $period);
		break;
	default:
		if(LOGGING) {
			$GLOBALS["log"]->logDebug(__FILE__, 
									  __LINE__, 
									  " Unrecognized frequency while"
									  ." cloning agenda.");
		}
		exit;
	} 
}

/** 
 * Return the next date candidate satisfying the conditions.
 *
 * The conditions are expressed by the parameters (see
 * cloneAgenda.ihtml for the interface, and function getNextMatch()
 * for complete interface to this feature).
 */
function getNextCandidate($startdate, $order, $day, $period) {
	$dateArray = split("-", $startdate); // depends on the format, bad idea
	$y = $dateArray[0];
	$m = $dateArray[1];

	if($order < 5)
		$mday = ($order - 1) * 7 + 1;
	else
		$mday = date("t", mktime(0,0,0,$m+$period,1,$y));

	$candidate = date(DATE_FORMAT, mktime(0,0,0, $m+$period, $mday, $y));
	return $candidate;
}

/**
 * Return the next open day after the candidate.
 *
 * The next open day is the next day that is neither a Saturday nor a
 * Sunday. No holiday check!
 */
function getNextOpenDay($candidate) {
	$dayName = date("D", strtotime($candidate));
	if(!strcmp($dayName, "Sun"))
		return incrementDay($candidate, 1);
	else if(!strcmp($dayName, "Sat"))
		return incrementDay($candidate, 2);
	else
		return $candidate;
}

/**
 * Return the previous open day prior to the candidate.
 *
 * The previous open day is the last day that is neither a Saturday
 * nor a Sunday. No holiday check!
 */
function getPreviousOpenDay($candidate) {
	$dayName = date("D", strtotime($candidate));
	if(!strcmp($dayName, "Sun"))
		return incrementDay($candidate, -2);
	else if(!strcmp($dayName, "Sat"))
		return incrementDay($candidate, -1);
	else
		return $candidate;
}

/**
 * Return the next date matching the condition.
 *
 * The condition is expressed by the parameters (see
 * cloneAgenda.ihtml for the interface).
 */
function getNextMatch($startdate, $order, $day, $period) {
	$candidate = getNextCandidate($startdate, $order, $day, $period);
	if(!strcmp($day, "OpenDay")) {
		switch($order) {
		case 1:
			$candidate = getNextOpenDay($candidate);
			break;
		case 5:
			$candidate = getPreviousOpenDay($candidate);
			break;
		default:
			echo "Doesn't make much sense, but could be implemented.";
			exit;
		}
	}
	else {
		$dayName = date("D", strtotime($candidate));
		$day = substr($day, 0, 3);
		while(strcmp($dayName, $day)) {
			if ($order < 5)
				$candidate = incrementDay($candidate, 1);
			else
				// Here I owe the reader an explanation: for some reason, when
				// trying to find the last, say Monday of a month, sometimes
				// the loop gets stuck in an infinite... well, er, loop:
				// incrementDay doesn't increment anything anymore. This
				// happens when the last Monday of the previous month was the
				// last day of the month. If someone understands why, please
				// contact me with an appropriate patch: erik.simon@cern.ch
				$candidate = incrementDay($candidate, -1);
			$dayName = date("D", strtotime($candidate));
		}
	}
	return $candidate;
}

/**
 * Define what to do when the maximum number of agendas is reached.
 */
function tooManyAgendas($fid) {
	global $AGE_WWW;

    $log = &AgeLog::getLog();

	if (ERRORLOG)
		$log->logError( __FILE__ . "." . __LINE__, 
									 "main", 
									 " Attempt to create more than " . 
									 MAX_CLONED_AGENDAS . 
									 " agendas, only creating that many"); 
	Header("Location: ${AGE_WWW}/displayLevel.php?fid=$fid&alertText=".
		   urlencode("<script> alert(\"Cannot create more than " . 
					 MAX_CLONED_AGENDAS . " agendas; only " .
					 MAX_CLONED_AGENDAS. " created.\");</script>"));
	exit;
}


/**
 * Treat agendas recurring on a fixed basis.
 *
 * Fixed basis: every day/week/month/year.
 */
function treatFixedRecurringAgenda($startdate, 
								   $startdate2, 
								   $period, 
								   $freq, 
								   $fid) {
	$curStartDate = $startdate;
	$counter = 0;
	while(strtotime($curStartDate) <= strtotime($startdate2)) {
		if($counter >= MAX_CLONED_AGENDAS) {
			tooManyAgendas($fid);
			break;
		}

		if(!creat($curStartDate)) {
			echo "** unable to create agenda at $curStartDate<br>";
		}
		$counter++;
		$curStartDate = incrementFixed($curStartDate, $period, $freq);
	}
	return $counter;
}

/**
 * Treats agendas recurring on a relative basis.
 *
 * Relative basis: first/second/.../last Monday/.../Sunday/Open Day
 * every x months.
 */
function treatRelativeRecurringAgenda($startdate, $startdate2, $order, $day, $period, $fid) {
	$curStartDate = getNextMatch($startdate, $order, $day, $period);
	$counter = 0;
	while(strtotime($curStartDate) <= strtotime($startdate2)) {
		if($counter >= MAX_CLONED_AGENDAS) {
			tooManyAgendas($fid);
			break;
		}
		if(!creat($curStartDate)) {
			echo "** unable to create agenda at $curStartDate<br>";
		}

		//$curStartDate = incrementDay($curStartDate, 1);
		$curStartDate = getNextMatch($curStartDate, $order, $day, $period);
		$counter++;
	}
	return $counter;
}

/**
 * Treat the single clone case.
 */
function cloneOnce($startdate) {
	if(!creat($startdate)) {
		echo "** unable to create agenda at $startdate<br>";
	}
}

/**
 * Return the starting date of an agenda.
 */
function retrieveStartingDate($ida) {
    $db = &AgeDB::getDB();
    $sql = "select stdate from AGENDA where id='$ida'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    $numRows = $res->numRows();

	if($numRows > 0) {
        $row = $res->fetchRow();
		return $row['stdate'];
    }
	else
		return false;
}


// ENTRY POINT:
if ($request == "Clone Agenda") {
	global $period, $monthPeriod, $freq, $order, $day, $sel; // url parameters
	global $ida, $fid;

	// NOW CHANGED IN authRequest.inc, BUT REMAINS TO BE CONFIRMED.
	//$permission = 255;

	// Check that we are authorized to clone the agenda
    // 	if ( !$authManager->canClon( $permission )) {
    // 		if ( LOGGING )
    // 			$GLOBALS[ "log" ]->logDebug( __FILE__, 
    // 										 __LINE__, 
    // 										 " Unallowed access with request"
    // 										 ." '$request' " );
    // 		outError(" You are not allowed to CLONE this EVENT".
    // 				 " (agenda with id '$ida') ", 
    // 				 "03", 
    // 				 &$Template);
    // 		exit;		
    // 	}	 

	if(!$startdate=retrieveStartingDate($ida)) {
		echo "Error retrieving start date for agenda with".
			" id=$ida, aborting.<br>";
		exit;
	}

	switch($sel) {
	case "once":
		$startdate2="$stdy-$stdm-$stdd";
		cloneOnce($startdate2);
		$num = 1;
		break;
	case "fixed":
		$startdate = "$indy2-$indm2-$indd2";
		switch($func2) {
		case "until":
			$startdate2="$stdy2-$stdm2-$stdd2";
			break;
		case "ntimes":
			$startdate2=incrementFixed($startdate, $period*($num2-1), $freq);
			break;
		}
		if(!isValidDateRange($startdate, $startdate2)) {
			Header("Location: "
				   ."${AGE_WWW}/modification/cloneAgenda.php".
				   "?ida=$ida&alertText="
				   . urlencode("Invalid ending date ".
							   "$startdate2 < $startdate"));
			exit;
			break;
		}
		$num = treatFixedRecurringAgenda($startdate, 
										 $startdate2, 
										 $period, 
										 $freq, 
										 $fid);
		break;
	case "relative":
		$startdate = "$indy3-$indm3-$indd3";
		$startdate = incrementMonth($startdate, -$monthPeriod);
		switch($func3) {
		case "until":
			$startdate2="$stdy3-$stdm3-$stdd3";
			break;
		case "ntimes":
			$startdate2=incrementMonth($startdate, $monthPeriod*$num3);
			$startdate2=incrementWeek($startdate2, $order);
			break;
		}
		if(!isValidDateRange($startdate, $startdate2)) {
			Header("Location: ".
				   "${AGE_WWW}/modification/cloneAgenda.php".
				   "?ida=$ida&alertText=".
				   urlencode("Invalid ending date: $startdate2 < $startdate"));
			exit;
			break;
		}
		$num = treatRelativeRecurringAgenda($startdate, 
											$startdate2, 
											$order, 
											$day, 
											$monthPeriod, 
											$fid);
		break;
	default:
		; // TODO: some error or log here
	}
  
	changesCommitted($fid, $num, $today, $access, $title);
	exit;
}
?>