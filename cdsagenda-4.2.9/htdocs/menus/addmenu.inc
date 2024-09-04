<?php
// $Id: addmenu.inc,v 1.1.1.1.4.4 2002/11/22 15:57:19 tbaron Exp $

// addmenu.inc --- menu add
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// addmenu.inc is part of CDS Agenda.
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

function CreateAddMenu() {
  global $fid,$AddMeetingActive,$AddSingleSessionMeetingActive,$AddSimpleEventActive,$AddLectureActive;

  $textMenu = "
<!-- `addmenu.inc' starts here -->
<!-- $Id: addmenu.inc,v 1.1.1.1.4.4 2002/11/22 15:57:19 tbaron Exp $ -->
" . CreateMenuHeader( "AddMenu" ) . "
		<IMG SRC=\"images/iconhome.gif\" ALT=\"\" VSPACE=0 HSPACE=0>&nbsp;<font size=-1 class=headline><B>NEW</B></font>
	<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">\n";

  if ( $AddMeetingActive ) {
    $textMenu .= "		addStaticMenuItem(\"addMenutext1\",\"Add&nbsp;meeting&nbsp;(multi-sessions)\",\"modification/addAgenda.php?fid=$fid&stylesheet=standard\",document);";
  }
  if ( $AddSingleSessionMeetingActive ) {
    $textMenu .= "		addStaticMenuItem(\"addMenutext4\",\"Add&nbsp;meeting&nbsp;(single-session)\",\"modification/addAgenda.php?fid=$fid&stylesheet=nosession\",document);";
  }
  if ( $AddSimpleEventActive ) {
    $textMenu .= "		addStaticMenuItem(\"addMenutext2\",\"Add&nbsp;simple&nbsp;event\",\"modification/addEvent.php?fid=$fid\",document);";
  }
  if ( $AddLectureActive ) {
    $textMenu .= "		addStaticMenuItem(\"addMenutext3\",\"Add&nbsp;lecture\",\"modification/addLecture.php?fid=$fid\",document);\n";
  }

  $textMenu .= "
	</SCRIPT>
" . CreateMenuFooter() . "
<!-- `addmenu.inc' ends here -->\n";

  return $textMenu;
}
?>
