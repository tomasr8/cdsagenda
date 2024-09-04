<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="agendaheader">
	<xsl:param name="agenda" select="0"/>
		<xsl:call-template name="header1">
			<xsl:with-param name="agenda" select="."/>
		</xsl:call-template>
		<br/>
		<xsl:call-template name="sessionlist">
			<xsl:with-param name="agenda" select="."/>
		</xsl:call-template>
</xsl:template>


<xsl:template name="agendaheaderseminars">
	<xsl:param name="agenda" select="0"/>
		<xsl:call-template name="header1">
			<xsl:with-param name="agenda" select="."/>
		</xsl:call-template>
		<br/>
		<xsl:call-template name="sessionlistseminars">
			<xsl:with-param name="agenda" select="."/>
		</xsl:call-template>
</xsl:template>




<xsl:template name="header1">
	<xsl:param name="agenda" select="0"/>
	<b>
	<xsl:value-of select="@title"/>
	</b>
</xsl:template>




<xsl:template name="sessionlist">
	<xsl:param name="agenda" select="0"/>
	<xsl:for-each select="session">
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
		<xsl:if test="substring-before(@title,' ') != 'Friday'">
		  - 
		</xsl:if>
	</xsl:for-each>
</xsl:template>



<xsl:template name="sessionlistseminars">
	<xsl:param name="agenda" select="0"/>
	<xsl:for-each select="session">
		<a href="#{@id}">
		<xsl:choose>
		<xsl:when test="@title != ''">
			<xsl:value-of select="substring-before(@title,' ')"/>
		</xsl:when>
		<xsl:otherwise>
			no title
		</xsl:otherwise>
		</xsl:choose>
		</a>
		<xsl:if test="substring-before(@title,' ') != 'Friday'">
		  - 
		</xsl:if>
	</xsl:for-each>
</xsl:template>



<xsl:template name="sessionheader">
	<xsl:param name="session" select="0"/>
	<small>
	<b>
	<xsl:value-of select="@title"/>
	</b>
	</small>
</xsl:template>



</xsl:stylesheet>