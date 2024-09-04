<?php
// $Id: imapfunctions.inc,v 1.1.2.1 2003/03/28 10:12:38 tbaron Exp $

// imapfunctions.inc --- this script includes all functions to access
// the IMAP server
//
// Copyright (C) 2003  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// imapfunctions.inc is part of eAgenda.
//
// eAgenda is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// eAgenda is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with eAgenda; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// Commentary:
// Non-CERN user should consider modifying this script for local use.
// 
//

function IMAPLogin($username,$password)
{
    $error = "";
    $IMAPserver = "{".$username.".mailbox.cern.ch:143/imap}";
    $mbox = @imap_open ($IMAPserver, $username, $password, OP_READONLY && OP_HALFOPEN );
    if ( $mbox ) {
        imap_close( $mbox ); 
    }
    else {
        $error = "Cannot login with IMAP... Please check your username and password.";
    }
    return $error;
}

?>
