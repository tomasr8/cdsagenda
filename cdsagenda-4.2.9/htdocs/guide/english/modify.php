<?
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > modify an agenda</STRONG></TD></TR><TR>

	  <TD colspan=3>
		<BR>
		<SMALL><FONT color=red>Accessing the Modification Area:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			To modify or fill an agenda in, you need the modification password you were given at creation time. You should also have received an email containing this number. Now go to the default display page of your agenda and enter this number in the <B>[Modify]</B> menu. Then press "Enter".<BR><BR><BR>
			If you correctly entered the password, you should get in what we call the "modification area". If your password is refused, make sure you did not enter any white space in it.
		</SMALL></BLOCKQUOTE>
		<SMALL><FONT color=red>The Modification Area:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			The "Modification Area" is caracterised by a green top menu, whereas the display top menu is blue.<BR>
			This is a WYSIWYG editor (What You See Is What You Get). In clear, what you see on this page will be exactly what the user will see when he will reach the default page for this agenda. All the modifications you do here are repercuted directly on the screen.<BR><BR>
			<B>Note:</B> The term "WYSIWYG" is a little dangerous when talking about Web applications, since the display will always be different between two platforms or two different browsers. Anyway, those of you who knew the first "Modification Area" of this tool (v1.0) will understand what I mean by WYSIWYG...<BR><BR>
<?
	print "
			When you are in the Modification Area, you just have to click on the title of the item you want to modify. The clickable items are preceded with a red arrow (<IMG SRC=\"${AGE_WWW}/images/link.gif\" ALT=\"\">)<BR><BR>\n";
?>
			An agenda is composed of a global object (called "Agenda") which contains one or several "sessions" themselves composed of "talks". The clickable items are the titles of the agenda, sessions and talks.<BR><BR>
			Clicking on an item will pop-up a toolbar. There are 3 different toolbars; one for each kind of item.
		</SMALL></BLOCKQUOTE>

		<SMALL><FONT color=green>See also:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			<A HREF="agenda_toolbar.php">agenda toolbar</A><BR>
			<A HREF="session_toolbar.php">session toolbar</A><BR>
			<A HREF="talk_toolbar.php">talk toolbar</A><BR><BR>
			
			<A HREF="find.php">find an agenda</A><BR>
			<A HREF="consult.php">consult an agenda</A><BR>
			<A HREF="create.php">create an agenda</A><BR>
		</SMALL></BLOCKQUOTE>
	  </TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
