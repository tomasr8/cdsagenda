<?
// $Id: adminAdd.php,v 1.2.2.8 2003/03/28 10:27:13 tbaron Exp $

// adminAdd.php --- Displays an interface for adding a new category to a given 
//	hierarchy level. It also, adds a new category to the agenda DB
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminAdd.php is part of  (Manager module).
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
//      input params: 
//	        action (string) -> (optional flag) indicates whether to display the 
//		            interface or add the specified item to the DB (value='add')
//	        fid (string) ----> (required when adding, set to 0 if not specified 
//                  when displaying) parent category identifier the new item is 
//                  going to be added
//	        level (int) -----> (required when adding, set to 1 if not specified 
//                  when displaying) hierarchy level the new item is going to
//		            be added
//	        title (string) --> (required when adding) new category title
//	        description (string) -> (optional when adding, if not specified set 
//                  to "") description of the new category
//	        stitle (string) -> (optional when adding, if not specified set 
//                  to "") short title for the new category
//      output: HTML when displaying
//	            JS Browser Close  and Opener Refresh when adding 

include("adminLocalConf.inc");

//==================================================
// Main script
//==================================================
//check in the DB the parents exist
$db = & AgeDB::getDB();


//----------------------------------------------
// ADD to the DB
//----------------------------------------------
if(isset($_POST["action"]) && trim($_POST["action"])=="add") {
    $fid = $_POST["fid"];
    $level = $_POST["level"];
    $title = $_POST["title"];
    //check the fid & level are there
    if( (!isset($fid)) || (trim($fid)=="") ) {
        die("error!! fid not specified or incorrect");
    }
    //if level=1 add as base (fid=0)
    if( (!isset($level)) || (trim($level)=="") ) {
        die("error!! level not specified or incorrect");
    }
    //don't check if the parent exists when is a base
    if($level!=1) {
        $res=$db->query( "select title
                          from LEVEL
        				  where uid='$fid'
					      and level=".($level-1) );
        if($res->numRows()==0) {
            die("error!! parent category not found"); 
        }
    }
    // get new id
    // we don't use the autoincrement feature for backwards 
    // compatibility with old ims of the database
    $sql = "SELECT uid from LEVEL ORDER BY uid desc LIMIT 1";
    $res=$db->query($sql);
    if($res->numRows()==0) {
        $newid = 1;
    }
    else {
        $row = $res->fetchRow();
        $newid = $row['uid'] + 1;
    }
    
    //check fields are not empty
    if( (!isset($title)) || (trim($title)=="") ) {
        die("error!! can't create this item if it doesn't have title");
    }
    //format properly the fields
    $title=addslashes($title);
    $description=addslashes($description);
    $stitle=addslashes($stitle);
    //update DB
    $time=time();
    $today=strftime("%Y-%m-%d",$time);
    
    $db->autoCommit( false );
    
    $res = $db->query("insert into LEVEL (uid, fid, level, title, cd, md, 
                                          abstract, icon, stitle) 
                                  values ('$newid','$fid', $level, 
                                          '$title', '$today', '$today', 
                                          '$description', '', '$stitle' )" );

    if( DB::isError($res) ){
        die("error!! couldn't add item:".$res->getMessage());
    }

    // TODO FIX THIS
    // $counter = mysqli_insert_id();
    // //upload the icon file
    // if (is_uploaded_file($_FILES['icon']['tmp_name'])) {
    //     copy($_FILES['icon']['tmp_name'], "$AGE/$IMAGES/levelicons/".$counter.".gif");
    // }
    // //check if there were already some agendas in the parent category and, in 
    // //  this case, move them to the newly created one
    // $res = $db->query("select id
    //                    from AGENDA
	// 				   where fid='$fid'" );
    // if($res->numRows()!=0){
    //     while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ){
    //         $err = reallocat( $row->id, $counter );
    //         if($err!="") {
    //             $db->rollback();
    //             die("error!! couldn't move agenda '".$row->id."' to the newly created category: $err. The sub-category couldn't be create");
    //         }
    //     }
    // }
    $db->commit();
    //close this window and refresh the caller one
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

    
}
//----------------------------------------------
// DISPLAY the form
//----------------------------------------------
else {
    $Template = new Template( $PathTemplate );
    $Template->set_file(array( "mainpage" => "adminAdd.ihtml",
                               "error" => "error.ihtml"));

    if( (!isset($fid)) || (trim($fid)=="") ) {
        $fid="0";
    }
    if ($fid == "0") {
        $level = 1;
    }
    else {
        $sql = "SELECT level
                FROM LEVEL
                where uid='$fid'";
        $res = $db->query($sql);
        if ($row = $res->fetchRow()) {
            $level = $row['level']+1;
        }
        else {
            // fid does not exist
            outError("father category does not exist...","01",$Template);
            exit;
        }
    }
    $Template->set_var( "img_add", AGE_ADMIN_IMG_ADD );
    $Template->set_var( "postAddr", getBasicAddURL() );

    //check if the parent sub-category contains agendas and, in that case
    //  warn the user
    $res = $db->query("select id
                       from AGENDA
					   where fid='$fid'");
    if($res->numRows()==0) {
        $Template->set_var("warning", "");
    }
    else {
        $str='
        <br>
        <table align="center" width="70%" border="1">
	  <tr>
	    <td><font size="2" color="blue"><b>WARNING!!</b> This sub-category already contains some agendas; as it is not allowed to have sub-categories and agendas in the same level, if you decide to create a new category <b>all the existing agendas will be moved into the new category created</b></font></td>
	  </tr>
	</table>
	<br>';
        $Template->set_var("warning", $str);
    }

    //----------------------------------------------
    // base to be added
    //----------------------------------------------
    if( $level==1 ) {
        $Template->set_var( "category_name", "base");
    }
    //----------------------------------------------
    // subcategory to be added
    //----------------------------------------------
    else {
        $Template->set_var( "category_name", "sub-category to <font color=\"red\">".getHierarchy( $fid )."</font>" );
    }
    
    $Template->set_var("fid", $fid);
    $Template->set_var("level", $level);
    $Template->set_var("maxfilesize", $maxfileuploadsize);
    
    $Template->pparse( "final-page", "mainpage" );
}
?>
