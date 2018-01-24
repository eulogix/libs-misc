<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\HTML;

use HTML_SAXFormScannerPageProcessor;
use HTML_SAXParser;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class HTMLProcessor
{
    /**
     * @param $html
     * @return array
     */
    public static function getFormsArray($html)
    {
        $procObj = new HTML_SAXFormScannerPageProcessor();
        $parser = new HTML_SAXParser();
        $parser->init($procObj,'begin','endTag','character');
        $parser->parseString($html);
        return is_array($procObj->formsArray) ? $procObj->formsArray : [];
    }

    /**
     * Returns just the form values as an associative array
     * @param string $html
     * @param string $formName
     * @return array
     */
    public static function getFormValues($html, $formName = null)
    {
        $lastSelect = '';
        $ret = array();
        $forms = self::getFormsArray($html);
        foreach($forms as $form) {
            if (is_array($form[ 'fields' ]) && ( !$formName || $formName == $form[ 'name' ] )) {
                foreach($form['fields'] as $field) {
                    if ($field[ 'name' ]) {
                        $ret[ $field[ 'name' ] ] = $field[ 'value' ];
                        if ($field[ 'tag' ] == 'select') {
                            $lastSelect = $field[ 'name' ];
                        }
                    } else {
                        if ($field[ 'tag' ] == 'option' && $field[ 'selected' ] && $field[ 'value' ]) {
                            $ret[ $lastSelect ] = $field[ 'value' ];
                        }
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Returns the complete form structure, including all the possible option values for SELECTs
     * @param string $html
     * @param string $formName
     * @return array
     */
    public static function getFormStructure($html, $formName = null)
    {
        $lastSelect = '';
        $ret = array();
        $forms = self::getFormsArray($html);
        foreach($forms as $form)
            if (is_array($form[ 'fields' ]) && ( !$formName || $formName == $form[ 'name' ] )) {
                $formArr = [
                    'name' => $form['name']
                ];

                foreach($form['fields'] as $field)
                    if($field['name']) {
                        $formArr['fields'][ $field['name'] ] = [
                            'value' =>  $field['value'],
                            'tag'   =>  $field['tag']
                        ];
                        if($field['tag'] == 'select')
                            $lastSelect = $field['name'];
                    } else {
                        if($field['tag'] == 'option' && $field['value'])
                            $formArr['fields'][$lastSelect]['options'][] = [
                                'value' => $field['value'],
                                'selected' => $field['selected'],
                                'text' => $field['text']
                            ];
                    }
                $ret[] = $formArr;
            }
        return $ret;
    }

    /**
     * @param string $html
     * @param string $selectName
     * @param string $formName
     * @return array
     */
    public static function getSelectOptions($html, $selectName, $formName = null) {
        $formStructure = self::getFormStructure($html, $formName);
        $selectedForm = null;
        if($formName)
            $selectedForm = $formStructure[$formName];
        else foreach($formStructure as $form)
            if(isset($form['fields'][$selectName]['options']))
                $selectedForm = $form;
        return $selectedForm ? @$selectedForm['fields'][$selectName]['options'] : [];
    }
}