<?php
// $Id: viewmenu.inc,v 1.1.1.1.4.5 2002/11/22 15:57:22 tbaron Exp $

// viewmenu.inc --- view menu
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// viewmenu.inc is part of CDS Agenda.
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

function CreateViewMenu() {
	global $stylesheets,$ida,$dl,$dd,$OtherFormatPSActive,$OtherFormatPDFActive,$OtherFormatExcellActive,$OtherFormatTextActive,$OtherFormatvCalActive;

	$text = "<!-- `viewmenu.inc' starts here -->
<!-- $Id: viewmenu.inc,v 1.1.1.1.4.5 2002/11/22 15:57:22 tbaron Exp $ -->
" . CreateMenuHeader( "ViewMenu" );

	$num = 1;
	$text .= "
		&nbsp;<font size=-1 class=headline><B>AVAILABLE&nbsp;VIEWS</B></font>
		<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">\n";

	foreach ($stylesheets as $key => $val) {
		$val = str_replace(" ","&nbsp;", $val);
		$text .= "
		addStaticMenuItem(\"m5text$num\",\"$val\",\"fullAgenda.php?ida=$ida&amp;stylesheet=$key&amp;dl=$dl&amp;dd=$dd\",document);";
		$num++;
	}

	$text .= "
		if (DOM == 1)
		{
			document.write('&nbsp;<font size=-1 class=headline><B>OTHER&nbsp;FORMATS</B></font>');
		}
		else
		{
			document.write('<BR>&nbsp;<font size=-1 class=headline><B>OTHER&nbsp;FORMATS</B></font>');
		} ";

	if ( $OtherFormatPSActive ) {
		$text .= "		addStaticMenuItem(\"m5text$num\",\"PostScript...\",\"event.cancelBubble = true;javascript:center('PSMenu',true)\",document);";
		$num++;
	}
	if ( $OtherFormatPDFActive ) {
		$text .= "		addStaticMenuItem(\"m5text$num\",\"PDF...\",\"event.cancelBubble = true;javascript:center('PDFMenu',true)\",document);";
		$num++;
	}
	if ( $OtherFormatExcellActive ) {
		$text .= "		addStaticMenuItem(\"m5text$num\",\"Excel\",\"makeExcel.php?ida=$ida&amp;stylesheet=$stylesheet&amp;dl=$dl&amp;dd=$dd\",document);";
		$num++;
	}
	if ( $OtherFormatTextActive ) {
		$text .= "		addStaticMenuItem(\"m5text$num\",\"Text\",\"makeText.php?ida=$ida\",document);";
		$num++;
	}
	if ( $OtherFormatvCalActive ) {
		$text .= "		addStaticMenuItem(\"m5text$num\",\"vCalendar\",\"makevCal.php?id=$ida\",document);";
		$num++;
	}

	$text .= "
		if (DOM == 1)
		{
			document.write('&nbsp;<font size=-1 class=headline><B>IN&nbsp;THIS&nbsp;VIEW</B></font>');
		}
		else
		{
			document.write('<BR>&nbsp;<font size=-1 class=headline><B>IN&nbsp;THIS&nbsp;VIEW</B></font>');
		}\n";

	$num++;

	// display level
	if ($dl != "session") {
		$text .= "
		addStaticMenuItem(\"m5text$num\",\"Hide&nbsp;all&nbsp;talks\",\"fullAgenda.php?ida=$ida&amp;stylesheet=$stylesheet&amp;dl=session&amp;dd=$dd\",document);\n";
	}
	else {
		$text .= "
		addStaticMenuItem(\"m5text$num\",\"Display&nbsp;all&nbsp;talks\",\"fullAgenda.php?ida=$ida&amp;stylesheet=$stylesheet&amp;dl=talk&amp;dd=$dd\",document);\n";
	}

	$text .= "
		if (DOM == 1)
		{
			document.write('&nbsp;<font size=-1 class=headline><B>FOCUS&nbsp;ON</B></font>');
		}
		else
		{
			document.write('<BR>&nbsp;<font size=-1 class=headline><B>FOCUS&nbsp;ON</B></font>');
		}\n";

	$db = &AgeDB::getDB();
	$sql = "select DISTINCT speriod1,DATE_FORMAT(speriod1,'%W %D %M %Y') AS date from SESSION where ida='$ida' order by speriod1";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

	$num++;
	if ($numRows != 0) {
		for ($i = 0; $i < $numRows; $i++ ) {
			$row = $res->fetchRow();
			$text .= "
	addStaticMenuItem(\"m5text$num\",\"" . $row['date'] . "\",\"fullAgenda.php?ida=$ida&amp;stylesheet=$stylesheet&amp;dl=$dl&amp;dd=" . $row['speriod1'] . "\",document);";
			$num++;
		}
	}
	if ($dd != "" && $dd != "0000-00-00") {
		$text .= "
		addStaticMenuItem(\"m5text$num\",\"All Days\",\"fullAgenda.php?ida=$ida&amp;stylesheet=$stylesheet&amp;dl=$dl\",document);";
	}

	$text .= "
      </SCRIPT>
	  </SMALL>
" . CreateMenuFooter() . "
	  <!-- `menu5.inc' ends here -->";

	return $text;
}

?>
