<?php
// $Id: adminLocalConf.inc,v 1.2.2.5 2003/03/28 10:27:15 tbaron Exp $

// adminLocalConf.php --- Configuration file for the agenda manager module; all
//                  the module files include this one.
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Hector Sanchez <hector.sanchez@cern.ch>
//
// adminLocalConf.php is part of CDS Agenda (Manager module).
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

include("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once( "../platform/system/commonfunctions.inc" );
require_once( "../platform/authentication/sessinit.inc" );
require_once( "DB.php" );
require_once( "../AgeDB.php" );
include("adminCommon.inc");

?>
