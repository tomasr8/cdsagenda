<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- This stylesheet is mainly used from the online bulletin to create -->
<!-- the weekly calendar of all seminars-->

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
	from <xsl:value-of select="@stdate"/> to <xsl:value-of select="@endate"/><br/><br/>
	</td>
</tr>
</table>

<center>
<small><a href="makePDF.php?ida={/agenda/@id}&amp;format=A4&amp;orientation=Landscape&amp;colors=colored&amp;scale=1.0&amp;stylesheet=compactnosession">printable version (pdf)</a></small>
<table cellspacing="1" cellpadding="0" bgcolor="white" border="0">

<tr width="100%">
<td></td>
<xsl:for-each select="/agenda/session">
<xsl:variable name="day" select="@sday"/>
<xsl:if test="count(preceding::session[position()=1 and @sday=$day]) = 0">
	<td class="headerselected" align="center" bgcolor="#000060"><font color="white">
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
	<font size="-2" color="white"><b>
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

	<xsl:for-each select="/agenda/session/talk[@day=$sday and substring-before(@stime,':') &lt; 13 and ../@id=$ids]">
	<xsl:variable name="session" select="../@id"/>
	<xsl:variable name="sessionroom" select="../@room"/>
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
		<td valign="top"><font size="-1">
		[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
		<xsl:if test="@category != '' and @category != ' '">
			<font class="headerselected" bgcolor="#000060" size="-1"><xsl:value-of select="@category"/></font><br/>
		</xsl:if>
		<xsl:value-of select="@title" disable-output-escaping="yes"/>
		[/modifytalk]
		<xsl:if test="@speaker != ''">
			(<font color="green" size="-1"><xsl:value-of select="@speaker"/></font>)
		</xsl:if>
		<xsl:if test="@room != '0--' and @room != $sessionroom and @room != ''">
			(
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			)
		</xsl:if>
		</font></td>
	</xsl:when>
	<xsl:otherwise>
		<td valign="top" bgcolor="#FFdcdc">
		<font size="-1"><xsl:value-of select="@stime"/></font>	
		</td>
		<td valign="top" bgcolor="#FFcccc" align="center" colspan="1"><font size="-1">
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
	<font size="-2" color="white"><b>
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


	<xsl:for-each select="/agenda/session/talk[@day=$sday and substring-before(@stime,':') &gt;= 13 and ../@id=$ids]">
	<xsl:variable name="session" select="../@id"/>
	<xsl:variable name="sessionroom" select="../@room"/>
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
		<font size="-1"><xsl:value-of select="@stime"/></font>
		<xsl:text disable-output-escaping="yes">&#60;/td&#62;</xsl:text>
		<td valign="top"><font size="-1">
		[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
		<xsl:if test="@category != '' and @category != ' '">
			<font class="headerselected" bgcolor="#000060" size="-1"><xsl:value-of select="@category"/></font><br/>
		</xsl:if>
		<xsl:value-of select="@title" disable-output-escaping="yes"/>
		[/modifytalk]
		<xsl:if test="@speaker != ''">
			(<font color="green" size="-1"><xsl:value-of select="@speaker"/></font>)
		</xsl:if>
		<xsl:if test="@room != '0--' and @room != $sessionroom and @room != ''">
			(
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			)
		</xsl:if>
		</font></td>
	</xsl:when>
	<xsl:otherwise>
		<td valign="top" bgcolor="#FFdcdc">
		<font size="-1"><xsl:value-of select="@stime"/></font>	
		</td>
		<td valign="top" bgcolor="#FFcccc" align="center" colspan="1"><font size="-1">
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
<br/>
</center>

</xsl:template>
</xsl:stylesheet>