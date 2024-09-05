<?php
// $Id: AgeDB.php,v 1.1.2.2 2002/10/23 08:56:21 tbaron Exp $

// AgeDB.php --- database handler
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// AgeDB.php is part of CDS Agenda.
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

require_once( "DB.php" );
require_once( "config/config.php" );

//===========================
//  DB related
//===========================
define('DSN', "mysqli://$mysql_userid:$mysql_password@$mysql_machine:$mysql_port/$dbName");
define('FETCHMODE', DB_FETCHMODE_ASSOC);

class AgeDB {

    function AgeDB(){
    }

    function & getDB(){
		static $db;

		if(!isset($db)){
			$db= DB::connect( DSN );
			if(DB::isError($db)){
				die($db->getMessage());  
			}
			$db->setFetchMode(FETCHMODE);
		}
		return $db;
    }
}
?>
