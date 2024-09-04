<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="../date.xsl"/>

<xsl:output method="text" version="1.0" encoding="UTF-8" indent="no"/>

<xsl:template match="agenda">

<xsl:variable name="talkday" select="@title"/>
<xsl:variable name="sessionday" select="@stdate"/>
<xsl:variable name="thisroom" select="@room"/>


	<xsl:value-of select="@title"/><xsl:text>  </xsl:text>(<xsl:call-template name="prettydatetext"><xsl:with-param name="dat" select="@stdate"/></xsl:call-template><xsl:if test="@stdate != @endate"><xsl:text> to </xsl:text><xsl:call-template name="prettydatetext"><xsl:with-param name="dat" select="@endate"/></xsl:call-template></xsl:if>) <xsl:if test="@location != ''">(<xsl:value-of select="@location"/>: <xsl:if test="@room != '0--' and @room != ''"><xsl:choose><xsl:when test="contains(@room,'classinfo')"><xsl:value-of select="substring-before(substring-after(@room,'classinfo&gt;'),'&lt;/a&gt;')" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="@room" disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:if>)</xsl:if>
<xsl:if test="@comments != ''"><xsl:text>
__________________________________________________________
</xsl:text><xsl:value-of select="@comments"/></xsl:if>
__________________________________________________________
<xsl:for-each select="session">
<xsl:text>

</xsl:text>
<xsl:if test="@sday != $talkday and @sday != $sessionday">
	<xsl:call-template name="prettydatetext"><xsl:with-param name="dat" select="@sday"/></xsl:call-template>
__________________________________________________________

</xsl:if>

<xsl:variable name="ids" select="@id"/>
<xsl:variable name="sessionday" select="@sday"/>
<xsl:variable name="sessionroom" select="@room"/>

<xsl:if test="@title != ''">
<xsl:value-of select="@title"/><xsl:text>  </xsl:text>
<xsl:if test="@room != '' and @room != '0--' and @room != $thisroom">(Room: <xsl:choose><xsl:when test="contains(@room,'classinfo')"><xsl:value-of select="substring-before(substring-after(@room,'classinfo&gt;'),'&lt;/a&gt;')" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="@room" disable-output-escaping="yes"/></xsl:otherwise></xsl:choose>)</xsl:if>
<xsl:if test="@comments != ''"><xsl:text>
----------------------------------------
</xsl:text><xsl:value-of select="@comments"/>
</xsl:if>
----------------------------------------
</xsl:if>
<xsl:for-each select="talk">
<xsl:variable name="idt" select="@id"/>
<xsl:if test="/agenda/@type != 'olist'">
	<xsl:variable name="talkday" select="@day"/>
	<xsl:if test="count(preceding-sibling::talk[position()=1 and @day=$talkday]) = 0 and @day != $sessionday"><xsl:text>
</xsl:text>
		<xsl:call-template name="prettydatetext"><xsl:with-param name="dat" select="@day"/></xsl:call-template>
_________________________

	</xsl:if>
</xsl:if>
<xsl:text>
</xsl:text>
	<xsl:choose>
	<xsl:when test="/agenda/@type != 'olist'">
	        <xsl:if test="@stime != '00:00'">
	                <xsl:value-of select="@stime"/><xsl:text>   </xsl:text>
	        </xsl:if>
	</xsl:when>
	<xsl:otherwise><xsl:text>
</xsl:text>
		<xsl:number format="1"/>.<xsl:text>  </xsl:text></xsl:otherwise>
	</xsl:choose>

<xsl:choose>
<xsl:when test="@type=1">
	<xsl:if test="@category != ''">
	<xsl:value-of select="@category"/><xsl:text>
        </xsl:text>
	</xsl:if>

	<xsl:value-of select="@title"/>
	<xsl:if test="@speaker != ''">
		(<xsl:value-of select="@speaker"/><xsl:if test="@affiliation != ''"> / <xsl:value-of select="@affiliation"/></xsl:if>)</xsl:if>
	<xsl:if test="@room != '' and @room != '0--' and @room != $sessionroom">   (Room: <xsl:choose><xsl:when test="contains(@room,'classinfo')"><xsl:value-of select="substring-before(substring-after(@room,'classinfo&gt;'),'&lt;/a&gt;')" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="@room" disable-output-escaping="yes"/></xsl:otherwise></xsl:choose>)</xsl:if><xsl:text>
</xsl:text>
        <xsl:if test="@comments != ''"><xsl:value-of select="@comments"/><xsl:text>
</xsl:text>
	</xsl:if>

</xsl:when>
<xsl:otherwise>--- <xsl:value-of select="@title"/> ---
</xsl:otherwise>
</xsl:choose>
	

<xsl:for-each select="subtalk">
<xsl:variable name="idt" select="@id"/><xsl:text>
        o </xsl:text>
	<xsl:value-of select="@title" disable-output-escaping="yes"/>
	<xsl:if test="@speaker != ''">
		(<xsl:value-of select="@speaker"/>      
		<xsl:if test="@affiliation != ''"> / <xsl:value-of select="@affiliation"/>)
		</xsl:if>
	</xsl:if>
</xsl:for-each>
</xsl:for-each>
</xsl:for-each>

</xsl:template>


</xsl:stylesheet>