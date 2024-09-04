<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="date.xsl"/>
<xsl:include href="standardheader.xsl"/>
<xsl:output method="html"/>

<!-- GLobal object: Agenda -->
<xsl:template match="agenda">

<xsl:call-template name="agendaheader">
	<xsl:with-param name="agenda" select="."/>
</xsl:call-template>

<xsl:for-each select="session">	
<xsl:variable name="ids" select="@id"/>
<xsl:variable name="day" select="@sday"/>
<xsl:if test="count(preceding-sibling::session[position()=1 and @sday=$day]) = 0">
<tr>
	<td colspan="4">
	<a name="{$day}"/>
	<br/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><br/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><b>
	<xsl:call-template name="prettydate">
		<xsl:with-param name="dat" select="@sday"/>
	</xsl:call-template>
	</b>
	<hr/>
	</td>
</tr>
</xsl:if>

<xsl:call-template name="sessionheader">
	<xsl:with-param name="session" select="."/>
</xsl:call-template>

<table width="100%" cellpadding="4" cellspacing="0" border="0">
<xsl:for-each select="talk">
<xsl:variable name="idt" select="@id"/>

<xsl:choose>
	<xsl:when test="@type=1">
	<xsl:text disable-output-escaping="yes">
&#60;tr&#62;
	</xsl:text>
	</xsl:when>
	<xsl:otherwise>
	<xsl:text disable-output-escaping="yes">
&#60;tr bgcolor="#90C0F0" class="header"&#62;
	</xsl:text>
	</xsl:otherwise>
