<?
function createPSMenu()
{
    global $AGE_WWW,$ida,$stylesheet,$dl,$dd;

    $menuText = "
<!-- `psmenu.inc' starts here -->
<!-- $Id: psmenu.inc,v 1.1.2.1 2002/11/22 15:57:21 tbaron Exp $ -->
<DIV	ID=\"PSMenu\"
	CLASS=\"menudiv\"
	onclick=\"event.cancelBubble = true;\">
<FORM action=\"makePS.php\" method=\"POST\">
<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=1 CLASS=header>
<TR class=\"headerselected\"><TD align=\"center\">
		&nbsp;<strong><SMALL><FONT color=\"white\">PS Creation Setup</FONT></SMALL></strong><BR>
	</TD>
</TR>
<TR>
	<TD align=\"center\">
		<TABLE width=\"100%\">
		<TR><TD align=right>
		<INPUT type=\"hidden\" name=param value=\"${AGE_WWW}/fullAgenda.php?printable=1&ida=$ida&amp;header=none&amp;stylesheet=$stylesheet&amp;dl=$dl&amp;dd=$dd\">
		<SMALL>orientation:</SMALL></TD><TD><SMALL><SELECT name=orientation><OPTION>portrait<OPTION>landscape</SELECT></SMALL></TD></TR>
		<TR><TD align=right><SMALL>colors:</SMALL></TD><TD><SMALL><SELECT name=colors><OPTION>black&amp;white<OPTION>colored</SELECT></SMALL></TD></TR>
		<TR><TD align=right><SMALL>format:</SMALL></TD><TD><SMALL><SELECT name=format><OPTION>A5<OPTION selected>A4<OPTION>A3</SELECT></SMALL></TD></TR>
		<TR><TD align=right><SMALL>scale:</SMALL></TD><TD><SMALL><INPUT name=\"scale\" size=3 value=\"1.0\"></SMALL></TD></TR></TABLE>
	</TD>
</TR>
<TR class=headerselected>
	<TD align=center><FONT color=\"white\" size=-1>
		<INPUT type=\"button\" value=\"OK\" onClick=\"javascript:popStaticMenuUp('PSMenu',false);if (document.layers){document.forms[0].submit();}else{document.forms[2].submit();};\"> <INPUT type=\"button\" value=\"CANCEL\" onClick=\"javascript:popStaticMenuUp('PSMenu',false);\">
	</FONT>
	</TD>
</TR>
</TABLE>
</FORM>
</DIV>
<!-- `menu6.inc' ends here -->";
    return $menuText;
}
?>
