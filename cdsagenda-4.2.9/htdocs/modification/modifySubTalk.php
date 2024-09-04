<?php
// $Id: modifySubTalk.php,v 1.1.1.1.4.6 2002/12/20 15:28:17 tbaron Exp $

// modifySubTalk.php --- edit subtalk data
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// modifySubTalk.php is part of CDS Agenda.
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
require_once("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once('../platform/system/logManager.inc');

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "mainpage" => "modifySubTalk.ihtml", "error" => "error.ihtml" ));
	
$Template->set_var( "images", "$IMAGES_WWW" );

//FIXME: workaround for authentication
$topicFields = true;

// Set the page depending on permissions
if ( $topicFields )
{
    // Enable topic fields
    $Template->set_var( "modifySubTalk_topicFieldsOnEnd", "" );
    $Template->set_var( "modifySubTalk_topicFieldsOnStart", "" );
    // Disable fixed text for topic fields
    $Template->set_var( "modifySubTalk_topicFieldsOffStart", "<!--" );
    $Template->set_var( "modifySubTalk_topicFieldsOffEnd", "-->" );
}
else
{
    // Disable topic fields
    $Template->set_var( "modifySubTalk_topicFieldsOnEnd", "-->" );
    $Template->set_var( "modifySubTalk_topicFieldsOnStart", "<!--" );
    // Enable fixed text for topic fields
    $Template->set_var( "modifySubTalk_topicFieldsOffStart", "" ); 
    $Template->set_var( "modifySubTalk_topicFieldsOffEnd", "" );
}	    
if ( $otherFields )
{
}

$Template->set_var("modSub_IDS","$ids");
$Template->set_var("modSub_IDT","$idt");
$Template->set_var("modSub_IDA","$ida");
$Template->set_var("modSub_AN","$AN");
$Template->set_var("modSub_POSITION","$position");
$Template->set_var("modSub_STYLESHEET","$stylesheet");

$db = &AgeDB::getDB();
	
$sql = "SELECT ttitle,tspeaker,tcomment,repno,duration,tday,stime,type,affiliation,email,TIME_TO_SEC(duration) FROM SUBTALK where ida='$ida' and ids='$ids' and idt='$idt'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ( $numRows == 0 )
{
    if ( ERRORLOG )
        {
            $log = &AgeLog::getLog();
            $log->logError( __FILE__ . "." . __LINE__, 
                            "main", 
                            " Cannot find subtalk ida='$ida' and ids='$ids' and idt='$idt' " );
        }		
		
    $str_ifres="Cannot find this talk";
    $str_help2="";
    $str_help3="";
    $str_help4="";
    $str_help5="";
    $str_help6="";
    $str_help7="";
		
    exit();
}
else
{
    $row = $res->fetchRow();

    $str_ifres="<SCRIPT>";
		
    $row['ttitle']=stripslashes($row['ttitle']);
    $row['ttitle']=str_replace("'","\'",$row['ttitle']);
    $row['ttitle']=ereg_replace("\r","",$row['ttitle']);
    $row['ttitle']=ereg_replace("\n","",$row['ttitle']);

    $Template->set_var( "modifySubTalk_title", $row['ttitle'] );

    $row['tspeaker']=stripslashes($row['tspeaker']);
    $row['tspeaker']=str_replace("'","\'",$row['tspeaker']);
    $row['tspeaker']=ereg_replace("\r","",$row['tspeaker']);
    $row['tspeaker']=ereg_replace("\n","",$row['tspeaker']);

    $Template->set_var( "modifySubTalk_tspeaker", $row['tspeaker'] );

    $row['tcomment']=stripslashes($row['tcomment']);
    $row['tcomment']=str_replace("'","\'",$row['tcomment']);
    $row['tcomment']=ereg_replace("\r","",$row['tcomment']);
    $row['tcomment']=ereg_replace("\n","\\n",$row['tcomment']);
		
    $str_help3="document.forms[0].tcomment.value='" . $row['tcomment'] . "';\n";

    $row['repno']=stripslashes($row['repno']);
    $row['repno']=str_replace("'","\'",$row['repno']);
    $row['repno']=ereg_replace("\r","",$row['repno']);
    $row['repno']=ereg_replace("\n","",$row['repno']);
		
    $Template->set_var("modifySubTalk_repno", ( $row['repno'] == "" ? "" : $row['repno'] ));

    $array=split(":",$row['duration'],3);
    $durationh = "$array[0]";
    $durationm = "$array[1]";
    $Template->set_var( "modifySubTalk_durationh", $durationh );
    $Template->set_var( "modifySubTalk_durationm", $durationm );
    $durationmIndex = (int)($durationm/5);
		
    $str_help5="document.forms[0].durationh.selectedIndex=$durationh;\n
			    document.forms[0].durationm.selectedIndex=$durationmIndex;\n";

    $sinfo = split("-",$row['tday'],3);
		
    $str_help6="document.forms[0].stdd.value=$sinfo[2];\n
			    document.forms[0].stdy.value=$sinfo[0];\n
			    document.forms[0].stdm.value=$sinfo[1];\n";

    $stime = explode(":",$row['stime']);
		
    $str_help7="document.forms[0].thstart.value='$stime[0]';\n
		document.forms[0].tmstart.value='$stime[1]';\n
		document.forms[0].ttype.selectedIndex=" . $row['type'] . "-1;\n
		document.forms[0].affiliation.value='" . $row['affiliation'] . "';\n
		</SCRIPT>";
		
    $Template->set_var( "modifySubTalk_temail", $row['email'] );
    //		document.forms[0].temail.value='" . $row['email'] . "';\n
}
	
$Template->set_var("str_ifres","$str_ifres");
$Template->set_var("str_help3","$str_help3");
$Template->set_var("str_help5","$str_help5");
$Template->set_var("str_help6","$str_help6");
$Template->set_var("str_help7","$str_help7");
    
$Template->pparse( "final-page" , "mainpage" );
?>