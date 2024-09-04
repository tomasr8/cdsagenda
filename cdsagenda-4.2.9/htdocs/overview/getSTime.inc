<?php
// $Id: getSTime.inc,v 1.1.1.1.4.4 2002/11/13 14:52:08 tbaron Exp $

// getSTime.inc --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// getSTime.inc is part of CDS Agenda.
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

function Get_Stime($id,$thisday) {
    $db = &AgeDB::getDB();

    $sqlAgenda = "select stime from AGENDA where id='$id' and stdate='$thisday'";
    $resAgenda = $db->query($sqlAgenda);
    if (DB::isError($resAgenda)) {
        die ($resAgenda->getMessage());
    }
    if ($rowAgenda = $resAgenda->fetchRow()) {
        if( $rowAgenda['stime'] != "00:00:00" ) {
            return $rowAgenda['stime'];
        }
    }

    $sqlSession = "select stime from SESSION where ida='$id' and speriod1='$thisday' and stime!='00:00:00' order by stime LIMIT 1";
    $resSession = $db->query($sqlSession);
    if (DB::isError($resSession)) {
        die ($resSession->getMessage());
    }
	if ( $rowSession = $resSession->fetchRow()) {
        return $rowSession['stime'];
    }

    $sqlTalk = "select stime from TALK where ida='$id' and tday='$thisday' and stime!='00:00:00' order by stime LIMIT 1";
    $resTalk = $db->query($sqlTalk);
    if (DB::isError($resTalk)) {
        die ($resTalk->getMessage());
    }
    if ( $rowTalk = $resTalk->fetchRow() ) {
        return $rowTalk['stime'];
    }
     
    return "";
}
?>