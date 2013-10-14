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
<p:config xmlns:p="http://www.orbeon.com/oxf/pipeline"
    xmlns:oxf="http://www.orbeon.com/oxf/processors"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xforms="http://www.w3.org/2002/xforms"
    xmlns:xxforms="http://orbeon.org/oxf/xml/xforms"
    xmlns:foxml="info:fedora/fedora-system:def/foxml#"
    xmlns:fedora-model="info:fedora/fedora-system:def/model#" 
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    >
    
    <p:param type="input" name="instance" />

    <p:param type="output" name="data" />

    <!-- Instantiate session/scope generator to load user login session info  -->
    <p:processor name="oxf:scope-generator">
        <p:input name="config">
            <config>
                <key>user-credentials</key>
                <scope>session</scope>
                <session-scope>application</session-scope>
            </config>
        </p:input>
        <p:output name="data" id="user-credentials"/>
    </p:processor>
    
    <!-- grab all avialble groups -->
    <!-- TODO: the xforms:model stuff isn't neccesary and is only used for testing -->
    <p:processor name="oxf:xslt">
        <p:input name="data" href="oxf:/apps/VuDL/conf/config.xml"/>
        <p:input name="config">
            <xsl:stylesheet version="2.0">
                <xsl:template match="/*">
                    <xsl:variable name="all-groups" select="//group"/>
                    <xforms:model id="AllGroups-model"
                                  xmlns:xforms="http://www.w3.org/2002/xforms"
                                  xmlns:xxforms="http://orbeon.org/oxf/xml/xforms"
                                  xmlns:ev="http://www.w3.org/2001/xml-events"
                                  xmlns:xs="http://www.w3.org/2001/XMLSchema"
                                  xmlns:fr="http://orbeon.org/oxf/xml/form-runner"
                                  xmlns:xi="http://www.w3.org/2001/XInclude"
                                  xmlns:foxml="info:fedora/fedora-system:def/foxml#"
                                  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                                  xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result"
                                  xmlns:fedora-model="info:fedora/fedora-system:def/model#" 
                                  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
                                  xmlns:rel="info:fedora/fedora-system:def/relations-external#"
                                  xmlns:dc="http://purl.org/dc/elements/1.1/"
                                  xmlns:management="http://www.fedora.info/definitions/1/0/management/">
                    
                        <xforms:instance id="all-groups">
                           <instance>
                              <xsl:copy-of select="$all-groups"/>
                           </instance>
                        </xforms:instance>
                    </xforms:model>
                </xsl:template>
            </xsl:stylesheet>
        </p:input>
        <p:output name="data" id="all-groups"/>
    </p:processor>
    
    <!-- grab users' group from config -->
    <!-- TODO: the xforms:model stuff isn't neccesary and is only used for testing -->
    <p:processor name="oxf:xslt">
        <p:input name="data" href="oxf:/apps/VuDL/conf/config.xml"/>
        <p:input name="user-credentials" href="#user-credentials"/>
        <p:input name="all-groups" href="#all-groups"/>
        
        <p:input name="config">
            <xsl:stylesheet version="2.0">
                <xsl:template match="/*">
                    
                    <xsl:variable name="user-credentials" select="doc('input:user-credentials')"/>
                    <xsl:variable name="all-groups" select="doc('input:all-groups')"/>
                    <xsl:variable name="group-access" select="$user-credentials//attribute[@name='fedoraRole']/value/string()"/>
                    
                    <xforms:model id="AccessModel-model"
                                  xmlns:xforms="http://www.w3.org/2002/xforms"
                                  xmlns:xxforms="http://orbeon.org/oxf/xml/xforms"
                                  xmlns:ev="http://www.w3.org/2001/xml-events"
                                  xmlns:xs="http://www.w3.org/2001/XMLSchema"
                                  xmlns:fr="http://orbeon.org/oxf/xml/form-runner"
                                  xmlns:xi="http://www.w3.org/2001/XInclude"
                                  xmlns:foxml="info:fedora/fedora-system:def/foxml#"
                                  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                                  xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result"
                                  xmlns:fedora-model="info:fedora/fedora-system:def/model#" 
                                  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
                                  xmlns:rel="info:fedora/fedora-system:def/relations-external#"
                                  xmlns:dc="http://purl.org/dc/elements/1.1/"
                                  xmlns:management="http://www.fedora.info/definitions/1/0/management/">
                    
                        <xforms:instance id="user-group">
                           <instance>
                              <xsl:copy-of select="$all-groups//group[@id=$group-access]"/>
                           </instance>
                        </xforms:instance>
                    </xforms:model>
                </xsl:template>
            </xsl:stylesheet>
        </p:input>
        <p:output name="data" id="user-group"/>
    </p:processor>
    
    <!-- create document of available view (search, sitemap, settings, etc) based on the users fedoraRole -->
    <p:for-each href="#user-group" select="//view" root="Views" id="ViewButtons-out">
        <p:processor name="oxf:xslt">
            <p:input name="data" href="current()"/>
            <p:input name="config">
                <xsl:stylesheet version="2.0">
                    <xsl:template match="/*">
                        <xsl:variable name="current" select="."/>
                            <views>
                                <view name="{./@name}">
                                    <xsl:variable name="view" select="document(concat('oxf:/apps/VuDL/view/buttons/', ./@name, '-button.xml'))"/>
                                    <xsl:copy-of select="$view"/>
                                </view>
                            </views>
                    </xsl:template>
                </xsl:stylesheet>
            </p:input>
            <p:output name="data" ref="ViewButtons-out"/>
        </p:processor>
    </p:for-each>
    
    <!-- grab currentPage and load content and model -->
    <!-- TODO: the xforms:model stuff isn't neccesary and is only used for testing -->
    <p:processor name="oxf:xslt">
        <p:input name="data" href="oxf:/apps/VuDL/conf/config.xml"/>
        <p:input name="PageModel" href="#instance"/>
        
        <p:input name="config">
            <xsl:stylesheet version="2.0">
                <xsl:template match="/*">
                    
                    <xsl:variable name="PageModel" select="doc('input:PageModel')"/>

                    
                    <xforms:model id="currentPageModel-model"
                                  xmlns:xforms="http://www.w3.org/2002/xforms"
                                  xmlns:xxforms="http://orbeon.org/oxf/xml/xforms"
                                  xmlns:ev="http://www.w3.org/2001/xml-events"
                                  xmlns:xs="http://www.w3.org/2001/XMLSchema"
                                  xmlns:fr="http://orbeon.org/oxf/xml/form-runner"
                                  xmlns:xi="http://www.w3.org/2001/XInclude"
                                  xmlns:foxml="info:fedora/fedora-system:def/foxml#"
                                  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                                  xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result"
                                  xmlns:fedora-model="info:fedora/fedora-system:def/model#" 
                                  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
                                  xmlns:rel="info:fedora/fedora-system:def/relations-external#"
                                  xmlns:dc="http://purl.org/dc/elements/1.1/"
                                  xmlns:management="http://www.fedora.info/definitions/1/0/management/">
                    
                        <xforms:instance id="currentPage-document">
                           <instance>
                              <xsl:variable name="currentPage" select="$PageModel//currentPage/string()"/>
                              <view>
                                  <xsl:copy-of select="document(concat('oxf:/apps/VuDL/view/views/', $currentPage, '-view.xml'))"/>
                              </view>
                              <model>
                                  <xsl:copy-of select="document(concat('oxf:/apps/VuDL/model/', $currentPage, '-model.xml'))"/>
                              </model>
                           </instance>
                        </xforms:instance>
                    </xforms:model>
                </xsl:template>
            </xsl:stylesheet>
        </p:input>
        <p:output name="data" id="currentPage"/>
    </p:processor>
    
    <!--  TODO: this is only needed for testing purposes. -->
    <p:processor name="oxf:xslt">
        <p:input name="data" href="oxf:/apps/VuDL/conf/config.xml"/>
        <p:input name="ViewButtons-out" href="#ViewButtons-out"/>
        
        <p:input name="config">
            <xsl:stylesheet version="2.0">
                <xsl:template match="/*">
                    
                    <xsl:variable name="ViewButtons-out" select="doc('input:ViewButtons-out')"/>
                    
                    <xforms:model id="ViewButtons-out-model"
                                  xmlns:xforms="http://www.w3.org/2002/xforms"
                                  xmlns:xxforms="http://orbeon.org/oxf/xml/xforms"
                                  xmlns:ev="http://www.w3.org/2001/xml-events"
                                  xmlns:xs="http://www.w3.org/2001/XMLSchema"
                                  xmlns:fr="http://orbeon.org/oxf/xml/form-runner"
                                  xmlns:xi="http://www.w3.org/2001/XInclude"
                                  xmlns:foxml="info:fedora/fedora-system:def/foxml#"
                                  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                                  xmlns:sparql="http://www.w3.org/2001/sw/DataAccess/rf1/result"
                                  xmlns:fedora-model="info:fedora/fedora-system:def/model#" 
                                  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
                                  xmlns:rel="info:fedora/fedora-system:def/relations-external#"
                                  xmlns:dc="http://purl.org/dc/elements/1.1/"
                                  xmlns:management="http://www.fedora.info/definitions/1/0/management/">
                    
                        <xforms:instance id="ViewTypes">
                           <instance>
                              <xsl:copy-of select="$ViewButtons-out"/>
                           </instance>
                        </xforms:instance>
                    </xforms:model>
                </xsl:template>
            </xsl:stylesheet>
        </p:input>
        <p:output name="data" id="ViewTypes"/>
    </p:processor>

    <p:processor name="oxf:xinclude">
        <p:input name="config" href="oxf:/apps/VuDL/view/view.xhtml"/>
        <p:input name="PageModel" href="#instance"/>
        
        <!-- TODO: We don't need these. They are for testing -->
        <p:input name="all-groups" href="#all-groups"/>
        <p:input name="user-group" href="#user-group"/>
        <p:input name="ViewTypes" href="#ViewTypes"/>
        <p:input name="currentPage" href="#currentPage"/>
        
        <!--  Load buttons -->
        <p:input name="home-button" href="#ViewButtons-out#xpointer(//view[@name='home']/*)"/>
        <p:input name="sitemap-button" href="#ViewButtons-out#xpointer(//view[@name='sitemap']/*)"/>
        <p:input name="search-button" href="#ViewButtons-out#xpointer(//view[@name='search']/*)"/>
        <p:input name="visualizations-button" href="#ViewButtons-out#xpointer(//view[@name='visualizations']/*)"/>
        <p:input name="settings-button" href="#ViewButtons-out#xpointer(//view[@name='settings']/*)"/>
        <p:input name="audit-button" href="#ViewButtons-out#xpointer(//view[@name='audit']/*)"/>
        <p:input name="users-button" href="#ViewButtons-out#xpointer(//view[@name='users']/*)"/>

        <!-- Load currentPage-view -->
        <p:input name="page-view" href="#currentPage#xpointer(//instance/view)"/>
        
        <!--  Load currentPage-model -->
        <p:input name="page-model"  href="#currentPage#xpointer(//instance/model)"/>
        
        <p:output name="data" id="final-view" ref="data"/> <!--  -->
    </p:processor>
    

    
    <p:processor name="oxf:pipeline">
        <p:input name="data" href="#final-view"/>
        <p:input name="config" href="/config/epilogue.xpl"/>
    </p:processor>
    
    
</p:config>
