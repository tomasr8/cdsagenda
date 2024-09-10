<?php
// $Id: file.php,v 1.1.2.10 2004/10/06 13:52:24 tbaron Exp $

// file.php --- list all attached files
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// file.php is part of CDS Agenda.
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
require_once '../AgeLog.php';
require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once '../platform/system/logManager.inc';
require_once '../platform/system/archive.inc';

$ida = $_REQUEST[ "ida" ];
$ids = $_REQUEST[ "ids" ];
$idt = $_REQUEST[ "idt" ];
$ifd = $_REQUEST[ "ifd" ];
$position = $_REQUEST[ "position" ];
$type = $_REQUEST[ "type" ];
$delete = $_REQUEST[ "delete" ];

$AN = $_REQUEST[ "an" ];
$position = $_REQUEST[ "position" ];
$stylesheet = $_REQUEST[ "stylesheet" ];
$from = $_REQUEST[ "from" ];
$nbFiles = $_REQUEST[ "nbFiles" ];


// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( 	"mainpage"=> "file.ihtml",
                            "error" => "error.ihtml" ));

// New archive object
$archive = new archive();

/****************************************************************************
 * Check if we are using Cookies to make persistent the *********************
 * agenda/password  authorizations ******************************************
 ****************************************************************************/

$canRemove = true;
$canUpload = true;

/****************************************************************************
 * End of authorization checks **********************************************
 ****************************************************************************/

$db = &AgeDB::getDB();
$log = &AgeLog::getLog();

$Template->set_var( "images", $IMAGES_WWW );
$Template->set_var("file_from","file.php");

$dirmode = 0777;
$filmode = 0666;
umask(000);

if ( $canRemove ) {
    $Template->set_var( "file_deleteAll", "<INPUT class=headerselected type=submit name=deleteall value=\"delete all\" onClick=\"return confirm('Are you sure you want to delete all these files?');\">");
}
else {
    $Template->set_var( "file_deleteAll", "(You cannot delete files)");
}

$agendadir="$ARCHIVE/$ida";
$agendaurl="$ARCHIVEURL/$ida";

if (!@is_dir("$agendadir")) {
    if ( (! mkdir("$agendadir", $dirmode)) && ERRORLOG ) {
        $log->logError( __FILE__ . "." . __LINE__, "main", " Failed to mkdir '$agendadir' with permission '$dirmode' " );
    }
}

if (!@is_dir("$agendadir/$ida$ids$idt")) {
    if ( !( mkdir("$agendadir/$ida$ids$idt", $dirmode )) && ERRORLOG ) {
        $log->logError( __FILE__ . "." . __LINE__, "main", " Failed to mkdir '$agendadir/$ida$ids$idt' with permission '$dirmode' " );
    }
}

$Template->set_var("fileSes_IDA","$ida");
$Template->set_var("fileSes_IDS","$ids");
$Template->set_var("fileSes_IDT","$idt");
$Template->set_var("fileSes_AN","$AN");
$Template->set_var("fileSes_POSITION","$position");
$Template->set_var("fileSes_STYLESHEET","$stylesheet");

// Compute new event protection
if ($request == "Change Protection") {
    if ($protectiontype == "no" || $protectiontype == "global") {
        $sql = "DELETE
                FROM EVENT_PASSWORD
                WHERE eventID='$ida$ids$idt'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
    }
    elseif ($protectiontype == "password") {
        $sql = "SELECT *
                FROM EVENT_PASSWORD
                WHERE eventID='$ida$ids$idt'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $sql = "UPDATE EVENT_PASSWORD
                    SET password='$eventkey'
                    WHERE eventID='$ida$ids$idt'";
        }
        else {
            $sql = "INSERT
                    INTO EVENT_PASSWORD
                    VALUES('$ida$ids$idt','$eventkey')";
        }
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
    }
}

