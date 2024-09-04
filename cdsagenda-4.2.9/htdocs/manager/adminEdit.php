<?
// $Id: adminEdit.php,v 1.2.2.5 2003/03/28 10:27:15 tbaron Exp $

// adminEdit.php --- Displays an interface that allows to modify sub-categories //                  properties and commits the changes to the agenda DB. 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminEdit.php is part of  (Manager module).
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
// Main script
//============================================================================
  
if( (!isset($uid_level)) || (trim($uid_level)=="") ) 
{
    die("error!! level item uid not specified or incorrect");
}

$db = & AgeDB::getDB();
  
//----------------------------------------------
// UPDATE to the DB
//----------------------------------------------
if(isset($action) && trim($action)=="edit")
{
    if (is_uploaded_file($_FILES['icon']['tmp_name'])) {
        copy($_FILES['icon']['tmp_name'], "$AGE/$IMAGES/levelicons/".${uid_level}.".gif");
    }
    if ($deleteicon == "on" and file_exists("$AGE/$IMAGES/levelicons/".${uid_level}.".gif"))
        {
            unlink("$AGE/$IMAGES/levelicons/".${uid_level}.".gif");
        }
    if( (!isset($title)) || (trim($title)=="") )
        {
            die("error!! it doesn't make sense to set the item title to nothing");
        }
    if( !isset($description) )
        {
            $description="";
        }
    if( !isset($icon) )
        {
            $icon="";
        }
    if( !isset($stitle) )
        {
            $stitle="";
        }
    if ( !isset($visibility) )
        {
            $visibility = "999";
        }
    if ( !isset($accessPassword) )
        {
            $accessPassword = "";
        }
    if ( !isset($modifyPassword) )
        {
            $modifyPassword = "";
        }
    
    if(!get_magic_quotes_gpc())
        {
            $title=addslashes($title);
            $description=addslashes($description);
            $stitle=addslashes($stitle);
        }
    
    //upload the icon file or download it-->??
    //set the timming flag for the md
    $time=time();
    $today=strftime("%Y-%m-%d",$time);
    
    $err = $db->query("
    		update LEVEL set title='$title',
		                     abstract='$description',
		                     stitle='$stitle',
                             visibility='$visibility',
                             modifyPassword='$modifyPassword',
                             accessPassword='$accessPassword',
		                     md='$today'
            where uid='$uid_level'");

    if( DB::isError( $err ) )
        {
            die("error!! couldn't update item: ".$err->getMessage());
        }
    //close this window and refresh the caller one
?>
<html>
  <head>
  </head>
  <body>
  <script language="JavaScript">
    opener.location.reload(true);
    window.close();
  </script>
  </body>
</html>
<?
  }

//----------------------------------------------
// DISPLAY the form
//----------------------------------------------
else
{
    // Is this category a deadend?
    $sql = "SELECT uid
            FROM LEVEL
            WHERE fid='$uid_level'
            LIMIT 1";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) 
        {
            $deadend = 0;
        }
    else 
        {
            $deadend = 1;
        }

    $res = $db->query("select title, level, abstract, icon, stitle, visibility, modifyPassword, accessPassword, fid
                       from LEVEL
     				   where uid='$uid_level'");
    //no item found
    if($res->numRows()==0) 
        {
            die("error!! the uid specified doesn't correspond to any item");
        }
    
    $Template = new Template( $PathTemplate );
    $Template->set_file(array( "mainpage" => "adminEdit.ihtml"));
    
    $row = $res->fetchRow( DB_FETCHMODE_OBJECT );
    //----------------------------------------------
    // base to be edited
    //----------------------------------------------
    if($row->level==1)
        {
            $Template->set_var("category_name", "base <font color=\"red\">".$row->title."</font>");
        }   
    //----------------------------------------------
    // subcategory to be edited
    //----------------------------------------------
    else
        {
            $Template->set_var("category_name", "sub-category <font color=\"red\">".getHierarchy($uid_level)."</font>");
        }

    $Template->set_var("img_edit", AGE_ADMIN_IMG_EDIT);
    $Template->set_var("postAddr", getEditURL() );
    $Template->set_var("title", $row->title);
    $Template->set_var("description", $row->abstract);
    $Template->set_var("icon", $row->icon);
    $Template->set_var("stitle", $row->stitle);
    $Template->set_var("uid_level", $uid_level);
    
    // Deal with visibility level
    // Not dead-end category
    if (!$deadend) 
        {
            $visibilitytext = "Not Applicable";
        }
    else 
        {
            $visibility = $row->visibility;
            if ($visibility == '') 
                {
                    $visibility = 999;
                }
            $visibilitytext = "<SELECT name=visibility><OPTION value=0 ".
                ($visibility == 0 ? "selected" : "").
                ">nowhere";
            $currentcateg = $uid_level;
            $currentvisibility = 1;
            while ($currentcateg != "0") 
                {
                    $sql2 = "SELECT title,fid
                             FROM LEVEL
                             WHERE uid='$currentcateg'";
                    $res2 = $db->query($sql2);
                    if (DB::isError($res2)) 
                        {
                            die ($res2->getMessage());
                        }
                    if ($row2 = $res2->fetchRow()) 
                        {
                            $currentcateg = $row2['fid'];
                            $title = $row2['title'];
                            $visibilitytext .= "<OPTION value=$currentvisibility ".
                                ($visibility == $currentvisibility ? "selected" : "").
                                ">$title";
                            $currentvisibility++;
                        }
                    else 
                        {
                            $currentcateg = "0";
                        }
                }
            $visibilitytext .= "<OPTION value=999 ".
                ($visibility == 999 ? "selected" : "").
                ">everywhere</SELECT>";
        }
    $Template->set_var("visibility",$visibilitytext);

    // Deal with access password
    // Not dead-end category
    if (!$deadend) 
        {
            $accesspasswordtext = "Not Applicable";
        }
    else 
        {
            $accesspasswordtext = "<INPUT name=accessPassword size=15 value='".$row->accessPassword."'>";
        }
    $Template->set_var("accessPassword",$accesspasswordtext);

    // Deal with modify password
    // Not dead-end category
    if (!$deadend) 
        {
            $modifypasswordtext = "Not Applicable";
        }
    else 
        {
            $modifypasswordtext = "<INPUT name=modifyPassword size=15 value='".$row->modifyPassword."'>";
        }
    $Template->set_var("modifyPassword",$modifypasswordtext);
    $Template->set_var("maxfilesize", $maxfileuploadsize);

    
    $Template->pparse( "final-page", "mainpage" );
}
?>
