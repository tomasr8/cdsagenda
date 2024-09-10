<?php
// $Id: makeExcel.php,v 1.1.1.1.4.7 2003/03/28 10:32:18 tbaron Exp $

// makeExcel.php --- yet to be written
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// makeExcel.php is part of CDS Agenda.
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

require_once 'AgeDB.php';
require_once 'AgeLog.php';
require_once 'config/config.php';
//require_once 'platform/authentication/authorization.inc';
require_once 'platform/template/template.inc';

# Template creation
###########################################
// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array( "error"=> "error.ihtml" ));

$options = "-un";
$options2 = "";
// Parameters for the tool HTMLDOC
$htmlDocParam = "";
if ( $orientation == "" )
     $orientation = "portrait";
     if ( $colors == "" )
     $colors = "black&white";
     if ( $scale == "" )
     $scale = "1.0";
     if ($orientation == "landscape")
{
    $options .= "L";
    $htmlDocParam .= " --landscape ";
}
if ($colors == "colored")
{
    $options .= "U";
    $htmlDocParam .= " --color ";
}
if ($scale != "1.0") { $options2 = "-s $scale"; }

$format = strtolower($format);

if ($param == "")
{
    $param = "${AGE_WWW}/fullAgenda.php?ida=$ida&stylesheet=excel&header=none&dl=$dl&dd=$dd";
}

Header("Content-type: application/vnd.ms-excel");
#Header("Content-Disposition: attachment; filename=agenda.txt");
$id = uniqid("");

$log = &AgeLog::getLog();

if ( $LYNX != "" ) {
    `$LYNX -source -auth=agenda:$PHP_AUTH_PW "$param" > $TMPDIR/createPDF_${id}.txt`;
    $fp = fopen("$TMPDIR/createPDF_${id}.txt","r+");
    $text = fread($fp,filesize("$TMPDIR/createPDF_${id}.txt"));
    fclose($fp);

    $text = ereg_replace('.*<!--START-->',"",$text);
    $text = ereg_replace('<!--STOP-->.*',"",$text);

    echo $text;
    unlink("$TMPDIR/createPDF_${id}.txt");
}

else {
    outError("Cannot output Excel. Neither htmldoc nor lynx are installed on the server","01",$Template);
}

?>


