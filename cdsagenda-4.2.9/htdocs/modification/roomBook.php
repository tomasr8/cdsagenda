<?php
// $Id: roomBook.php,v 1.1.1.1.4.5 2002/10/23 08:56:36 tbaron Exp $

// roomBook.php --- link to the room booking service
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// roomBook.php is part of CDS Agenda.
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

$db = &AgeDB::getDB();
$sql = "select speriod1 from SESSION where ida='$ida' and ids='$ids'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ( $numRows == 0 )
     if ( ERRORLOG )
{
    $log = &AgeLog::getLog();
    $log->logError( __FILE__ . "." . __LINE__, "main", " Retrieved 0 elements with select '". $GLOBALS[ "table" ]->lastSelect . "' " );
}

$row = $res->fetchRow();

$sday = $row['speriod1'];

$sinfo = split("-",$sday,3);
if ( $sinfo[1] == "01" ) { $sinfo[1] = "JAN"; }
if ( $sinfo[1] == "02" ) { $sinfo[1] = "FEB"; }
if ( $sinfo[1] == "03" ) { $sinfo[1] = "MAR"; }
if ( $sinfo[1] == "04" ) { $sinfo[1] = "APR"; }
if ( $sinfo[1] == "05" ) { $sinfo[1] = "MAY"; }
if ( $sinfo[1] == "06" ) { $sinfo[1] = "JUN"; }
if ( $sinfo[1] == "07" ) { $sinfo[1] = "JUL"; }
if ( $sinfo[1] == "08" ) { $sinfo[1] = "AUG"; }
if ( $sinfo[1] == "09" ) { $sinfo[1] = "SEP"; }
if ( $sinfo[1] == "10" ) { $sinfo[1] = "OCT"; }
if ( $sinfo[1] == "11" ) { $sinfo[1] = "NOV"; }
if ( $sinfo[1] == "12" ) { $sinfo[1] = "DEC"; }
$sinfo[0] = substr($sinfo[0], -2);
$sdate = "${sinfo[2]}-${sinfo[1]}-${sinfo[0]}";


Header("Location: $libraryADDRESS?" . ( $withParameters != "" ? $withParameters  . $sdate : "" ));
?>