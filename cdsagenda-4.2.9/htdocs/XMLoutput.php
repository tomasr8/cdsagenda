<?php
// $Id: XMLoutput.php,v 1.1.1.1.4.8 2004/07/29 10:06:03 tbaron Exp $

// XMLoutput.php --- extracts the agenda info from mysql and creates
// xml output
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// XMLoutput.php is part of CDS Agenda.
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
require_once 'AgeLog.php';
require_once 'config/config.php';
require_once 'platform/system/logManager.inc';

$log = &AgeLog::getLog();
$log->logDebug( __FILE__, __LINE__, " xmloutput!" );

/////////////////////////////////////////////////////
//
//
// Define classes
//
//
/////////////////////////////////////////////////////

require_once ("objects/agenda.obj");
require_once ("objects/file.obj");
require_once ("objects/session.obj");
require_once ("objects/talk.obj");
require_once ("objects/subtalk.obj");

/////////////////////////////////////////////////////
//
//
// Global Functions
//
//
/////////////////////////////////////////////////////

function formatfield($field)
{
	$field = htmlspecialchars($field);
	$field = str_replace("\007","",$field);
	$field = str_replace("\003","",$field);
	$field = str_replace("\010","",$field);
	$field = str_replace("\013","",$field);
	$field = str_replace("\014","",$field);
	$field = str_replace("\016","",$field);
	$field = str_replace("\017","",$field);
	$field = str_replace("\020","",$field);
	$field = str_replace("\021","",$field);
	$field = str_replace("\022","",$field);
	$field = str_replace("\023","",$field);
	$field = str_replace("\024","",$field);
	$field = str_replace("\025","",$field);
	$field = str_replace("\026","",$field);
	$field = str_replace("\027","",$field);
	$field = str_replace("\030","",$field);
	$field = str_replace("\031","",$field);
	$field = str_replace("\032","",$field);
	$field = str_replace("\033","",$field);
	$field = str_replace("\034","",$field);
	$field = str_replace("\035","",$field);
	$field = str_replace("\036","",$field);
	$field = str_replace("\037","",$field);
	$formatted = stripslashes($field);
	return $formatted;
}

/////////////////////////////////////////////////////
//
//
// Main Script
//
//
/////////////////////////////////////////////////////

// Authentication
///////////////////////////////////////////////////

$db  = &AgeDB::getDB();
$sql = "select confidentiality,apassword,fid from AGENDA where id='$ida'";
$res = $db->query($sql);
if (DB::isError($res)) {
	die ($res->getMessage());
}

if ( $res->numRows() == 0 ) {
	if ( ERRORLOG )
		$log->logError( __FILE__ . "." . __LINE__, 
                        "main", 
                        " Cannot find agenda with id='$ida' " );
	echo "$ida: No such agenda";
	exit;
}

$row = $res->fetchRow();
$fid = $row['fid'];
$confidentiality = $row['confidentiality'];
$apassword       = $row['apassword'];

// get default values from father category
$sql2 = "SELECT accessPassword
        FROM LEVEL
        WHERE uid='$fid'";
$res2 = $db->query($sql2);
$row2 = $res2->fetchRow();
$globalprotect = $row2['accessPassword'];

if ($confidentiality == "password" || $globalprotect != "")
{
	if ($PHP_AUTH_PW == "") {
		Header("WWW-Authenticate: Basic realm=\"agenda\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "XMLoutput: Access denied.\n";
		exit;
	}
	else if ($PHP_AUTH_PW != $apassword && $PHP_AUTH_PW != $globalprotect ) {
		Header("WWW-Authenticate: Basic realm=\"agenda\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "XMLoutput: Access denied.\n";
		exit;
	}
}
?>