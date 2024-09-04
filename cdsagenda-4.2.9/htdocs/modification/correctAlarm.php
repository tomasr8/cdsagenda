<?php
// $Id: correctAlarm.php,v 1.1.1.1.4.5 2002/10/23 08:56:34 tbaron Exp $

// correctAlarm.php --- correct alarm settings
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// correctAlarm.php is part of CDS Agenda.
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
require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once '../platform/system/logManager.inc';

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage"=> "correctAlarm.ihtml" ));

$db = &AgeDB::getDB();
$sql = "select id,address,delay,text,include_agenda from ALERT ".
"where id='$ida' and delay=$delay and address='$address'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ( $numRows == 0 ) {
	if ( ERRORLOG ) {
        $log = &AgeLog::getLog();
		$log->logError( __FILE__ . "." . __LINE__, "main", " Retrieved 0 elements with select '" . $sql . "' " );
	}
}          

$row = $res->fetchRow();

$Template->set_var("images","$IMAGES_WWW");

$str_help="Correct Alarm:" . $row['address'] . "<BR>
	<INPUT type=hidden name=\"period\" value=\"$delay\">
	<INPUT type=hidden name=\"address\" value=\"" . $row['address'] . "\">\n";

$Template->set_var("str_help","$str_help");

$str_help2="additional note: <TEXTAREA name=newnote rows=5 cols=50>" . $row['text'] . "</TEXTAREA><br>\n";
if ($row['include_agenda'] == "on") {
	$str_help2.= "
	<input type=checkbox name=include_agenda checked> Include a text version of the agenda in the email\n";
}
else {
	$str_help2.= "
	<input type=checkbox name=include_agenda> Include a text version of the agenda in the email\n";
}
$Template->set_var("str_help2","$str_help2");
	
$str_help3="<SCRIPT>document.forms[0].newperiod.selectedIndex=" . $row['delay'] . "-1;</SCRIPT>\n";
$str_help3.="<INPUT type=hidden name=ida value=$ida>\n";
$str_help3.="<INPUT type=hidden name=fid value=$fid>\n";
$str_help3.="<INPUT type=hidden name=level value=$level>\n";
$str_help3.="<INPUT type=hidden name=position value=$position>\n";
$str_help3.="<INPUT type=hidden name=stylesheet value=$stylesheet>\n";
	
$Template->set_var("str_help3","$str_help3");

$Template->pparse( "final-page" , "mainpage" );

?>