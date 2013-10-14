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
          xmlns:f="http://orbeon.org/oxf/xml/formatting" 
          xmlns:xhtml="http://www.w3.org/1999/xhtml"
          xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
          xmlns:oxf="http://www.orbeon.com/oxf/processors">

         <!-- Instantiate session/scope generator -->
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
          
          <p:choose href="#user-credentials">
          
              <p:when test="/authorization"></p:when> <!-- Success, do Nothing -->

              <p:otherwise> <!-- Not logged in. Redirect to the login page -->
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
