<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="date.xsl"/>
<xsl:output method="html"/>

<!-- GLobal object: Agenda -->
<xsl:template match="agenda">
<xsl:text disable-output-escaping="yes">
&#060;!--START--&#062;
&#060;!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN"&#062;
</xsl:text>

<HTML>
<HEAD>
	
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=iso-8859-1"/>
	<TITLE><xsl:value-of select="@title"/></TITLE>
	<META NAME="GENERATOR" CONTENT="CDS Agenda"/>
<xsl:text disable-output-escaping="yes">&#060;META NAME="CHANGED" CONTENT="</xsl:text>
<xsl:value-of select="@modified"/>
<xsl:text disable-output-escaping="yes">"&#062;</xsl:text>
	
</HEAD>

<STYLE>
	<!-- 
	BODY { font-family: "Helvetica"; font-size: 2 }
	 -->
</STYLE>

<BODY TEXT="#000000" BGCOLOR="#ffffff">
<TABLE FRAME="VOID" CELLSPACING="0" COLS="5" RULES="GROUPS" BORDER="1">
	<COLGROUP><COL WIDTH="82"/><COL WIDTH="365"/><COL WIDTH="158"/><COL WIDTH="77"/><COL WIDTH="26"/></COLGROUP>
	<TBODY>
		<TR>
			<TD COLSPAN="4" WIDTH="682" HEIGHT="19" ALIGN="LEFT" VALIGN="TOP"><B><FONT FACE="Arial" SIZE="2"><xsl:value-of select="@title"/> - <xsl:value-of select="@location"/><xsl:if test="/agenda/session[1]/@room != '0--' and /agenda/session[1]/@room != ''"> (<xsl:value-of select="/agenda/session[1]/@room" disable-output-escaping="yes"/>)</xsl:if> - <xsl:call-template name="prettydate"><xsl:with-param name="dat" select="/agenda/session/talk[1]/@day"/></xsl:call-template><xsl:if test="/agenda/session/talk[1]/@stime != '00:00'"> / <xsl:value-of select="/agenda/session/talk[1]/@stime"/></xsl:if></FONT></B></TD>
			<TD WIDTH="26" HEIGHT="19" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
		</TR>
<xsl:for-each select="session">
<xsl:variable name="day" select="@sday"/>
<xsl:if test="count(preceding-sibling::session[position()=1 and @sday=$day]) = 0">
		<TR>
			<TD WIDTH="82" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="365" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="158" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="77" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="26" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
		</TR>
		<TR BGCOLOR="#99CCFF">
			<TD COLSPAN="4" WIDTH="682" HEIGHT="17" ALIGN="CENTER" VALIGN="TOP"><B><FONT FACE="Arial"><xsl:call-template name="prettydate"><xsl:with-param name="dat" select="@sday"/></xsl:call-template></FONT></B></TD>
			<TD WIDTH="26" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
		</TR>
		<TR>
			<TD WIDTH="82" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="365" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="158" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="77" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="26" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
		</TR>
</xsl:if>
		<TR BGCOLOR="#000060">
			<TD WIDTH="82" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial" color="white"><xsl:if test="@stime != '00:00'"><xsl:value-of select="@stime"/></xsl:if></FONT></TD>
			<TD WIDTH="365" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial" color="white"><xsl:value-of select="@title"/></FONT></TD>
			<TD WIDTH="158" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial" color="white"><xsl:value-of select="@schairman"/></FONT></TD>
			<TD WIDTH="77" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial" color="white"><xsl:if test="@room != '0--' and @room != ../@room and @
room != ''"><xsl:value-of select="@room" disable-output-escaping="yes"/><xsl:value-of select="@sroom" disable-output-escaping="yes"/></xsl:if></FONT></TD>
			<TD WIDTH="26" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
		</TR>
		<TR>
			<TD WIDTH="82" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="365" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="158" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="77" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
			<TD WIDTH="26" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
		</TR>
<xsl:for-each select="talk">
<xsl:variable name="idt" select="@id"/>
		<TR>
			<TD WIDTH="82" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial">
	<xsl:choose>
	<xsl:when test="/agenda/@type != 'olist'">
	        <xsl:if test="@stime != '00:00'">
		<xsl:value-of select="@stime"/>
	        </xsl:if>
	</xsl:when>
	<xsl:otherwise>
		<xsl:number format="1"/>.
	</xsl:otherwise>
	</xsl:choose></FONT></TD>
			<TD WIDTH="365" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP" BGCOLOR="#C0C0C0"><FONT FACE="Arial"><xsl:if test="@category != ''"><b><xsl:value-of select="@category"/></b><br/></xsl:if><b><xsl:value-of select="@title"/></b></FONT><xsl:if test="@comments != ''"><br/><xsl:value-of select="@comments" disable-output-escaping="yes"/></xsl:if></TD>
			<TD WIDTH="158" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP" BGCOLOR="#C0C0C0"><FONT FACE="Arial"><xsl:value-of select="@speaker"/></FONT></TD>
			<TD WIDTH="77" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><xsl:if test="@room != '0--' and @room != ../@room and @room != ''"><xsl:value-of select="@room" disable-output-escaping="yes"/></xsl:if></FONT></TD>
			<TD WIDTH="26" HEIGHT="17" ALIGN="LEFT" VALIGN="TOP"><FONT FACE="Arial"><BR/></FONT></TD>
		</TR>
</xsl:for-each>
<xsl:variable name="day" select="@sday"/>
</xsl:for-each>
	</TBODY>
</TABLE>
<xsl:text disable-output-escaping="yes">
&#060;!-- ************************************************************************** --&#062;
</xsl:text>
</BODY>

</HTML>
<xsl:text disable-output-escaping="yes">
&#060;!--STOP--&#062;
</xsl:text>
</xsl:template>
</xsl:stylesheet>