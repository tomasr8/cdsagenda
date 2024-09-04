<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="date.xsl"/>
<xsl:import href="standardheader.xsl"/>
<xsl:output method="html"/>

<!-- GLobal object: Agenda -->
<xsl:template match="agenda">

<xsl:call-template name="agendaheader">
	<xsl:with-param name="agenda" select="."/>
</xsl:call-template>

<xsl:for-each select="minutes">
	<ul>
	<li>
	<small>
	<xsl:value-of select="." disable-output-escaping="yes"/>
	</small>
	</li>
	</ul>
</xsl:for-each>

<xsl:for-each select="session">	
<xsl:variable name="ids" select="@id"/>
<br/><br/>

<xsl:call-template name="sessionheader">
	<xsl:with-param name="session" select="."/>
</xsl:call-template>

	<xsl:for-each select="minutes">
		<ul>
		<li>
		<small>
		<xsl:value-of select="." disable-output-escaping="yes"/>
		</small>
		</li>
		</ul>
	</xsl:for-each>


<xsl:for-each select="talk">
<xsl:variable name="idt" select="@id"/>

	<xsl:for-each select="minutes">
		<ul><ul>
		<li>
		<small>
		<xsl:value-of select="." disable-output-escaping="yes"/>
		</small>
		</li>
		</ul></ul>
	</xsl:for-each>

	<xsl:for-each select="subtalk">
	<xsl:variable name="idt" select="@id"/>
		<xsl:for-each select="minutes">
			<ul><ul><ul>
			<li>
			<small>
			<xsl:value-of select="." disable-output-escaping="yes"/>
			</small>
			</li>
			</ul></ul></ul>
		</xsl:for-each>
	</xsl:for-each>
</xsl:for-each>	

</xsl:for-each>	

</xsl:template>
</xsl:stylesheet>