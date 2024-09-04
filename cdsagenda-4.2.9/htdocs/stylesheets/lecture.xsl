<?xml version='1.0'?>
<xsl:stylesheet version='1.0' 
   xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
   xmlns:string="http://www.jclark.com/xt/java/java.lang.String">

<xsl:include href="date.xsl"/>
<xsl:output method="html"/>

<!-- GLobal object: Agenda -->
<xsl:template match="agenda">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="298" align="center" valign="top">      <p><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text></p>
      <table width="90%" border="0" cellpadding="0" cellspacing="0" class="headerabstract">

        <tr>
          <td colspan="3"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="headerselected2">
            <tr>
              <td width="14%"><img src="images/conference.gif" width="113" height="69"/></td>
              <td width="57%"><span class="titles"><xsl:text disable-output-escaping="yes">&#38;quot;</xsl:text>[modifyagenda]<xsl:value-of select="@title" disable-output-escaping="yes"/>[/modifyagenda]<xsl:text disable-output-escaping="yes">&#38;quot;</xsl:text></span><br/>
                        <span class="author">by <xsl:value-of select="@chairperson"/></span></td>

              <td width="9%" class="settings"><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text></td>
              <td width="20%" nowrap="1" class="settings">Lecture<br/>
                <span class="bold">
			<xsl:call-template name="prettydate">
				<xsl:with-param name="dat" select="@stdate"/>
			</xsl:call-template>
		</span><br/>

		<xsl:if test="@stime != '00:00'">
			from <span class="bold"><xsl:value-of select="substring(@stime,0,6)"/></span>
		</xsl:if>
		<xsl:if test="@etime != '00:00'">
			to <span class="bold"><xsl:value-of select="substring(@etime,0,6)"/></span>
		</xsl:if>

		<xsl:if test="@location != ''">
			<br/>at 
			<xsl:value-of select="@location"/>
			<xsl:if test="@room != '0--' and @room != 'Select:'">
				 (<xsl:choose>
				<xsl:when test="contains(@room,'classinfo')">
					<xsl:value-of select="concat(substring-before(@room,'classinfo'),'class=normal',substring-after(@room,'classinfo'))" disable-output-escaping="yes"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="@room" disable-output-escaping="yes"/>
				</xsl:otherwise>
				</xsl:choose>)
			</xsl:if>
		</xsl:if>
 </td>

            </tr>
          </table></td>
        </tr>
        <tr>
          <td valign="top" nowrap="1" class="abstract"><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text></td>
          <td valign="top" nowrap="1" class="abstract"><strong>Abstract :</strong></td>
          <td valign="top" width="82%" class="abstract">
		<xsl:value-of select="@comments" disable-output-escaping="yes"/>
	  </td>

        </tr>
        <tr class="headerselected2">
          <td width="6%" height="33" valign="top" nowrap="1" class="headerselectedimg"><img src="images/paperclip.gif" width="56" height="32"/></td>
          <td valign="top" nowrap="1" class="headerselected3"><strong>Material
            :</strong></td>
          <td valign="top" nowrap="1" class="headerselected3"><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
		<xsl:for-each select="/agenda/link">
			<xsl:if test="@type!='part1' and @type!='part2' and @type!='part3' and @type!='part4' and @type!='part5' and @type!='part6' and @type!='part7' and @type!='part8' and @type!='part9' and @type!='part10'">
				<a href="{@url}" class="material">
				<xsl:value-of select="@type"/>
				</a>; 
			</xsl:if>
		</xsl:for-each>
		<div align="right">
		<xsl:for-each select="/agenda/link">
			<xsl:if test="@type='part1' or @type='part2' or @type='part3' or @type='part4' or @type='part5' or @type='part6' or @type='part7' or @type='part8' or @type='part9' or @type='part10'">
				<a href="{@url}" class="material">
				<img src="images/{@type}.gif" class="parts"/>
				</a><xsl:text disable-output-escaping="yes">&#38;nbsp;</xsl:text>
			</xsl:if>
		</xsl:for-each>
		</div>
		
	  </td>
        </tr>

      </table>
    <div align="center"></div></td>
  </tr>
</table>

</xsl:template>
</xsl:stylesheet>