<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > <A HREF="modify.php">modify an agenda</A> > <A HREF="agenda_toolbar.php">agenda toolbar</A> > modify agenda data</STRONG></TD></TR><TR>

	  <TD colspan=3><SMALL>
		<BR>
		This form allows you to modify the data you entered when you created the agenda. The current values appear in the corresponding fields when you access this page. You can modify them, then click on the "Modify Agenda" button to record your changes.</SMALL><BR><BR>

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
<TR>
   <TD valign=top align=right><B><SMALL>Default Stylesheet</SMALL></B></TD>
   <TD><SMALL>An agenda is composed of various fields which are stored in a database. CDS Agenda uses stylesheets to display it. <a href="other_formats.php">Different stylesheets</a> will represent different outputs on the screen. You can setup here the default output format for your agenda.</SMALL></TD>
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
