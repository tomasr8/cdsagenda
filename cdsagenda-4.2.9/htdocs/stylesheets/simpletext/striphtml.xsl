<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="stripbr">
      <xsl:param name="text" select="0"/>
      <xsl:value-of select="$text"/>
</xsl:template>

</xsl:stylesheet>