<?php
// $Id: reportLog.php,v 1.1.1.1.4.3 2002/10/23 08:56:24 tbaron Exp $

// reportLog.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Erik Simon <erik.simon@cern.ch>
//
// reportLog.php is part of CDS Agenda.
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

//FIXME: FILE NOT TESTED

require_once ( "config/config.php" );
require_once 'platform/system/cookieManager.inc';
require_once( "platform/template/template.inc" );
    
// Check if is called to log or to unlog
if ( $Login == "UnLog" )
{
	if ( !isset( $GLOBALS[ "cookieMan" ] ))
		// Instantiate the cookieManager class
		$GLOBALS[ "cookieMan" ] = new cookieManager();
	// Retrieve the identity of the current user
	$GLOBALS[ "cookieMan" ]->delete( $reportPageCookieName );
	// Clear the actual log and redirect to displayCalendar.php
	
	Header( "Location: displayCalendar.php?report=1&fid=$fid" );
}

// Report creation
if ( $agendaTABLEExtension_1 == false ) {
	// Cannot use the report output without there extensions on the database
	$report == 0;
}

// new template
$Template = new Template( $PathTemplate );

// Template set-up
$Template->set_file(array( "reportLog"=> "reportLog.ihtml" ));
 
$Template->set_var( "reportLog_domain", $standardEMAILDomain );
$Template->set_var( "reportLog_parameters", "fid=" . $fid . "&report=1&reportLog=1" );
    
$Template->pparse( "final-page", "reportLog" );
?>