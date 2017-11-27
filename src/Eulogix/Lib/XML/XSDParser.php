<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\XML;

use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class XSDParser {

    /**
     * @param $xsd
     * @return \SimpleXMLElement
     */
    function xsd2xml($xsd) {
        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = true;
        $doc->load($xsd);

        $temp_xml = tempnam(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(),'XML');

        $doc->save($temp_xml);

        $myxmlfile = file_get_contents($temp_xml);
        @unlink($temp_xml);
        $parseObj = str_replace($doc->lastChild->prefix.':','',$myxmlfile);
        //file_put_contents($temp_xml, $parseObj);
        $simpleXml = simplexml_load_string($parseObj);
        return $simpleXml;
    }


    protected function simpleXmlElementToArray($xmlElement, $contentAttributeName=false) {
        $attributes = array();
        $elemAttributes = $xmlElement->attributes();
        foreach($elemAttributes as $attr => $tv) {
            $v = $tv->__toString();

            switch(strtolower($v)) {
                case 'true':  $v = true; break;
                case 'false': $v = false; break;
            }

            $attributes[$attr] = $v;
        }

        //in case we have a content, save it
        $content = trim($xmlElement->__toString());
        if($content) {
            $attributes[ $contentAttributeName ? $contentAttributeName : "NodeContent" ] = $content;
        }
        return $attributes;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param $typeName
     * @return bool|\SimpleXMLElement
     */
    function extractComplexType(\SimpleXMLElement $xml, $typeName) {
        foreach($xml->complexType as $ct) {
            $complexTypeName = $ct->attributes()['name']->__toString();
            //new table attributes
            if($complexTypeName == $typeName) {
                return $ct;
            }
        }
        return false;
    }

    function extractCoolElements($xmlFile) {

        $arr = [];


        $xml = $this->xsd2xml($xmlFile);
        foreach($xml->complexType as $ct) {
            $complexTypeName = $ct->attributes()['name']->__toString();

            //new table attributes
            if ($this->shouldScanComplexType($complexTypeName)) {
                $regex = $this->hasCoolPrefix($complexTypeName) ? '/^_*(.+?)$/sim' : '/^_(.+?)$/sim';
                $arr[$complexTypeName] = $this->extractCoolElementsFromNode($ct, $regex, $xml);
            }


        }
        return $arr;
    }

    protected function extractCoolElementsFromNode($ct, $regex, $xml)
    {
        $arr = [];

        //scan attributes
        foreach ($ct->attribute as $att) {
            $attributeName = $att->attributes()['name']->__toString();
            if (preg_match($regex, $attributeName, $m)) {
                //cool attribute
                $arr['attributes'][$m[1]] = $this->simpleXmlElementToArray($att);
            }
        }

        //scan choices
        if ($ct->choice) {
            foreach ($ct->choice as $choice) {
                foreach ($choice->element as $element) {
                    if ($elementName = $element->attributes()['name']) {
                        if (preg_match($regex, $elementName->__toString(), $m)) {
                            //cool choice
                            $choiceArr = $this->simpleXmlElementToArray($element);
                            if (isset($element->attributes()['type'])) {
                                if ($complexType = $this->extractComplexType($xml, $element->attributes()['type']->__toString())) {
                                    $choiceArr['_type'] = json_decode(json_encode((array)$complexType), 1);
                                }
                            }
                            $arr['choices'][$m[1]] = $choiceArr;
                        }
                    }
                }
            }
        }


        return $arr;
    }

    /**
     * @param $complexTypeName
     * @return bool
     */
    protected function shouldScanComplexType($complexTypeName)
    {
        return in_array($complexTypeName, array( "table", "column")) ||  $this->hasCoolPrefix($complexTypeName);
    }

    /**
     * @param $complexTypeName
     * @return bool
     */
    protected function hasCoolPrefix($complexTypeName)
    {
        return strpos($complexTypeName, '_') === 0;
    }

} 