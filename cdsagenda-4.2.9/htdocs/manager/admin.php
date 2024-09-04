<?
// $Id: admin.php,v 1.2.2.5 2002/10/23 08:56:26 tbaron Exp $

// admin.php --- Displays the list of bases, sub-categories or agendas for a 
//              given parent and level, allowing the user to perform management
//              actions over them.
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// admin.php is part of  (Manager module).
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
//      input params:   fid (optional) ---> parent identifier
//		                level (optional) -> hierarchy level to display
//      output: HTML
//

  include("adminLocalConf.inc");

//=============================================================================
// function: getNavigator
//
// purpose: builds the HTML navigator for quick access to any of the 
//	sub-categories of the hierarchy to the current item
// params: cur_fid (string) -> current parent uid 
//	   cur_level (int) --> current level 
// return value: (string) HTML code corresponding to the navigator for the 
//	current parent sub-category including links to each of the 
//	sub-categories from the base to the current item
//=============================================================================
function getNavigator($cur_fid)
{
    global $userid;

    if($cur_fid==0) {
        return "<font size=\"-1\">BASES</font>";
    }

    $db = & AgeDB::getDB();
    $first=1;
    while($cur_fid!=0) {
        $res = $db->getRow( "select fid,title
                             from LEVEL
					         where uid='$cur_fid'" );
        $cur_title=$res["title"]; 
        if($first == 1) {
            $str="<font size=\"-1\">".$cur_title."</font>";
            $first = 0;
        }
        elseif (!canManageCategory($userid,$cur_fid)) {
            $str="<font size=\"-1\">".$cur_title."</font>&gt;$str";
        }
        else {
            $str="<a href=\"".getMainURL($cur_fid)."\"><font size=\"-1\">$cur_title</font></a>&gt;$str";
        }
        $cur_fid=$res["fid"];
    }
    if (canManageCategory($userid,"0")) {
        $str="<a href=\"".getMainURL()."\"><font size=\"-1\">BASES</font></a>&gt;$str";
    }
    else {
        $str="<font size=\"-1\">BASES</font>&gt;$str";
    }
  
    return $str;
}
  
//=============================================================================
// Main script
//=============================================================================

$Template = new Template( $PathTemplate );
$Template->set_file(array( "mainpage"=> "adminMain.ihtml", 
                           "error"=>"error.ihtml" ));
$db = & AgeDB::getDB();

if ($fid == "") {
    $fid = "0";
}

if ($authentication && !canManageCategory($userid,$fid)) {
    outError("Sorry, you are not allowed to manage this category","10",&$Template);
    exit;
}

if ($request == "moveup" && $modid != "") {
    // retrieve all categs
    $categs = array();
    $sql = "select uid
            from LEVEL 
			where fid='$fid'
			order by categorder,title";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        array_push($categs,$row['uid']);
    }
    // get the position of the requested
    $myplace = array_search($modid,$categs);

    // if the category is not the first one
    if ($myplace != 0) {
        $categs[$myplace] = $categs[$myplace-1];
        $categs[$myplace-1] = "$modid";

        // then update the orders
        for ($i=0;$i<count($categs);$i++) {
            $sql = "UPDATE LEVEL
                    SET categorder=$i
                    WHERE uid='".$categs[$i]."'";
            $res = $db->query($sql);
        }
    }
}

if ($request == "movedown" && $modid != "") {
    // retrieve all categs
    $categs = array();
    $sql = "select uid
            from LEVEL 
			where fid='$fid'
			order by categorder,title";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        array_push($categs,$row['uid']);
    }
    // get the position of the requested
    $myplace = array_search($modid,$categs);
    // if the category is not the last one
    if ($myplace != count($categs)-1) {
        $categs[$myplace] = $categs[$myplace+1];
        $categs[$myplace+1] = "$modid";
        
        // then update the orders
        for ($i=0;$i<count($categs);$i++) {
            $sql = "UPDATE LEVEL
                    SET categorder=$i
                    WHERE uid='".$categs[$i]."'";
            $res = $db->query($sql);
        }
    }
}


