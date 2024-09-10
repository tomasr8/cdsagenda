<?php
// $Id: userLog.php,v 1.1.1.1.4.4 2003/03/28 10:30:04 tbaron Exp $

// userLog.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Erik Simon <erik.simon@cern.ch>
//
// userLog.php is part of CDS Agenda.
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

include_once( "config/config.php" );
require_once( "platform/template/template.inc" );
require_once( "platform/authentication/sessinit.inc" );

$userid = $_SESSION["userid"];
$userid_email = getEmail($userid);

if ($_POST["action"] == "cancel") {
    Header("Location: $nextpage?$nextquery");
}

if ($_POST["action"] == "logout") {
    logout($userid,$userid_email);
    Header("Location: $nextpage?$nextquery");
}

if ($_POST["action"] == "register") {
    $msg = register($userid,$userid_email,$_POST["logtext"],$_POST["passtext"]);
    if ($msg == "") {
        Header("Location: $nextpage?$nextquery");
    }
}

if ($_POST["action"] == "login") {
    $msg = login($userid,$userid_email,$_POST["logtext"],$_POST["passtext"]);
    if ($msg == "") {
        Header("Location: $nextpage?$nextquery");
    }
}

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array( "mainpage"=> "userLog.ihtml" ));

$Template->set_var("userLog_msg", $msg );
$Template->set_var("userLog_action", $nextpage );
$Template->set_var("userLog_query", $nextquery );
$Template->set_var("userLog_logtext", $logtext );

echo "<font color=red>Do not use your CERN password! This website is horribly insecure and passwords are stored in plaintext among other things.</font>";
$Template->pparse( "final-page", "mainpage" );
?>