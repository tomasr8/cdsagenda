<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- printablenopb.xsl: printable format with no page breaks -->


<xsl:import href="../date.xsl"/>
<xsl:output method="html"/>

<xsl:template match="agenda">

<div align="right">
<font face="Times">
<xsl:value-of select="@repno"/><br/>
<xsl:value-of select="@modified"/>
</font>

</div>

<br/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><br/>

<center>
<b>
<font size="+1" face="optima">
ORGANISATION EUROP<xsl:text disable-output-escaping="yes">&#38;Eacute;</xsl:text>ENNE POUR LA RECHERCHE NUCL<xsl:text disable-output-escaping="yes">&#38;Eacute;</xsl:text>AIRE<br/>
<font size="+4" face="optima">
CERN 
</font>
EUROPEAN ORGANIZATION FOR NUCLEAR RESEARCH
</font>
</b>
</center>

<center>
<hr width="100%"/>
</center>

<br/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text><br/>

<center>
<font size="+1" face="Times">
<u>
<xsl:value-of select="@category1" disable-output-escaping="yes"/><br/>
</u>
</font>
<br/>

[modifyagenda]
<font size="+1" face="Times">
<xsl:value-of select="@title"/><br/>
</font>
[/modifyagenda]
<br/>

<font face="Times">
<xsl:value-of select="@location"/> - 
 <xsl:call-template name="prettydate">
	<xsl:with-param name="dat" select="/agenda/session/talk[1]/@day"/>
</xsl:call-template>
<xsl:if test="/agenda/session/talk[1]/@stime != '00:00'">
	- <xsl:value-of select="/agenda/session/talk[1]/@stime"/>
</xsl:if>
</font>
<br/>
<xsl:if test="/agenda/session[1]/@room != '0--' and /agenda/session[1]/@room != ''">
	<br/><b><font face="Times"><xsl:value-of select="/agenda/session[1]/@room" disable-output-escaping="yes"/></font></b>
</xsl:if>
<br/><br/>

<b>
<font size="+1" face="Times">
AGENDA
</font>
</b>

</center>
<xsl:if test="@comments != ''">
	<br/><br/>
	<xsl:value-of select="@comments" disable-output-escaping="yes"/>
</xsl:if>

<br/><br/>

<xsl:text disable-output-escaping="yes">
&#060;!--NewPage--&#062;
</xsl:text>

<xsl:for-each select="session">
<table width="100%" border="0">
<xsl:variable name="ids" select="@id"/>
<xsl:if test="/agenda/@type != 'olist'">
<tr>
<td colspan="4" align="left"><br/>
<font face="Times">
<b>
[modifysession ids="<xsl:value-of select="@id"/>"]
<xsl:value-of select="@title"/>
[/modifysession]
</b>
<xsl:if test="@room != '' and @room != '0--'">
	(Room: <xsl:value-of select="@room" disable-output-escaping="yes"/>)
</xsl:if>
<hr width="100%"/>

<xsl:if test="@comments != ''">
	<xsl:value-of select="@comments" disable-output-escaping="yes"/>
	<hr width="100%"/>
</xsl:if>

</font>
</td>
</tr>
</xsl:if>

<xsl:for-each select="talk">
<xsl:variable name="idt" select="@id"/>
<xsl:if test="/agenda/@type != 'olist'">
	<xsl:variable name="day" select="@day"/>
	<xsl:if test="count(preceding-sibling::talk[position()=1 and @day=$day]) = 0">
	<tr>
	        <td colspan="4">
	        <b><i>
		<xsl:call-template name="prettydate">
			<xsl:with-param name="dat" select="@day"/>
		</xsl:call-template>
	        </i></b>
	        <br/><br/>
	        </td>
	</tr>
	</xsl:if>
</xsl:if>
   
<tr>
        <td align="center" valign="top" width="8%">
	<xsl:choose>
	<xsl:when test="/agenda/@type != 'olist'">
	        <b><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	        <xsl:if test="@stime != '00:00'">
	                <font face="times"><xsl:value-of select="@stime"/> </font> 
	        </xsl:if>
	        </b>
	</xsl:when>
	<xsl:otherwise>
		<font face="times"><xsl:number format="1"/>.</font>
	</xsl:otherwise>
	</xsl:choose>
        </td>

<xsl:choose>
<xsl:when test="@type=1">
	<td valign="top">
	[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
	<xsl:if test="@category != ''">
		<b><font face="times">
		<xsl:value-of select="@category"/>
		</font></b>
		<br/><br/>
	</xsl:if>
	<font face="times"><b>
	<xsl:value-of select="@title"/> 
	</b></font>
	[/modifytalk]
        <xsl:if test="@comments != ''">
	<br/><font face="times"><xsl:value-of select="@comments" disable-output-escaping="yes"/></font>
	</xsl:if>
	</td>

	<td align="right" valign="top">
	<xsl:if test="@speaker != ''">
		&#x0a0;&#x0a0;
		<b><font face="times"><xsl:value-of select="@speaker"/></font></b>        
		<xsl:if test="@affiliation != ''">
			 / <i><font face="times"><xsl:value-of select="@affiliation"/></font>
			</i>
		</xsl:if><br/>
	</xsl:if>
	</td>
</xsl:when>
<xsl:otherwise>
	<td colspan="3" align="center">
	<font face="Times">--- <xsl:value-of select="@title"/> ---</font>
	</td>
</xsl:otherwise>
</xsl:choose>
	

	
</tr>

<xsl:for-each select="subtalk">
<xsl:variable name="idt" select="@id"/>
<tr>
	<td>
	</td>
	<td>
	[modifysubtalk ids="<xsl:value-of select="../../@id"/>" idt="<xsl:value-of select="@id"/>"]
	<xsl:if test="@category != ''">
		<B>
		<xsl:value-of select="@category"/>
		</B>:    
	</xsl:if>
	<font face="times"><xsl:value-of select="@title" disable-output-escaping="yes"/></font><br/>
	[/modifysubtalk]
	</td>
	<td align="right" valign="top">
	<xsl:if test="@speaker != ''">
		&#x0a0;&#x0a0;
		<b><font face="times"><xsl:value-of select="@speaker"/></font></b>        
		<xsl:if test="@affiliation != ''">
			 / <i><font face="times"><xsl:value-of select="@affiliation"/></font>
			</i>
		</xsl:if>
	</xsl:if>
	</td>
</tr>
</xsl:for-each>
</xsl:for-each>
</table>
</xsl:for-each>
<br/>


</xsl:template>

<xsl:template match="session">
<xsl:value-of select="@title"/>
</xsl:template>

</xsl:stylesheet>