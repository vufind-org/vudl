<?xml version="1.0" encoding="utf-8"?>
<!--
Copyright (C) Villanova University 2012.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2,
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
-->
<xsl:stylesheet version="1.0" 
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
                xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result" 
                xmlns:rel="info:fedora/fedora-system:def/relations-external#" 
                xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <xsl:param name="PID"/>
    
    <xsl:output method="html" indent="yes" media-type="text/html" omit-xml-declaration="yes"/>

    <xsl:template match="/*">

        <div>
            <xsl:for-each select="//sparql:child[@uri=concat('info:fedora/', $PID)]">
                <xsl:variable name="parentURI" select="../sparql:parent/@uri"/>
                <xsl:variable name="parentName" select="../sparql:parentTitle"/>
                <xsl:call-template name="parent">
                    <xsl:with-param name="parentURI" select="$parentURI"/>
                    <xsl:with-param name="parentName" select="$parentName"/>
                </xsl:call-template>
            </xsl:for-each>
        </div>
        
    </xsl:template>
    
    <xsl:template name="parent">
        <xsl:param name="parentURI"/>
        <xsl:param name="parentName"/>
        <div style="float:right;" >
            <div style="float:right;">
                <a href="/VuDL/object/{substring-after($parentURI, '/')}" title="{$parentName}">
                    <xsl:value-of select="substring($parentName, 1, 30)"/>
                </a>
                <span> -> </span>
            </div>
            <xsl:if test="//sparql:child[@uri=$parentURI]">
                <div style="float:left;">
                    <xsl:for-each select="//sparql:child[@uri=$parentURI]">
                        <xsl:variable name="new_parentURI" select="../sparql:parent/@uri"/>
                        <xsl:variable name="new_parentName" select="../sparql:parentTitle"/>
                        <xsl:call-template name="parent">
                            <xsl:with-param name="parentURI" select="$new_parentURI"/>
                            <xsl:with-param name="parentName" select="$new_parentName"/>
                        </xsl:call-template>
                    </xsl:for-each>
                </div>
            </xsl:if>
        </div>
        <xsl:if test="//sparql:child[@uri=$parentURI]/../following-sibling::*">
            <div style="clear:both;"/>
        </xsl:if>
    </xsl:template>
    
</xsl:stylesheet>