// Compute new single file protection
if ($request == "Change File Protection") {
    if ($protectiontype == "no" || $protectiontype == "global") {
        $sql = "DELETE
                FROM FILE_PASSWORD
                WHERE fileID='$fileID'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
    }
    elseif ($protectiontype == "password") {
        $sql = "SELECT *
                FROM FILE_PASSWORD
                WHERE fileID='$fileID'";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        if ($row = $res->fetchRow()) {
            $sql = "UPDATE FILE_PASSWORD
                    SET password='$filekey'
                    WHERE fileID='$fileID'";
        }
        else {
            $sql = "INSERT
                    INTO FILE_PASSWORD
                    VALUES('$fileID','$filekey')";
        }
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
    }
}

// Event Protection Level
if (isEventProtected($ida,$ids,$idt)) {
    $file_eventprotection = "<img src=".$IMAGES_WWW."/protected.gif alt='protected' border=0>";
}
else {
    $file_eventprotection = "<img src=".$IMAGES_WWW."/notprotected.gif alt='not protected' border=0>";
}
$Template->set_var("file_eventprotection","<a href=\"\" onClick=\"document.forms[0].action='eventprotection.php';document.forms[0].submit();return false;\">".$file_eventprotection."</a>");

// TODO this might've been important, but it crashes so..
// chdir ("$agendadir/$ida$ids$idt");

$str_help = "";

if ($createpdf != "" && $PS2PDF != "") {
    $newname = ereg_replace(".ps",".pdf",$createpdf);
    `$PS2PDF $agendadir/$ida$ids$idt/$filetype/$createpdf $agendadir/$ida$ids$idt/$filetype/$newname`;
    if (is_file("$agendadir/$ida$ids$idt/$filetype/$newname")) {
        @chmod("$agendadir/$ida$ids$idt/$filetype/$newname", $filmode);
        $result = $archive->addFile("$ida$ids$idt",
                          "$agendadir/$ida$ids$idt/$filetype",
                          "$newname",
                          "$filetype",
                          filesize("$agendadir/$ida$ids$idt/$filetype/$newname"));
    }
}

if ($createps != "" && $ACROREAD != "") {
    $newname = ereg_replace(".pdf",".ps",$createps);
    `$ACROREAD -toPostScript $agendadir/$ida$ids$idt/$filetype/$createps`;
    if (is_file("$agendadir/$ida$ids$idt/$filetype/$newname")) {
        @chmod("$agendadir/$ida$ids$idt/$filetype/$newname", $filmode);
        $result = $archive->addFile("$ida$ids$idt",
                          "$agendadir/$ida$ids$idt/$filetype",
                          "$newname",
                          "$filetype",
                          filesize("$agendadir/$ida$ids$idt/$filetype/$newname"));
    }
}

if ( $createtxtfrompdf != "" && $PS2ASCII != "") {
    $newname = ereg_replace(".pdf",".txt",$createtxtfrompdf);
    `$PS2ASCII $agendadir/$ida$ids$idt/$filetype/$createtxtfrompdf $agendadir/$ida$ids$idt/$filetype/$newname`;
    if (is_file("$agendadir/$ida$ids$idt/$filetype/$newname")) {
        @chmod("$agendadir/$ida$ids$idt/$filetype/$newname", $filmode);
        $result = $archive->addFile("$ida$ids$idt",
                          "$agendadir/$ida$ids$idt/$filetype",
                          "$newname",
                          "$filetype",
                          filesize("$agendadir/$ida$ids$idt/$filetype/$newname"));
    }
}

// Change the type of the file
if ($fileChange != "") {
    if (!@is_dir("$agendadir/$ida$ids$idt/$newfiletype")) { mkdir ("$agendadir/$ida$ids$idt/$newfiletype", $dirmode); }
    $name = basename($fileChange);

    if ($newfiletype != $filetype) {
        $myfileID = $archive->getFileID("$ida$ids$idt","$agendadir/$ida$ids$idt/$filetype","$fileChange");
        $myfile = $archive->getFile($myfileID);
        if ($myfile != "") {
            $myfile->changeField("category","$newfiletype");
            $myfile->changeField("path","$agendadir/$ida$ids$idt/$newfiletype");
            rename("$agendadir/$ida$ids$idt/$filetype/$fileChange","$agendadir/$ida$ids$idt/$newfiletype/$name");
        }
    }
}


if ($delete != "") {
    if ($archive->deleteFile("$delete")) {
        modifyLastUpdate();
    }
}

