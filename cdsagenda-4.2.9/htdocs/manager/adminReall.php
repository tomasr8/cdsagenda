<?
// $Id: adminReall.php,v 1.2.2.7 2004/07/29 10:06:06 tbaron Exp $

// adminReall.php --- Displays an interface that allows to re-allocate a 
//                  sub-category and commits the changes to the agenda DB
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminReall.php is part of  (Manager module).
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
//  function: reallocateLevelItem
//  purpose:
//  params: 
//  return value:
//============================================================================
function reallocateLevelItem( $uid_level, $new_fid )
{
    $db = & AgeDB::getDB();

    // fetch new level
    $res = $db->query( "SELECT level
                        FROM LEVEL
                        WHERE uid='$new_fid'" );
    $row = $res->fetchRow();
    $new_level = $row['level'] + 1;

    $err = $db->query( "UPDATE LEVEL
                        SET fid='$new_fid',level='$new_level'
                        WHERE uid='$uid_level'" );
    if( DB::isError( $err )) {
        return "error!! couldn't update level: ".$err->getMessage();
    }
    return "";
}





//============================================================================
// Main script
//============================================================================
if( (!isset($uid_level)) || (trim($uid_level)=="") || (count($uid_level)==0) ){
    die("error!! level item uid not specified or incorrect");
}
  
$db = & AgeDB::getDB();

$sql_str="";
foreach($uid_level as $element) {
    if($sql_str!="") {
        $sql_str.=", ";
    }
    $sql_str.="'$element'";
}
$sql_str="($sql_str)";

$res = $db->query("select uid, level, fid, title
                   from LEVEL
				   where uid in $sql_str");
if(DB::isError($res)) {
    die("error!! the specified uids don't correspond to any item: ".$res->getMessage());
}

//---------------------------------------------------------------
// MANAGE RE-ALLOCATION
//---------------------------------------------------------------
if( isset($action) and ($action=="reall") ) {
    if( (!isset($new_fid)) || (trim($new_fid)=="") ) {
        die("error!! the new fid hasn't been specified");
    }

    $db->autoCommit( false );

    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) {
        //ToDo: check if the parent exists (only if it is not a base)
        //ToDo: check the destination category doesn't have any agenda
        $err = reallocateLevelItem( $row->uid, $new_fid );
        if( $err!="" ) {
            $db->rollback();
            die("error reallocating ".$row->uid." - ".$err);
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
// INTERMEDIATE DATA SELECTION
//---------------------------------------------------------------
if( isset($action) ) {
    $new_fid="0";
    if( $action=="down") {
        $new_fid=$down_new_fid;
    }
    elseif( $action=="up" ) {
        $new_fid=$up_new_fid;
    }
    elseif( $action!="base" ) {
        die("error!! incorrect action");
    }

    $str="";
    foreach($uid_level as $element) {
        if($str!="") {
            $str.="&";
        }
        $str.="uid_level[]=$element";
    }
    header("Location: ".getReallURLLevel()."?$str&new_fid=$new_fid&action=reall");
    exit;
}

//---------------------------------------------------------------
// DISPLAY the FORM
//---------------------------------------------------------------
$Template = new Template( $PathTemplate );
$Template->set_file(array( "mainpage" => "adminReall.ihtml"));

$Template->set_var("img_reall", AGE_ADMIN_IMG_REALL);

//all the selected items must have the same level and same fid
$row = $res->fetchRow( DB_FETCHMODE_OBJECT );
$level=$row->level;
$fid=$row->fid;

if($level>1) {
    $name="sub-category";
    if(count($res)>1) {
        $name="sub-categories";
    }
    $str_hier=getHierarchy($fid);
}
else {
    $name="base";
    if(count($res)>1) {
        $name="bases";
    }
}
  
$str="";
$hidden="";
$tree_vars="";
$not_allowed=array();
do {
    if($level>1) {
        $str.="<br>$str_hier &gt; ".$row->title;
    }
    else {
        $str.="<br>".$row->title;
    }
    $hidden.='<input type="hidden" name="uid_level[]" value="'.$row->uid.'">';
    if($tree_vars!="") {
        $tree_vars.="&";
    }
    $tree_vars.="uid_level[]=".$row->uid;
    array_push( $not_allowed, $row->uid );
} while($row = $res->fetchRow( DB_FETCHMODE_OBJECT ));

$Template->set_var("category_name", "$name <font color=\"red\">$str</font>");
  
$common_actions='
      <form>
        '.$hidden.'
        <table width="100%">';

//----------------------------
// Level down action
//----------------------------
$res_sons = $db->query("select uid, title, level
                        from LEVEL
					    where fid='$fid'
					    and uid not in $sql_str
				        order by title" );
if( $res_sons->numRows()!=0 ) {
    $hierarchy=getHierarchy( $fid );
    $select_parent='';
    while( $row_sons = $res_sons->fetchRow(  DB_FETCHMODE_OBJECT ) ) {
        $res_agendas = $db->query("select id
                                   from AGENDA
						           where fid='".$row_sons->uid."'");
        if($res_agendas->numRows()==0) {
            $select_parent.='<option value="'.$row_sons->uid.'">'.$row_sons->title.'</option>';
        }
    }
    if($select_parent!='') {
        $common_actions.='
	  <tr>
	    <td align="right"><input type="radio" name="action" value="down" checked></td>
            <td align="left" width="100%"><b>one level down</b> <font size="2">(choose this option if you want to move this item one level down in the hierarchy)</font></td>
	  </tr>
	  <tr>
	    <td colspan="2">
	      <table width="90%" align="center">
	        <tr>
		  <td align="left"><font size="3">from<br>
		  <i>'.$hierarchy.'</i><br>
		  to<br><i>'.$hierarchy.' &gt;</i> <select name="down_new_fid">'.$select_parent.'</select></font>
		  </td>
		</tr>
	      </table>
	    </td>
	  </tr>';
    }
}

//----------------------------
// Level up action
//----------------------------
if($level>2) {
    $hierarchy=getHierarchy( $fid );
    $parent_hierarchy=explode("&nbsp;&gt;&nbsp;", $hierarchy);
    array_pop($parent_hierarchy);
    $parent_hierarchy=implode("&nbsp;&gt;&nbsp;", $parent_hierarchy);
    
    $result = $db->getRow("SELECT fid, level
                           FROM LEVEL
				           WHERE uid='$fid'");
    $son_fid = $result['fid'];
    
    if (!$authentication || canManageCategory($userid,$son_fid)) {
        $common_actions.='
          <tr>  
	    <td align="right"><input type="radio" name="action" value="up"></td>
	    <td align="left" width="100%"><b>one level up</b> <font size="2">(choose this option if you want to move this item one level up in the hierarchy)</font></td>
	  </tr>
	  <tr>
	    <td colspan="2">
	      <table width="90%" align="center">
	        <tr>
		  <td align="left"><font size="3">from<br>
		    <i>'.$hierarchy.'</i><br>
                    to<br><i>'.$parent_hierarchy.'</i>
                  </td>
		</tr>
	      </table>
	      <input type="hidden" name="up_new_fid" value="'.$son_fid.'">
	    </td>
	  </tr>';
    }
}

//----------------------------
// Base action
//----------------------------
if($level!=1 && (!$authentication || canManageCategory($userid,"0"))) {
    $common_actions.='
	  <tr>
	    <td align="right"><input type="radio" name="action" value="base"></td>
	    <td align="left"><b>base</b> <font size="2">(choose this option if you want to set directly this sub-category as a base)</font>
	    </td>
	  </tr>';
}
//----------------------------
  
$common_actions.='
          <tr>
	    <td align="center" colspan="2"><br><input type="submit" value="apply action"><input type="submit" onClick="window.close();" value="cancel"></td>
	  </tr>
        </table>
      </form>
      ';
$Template->set_var("common_actions", $common_actions);

$manual=getTree( '0', $expanded, $expand, $contract, getReallURLLevel()."?$tree_vars", getReallURLLevel()."?$tree_vars&action=reall&new_fid={fid}", $not_allowed  );

$manual="<br><table width=\"70%\" align=\"center\" border=\"1\"><tr><td><font size=\"2\" color=\"blue\">Browse the hierarchy using the little arrows on the left until you find the desired destination sub-category and then click on it</font></td></tr></table><br>".$manual;

$Template->set_var( "manual_selection", $manual );
$Template->pparse( "final-page", "mainpage" );
?>    
