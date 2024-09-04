<?php
// $Id: pureXML.php,v 1.1.1.1.4.5 2003/03/28 10:31:21 tbaron Exp $

// pureXML.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// pureXML.php is part of CDS Agenda.
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
// Changed by T Baron (26/09/2001) XMLoutput is included instead of
// system called
//
	
require_once 'AgeDB.php';
require_once 'AgeLog.php';
require_once 'config/config.php';
require_once 'platform/template/template.inc' );
		
if ( ERRORLOG ) {
    $log = &AgeLog::getLog();
}
	 
$db  = &AgeDB::getDB();
$sql = "select confidentiality,apassword,fid from AGENDA where id='$ida'";
$res = $db->query($sql);

if (DB::isError($res)) {
	die ($res->getMessage());
}

$numRows = $res->numRows();

// Authentication
///////////////////////////////////////////////////

if ( $numRows == 0 )
{
	if ( ERRORLOG )
		$log->logError( __FILE__ . "." . __LINE__, "main", " Cannot find agenda with id='$ida' " );
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

if ( $confidentiality == "password" || $globalprotect != "")
{
	if ( $PHP_AUTH_PW == "" ) {
		Header("WWW-Authenticate: Basic realm=\"agenda\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "pureXML: Access denied.\n";
		exit;
	}
	else if ( $PHP_AUTH_PW != $apassword && $PHP_AUTH_PW != $globalprotect ) {
		Header("WWW-Authenticate: Basic realm=\"agenda\"");
		Header("HTTP/1.0 401 Unauthorized");
		echo "pureXML: Access denied.\n";
		exit;
	}
}
Header("content-type: text/plain\n\n");

// call the main program
include 'XMLoutput.php';

$agenda = new agenda($ida);
print $agenda->displayXML();
?>