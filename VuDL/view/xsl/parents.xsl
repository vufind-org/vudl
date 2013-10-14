<?xml version="1.0" encoding="windows-1250"?>
<xsl:stylesheet version="1.0" 
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
                xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result" 
                xmlns:rel="info:fedora/fedora-system:def/relations-external#" 
                xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
    <xsl:param name="PID"/>
    <xsl:output method="xml" indent="yes" media-type="text/xml"/>
    <xsl:template match="/*">
        <sparql:parents pid="{$PID}">
            <xsl:for-each select="//sparql:child[@uri=concat('info:fedora/', $PID)]">
                <xsl:variable name="parentURI" select="../sparql:parent/@uri"/>
                <xsl:variable name="parentName" select="../sparql:parentTitle"/>
                <xsl:call-template name="parent">
                    <xsl:with-param name="parentURI" select="$parentURI"/>
                    <xsl:with-param name="parentName" select="$parentName"/>
                </xsl:call-template>
            </xsl:for-each>
        </sparql:parents>
    </xsl:template>
    <xsl:template name="parent">
        <xsl:param name="parentURI"/>
        <xsl:param name="parentName"/>
        <sparql:parent uri="{$parentURI}" PID="{substring-after($parentURI,'/')}" name="{$parentName}">
            <xsl:for-each select="//sparql:child[@uri=$parentURI]">
                <xsl:variable name="new_parentURI" select="../sparql:parent/@uri"/>
                <xsl:variable name="new_parentName" select="../sparql:parentTitle"/>
                <xsl:call-template name="parent">
                    <xsl:with-param name="parentURI" select="$new_parentURI"/>
                    <xsl:with-param name="parentName" select="$new_parentName"/>
                </xsl:call-template>
            </xsl:for-each>
        </sparql:parent>
    </xsl:template>
</xsl:stylesheet>