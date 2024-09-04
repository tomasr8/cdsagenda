<?
// $Id: adminDel.php,v 1.2.2.5 2004/07/29 10:06:06 tbaron Exp $

// adminDel.php --- Deletes a set of specified sub-categories (and their 
//                  descendants) or agendas 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminDel.php is part of  (Manager module).
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
//  function: delet
//  purpose:
//  params:
//  return value:
//============================================================================
  function delet( $ida )
  {
    //deleting an agenda means to also delete all its session, talks and 
    //  subtalks
    $db= & AgeDB::getDB();
    //first, check that the agenda exists
    $res = $db->query( "select title, id, stdate, endate,
    					       location, nbsession, chairman,
						       cem, status, an, cd, md, 
						       stylesheet, format, 
						       confidentiality, apassword,
						       repno, fid, acomments, 
						       keywords, visibility, bld,
						       floor, room
					    from AGENDA
					    where id='$ida'");
    //if the agenda doesn't exist, we just return
    if($res->numRows()==0)
    {
      return "agenda doesn't exists";
    }
    //agenda exists, so move it to the bk table
    $row=$res->fetchRow( DB_FETCHMODE_OBJECT );
    $nbsession=$row->nbsession;
    if( (trim($nbsession)=="")||(!isset($row->nbsession)) )
    {
      $nbsession="null";
    }
    $time=time();
    $today=strftime("%Y-%m-%d %H:%M:%S",$time);
    
    $err = $db->query("
    		insert into BK_AGENDA( title, id, stdate, endate, location, 
		                       nbsession, chairman, cem, status, an, 
				               cd, md, stylesheet, format, 
				               confidentiality, apassword, repno, 
				               fid, acomments, keywords, visibility,
				               bld, floor, room, dd )
                values ( '".addslashes($row->title)."', '".$row->id."', '".
		            $row->stdate."', '".$row->endate."', '".
			        addslashes($row->location)."', ".$nbsession.", '".
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
			        $row->fid."', '".
			        addslashes($row->acomments)."', '".
			        addslashes($row->keywords)."', '".
			        addslashes($row->visibility)."', ".
			        addslashes($row->bld).", '".
			        addslashes($row->floor)."', '".
			        addslashes($row->room)."', '".$today."')");
    if(DB::isError($err))
    {
      return "error!! couldn't move the agenda to bk table:".$err->getMessage();
    }

    //then, move the agenda's sessions to the corresponding bk table
    $res = $db->query( "select ida, ids, schairman, speriod1,
    					       stime, eperiod1, etime, 
						       stitle, snbtalks, slocation,
						       scem, sstatus, bld, floor,
						       room, broadcasturl, cd, md,
						       scomment
                        from SESSION
					    where ida='$ida'" );
    while($row = $res->fetchRow( DB_FETCHMODE_OBJECT ))
    { 
      $snbtalks=$row->snbtalks;
      if( (trim($snbtalks)=="")||(!isset($row->snbtalks)) )
	  {
	    $snbtalks="null";
	  }
      $err = $db->query( "
      		insert into BK_SESSION( ida, ids, schairman, speriod1, stime, 
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
      if( DB::isError($err) )
      {
        return "error!! couldn't move the session '".$row->ids."' to the bk table: ".$err->getMessage();
      }
    }
    //then move the agenda's talks to the corresponding bk table
    $res = $db->query( "select ida, ids, idt, ttitle, 
                               tspeaker, tday, tcomment,
						       location, bld, floor, room,
						       broadcasturl, type, cd, md,
						       category, stime, repno, 
						       affiliation, duration, email
                        from TALK
					    where ida='$ida'" );
    while($row = $res->fetchRow( DB_FETCHMODE_OBJECT ))
    {
      $err = $db->query( "
      		insert into BK_TALK( ida, ids, idt, ttitle, tspeaker, tday, 
				                 tcomment, location, bld, floor, room,
				                 broadcasturl, type, cd, md, category, 
				                 stime, repno, affiliation, duration, email)
                values( '".$row->ida."', '".$row->ids."', '".
		                   $row->idt."', '".
			               addslashes($row->ttitle)."', '".
			               addslashes($row->tspeaker)."', '".$row->tday."', '".
			               addslashes($row->tcomment)."', '".
			               $row->location."', ".
			               $row->bld.", '".$row->floor."', '".
			               $row->room."', '".
			               addslashes($row->broadcasturl)."', ".
			               $row->type.", '".$row->cd."', '".$row->md."', '".
			               addslashes($row->category)."', '".
			               $row->stime."', '".$row->repno."', '".
			               addslashes($row->affiliation)."', '".
			               $row->duration."', '".
			               addslashes($row->email)."')" );
      //couldn't copy a talk to the bk table: all the alredy copied talks, 
      //  sessions and agendas must be deleted from the bk tables
      if( DB::isError($err) )
      {
        return "error!! couldn't move the talk '".$row->ids.$row->idt."' to the bk table: ".$err->getMessage();
      }
    }
    //then, move the agenda's subtalks to the corresponding bk table
    $res = $db->query( "select ida, ids, idt, ttitle, tspeaker, tday, tcomment,
						       type, cd, md, stime, repno, affiliation, 
                               duration, fidt, email
                        from SUBTALK
					    where ida='$ida'" );
    while( $row = $res->fetchRow( DB_FETCHMODE_OBJECT ) )
    {
      $err = $db->query(" 
      	  	insert into BK_SUBTALK( ida, ids, idt, ttitle, tspeaker, tday,
		                            tcomment, type, cd, md, stime, repno,
					                affiliation, duration, fidt, email )
                values( '".$row->ida."', '".$row->ids."', '".
		                   $row->idt."', '".addslashes($row->ttitle)."', '".
			               addslashes($row->tspeaker)."', '".$row->tday."', '".
			               addslashes($row->tcomment)."', ".
			               $row->type.", '".$row->cd."', '".
			               $row->md."', '".$row->stime."', '".
			               addslashes($row->repno)."', '".
			               addslashes($row->affiliation)."', '".
			               $row->duration."', '".$row->fidt."', '".
			               addslashes($row->email)."')" );
      //couldn't copy a subtalk to the bk table: all the already copied
      //  subtalks, talks, sessions and agendas must be deleted from bk tables
      if( DB::isError($err) )
      {
	    return "error!! couldn't move the subtalk '".$row->ids.$row->idt."-".$row->ttitle."' to the bk table: ".$err->getMessage();
      }
    }
    //now that we have copied all the data, we can proceed deleting the agendas
    //  on the 'real' tables (non-bk ones).
    $err = $db->query("delete from AGENDA where id='$ida'");
    if( DB::isError( $err ) )
    {
      //leave things as before: delete all the entries in the bk table
      return "error!! couldn't delete agenda: ".$err->getMessage();
    }
    $err = $db->query("delete from SESSION where ida='$ida'");
    if( DB::isError( $err ) )
    {
      //leave things as before: copy the agenda, the deleted sessions and delete
      //	entries in the bk tables
      return "error!! couldn't delete agenda (session): ".$err->getMessage();
    }
    $err = $db->query("delete from TALK where ida='$ida'");
    if( DB::isError( $err ) )
    {
      //leave things as before: copy the agenda, sessions, deleted talks 
      //  and delete entries in the bk tables
      return "error!! couldn't delete agenda (talk): ".$err->getMessage();
    }
    $err = $db->query("delete from SUBTALK where ida='$ida'");
    if( DB::isError( $err ) )
    {
      //leave things as before: copy the agenda, sessions, talks, deleted  
      //  subtalks and delete entries in the bk tables
      return "error!! couldn't delete agenda (talk): ".$err->getMessage();
    }

    return "";
  }

//============================================================================
//  function: deleteLevelItems
//  purpose:
//  params:
//  return value:
//============================================================================
  function deleteLevelItems( $fid, $level )
  {
    $db = & AgeDB::getDB();
    //check for all the items on this level
    $res = $db->query( "select uid, fid, level, title, cd, 
                               md, abstract, icon, stitle
                        from LEVEL
				        where fid='$fid'
				        and level=$level" );
    //if there are items according to the specified criteria, they have to be 
    //	be deleted
    if($res->numRows()!=0){
      while($row = $res->fetchRow( DB_FETCHMODE_OBJECT )){
	    $str=deleteLevelItems( $row->uid, $level+1);
	    if( $str!="" ){
	      //some error occurred during son deletion
	      return $str; 
	    }
	    //if everything went ok, we proceed to delete the current item
	    $err = $db->query( "delete from LEVEL where uid='".$row->uid."'" );
	    if(DB::isError($err)){
	      return "error!! couldn't delete level item '".$row->id."'-'".$row->title."': ".$err->getMessage();
	    }
      }
    }
    //if there aren't items according to the specified criteria, let's check
    //  if there are some agendas for the specified fid and, in this case, 
    //  delete them
    else
    {
      $res = $db->query("select id
                         from AGENDA
     					 where fid='$fid'");
      while($row = $res->fetchRow( DB_FETCHMODE_OBJECT ))
	  {
	    $str=delet( $row->id );
	    if($str!="")
	    {
	      return $row->id."-".$str;
	    }
	  }
    }
    
    return "";
  }

//============================================================================
//  function: deleteLevel
//  purpose:
//  params:
//  return value:
//============================================================================
  function deleteLevel( $uid )
  {
    $db = & AgeDB::getDB();
    //we suppose the item specified by the uid exists, so we look for the sons
    $res = $db->query( "select uid, level, title
                        from LEVEL
					    where uid='$uid'" );
    if($res->numRows()==0)
    {
      return "level '$uid' doesn't exists";
    }
    
    $row = $res->fetchRow( DB_FETCHMODE_OBJECT );
    

    $err = deleteLevelItems ( $uid, $row->level+1 );
    //some error occurred during the deletion
    if($err!="")
    {
      return "$err";
    }


    $err = $db->query( "delete from LEVEL where uid='$uid'" );
    if( DB::isError($err) )
    {
      return "error!! couldn't delete the level item '$uid'-'".$row->title."'";
    }
    return "";
  }

//===========================================================================
//  Main script
//===========================================================================
  //check uid parameter is there
  if( ( (!isset($uid_level)) || (trim($uid_level)=="") ) &&
      ( (!isset($ida)) || (trim($ida)=="")) )
  {
    if(isset($refreshURL) && (trim($refreshURL)!=""))
    {
      header("Location: $refreshURL");
    }
    else
    {
      header("Location: ".getMainURL());
    }
  }

  $str="";
  //delete subcategories
  $db = & AgeDB::getDB();
  $db->autoCommit(false);
  if(isset($uid_level))
  {
    foreach($uid_level as $element)
    {
      $str=deleteLevel( $element );
      if($str!="")
      {
        break;
      }
    }
  }
  //delete agendas
  elseif(isset($ida))
  {
    foreach($ida as $element)
    {
      $str=delet( $element );
      if($str!="")
      {
        break;
      }
    }
  }

  if($str=="")
  {
    $db->commit();
    header("Location: $refreshURL" );
  }
  else
  {
    $db->rollback();
    print $str;
  }
?>
