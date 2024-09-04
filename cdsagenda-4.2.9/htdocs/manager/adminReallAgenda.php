<?
// $Id: adminReallAgenda.php,v 1.2.2.5 2004/07/29 10:06:06 tbaron Exp $

// adminReallAgenda.php --- Displays an interface that allows to re-allocate a
//                 set of agendas into another sub-category and commits the 
//                 changes to the agenda DB
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminReallAgenda.php is part of  (Manager module).
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

  include("adminLocalConf.inc");

//============================================================================
//  function: getTre
//  purpose:
//  params:
//  return value:
//============================================================================
function getTre( $fid, $level, $expanded, $expand, $contract, $self_url, $action_url, $not_allowed )
{
    global $userid;

    $db = & AgeDB::getDB();
    
    if(!isset($expanded))
    {
      $expanded="";
    }
    if(isset($expand) && (trim($expand)!=""))
    {
      $expanded.="#|#$expand";
    }
    $a_expanded=explode("#|#", $expanded);
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
    $expanded=implode("#|#", $a_expanded);
    $res = $db->query("select title, level, uid
                       from LEVEL
                       where fid='$fid'
                       order by title");
    //there are some LEVEL sub-categories under this one
    $str="";
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) {
      $sons="";
      $args="expanded=".urlencode($expanded);
      if( in_array($row->uid, $a_expanded) ) {
        $img=AGE_ADMIN_IMG_TREE_OPENED;
        $args.="&contract=".$row->uid;
        $sons.=getTre( $row->uid, $level+1, $expanded , "", "", $self_url, $action_url, $not_allowed );
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
        $res_sons=$db->query("select uid
                              from LEVEL
                              where fid='".$row->uid."'" );
        if($res_sons->numRows()!=0) {
          $str.=$temp.'<a href="'.$self_url.'&'.$args.'"><img align="middle" border="0" src="'.$img.'"></a>'.$row->title.'<br>'.$sons;
        }
        else {
            if (!$authentication || canManageCategory($userid,$row->uid)) {
                $action_url_p=str_replace("{fid}", $row->uid, $action_url);
                $str.=$temp.'<img align="middle" border="0" src="'.$img.'"><a href="'.$action_url_p.'" onClick="return confirm(\'Are you sure you want to move the current item into the selected sub-category ('.$row->title.')\');">'.$row->title.'</a><br>'.$sons;
            }
            else {
                $str.=$temp.'<img align="middle" border="0" src="'.$img.'">'.$row->title.'<br>'.$sons;
            }
        }
      }
    }
    return $str;
  }


//============================================================================
// Main script
//============================================================================
  if( (!isset($ida)) || (trim($ida)=="") || (count($ida)==0) )
  {
    die("error!! agenda's indentifier not specified or incorrect");
  }
  
  $db = & AgeDB::getDB();

  $sql_str="";
  foreach($ida as $element)
  {
    if($sql_str!="")
    {
      $sql_str.=", ";
    }
    $sql_str.="'$element'";
  }
  $sql_str="($sql_str)";

  $res = $db->query("select id, title, fid
                     from AGENDA
					 where id in $sql_str");
  if($res->numRows()==0)
  {
    die("error!! the specified agenda ids don't correspond to any item");
  }

//---------------------------------------------------------------
// MANAGE RE-ALLOCATION
//---------------------------------------------------------------
  if( isset($action) and ($action=="reall") )
  {
    if( (!isset($new_fid)) || (trim($new_fid)=="") )
    {
      die("error!! the new fid hasn't been specified");
    }
    $db->autoCommit(false);
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) {
      //ToDo: check if the parent exists (only if it is not a base)
      //ToDo: check the destination category doesn't have any agenda
      $err = reallocat( $row->id, $new_fid );

      if($err!="")
      {
        $db->rollback();
        die("$err");
      }
    }
    $db->commit();
?>
<html>
  <head>
  </head>
  <body>
  <script language="JavaScript">
    opener.location.reload(false);
    window.close();
  </script>
  </body>
</html>
<?
    exit;
  }
//---------------------------------------------------------------
// DISPLAY the FORM
//---------------------------------------------------------------
  $Template = new Template( $PathTemplate );
  $Template->set_file(array( "mainpage" => "adminReallAgenda.ihtml"));

  $Template->set_var( "img_reall", AGE_ADMIN_IMG_REALL );

  $name="agenda";
  if(count($ida)>1)
  {
    $name="agendas";
  }
  $str="";
  $tree_vars="";
  while($row = $res->fetchRow( DB_FETCHMODE_OBJECT ))
  {
    $str.="<br>".$row->title;
    //we suppose that all the selected agendas have the same parent
    if(count($not_allowed)==0) {
      $not_allowed=array( $row->fid );
    }
    if($tree_vars!="")
    {
      $tree_vars.="&";
    }
    $tree_vars.="ida[]=".$row->id;
  }
  $Template->set_var("category_name", "$name <font color=\"red\">$str</font>");
  
  $manual=getTre( '0', 1, $expanded, $expand, $contract, getReallURLAgenda()."?$tree_vars", getReallURLAgenda()."?$tree_vars&action=reall&new_fid={fid}", $not_allowed  );

  $manual="<br><table width=\"70%\" align=\"center\" border=\"1\"><tr><td><font size=\"2\" color=\"blue\">Browse the hierarchy using the little arrows on the left until you find the desired destination sub-category and then click on it</font></td></tr></table><br>".$manual;

  $Template->set_var( "manual_selection", $manual );

  $Template->pparse( "final-page", "mainpage" );
?>    
