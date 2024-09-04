<?xml version='1.0'?>
<xsl:stylesheet version='1.0' 
   xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="agendaheader">
	<xsl:param name="agenda" select="0"/>
<table width="100%" border="0" bgcolor="white" cellpadding="1" cellspacing="1">
<tr>
	<td valign="top" align="left">
		<xsl:call-template name="header1">
			<xsl:with-param name="agenda" select="."/>
		</xsl:call-template>
	</td>
	<td align="right" valign="top">
		<font size="-2" color="#000060">
		[<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>last update: 
				<xsl:call-template name="prettydate">
					<xsl:with-param name="dat" select="@modified"/>
				</xsl:call-template><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>]<br/>
		</font>
		<xsl:call-template name="sessionlist">
			<xsl:with-param name="agenda" select="."/>
		</xsl:call-template>
	</td>
</tr>
</table>
</xsl:template>





<xsl:template name="agendaheadernosession">
	<xsl:param name="agenda" select="0"/>
<table width="100%" border="0" bgcolor="white" cellpadding="1" cellspacing="1">
<tr>
	<td valign="top" align="left">
		<xsl:call-template name="header1">
			<xsl:with-param name="agenda" select="."/>
		</xsl:call-template>
	</td>
	<td align="right" valign="top">
		<font size="-2" color="#000060">
		[ last update: 
				<xsl:call-template name="prettydate">
					<xsl:with-param name="dat" select="@modified"/>
				</xsl:call-template> ]<br/><br/>
		</font>
	</td>
</tr>
</table>
</xsl:template>





<xsl:template name="header1">
	<xsl:param name="agenda" select="0"/>
		<table border="0" bgcolor="gray" cellspacing="1" cellpadding="1">
		<tr><td colspan="1">
			<table border="0" cellpadding="2" cellspacing="0" width="100%" class="headerselected" bgcolor="#000060">
			<tr>
				<td width="35">
					<img src="images/meeting.gif" width="32" height="32" alt="lecture"/>
				</td>
				<td class="headerselected" align="right">
					<b><strong>
					[modifyagenda]
					<font size="+2" face="arial" color="white">
					<xsl:value-of select="@title" disable-output-escaping="yes"/>
					</font>
					[/modifyagenda]
					</strong></b>
					<br/>
					<small>
					<xsl:value-of select="@repno" disable-output-escaping="yes"/>
					</small>
				</td>
			</tr>
			</table>
		</td></tr>
		<tr><td>

		<table border="0" bgcolor="#f0c060" cellpadding="2" cellspacing="0" width="100%" class="results">
		<tr>
			<td valign="top" align="right">
				<b><strong>
				Date/Time: 
				</strong></b>
			</td>
			<td><small>
				<xsl:choose>
				<xsl:when test="@stdate = @endate">
					<xsl:call-template name="prettydate">
						<xsl:with-param name="dat" select="@stdate"/>
					</xsl:call-template>
					<xsl:if test="@stime != '00:00'">
						from <xsl:value-of select="substring(@stime,0,6)"/>
					</xsl:if>
					<xsl:if test="@etime != '00:00'">
						to <xsl:value-of select="substring(@etime,0,6)"/>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					from 
					<xsl:call-template name="prettydate">
						<xsl:with-param name="dat" select="@stdate"/>
					</xsl:call-template>
					<xsl:if test="@stime != '00:00'">
						(<xsl:value-of select="substring(@stime,0,6)"/>)
					</xsl:if>
					to
					<xsl:call-template name="prettydate">
						<xsl:with-param name="dat" select="@endate"/>
					</xsl:call-template>
					<xsl:if test="@etime != '00:00'">
						(<xsl:value-of select="substring(@etime,0,6)"/>)
					</xsl:if>
				</xsl:otherwise>
				</xsl:choose>
			</small>
			</td>
		</tr>

		<xsl:if test="@location != ''">
		<tr>
			<td valign="top" align="right">
				<b><strong>
				Location:
				</strong></b>
			</td>
			<td><small>
			<xsl:value-of select="@location"/>
			</small></td>
		</tr>
		</xsl:if>

		<xsl:if test="@room != '0--' and @room != ''">
		<tr>
			<td valign="top" align="right">
				<b><strong>
				Room:
				</strong></b>
			</td>
			<td><small>
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			</small></td>
		</tr>
		</xsl:if>

		<xsl:choose>
		<xsl:when test="@chairperson != ''">
		<tr>
			<td valign="top" align="right">
				<b><strong>
				Chair:
				</strong></b>
			</td>
			<td><small>
			<xsl:choose>
			<xsl:when test="@chairpersonemail != ''">
				<a href="mailto:{@chairpersonemail}">
				<xsl:value-of select="@chairperson"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="@chairperson"/>
			</xsl:otherwise>
			</xsl:choose>
			</small></td>
		</tr>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="@chairpersonemail != ''">
			<tr>
			<td valign="top" align="right">
				<b><strong>
				Contact:
				</strong></b>
			</td>
			<td><small>
				<a href="mailto:{@chairpersonemail}">
				<xsl:value-of select="@chairpersonemail"/>
				</a>
			</small></td>
			</tr>
			</xsl:if>
		</xsl:otherwise>
		</xsl:choose>

		<xsl:if test="@comments != ''">
		<tr>
			<td valign="top" align="right">
				<b><strong>
				Description:
				</strong></b>
			</td>
			<td>
			<i>
			<small>
			<xsl:value-of select="@comments" disable-output-escaping="yes"/>
			</small>
			</i>
			</td>
		</tr>
		</xsl:if>

		<xsl:if test="count(child::link) != 0">
		<tr>
			<td valign="top" align="right">
				<b><strong>
				Material:
				</strong></b>
			</td>
			<td>
				<xsl:for-each select="link">
				<small>
				<img src="images/smallfiles.gif" alt="" width="9" height="11"/> <xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<xsl:if test="@type='minutes'"><xsl:text disable-output-escaping="yes">&#60;font size="+1"&#62;&#60;b&#62;</xsl:text></xsl:if>
				<a href="{@url}">
				 <xsl:value-of select="@type"/>
				</a>
				<xsl:if test="@type='minutes'"><xsl:text disable-output-escaping="yes">&#60;/b&#62;&#60;/font&#62;</xsl:text></xsl:if>
				</small><br/>
			</xsl:for-each>
			</td>
		</tr>
		</xsl:if>

		</table>
		</td></tr>
		</table>