if ($deleteall != "") {
    if ($archive->deleteAllFiles("$ida$ids$idt")) {
        modifyLastUpdate();
    }
}


if ($Save != "") {
    if (!@is_dir("$agendadir/$ida$ids$idt/minutes")) {
        mkdir ("$agendadir/$ida$ids$idt/minutes", $dirmode);
    }
    @chmod("$agendadir/$ida$ids$idt/minutes", $dirmode);
    $fp=fopen("$agendadir/$ida$ids$idt/minutes/$ida$ids$idt.txt","w");
    $simpletext=wordwrap(stripslashes($simpletext),100,"\n");
    $simpletext=ereg_replace("\r\n","\n",$simpletext);
    fwrite($fp,$simpletext);
    fclose($fp);
    @chmod("$agendadir/$ida$ids$idt/minutes/$ida$ids$idt.txt", $filmode);
    if ($archive->addFile("$ida$ids$idt",
                          "$agendadir/$ida$ids$idt/minutes",
                          "$ida$ids$idt.txt",
                          "minutes",
                          filesize("$agendadir/$ida$ids$idt/minutes/$ida$ids$idt.txt"))) {
        modifyLastUpdate();
    }
}

$Template->set_var("str_help","$str_help");


/****************************************************************************
 * File uploads                **********************************************
 ****************************************************************************/

// Upload the files if requested by the user
$i = 1;
$filename = "Files$i";

while ($_FILES[$filename] != "")
{
    $destname = $_POST["destname$i"];
    $copyright = $_POST["copyright$i"];
    $deny = $_POST["deny$i"];
    $limitdomain = $_POST["limitdomain$i"];
    $fdescription = $_POST["fdescription$i"];
    $newTypeSelect = $_POST["newTypeSelect$i"];
    uploadFile($_FILES[$filename]["tmp_name"],str_replace("\\","",$_FILES[$filename]["name"]),$destname,$copyright,$deny,$limitdomain,$fdescription,$newTypeSelect);
    modifyLastUpdate();
    $i++;
    $filename = "Files$i";
}
// Test if must go to a specific script
if ( $i>1 && strcmp( $_REQUEST["query"], "" ) )
{
    Header( "Location: " . $_REQUEST["nextScript"] . "?" . preg_replace( "/__/", "&", $_REQUEST["query"] ));
    exit;
}


/****************************************************************************
 * File linkages               **********************************************
 ****************************************************************************/

// Link the files if requested by the user
$i = 1;
$filename = "URL$i";

while ($_FILES[$filename] != "")
{
    $destname = "destname$i";
    $copyright = "copyright$i";
    $deny = "deny$i";
    $limitdomain = "limitdomain$i";
    $fdescription = "fdescription$i";
    $newTypeSelect = "newTypeSelect$i";
    linkFile($_FILES[$filename],${$destname},${$copyright},${$deny},${$limitdomain},${$fdescription},${$newTypeSelect});
    modifyLastUpdate();
    $i++;
    $filename = "URL$i";
}


