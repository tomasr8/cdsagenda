<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="../date.xsl"/>
<xsl:output method="html"/>

<xsl:template match="agenda">

<div align="right">
<font face="Times">
<xsl:value-of select="@repno"/><br/>
<xsl:call-template name="prettydate">
	<xsl:with-param name="dat" select="@modified"/>
</xsl:call-template>
</font>
</div>

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
<xsl:value-of select="@title" disable-output-escaping="yes"/><br/>
</font>
[/modifyagenda]
<br/>

<font face="Times">
<xsl:value-of select="@location"/> - 
 <xsl:call-template name="prettydate">
	<xsl:with-param name="dat" select="/agenda/session[1]/@sday"/>
</xsl:call-template>
<xsl:if test="/agenda/session[1]/@stime != '00:00'">
	- <xsl:value-of select="/agenda/session[1]/@stime"/>
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

<xsl:for-each select="session">
<xsl:variable name="day" select="@sday"/>
<xsl:if test="count(preceding-sibling::session[position()=1 and @sday=$day]) = 0">
	<xsl:text disable-output-escaping="yes">
	&#060;!--NewPage--&#062;
	</xsl:text>
	<br/>
	<xsl:text disable-output-escaping="yes">
	&lt;table width="100%" border="0" cellpadding="1" cellspacing="1"&gt;
	</xsl:text>
	<tr bgcolor="black">
	        <td colspan="3" bgcolor="#dddddd">
	        <b><i>
		<font face="Times" size="+1">
		<xsl:call-template name="prettydate">
			<xsl:with-param name="dat" select="@sday"/>
		</xsl:call-template>
		</font>
	        </i></b>
	        </td>
	</tr>
	<tr><td colspan="3"> <br/></td></tr>
</xsl:if>

<tr><td colspan="3"> <br/></td></tr>
<xsl:variable name="ids" select="@id"/>
<xsl:if test="/agenda/@type != 'olist'">
<tr>
<td colspan="3" align="left">

<table width="100%" bgcolor="#f4f4f4" cellpadding="0" cellspacing="0" border="0">
<tr><td>
<font face="Times">
<b>
<u>
[modifysession ids="<xsl:value-of select="@id"/>"]
<xsl:value-of select="@title"/>
[/modifysession]
</u>
</b>
<small>
<xsl:if test="@stime != '00:00'">
   <xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
   (<xsl:value-of select="@stime"/>
   <xsl:if test="@etime != '00:00'">-&gt;<xsl:value-of select="@etime"/></xsl:if>)
</xsl:if>
<xsl:if test="@location != ''">
   (Location: <xsl:value-of select="@location"/>)
</xsl:if>
<xsl:if test="@room != '0--' and @room != ''">
   (Room: <xsl:value-of select="@room" disable-output-escaping="yes"/>)
</xsl:if>
<xsl:if test="@comments != ''">
	<br/>
	<xsl:value-of select="@comments" disable-output-escaping="yes"/>
</xsl:if>
</small>
</font>
</td>
<td align="right" valign="top" bgcolor="#f4f4f4">
   <small>
   <xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
   <xsl:if test="@chairperson != ''">
   Chair: <xsl:value-of select="@chairperson"/>
   </xsl:if>
   </small>
</td>
</tr>
</table>

</td>
</tr>
</xsl:if>

<xsl:for-each select="talk">
<xsl:variable name="idt" select="@id"/>
   
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
		<br/>
	</xsl:if>
	<font face="times"><b>
	<xsl:value-of select="@title" disable-output-escaping="yes"/> 
	</b></font>
	[/modifytalk]
	<xsl:if test="@repno != ''">
	 (<xsl:value-of select="@repno" disable-output-escaping="yes"/>)
	</xsl:if>
        <xsl:if test="@duration != '00:00:00'">
	 <small>(<xsl:call-template name="prettyduration"><xsl:with-param name="duration" select="@duration"/></xsl:call-template>)</small>
	</xsl:if>
	<xsl:if test="@location != '' and @location != ../@location">
	   <small> (<xsl:value-of select="@location"/>)</small>
	</xsl:if>
	<xsl:if test="@room != '0--' and @room != '' and @room != ../@room">
	   <small> (<xsl:value-of select="@room" disable-output-escaping="yes"/>)</small>
	</xsl:if>
        <xsl:if test="@comments != ''">
	<br/><font face="times"><xsl:value-of select="@comments" disable-output-escaping="yes"/></font>
	</xsl:if>
	</td>

	<td align="right" valign="top">
	<xsl:if test="@speaker != ''">
		<b><font face="times"><xsl:value-of select="@speaker"/></font></b>        
		<xsl:if test="@affiliation != ''">
			 / <i><font face="times"><xsl:value-of select="@affiliation"/></font>
			</i>
		</xsl:if>
	</xsl:if>
	</td>
</xsl:when>
<xsl:otherwise>
	<td colspan="2" align="center">
	[modifytalk ids="<xsl:value-of select="../@id"/>" idt="<xsl:value-of select="@id"/>"]
	<font face="Times">--- <xsl:value-of select="@title"/> ---</font>
	[/modifytalk]
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
	<font face="times"><xsl:value-of select="@title" disable-output-escaping="yes"/></font>
	[/modifysubtalk]
	<xsl:if test="@repno != ''">
	 (<xsl:value-of select="@repno" disable-output-escaping="yes"/>)
	</xsl:if>
        <xsl:if test="@duration != '00:00:00'">
	 <small>(<xsl:call-template name="prettyduration"><xsl:with-param name="duration" select="@duration"/></xsl:call-template>)</small>
	</xsl:if>
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
<xsl:variable name="day" select="@sday"/>
<xsl:if test="count(following-sibling::session[position()=1 and @sday=$day]) = 0">
	<xsl:text disable-output-escaping="yes">
	&lt;/table&gt;
	</xsl:text>
	<br/><br/>
</xsl:if>
</xsl:for-each>

</xsl:template>

</xsl:stylesheet>