<?php
// $Id: checkAlarm.php.in,v 1.1.2.1 2002/11/12 07:37:37 tbaron Exp $

// checkAlarm.php --- batch to send alert emails
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// checkAlarm.php is part of CDS Agenda.
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

require "~/Downloads/cdsagenda/www/data/cdsagenda/config/config.php";
require "$AGE/AgeDB.php";
require "$AGE/modification/SendAlertEmail.php";

$db = &AgeDB::getDB();

$sql = "select id,address,delay,text from ALERT";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

while ( $row = $res->fetchRow() ) {
	$ida = "" . $row['id'];
	$address = "" . $row['address'];
	$delay = "" . $row['delay'];
	$note = "" . $row['text'];

	$sql ="select id from AGENDA where id='$ida' ".
		"and CURDATE() = SUBDATE(stdate,INTERVAL " . $row['delay'] . " DAY)";
	$res2 = $db->query($sql);
	if (DB::isError($res2)) {
		die ($res2->getMessage());
	}
	$numRows2 = $res2->numRows();

	if ( $numRows2 != 0 ) {
		//print "today...\n";
		//print "send alert\n";
		Send_Alert_Email();

		$sql =  "delete from ALERT where id='$ida' ".
			"and address='$address' and delay=$delay";
		$res3 = $db->query($sql);
		if (DB::isError($res3)) {
			die ($res3->getMessage());
		}
	}
	else {
		//print "not today...\n";
		$sql = "select id from AGENDA where id='$ida' and ".
			"CURDATE() > SUBDATE(stdate,INTERVAL " . $row['delay'] . " DAY)";
		$res3 = $db->query($sql);
		if (DB::isError($res3)) {
			die ($res3->getMessage());
		}
		$numRows3 = $res3->numRows();

		if ( $numRows3 != 0 ) {
			//print "delete alert\n";
			$sql = "delete from ALERT where id='$ida' and ".
				"address='$address' and delay=$delay";
			$res4 = $db->query($sql);
			if (DB::isError($res4)) {
				die ($res4->getMessage());
			}
		}
	}
}
?>