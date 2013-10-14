<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" 
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" indent="yes" media-type="text/html" omit-xml-declaration="yes"/>

    <xsl:template match="/*">
        <xsl:variable name="src" select="document('input:instance')"/>
        <xsl:variable name="content" select="$src//content"/>
        <xsl:variable name="mime" select="$src//mime"/>

        <xsl:choose>
            <xsl:when test="$mime = 'image/jpeg' or $mime = 'image/png'">
                <img src="{$content}"/>
            </xsl:when>
            <xsl:when test="$mime = 'text/plain'">
                <pre>
                    <xsl:copy-of select="document($content)"/>
                </pre>
            </xsl:when>
            <xsl:when test="$mime = 'text/xml'">
                <xsl:copy-of select="document($content)"/>
            </xsl:when>
            <xsl:otherwise>
                <a href="{$content}">Download</a>
            </xsl:otherwise>
        </xsl:choose>
        
    </xsl:template>
    
</xsl:stylesheet>