<?
// $Id: adminCommon.inc,v 1.2.2.10 2004/10/06 13:53:56 tbaron Exp $

// adminCommon.php --- Shared file including general-purpose functions
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminCommon.php is part of  (Manager module).
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

//===========================
// check management rights
//===========================
if ($fid != "") {
  if (!canManageCategory($userid,$fid)) {
        print "You are not allowed to access the administrator tool for this category";
        exit(0);
  }
}
if ($uid_level != "" and !is_array($uid_level)){
  $fid = $uid_level;
  if (!canManageCategory($userid,$fid)) {
        print "You are not allowed to access the administrator tool for this category";
        exit(0);
  }
}
if ($ida != "" and !is_array($ida)) {
  if (!canModifyAgenda($userid,$ida)) {
        print "You are not allowed to access the administrator tool for this agenda";
        exit(0);
  }
}

//===========================
// paths to images
//===========================

  define("AGE_ADMIN_IMG_ADD", "images/add.gif");
  define("AGE_ADMIN_IMG_DEL", "images/delete.gif");
  define("AGE_ADMIN_IMG_EDIT", "images/edit.gif");
  define("AGE_ADMIN_IMG_AUTH", "$IMAGES_WWW/authorize.gif");
  define("AGE_ADMIN_IMG_REALL", "images/movetalk.gif");
  define("AGE_ADMIN_IMG_UP", "$IMAGES_WWW/upArrow.gif");
  define("AGE_ADMIN_IMG_TREE_OPENED", "images/downArrow.gif");
  define("AGE_ADMIN_IMG_TREE_CLOSED", "images/rightArrow.gif");
  define("AGE_ADMIN_IMG_TRASH", "images/trash.gif");
  define("AGE_ADMIN_IMG_MOVEUP", "$IMAGES_WWW/upicon.gif");
  define("AGE_ADMIN_IMG_MOVEDOWN", "$IMAGES_WWW/downicon.gif");

