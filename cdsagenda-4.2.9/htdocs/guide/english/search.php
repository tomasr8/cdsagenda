<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">Top</A> > Search&nbsp;and&nbsp;Display > <A HREF="find.php">Find an Agenda</A> > Search</STRONG></TD></TR><TR>

	  <TD colspan=3>
		<BR><BR>
		<SMALL><FONT color=red>INTERFACE:</FONT></SMALL><BR>
		<IMG SRC="<? print ${IMAGES_WWW}; ?>/search.gif" ALT="">
		<BR><BR>
		<SMALL><FONT color=red>SEARCH FIELD:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Make sure you choose the right field to search in. If you search for the name of a speaker and choose "title/comments" you will have no chance to find anything!<BR><BR>
			Agenda id allows you to find an agenda given its id: Do not forget the "a" at the beginning of it (eg. "a00139")<BR><BR>
			Remember an agenda is made of several sessions containing several talks. The words you are looking for may appear in a session and/or in its constituting talks!
		</SMALL></BLOCKQUOTE>
		<SMALL><FONT color=red>SEARCH PERIOD:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			3 ways of choosing a time period:<BR><BR></SMALL>
			<UL>
			<LI><SMALL>Given a particular day, month and/or year</SMALL>
			<LI><SMALL>Between two dates (beware the format!)</SMALL>
			<LI><SMALL>Given a week<BR><BR></SMALL>
			</UL>
			<SMALL>By default this is set to "Any day" "Any Month" "Any Year", which means no restriction on the search period.
		</SMALL></BLOCKQUOTE>
		<SMALL><FONT color=red>SEARCH WORDS:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			In the example above, the program will search for agenda containing "muon" AND "decay" in their title or abstract. If you wish to search "muon" OR "decay", simply check the "or" button.
		</SMALL></BLOCKQUOTE>

		<SMALL><FONT color=green>See also:</FONT></SMALL>
			<BLOCKQUOTE><SMALL>
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
