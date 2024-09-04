<?

function CreateToolMenu()
{
  global $fid,$ida,$stylesheet,$IMAGES_WWW,$AGE_WWW,$calendarActive;

  $textMenu = "
<!-- `toolmenu.inc' starts here -->
<!-- $Id: toolmenu.inc,v 1.1.1.1.4.6 2004/07/29 10:06:07 tbaron Exp $ -->
" . CreateMenuHeader( "ToolMenu" ) . "
		<IMG SRC=\"$IMAGES_WWW/iconhome.gif\" ALT=\"\" VSPACE=0 HSPACE=0>&nbsp;<font size=-1 class=headline><B>AGENDAS</B></font>
	<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">";
  $textMenu .= "      addStaticMenuItem(\"m1textMainMenu\",\"Home\",\"$AGE_WWW/List.php\",document,\"#9cf\");\n";
  $textMenu .= "      addStaticMenuItem(\"m1text1\",\"Agenda&nbsp;Search\",\"$AGE_WWW/search.php?fid=$fid\",document,\"#9cf\");\n";
  $textMenu .= "      addStaticMenuItem(\"m1text2\",\"Agenda&nbsp;Map\",\"$AGE_WWW/agendamap.php\",document,\"#9cf\");\n";
  $textMenu .= "      addStaticMenuItem(\"m1text3\",\"Overview\",\"$AGE_WWW/overview/overview.php?fid=$fid\",document,\"#9cf\");\n";
  $textMenu .= "      addStaticMenuItem(\"m1text5\",\"Calendar\",\"$AGE_WWW/monitor.php?categories=$fid\",document,\"#9cf\");\n";
  if ($fid != 0 && $fid != "" && $calendarActive) {
      $textMenu .=  "      addStaticMenuItem(\"mlisttext4\",\"Calendar\",\"$AGE_WWW/displayCalendar.php?fid=$fid&report=1\",document,\"#9cf\");\n";
  }

  $textMenu .= "
	</SCRIPT>
" . CreateMenuFooter() . "
<!-- `toolmenu.inc' ends here -->\n";

  return $textMenu;
}

?>
