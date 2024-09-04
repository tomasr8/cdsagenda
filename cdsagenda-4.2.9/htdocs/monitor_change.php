<?php
//$Id: monitor_change.php,v 1.1.2.1 2004/07/29 10:07:19 tbaron Exp $

// monitor_change.php --- monitor_change creation script
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch> - Mostly based on a
// perl script provided by Yves Perrin (EP-SFT)
//
// monitor_change.php is part of CDS Agenda.
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
require_once 'config/config.php';
require_once 'platform/template/template.inc';

$db = &AgeDB::getDB();

$self_url = "monitor_change.php?categories=".urlencode($categories);
if ($categories == "") {
    $categories = array( '3l13','3l160','2l64' );
}
elseif (!is_array($categories)) {
    $categories = split(" ",$categories);
}
if ($expanded == "" && sizeof($categories) > 0) {
    foreach($categories as $category) {
        $expanded .= GetExpandedTree($category);
    }
}

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => "monitor_change.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));	

$Template->set_var("list_supportEmail", $support_email);
$Template->set_var("list_runningAT", $runningAT);
$Template->set_var("images", $IMAGES_WWW );
$Template->parse( "list_jsmenutools", "JSMenuTools", true );

// Prepare menu creation
$topbarStr .= 
"<table border=0 cellspacing=1 cellpadding=0 width=\"100%\">
<tr>";
include 'menus/topbar.php';
$menuArray = array("MainMenu",
				   "ToolMenu",
				   "UserMenu",
				   "HelpMenu");
$topbarStr .= CreateMenuBar($menuArray);
$topbarStr .= " </tr> </table>\n\n";
$Template->set_var("list_topmenubar", $topbarStr);
$Template->set_var("nbofmonths", $nbofmonths);


$treeText = getTre('0',1,$expanded,$expand,$contract,$self_url);
$Template->set_var("main_list", $treeText);


// Create Menus
$menuText = CreateMenus($menuArray);
$Template->set_var("list_menus", $menuText);

// Create Footer
$Template->set_var("AGEfooter_shortURL", "");
$Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
$Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
$Template->parse("AGEfooter_template", "AGEfooter", true);

$Template->pparse("final-page", "mainpage");



function GetExpandedTree($category)
{
    global $db;
    $sql = "SELECT fid FROM LEVEL where uid='$category'";
    $res = $db->query($sql);
    $row = $res->fetchRow();
    
    if ($row['fid'] == "" || $row['fid'] == 0) {
        return "";
    }
    else {
        return $row['fid'] . " " . GetExpandedTree($row['fid']);
    }
}

function getTre( $fid, $level, $expanded, $expand, $contract, $self_url )
{
    global $IMAGES_WWW,$categories,$db;
    
    if(!isset($expanded)){
      $expanded="";
    }
    if(isset($expand) && (trim($expand)!="")){
      $expanded.=" $expand";
    }
    $a_expanded=explode(" ", $expanded);
    $a_expanded=array_unique($a_expanded);
    if(isset($contract) && (trim($contract)!=""))
    {
      reset($a_expanded);
      for($i=0; $i<count($a_expanded); $i++)
      {
        if($a_expanded[$i]==$contract)
          unset($a_expanded[$i]);
      }
    }
    $expanded=implode(" ", $a_expanded);
    $res = $db->query("select title, level, uid
                       from LEVEL
                       where fid='$fid' and level=$level
                       order by title");
    //there are some LEVEL sub-categories under this one
    $str="";
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) {
        $uid = $row->uid;
        $sons="";
        $args="expanded=".urlencode($expanded);
        if( in_array($uid, $a_expanded) ) {
            $img=$IMAGES_WWW."/downArrow.gif";
            $args.="&contract=".$uid;
            $sons.=getTre( $uid, $level+1, $expanded , "", "", $self_url);
        }
        else {
            $img=$IMAGES_WWW."/rightArrow.gif";
            $args.="&expand=".$uid;
        }
        $temp="";
        for($j=0;$j<($level-1);$j++) {
            $temp.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $res_sons=$db->query("select uid
                              from LEVEL
                              where fid='".$uid."'
						      and level=".($row->level+1) );
        if($res_sons->numRows()!=0) {
            $str.=$temp.'<a href="'.$self_url.'&'.$args.'"><img align="middle" border="0" src="'.$img.'"></a><input type=checkbox name="categories[]" value='.$uid.' '.(in_array($uid,$categories)?"checked":"").'>'.$row->title.'<br>'.$sons;
      }
      else {
          $str.=$temp.'<img align="middle" border="0" src="'.$img.'"><input type=checkbox name="categories[]" value='.$uid.' '.(in_array($uid,$categories)?"checked":"").'>'.$row->title.'</a><br>'.$sons;
      }
    }
    return $str;
}

?>

