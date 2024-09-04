<?
	include "../../config/config.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 

<HTML>
<HEAD>
<?
	print "
<TITLE>CDS Agenda $VERSION - User guide</TITLE>
   <link rel=\"stylesheet\" href=\"${IMAGES_WWW}/cds_user_guide.css\">\n";
?>
</HEAD>
<BODY BGCOLOR="#FFFFFF" LINK="#009900" VLINK="#006600" ALINK="#FF0000"
	class=list>

<BR>
<CENTER>
<TABLE border=0 cellpadding=0 cellspacing=0 width="75%">
  <TR>
<?
	print "
    <TD valign=top><A HREF=\"${AGE_WWW}\"><img border=0 alt=\"CDS Agendas\" src=\"${AGE_WWW}/images/dec.agendas.gif\"></A></TD>\n";
?>
    <TD>  </TD>
    <TD align=left>
        &nbsp;&nbsp;<strong class=headline><FONT size="+2">USER GUIDE</FONT></strong>
<?
	print "
	<BR>&nbsp;&nbsp;<SMALL>(CDS Agenda version $VERSION)</SMALL>\n";
?>
    </TD>
  </TR>
<TR>
	  <TD colspan=3>
	    <BR><BR>
	    <BIG><STRONG class=headline>Table of Contents</STRONG></BIG>

	    <UL>
	      <LI><B>Introduction</B>
		<UL>
		  <LI><SMALL><A HREF="introduction.php">Use and scope of the tool</A></SMALL>
		  <LI><SMALL><A HREF="about.php">About CDS Agenda</A></SMALL>
		</UL>
		<LI><B>Search and Display</B>
		<UL>
			<LI><SMALL><A HREF="find.php">find an agenda</A></SMALL>
			<UL>
				<LI><SMALL><A HREF="search.php">search interface</A></SMALL>
			</UL>
			<LI><SMALL><A HREF="consult.php">consult an agenda</A></SMALL>
			<UL>
				<LI><SMALL><A HREF="default_format.php">default format</A></SMALL>
				<LI><SMALL><A HREF="other_formats.php">other formats</A></SMALL>
				<LI><SMALL><A HREF="overview.php">overview display</A></SMALL>
			</UL>
        </UL>
		<LI><B>Export Tools</B>
		<UL>
			<LI><SMALL><A HREF="pscheduler.php">Add to personal scheduler</A></SMALL>
        </UL>
		<LI><B>Create and Modify</B>
		<UL>
			<LI><SMALL><A HREF="create.php">create an agenda</A></SMALL>
			<LI><SMALL><A HREF="modify.php">modify an agenda</A></SMALL>
			<UL>
				<LI><SMALL><A HREF="agenda_toolbar.php">agenda toolbar</A></SMALL>
				<UL>
					<LI><SMALL><A HREF="add_session.php">add a session</A></SMALL>
					<LI><SMALL><A HREF="modify_agenda.php">modify the general data of an agenda</A></SMALL>
					<LI><SMALL><A HREF="edit_time_table.php">edit time table</A></SMALL>
					<LI><SMALL><A HREF="clone_agenda.php">clone agenda</A></SMALL>
					<LI><SMALL><A HREF="alarm.php">alarm set up</A></SMALL>
				</UL>
				<LI><SMALL><A HREF="session_toolbar.php">session toolbar</A></SMALL>
				<UL>
					<LI><SMALL><A HREF="add_talk.php">add a talk</A></SMALL>
					<LI><SMALL><A HREF="modify_session.php">modify the general data of a session</A></SMALL>
					<LI><SMALL><A HREF="write_minutes.php">write minutes</A></SMALL>
					<LI><SMALL><A HREF="compute_duration.php">compute the duration of the talks</A></SMALL>
					<LI><SMALL><A HREF="compute_time.php">compute the starting time of the talks</A></SMALL>
				</UL>
				<LI><SMALL><A HREF="talk_toolbar.php">talk toolbar</A></SMALL>
				<UL>
					<LI><SMALL><A HREF="add_subtalk.php">add a subtalk</A></SMALL>
					<LI><SMALL><A HREF="modify_talk.php">modify the general data of a talk</A></SMALL>
					<LI><SMALL><A HREF="write_minutes.php">write minutes</A></SMALL>
				</UL>
				<LI><SMALL>subtalk toolbar</SMALL>
				<UL>
					<LI><SMALL><A HREF="modify_subtalk.php">modify the general data of a subpoint</A></SMALL>
				</UL>
			</UL>
			<LI><SMALL><A HREF="attach.php">attach files</A></SMALL>
			<UL>
				<LI><SMALL><A HREF="list_of_files.php">list of files</A></SMALL>
				<LI><SMALL><A HREF="file_upload.php">file upload</A></SMALL>
				<LI><SMALL><A HREF="file_linkage.php">file linkage</A></SMALL>
				<LI><SMALL><A HREF="scanning_request.php">scanning request</A></SMALL>
			</UL>
		</UL>
	      <LI><B>Notes</B>
			<UL>
				<LI><SMALL><A HREF="print_agenda.php">printing an agenda</A></SMALL>
				<LI><SMALL><A HREF="login.php">why login?</A></SMALL>
			</UL>
		<LI><B><SMALL><A HREF="faq.php">FAQ</A></SMALL></B>
	    </UL>
	    
	  </TD>
</TR>
</TABLE>
</CENTER>
<BR><BR>
<HR>
</BODY>
</HTML>
