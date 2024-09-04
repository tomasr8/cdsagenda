<?php
// $Id: MinWrit.php,v 1.1.2.2 2002/10/23 08:56:30 tbaron Exp $

// MinWritTalk.php --- minutes editor
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// MinWritTalk.php is part of CDS Agenda.
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

require_once("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once '../AgeLog.php';

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file( array ( "mainpage"=> "MinWrit.ihtml", "error" => "error.ihtml" ));	

$Template->set_var( "images", $IMAGES_WWW );

$Template->set_var("MinWritTalk_CATEG","$categ");
$Template->set_var("MinWritTalk_TYPE","$type");
$Template->set_var("MinWritTalk_BASE","$base");
$Template->set_var("MinWritTalk_IDA","$ida");
$Template->set_var("MinWritTalk_IDS","$ids");
$Template->set_var("MinWritTalk_IDT","$idt");
$Template->set_var("MinWritTalk_FROM","$from");
$Template->set_var("MinWritTalk_AN","$AN");
$Template->set_var("MinWritTalk_POSITION","$position");
$Template->set_var("MinWritTalk_STYLESHEET","$stylesheet");

$log = &AgeLog::getLog();

$agendadir="$ARCHIVE/$ida";
$strOutFile = "";
if (file_exists("$agendadir/$ida$ids$idt/minutes/$ida$ids$idt.txt"))
{
    $fd = fopen ( "$agendadir/$ida$ids$idt/minutes/$ida$ids$idt.txt", "r" );
    if ( !$strOutFile = fread ($fd, filesize ( "$agendadir/$ida$ids$idt/minutes/$ida$ids$idt.txt" )))
        if ( ERRORLOG )
            $log->logError( __FILE__ . "." . __LINE__, "main", " Cannot read file '$agendadir/$ida$ids$idt/minutes/$ida$ids$idt.txt' " );
    fclose( $fd );
}
	
$Template->set_var( "MinWritTalk_Minutes", ( $strOutFile ? $strOutFile : "" ));
$Template->pparse( "final-page" , "mainpage" );

?>
