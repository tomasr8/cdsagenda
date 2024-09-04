<?
// $Id: adminEditAgenda.php,v 1.2.2.4 2003/03/28 10:27:15 tbaron Exp $

// adminEditAgenda.php --- Displays an interface that allows to modify agendas'
//                  main properties and commits the changes to the agenda DB. 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminEditAgenda.php is part of  (Manager module).
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
  
  if( (!isset($ida)) || (trim($ida)=="") ) 
  {
    die("error!! agenda's id not specified or incorrect");
  }

  $db = & AgeDB::getDB();
  
//----------------------------------------------
// UPDATE to the DB
//----------------------------------------------
  if(isset($action) && trim($action)=="edit")
  {
    if( (!isset($confid)) || (!in_array($confid, array("open", "password", "cern-only"))) )
    {
      $confid="open";
    }
    if( !isset($apassword) )
    {
      $apassword="";
    }

    //set the timming flag for the md
    $time=time();
    $today=strftime("%Y-%m-%d",$time);

    $err = $db->query("update AGENDA
                       set confidentiality='$confid',
		                   apassword='$apassword',
                           status='$status',
		                   md='$today'
                       where id='$ida'");
    if( DB::isError($err) )
    {
      die("error!! couldn't update agenda '$ida': ".$err->getMessage());
    }
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
  else
  {
    $res = $db->query("select confidentiality, apassword, title, status
                       from AGENDA
     				where id='$ida'");
    //no item found
    if($res->numRows()==0)
    {
      die("error!! the id specified doesn't correspond to any agenda");
    }

    $row = $res->fetchRow( DB_FETCHMODE_OBJECT );
  
    $Template = new Template( $PathTemplate );
    $Template->set_file(array( "mainpage" => "adminEditAgenda.ihtml"));

    $Template->set_var("title", "base <font color=\"red\">".$row->title."</font>");
    $Template->set_var("postAddr", getEditURLAgenda() );
    $Template->set_var("img_edit", AGE_ADMIN_IMG_EDIT );

    if($row->confidentiality=="password")
    {
      $Template->set_var("selected_password", "selected");
      $Template->set_var("selected_open", "");
      $Template->set_var("selected_cern", "");
    }
    elseif($row->confidentiality=="cern-only")
    {
      $Template->set_var("selected_password", "");
      $Template->set_var("selected_open", "");
      $Template->set_var("selected_cern", "selected");
    }
    else
    {
      $Template->set_var("selected_password", "");
      $Template->set_var("selected_open", "selected");
      $Template->set_var("selected_cern", "");
    }
    $Template->set_var("apassword", $row->apassword);
    $status = $row->status;
    if ($status == "open") {
        $Template->set_var("open", "checked");
        $Template->set_var("close", "");
    }
    else {
        $Template->set_var("close", "checked");
        $Template->set_var("open", "");
    }

    $Template->set_var("ida", $ida);

    $Template->pparse( "final-page", "mainpage" );
  }
?>
