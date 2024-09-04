<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="date.xsl"/>
<xsl:output method="html"/>

<!-- GLobal object: Agenda -->
<xsl:template match="agenda">

<table width="100%" bgcolor="white">
<tr>
	<td>
	<b>
	[modifyagenda]
	<xsl:value-of select="@title"/>
	[/modifyagenda]
	</b><br/>
	<xsl:if test="@stdate != @endate">
	from
	</xsl:if> 
	<xsl:call-template name="prettydate"><xsl:with-param name="dat" select="@stdate"/></xsl:call-template>
	<xsl:if test="@stdate != @endate">
	 to <xsl:call-template name="prettydate"><xsl:with-param name="dat" select="@endate"/></xsl:call-template>
	</xsl:if> 
	<br/><br/>
	</td>
	<td align="right">
	<table border="0" bgcolor="silver" cellspacing="3" cellpadding="1">
	<tr><td>
		<table border="0" bgcolor="white" cellpadding="2" cellspacing="0" width="100%">
		<tr>
			<td>

			<table><tr><td bgcolor="#90c0f0"><font size="-2"><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text></font></td><td><font size="-2"><b>: Sessions</b></font></td></tr></table>
			
			</td>
			<td>

			<table cellspacing="0"><tr><td bgcolor="silver"><font size="-2"><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text></font></td><td><font size="-2">/</font></td><td bgcolor="#D2D2D2"><font size="-2"><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text></font></td><td><font size="-2"><b>: Talks</b></font></td></tr></table>
			
			</td>
			<td>

			<table><tr><td bgcolor="#FFcccc"><font size="-2"><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text></font></td><td><font size="-2"><b>: Breaks</b></font></td></tr></table>
			
			</td>
		</tr>
		</table>
	</td></tr>
	</table>
	</td>
</tr>
</table>

<center>
<table cellspacing="1" cellpadding="0" bgcolor="white" border="0">

<tr width="100%">
<td></td>
<xsl:for-each select="/agenda/session">
<xsl:variable name="day" select="@sday"/>
<xsl:if test="count(preceding::session[position()=1 and @sday=$day]) = 0">
	<td class="headerselected" align="center" bgcolor="#000060"><font size="-2" color="white">
	<b>
	<xsl:call-template name="prettydate">
		<xsl:with-param name="dat" select="@sday"/>
	</xsl:call-template>
	</b><br/>
	</font></td>
</xsl:if>
</xsl:for-each>
</tr>

<tr bgcolor="white">
<td valign="top" class="headerselected" bgcolor="#000060" width="30">
	<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<tr>
	<td align="center" class="headerselected" bgcolor="#000060">
	<font size="-2"><b>
	AM
	</b></font>
	</td>
	</tr>
	</table>
</td>



