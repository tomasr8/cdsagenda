<?php
// $Id: makePDF.php,v 1.1.1.1.4.7 2003/03/28 10:32:18 tbaron Exp $

// makePDF.php --- yet to be written
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// makePDF.php is part of CDS Agenda.
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
require_once 'config/config.php';
require_once 'platform/template/template.inc';

$Template = new Template( $PathTemplate );
$Template->set_file(array( "error"=> "error.ihtml" ));

$html2psParam = "-un";
$html2psParam2 = "";
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

Header("Content-Disposition: attachment; filename=\"eagenda.pdf\"");
Header("Content-type: application/pdf");

$id = uniqid("");

if ($PHP_AUTH_PW == "") {
    $PHP_AUTH_PW = "toto";
}

if ( $HTMLDOC != "") {
    // Write the content type to the client...
    Header("Content-Disposition: inline; filename=\"eagenda.pdf\"");
    flush();

    // Run HTMLDOC to provide the PDF file to the user...
    passthru("$LYNX -source -auth=agenda:$PHP_AUTH_PW \"$param\" | $HTMLDOC $htmlDocParam --firstpage c1 -t pdf --quiet --jpeg --webpage --header t.D --footer ./. --left 0.5in - ");
}

elseif ( $LYNX != "" && $HTML2PS != "" && $PS2PDF != "" ) {
    system("$LYNX -source -auth=agenda:$PHP_AUTH_PW \"$param\" > $TMPDIR/createPDF_${id}.html");
    system("$HTML2PS -S \"paper{type:$format;}\" $html2psParam $html2psParam2 $TMPDIR/createPDF_${id}.html > $TMPDIR/createPDF_${id}.ps");
    chdir("$AGE");
    system("$PS2PDF -sPAPERSIZE=$format $TMPDIR/createPDF_${id}.ps $TMPDIR/createPDF_${id}.pdf");
    print "$PS2PDF -sPAPERSIZE=$format $TMPDIR/createPDF_${id}.ps $TMPDIR/createPDF_${id}.pdf";
    readfile("$TMPDIR/createPDF_${id}.pdf");
    unlink("$TMPDIR/createPDF_${id}.html");
    unlink("$TMPDIR/createPDF_${id}.ps");
    unlink("$TMPDIR/createPDF_${id}.pdf");

}

else {
    outError("Cannot output PDF. Neither htmldoc nor lynx+html2ps+ps2pdf are installed on the server","01",$Template);
}
?>
