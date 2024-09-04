<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">Top</A> > Search&nbsp;and&nbsp;Display > Consult an Agenda</STRONG></TD></TR><TR>

	  <TD colspan=3>
		<BR>
		<SMALL><FONT color=red>List of agendas:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Once you have reached the proper category in the Agenda hierarchical tree, you face a list of all the agendas in this category. This list is displayed in chronological order. Next to each agenda appears in parenteses the protection level of the agenda. <BR></SMALL>
			<UL>
			<LI><SMALL>No mention means the agenda is open to anybody in consultation.</SMALL>
			<LI><SMALL>(protected) means you will neeed a password to access this agenda (please contact the responsible of the agenda for this password).</SMALL>
			<LI><SMALL>(cern-only) means only computers on the CERN site will be able to read the agenda data.</SMALL></UL>
			<SMALL>To access an agenda click on its title.<BR><BR></SMALL>
			<CENTER><IMG SRC="<? print ${IMAGES_WWW}; ?>/list.gif" ALT="" border=1></CENTER><BR>
			<SMALL>When an agenda has been created less than a week ago, a little yellow "New!" flag appears next to it.
		</SMALL></BLOCKQUOTE>

		<SMALL><FONT color=red>Display of an agenda:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Once you have clicked on the agenda title and if needed entered the appropriate password, you access the default display of the agenda. You can also display the agenda in other formats.<BR><BR>
<?
	print "
			The last way of accessing data from the CDS Agenda, is to use the <a href=\"../../overview.php\">overview</a> display (you will find it in the <B>[Tools]</B> menu), which allows you to have an overview of all the agendas for a given category and a given period of time.\n";
?>
		</SMALL></BLOCKQUOTE>

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
