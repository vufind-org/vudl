<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" 
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
                xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result" 
                xmlns:rel="info:fedora/fedora-system:def/relations-external#" 
                xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <xsl:param name="PID"/>
    <xsl:output method="html" indent="yes" media-type="text/xml" omit-xml-declaration="yes"/>

    <xsl:template match="/*">

        <children>
            <child uri="info:fedora/{$PID}">
                <xsl:for-each select="//sparql:parent[@uri=concat('info:fedora/', $PID)]">
                    <xsl:variable name="childURI" select="../sparql:child/@uri"/>
                    <xsl:variable name="childName" select="../sparql:childTitle"/>
                    <xsl:variable name="numParents" select="../sparql:k0"/>
                    
                    <xsl:call-template name="child">
                        <xsl:with-param name="childURI" select="$childURI"/>
                        <xsl:with-param name="childName" select="$childName"/>
                        <xsl:with-param name="numParents" select="$numParents"/>
                    </xsl:call-template>
    
                </xsl:for-each>
            </child>
        </children>
        
    </xsl:template>
    
    <xsl:template name="child">
        <xsl:param name="childURI"/>
        <xsl:param name="childName"/>
        <xsl:param name="numParents"/>
            <!-- <xsl:if test="//sparql:parent[@uri=$childURI]"> -->
                <child uri="{$childURI}" name="{$childName}" numParents="{$numParents}">
                    <xsl:for-each select="//sparql:parent[@uri=$childURI]">
                        <xsl:variable name="new_childURI" select="../sparql:child/@uri"/>
                        <xsl:variable name="new_childName" select="../sparql:childTitle"/>
                        <xsl:variable name="new_numParents" select="../sparql:k0"/>
                        <xsl:call-template name="child">
                            <xsl:with-param name="childURI" select="$new_childURI"/>
                            <xsl:with-param name="childName" select="$new_childName"/>
                            <xsl:with-param name="numParents" select="$new_numParents"/>
                        </xsl:call-template>
                    </xsl:for-each>
                </child>
            <!-- </xsl:if> -->

    </xsl:template>
    
</xsl:stylesheet>