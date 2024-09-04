<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="prettyduration">
	<xsl:param name="duration" select="0"/>
	<xsl:if test="number(substring($duration,1,2)) != '00'">
		<xsl:value-of select="translate(substring($duration,1,2),'0','')"/>h</xsl:if><xsl:value-of select="substring($duration,4,2)"/>'
</xsl:template>

</xsl:stylesheet>