<?php

function CreateHelpMenu()
{
  global $AGE_WWW,$VERSION,$support_submit,$statsOn,$fid;

  $textMenu = "
<!-- `helpmenu.inc' starts here -->
<!-- $Id: helpmenu.inc,v 1.1.1.1.4.5 2003/01/27 09:22:43 tbaron Exp $ -->
" . CreateMenuHeader( "HelpMenu" );

  $textMenu .= "<IMG SRC=\"${AGE_WWW}/images/iconinfo.gif\" ALT=\"\" VSPACE=0 HSPACE=0>&nbsp;<font size=-1 class=headline><B>DOCUMENTATION</B></font>\n";

  $textMenu .= "<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">";

  $textMenu .=  "
		addStaticMenuItem(\"m3text1\",\"User&nbsp;Guide\",\"${AGE_WWW}/guide/english/index.php\",document);
		addStaticMenuItem(\"m3text2\",\"About\",\"${AGE_WWW}/guide/english/about.php\",document);\n";

  $textMenu .= "</SCRIPT>";


  if ($support_submit != "") {
      $textMenu .= "
		<BR><IMG SRC=\"${AGE_WWW}/images/iconpen.gif\" ALT=\"\" VSPACE=0 HSPACE=0>&nbsp;<font size=-1 class=headline><B>EMAIL&nbsp;US</B></font>\n";
      $textMenu .= "
<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">
		addStaticMenuItem(\"m3text3\",\"Bugs&nbsp;and&nbsp;comments\",\"$support_submit\",document);
</SCRIPT>";
  }

	
  if ($statsOn) {
    $textMenu .= "
		<BR><IMG SRC=\"${AGE_WWW}/images/iconstats.gif\" ALT=\"\" VSPACE=0 HSPACE=0>&nbsp;<font size=-1 class=headline><B>STATISTICS</B></font>
	<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">
		addStaticMenuItem(\"m3text4\",\"Database\",\"${AGE_WWW}/tools/stats.php?fid=$fid\",document);
	</SCRIPT>";
  }

  $textMenu .= CreateMenuFooter() . "
<!-- `helpmenu.inc' ends here -->";

  return $textMenu;
}
?>
