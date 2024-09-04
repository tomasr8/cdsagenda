<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="../date.xsl"/>
<xsl:include href="standardpalmheader.xsl"/>
<xsl:output method="html"/>

<!-- GLobal object: Agenda -->
<xsl:template match="agenda">

<xsl:for-each select="session">	
<xsl:variable name="ids" select="@id"/>
<a name="{$ids}"/>

<table border="0">
<xsl:for-each select="talk">
<xsl:variable name="idt" select="@id"/>
<xsl:variable name="day" select="@day"/>
	<tr>
	<td valign="top">
	<font size="-1">
	<a name="{$ids}{@id}"/>
	<xsl:if test="@stime != '00:00'">
		<b><xsl:value-of select="@stime"/></b>
	</xsl:if>
	</font>
	</td>
	<td>
		<font size="-1">
		<xsl:if test="@category != '' and @category != ' '">
			<b><xsl:value-of select="@category"/></b>
			<br/>
		</xsl:if>

		<xsl:variable name="idt" select="@id"/>
		<b><xsl:value-of select="@title" disable-output-escaping="yes"/></b>
		<xsl:if test="@speaker != ''">
			<b>(<xsl:value-of select="@speaker"/></b>	
			<xsl:if test="@affiliation != ''">
				/<i><xsl:value-of select="@affiliation"/></i>
			</xsl:if>
			<b>)</b>
		</xsl:if>

		<br/>

		<xsl:if test="count(child::link) != 0">
			<xsl:for-each select="link">
				<xsl:if test="@type = 'minutes'">
				(<small>
				<img src="images/smallfiles.gif" alt="" height="11" width="9"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				<xsl:value-of select="@type"/>
				</a>
				</small>)
				</xsl:if>
			</xsl:for-each>
		</xsl:if>

		<xsl:choose>
		<xsl:when test="@location != ../@location or @room != ../@room">
			<xsl:if test="@location != '' and @location != ../@location">
				(<xsl:value-of select="@location"/><br/>)
			</xsl:if>
			<xsl:if test="@room != '0--' and @room != ../@room and @room != ''">
				(
				<xsl:value-of select="@room" disable-output-escaping="yes"/>
				)
			</xsl:if>
		</xsl:when>
		</xsl:choose>


	<xsl:if test="@comments != ''">
		<br/>
		<small>
		<xsl:value-of select="@comments" disable-output-escaping="yes"/>
		</small>
	</xsl:if>
	<br/><br/>
		</font>
	</td>
</tr>

</xsl:for-each>
</table>
<br/>
</xsl:for-each>	

<BR/><HR/>
CDS Agendas - CERN ETT-DH-CDS

</xsl:template>
</xsl:stylesheet>