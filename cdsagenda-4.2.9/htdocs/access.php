<?php
// $Id: access.php,v 1.1.1.1.4.10 2004/07/29 10:06:03 tbaron Exp $

// access.php --- access to the modification area
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// access.php is part of CDS Agenda.
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
require_once 'platform/template/template.inc';
require_once 'platform/system/commonfunctions.inc';
require_once "platform/authentication/sessinit.inc";

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "error"=>"error.ihtml" ));

if (($authentication && !canModifyAgenda($userid,$ida)) && $AN == "")
{
	outError("Please enter a valid password...","01", &$Template );
	exit;
}
else
{
    $db  = &AgeDB::getDB();
    if ($authentication && canModifyAgenda($userid,$ida)) {
        $sql = "SELECT an
                FROM AGENDA
                WHERE id='$ida'";
        $res = $db->query($sql);
        $row = $res->fetchRow();
        $an = $row['an'];
        setcookie("AN$ida","$an",0);
    }
    else {
        // First try with global protection
        $sql = "SELECT uid
                FROM LEVEL,AGENDA
                WHERE AGENDA.fid=uid and
                      modifyPassword!='' and
                      modifyPassword='$AN' and
                      id='$ida'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        $numRows = $res->numRows();
        if ($numRows != "1") {
            // Then try with the agenda protection
            $sql = "SELECT 	id 
                    FROM 	AGENDA 
                    WHERE 	an='$AN' and id='$ida'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ($res->getMessage());
            }
            $numRows = $res->numRows();
            if ($numRows != "1") {
                outError("Sorry, wrong password","01", &$Template );
                exit;
            }
        }
        if ($authentication) {
            setUserModifyAgendaAuthorization($userid,$ida);
        }
        setcookie("AN$ida","$AN",0);
    }
}
Header("Location:modification/displayAgenda.php?ida=$ida&position=$position");

?>