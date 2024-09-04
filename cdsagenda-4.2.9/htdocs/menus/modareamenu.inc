<?
function CreateModAreaMenu()
{
  global $IMAGES,$ida;

  $text = "<!-- `modareamenu.inc' starts here -->
<!-- $Id: modareamenu.inc,v 1.1.1.1.4.3 2002/11/22 15:57:20 tbaron Exp $ -->
" . CreateMenuHeader( "ModAreaMenu" );

  $text .= "<IMG SRC=\"../${IMAGES}iconhome.gif\" ALT=\"\" VSPACE=0 HSPACE=0>&nbsp;<font size=-1 class=headline><B>AGENDAS</B></font>";

  $text .= "<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">";

  $text .= "addStaticMenuItem(\"menumodareatext1\",\"Delete&nbsp;this&nbsp;agenda\",\"javascript:delet();\",document);
	</SCRIPT>
		<HR>
	<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">";

  $text .= "addStaticMenuItem(\"menumodareatext2\",\"Quit&nbsp;modification&nbsp;area\",\"../fullAgenda.php?ida=$ida\",document);\n";

  $text .= "
	</SCRIPT>
" . CreateMenuFooter() . "
<!-- `modareamenu.inc' ends here -->";

  return $text;
}
?>