//============================================================================
//  function: getHierarchy
//  purpose: Gives a text containing the list of categories in the hierarchy
//	to the current level.
//  params:  fid (string) -> parent category identifier
//  return value: (string, HTML) list of categories from the base level to the 
//	specified one, separated by ">"
//============================================================================
  function getHierarchy( $fid )
  {

    $str="";
    if($fid=='0') return "bases";

    $db = & AgeDB::getDB();
    
    while($fid != '0')
    {
      $res = $db->getRow( "select fid,title
                           from LEVEL
                           where uid='$fid'" );
      if($str!="") 
      {
        $str="<b>&nbsp;&gt;&nbsp;</b>".$str;
      }
      $str=$res["title"].$str;
      $fid=$res["fid"];
    }

    return $str;
  }

//============================================================================
//  function: getMainURL
//  purpose: returns the URL of the administrator for a given hierarchy
//	level
//  params: fid (string)
//  return value:
//============================================================================
function getMainURL ( $fid="0" )
{
    if($fid == "0") {
        return "admin.php";
    }
    return "admin.php?fid=$fid";
  }

//============================================================================
//  function: getGoToURL
//  purpose: returns the URL of the given category
//	level
//  params: fid (string)
//  return value:
//============================================================================
function getGoToURL ( $fid="0" )
{
    global $AGE_WWW;

    if($fid == "0") {
        return "$AGE_WWW/List.php";
    }
    return "$AGE_WWW/displayLevel.php?fid=$fid";
}

//============================================================================
//  function: getBasicAddURL
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getBasicAddURL()
  {
    return "adminAdd.php";
  }
//============================================================================
//  function: getAddURL
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getAddURL ( $fid="0" )
  {
    return "adminAdd.php?fid=$fid";
  }

//============================================================================
//  function: getAuthURL
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getAuthURL ( $fid="0" )
  {
    return "adminAuth.php?fid=$fid";
  }

//============================================================================
//  function: getDelURL
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getDelURL()
  {
    return "adminDel.php";
  }

//============================================================================
//  function: getDelURLLevel
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getDelURLLevel ( $uid_level )
  {
    return getDelURL()."?uid_level".urlencode("[]")."=$uid_level&refreshURL=".urlencode("http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  }
  
//============================================================================
//  function: getDelURLAgenda
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getDelURLAgenda ( $ida )
  {
    return getDelURL()."?ida".urlencode("[]")."=$ida&refreshURL=".urlencode("http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
  }

//============================================================================
//  function: getEditURL
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getEditURL ( $uid_level="" )
  {
    if($uid_level=="")
    {
      return "adminEdit.php";
    }
    else
    {
      return "adminEdit.php?uid_level=$uid_level";
    }
  }

//============================================================================
//  function: getEditURLAgenda
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getEditURLAgenda( $ida="" )
  {
    if($ida!="")
    {
      return "adminEditAgenda.php?ida=$ida";
    }
    else
    {
      return "adminEditAgenda.php";
    }
  }

//============================================================================
//  function: getReallURLAgenda
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getReallURLAgenda ( $ida="" )
  {
      if($ida!="")
      {
        return "adminReallAgenda.php?ida[]=$ida";
      }
      else
      {
        return "adminReallAgenda.php";
      }
  }
//============================================================================
//  function: getReallURLLevel
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getReallURLLevel ( $uid_level="" )
  {
    if($uid_level!="")
    {
      return "adminReall.php?uid_level[]=$uid_level";
    }
    else
    {
      return "adminReall.php";
    }
  }

//============================================================================
//  function: getRecoverURL
//  purpose:
//  params: 
//  return value:
//============================================================================
  function getRecoverURL ( $new_fid="" )
  {
    if($new_fid=="")
    {
      return "adminRestore.php";
    }
    else
    {
      return "adminRestore.php?new_fid=$new_fid";
    }
  }

//============================================================================
//  function: getIconText
//  purpose:
//  params: 
//  return value:
//============================================================================
function getIconText ( $uid="" )
{
    global $AGE_WWW,$IMAGES,$AGE;
    
    if( $uid=="" ||
        !is_file("$AGE/$IMAGES/levelicons/$uid.gif")) {
        return "";
    }
    else {
        return "&nbsp;<img src=".${AGE_WWW}."/$IMAGES/levelicons/$uid.gif height=16 width=16 alt='icon' border=1>";
    }
  }

//============================================================================
//  function: getAgendaURL
//  purpose:
//  params: 
//  return value:
//============================================================================

  function getAgendaURL ( $ida )
  {
    global $AGE_WWW;

    return "$AGE_WWW/fullAgenda.php?ida=$ida";
  }


//============================================================================
//  function: getTree
//  purpose:
//  params: 
//  return value:
//============================================================================
function getTree( $fid, $expanded, $expand, $contract, $self_url, $action_url, $not_allowed )
{
    global $userid;
    $db = & AgeDB::getDB();
    
    if(!isset($expanded)) {
      $expanded="";
    }

    if(isset($expand) && (trim($expand)!="")) {
      $expanded.="#|#$expand";
    }
   
    $a_expanded=explode("#|#", $expanded);
    $a_expanded=array_unique($a_expanded);

    if(isset($contract) && (trim($contract)!="")) {
      reset($a_expanded);
      for($i=0; $i<count($a_expanded); $i++)
      {
        if($a_expanded[$i]==$contract){
          unset($a_expanded[$i]);
        }
      }
    }

    $expanded=implode("#|#", $a_expanded);

    $res = $db->query("select title, uid, level
                       from LEVEL
  				       where fid='$fid'
				       order by title");
    $str="";
    for($i=0; $i<count($res); $i++)
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) {
        $level = $row->level + 1;
        $uid = $row->uid;
            $sons="";
        $args="expanded=".urlencode($expanded);
        if( in_array($row->uid, $a_expanded) ) {
            $img=AGE_ADMIN_IMG_TREE_OPENED;
            $args.="&contract=".$row->uid;
            $sons.=getTree( $row->uid, $expanded , "", "", $self_url, $action_url, $not_allowed );
        }
        else {
            $img=AGE_ADMIN_IMG_TREE_CLOSED;
            $args.="&expand=".$row->uid;
        }
        $temp="";
        for($j=0;$j<($level-1);$j++) {
            $temp.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }
	
        if(in_array($row->uid, $not_allowed)) {
            $str.=$temp.'<img align="middle" border="0" src="'.$img.'">'.$row->title.'<br>';
        }
        else {
            $res_sons=$db->query("select id from AGENDA where fid='".$row->uid."'");
            if($res_sons->numRows()==0) {
                if (!$authentication || canManageCategory($userid,$row->uid)) {
                    $action_url_p=str_replace("{fid}", $row->uid, $action_url);
                    $str.=$temp.'<a href="'.$self_url.'&'.$args.'"><img align="middle" border="0" src="'.$img.'"></a><a href="'.$action_url_p.'" onClick="return confirm(\'Are you sure you want to move the current item into the selected sub-category ('.$row->title.')\');">'.$row->title.'</a><br>'.$sons;
                }
                else {
                    $str.=$temp.'<a href="'.$self_url.'&'.$args.'"><img align="middle" border="0" src="'.$img.'"></a>'.$row->title.'<br>'.$sons;
                }
            }
            else {
                $str.=$temp.'<img align="middle" border="0" src="'.$img.'">'.$row->title.'<br>';
            }
        }
    }
    return $str;
  }

//============================================================================
//  function: reallocat
//  purpose:
//  params:
//  return value:
//============================================================================

  function reallocat( $ida, $new_fid )
  {
    $db=AgeDB::getDB();
    //it is supposed that the destination sub-category is a 'low level' one
    //  (doesn't contain any other sub-category)
    $res = $db->query("update AGENDA
                       set fid='$new_fid'
                       where id='$ida'");
    if(DB::isError( $res ))
    {
      return "error!! couldn't move agenda '$ida' to the new subcategory ($ida):".$res->getMessage();
    }
    return "";
  }

?>
