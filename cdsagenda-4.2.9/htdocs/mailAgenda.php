<?php
// $Id mailAgenda.php,v 1.1.1.1.4.14 2003/01/06 16:47:59 tbaron Exp $

// mailAgenda.php --- 
//
// Copyright (C) 2003  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// mailAgenda.php is part of eAgenda.
//
// eAgenda is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// eAgenda is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with eAgenda; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// Commentary:
//
// 
//

require_once 'AgeDB.php';
require_once 'AgeLog.php';
require_once 'config/config.php';
require_once 'platform/template/template.inc';
require_once 'platform/system/cmailfile.inc';
require_once 'platform/system/commonfunctions.inc';

$db  = & AgeDB::getDB();

$Template = new Template( $PathTemplate );
$Template->set_file(array("error" => "error.ihtml", 
                          "email" => "emailbody.ihtml"));
                    
$stylesheet = "email";
             
if ($ida == "") {
    outError("Error sending email. ida not defined","01",&$Template);
}
if ($dest == "") {
    outError("Error sending email. destinator not defined","01",&$Template);
}
       
if ( $dest != "" ) {
	$sql = "select title,id from AGENDA where id='$ida'";

	$resTitle = $db->query($sql);
	if (DB::isError($resTitle)) {
		die ($resTitle->getMessage());
	}
	$numRowsTitle = $resTitle->numRows();
	if ($numRowsTitle > 0) {
		$rowTitle = $resTitle->fetchRow();
	}
							  
	// Email request, send the resulting text string to the dest address
    // FIXME: To be reimplemented following Simone Grassi code
	if ( EXECLOG)
		$log->logExec( __FILE__, __LINE__, " Sending by email the result for ida='$ida' to dest :'$dest' " );
	// Adding an explanation header to the body
	$htmltext = $emailHeader1 . "\n" . $htmltext;
	$GLOBALS[ "CMail" ] = new CMailFile( $EMAIL_SYSTEM_SUBJECT, $dest, "smr" . $rowTitle['smr'] . $standardEMAILDomain , $htmltext, "text/plain" );
	if ( !$GLOBALS[ "CMail" ]->sendfile( )) {
        if ( ERRORLOG)
            $log->logError( __FILE__, __LINE__, " '$error_msg' FALSE received sending email to '$dest' with body '$htmltext' " );
        outError("There was an error when sending the email. <br>The email has not sent","01",&$Template);

    }
	else {
        $Template->set_var("email_imageDIR", $IMAGES );
        $Template->set_var("email_msg", "The email has been sent" );
    }

	$Template->pparse( "final-page", "email" );
}

?>
