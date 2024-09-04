<?
	include ("../../config/config.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 

<HTML>
<HEAD>
<?
	print "
<TITLE>CDS Agendas $VERSION - User guide</TITLE>
   <link rel=\"stylesheet\" href=\"${IMAGES_WWW}/cds_user_guide.css\">\n";
?>
</HEAD>
<BODY 	BGCOLOR="#FFFFFF" 
	LINK="#009900" 
	VLINK="#006600" 
	ALINK="#FF0000"
	class="list">

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
	<BR>
    </TD>
  </TR>
<TR>
	<TD>&nbsp;</TD>
</TR>
<TR>
	<TD class=results colspan=3>
<?
	print "
		<IMG SRC=\"${AGE_WWW}/images/okay.gif\" ALT=\"\" align=top>\n";
?>