$eventID = "$ida$ids$idt";
$nbEntries = 0;
$files = array();
// Initialize the string for the template
$str_while = "";
$archive->synchronize("$eventID");
$categories = $archive->listFileCategories("$eventID");
while ($category = current($categories)) {
    $files = $archive->listFiles($eventID, $category);

    while ($file = current($files)) {
        $name = $file->getField("name");
        $path = $file->getField("path");
        $fileID = $file->getField("id");

        // Event Protection Level
        if (isFileProtected($fileID)) {
            $fileprotectiontext = "<img src=".$IMAGES_WWW."/protected.gif alt='protected' border=0>";
        }
        else {
            $fileprotectiontext = "<img src=".$IMAGES_WWW."/notprotected.gif alt='not protected' border=0>";
        }

        $additional_text = "";
        if ( preg_match("/(.*)\.ps/",$name,$found) && !is_file("$path/$found[1].pdf")) {
            if ( $createPDFActive && $PS2PDF != "")
                $additional_text = "[<A HREF=file.php?ida=$ida&ids=$ids&idt=$idt&createpdf=".urlencode("$name")."&filetype=".urlencode("$category")."&position=$position>create&nbsp;PDF</A>]";
            else
                $additional_text = "";
        }
        if ( preg_match("/(.*)\.pdf/",$name,$found )) {
            $additional_text = "";
            if (( $createPSActive && $ACROREAD != "") && ( !is_file("$path/$found[1].ps" )) && ( !is_file("$path/$found[1].ps.gz" )))
                $additional_text .= "[<A HREF=file.php?ida=$ida&ids=$ids&idt=$idt&createps=".urlencode("$name")."&filetype=".urlencode("$category")."&position=$position>create&nbsp;PS</A>]";
            if ( $createTXTActive  && $PS2ASCII && ( !is_file( "$path/$found[1].txt" )))
                $additional_text .= "[<A HREF=file.php?ida=$ida&ids=$ids&idt=$idt&createtxtfrompdf=".urlencode("$name")."&filetype=".urlencode("$category")."&position=$position>create&nbsp;TXT</A>]";
        }

        $str_while .= "<TR><TD align=right><a href=\"\" onClick=\"document.forms[0].action='fileprotection.php';document.forms[0].fileID.value='$fileID';document.forms[0].submit();return false;\">".$fileprotectiontext."</a></TD><TD align=right><small><B>$name</B></small></TD><TD align=center><small>[<A HREF=file.php?ida=$ida&ids=$ids&idt=$idt&position=$position&delete=".urlencode("$fileID")."&filetype=".urlencode("$category")." onClick=\"return confirm('Are you sure you want to delete this file?');\">delete</A>]";
        if ( $displayFileActive )
            $str_while .="[<A HREF='$AGE_WWW/askArchive.php?base=agenda&categ=$ida&id=".urlencode("$eventID/$category/$name")."'>display</A>]$additional_text</small>\n";

        $str_while .= "</TD><TD>\n";
        $nbEntries++;

        $str_while .= "<small><SELECT name=\"filetype$nbEntries\" onChange=\"document.forms[0].fileChange.value='$name';document.forms[0].filetype.value='$category';document.forms[0].newfiletype.value=this.options[this.selectedIndex].value;document.forms[0].submit();\">\n";

        // fill in array structure with list of categories;
        $sql = "SELECT categoryID,
                       shortName,
                       longName,
                       preselected
                FROM CATEGORY
                ORDER BY categoryID";
        $res = $db->query($sql);
        if (DB::isError($res)) {
            die ($res->getMessage());
        }
        while ($row = $res->fetchRow()) {
            $FILE_TYPES_ARRAY[$row['shortName']] = $row['longName'];
            if ($row['preselected'] == 1) {
                $FILE_TYPES_ARRAY_PRESELECTED = $row['categoryID'];
            }
        }

        $flag = false;
        foreach ($FILE_TYPES_ARRAY as $key => $val) {
            if ($key == $category) {  // No need to wrap $category in quotes, it's already a variable
                $str_while .= "    <OPTION value='$key' selected> $val\n";
                $flag = true;
            } else {
                $str_while .= "    <OPTION value='$key'> $val\n";
            }
        }
        if ( !$flag )
            $str_while .= "        <OPTION value='$category' selected> $category\n";

        $str_while .= "</SELECT></small>\n</TD>\n";
        next($files);
    }
    next ($categories);
}

$flagsStr = "
<TD bgcolor=#eeeeee align=\"center\"><font size=-1 color=#888888>CP</font></TD>
<TD bgcolor=#eeeeee align=\"center\"><font size=-1 color=#888888>DL</font></TD>
<TD bgcolor=#eeeeee align=\"center\"><font size=-1 color=#888888>OL</font></TD>
<TD bgcolor=#eeeeee align=\"center\"><font size=-1 color=#888888>Change File Permission</font></TD>";
$Template->set_var( "file_Message", $file_Message . "<BR>" );
$Template->set_var("file_runningAT", $runningAT );
$Template->set_var( "str_while", $str_while );

if ( $nbEntries == 0 ) {
    $str_help8="<TR><TD align=center><I>no file yet...</I></TD></TR>";
}
else {
    $str_help8="";
}

