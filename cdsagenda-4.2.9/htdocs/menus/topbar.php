<?php
// $Id: topbar.php,v 1.1.1.1.4.9 2003/03/28 10:25:43 tbaron Exp $

// topbar.php --- menu bar toolkit
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// topbar.php is part of CDS Agenda.
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

function CreateMenuBar( $menuArray ) {
  global $VERSION,$IMAGES_WWW,$authentication,$stylesheetdesc,$leveltext,$focustext,$makePDFparam,$ida,$stylesheet,$dl,$dd;

  $textMenu = "";

  $textMenu .= "
<table border=0 cellspacing=1 cellpadding=0 width=\"100%\">
<tr>";


  if (in_array("MainMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "MainMenu", "CDS&nbsp;Agenda&nbsp;System&nbsp;v$VERSION");
  }

  if (in_array("ToolMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "ToolMenu", "Tools");
  }

  if (in_array("ViewMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "ViewMenu", "Change&nbsp;View");
  }

  if (in_array("ModifyMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "ModifyMenu", "Modify");
  }

  if ( $authentication && in_array("UserMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "UserMenu", "User");
  }

  if (in_array("GoToMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "GoToMenu", "Go&nbsp;To");
  }

  if (in_array("AddMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "AddMenu", "Add&nbsp;Event");
  }

  if (in_array("ModAreaMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell( "ModAreaMenu", "Modification&nbsp;Area");
  }

  $textMenu .= "\n<td class=topbarheader width=\"100%\" align=center>";

  if (in_array("InfoText",$menuArray)) {
    $textMenu .= "<TABLE border=0 cellpadding=0 cellspacing=0 width=\"100%\"><TR><TD><FONT color=#000077 face=\"optima\" size=-2>&nbsp;&nbsp;Style:&nbsp;$stylesheetdesc</font><font color=white size=-2>&nbsp;|&nbsp;</font><FONT color=#000077 face=\"optima\" size=-2>$leveltext</font><font color=white size=-2>&nbsp;|&nbsp;</font><FONT color=#000077 face=\"optima\" size=-2>$focustext</font><font color=white size=-2>&nbsp;|&nbsp;</font></TD><TD></TD><TD><A HREF=\"makePDF.php?orientation=portrait&amp;colors=colored&amp;format=A4&amp;scale=1.0&amp;param=$makePDFparam\"><IMG SRC=\"$IMAGES_WWW/iconprnt2.gif\" ALT=\"printable PDF\" border=0 align=right height=13 width=14></A>&nbsp;</TD><TD><A HREF=\"fullAgenda.php?ida=$ida&amp;header=none&amp;stylesheet=$stylesheet&amp;dl=$dl&amp;dd=$dd\"><IMG SRC=\"$IMAGES_WWW/up_white2.gif\" ALT=\"withdraw header\" border=0 align=right height=7 width=13></A>&nbsp;</TD></TR></TABLE>";
  }
  else
    $textMenu .= "&nbsp;";

  $textMenu .= "</td>";

  if (in_array("HelpMenu",$menuArray)) {
    $textMenu .= CreateTopBarCell ("HelpMenu","Help");
  }

  $textMenu .= "
</tr>
</table><BR><BR>\n\n";

  return $textMenu;
}

function CreateTopBarCell( $menuName, $menuText )
{
    $topbarcell = "
   <TD CLASS=\"topbarheader\"
       ID=\"" . $menuName ."Tip\"
       CLASS=\"menutop\"><A HREF=\"\" onmouseover=\"clearTimeout(timerID);timerID=setTimeout('if (document.getElementById){document.getElementById(\'" . $menuName . "Tip\').style.backgroundColor=\'#006\';document.getElementById(\'" . $menuName . "TipText\').style.color=\'white\';};popStaticMenuUp(\'" . $menuName . "\',true)',opentimer);\" onmouseout=\"clearTimeout(timerID);timerID=setTimeout('closeOpened()',closetimer);\" onclick=\"return false;\"><FONT color=black><SMALL><SPAN ID='" . $menuName . "TipText'>[" . $menuText . "]</SPAN></SMALL></FONT></A>&nbsp;&nbsp;</TD>";
    return $topbarcell;
}

function CreateMenuHeader( $menuName )
{
    $menuheader = "
<DIV	ID=\"$menuName\"
	    CLASS=\"menudiv\"
        onMouseOver = \"clearTimeout(timerID)\"
        onClick = \"clearTimeout(timerID)\"
        onMouseOut  = \"timerID=setTimeout('closeName(\'$menuName\')',closetimer);\">

<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">
   function onMouseOut() {
      timerID=setTimeout('closeName(\'$menuName\')',closetimer);
      return false;
   }
   function onMouseOver() {
      clearTimeout(timerID);
      return false;
   }
</SCRIPT>

<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=1 CLASS=header>
<tr>
	<td valign=top>";
    return $menuheader;
}

function CreateMenuFooter( )
{
    $menufooter = "
	</TD>
</TR>
</TABLE>
</DIV>";
    return $menufooter;
}

function CreateMenus( $menuArray )
{
  global $AGE,$header;
  $textMenu = "";

  if (in_array("MainMenu",$menuArray)) {
    require ("$AGE/menus/mainmenu.inc");
    $textMenu .= CreateMainMenu();
  }

  if (in_array("ToolMenu",$menuArray)) {
    require ("$AGE/menus/toolmenu.inc");
    $textMenu .= CreateToolMenu();
  }

  if (in_array("GoToMenu",$menuArray)) {
    require ("$AGE/menus/gotomenu.inc");
    $textMenu .= CreateGoToMenu();
  }

  if (in_array("UserMenu",$menuArray)) {
    require ("$AGE/menus/usermenu.inc");
    $textMenu .= CreateUserMenu();
  }

  if (in_array("AddMenu",$menuArray)) {
    require ("$AGE/menus/addmenu.inc");
    $textMenu .= CreateAddMenu();
  }

  if (in_array("HelpMenu",$menuArray)) {
    require ("$AGE/menus/helpmenu.inc");
    $textMenu .= CreateHelpMenu();
  }

  if (in_array("ModifyMenu",$menuArray)) {
    require ("$AGE/menus/modifymenu.inc");
    $textMenu .= CreateModifyMenu();
  }

  if (in_array("ViewMenu",$menuArray)) {
    require ("$AGE/menus/viewmenu.inc");
    $textMenu .= CreateViewMenu();
  }

  if (in_array("ModAreaMenu",$menuArray)) {
    require ("$AGE/menus/modareamenu.inc");
    $textMenu .= CreateModAreaMenu();
  }

  return $textMenu;
}
?>
