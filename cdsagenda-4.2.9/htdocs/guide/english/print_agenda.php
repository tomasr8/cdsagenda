<?
	include "../../config/config.php";
	include "guideHeader.php";
?>

		<STRONG class=headline><A HREF="index.php">top</A> > notes > printing an agenda</STRONG></TD></TR><TR>
		
	  <TD colspan=3>
		<BR><SMALL>
	There are several ways for printing an agenda
	<UL>
	<LI><B>Use the default printable PDF output</B><BR><BR>
		<UL>
			CDS Agendas offers you a default PDF output format by clicking the small printer icon in the top blue menu.<BR>
			<IMG SRC="<?print ${IMAGES_WWW};?>/blue_tool_printer.gif"><BR>
			This PDF format has been designed to optimize the clarity of the printing for the biggest agendas. It will print the header page on a separate page, and will insert page breaks for every new day.
		</UL><BR>
	<LI><B>Use the Browser's printing facility</b><BR><BR>
		<UL>
			In this case you should first choose the view of the agenda which suits your needs. For this, go to the <B>[Change View]</B> menu.<BR>
			Then you should get rid of the header and footer of the standard agenda display by clicking the white up arrow in the top blue menu of the tool.<BR>
			<IMG SRC="<?print ${IMAGES_WWW};?>/blue_tool_up_arrow.gif"><BR>
			Then use the "File"->"Print..." facility of your browser.<BR>
			The result in this case depends on your browser's intrinsic printing ability. For example Internet Explorer will usually give you better results than Netscape.
		</UL><BR>
	<LI><B>Use the PostScript/PDF creation feature</B><BR><BR>
		<UL> 
			CDS Agendas offers you the opportunity to create a PostScript or a PDF from any view of your agenda.<BR>
			To use this feature, first choose the view of the agenda you wish to print by going into the <B>[Change View]</B> menu.<BR>
			Then, still in the <B>[Change View]</B> menu, click on <B>PostScript...</b> or <B>PDF...</B>.
			A setup menu will then appear in which you can choose the orientation of the output, whether it should be black&white or colored, the format (size) of the resulting page, and finally a scale.<BR>
		 The scale can be used for example to put the whole agenda on one page. If the default scale "1.0" results in a 2 pages display, try creating a new output with a smaller scale ("0.8" for example) until you obtain a good result.<BR>
		The scale can also be >1.0, for example if you wish to create an A3 Poster of the whole meeting agenda.
		</UL><BR>
	  </TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
