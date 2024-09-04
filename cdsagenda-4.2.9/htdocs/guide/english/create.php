<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > create an agenda</STRONG></TD></TR><TR>

	  <TD colspan=3><SMALL>
		<BR>
		Anybody can create an agenda in this system. But make sure you create it in the proper category! (see "<a href='find.php'>find an agenda</a>")<BR>
<?
	print "
		If you want to test the system, feel free to create agendas in this <A HREF=\"${AGE_WWW}/displayLevel.php?fid=3l52&amp;level=4\">dummy category</A>.<BR><BR>\n";
?>
		When you are in the category in which you want to create an agenda, go in the <b>[Add Event]</b> menu. Agendas can only be created in bottom-end categories (no sub-categories under this one). In the following example:<BR>
		<IMG SRC="<? print ${IMAGES_WWW}; ?>/tree.gif" ALT=""><BR><BR>
		you can create an agenda in the category "2000" but not in "CERN", nor in "Seminars".<BR><BR>
		This <b>[Add Event]</b> menu contains three items to create different types of events:
		<UL>
			<LI>Choose "Add Meeting (multi-sessions)" if you want to enter the whole schedule of a meeting/conference in the system (with several sessions included).
			<LI>Choose "Add Meeting (single-session)" if you want to enter the  schedule of a simple meeting in the system.
			<LI>Choose "Add Lecture" if you want to enter only one entry (for a lecture or a seminar).
			<LI>Choose "Add Simple Event" if you want to store and promote one event without describing its planning.
		</UL>
		The lecture includes a speaker (with affiliation), a starting time and duration, whether the simple event include a starting date and an ending date.<BR><BR>
		Then you need to fill the form in and press the "ADD AGENDA" button at the bottom of the page. Your agenda is then created, it appears on the list of agendas with a little "New!" flag next to it. You are given a password from a pop-up box. Keep this password preciously, it will allow you to fill your agenda in and modify it afterwards. You should also receive an email containing this modification password.<BR><BR>
		Your agenda is still empty, you need to fill it in.<BR>
		See "modify an agenda" to know how to.
		<BR><BR></SMALL>

		<SMALL><FONT color=red>Description of the fields in the creation form:</FONT></SMALL>
			<BLOCKQUOTE>

<TABLE cellspacing=10 border=0>
<TR>
   <TD valign=top align=right><B><SMALL>Title</SMALL></B></TD>
   <TD><SMALL>Title of the agenda</SMALL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Chairman</SMALL></B></TD>
   <TD><SMALL>Name of the chairman of the meeting/conference (preferably formatted like that: Name, Initial(s))</SMALL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Contact e-mail</SMALL></B></TD>
   <TD><SMALL>E-mail address of the chairman. This field is optional. If you enter it, the name of the chairman will become clickable, allowing the user to send him directly an e-mail.</SMALL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Starting/ending dates</SMALL></B></TD>
   <TD><SMALL>Dates of beginning and end of the conference. If this is a one-day conference, enter the same date for both.</SMALL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Location</SMALL></B></TD>
   <TD><SMALL>General place where the meeting/conference will take place. Default is "CERN".</SMALL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Format</SMALL></B></TD>
   <TD><SMALL>This will have an effect on the display of your agenda<BR></SMALL>
	<UL>
      <LI><SMALL>The <B>"time-table"</B> format is the classical agenda. </SMALL>
      <LI><SMALL>If you choose the <B>"ordered list"</B> format, this agenda will be a succession of points, the hours you enter for each talk will not be meaningfull anymore (they will only indicate the order of the list).</SMALL></UL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Confidentiality level</SMALL></B></TD>
   <TD><SMALL>Here you have to choose between 3 different levels of protection:<BR></SMALL>
	<UL>
      <LI><SMALL><B>open</B>: means that anybody will be able to access (display) your agenda.</SMALL>
      <LI><SMALL><B>cern only</B>: your agenda will be accessible only from a computer located at CERN</SMALL>
      <LI><SMALL><B>password</B>: your agenda will be accessible only with an access password </SMALL></UL>
      <SMALL><B> Please note that this confidentiality level has NOTHING to do with the modification of the agenda. This only concerns the consultation (display) of the agenda</B></SMALL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Access password</SMALL></B></TD>
   <TD><SMALL>If you choose "password" as the confidentiality level of your agenda, you have to enter the access number of this agenda. <BR>The user will have to enter it to be able to consult this agenda. <BR>The username for the consultation of an agenda is always "agenda".</SMALL></TD>
</TR>
<TR>
   <TD valign=top align=right><B><SMALL>Report number</SMALL></B></TD>
   <TD><SMALL>If this agenda has an official CERN number, you can enter it here. It will be displayed in the header of the printable versions of the agenda.<BR>&nbsp;</SMALL></TD>
</TR>

</TABLE>



		</BLOCKQUOTE>

		<SMALL><FONT color=green>See also:</FONT></SMALL>
			<BLOCKQUOTE><SMALL>
		<A HREF="find.php">find an agenda</A><BR>
		<A HREF="consult.php">consult an agenda</A><BR>
		<A HREF="modify.php">modify an agenda</A><BR>
			</SMALL></BLOCKQUOTE>
	  </TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
