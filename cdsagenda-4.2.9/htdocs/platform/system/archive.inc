<?php
// $Id: archive.inc,v 1.1.2.3 2004/10/06 13:46:33 tbaron Exp $

// archive.inc --- archive management
//
// Copyright (C) 2003  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// archive.inc is part of eAgenda.
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
//
// 
//

require_once 'file.inc';

class archive
{
    var $server;
    var $protocol;

    function archive( $server="localhost", $protocol="filesystem") {
        global $db;

        $this->server = $server;
        $this->protocol = $protocol;
    }

    function addFile($eventID, $path, $name, $category, $size = 0, $description = "", $numPages = 0, $format = "") {
        global $db;

        if ($this->fileExists($eventID, $path, $name)) {
            return -1;
        }
        
        $sql = "INSERT 
                INTO FILE (format, 
                           category, 
                           name, 
                           path, 
                           description, 
                           numPages, 
                           size, 
                           deleted,
                           cd,
                           md)
                VALUES ('$format',
                        '$category',
                        '".$name."',
                        '".$path."',
                        '$description',
                        '$numPages',
                        '$size',
                        '0',
                        NOW(),
                        NOW())";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        // $fileID = mysql_insert_id();
        $fileID = $db->getOne("SELECT LAST_INSERT_ID()");

        // Add the FILE_EVENT entry
        $sql = "INSERT 
                INTO FILE_EVENT
                values('$fileID','$eventID')";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }

        return 1;    
    }

    function fileExists($eventID, $path, $name) {
        global $db;

        $sql = "SELECT FILE.id
                FROM FILE,FILE_EVENT
                WHERE FILE.path = '".$path."' and
                      FILE.name = '".$name."' and 
                      eventID = '$eventID' and
                      FILE.id = FILE_EVENT.fileID and
                      FILE.deleted = 0";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        $num = $res->numRows();
        return $num;
    }

