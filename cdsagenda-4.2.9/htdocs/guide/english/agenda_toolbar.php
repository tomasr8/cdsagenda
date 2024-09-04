<?
include "../../config/config.php";
include "guideHeader.php";
?>

<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify > <A HREF="modify.php">modify an agenda</A> > agenda toolbar</STRONG></TD></TR><TR>

<TD colspan=3>
<BR>
<SMALL><FONT color=red>Description:</FONT></SMALL>
<BLOCKQUOTE><SMALL>
You open it by clicking on the agenda title in the <A HREF="modify.php">modification area</A>. It looks like this:<BR><BR>
<IMG SRC="<?print ${IMAGES_WWW}; ?>/agenda_toolbar.gif" ALT=""><BR><BR>
</SMALL></BLOCKQUOTE>
<?
print "
		<IMG SRC=\"${IMAGES_WWW}/add.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Add New Session:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Click here to create a new session in this agenda. You will then have to fill a form with the session data and click on the \"ADD SESSION\" button at the bottom of the form. Your new session then appears in the agenda.
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/edit.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Edit Agenda Data:</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Click here to modify the data of the global object (your conference title, chairman...).
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/delete.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Delete Agenda</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			You will be prompted for a confirmation. If you confirm, your agenda will then be destroyed with its constituting sessions and talks. The attached files will remain (for security reasons) for some time on our server before being deleted.
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/clone.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Clone Agenda</FONT></SMALL>
		<BLOCKQUOTE><SMALL>
			Click here if you want to create a new agenda which will be the exact copy of this one.
		</SMALL></BLOCKQUOTE>
		<IMG SRC=\"${IMAGES_WWW}/files.gif\" ALT=\"\" align=bottom> <SMALL><FONT color=red>Attach files</FONT></SMALL>
		<BLOCKQUOTE><SMALL>\n";
?>
This menu is used to associate one or several files to an agenda.<BR><BR></SMALL>
<IMG SRC="<? print ${IMAGES_WWW}; ?>/agenda_attach_files.gif" ALT=""><BR><BR>
<UL>
<LI><SMALL><B>List of files</B>: click here to see the list of all the files attached to this agenda (minutes, slides...).</SMALL>
<LI><SMALL><B>File Upload</B>: click here if you want to transfer a file to our server to be attached to this agenda. You should have the file locally stored on your computer. In this case, we are in charge of keeping the document you have transfered.</SMALL>
<LI><SMALL><B>File Linkage</B>: Use this option if you wish the file to stay on your server or on a server different from CDS. In this case, you just have to give a URL, it will appear in the List of files with a .link extension</SMALL>
</UL>
</BLOCKQUOTE>

<SMALL><FONT color=green>See also:</FONT></SMALL>
<BLOCKQUOTE><SMALL>
<A HREF="session_toolbar.php">session toolbar</A><BR>
<A HREF="talk_toolbar.php">talk toolbar</A><BR><BR>

<A HREF="list_of_files.php">list of files</A><BR>
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