$Template->set_var( "str_help8", $str_help8 );
$Template->pparse( "final-page" , "mainpage" );

closedir($d);




function modifyLastUpdate()
{
    global $ida,$ids,$idt;

    $db = &AgeDB::getDB();

    $time=time();
    $today=strftime("%Y-%m-%d",$time);
    //     if ( !isset( $GLOBALS[ "table" ] ))
    //         $GLOBALS[ "table" ] = new table( "agenda" );

    if ($idt != "") {
        $sql = "update TALK set md='$today' where ida='$ida' and ids='$ids' and idt='$idt'";
    }
    elseif ($ids != "") {
        $sql = "update SESSION set md='$today' where ida='$ida' and ids='$ids'";
    }
    elseif ($ida != "") {
        $sql = "update AGENDA set md='$today' where id='$ida'";
    }
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
}

function formatFileName($text)
{
    $text = str_replace("\007","",$text);
    $text = str_replace("\010","",$text);
    $text = str_replace("\014","",$text);
    $text = str_replace("\016","",$text);
    $text = str_replace("\017","",$text);
    $text = str_replace("\020","",$text);
    $text = str_replace("\021","",$text);
    $text = str_replace("\022","",$text);
    $text = str_replace("\023","",$text);
    $text = str_replace("\024","",$text);
    $text = str_replace("\025","",$text);
    $text = str_replace("\026","",$text);
    $text = str_replace("\027","",$text);
    $text = str_replace("\030","",$text);
    $text = str_replace("\031","",$text);
    $text = str_replace("\032","",$text);
    $text = str_replace("\033","",$text);
    $text = str_replace("\034","",$text);
    $text = str_replace("\035","",$text);
    $text = str_replace("\036","",$text);
    $text = str_replace("\037","",$text);
    $text = str_replace("#","_",$text);
    return $text;
}


function linkFile($URL,$destname,$copyright,$deny,$limitdomain,$fdescription,$newTypeSelect)
{
    global $newTypeFreeText,$replaceBlankINFileNameChar,$agendadir,$ida,$ids,$idt,$Template,$idu,$dirSeparator,$file_Message,$LINK_MANAGMENT,$admin_email,$archive,$dirmode,$filmode;


    $db = &AgeDB::getDB();

    // define the type of material the user chose for this file
    if ($newTypeFreeText != "")
        $newType = $newTypeFreeText;
    else
        $newType = $newTypeSelect;
    $newType = ereg_replace(" ",$replaceBlankINFileNameChar,$newType);
    $newType = formatFileName($newType);

    // and the directory the file will be stored in
    $dirType = "$newType";

    // create the directory
    if (!@is_dir("$agendadir/$ida$ids$idt/$dirType"))
        @mkdir ("$agendadir/$ida$ids$idt/$dirType", $dirmode);

    // Create a redirection file to the given URL
    if ($URL != "") {
        if ($destname == "") {
            $baseName = basename($URL);
            $baseName = ereg_replace("\?.*","",$baseName);
            $baseName = ereg_replace("\.[^\.]+$","",$baseName);
            $destname = $baseName;
        }
        $destname = str_replace( '%20', '_', $destname );
        $destname = ereg_replace(" ",$replaceBlankINFileNameChar,$destname);
        $destname = formatFileName($destname);

	$i = 1;
	while (file_exists("$agendadir/$ida$ids$idt/$dirType/$destname.link")) {
		$i++;
		$destname = "$destname$i";
	}

        // Copy the file
        $output = fopen("$agendadir/$ida$ids$idt/$dirType/$destname.link","w" );
        fwrite($output,"<?php\n" );
        fwrite($output,"Header(\"location: $URL\");\n");
        fwrite($output,"?>");
        fclose($output);
        @chmod("$agendadir/$ida$ids$idt/$dirType/$destname.link", $filmode);

        if (file_exists("$agendadir/$ida$ids$idt/$dirType/$destname.link")) {
            // Start transaction
            $db->autocommit(false);
            // Insert also into the database
            $result = $archive->addFile("$ida$ids$idt",
                                        "$agendadir/$ida$ids$idt/$dirType",
                                        "$destname.link",
                                        "$dirType",
                                        filesize("$agendadir/$ida$ids$idt/$dirType/$destname.link"));
            if (!$result) {
                // Failed to change the database, rollback the transaction
                $db->rollback();
                unlink("$agendadir/$ida$ids$idt/$dirType/$destname.link");
                outError( " An error occured while archiving your file, try again later ", "02", $Template );
                exit;
            }
            elseif ($result == -1) {
                $file_Message = "Another file of the same type with the same name is present, the new one will replace it";
                // Another entry with the same fileName is present cannot insert it
                $db->rollback();
            }
            else {
                $db->commit();
            }
        }
    }
}



