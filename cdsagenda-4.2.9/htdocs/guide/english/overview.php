<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">Top</A> > Search&nbsp;and&nbsp;Display > <A HREF="consult.php">Consult an Agenda</A> > Overview Display</STRONG></TD></TR><TR>
		
	  <TD colspan=3>
		<BR><SMALL>
<?
	print "
		This feature is accessible <A HREF=\"${AGE_WWW}/overview/overview.php\">here</A>. It allows you to display in one page all the events (agendas) in a given category taking place during a given period of time.<BR>\n";
?>
		The proposed periods are: <b>day</b>, <b>week</b>, <b>month</b> and <b>year</b>.
		You can choose the category, and the period which interests you.<BR><BR>
		Here is an example:<BR><BR>
		<IMG SRC="<? print ${IMAGES_WWW}; ?>/overview.gif" ALT="" border=1><BR><BR>
		In this example we ask the program to display all seminars taking place at CERN today Friday 9th March 2001.<BR>
		By opposition to the classical hierarchical browsing (as explained in "<a href='find.php'>find an agenda</a>"), this overview feature is more a time-driven way of finding the information you are looking for.
		<BR><BR></SMALL>
		<SMALL><FONT color=green>See also:</FONT></SMALL>
			<BLOCKQUOTE><SMALL>
			<A HREF="default_format.php">default format</A><BR>
			<A HREF="other_formats.php">other formats</A><BR><BR>
			
			<A HREF="find.php">find an agenda</A><BR>
			<A HREF="create.php">create an agenda</A><BR>
			<A HREF="modify.php">modify an agenda</A><BR>
			</SMALL></BLOCKQUOTE>
	  </TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