//-----------------------------------------------------------------------
//subcategories or agendas to be shown
//-----------------------------------------------------------------------
  if( (isset($fid) || (trim($fid)!="")) && $fid!="0" )
  {
    
    $parent_fid = $db->getOne("select fid 
                               from LEVEL 
                               where uid='$fid'");
    
    if (canManageCategory($userid,$parent_fid)) {
        $Template->set_var( "up_button", "<a href=\"".getMainURL($parent_fid)."\"><img src=\"".AGE_ADMIN_IMG_UP."\" align=\"middle\" alt=\"Go to upper category\" border=\"0\"></a>&nbsp;");
    }
    else {
        $Template->set_var( "up_button", "" );
    }


    //display subcategories or agendas for the given parameters
    $res = $db->query( "select uid,title 
                        from LEVEL 
					    where fid='$fid'
					    order by categorder,title" );
//-----------------------------------------------------------------------
// subcategories to be shown
//-----------------------------------------------------------------------
    if( $res->numRows()!=0 )
    {
        $first = true;
        while( $row = $res->fetchRow(DB_FETCHMODE_OBJECT) )
            {
                if (canManageCategory($userid,$row->uid)) {
                    $str_help.='
	  <tr>
	     <td>
	       <a href="'.getDelURLLevel( $row->uid ).'" onClick="return confirm(\'Are you sure you want to delete this sub-category and all sub-categories and agendas it contains?\');"><img border="0" src="'.AGE_ADMIN_IMG_DEL.'" align="middle" alt="Delete this sub-category"></a>
           <a href="JavaScript:openwindowlink(\''.getReallURLLevel( $row->uid ).'\', \'reall\')"><img border="0" src="'.AGE_ADMIN_IMG_REALL.'" align="middle" alt="Reallocate this sub-category"></a>
           <a href="" onClick="document.forms[0].request.value=\'moveup\';document.forms[0].modid.value=\''.$row->uid.'\';document.forms[0].submit();return false;"><img border="0" src="'.AGE_ADMIN_IMG_MOVEUP.'" align="middle" alt="Move sub-category up"></a>
           <a href="" onClick="document.forms[0].request.value=\'movedown\';document.forms[0].modid.value=\''.$row->uid.'\';document.forms[0].submit();return false;"><img border="0" src="'.AGE_ADMIN_IMG_MOVEDOWN.'" align="middle" alt="Move sub-category down"></a>
	     </td>
	     <td>
	       <input type="checkbox" name="uid_level[]" value="'.$row->uid.'"><a href="'.getMainURL($row->uid).'">'.$row->title.'</a>&nbsp;<small>
         </td>
         <td>'.getIconText($row->uid).'</td>
	  </tr>';
                }
            }
        $cat_name="subcategory";
        $cat_name_plural="subcategories";
        $Template->set_var( "recover_button", "" );
        $Template->set_var( "reall_url", getReallURLLevel() );
    }
//-----------------------------------------------------------------------
// agendas to be shown
//-----------------------------------------------------------------------
    else
    {
      $Template->set_var( "recover_button", '<a href="JavaScript:openwindowlink(\''.getRecoverURL( $fid ).'\', \'recover\')"><img border="0" align="middle" src="'.AGE_ADMIN_IMG_TRASH.'" height="20" width="20" alt="Restore deleted agendas to this sub-category"></a>' );
      
      $res = $db->query( "select id, title, stdate
      					  from AGENDA
					      where fid='$fid'
					      order by stdate desc" );
      if( $res->numRows()==0 )
      {
        $str_help="no agendas or subcategories to be shown"; 
        $cat_name="subcategory";
        $cat_name_plural="subcategories";
      }
      else
      {
        while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ){
          $str_help.='
	     <tr>
	       <td>
	         <a href="'.getDelURLAgenda( $row->id ).'" onClick="return confirm(\'Are you sure you want to delete this agenda?\');"><img border="0" src="'.AGE_ADMIN_IMG_DEL.'" align="middle" alt="Delete this agenda"></a>
	      <a href="JavaScript:openwindowlink(\''.getEditURLAgenda( $row->id ).'\', \'editagenda\')"><img border="0" src="'.AGE_ADMIN_IMG_EDIT.'" align="middle" alt="Modify this agenda"></a>
	      <a href="JavaScript:openwindowlink(\''.getReallURLAgenda( $row->id ).'\', \'reall_agenda\')"><img border="0" src="'.AGE_ADMIN_IMG_REALL.'" align="middle" alt="Reallocate this agenda"></a>
	       </td>
	       <td><input type="checkbox" name="ida[]" value="'.$row->id.'"> <font size="2">['.$row->stdate.']</font> <b><a href="'.getAgendaURL( $row->id ).'">'.$row->title.'</a></b></td>
	     </tr>';
        }
        $cat_name="agenda";
        $cat_name_plural="agendas";
        $Template->set_var( "reall_url", getReallURLAgenda() );
      }
    }
    $Template->set_var( "bases", $str_help );
  }
//-----------------------------------------------------------------------
// bases to be shown
//-----------------------------------------------------------------------
  else
  {
    $title="BASES";
    $fid=0;

    //display bases 
    $res = $db->query( "select uid,title 
                        from LEVEL 
                        where fid='0' 
			order by categorder,title" );
    $str_help = "";
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) )
    {
      $str_help.="
        <tr>
          <td>
	    <a href=\"".getDelURLLevel($row->uid)."\" onClick=\"return confirm('Are you sure you want to delete this base and all sub-categories and agendas it contains?');\"><img border=\"0\" src=\"".AGE_ADMIN_IMG_DEL."\" align=\"middle\" alt=\"Delete this base\"></a>
	    <a href=\"JavaScript:openwindowlink('".getReallURLLevel( $row->uid )."', 'reall')\"><img border=\"0\" src=\"".AGE_ADMIN_IMG_REALL."\" align=\"middle\" alt=\"Reallocate this base\"></a>
            <a href=\"\" onClick=\"document.forms[0].request.value='moveup';document.forms[0].modid.value='".$row->uid."';document.forms[0].submit();return false;\"><img border=\"0\" src=\"".AGE_ADMIN_IMG_MOVEUP."\" align=\"middle\" alt=\"Move sub-category up\"></a>
            <a href=\"\" onClick=\"document.forms[0].request.value='movedown';document.forms[0].modid.value='".$row->uid."';document.forms[0].submit();return false;\"><img border=\"0\" src=\"".AGE_ADMIN_IMG_MOVEDOWN."\" align=\"middle\" alt=\"Move sub-category down\"></a>
           </td>
	   <td>
	      <input type=\"checkbox\" name=\"uid_level[]\" value=\"".$row->uid."\"><a href=\"".getMainURL($row->uid, 2)."\">".$row->title."</a>
       </td>
       <td>".getIconText($row->uid)."</td>";
    }
    $cat_name="base";
    $cat_name_plural="bases";
    $Template->set_var( "bases", $str_help );
    $Template->set_var( "up_button", "");  
    $Template->set_var( "recover_button", "" );
    $Template->set_var( "reall_url", getReallURLLevel() );
    $fid="0";
  }

//-----------------------------------------------
$Template->set_var( "category_name", $cat_name );
$Template->set_var( "fid", $fid );
$Template->set_var( "category_name_plural", $cat_name_plural );
$Template->set_var( "delete_url", getDelURL() );
$Template->set_var( "refreshURL", "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
$Template->set_var( "img_add", AGE_ADMIN_IMG_ADD);
$Template->set_var( "img_del", AGE_ADMIN_IMG_DEL);
$Template->set_var( "img_edit", AGE_ADMIN_IMG_EDIT );
$Template->set_var( "img_reall", AGE_ADMIN_IMG_REALL );
$Template->set_var( "img_up", AGE_ADMIN_IMG_UP);
$Template->set_var( "img_trash", AGE_ADMIN_IMG_TRASH);
$Template->set_var( "navigator", getNavigator($fid)."&nbsp;<small>[<a href=\"".getGoToURL($fid, 2)."\">go&nbsp;to</a>]</small>" );
if($cat_name=="agenda"){
    $cat_name="sub-category";
}

$addbuttontext = "<a href=\"JavaScript:openwindowlink('".getAuthURL($fid)."', 'authorize')\"><img border=\"0\" src='".AGE_ADMIN_IMG_AUTH."' align=\"middle\" alt=\"Authorizations for this category\"></a>";
$addbuttontext .= ($fid!="0"?"<a href=\"JavaScript:openwindowlink('".getEditURL($fid)."', 'edit')\"><img border=\"0\" src='".AGE_ADMIN_IMG_EDIT."' align=\"middle\" alt=\"Modify this category\"></a>":"")."<a href=\"JavaScript:openwindowlink('".getAddURL( $fid )."', 'add')\"><img src=\"".AGE_ADMIN_IMG_ADD."\" align=\"middle\" border=\"0\" alt=\"Add a new $cat_name\"></a>";
$Template->set_var( "add_button", $addbuttontext);

$Template->pparse( "final-page" , "mainpage" );
?>
