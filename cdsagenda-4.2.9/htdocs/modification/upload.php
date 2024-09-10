<?php
// $Id: upload.php,v 1.1.1.1.4.9 2003/03/28 10:19:29 tbaron Exp $

// upload.php --- upload a file
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// upload.php is part of CDS Agenda.
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

require_once("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once '../AgeDB.php';
require_once('../platform/system/logManager.inc');

// Variable goBack==htable means this page was called with new
// documents table script htable.php
$htable = ( !strcmp( $_REQUEST["goBack"], "htable" ) ? 1 : 0 );
$ida = $_REQUEST[ "ida" ];
$ids = $_REQUEST[ "ids" ];
$idt = $_REQUEST[ "idt" ];

$AN = $_REQUEST[ "an" ];
$position = $_REQUEST[ "position" ];
$stylesheet = $_REQUEST[ "stylesheet" ];
$from = $_REQUEST[ "from" ];
$nbFiles = $_REQUEST[ "nbFiles" ];

// new template
$templFileAg = new Template( $PathTemplate );

// Template set-up
$templFileAg->set_file( array( 
			      "error"=>"error.ihtml",
			      "mainpage"=>"upload.ihtml" ));

// instantiate the db object if not initialized
$db  = &AgeDB::getDB();

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

// Set some parameters
if ( $htable )
{
  $templFileAg->set_var( "submitScript", "$from" );
  $templFileAg->set_var( "nextQuery", "" );
}
else
{
  $templFileAg->set_var( "submitScript", $_REQUEST["thisScript"] );
  $templFileAg->set_var( "nextQuery", "<INPUT type=hidden name=query value=\"".$_REQUEST["QUERY"]."\">" );
}

$templFileAg->set_var("images",$IMAGES_WWW);

$str_help="";
$str2 = "";

$templFileAg->set_var("ida",$ida);
$templFileAg->set_var("ids",$ids);
$templFileAg->set_var("idt",$idt);
$templFileAg->set_var("an",$AN);
$templFileAg->set_var("pos",$position);
$templFileAg->set_var("styl",$stylesheet);
$templFileAg->set_var("from",$from);
$templFileAg->set_var("action",$from);
$templFileAg->set_var("maxfilesize",$maxfileuploadsize);

if ( $allowNewDocumentsType )
{
  $str_help .= "<BR><LI>Please choose here the type of material, or enter a free text:<br>";
  // we now create the select box containing
  // all the predefined file types
  // if one of the types is the one used for the current file
  // we select it also in the select box
  $str_help .= "Select: <SELECT name=\"newTypeSelect\">\n";
  // $FILE_TYPES_ARRAY from config.php
  foreach ($FILE_TYPES_ARRAY as $key => $val) {
    if ($key == "moreinfo") {
        $str_help .= "<OPTION value='$key' selected> $val\n";
    } else {
        $str_help .= "<OPTION value='$key'> $val\n";
    }
  }
  // and we close the select box...
  $str_help .= "</SELECT>";
  $str_help .= " or enter a free text: <INPUT size=25 name=newTypeFreeText>\n";
}

$templFileAg->set_var("hlp",$str_help);

if ($nbFiles == "")
     $nbFiles = 1;

     $nbfilestext = "";
     for ($i=1;$i<21;$i++)
     if ($i == $nbFiles)
     $nbfilestext .= "<OPTION selected>$i\n";
     else
     $nbfilestext .= "<OPTION>$i\n";

$templFileAg->set_var("nbfiles",$nbfilestext);

$filelist = "";
// Now we display the form fields for each requested file upload
for ($i=1;$i<=$nbFiles;$i++)
{
  if (($i/2) == (int)($i/2))
    $bgcolor = "#cccccc";
  else
    $bgcolor = "#dddddd";
  $filelist .= "<tr bgcolor=$bgcolor><Td valign=top><small>Click on the \"Browse\" button and select file #$i:<br><INPUT TYPE=file NAME=Files$i VALUE=\"\" SIZE=30 MAXLENGTH=80></small></td><td><small>Destination name (if different from original):<br><input type=text name=destname$i size=20></small>";
  $filelist .= "<br><SMAll>Type of material:<br><SELECT name=\"newTypeSelect$i\">";
  foreach ($FILE_TYPES_ARRAY as $key => $val) {
    if ($key == "moreinfo") {
        $filelist .= "<OPTION value='$key' selected> $val\n";
    } else {
        $filelist .= "<OPTION value='$key'> $val\n";
    }
  }
  $filelist .= "</SELECT></td>";
  $filelist .= "</tr>";
}

$templFileAg->set_var("filelist",$filelist);

$templFileAg->set_var("hlp2","");

$templFileAg->set_var("uploadAgenda_title",$title);

$templFileAg->pparse( "finalpage","mainpage" );

?>