    function getFileID($eventID, $path, $name) {
        global $db;
        $sql = "SELECT FILE.id
                FROM FILE,FILE_EVENT
                WHERE FILE.path = '".$path."' and
                      FILE.name = '".$name."' and 
                      eventID = '$eventID' and
                      FILE.id = FILE_EVENT.fileID";

        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            return $row['id'];
        }
        else {
            return false;
        }
    }

    function getFileIDFromFullPath($eventID, $fullpath) {
        global $db;

        $name = preg_replace("/.*\//","",$fullpath);
        $path = str_replace("/$name","",$fullpath);

        return $this->getFileID($eventID, $path, $name);
    }

    function getFile($fileID) {
        global $db;

        $sql = "SELECT eventID
                FROM FILE_EVENT
                WHERE fileID = '$fileID'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $eventID = $row['eventID'];
        }
        else {
            $eventID = "";
        }

        $sql = "SELECT *
                FROM FILE
                WHERE id = '$fileID'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $newfile = new file($row['id'],
                            $row['format'],
                            $row['category'],
                            $row['cd'],
                            $row['md'],
                            $row['name'],
                            $row['path'],
                            $row['description'],
                            $row['numPages'],
                            $row['size'],
                            $row['deleted'],
                            "$eventID",
                            "");
            return $newfile;
        }
        else {
            return false;
        }
    }

    function listFiles($eventID, $category = "") {
        global $db;
        $files = array();

        if ($category != "") {
            $additional_sql = " and FILE.category = '$category'";
        }
        else {
            $additional_sql = "";
        }

        // Retrieve all files associated with event eventID
        $sql = "SELECT FILE.* 
                FROM FILE,FILE_EVENT
                WHERE eventID = '$eventID' and
                      FILE.id = FILE_EVENT.fileID and
                      FILE.deleted = 0 " . $additional_sql;
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        while ($row = $res->fetchRow()) {
            $newfile = $this->getFile($row['id']);
            array_push($files,$newfile);
        }
        return $files;
    }

    function listDeletedFiles($eventID, $fromDate = "", $toDate = "") {
        global $db;
        $files = array();

        $additional_sql = "";
        if ($fromDate != "") {
            $additional_sql .= " and FILE.md >= '$fromDate'";
        }
        if ($toDate != "") {
            $additional_sql .= " and FILE.md <= '$toDate'";
        }

        // Retrieve all files associated with event eventID
        $sql = "SELECT FILE.* 
                FROM FILE,FILE_EVENT
                WHERE eventID = '$eventID' and
                      FILE.id = FILE_EVENT.fileID and
                      FILE.deleted = 0 " . $additional_sql;
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        while ($row = $res->fetchRow()) {
            $newfile = $this->getFile($row['id']);
            array_push($files,$newfile);
        }
        return $files;
    }

    function listFileCategories($eventID) {
        global $db;
        $categories = array();

        // Retrieve all files associated with event eventID
        $sql = "SELECT DISTINCT FILE.category 
                FROM FILE,FILE_EVENT
                WHERE eventID = '$eventID' and
                      FILE.id = FILE_EVENT.fileID and
                      FILE.deleted = 0";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        while ($row = $res->fetchRow()) {
            array_push($categories,$row['category']);
        }
        return $categories;
    }

    function listUserFiles($userID, $fromDate = "", $toDate = "") {
        global $db;
        $files = array();

        $additional_sql = "";
        if ($fromDate != "") {
            $additional_sql .= " and FILE.md >= '$fromDate'";
        }
        if ($toDate != "") {
            $additional_sql .= " and FILE.md <= '$toDate'";
        }

        // Retrieve all files associated with event eventID
        $sql = "SELECT FILE.* 
                FROM FILE,FILE_USER
                WHERE userID = $userID and
                      FILE.id = FILE_USER.fileID and
                      FILE.deleted = 1 " . $additional_sql;
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ("$sql: ".$res->getMessage());
        }
        while ($row = $res->fetchRow()) {
            $newfile = $this->getFile($row['id']);
            array_push($files,$newfile);
        }
        return $files;
    }

    function deleteFile($fileID,$useTrash = false) {
        global $ARCHIVETRASH, $db;
        $thisfile = $this->getFile($fileID);
        if (!$thisfile) {
            return false;
        }
        $oldpath = $thisfile->path."/".$thisfile->name;
        $newpath = $ARCHIVETRASH;
        $newname = preg_replace("/\//","_",$oldpath);
        if ($useTrash) {
            if (!file_exists("$newpath")) {
                mkdir("$newpath", 0777);
                @chmod("$newpath", 0777);
            }
            if (!rename($oldpath, $newpath."/".$newname)) {
                return false;
            }
            $sql = "INSERT 
                    INTO FILETRASH (id, deletedName, deletedPath, deletionDate)
                    values('$fileID', '".$newname."', '".$newpath."', NOW())";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ("$sql: ".$res->getMessage());
            }
            $sql = "UPDATE FILE
                SET deleted = 1,
                    md = NOW()
                WHERE id = '$fileID'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ("$sql: ".$res->getMessage());
            }
        }
        else {
            $sql = "DELETE 
                    FROM FILE
                    where id = '$fileID'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ("$sql: ".$res->getMessage());
            }
            $sql = "DELETE 
                    FROM FILETRASH
                    where id = '$fileID'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ("$sql: ".$res->getMessage());
            }
            $sql = "DELETE 
                    FROM FILE_EVENT
                    where fileID = '$fileID'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ("$sql: ".$res->getMessage());
            }
            $sql = "DELETE 
                    FROM FILE_PASSWORD
                    where fileID = '$fileID'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ("$sql: ".$res->getMessage());
            }
            if (!unlink($oldpath)) {
                return false;
            }
        }
        return true;
    }
    
    function deleteAllFiles($eventID, $category = "", $useTrash = false) {
        $files = array();
        $files = $this->listFiles($eventID, $category);
        if (count($files) > 0) {
            reset($files);
            while ($file = current($files)) {
                $this->deleteFile($file->getField('id'));
                next($files);
            }
        }
        else {
            return false;
        }
    }

    function getFileField($fileID, $fieldName) {
        $file = getFile($fileID);
        if (!$thisfile) {
            return false;
        }
        return $file->getField($fieldname);
    }

    function changeFileField($fileID, $fieldName, $value) {
        $file = getFile($fileID);
        if (!$thisfile) {
            return false;
        }
        return $file->changeField($fieldName, $value);
    }

    function synchronize($eventID) {
        global $ARCHIVE;
        
        $ida = preg_replace("/s.*/","",$eventID);
        $agendadir="$ARCHIVE/$ida";

        $d = opendir("$agendadir/$eventID");
        while ($entry = readdir($d)) {
            if ($entry != "." && $entry != "..") {
                if (@is_dir("$agendadir/$eventID/$entry")) {
                    $d2=opendir("$agendadir/$eventID/$entry");
                    while ($entry2=readdir($d2)) {
                        if ($entry2 != "." && $entry2 != "..") {
                            // Synchronization with the database
                            if (!$this->getFileID("$eventID",
                                                     "$agendadir/$eventID/$entry",
                                                     "$entry2")) {
                                $this->addFile("$eventID",
                                                  "$agendadir/$eventID/$entry",
                                                  "$entry2",
                                                  "$entry",
                                                  filesize("$agendadir/$eventID/$entry/$entry2"));
                            }
                        }
                    }
                }
            }
        }
    }
}

?>
