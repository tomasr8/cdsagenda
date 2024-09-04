<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > search&nbsp;and&nbsp;display > <A HREF="modify.php">modify an agenda</A> > <A HREF="agenda_toolbar.php">agenda toolbar</A> > edit time table</STRONG></TD></TR><TR>

	  <TD colspan=3><SMALL>
		<BR>
		This special feature allows you to edit the time table of a whole agenda.<BR><BR>
		<UL>
		<LI>All the talks appear as colored rectangles on a time scale representing one working day (7AM - 11PM). There are as many time scales as sessions.<BR><BR>
		<LI>Drag&Drop the talks to their proper place on the time scale, then click on the "save changes" link to actually store your changes.<BR><BR>
		<LI>You can also go back to the initial state of the agenda by clicking the "initialize" link before saving your changes. The initial state is lost when you save your changes.<BR><BR>
		<LI>You can also choose whether you want to drag&drop one single talk (radio button "simple"), or if you want to drag all the talks in a session ("grouped").<BR><BR>
		<LI>A little drop-down menu allow you to choose which day you will modify. Don't forget to save your changes before moving to another day.<BR><BR>
		<LI>Click on <IMG SRC="<?print ${IMAGES_WWW}; ?>/zoom.gif"> icon to zoom (x2) the time scale.
		</UL>
		<BR><BR></SMALL>


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
