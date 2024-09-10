<?php
// $Id: changePassword.php,v 1.1.2.1 2003/03/28 10:36:55 tbaron Exp $

// changePassword.php --- change user password
//
// Copyright (C) 2003  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// cloneAgenda.php is part of CDS Agenda.
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
// Code:
//

require_once "config/config.php";
require_once "platform/template/template.inc";
require_once 'platform/system/logManager.inc';
require_once 'platform/system/commonfunctions.inc';
require_once 'platform/authentication/sessinit.inc';

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array("mainpage" => "changePassword.ihtml",
                          "error"    => "error.ihtml" ));

$Template->set_var("images","$IMAGES_WWW");

if (getEmail($userid) == "guest") {
    outError("User not logged... Cannot change password", "02", $Template);
    exit;
}

if ($request == "change") {
    if ($password != $password2) {
        $msg = "The passwords you entered are different. Please try again";
    }
    elseif ($password == "") {
        $msg = "Cannot set empty password. Please try again";
    }
    else {
        if (changePassword($userid,$password)) {
            $msg = "<script>window.close();</script>";
        }
        else {
            $msg = "The new password is the same as the old one. Please enter a new one.";
        }
    }
}

$Template->set_var("changepassword_msg","$msg");
$Template->set_var("changepassword_body","$body");

$Template->pparse( "final-page" , "mainpage" );
?>
