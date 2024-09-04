<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > <A HREF="modify.php">modify an agenda</A> > talk toolbar</STRONG></TD></TR><TR>

	  <TD colspan=3>
		<BR>
		<SMALL><FONT color=red>Description:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			You open it by clicking on a talk title in the <A HREF="modify.php">modification area</A>. It looks like this:<BR><BR>
<?
	print "
		<IMG SRC=\"${IMAGES_WWW}/talk_toolbar.gif\" ALT=\"\"><BR><BR>
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/add.gif\" aLT=\"\" align=bottom> <SMALL><FONT color=red>Add Sub Point:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			You can create a sub-point under this talk. Use this option if, for example, a talk is made of several points by different speakers.
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/edit.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Edit Talk Data:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Click here to modify the data of the talk you clicked on (talk title, chairman...).
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/delete.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Delete Talk</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			You will be prompted for a confirmation. If you confirm, this talk will then be destroyed. The attached files will remain (for security reasons) for some time on our server before being deleted.
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/files.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Attach files</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			This menu is used to associate one or several files to a talk.<BR><BR></SMALL>
			<IMG SRC=\"${IMAGES_WWW}/talk_attach_files.gif\" ALT=\"\"><BR><BR>
			<UL>
				<LI><SMALL><B>List of files</B>: click here to see the list of all the files attached to this talk (minutes, slides...).</SMALL>
				<LI><SMALL><B>Scanning Request</B>: You have the opportunity here to send a scanning request for this particular talk. If you have several talks to scan in a same session, you'd better use the scanning request in the <A HREF=\"session_toolbar.php\">session toolbar</A>.</SMALL>
				<LI><SMALL><B>File Upload</B>: click here if you want to transfer a file to our server to be attached to this talk. You should have the file locally stored on your computer. In this case, we are in charge of keeping the document you have transfered.</SMALL>
				<LI><SMALL><B>File Linkage</B>: Use this option if you wish the file to stay on your server or on a server different from CDS. In this case, you just have to give a URL, it will appear in the List of files with a .link extension</SMALL>
				<LI><SMALL><B>Give Report Number</B>:<BR><BR></SMALL>
				<BLOCKQUOTE>
					<IMG SRC=\"${IMAGES_WWW}/give_rn.gif\" ALT=\"\"><BR><SMALL>
					This option, still in developement, will allow you to make a link to a document stored in EDMS or Weblib only by giving its reference number.</SMALL>
				</BLOCKQUOTE>
			</UL>
		</BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/minutes.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Write Minutes</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Instead of transfering a file, you can also directly write the minutes for this talk in an input box. These minutes will be stored in a file attached to this talk (reachable from \"List of Files\").
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/time.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Advance/Delay Talk</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			<IMG SRC=\"${IMAGES_WWW}/ad_talk.gif\" ALT=\"\"><BR><BR>
			This menu allows you to advance or delay a talk of a given time (from 5 to 60 minutes) (blue arrows), or advance/delay all the talk after the clicked one (red arrows).
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/camera.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Broadcast URL</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Enter here a link to the webcast site which will broadcast the talk you have chosen.
		</SMALL></BLOCKQUOTE>\n";
?>

		<SMALL><FONT color=green>See also:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			<A HREF="agenda_toolbar.php">agenda toolbar</A><BR>
			<A HREF="session_toolbar.php">session toolbar</A><BR><BR>

			<A HREF="list_of_files.php">list of files</A><BR>
			<A HREF="scanning_request.php">scanning request</A><BR>
			<A HREF="file_upload.php">file upload</A><BR>
			<A HREF="file_linkage.php">file linkage</A><BR><BR>
			
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