function uploadFile($MainFile,$MainFile_name,$destname,$copyright,$deny,$limitdomain,$fdescription,$newTypeSelect)
{
    global $newTypeFreeText,$replaceBlankINFileNameChar,$agendadir,$ida,$ids,$idt,$Template,$idu,$dirSeparator,$file_Message,$admin_email,$archive,$dirmode,$filmode;

    $db = &AgeDB::getDB();

    // define the type of material the user chose for this file
    if ($newTypeFreeText != "")
        $newType = $newTypeFreeText;
    else
        $newType = $newTypeSelect;
    $newType = preg_replace("/ /",$replaceBlankINFileNameChar,$newType);
    $newType = formatFileName($newType);

    // and the directory the file will be stored in
    $dirType = "$newType";
    // if for a reason the type is not correct, return
    if (chop($dirType) == "") {
	return;
    }

    // create the directory
    if (!@is_dir("$agendadir/$ida$ids$idt/$dirType"))
        @mkdir ("$agendadir/$ida$ids$idt/$dirType", $dirmode);
    if (@is_file("$MainFile")) {
        if ($destname == "") {
            // Remove %20 from MAC upload
            $destname = $MainFile_name;
        }
        else {
            $extension = preg_replace("/.*\./","",$MainFile_name);
            if ($extension != "" && !preg_match("/\.$extension/",$destname)) {
                $destname = $destname . "." . $extension;
            }
        }
        $destname = str_replace( '%20', '_', $destname );
        $destname = preg_replace("/ /",$replaceBlankINFileNameChar,$destname);
        $destname = formatFileName($destname);
        // Copy the file
        copy( "$MainFile","$agendadir/$ida$ids$idt/$dirType/$destname" );
        @chmod("$agendadir/$ida$ids$idt/$dirType/$destname", $filmode);

        if (file_exists("$agendadir/$ida$ids$idt/$dirType/$destname")) {
            // Start transaction
            $db->autocommit(false);
            // Insert also into the database
            $result = $archive->addFile("$ida$ids$idt",
                                        "$agendadir/$ida$ids$idt/$dirType",
                                        "$destname",
                                        "$dirType",
                                        filesize("$agendadir/$ida$ids$idt/$dirType/$destname"));
            if (!$result) {
                // Failed to change the database, rollback the transaction
                $db->rollback();
                outError( " An error occured while archiving your file, try again later ", "02", $Template );
                exit;
            }
            elseif ($result == -1) {
                $file_Message = "Another file of the same type with the same name is present, the new file will replace the old one";
                // Another entry with the same fileName is present cannot insert it
                $db->rollback();
            }
            else {
                $db->commit();
            }
        }
    }
}


function isEventProtected($ida,$ids,$idt)
{
    $db = &AgeDB::getDB();

    $sql = "SELECT confidentiality,apassword
            FROM AGENDA
            WHERE id='$ida'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    if ($row = $res->fetchRow()) {
        if ($row['confidentiality'] == "password" && $row['apassword'] != "") {
            return true;
        }
    }
    $sql = "SELECT *
            FROM EVENT_PASSWORD
            WHERE eventID='$ida$ids$idt'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    if ($row = $res->fetchRow()) {
        return true;
    }

    return false;
}

function isFileProtected($fileID)
{
    $db = &AgeDB::getDB();

    $sql = "SELECT *
            FROM FILE_PASSWORD
            WHERE fileID='$fileID'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    if ($row = $res->fetchRow()) {
        return true;
    }

    return false;
}
?>
