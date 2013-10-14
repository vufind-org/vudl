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
    xmlns:foxml="info:fedora/fedora-system:def/foxml#">
    
    <p:param type="input" name="data" />
    <p:param type="input" name="PID" />
    <p:param type="output" name="data" />
    
    <!--  -->
    <p:processor name="oxf:xslt">
        <p:input name="config">
            <xsl:stylesheet version="2.0">
                <xsl:import href="oxf:/apps/VuDL/view/xsl/breadcrumb.xsl"/>
                <xsl:param name="PID" select="doc('input:PID')"/>
            </xsl:stylesheet>
        </p:input>

        <p:input name="data" href="#data"/>
        <p:input name="PID" href="#PID"/>
        <p:output name="data" id="format-parents" ref="data"/>
    </p:processor>

</p:config>
