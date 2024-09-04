<?
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > create&nbsp;and&nbsp;modify  > <A HREF="attach.php">attach files</A> > list of files </STRONG></TD></TR><TR>

	  <TD colspan=3><SMALL>
		<BR>
		You can reach this menu from the <A HREF="modify.php">modification area</A> of your agenda.<BR>
		Here is what this menu looks like:<BR></SMALL>
		<BLOCKQUOTE><SMALL>
		<IMG SRC="<?print ${IMAGES_WWW};?>/list_of_files.gif" ALT="" border=1><BR><BR>
		This example shows the list of all the files attached to a talk which talk id is a00152s1t16.<BR>
		You can see that 2 files are attached to this talk:<BR><BR></SMALL>
		<UL>
		<LI><SMALL>The first file is a link (extension .link), which you can delete by clicking the "delete" link, or display by clicking the "display" link. It is only a redirection file containing an URL. It has been created by "file linkage". The file it points to is probably stored on a different server.<BR>
		A type can be attributed to each file using the pull-down menu at the right side of the file name. This one is of type "minutes"<BR><BR></SMALL>

		<LI><SMALL>The second one is a pdf document. This file is stored on CDS (it has been transfered via file upload)<BR>
		The file is of type "document". The link next to it ("create PostScript") allows you to convert the file into PostScript.<BR><BR></SMALL>

		<LI><SMALL>The last attached file is a TIFF file, probably created via a scanning request. It has been attributed the type "transparencies".<BR><BR></SMALL>
		</UL>

		<SMALL>The type you indicate using the pull-down menu will indicate to people who consult your agenda which kind of material they are looking at.<BR><BR>
		After the list of files, you have access to a list of buttons which allow you to delete all the files in the list, upload a new file, make a link to a file</SMALL>
		
		</BLOCKQUOTE>
		<BR><BR>
		<SMALL><FONT color=green>See also:</FONT></SMALL>
			<BLOCKQUOTE><SMALL>
			<A HREF="scanning_request.php">scanning request</A><BR>
			<A HREF="file_upload.php">file upload</A><BR>
			<A HREF="file_linkage.php">file linkage</A><BR><BR>
			
		<A HREF="find.php">find an agenda</A><BR>
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
