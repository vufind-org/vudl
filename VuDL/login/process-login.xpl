<!--
Copyright (C) Villanova University 2011.

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
    xmlns:foxml="info:fedora/fedora-system:def/foxml#">
    
    <p:param type="input" name="instance" />
    <p:param type="output" name="data" />
    
    <!-- Build submission with supplied credentials -->
    <p:processor name="oxf:xslt">
        <p:input name="data" href="#instance"/>
        <p:input name="config" >

            <xforms:submission xsl:version="2.0"
                               method="get" 
                               action="http://localhost:8088/fedora/user"
                               >
                <xforms:header>
                    <xforms:name>Authorization</xforms:name>
                    <xforms:value>
                        <xsl:value-of select="/login/base64-encoded"/>
                    </xforms:value>
                </xforms:header>
            </xforms:submission>
        </p:input>
        <p:output name="data" id="get-submission"/>
    </p:processor>

    <!-- Instantiate empty request -->
    <p:processor name="oxf:request">
      <p:input name="config">
        <config/>
      </p:input>
      <p:output name="data" id="request"/>
    </p:processor>

    <!-- Process GET submission to Fedora's API-M -->
    <p:processor name="oxf:xforms-submission">
      <p:input name="submission" href="#get-submission"/>
      <p:input name="request" href="#request"/> 
      <p:output name="response" id="submission-response" ref="data"/>
    </p:processor>

    <!-- Handle GET API-M request -->
    <p:choose href="#submission-response">

        <p:when test="/user"> <!-- Successful Authentication -->

            <!-- Stuff user servlet response into the session variable -->
            <p:processor name="oxf:scope-serializer">
                <p:input name="config">
                    <config>
                        <key>user-credentials</key>
                        <scope>session</scope>
                        <session-scope>application</session-scope>
                    </config>
                </p:input>
                <p:input name="data" href="aggregate('authorization', #submission-response, #instance)"/>
            </p:processor>
            
            <!-- Forward the user to the home screen -->
            <p:processor name="oxf:redirect">  
                <p:input name="data">  
                    <redirect-url>  
                        <path-info>/VuDL/sitemap</path-info>
                    </redirect-url>  
                </p:input>   
            </p:processor>
            
        </p:when>

        <p:otherwise> <!-- Unsuccessful authenticated. Send back to the login screen -->
            <p:processor name="oxf:redirect">  
                <p:input name="data">  
                    <redirect-url>  
                        <path-info>/VuDL/login</path-info> 
                    </redirect-url>  
                </p:input>   
            </p:processor>
            
        </p:otherwise>
    </p:choose>
</p:config>
