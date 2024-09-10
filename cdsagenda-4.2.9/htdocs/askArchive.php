<?php
// $Id: askArchive.php,v 1.1.1.1.4.16 2004/10/06 13:56:25 tbaron Exp $

// askArchive.php --- link manager
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// askArchive.php is part of CDS Agenda.
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

//////////////////////////////////////////////////////////////////////
// Receipt the requests for files into the archive one way to work is
// to be compatible with the cgi-bin/setlink way to retrieve fields
// with parameters:
//      base = agenda
//      categ = idAgenda
//      id=idEvent/filesType
// It will show a list of files of the filesType type, if the file is
// only one it will directly send this file
//////////////////////////////////////////////////////////////////////

require_once 'AgeDB.php';
require_once 'config/config.php';
require_once 'platform/template/template.inc';
require_once 'platform/system/commonfunctions.inc';
require_once 'platform/system/archive.inc';

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file( array( "mainpage"=> "askArchive.ihtml",
							"element" => "askArchiveElement.ihtml",
							"error"   => "error.ihtml" ));

$archive = new archive();
$db = &AgeDB::getDB();

$Template->set_var( "askArchive_title", " Archive retriving system " );

if (preg_match("/\.\.\//",$QUERY_STRING)){
    outError("This access is not allowed.","01",$Template);
    mail($support_email,"strange call on askArchive","$AGE_WWW/askArchive?".$QUERY_STRING);
    exit;
}

if (!preg_match("/=/",$QUERY_STRING)) {
	$base = "agenda";
	$QUERY_STRING = urldecode($QUERY_STRING);
	$QUERY_STRING = str_replace(" ","+",$QUERY_STRING);
	$categ = preg_replace("/\/.*/","",$QUERY_STRING);
	$id = preg_replace("/^$categ\//","",$QUERY_STRING);
}

$base = $_GET['base'];
$categ = $_GET['categ'];
$id = $_GET['id'];

if ($categ == "") {
    outError("Sorry... Parameter <i>categ</i> needed","01",$Template);
    exit;
}
elseif ($id == "") {
    outError("Sorry... Parameter <i>id</i> needed","01",$Template);
    exit;
}
elseif (!file_exists("$ARCHIVE/$categ")) {
    outError("Sorry... Cannot find agenda $categ","01",$Template);
    exit;
}
else {
    // initialise variables
    $numfiles = 0;
    $id = str_replace("\\","",$id);
    $additionalfiletext = "<blockquote><table border=0 cellspacing=2>";

    if (is_file("$ARCHIVE/$categ/$id")) {
        $lastfile = "$ARCHIVE/$categ/$id";
        $numfiles = 1;
    }
    elseif (is_dir("$ARCHIVE/$categ/$id")) {
        // first open base directory
        $dp = opendir ("$ARCHIVE/$categ/$id");
        while ($addfile = readdir($dp)) {
            if (is_file("$ARCHIVE/$categ/$id/$addfile") && $addfile != "." && $addfile != ".." && !preg_match("/^icon-.*.gif/",$addfile)) {
                $numfiles++;
                $lastfile = "$ARCHIVE/$categ/$id/$addfile";
                $size = filesize("$ARCHIVE/$categ/$id/$addfile");
                $filename = preg_replace("/([^\.]*)\..*/","\\1",$addfile);
                if (file_exists("$ARCHIVE/$categ/$id/icon-$filename.gif")) {
                    $imagesrc = "$ARCHIVEURL/$categ/$id/icon-$filename.gif";
                }
                else {
                    $imagesrc = "$IMAGES/smallfiles.gif";
                }
                $additionalfiletext .= "<tr><td align=right><img src='$imagesrc'></td><td valign=top><a href='$AGE_WWW/askArchive.php?base=agenda&categ=$categ&id=$id/$addfile'>$addfile</a></td><td valign=top><font size=-2 color=green>[$size o]</font></td></tr>";
            }
        }
        closedir($dp);
        $additionalfiletext .= "</table></blockquote>";

        if ($numfiles != 0) {
            $numberoffilestext = "$numfiles";
            $Template->set_var( "askArchive_number", $numberoffilestext );
            $Template->set_var( "askArchive_element", $additionalfiletext );
        }
        else {
            outError("file not found...","01",$Template);
            exit;
        }
    }
    $Template->set_var( "askArchive_backagenda", "$AGE_WWW/fullAgenda.php?ida=$categ" );
}

