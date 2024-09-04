<?php
// $Id: synchronizefiles.php,v 1.1.2.1 2003/01/16 14:49:32 tbaron Exp $

// synchronizefiles.php --- synchronization between db and file system
//
// Copyright (C) 2003  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// synchronizefiles.php is part of eAgenda.
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
// Commentary: This script is to be launched from the command-line
// It will parse the archive area of the installed Agenda system, and
// create one database entry per found file. Additionally, error
// messages are stored in a file named synchronize_error.txt
//
// 
//

require_once '../htdocs/AgeDB.php';
require_once '../htdocs/config/config.php';
require_once '../htdocs/platform/system/archive.inc';

$db = &AgeDB::getDB();
$archive = new archive();
$numfiles = 0;

$fp = fopen("synchronize_error.txt","w");

$maindir = opendir($ARCHIVE);
while ($agenda = readdir($maindir)) {
    if ($agenda != "." && $agenda != "..") {
        if (is_file("$ARCHIVE/$agenda")) {
            fwrite($fp,"found bad file: $ARCHIVE/$agenda\n");
        }
        else {   
            $agendadir = opendir("$ARCHIVE/$agenda");
            while ($eventid = readdir($agendadir)) {
                if ($eventid != "." && $eventid != "..") {
                    if (is_file("$ARCHIVE/$agenda/$eventid")){
                        fwrite($fp,"found bad file: $ARCHIVE/$agenda/$eventid\n");
                    }
                    else {
                        $eventdir = opendir("$ARCHIVE/$agenda/$eventid");
                        while ($file = readdir($eventdir)) {
                            if ($file != "." && $file != "..") {
                                if (is_file("$ARCHIVE/$agenda/$eventid/$file") && !$archive->fileExists($eventid,"$ARCHIVE/$agenda/$eventid",$file)) {
                                    $result = $archive->addFile($eventid,
                                                                "$ARCHIVE/$agenda/$eventid",
                                                                "$file",
                                                                "moreinfo",
                                                                filesize("$ARCHIVE/$agenda/$eventid/$file"));
                                    if (!$result) {
                                        die("error in addFile");
                                    }
                                    if ($result == 1) {
                                        $numfiles++;
                                    }
                                }
                                if (is_dir("$ARCHIVE/$agenda/$eventid/$file")) {
                                    $filedir = opendir ("$ARCHIVE/$agenda/$eventid/$file");
                                    while ($file2 = readdir($filedir)) {
                                        if ($file2 != "." && $file2 != "..") {
                                            if (is_file("$ARCHIVE/$agenda/$eventid/$file/$file2") && !$archive->fileExists($eventid,"$ARCHIVE/$agenda/$eventid/$file",$file2)) {
                                                $result = $archive->addFile($eventid,
                                                                            "$ARCHIVE/$agenda/$eventid/$file",
                                                                            "$file2",
                                                                            "$file",
                                                                            filesize("$ARCHIVE/$agenda/$eventid/$file/$file2"));
                                                if (!$result) {
                                                    die("error in addFile");
                                                }
                                                if ($result == 1) {
                                                    $numfiles++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }                  
                        }
                    }
                }
            }
        }
    }

    print "Inserted $numfiles files";
    fclose($fp);
}

?>
