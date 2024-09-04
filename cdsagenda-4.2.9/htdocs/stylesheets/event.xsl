<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="date.xsl"/>
<xsl:output method="html"/>

<!-- GLobal object: Agenda -->
<xsl:template match="agenda">
<center>
<table border="0" bgcolor="white" cellpadding="1" cellspacing="1">
<tr>
	<td valign="top" align="left">
		<table border="0" bgcolor="gray" cellspacing="1" cellpadding="1">
		<tr><td colspan="1">
			<table border="0" cellpadding="2" cellspacing="0" width="100%" class="headerselected" bgcolor="#000060">
			<tr>
				<td width="35">
					<img src="images/event.gif" width="32" height="32" alt="event"/>
				</td>
				<td align="left">
					<font size="+1" color="#bbbbbb">EVENT</font>
				</td>
				<td align="right">
					<xsl:choose>
					<xsl:when test="@stdate = @endate">
						<font size="-2" color="white">
						<xsl:call-template name="prettydate">
							<xsl:with-param name="dat" select="@stdate"/>
						</xsl:call-template>
						<xsl:if test="@stime != '00:00'">
							from <xsl:value-of select="substring(@stime,0,6)"/>
						</xsl:if>
						<xsl:if test="@etime != '00:00'">
							to <xsl:value-of select="substring(@etime,0,6)"/>
						</xsl:if>
						</font>
					</xsl:when>
					<xsl:otherwise>
						<font size="-2" color="white">
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
						</font>
					</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
			<td>

			<table border="0" bgcolor="#f0c060" cellpadding="2" cellspacing="0" width="100%" class="results">

			<tr>
				<td align="right" valign="top">
					<b><strong>
					Name:
					</strong></b>
				</td>
				<td>
					[modifyagenda]
					<font face="arial" color="#333333">
					<xsl:value-of select="@title"/>
					</font>
					[/modifyagenda]
				</td>
			</tr>

			<xsl:if test="@location != ''">
			<tr>
				<td align="right" valign="top">
					<b><strong>
					Location:
					</strong></b>
				</td>
				<td>
					<font size="-1" face="arial" color="#333333">
					<xsl:value-of select="@location"/>
					<xsl:if test="@room != '0--'">
						<font size="-2"> (
						<xsl:value-of select="@room" disable-output-escaping="yes"/>
						)</font>
					</xsl:if>
					</font>
				</td>
			</tr>
			</xsl:if>

			<xsl:if test="@chairperson != ''">
			<tr>
				<td align="right" valign="top">
					<b><strong>
					Contact:
					</strong></b>
				</td>
				<td>
					<font size="-1" face="arial" color="#333333">
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
					</font>
				</td>
			</tr>
			</xsl:if>

			<xsl:if test="string-length(@comments) > 5">
			<tr>
				<td align="right" valign="top">
					<b><strong>
					Description:
					</strong></b>
				</td>
				<td>
					<font size="-1" face="arial" color="#333333">
					<xsl:value-of select="@comments" disable-output-escaping="yes"/>
					</font>
				</td>
			</tr>
			</xsl:if>

		</table>

		<xsl:if test="count(/agenda/session) != 0">
			<font color="white">
			<center>
			<br/>This agenda is not designed for this stylesheet.<br/>The "Event" stylesheet fits with an agenda containing no session nor talk.<br/>
			In the present case the use of this stylesheet causes a loss of information, please choose another stylesheet to have access to all the data.<br/><br/>
			</center>
			</font>
		</xsl:if>

		</td>
	</tr>

	<xsl:if test="count(./link) != 0">
	<tr>
		<td valign="top">
			<table border="0" cellpadding="2" cellspacing="0" width="100%" class="headerselected" bgcolor="#000060">
			<tr>
				<td align="right" valign="top">
					<font size="+1" color="#bbbbbb">
					<b>
					Material:
					</b>
					</font>
				</td>
				<td align="left">
					<xsl:for-each select="./link">
						<img src="images/smallfiles.gif" alt="material" width="11" height="14"/>
						<a href="{@url}">
						<font size="-1" face="arial" color="#bbbbbb">
						<xsl:value-of select="@type"/>
						</font>
						</a>
						<br/>
					</xsl:for-each>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</xsl:if>	

	</table>
	</td>
</tr>
</table>
</center>

</xsl:template>
</xsl:stylesheet>