if ($numfiles == 0) {
	outError("file not found...","01",$Template);
        exit;
}
elseif ($numfiles != 1) {
    $Template->pparse( "final-page", "mainpage" );
}
else {
    if (preg_match("/\./",$lastfile)) {
        $extension = strtolower(preg_replace("/.*\./","",$lastfile));
    }
    else {
        $extension = "";
    }
    if ($extension == "link") {
        include ("$lastfile");
        exit;
    }

    // Authorization
    // First check if a password is associated with the single file
    $eventID = preg_replace("/\/.*/","",$id);
    $apassword = "";
    $fileID = $archive->getFileIDFromFullPath($eventID,$lastfile);
    if ($fileID) {
        $sql = "SELECT password
                FROM FILE_PASSWORD
                WHERE fileID='$fileID'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $apassword = $row['password'];
        }
    }
    if (!$apassword) {
        // Then check if a password is associated with the event
        $sql = "SELECT password
                FROM EVENT_PASSWORD
                WHERE eventID='$eventID'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $apassword = $row['password'];
        }
        else {
            // else if a password is associated with the agenda
            $sql = "select confidentiality,apassword from AGENDA where id='$categ'";
            $res = $db->query($sql);
            if (DB::isError($res)) {
                die ($res->getMessage());
            }
            $row = $res->fetchRow();
            $confidentiality = $row['confidentiality'];
            if ($confidentiality == "password") {
                $apassword = $row['apassword'];
            }
            else {
                // if a password is associated with the category
                $sql = "SELECT accessPassword
                        FROM LEVEL,AGENDA
                        WHERE uid=AGENDA.fid and
                              id='$categ'";
                $res = $db->query($sql);
                if (DB::isError($res)) {
                    die ($res->getMessage());
                }
                $row = $res->fetchRow();
                $apassword = $row['accessPassword'];
            }
        }
    }

    // ACCESS WITH A PASSWORD
    if ( $apassword != "") {
        if ( $PHP_AUTH_PW == "" ) {
            Header( "WWW-Authenticate: Basic realm=\"agenda\"" );
            Header( "HTTP/1.0 401 Unauthorized" );
            echo "Access denied.\n";
            exit;
        }
        else if ( $PHP_AUTH_PW != $apassword ) {
            Header( "WWW-Authenticate: Basic realm=\"agenda\"" );
            Header( "HTTP/1.0 401 Unauthorized" );
            echo "Access denied.\n";
            exit;
        }
    }
    // Domain restriction
    elseif ($confidentiality == "cern-only") {
        $allowed = false;
        // Now checks using an array from config/config.php
        for ( $indX = 0; $indX < count( $restrictAddress ); $indX++ ) {
            if ( ereg( $restrictAddress[ $indX ], $REMOTE_ADDR )) {
                $allowed = true;
                continue;
            }
        }
        if ( !$allowed ) {
            Header("WWW-Authenticate: Basic realm=\"agenda\"");
            Header("HTTP/1.0 401 Unauthorized");
            echo "Access restricted to $runningAT users!";
            exit();
        }
    }

    //Output file content
    $mimetype = $RECOGNIZED_MIME_TYPES[$extension];
    if ($mimetype == "") {
        $mimetype = "unknown";
    }
    header("Content-Length: ".filesize("$lastfile"));
    header("Content-Type: $mimetype");
    header("Content-Disposition: inline; filename=\"".preg_replace("/.*\//","",$lastfile)."\"");

    $fp = fopen("$lastfile","r");
    while ($chunk = fread($fp,1024)) {
	    print $chunk;
    }
    fclose($fp);
}

?>
