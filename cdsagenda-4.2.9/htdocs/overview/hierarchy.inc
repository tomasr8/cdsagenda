<?php
// $Id: hierarchy.inc,v 1.1.1.1.4.6 2004/07/29 10:06:10 tbaron Exp $

// hierarchy.inc --- hierarchical structure functions
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// hierarchy.inc is part of CDS Agenda.
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

require_once "$AGE/AgeDB.php";


// from a level id, this function returns an array containing all sons
// of this level
function getChildren($fid)
{
    $array = array();

    $db = &AgeDB::getDB();
    $sql = "select uid from LEVEL where fid='$fid'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    $numRows = $res->numRows();

    if ($numRows != 0) {
        for ($i=0; $numRows > $i;$i++) {
            $row = $res->fetchRow();
            array_push($array,$row['uid']);
            $array = array_merge($array,getChildren($row['uid']));
        }
    }
    return $array;
}


// from an array passed as parameter, this function returns a
// bracket-enclosed, comma-separated list of all elements in the array
function createTextFromArray($array)
{
    $text = "(''"; // Start with (''

    foreach ($array as $value) {
        $text .= ",'".$value."'";
    }

    $text .= ")"; // Close the parentheses
    return $text;
}


//given a level id, this function returns the hierarchical tree under
//this level
function getFatherTree($uid)
{
    global $IMAGES,$AGE;

    $array = array();
    $db = &AgeDB::getDB();

    $sql = "select fid,level from LEVEL where uid='$uid'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    $numRows = $res->numRows();

    if ($numRows != 0) {
        $row = $res->fetchRow();
        if (file_exists("$AGE/$IMAGES/levelicons/$uid.gif")) {
            $iconname = "$uid.gif";
        }
        else {
            $iconname = "";
        }
        $level = array("uid" => $uid,"icon" => $iconname,"level" => $row['level']);
        array_push($array,$level);
        if ($row['fid'] != "0") {
            $array = array_merge($array,getFatherTree($row['fid']));
        }
    }
    return $array;
}


// given a hierarchical tree, this function returns the highest level
// id which icon field is not empty
function getIconLevel($array, $maxlevel)
{
    $iconlevel = "";

    foreach ($array as $val) {
        if (!empty($val['icon']) && $val['level'] >= $maxlevel) {
            $iconlevel = $val['uid'];
        }
    }

    return $iconlevel;
}


//given a level id, this function returns the corresponding icon
//picture name
function getIcon($uid)
{
    global $IMAGES,$AGE;

    if (file_exists("$AGE/$IMAGES/levelicons/$uid.gif")) {
        return $uid;
    }
    else {
        return "";
    }
}

//get number of agendas inside given categories
function getAgendaNumber($array)
{
    $levelsettext = createTextFromArray($array);
    $db = &AgeDB::getDB();
    $sql = "	SELECT COUNT(id) as count
		FROM AGENDA
		WHERE fid in $levelsettext and fid!=''";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
	return $row['count'];
    }
    else {
	return 0;
    }
}
?>
