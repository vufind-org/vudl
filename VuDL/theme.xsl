<!--
  Copyright (C) 2010 Orbeon, Inc.

  This program is free software; you can redistribute it and/or modify it under the terms of the
  GNU Lesser General Public License as published by the Free Software Foundation; either version
  2.1 of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  See the GNU Lesser General Public License for more details.

  The full text of the license is available at http://www.gnu.org/copyleft/lesser.html
  -->
<!--
    This is a very simple theme that shows you how to create a common layout for all your pages. You can modify it at
    will or, even better, copy it as theme.xsl under your application folder where it will be picked up. For example,
    if your app is my-app: resources/my-app/theme.xsl.
-->
<xsl:stylesheet version="2.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:version="java:org.orbeon.oxf.common.Version">

    <!-- Orbeon Forms version -->
    <xsl:variable name="orbeon-forms-version" select="version:getVersionString()" as="xs:string"/>

    <xsl:template match="xhtml:head">
        <xsl:copy>
            <xsl:call-template name="head"/>
        </xsl:copy>
    </xsl:template>
    
    <xsl:template match="xhtml:body">
        <xsl:copy>
            <xsl:call-template name="body"/>
        </xsl:copy>
    </xsl:template>
    
    <xsl:template name="head">
        <xsl:apply-templates select="@*"/>

        <xhtml:script src="/apps/VuDL/js/raphael.js" type="text/javascript"></xhtml:script>
        
        <xhtml:link rel="stylesheet" href="/fr/style/bootstrap/css/bootstrap.css" type="text/css"/>
        <xhtml:link rel="stylesheet" href="/fr/style/bootstrap/css/bootstrap-responsive.css" type="text/css"/>
        
        <!-- Handle head elements except scripts -->
        <xsl:apply-templates select="xhtml:meta | xhtml:link | xhtml:style"/>
        <!-- -->
        
        
        <!-- Title -->
        <xhtml:title>
            <xsl:apply-templates select="xhtml:title/@*"/>
            <xsl:choose>
                <xsl:when test="xhtml:title != ''">
                    <xsl:value-of select="xhtml:title"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="(/xhtml:html/xhtml:body/xhtml:h1)[1]"/>
                </xsl:otherwise>
            </xsl:choose>
        </xhtml:title>
        <!-- Orbeon Forms version -->
        <xhtml:meta name="generator" content="{$orbeon-forms-version}"/>
        <!-- Handle head scripts if present -->
        <xsl:apply-templates select="xhtml:script"/>
    </xsl:template>
    
    <xsl:template name="body">
            <xhtml:div class="container">

                <xhtml:div id="content">

                    <xsl:apply-templates select="/xhtml:html/xhtml:body/@*"/>

                    <xsl:apply-templates select="/xhtml:html/xhtml:body/node()"/>
                </xhtml:div>
                
                <xhtml:div id="endbar"/>
                
            </xhtml:div>
            
            
            <!-- Le javascript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->
            
            
            <!-- 
            
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-transition.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-alert.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-modal.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-dropdown.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-scrollspy.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-tab.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-tooltip.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-popover.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-button.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-collapse.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-carousel.js"></xhtml:script>
            <xhtml:script src="/fr/style/bootstrap-src/js/bootstrap-typeahead.js"></xhtml:script>
            -->
    </xsl:template>
    
    
    <!-- Simply copy everything that's not matched -->
    <xsl:template match="@*|node()" priority="-2">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()"/>
        </xsl:copy>
        
    </xsl:template>

</xsl:stylesheet>
