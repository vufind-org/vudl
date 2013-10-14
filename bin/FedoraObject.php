<?php
/*
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
*/

require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Stream.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Http/Client.php';

class FedoraObject {

    var $defaultApiBase = 'http://localhost:8088/fedora';
    
    var $apiBase;
    
    var $client;
    
    var $PID;
    var $parentPID;
    var $title;
    var $datastreams;
    var $methods;
    var $modelType;
    var $namespace;
    
    var $PIDlogger;
    
    var $structmap;
    var $structmap_start = "<METS:structMap xmlns:METS='http://www.loc.gov/METS/' TYPE='physical'/>";

    var $logger;
    
    function FedoraObject($logger) {
        
        $this->logger = $logger;
        
        $this->client = new Zend_Http_Client();
        $this->client->setAuth('fedoraAdmin', 'fedoraAdmin', Zend_Http_Client::AUTH_BASIC);
        $this->client->setConfig(array('maxredirects' => 0,
                                       'timeout'      => 240));
        $this->apiBase = $this->defaultApiBase;
    }
    

    function setApiBase($base) {
        $this->apiBase = $base;
    }

    
    function listCollectionIngest() {
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                   'info:fedora/fedora-system:def/model#hasModel', 
                                                   'info:fedora/vudl-system:ListCollection', 
                                                   'false', 
                                                   '');
    }
    
    function resourceCollectionIngest($rawResource, $licenseStr, $agents, $processMD) {
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                   'info:fedora/fedora-system:def/model#hasModel', 
                                                   'info:fedora/vudl-system:ResourceCollection', 
                                                   'false', 
                                                   '');
        if (strlen($rawResource)) {
            $xml = simplexml_load_string($rawResource);
            $xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
            
            $legacyIdentifier = $xml->xpath('//dc:identifier[1]');
            
            $addDatastream = $this->addDatastream('LEGACY-METS', 
                                                 'M', 
                                                 '', 
                                                 '', 
                                                 'Legacy METS file from exist-db', 
                                                 'false', 
                                                 'A', 
                                                 '', 
                                                 'DISABLED', 
                                                 'none', 
                                                 'text/xml', 
                                                 'Initial Ingest addDatastream - LEGACY-METS',
                                                 $rawResource);
            
            $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'http://digital.library.villanova.edu/rdf/relations#hasLegacyURL', 
                                                 (string)$legacyIdentifier[0][0], 
                                                 'true', 
                                                 '');
                                                 
        }
        
        if (strlen($licenseStr)) {
            $addDatastream = $this->addDatastream('LICENSE', 
                                                 'X', 
                                                 '', 
                                                 '', 
                                                 'License Information for this Resource Object', 
                                                 'false', 
                                                 'A', 
                                                 '', 
                                                 'DISABLED', 
                                                 'none', 
                                                 'text/xml', 
                                                 'Initial Ingest addDatastream - LICENSE',
                                                 $licenseStr);
        }
        
        if (strlen($agents)) {
            $addDatastream = $this->addDatastream('AGENTS', 
                                                 'X', 
                                                 '', 
                                                 '', 
                                                 'METS:metsHdr AGENT info', 
                                                 'false', 
                                                 'A', 
                                                 '', 
                                                 'DISABLED', 
                                                 'none', 
                                                 'text/xml', 
                                                 'Initial Ingest addDatastream - AGENTS',
                                                 $agents);
        }
        
        if (strlen($processMD)) {
            $addDatastream = $this->addDatastream('PROCESS-MD', 
                                                 'M', 
                                                 '', 
                                                 '', 
                                                 'DIGIPROVMD:DIGIPROVMD info', 
                                                 'false', 
                                                 'A', 
                                                 '', 
                                                 'DISABLED', 
                                                 'none', 
                                                 'text/xml', 
                                                 'Initial Ingest addDatastream - PROCESS-METADATA',
                                                 $processMD);
        }
        
    }
    
    function folderCollectionIngest() {
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                   'info:fedora/fedora-system:def/model#hasModel', 
                                                   'info:fedora/vudl-system:FolderCollection', 
                                                   'false', 
                                                   '');
    }

    function pdfIngest() {
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                   'info:fedora/fedora-system:def/model#hasModel', 
                                                   'info:fedora/vudl-system:PDFData', 
                                                   'false', 
                                                   '');
    }
    
    function imageIngest() {
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                   'info:fedora/fedora-system:def/model#hasModel', 
                                                   'info:fedora/vudl-system:ImageData', 
                                                   'false', 
                                                   '');
    }
    
    function dataIngest() {
        //
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'info:fedora/fedora-system:def/model#hasModel', 
                                                 'info:fedora/vudl-system:DataModel', 
                                                 'false',
                                                 '');


    }
    
    function ImageDataIngest() {
        //
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'info:fedora/fedora-system:def/model#hasModel', 
                                                 'info:fedora/vudl-system:ImageData', 
                                                 'false',
                                                 '');
    }
    function PDFDataIngest() {
        //
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'info:fedora/fedora-system:def/model#hasModel', 
                                                 'info:fedora/vudl-system:PDFData', 
                                                 'false',
                                                 '');
    }
    
    function dataTypeIngest($type) {
        //
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'info:fedora/fedora-system:def/model#hasModel', 
                                                 'info:fedora/vudl-system:' . $type, 
                                                 'false',
                                                 '');
    }
    
    function addMemberToStructmap ($childPID) {
        $div = $this->structmap->addChild('METS:div', null, "http://www.loc.gov/METS/");

        $divs = $this->structmap->xpath('//METS:div');

        $div->addAttribute('ORDER', count($divs));
        $fptr = $div->addChild('METS:fptr', null, "http://www.loc.gov/METS/");
        $fptr->addAttribute('FILEID', $childPID);
        
    }
    
    function addStructmapDatastream() {
        $addDatastream = $this->addDatastream('STRUCTMAP', 
                                             'X', 
                                             '', 
                                             '', 
                                             'Ordered list of Members', 
                                             'false', 
                                             'A', 
                                             '', 
                                             'DISABLED', 
                                             'none', 
                                             'text/xml', 
                                             'Initial Ingest addDatastream - STRUCTMAP',
                                             $this->structmap->asXML());
    }
    
    function collectionIngest() {
        // Instatiate structmap
        $this->structmap = simplexml_load_string($this->structmap_start);
        $this->structmap->registerXPathNamespace("METS", "http://www.loc.gov/METS/");
        
        //
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'info:fedora/fedora-system:def/model#hasModel', 
                                                 'info:fedora/vudl-system:CollectionModel', 
                                                 'false',
                                                 '');
        //                          
        $addDatastream = $this->addDatastream('MEMBER-QUERY', 
                                             'E', 
                                             'http://local.fedora.server/fedora/objects/' . $this->PID . '/methods/vudl-system:CollectionModelService/generateMemberQuery', 
                                             '', 
                                             'Query to list members', 
                                             'false', 
                                             'A', 
                                             '', 
                                             'DISABLED', 
                                             'none', 
                                             'text/plain', 
                                             'Initial Ingest addDatastream - MEMBER-QUERY',
                                             null);
        
        $addDatastream = $this->addDatastream('MEMBER-LIST-RAW', 
                                             'E', 
                                             'http://local.fedora.server/fedora/objects/' . $this->PID . '/methods/vudl-system:CollectionModelService/executeMemberQuery', 
                                             '', 
                                             'Unformatted list of Members', 
                                             'false', 
                                             'A', 
                                             '', 
                                             'DISABLED', 
                                             'none', 
                                             'text/xml', 
                                             'Initial Ingest addDatastream - MEMBER-LIST-RAW',
                                             null);
    }
    
    function coreIngest($objState) {
        //
        $ingest = $this->ingest($this->title, 
                               'info:fedora/fedora-system:FOXML-1.1', 
                               'UTF-8', 
                               $this->namespace, 
                               'diglibEditor', 
                               $this->title . ' - ingest', 
                               'false');
        
        // Change State
        $modify = $this->modifyObject('', 
                                     '', 
                                     $objState, 
                                     'Set initial state', 
                                     '');
        
        //
        
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'info:fedora/fedora-system:def/model#hasModel', 
                                                 'info:fedora/vudl-system:CoreModel', 
                                                 'false', 
          
        //This is only necessary if using oaiprovider.war                                       '');
        /*
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'http://www.openarchives.org/OAI/2.0/itemID', 
                                                 'oai:digital.library.villanova.edu:' . $this->PID, 
                                                 'true', 
                                                 '');
        */
        $addRelationship = $this->addRelationship('info:fedora/' . $this->PID, 
                                                 'info:fedora/fedora-system:def/relations-external#isMemberOf', 
                                                 'info:fedora/' . $this->parentPID, 
                                                 'false', 
                                                 '');                                                                              
        // ---------------------------------------- Add Datasatreams

        $addDatastream = $this->addDatastream('PARENT-QUERY', 
                                             'E', 
                                             'http://local.fedora.server/fedora/objects/' . $this->PID . '/methods/vudl-system:CoreModelService/generateParentQuery', 
                                             '', 
                                             'Query to list Parents', 
                                             'false', 
                                             'A', 
                                             '', 
                                             'DISABLED', 
                                             'none', 
                                             'text/plain', 
                                             'Initial Ingest addDatastream - PARENT-QUERY',
                                             null);
        
        $addDatastream = $this->addDatastream('PARENT-LIST-RAW', 
                                             'E', 
                                             'http://local.fedora.server/fedora/objects/' . $this->PID . '/methods/vudl-system:CoreModelService/executeParentQuery', 
                                             '', 
                                             'Unformatted list of Parents', 
                                             'false', 
                                             'A', 
                                             '', 
                                             'DISABLED', 
                                             'none', 
                                             'text/xml', 
                                             'Initial Ingest addDatastream - PARENT-LIST-RAW',
                                             null);
        
        $addDatastream = $this->addDatastream('PARENT-LIST', 
                                             'E', 
                                             'http://local.fedora.server/fedora/objects/' . $this->PID . '/methods/vudl-system:CoreModelService/formatParentQueryResult', 
                                             '', 
                                             'XML list of parents grouped by multiple paths', 
                                             'false', 
                                             'A', 
                                             '', 
                                             'DISABLED', 
                                             'none', 
                                             'text/xml', 
                                             'Initial Ingest addDatastream - PARENT-LIST',
                                             null);

    }
    
    function getNextPID() {
        $this->client->setMethod(Zend_Http_Client::POST);
        $this->client->setUri($this->apiBase . '/objects/nextPID');
        //$this->client->setParameterGet('numPIDs', '1');
        $this->client->setParameterGet('namespace', $this->namespace);
        $this->client->setParameterGet('format', 'xml');

        $response = $this->client->setRawData(null)->request();
        
        $this->client->resetParameters();
        
        $xml = simplexml_load_string($response->getBody());
        $xml->registerXPathNamespace('management', 'http://www.fedora.info/definitions/1/0/management/');

        $pid = $xml->xpath('//management:pid[1]');
        return (string)$pid[0][0];
    }
    
    function setPID($PID) {
        $this->PID = $PID;
    }
    
    function ingest($label, $format, $encoding, $namespace, $ownerId, $logMessage, $ignoreMime) {
        $this->client->setMethod(Zend_Http_Client::POST);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID);
        $this->client->setParameterGet('label', $label);
        $this->client->setParameterGet('format', $format);
        $this->client->setParameterGet('encoding', $encoding);
        $this->client->setParameterGet('namespace', $namespace);
        $this->client->setParameterGet('ownerId', $ownerId);
        $this->client->setParameterGet('logMessage', $logMessage);
        $this->client->setParameterGet('ignoreMime', $ignoreMime);
        
        $response = $this->client->setRawData(null)->request();
        
        $this->client->resetParameters();
        
        if (strlen($this->PIDlogger)) {
            $current = file_get_contents($this->PIDlogger);
            file_put_contents($this->PIDlogger, $current . $this->PID . "\n");
        }
        
        return $response;
    }
    
    function export($format, $context, $encoding) {
        $this->client->setMethod(Zend_Http_Client::GET);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID . '/export');

        $this->client->setParameterGet('format', $format);
        $this->client->setParameterGet('context', $context);
        $this->client->setParameterGet('encoding', $encoding);

        $response = $this->client->setRawData(null)->request();
        
        $this->client->resetParameters();

        return $response;
    }
    
    function modifyObject($label, $ownerId, $state, $logMessage, $lastModifiedDate) {
        $this->client->setMethod(Zend_Http_Client::PUT);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID);
        
        if (strlen($label)) {
            $this->client->setParameterGet('label', $label);
        }
        if (strlen($ownerId)) {
            $this->client->setParameterGet('ownerId', $ownerId);
        }
        if (strlen($state)) {
            $this->client->setParameterGet('state', $state);
        }
        if (strlen($logMessage)) {
            $this->client->setParameterGet('logMessage', $logMessage);
        }
        if (strlen($lastModifiedDate)) {
            $this->client->setParameterGet('lastModifiedDate', $lastModifiedDate);
        }
        
        $response = $this->client->setRawData(null)->request();
        $this->client->resetParameters();
        
        return $response;
    }
    
    function addRelationship($subject, $predicate, $object, $isLiteral, $datatype) {
        $this->client->setMethod(Zend_Http_Client::POST);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID . '/relationships/new');
        $this->client->setParameterGet('subject', $subject);
        $this->client->setParameterGet('predicate', $predicate);
        $this->client->setParameterGet('object', $object);
        $this->client->setParameterGet('isLiteral', $isLiteral);
        $this->client->setParameterGet('datatype', $datatype);
        
        $response = $this->client->setRawData(null)->request();
        $this->client->resetParameters();
        
        return $response;
    }
    
    function addDatastream($dsID, $controlGroup, $dsLocation, $altIDs, $dsLabel, $versionable, $dsState, $formatURI, $checksumType, $checksum, $mimeType, $logMessage, $data) {
        $this->client->setMethod(Zend_Http_Client::POST);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID . '/datastreams/' . $dsID);
        $this->client->setParameterGet('controlGroup', $controlGroup);
        $this->client->setParameterGet('dsLocation', $dsLocation);
        $this->client->setParameterGet('altIDs', $altIDs);
        $this->client->setParameterGet('dsLabel', $dsLabel);
        $this->client->setParameterGet('versionable', $versionable);
        $this->client->setParameterGet('dsState', $dsState);
        $this->client->setParameterGet('formatURI', $formatURI);
        $this->client->setParameterGet('checksumType', $checksumType);
        if ($checksumType != 'DISABLED') {
            $this->client->setParameterGet('checksum', null);
        }
        $this->client->setParameterGet('mimeType', $mimeType);
        $this->client->setParameterGet('logMessage', $logMessage);
        
        try {
            $response = $this->client->setRawData($data)->request();
        } catch (Exception $e) {
            $response = null;
            $this->logger->log('addDatastream Error: ' . $this->apiBase . '/objects/' . $this->PID . '/datastreams/' . $dsID , Zend_Log::INFO);
            $this->logger->log($e->getMessage() , Zend_Log::INFO);
            $this->logger->log($e->getTraceAsString() , Zend_Log::INFO);
        }
        $this->client->resetParameters();
        
        return $response;
    }
    
    function modifyDatastream($dsID, $dsLocation, $altIDs, $dsLabel, $versionable, $dsState, $formatURI, $checksumType, $checksum, $mimeType, $logMessage, $ignoreContent, $lastModifiedDate, $data) {
        $this->client->setMethod(Zend_Http_Client::PUT);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID . '/datastreams/' . $dsID);
        //
        if (strlen($dsLocation)) {
            $this->client->setParameterGet('dsLocation', $dsLocation);
        }
        if (strlen($altIDs)) {
            $this->client->setParameterGet('altIDs', $altIDs);
        }
        if (strlen($dsLabel)) {
            $this->client->setParameterGet('dsLabel', $dsLabel);
        }
        if (strlen($versionable)) {
            $this->client->setParameterGet('versionable', $versionable);
        }
        if (strlen($dsState)) {
            $this->client->setParameterGet('dsState', $dsState);
        }
        if (strlen($formatURI)) {
            $this->client->setParameterGet('formatURI', $formatURI);
        }
        if (strlen($checksumType)) {
            $this->client->setParameterGet('checksumType', $checksumType);
        }
        if (strlen($checksum)) {
            $this->client->setParameterGet('checksum', $checksum);
        }
        if (strlen($mimeType)) {
            $this->client->setParameterGet('mimeType', $mimeType);
        }
        if (strlen($logMessage)) {
            $this->client->setParameterGet('logMessage', $logMessage);
        }
        if (strlen($ignoreContent)) {
            $this->client->setParameterGet('ignoreContent', $ignoreContent);
        }
        if (strlen($lastModifiedDate)) {
            $this->client->setParameterGet('lastModifiedDate', $lastModifiedDate);
        }
        
        $response = $this->client->setRawData($data)->request();
        $this->client->resetParameters();

        return $response;
        
    }
    
    function purgeObject($pid, $logMessage) {
        $this->client->setMethod(Zend_Http_Client::DELETE);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID);
        //
        if (strlen($logMessage)) {
            $this->client->setParameterGet('logMessage', $logMessage);
        }
        
        $response = $this->client->setRawData(null)->request();
        $this->client->resetParameters();
    
        return $response;
          
    }

    function purgeDatastream($dsID, $logMessage) {
        $this->client->setMethod(Zend_Http_Client::DELETE);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID . '/datastreams/' . $dsID);
        //
        if (strlen($logMessage)) {
            $this->client->setParameterGet('logMessage', $logMessage);
        }
        
        $response = $this->client->setRawData(null)->request();
        $this->client->resetParameters();
    
        return $response;
          
    }
    
    function getDatastreamDissemination($dsID, $asOfDataTime, $download) {
        $this->client->setMethod(Zend_Http_Client::GET);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID . '/datastreams/' . $dsID . '/content');
        //
        if (strlen($asOfDataTime)) {
            $this->client->setParameterGet('asOfDataTime', $asOfDataTime);
        }
        if (strlen($download)) {
            $this->client->setParameterGet('download', $download);
        }
        
        $response = $this->client->request();
        $this->client->resetParameters();
        
        return $response->getBody();
    }
    
    function listDatastreams() {
        $this->client->setMethod(Zend_Http_Client::GET);
        $this->client->setUri($this->apiBase . '/objects/' . $this->PID . '/datastreams');
        $this->client->setParameterGet('format', 'xml');
        
        $response = $this->client->request();
        $this->client->resetParameters();
        
        //return $response;
        return $response->getBody();
    }

    function risearch($type, $flush, $lang, $format, $limit, $distinct, $stream, $query) {
        //$risearchURL = 'http://localhost:8088/fedora/risearch';
        $risearchURL = $this->apiBase . '/risearch';
        
        $this->client->setMethod(Zend_Http_Client::POST);
        $this->client->setUri($risearchURL);
        
        if (strlen($type)) {
        $this->client->setParameterPost('type', $type);
        }
        if (strlen($flush)) {
        $this->client->setParameterPost('flush', $flush);
        }
        if (strlen($lang)) {
        $this->client->setParameterPost('lang', $lang);
        }
        if (strlen($format)) {
        $this->client->setParameterPost('format', $format);
        }
        if (strlen($limit)) {
        $this->client->setParameterPost('limit', $limit);
        }
        if (strlen($distinct)) {
        $this->client->setParameterPost('distinct', $distinct);
        }
        if (strlen($stream)) {
        $this->client->setParameterPost('stream', $stream);
        }
        if (strlen($query)) {
        $this->client->setParameterPost('query', $query);
        }
        
        $response = $this->client->request();
        $this->client->resetParameters();
        
        return $response;
        
    }
}

?>