</xsl:choose>

	<td align="center" valign="top" width="1%">
	<font color="black">
	<b><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	<xsl:choose>
	<xsl:when test="/agenda/@type = 'olist'">
		<xsl:number format="A"/>.
	</xsl:when>
	<xsl:otherwise>
		<xsl:if test="@stime != '00:00'">
			<xsl:value-of select="@stime"/>  
		</xsl:if>
	</xsl:otherwise>
	</xsl:choose>
	</b>
	</font>
	<xsl:if test="@broadcasturl != ''">
		<br/><a href="{@broadcasturl}">
		<img src="images/camera.gif" border="0" width="33" height="24"/>
		<br/>(video broadcast)</a>
	</xsl:if>
	</td>

	<xsl:choose>
		<xsl:when test="@type=1">

		<xsl:choose>
		<xsl:when test="(@location != ../@location and @location != '') or (@room != ../@room and @room != '' and @room != '0--')">
			<td align="center" valign="top" class="header" bgcolor="#90C0F0" width="1%">
			<xsl:if test="@location != '' and @location != ../@location">
				<xsl:value-of select="@location"/><br/>
			</xsl:if>
			<xsl:if test="@room != '0--' and @room != ../@room and @room != ''">
				<xsl:value-of select="@room" disable-output-escaping="yes"/>
			</xsl:if>
			</td>

		</xsl:when>
		<xsl:otherwise>

		<td width="1%" align="center" valign="top"></td>
		</xsl:otherwise>
		</xsl:choose>


		<xsl:choose>
		<xsl:when test="count(preceding::talk) mod 2 = 1">
		<xsl:text disable-output-escaping="yes">
		&#60;td colspan="2" width="75%" valign="top" bgcolor="#E4E4E4"&#62;
	&#60;table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#E4E4E4"&#62;
		</xsl:text>
		</xsl:when>
		<xsl:otherwise>
		<xsl:text disable-output-escaping="yes">
		&#60;td colspan="2" width="75%" valign="top" bgcolor="#F6F6F6"&#62;
	&#60;table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F6F6F6"&#62;
		</xsl:text>
		</xsl:otherwise>
		</xsl:choose>

	<tr>
		<td valign="top">
		<a name="{$ids}{@id}"/>
		<xsl:if test="@category != '' and @category != ' '">
			<font size="+1" class="headerselected" bgcolor="#000060"><xsl:value-of select="@category"/></font>
			<br/><br/>
		</xsl:if>
		<xsl:variable name="idt" select="@id"/>
		[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
		<font class="headline"><b>
		<xsl:value-of select="@title" disable-output-escaping="yes"/>
		</b></font> 
		[/modifytalk]
		<xsl:if test="/agenda/@type != 'olist'"><small><font color="red"> (<xsl:call-template name="prettyduration"><xsl:with-param name="duration" select="@duration"/></xsl:call-template>) </font></small></xsl:if>
		<xsl:if test="@repno != ''">
			(<xsl:value-of select="@repno" disable-output-escaping="yes"/>)
		</xsl:if>
		<xsl:if test="count(child::link) != 0">
			(
			<xsl:for-each select="link">
				<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small>
			</xsl:for-each>
			)
		</xsl:if>
		</td>
		<td align="right">
		<xsl:if test="@speaker != ''">
			&#x0a0;&#x0a0;
			<b>
			<xsl:choose>
			<xsl:when test="@email != ''">
				<a href="mailto:{@email}">
				<xsl:value-of select="@speaker"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="@speaker"/>
			</xsl:otherwise>
			</xsl:choose>
			</b>	
			<xsl:if test="@affiliation != ''">
				 <br/><i>(<xsl:value-of select="@affiliation"/>)</i>
			</xsl:if>
		</xsl:if>
		</td>
	</tr>	
	<xsl:if test="@comments != ''">
	<tr>
		<td>
		<xsl:value-of select="@comments" disable-output-escaping="yes"/>
		</td>
	</tr>
	</xsl:if>
	<xsl:for-each select="subtalk">
	<xsl:variable name="idt" select="@id"/>
	<tr>
		<td>
		<ul>
		<li>
		<xsl:variable name="idt" select="@id"/>
		[modifysubtalk ids="<xsl:value-of select="../../@id"/>" idt="<xsl:value-of select="@id"/>"]
		<b class="headline"><small>
		<xsl:value-of select="@title" disable-output-escaping="yes"/>
		</small></b>
		[/modifysubtalk]
		<xsl:if test="@duration != '00:00:00'">
			<xsl:if test="/agenda/@type != 'olist'"><small><font color="red"> (<xsl:call-template name="prettyduration"><xsl:with-param name="duration" select="@duration"/></xsl:call-template>) </font></small></xsl:if>
		</xsl:if>
		<xsl:if test="@repno != ''">
			(<xsl:value-of select="@repno" disable-output-escaping="yes"/>)
		</xsl:if>
		<xsl:if test="count(child::link) != 0">
			(
			<xsl:for-each select="link">
				<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small>
			</xsl:for-each>
			)
		</xsl:if>
		<xsl:if test="@comments != ''">
			<br/><small><xsl:value-of select="@comments" disable-output-escaping="yes"/></small>
		</xsl:if>
		</li>
		</ul>

		</td>
		<td align="right">
		<xsl:if test="@speaker != ''">
			&#x0a0;&#x0a0;&#x0a0;&#x0a0;
			<b>
			<xsl:choose>
			<xsl:when test="@email != ''">
				<a href="mailto:{@email}">
				<xsl:value-of select="@speaker"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="@speaker"/>
			</xsl:otherwise>
			</xsl:choose>
			</b>	
			<xsl:if test="@affiliation != ''">
				 <br/><xsl:value-of select="@affiliation"/>
			</xsl:if>
		</xsl:if>
		</td>
	</tr>
	</xsl:for-each>
	<xsl:text disable-output-escaping="yes">
	&#60;/table&#62;
&#60;/td&#62;
	</xsl:text>
		</xsl:when>

		<xsl:otherwise>
		<td colspan="3" class="header" bgcolor="#90C0F0">
			<center>
			[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
			<xsl:value-of select="@title" disable-output-escaping="yes"/>
			[/modifytalk]
			</center>
		</td>
		</xsl:otherwise>
	</xsl:choose>
	
	<xsl:text disable-output-escaping="yes">
&#60;/tr&#62;
	</xsl:text>
</xsl:for-each>
</table>
<br/>

</xsl:for-each>	

</xsl:template>
</xsl:stylesheet>