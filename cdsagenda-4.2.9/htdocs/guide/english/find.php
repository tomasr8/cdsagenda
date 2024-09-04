<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">Top</A> > Search&nbsp;and&nbsp;Display > Find an Agenda</STRONG></TD></TR><TR>

	  <TD colspan=3><SMALL>
		<BR>
<?
  print "
		The agendas are grouped in a hierarchical way. At the top level (<A HREF=\"${AGE_WWW}\">main access page</A>), you will find the list of bases. Then you can navigate through this hierarchical tree of sub-categories in order to find the proper agenda. At any time during your navigation, you are informed of the exact place you are in by this little schema in the <B>[Go To]</B> menu:<BR><BR></SMALL>\n";
print "
		<CENTER><IMG SRC=\"${IMAGES_WWW}/tree.gif\" ALT=\"\" border=1></CENTER><BR><BR><SMALL>";
?>
		Each level in this schema is clickable and allows you to get directly to the chosen level.<BR><BR>
		The way the tree is organised depends on the base you are in. For example the tree of ATLAS agendas follows a low-level PBS of ATLAS, whereas the tree for the Seminars base follows a date driven structure.<BR><BR>
		<B>Please note</B>: There is no public tool for administrating the hierarchical tree of the CDS Agenda yet. If you need to create a new category, or move an agenda from one category to another, please ask us!<BR><BR>
<?
	print "
		The agendas are nested at the bottom-end of the tree. There in each branch the agendas appear chronologically. There is no limit regarding the depth of the tree. You can have an overview of the hierarchical tree and directly go to the desired branch by going to the <A HREF=\"${AGE_WWW}/agendamap.php\">Agenda Map</A>.<BR><BR>
		Another way of finding an agenda is to use the <A HREF=\"${AGE_WWW}/search.php\">search interface</A>.\n";
?>

		<BR><BR>

		<FONT color=green>See also:</FONT></SMALL>
			<BLOCKQUOTE><SMALL>
		<A HREF="search.php">search interface</A><BR>
		<A HREF="consult.php">consult an agenda</A><BR>
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
