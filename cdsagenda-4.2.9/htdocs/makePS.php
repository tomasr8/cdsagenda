<?php
// $Id: makePS.php,v 1.1.1.1.4.8 2003/03/28 10:32:18 tbaron Exp $

// makePS.php --- yet to be written
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// makePS.php is part of CDS Agenda.
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

require_once 'config/config.php';
require_once 'platform/template/template.inc';

$Template = new Template( $PathTemplate );
$Template->set_file(array( "error"=> "error.ihtml" ));	

$html2psParam = "-un";
$html2psParam2 = "";
// Parameters for the tool HTMLDOC
$htmlDocParam = "";
if ( $orientation == "" ) {
    $orientation = "portrait";
}
if ( $colors == "" ) {
    $colors = "black&white";
}
if ( $scale == "" ) {
    $scale = "1.0";
}
$format = strtolower($format);

if ($orientation == "landscape") { 
    $html2psParam .= "L"; 
    $htmlDocParam .= " --landscape ";
}
else {
    $htmlDocParam .= " --portrait ";
}

if ($colors == "colored") { 
    $html2psParam .= "U"; 
    $htmlDocParam .= " --color ";
}
else {
    $htmlDocParam .= " --gray ";
}

if ($scale != "1.0") { 
    $html2psParam2 = "-s $scale"; 
}

if ($format == "a3") {
    $htmlDocParam .= " --size 420x594mm ";
}
elseif ($format == "a4") {
    $htmlDocParam .= " --size 210x297mm ";
}
elseif ($format == "a5") {
    $htmlDocParam .= " --size 105x148mm ";
}

if ($param == "" ||
    strncmp($param, "http://", 7) != 0) {
    $param = "${AGE_WWW}/fullAgenda.php?printable=1&ida=$ida&stylesheet=$stylesheet&header=none&dl=$dl&dd=$dd";
}

Header("Content-type: application/postscript");
$id = uniqid("");
 

if ( $HTMLDOC != "") {
    // Write the content type to the client...
    Header("Content-Disposition: inline; filename=\"eagenda.ps\"");
    flush();

    // Run HTMLDOC to provide the PS file to the user...
    passthru("$LYNX -source -auth=agenda:$PHP_AUTH_PW \"$param\" | $HTMLDOC --firstpage c1 -t ps --quiet --jpeg --webpage --header t.D --footer ./. --left 0.5in $htmlDocParam - ");
}


elseif ( $LYNX != "" && $HTML2PS != "" ) {
    system("$LYNX -source -auth=agenda:$PHP_AUTH_PW \"$param\" > $TMPDIR/createPS_${id}.html");
    system("$HTML2PS -S \"paper{type:$format;}\" $html2psParam $html2psParam2 $TMPDIR/createPS_${id}.html > $TMPDIR/createPS_${id}.ps");
    readfile("$TMPDIR/createPS_${id}.ps");
    unlink("$TMPDIR/createPS_${id}.html");
    unlink("$TMPDIR/createPS_${id}.ps");
}

else {
    outError("Cannot output PostScript. Neither htmldoc nor lynx+html2ps are installed on the server","01",&$Template);
}
?>