</xsl:template>








<xsl:template name="sessionlist">
	<xsl:param name="agenda" select="0"/>

	<table border="0" bgcolor="gray" cellspacing="1" cellpadding="1">
	<tr><td>
	<table bgcolor="white" cellpadding="2" cellspacing="0" border="0" width="100%">

	<xsl:for-each select="session">
	<tr>
		<td valign="top" class="headerselected" bgcolor="#000060"><font size="-2">
		<xsl:choose>
		<xsl:when test="@sday=@eday">
			<xsl:call-template name="prettydate">
				<xsl:with-param name="dat" select="@sday"/>
			</xsl:call-template>
			<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
			<xsl:value-of select="@stime"/>
			<xsl:if test="@etime != '00:00'">-&gt;<xsl:value-of select="@etime"/></xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="prettydate">
				<xsl:with-param name="dat" select="@sday"/>
			</xsl:call-template>
			<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
			<xsl:value-of select="@stime"/>
			-&gt;
			<xsl:call-template name="prettydate">
				<xsl:with-param name="dat" select="@eday"/>
			</xsl:call-template>
			<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
			<xsl:value-of select="@etime"/>
		</xsl:otherwise>
		</xsl:choose>
		</font></td>
		
		<xsl:choose>
		<xsl:when test="count(preceding-sibling::session) mod 2 = 1">
			<xsl:text disable-output-escaping="yes">
			&#60;td valign="top" bgcolor="#E4E4E4"&#62;
			</xsl:text>
		</xsl:when>
		<xsl:otherwise>
			<xsl:text disable-output-escaping="yes">
			&#60;td valign="top" bgcolor="#F6F6F6"&#62;
			</xsl:text>
		</xsl:otherwise>
		</xsl:choose>
		<font size="-2">
		<a href="#{@id}">
		<xsl:choose>
		<xsl:when test="@title != ''">
			<xsl:value-of select="@title"/>
		</xsl:when>
		<xsl:otherwise>
			no title
		</xsl:otherwise>
		</xsl:choose>
			</a>
			<xsl:if test="@room != '0--' and @room != ''">
				<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				(<xsl:value-of select="@room" disable-output-escaping="yes"/>)
			</xsl:if>
			<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
		</font>
		<xsl:text disable-output-escaping="yes">&#60;/td&#62;</xsl:text>

		<xsl:choose>
		<xsl:when test="@chairperson != ''">
			<xsl:choose>
			<xsl:when test="count(preceding-sibling::session) mod 2 = 1">
				<xsl:text disable-output-escaping="yes">
			&#60;td valign="top" align="right" bgcolor="#E4E4E4"&#62;
			&#60;font size="-2"&#62;
			</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">
			&#60;td valign="top" align="right" bgcolor="#F6F6F6"&#62;
			&#60;font size="-2"&#62;
			</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
			<xsl:when test="@chairpersonemail != ''">
			<a href="mailto:{@chairpersonemail}">
			<xsl:value-of select="@chairperson"/>
			</a>
			</xsl:when>
			<xsl:otherwise>
			<xsl:value-of select="@chairperson"/>
			</xsl:otherwise>
			</xsl:choose>
			<xsl:text disable-output-escaping="yes">
			&#60;/font&#62;
			&#60;/td&#62;
			</xsl:text>	
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
			<xsl:when test="count(preceding-sibling::session) mod 2 = 1">
				<xsl:text disable-output-escaping="yes">
				&#60;td valign="top" align="right" bgcolor="#E4E4E4"&#62;
				</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">
				&#60;td valign="top" align="right" bgcolor="#F6F6F6"&#62;
				</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
			<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
			<xsl:text disable-output-escaping="yes">
			&#60;/td&#62;
			</xsl:text>
		</xsl:otherwise>
		</xsl:choose>
	</tr>
	</xsl:for-each>
	</table>
	</td></tr></table>
