<?php
// $Id: file.inc,v 1.1.2.1 2003/01/16 14:48:17 tbaron Exp $

// file.inc --- file class definition
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// file.inc is part of CDS Agenda.
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

class file
{
    var $id;
    var $name;
    var $path;
    var $format;
    var $category;
    var $description;
    var $cd;
    var $md;
    var $numPages;
    var $size;
    var $deleted;
    
    var $eventID;
    var $userID;

    function file($id, $format, $category, $cd, $md, $name, $path, $description,$numPages, $size, $deleted, $eventID, $userID){
        $this->id = $id;
        $this->format = $format;
        $this->category = $category;
        $this->cd = $cd;
        $this->md = $md;
        $this->numPages = $numPages;
        $this->size = $size;
        $this->deleted = $deleted;
        $this->eventID = $eventID;
        $this->userID = $userID;
        $this->path = $path;
        $this->name = $name;
    }

    function getField($fieldname) {
        $vars = get_object_vars($this);
        if ($vars["$fieldname"]) {
            return $vars["$fieldname"];
        }
    }

    function changeField($fieldname, $value) {
        global $db;

        $sql = "UPDATE FILE
                SET $fieldname = '$value',
                    md = NOW()
                WHERE id = '".$this->id."'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            return false;
        }
        return true;
    }
}
?>
