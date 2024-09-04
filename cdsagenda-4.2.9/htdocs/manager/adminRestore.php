<?
// $Id: adminRestore.php,v 1.2.2.4 2003/03/28 10:27:16 tbaron Exp $

// adminRestore.php --- Displays an interface that allows to recover deleted
//                  agendas into a certain sub-category and commits the changes //                  to the agenda DB. 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminRestore.php is part of  (Manager module).
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
//  function: restor
//  purpose:
//  params: 
//  return value:
//============================================================================
  function restor( $ida, $new_fid )
  {
    //first, we need to copy the agenda back to the main tables according to
    // the new subcategory.
    $db = AgeDB::getDB();
    //check the parent sub-category exists and that is a "low level" one (no
    //  sub-categories below it)
    $res = $db->query("select level
                       from LEVEL
                       where uid='$new_fid'");
    if($res->numRows()==0) {
      return "error!! the destination sub-category the agenda '$ida' has to be moved ('$new_fid') doesn't exist";
    }
    $row=$res->fetchRow( DB_FETCHMODE_OBJECT );
    $level=$row->level;
    //check the destination sub-category is a "low level" one
    $res = $db->query("select title
                       from LEVEL
                       where fid='$new_fid'
                       and level=".($level+1));
    if($res->numRows()!=0) {
      return "error!! cannot restore agenda '$ida' to the destination sub-category ('$new_fid') because this subcategory contains other sub-categories";
    }
    //copy BK records to main tables
    $res = $db->query( "select title, id, stdate, endate, location, nbsession, 
                               chairman, cem, status, an, cd, md, stylesheet, 
                               format, confidentiality, apassword, repno, fid, 
                               acomments, keywords, visibility, bld, floor, room
                        from BK_AGENDA
                        where id='$ida'");
    //if the agenda doesn't exist, we just return
    if($res->numRows()==0) {
      return "error!! agenda to be restored doesn't exists";
    }
    $row=$res->fetchRow( DB_FETCHMODE_OBJECT );
    //agenda exists, so move it to the main table with the new fid
    $nbsession=$row->nbsession;
    if( (trim($nbsession)=="")||(!isset($row->nbsession)) ) {
      $nbsession="null";
    }
    $err = $db->query("
                     insert into AGENDA( title, id, stdate, endate, location, 
                                         nbsession, chairman, cem, status, an, 
                                         cd, md, stylesheet, format, 
                                         confidentiality, apassword, repno, 
                                         fid, acomments, keywords, visibility,
                                         bld, floor, room )
                     values ( '".addslashes($row->title)."', '".
                                 $row->id."', '".
                                 $row->stdate."', '".$row->endate."', '".
                                 addslashes($row->location)."', ".
                                 $nbsession.", '".
                                 addslashes($row->chairman)."', '".
                                 addslashes($row->cem)."', '".
                                 addslashes($row->status)."', '".
                                 addslashes($row->an)."', '".
                                 $row->cd."', '".$row->md."', '".
                                 addslashes($row->stylesheet)."', '".
                                 addslashes($row->format)."', '".
                                 addslashes($row->confidentiality)."', '".
                                 addslashes($row->apassword)."', '".
                                 addslashes($row->repno)."', '".
                                 $new_fid."', '".
                                 addslashes($row->acomments)."', '".
                                 addslashes($row->keywords)."', '".
                                 addslashes($row->visibility)."', ".
                                 addslashes($row->bld).", '".
                                 addslashes($row->floor)."', '".
                                 addslashes($row->room)."')");
    if(DB::isError($err)) {
      return "error!! couldn't restore the agenda to main table: ".$err->getMessage();
    }
    //then, move the agenda's sessions to the corresponding main table
    $res = $db->query( "select ida, ids, schairman, speriod1, stime, eperiod1, 
                               etime, stitle, snbtalks, slocation, scem, 
                               sstatus, bld, floor, room, broadcasturl, cd, md,
                               scomment
                        from BK_SESSION
                        where ida='$ida'" );
    for( $i=0; $i<count($res); $i++ )
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) { 
      $snbtalks=$row->snbtalks;
      if( (trim($snbtalks)=="")||(!isset($row->snbtalks)) ) {
        $snbtalks="null";
      }
      $err = $db->query( "
                    insert into SESSION( ida, ids, schairman, speriod1, stime, 
                                         eperiod1, etime, stitle, snbtalks, 
                                         slocation, scem, sstatus, bld, floor,
                                         room, broadcasturl, cd, md, scomment )
                    values( '".$row->ida."', '".$row->ids."', '".
                               addslashes($row->schairman)."', '".
                               $row->speriod1."', '".
                               $row->stime."', '".$row->eperiod1."', '".
                               $row->etime."', '".
                               addslashes($row->stitle)."', ".
                               $snbtalks.", '".
                               addslashes($row->slocation)."', '".
                               addslashes($row->scem)."', '".
                               addslashes($row->sstatus)."', ".
                               $row->bld.", '".
                               addslashes($row->floor)."', '".
                               addslashes($row->room)."', '".
                               addslashes($row->broadcasturl)."', '".
                               $row->cd."', '".$row->md."', '".
                               addslashes($row->scomment)."')" );
      //couldn't copy the session: already copied agenda and sessions must be
      //  deleted from tables
      if( DB::isError( $err ) ) {
          return "error!! couldn't restore the session '".$row->ids."': ".$err->getMessage();
      }
    }
    //then move the agenda's talks to the corresponding main table
    $res = $db->query( "select ida, ids, idt, ttitle, tspeaker, tday, tcomment,
                               location, bld, floor, room, broadcasturl, type, 
                               cd, md, category, stime, repno, affiliation, 
                               duration, email
                        from BK_TALK
                        where ida='$ida'" );
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) {
      $err = $db->query( "
                   insert into TALK( ida, ids, idt, ttitle, tspeaker, tday, 
                                     tcomment, location, bld, floor, room,
                                     broadcasturl, type, cd, md, category, 
                                     stime, repno, affiliation, duration, email)
                   values( '".$row->ida."', '".$row->ids."', '".
                              $row->idt."', '".
                              addslashes($row->ttitle)."', '".
                              addslashes($row->tspeaker)."', '".
                              $row->tday."', '".
                              addslashes($row->tcomment)."', '".
                              $row->location."', ".
                              $row->bld.", '".$row->floor."', '".
                              $row->room."', '".
                              addslashes($row->broadcasturl)."', ".
                              $row->type.", '".$row->cd."', '".
                              $row->md."', '".
                              addslashes($row->category)."', '".
                              $row->stime."', '".$row->repno."', '".
                              addslashes($row->affiliation)."', '".
                              $row->duration."', '".
                              addslashes($row->email)."')");
      //couldn't copy a talk to the main table: all the alredy copied talks, 
      //  sessions and agendas must be deleted from the main tables
      if( DB::isError( $err ) ) {
        return "error!! couldn't restore the talk '".$row->ids.$row->idt."': ".$err->getMessage();
      }
    }
    //then, move the agenda's subtalks to the corresponding main table
    $res = $db->query( "select ida, ids, idt, ttitle, tspeaker, tday, tcomment,
                               type, cd, md, stime, repno, affiliation, 
                               duration, fidt, email
                        from BK_SUBTALK
                        where ida='$ida'" );
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) ) {
      $err = $db->query(" 
                  insert into SUBTALK( ida, ids, idt, ttitle, tspeaker, tday,
                                       tcomment, type, cd, md, stime, repno,
                                       affiliation, duration, fidt, email )
                  values( '".$row->ida."', '".$row->ids."', '".
                             $row->idt."', '".
                             addslashes($row->ttitle)."', '".
                             addslashes($row->tspeaker)."', '".
                             $row->tday."', '". 
                             addslashes($row->tcomment)."', ".
                             $row->type.", '".$row->cd."', '".
                             $row->md."', '".$row->stime."', '".
                             addslashes($row->repno)."', '".
                             addslashes($row->affiliation)."', '".
                             $row->duration."', '".$row->fidt."', '".
                             addslashes($row->email)."')" );
      //couldn't copy a subtalk to the main table: all the already copied
      //  subtalks, talks, sessions and agendas must be deleted from main tables
      if( DB::isError( $err ) ) {
        return "error!! couldn't restore the subtalk '".$row->ids.$row->idt."-".$row->ttitle."': ".$err->getMessage();
      }
    }
    //now that we have copied all the data, we can proceed deleting the agendas
    //  on the bk tables.
    $err = $db->query("delete from BK_AGENDA where id='$ida'");
    if( DB::isError( $err ) ) {
      return "error!! couldn't delete agenda from the BK table: ".$err->getMessage();
    }
    $err = $db->query("delete from BK_SESSION where ida='$ida'");
    if( DB::isError( $err ) ) {
      //leave things as before: copy the agenda, the deleted sessions and delete
      //        entries in the main tables
      return "error!! couldn't delete agenda (session) from the BK table: ".$err->getMessage();
    }
    $err = $db->query("delete from BK_TALK where ida='$ida'");
    if( DB::isError( $err ) ) {
      return "error!! couldn't delete agenda (talk): ".$err->getMessage();
    }
    $err = $db->query("delete from BK_SUBTALK where ida='$ida'");
    if( DB::isError( $err ) ) {
      return "error!! couldn't delete agenda (subtalk) from the BK table: ".$err->getMessage();
    }
    return "";
  }


//===================================================================
// Main script
//===================================================================
  
  $db = & AgeDB::getDB();
//----------------------------------------------
// RESTORE AGENDAS to the sub-category
//----------------------------------------------
  if( isset($ida) && (count($ida)>0) )
  {
    if( (!isset($new_fid)) || (trim($new_fid)=="") )
    {
      die("error!! the destination sub-category hasn't been specified");
    }
    
    $db->autoCommit(false);

    foreach($ida as $element)
    {
      $str=restor( $element, $new_fid );
      if($str!="")
      {
        $db->rollback();
        die("$str");
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
  }
//----------------------------------------------
// DISPLAY the form
//----------------------------------------------
  else
  {
    $level = $db->getOne("select level
                          from LEVEL
                          where uid='$new_fid'");

    $Template = new Template( $PathTemplate );
    $Template->set_file(array( "mainpage" => "adminRestore.ihtml"));

    $Template->set_var("postAddr", getRecoverURL());
    $Template->set_var("img_trash", AGE_ADMIN_IMG_TRASH);

    $Template->set_var("category_name", "<font color=\"red\">".getHierarchy($new_fid)."</font>" );
    $Template->set_var( "new_fid", $new_fid );
    
    if( (!isset($order_agendas)) || 
        (trim($order_agendas)=="") ||
        (!in_array($order_agendas, array("stdate", "title", "dd"))) )
    {
      $order_agendas="stdate";
    }
    if($order_agendas=="title")
    {
      $order_agendas.=" asc";
    }
    else
    {
      $order_agendas.=" desc";
    }

    $res = $db->query("select id, stdate, title, dd
                       from BK_AGENDA
                       where fid='$new_fid'
				       order by $order_agendas");
    $str="";
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) )
    {
      $str.='
	  <tr>
	    <td bgcolor="white"><input type="checkbox" name="ida[]" value="'.$row->id.'"></td>
	    <td bgcolor="white">'.$row->stdate.'</td>
	    <td bgcolor="white">'.$row->title.'</td>
	    <td bgcolor="white">'.$row->dd.'</td>
	  </tr>';
    }
    $Template->set_var( "deleted_agendas", $str );

    $Template->pparse( "final-page", "mainpage" );
  }
?>
