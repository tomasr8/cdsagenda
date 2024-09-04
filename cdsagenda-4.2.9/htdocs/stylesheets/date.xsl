<?xml version='1.0'?>
<xsl:stylesheet version='1.0' xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
      xmlns:date="http://www.jclark.com/xt/java/java.util.Date"
      xmlns:ddate="http://www.jclark.com/xt/java/DDate">

<xsl:template name="prettydate">
	<xsl:param name="dat" select="0"/>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='0'">Monday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='1'">Tuesday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='2'">Wednesday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='3'">Thursday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='4'">Friday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='5'">Saturday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='6'">Sunday</xsl:if>
	<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	<xsl:value-of select="substring($dat,9,2)"/>
	<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	<xsl:if test="substring($dat,6,2)='01'">January</xsl:if>
	<xsl:if test="substring($dat,6,2)='02'">February</xsl:if>
	<xsl:if test="substring($dat,6,2)='03'">March</xsl:if>
	<xsl:if test="substring($dat,6,2)='04'">April</xsl:if>
	<xsl:if test="substring($dat,6,2)='05'">May</xsl:if>
	<xsl:if test="substring($dat,6,2)='06'">June</xsl:if>
	<xsl:if test="substring($dat,6,2)='07'">July</xsl:if>
	<xsl:if test="substring($dat,6,2)='08'">August</xsl:if>
	<xsl:if test="substring($dat,6,2)='09'">September</xsl:if>
	<xsl:if test="substring($dat,6,2)='10'">October</xsl:if>
	<xsl:if test="substring($dat,6,2)='11'">November</xsl:if>
	<xsl:if test="substring($dat,6,2)='12'">December</xsl:if>
	<xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
	<xsl:value-of select="substring($dat,1,4)"/>
</xsl:template>

<xsl:template name="prettyduration">
	<xsl:param name="duration" select="0"/>
	<xsl:if test="number(substring($duration,1,2)) != '00'">
		<xsl:value-of select="translate(substring($duration,1,2),'0','')"/>h</xsl:if><xsl:value-of select="substring($duration,4,2)"/>'</xsl:template>

<xsl:template name="prettydatetext">
	<xsl:param name="dat" select="0"/>
	<xsl:value-of select="substring($dat,9,2)"/>
	<xsl:text disable-output-escaping="yes"> </xsl:text>
	<xsl:if test="substring($dat,6,2)='01'">January</xsl:if>
	<xsl:if test="substring($dat,6,2)='02'">February</xsl:if>
	<xsl:if test="substring($dat,6,2)='03'">March</xsl:if>
	<xsl:if test="substring($dat,6,2)='04'">April</xsl:if>
	<xsl:if test="substring($dat,6,2)='05'">May</xsl:if>
	<xsl:if test="substring($dat,6,2)='06'">June</xsl:if>
	<xsl:if test="substring($dat,6,2)='07'">July</xsl:if>
	<xsl:if test="substring($dat,6,2)='08'">August</xsl:if>
	<xsl:if test="substring($dat,6,2)='09'">September</xsl:if>
	<xsl:if test="substring($dat,6,2)='10'">October</xsl:if>
	<xsl:if test="substring($dat,6,2)='11'">November</xsl:if>
	<xsl:if test="substring($dat,6,2)='12'">December</xsl:if>
	<xsl:text disable-output-escaping="yes"> </xsl:text>
	<xsl:value-of select="substring($dat,1,4)"/>
</xsl:template>

<xsl:template name="getday">
	<xsl:param name="dat" select="0"/>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='0'">Monday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='1'">Tuesday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='2'">Wednesday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='3'">Thursday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='4'">Friday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='5'">Saturday</xsl:if>
	<xsl:if test="date:getDay(ddate:new((substring($dat,1,4)-1900),number(substring($dat,6,2))-1,number(substring($dat,9,2)-1)))='6'">Sunday</xsl:if>
</xsl:template>

</xsl:stylesheet>