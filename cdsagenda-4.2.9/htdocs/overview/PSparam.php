<?php
// $Id: PSparam.php,v 1.1.1.1.4.3 2002/10/23 08:56:38 tbaron Exp $

// PSparam.php --- overview: create postscript
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// PSparam.php is part of CDS Agenda.
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

include ("../config/config.php");
require_once '../platform/template/template.inc';

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => "psparam.ihtml"));
?>

<?
$paramText = "
		<INPUT type=\"hidden\" name=param value=\"$AGE_WWW/overview/overview.php?printable=1&period=$period&selectedday=$selectedday&selectedmonth=$selectedmonth&selectedyear=$selectedyear&fid=$fid\">\n";
$Template->set_var("psparam_param", $paramText);

$Template->pparse("final-page", 
                  "mainpage");
?>