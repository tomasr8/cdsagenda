<?
	//
	// Project:		Agenda Maker v2.0.2
	// Name:          	pocket/index.php
	// Description:   	hand-held devices page
	// Author:        	T.Baron
	//
	// Last Modified: 	18/07/2001
	//
	// INPUT: 		-
	// OUTPUT:	 	HTML
	//

	include ("../config/config.php");
	$fid = 0;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 

<HTML>
<HEAD>
  <TITLE>CDS Agendas - Handheld Devices</TITLE>  
  <link rel="stylesheet" href="../images/style.css">
</HEAD>
<BODY>

<TABLE>
<TR>
	<TD>
<img border=0 align=top src="../images/dec.agendas.small.gif" alt="CDS Agendas!">
	</TD>
	<TD>
		<font size=-1>
		for <a href=explain.php>hand held devices</a>
		</font>
	</TD>
</TR>
</TABLE>

<H1 class=headline>Variable URLS:</H1>

<TABLE align=center>
<TR>
	<TD bgcolor="#DDDDDD">
		<font size=-1>
	Special hand held agenda display (replace "a00187" with the id of the agenda you want to consult)
		</font>
	</TD>
</TR>
<TR>
	<TD>
	<a HREF="http://doc.cern.ch/palm?a00187">http://doc.cern.ch/palm?a00187</A>
	</TD>
</TR>
</TABLE>
<BR><BR><HR>
<small>
CDS Agendas - CERN ETT-DH-CDS
</small>
</BODY>
</HTML>
