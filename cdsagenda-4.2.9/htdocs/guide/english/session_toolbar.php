<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > <A HREF="modify.php">modify an agenda</A> > session toolbar</STRONG></TD></TR><TR>

	  <TD colspan=3>
		<BR>
		<SMALL><FONT color=red>Description:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			You open it by clicking on a session title in the <A HREF="modify.php">modification area</A>. It looks like this:<BR><BR>
			<IMG SRC="<? print ${IMAGES_WWW}; ?>/session_toolbar.gif" ALT=""><BR><BR>
		</SMALL></BLOCKQUOTE>
<?
	print "
		<IMG SRC=\"${IMAGES_WWW}/add.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Add New Talk:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Click here to create a new talk in this session. You will then have to fill a form with the talk data and click on the \"ADD TALK\" button at the bottom of the form. Your new talk then appears in the agenda.
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/edit.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Edit Session Data:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Click here to modify the data of the session you clicked on (session title, chairman...).
		</SMALL></BLOCKQUOTE>

		<IMG SRC=\"${IMAGES_WWW}/delete.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Delete Session</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			You will be prompted for a confirmation. If you confirm, this session will then be destroyed with its constituting talks. The attached files will remain (for security reasons) for some time on our server before being deleted.
		</SMALL></BLOCKQUOTE>

		<IMG SRC=\"${IMAGES_WWW}/book.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Room Booking</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Click here if you need to book a conference room or see the availability of a conference room at CERN. This links you to the <A HREF=\"http://booking.cern.ch:9000/cr/java/guest/welcomecrbs\">CRBS</A> (Conference Room Booking System at CERN).
		</SMALL></BLOCKQUOTE>

		<IMG SRC=\"${IMAGES_WWW}/files.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Attach files</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			This menu is used to associate one or several files to a session.<BR><BR></SMALL>
			<IMG SRC=\"${IMAGES_WWW}/session_attach_files.gif\" ALT=\"\"><BR><BR>
			<UL>
				<LI><SMALL><B>List of files</B>: click here to see the list of all the files attached to this session (minutes, slides...).</SMALL>
				<LI><SMALL><B>Scanning Request</B>: You have the opportunity here to send a scanning request for some or all of the talks in this sesssion. If you have many talks to be scanned in this session, you'd better use this one than the scanning request for each talk one by one.</SMALL>
				<LI><SMALL><B>File Upload</B>: click here if you want to transfer a file to our server to be attached to this session. You should have the file locally stored on your computer. In this case, we are in charge of keeping the document you have transfered.</SMALL>
				<LI><SMALL><B>File Linkage</B>: Use this option if you wish the file to stay on your server or on a server different from CDS. In this case, you just have to give a URL, it will appear in the List of files with a .link extension</SMALL>
				</UL>
		</BLOCKQUOTE>

		<IMG SRC=\"${IMAGES_WWW}/minutes.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Write Minutes</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Instead of transfering a file, you can also directly write the minutes for this talk in an input box. These minutes will be stored in a file attached to this talk (reachable from \"List of Files\").
		</SMALL></BLOCKQUOTE>

		<IMG SRC=\"${IMAGES_WWW}/time.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Compute talks duration</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			If you click here, the program will automatically <A HREF=\"compute_duration.php\">update the duration</A> of the talks in the given session.
		</SMALL></BLOCKQUOTE>

		<IMG SRC=\"${IMAGES_WWW}/time.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Compute talks starting time</FONT></SMALL>
		<BLOCKQUOTE><SMALL>\n";
?>
			If you click here, the program will automatically <A HREF="compute_time.php">update the starting time</A> of the talks in the given session.
		</SMALL></BLOCKQUOTE>

		<SMALL><FONT color=green>See also:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			<A HREF="agenda_toolbar.php">agenda toolbar</A><BR>
			<A HREF="talk_toolbar.php">talk toolbar</A><BR><BR>

			<A HREF="list_of_files.php">list of files</A><BR>
			<A HREF="scanning_request.php">scanning request</A><BR>
			<A HREF="file_upload.php">file upload</A><BR>
			<A HREF="file_linkage.php">file linkage</A><BR><BR>
			<A HREF="compute_duration.php">compute talks duration</A><BR>
			<A HREF="compute_time.php">compute talks starting time</A><BR><BR>
			
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