</xsl:template>


<xsl:template name="sessionheader">
	<xsl:param name="session" select="0"/>

<table width="100%" cellpadding="1" cellspacing="0" border="0">
<tr class="headerselected" bgcolor="#000060">
	<a name="{@id}"/>
	<td valign="top" class="headerselected" >
	[modifysession ids="<xsl:value-of select="@id"/>"]
	<font color="white">
	<b>
	<font size="+1" face="arial" color="white">
	<xsl:value-of select="@title"/>	
	</font>
	</b>
	</font>
	[/modifysession]
	<font size="-2">
		(<xsl:value-of select="@stime"/>
		<xsl:if test="@etime != '00:00'">-&gt;<xsl:value-of select="@etime"/></xsl:if>)
	</font>
	</td>
	<td valign="top" align="right">
	<xsl:choose>
	<xsl:when test="@comments != '' or @chairperson != '' or (@location != ../@location and @location != '') or (@room != '0--' and @room != '') or count(child::link) != 0">
		<table bgcolor="#f0c060" cellpadding="2" cellspacing="0" border="0" class="results" valign="top">

		<xsl:if test="@comments != ''">
			<tr>
				<td valign="top">
					<b><strong>
					Description:
					</strong></b>
				</td>
				<td>
					<i>
					<small>
					<xsl:value-of select="@comments" disable-output-escaping="yes"/><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
					</small>
					</i>
				</td>
			</tr>
		</xsl:if>

		<xsl:if test="@chairperson != ''">
		<tr>
			<td valign="top">
				<b><strong>
				Chair:
				</strong></b>
			</td>
			<td><small>
			<xsl:choose>
			<xsl:when test="@chairpersonemail != ''">
				<a href="mailto:{@chairpersonemail}">
				<xsl:value-of select="@chairperson"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="@chairperson"/>
			</xsl:otherwise>
			</xsl:choose>
			</small></td>
		</tr>
		</xsl:if>

		<xsl:if test="@location != ../@location and @location != ''">
		<tr>
			<td valign="top">
				<b><strong>
				Location:
				</strong></b>
			</td>
			<td><small>
			<xsl:value-of select="@location"/>
			</small></td>
		</tr>
		</xsl:if>

		<xsl:if test="@room != '0--' and @room != ''">
		<tr>
			<td valign="top">
				<b><strong>
				Room:
				</strong></b>
			</td>
			<td><small>
			<xsl:value-of select="@room" disable-output-escaping="yes"/>
			</small></td>
		</tr>
		</xsl:if>

		<xsl:if test="count(child::link) != 0">
		<tr>
			<td valign="top">
				<b><strong>
				Material:
				</strong></b>
			</td>
			<td><small>
			<xsl:for-each select="link">
				<img src="images/smallfiles.gif" alt="" width="9" height="11" valign="middle"/> <xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
				<a href="{@url}">
				 <xsl:value-of select="@type"/>
				</a>
				<br/>
			</xsl:for-each>
			</small></td>
		</tr>
		</xsl:if>

		<xsl:if test="@broadcasturl != ''">
		<tr>
			<td valign="top">
				<b><strong>
				Broadcast:
				</strong></b>
			</td>
			<td><small>
			<a href="{@broadcasturl}">
			<img src="images/camera.gif" alt="" border="0" width="33" height="24"/>
			</a>
			</small></td>
		</tr>
		</xsl:if>

		</table>
	</xsl:when>
	<xsl:otherwise>
	<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	</xsl:otherwise>
	</xsl:choose>
	</td>
</tr>
</table>
</xsl:template>



</xsl:stylesheet>