<xsl:for-each select="/agenda/session">
<xsl:variable name="ids" select="@id"/>
<xsl:variable name="sday" select="@sday"/>
<xsl:if test="count(preceding::session[position()=1 and @sday=$sday]) = 0">
	<td valign="top" bgcolor="gray">

	<xsl:choose>

	<xsl:when test="count(/agenda/session/talk[@day=$sday and substring-before(@stime,':') &lt; 13]) = 0 and count(/agenda/session[@sday=$sday and substring-before(@stime,':') &lt; 13]) = 0">
		<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	</xsl:when>

	<xsl:otherwise>

	<table width="100%" cellspacing="1" cellpadding="3" border="0">
	<xsl:for-each select="/agenda/session[@sday=$sday and substring-before(@stime,':') &lt; 13]">
	<xsl:variable name="ids" select="@id"/>
	

	<xsl:if test="count(/agenda/session/talk[@day=$sday and substring-before(@stime,':') &lt; 13 and ../@id=$ids]) = 0 and substring-before(@stime,':') &lt; 13">
		<tr>
		<td valign="top" bgcolor="#b0e0ff" width="5%">
		<b><font size="-2"><xsl:value-of select="@stime"/></font></b>
		</td>
		<td colspan="1" bgcolor="#90c0f0"><font size="-2">
		[modifysession ids="<xsl:value-of select="@id"/>"]
		<b><xsl:value-of select="@title"/></b>
		[/modifysession]
		<xsl:if test="@chairperson != ''">
			<font size="-2">(<font color="green" size="-2"><xsl:value-of select="@chairperson"/></font>)</font>
		</xsl:if>
		<xsl:if test="@room != '0--' and @room != ''">
			<font size="-2">(
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			)</font>
		</xsl:if>
		<xsl:if test="@etime != '00:00'">
			(until <xsl:value-of select="@etime"/>)
		</xsl:if>
		<xsl:if test="count(child::link) != 0">
			<xsl:for-each select="link">
			<br/>
				<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small><br/>
			</xsl:for-each>
		</xsl:if>
		</font></td>
		</tr>
	</xsl:if>

	<xsl:for-each select="/agenda/session/talk[@day=$sday and substring-before(@stime,':') &lt; 13 and ../@id=$ids]">
	<xsl:variable name="session" select="../@id"/>
	<xsl:variable name="sessionroom" select="../@room"/>
	<xsl:if test="count(preceding::talk[position()=1 and ../@id=$session]) = 0">
		<tr>
		<td valign="top" bgcolor="#b0e0ff" width="5%">
		<b><font size="-2"><xsl:value-of select="../@stime"/></font></b>
		</td>
		<td colspan="1" bgcolor="#90c0f0"><font size="-2">
		[modifysession ids="<xsl:value-of select="../@id"/>"]
		<b><xsl:value-of select="../@title"/></b>
		[/modifysession]
		<xsl:if test="../@chairperson != ''">
			<font size="-2">(<font color="green" size="-2"><xsl:value-of select="../@chairperson"/></font>)</font>
		</xsl:if>
		<xsl:if test="../@room != '0--' and ../@room != ''">
			<font size="-2">(
			<xsl:value-of select="../@room" disable-output-escaping="yes"/>
			)</font>
		</xsl:if>
		<xsl:if test="../@etime != '00:00'">
			(until <xsl:value-of select="../@etime"/>)
		</xsl:if>
		</font></td>
		</tr>
	</xsl:if>
	<xsl:choose>
	<xsl:when test="count(preceding::talk) mod 2 = 1">
	<xsl:text disable-output-escaping="yes">
	&#60;tr bgcolor="silver"&#62;
	</xsl:text>
	</xsl:when>
	<xsl:otherwise>
	<xsl:text disable-output-escaping="yes">
	&#60;tr bgcolor="#D2D2D2"&#62;
	</xsl:text>
	</xsl:otherwise>
	</xsl:choose>
	<xsl:choose>
	<xsl:when test="@type=1">
		<xsl:choose>
		<xsl:when test="count(preceding::talk) mod 2 = 1">
		<xsl:text disable-output-escaping="yes">
		&#60;td bgcolor="#D0D0D0" valign="top" width="5%"&#62;
		</xsl:text>
		</xsl:when>
		<xsl:otherwise>
		<xsl:text disable-output-escaping="yes">
		&#60;td bgcolor="#E2E2E2" valign="top" width="5%"&#62;
		</xsl:text>
		</xsl:otherwise>
		</xsl:choose>
		<font size="-2"><xsl:value-of select="@stime"/></font>
		<xsl:text disable-output-escaping="yes">&#60;/td&#62;</xsl:text>
		<td valign="top"><font size="-2">
		[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
		<xsl:if test="@category != '' and @category != ' '">
			<font class="headerselected" bgcolor="#000060"><xsl:value-of select="@category"/></font><br/>
		</xsl:if>
		<xsl:value-of select="@title" disable-output-escaping="yes"/>
		[/modifytalk]
		<xsl:if test="@speaker != ''">
			(<font color="green" size="-2"><xsl:value-of select="@speaker"/></font>)
		</xsl:if>
		<xsl:if test="@room != '0--' and @room != $sessionroom and @room != ''">
			(
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			)
		</xsl:if>
		<xsl:if test="count(child::link) != 0">
			<xsl:for-each select="link">
			<br/>
				<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small>
			</xsl:for-each>
		</xsl:if>
		</font></td>
	</xsl:when>
	<xsl:otherwise>
		<td valign="top" bgcolor="#FFdcdc">
		<font size="-2"><xsl:value-of select="@stime"/></font>	
		</td>
		<td valign="top" bgcolor="#FFcccc" align="center" colspan="1"><font size="-2">
		[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
		---<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:value-of select="@title"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>---
		[/modifytalk]
		</font></td>
	</xsl:otherwise>
	</xsl:choose>
	<xsl:text disable-output-escaping="yes">&#60;/tr&#62;</xsl:text>
	</xsl:for-each> 
	</xsl:for-each>
	</table>
	
	</xsl:otherwise>
	</xsl:choose>

	</td>

</xsl:if>
</xsl:for-each>
</tr>



<tr>
<td valign="top" class="headerselected" bgcolor="#000060">
	<table width="100%" cellspacing="0" cellpadding="2" border="0">
	<tr>
	<td align="center" class="headerselected" bgcolor="#000060">
	<font size="-2"><b>
	PM
	</b></font>
	</td>
	</tr>
	</table>
</td>


<xsl:for-each select="/agenda/session">
<xsl:variable name="ids" select="@id"/>
<xsl:variable name="sday" select="@sday"/>
<xsl:if test="count(preceding::session[position()=1 and @sday=$sday]) = 0">
	<td valign="top" bgcolor="gray">

	<xsl:choose>

	<xsl:when test="count(/agenda/session/talk[@day=$sday and substring-before(@stime,':') &gt;= 13]) = 0 and count(/agenda/session[@sday=$sday and substring-before(@stime,':') &gt;= 13]) = 0">
		<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	</xsl:when>

	<xsl:otherwise>

	<table width="100%" cellspacing="1" cellpadding="3" border="0">

	<xsl:for-each select="/agenda/session[@sday=$sday]">
	<xsl:variable name="ids" select="@id"/>

	<xsl:if test="count(/agenda/session/talk[@day=$sday and substring-before(@stime,':') &gt;= 13 and ../@id=$ids]) = 0 and substring-before(@stime,':') &gt;= 13">
		<tr>
		<td valign="top" bgcolor="#b0e0ff" width="5%">
		<b><font size="-2"><xsl:value-of select="@stime"/></font></b>
		</td>
		<td colspan="1" bgcolor="#90c0f0"><font size="-2">
		[modifysession ids="<xsl:value-of select="@id"/>"]
		<b><xsl:value-of select="@title"/></b>
		[/modifysession]
		<xsl:if test="@chairperson != ''">
			<font size="-2">(<font color="green" size="-2"><xsl:value-of select="@chairperson"/></font>)</font>
		</xsl:if>
		<xsl:if test="@room != '0--' and @room != ''">
			<font size="-2">(
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			)</font>
		</xsl:if>
		<xsl:if test="@etime != '00:00'">
			(until <xsl:value-of select="@etime"/>)
		</xsl:if>
		<xsl:if test="count(child::link) != 0">
			<xsl:for-each select="link">
			<br/>
				<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small>
			</xsl:for-each>
		</xsl:if>
		</font></td>
		</tr>
	</xsl:if>

	<xsl:for-each select="/agenda/session/talk[@day=$sday and substring-before(@stime,':') &gt;= 13 and ../@id=$ids]">
	<xsl:variable name="session" select="../@id"/>
	<xsl:variable name="sessionroom" select="../@room"/>
	<xsl:if test="count(preceding::talk[position()=1 and ../@id=$session and substring-before(@stime,':') &gt;= 13]) = 0">
		<tr>
		<td valign="top" bgcolor="#b0e0ff" width="5%">
		<b><font size="-2"><xsl:value-of select="../@stime"/></font></b>
		</td>
		<td colspan="1" bgcolor="#90c0f0"><font size="-2">
		[modifysession ids="<xsl:value-of select="../@id"/>"]
		<b><xsl:value-of select="../@title"/></b>
		[/modifysession]
		<xsl:if test="../@chairperson != ''">
			<font size="-2">(<font color="green" size="-2"><xsl:value-of select="../@chairperson"/></font>)</font>
		</xsl:if>
		<xsl:if test="../@room != '0--' and ../@room != ''">
			<font size="-2">(
			<xsl:value-of select="../@room" disable-output-escaping="yes"/>
			)</font>
		</xsl:if>
		<xsl:if test="../@etime != '00:00'">
			(until <xsl:value-of select="../@etime"/>)
		</xsl:if>
		<xsl:if test="count(child::link) != 0">
			<xsl:for-each select="link">
			<br/>
				<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small>
			</xsl:for-each>
		</xsl:if>
		</font></td>
		</tr>
	</xsl:if>
	<xsl:choose>
	<xsl:when test="count(preceding::talk) mod 2 = 1">
	<xsl:text disable-output-escaping="yes">
	&#60;tr bgcolor="silver"&#62;
	</xsl:text>
	</xsl:when>
	<xsl:otherwise>
	<xsl:text disable-output-escaping="yes">
	&#60;tr bgcolor="#D2D2D2"&#62;
	</xsl:text>
	</xsl:otherwise>
	</xsl:choose>
	<xsl:choose>
	<xsl:when test="@type=1">
		<xsl:choose>
		<xsl:when test="count(preceding::talk) mod 2 = 1">
		<xsl:text disable-output-escaping="yes">
		&#60;td bgcolor="#D0D0D0" valign="top" width="5%"&#62;
		</xsl:text>
		</xsl:when>
		<xsl:otherwise>
		<xsl:text disable-output-escaping="yes">
		&#60;td bgcolor="#E2E2E2" valign="top" width="5%"&#62;
		</xsl:text>
		</xsl:otherwise>
		</xsl:choose>
		<font size="-2"><xsl:value-of select="@stime"/></font>
		<xsl:text disable-output-escaping="yes">&#60;/td&#62;</xsl:text>
		<td valign="top"><font size="-2">
		[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
		<xsl:if test="@category != '' and @category != ' '">
			<font class="headerselected" bgcolor="#000060"><xsl:value-of select="@category"/></font><br/>
		</xsl:if>
		<xsl:value-of select="@title" disable-output-escaping="yes"/>
		[/modifytalk]
		<xsl:if test="@speaker != ''">
			(<font color="green" size="-2"><xsl:value-of select="@speaker"/></font>)
		</xsl:if>
		<xsl:if test="@room != '0--' and @room != $sessionroom and @room != ''">
			(
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			)
		</xsl:if>
		<xsl:if test="count(child::link) != 0">
			<xsl:for-each select="link">
			<br/>
				<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small>
			</xsl:for-each>
		</xsl:if>
		</font></td>
	</xsl:when>
	<xsl:otherwise>
		<td valign="top" bgcolor="#FFdcdc">
		<font size="-2"><xsl:value-of select="@stime"/></font>	
		</td>
		<td valign="top" bgcolor="#FFcccc" align="center" colspan="1"><font size="-2">
		[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
		---<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><xsl:value-of select="@title"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>---
		[/modifytalk]
		</font></td>
	</xsl:otherwise>
	</xsl:choose>
	<xsl:text disable-output-escaping="yes">&#60;/tr&#62;</xsl:text>
	</xsl:for-each> 
	</xsl:for-each>
	</table>
	
	</xsl:otherwise>
	</xsl:choose>

	</td>

</xsl:if>
</xsl:for-each>

</tr>
</table>
</center>

</xsl:template>
</xsl:stylesheet>