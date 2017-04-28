<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Error;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ErrorReport {
    
    private $errors = [];
    private $generalErrors = [];
    
    /**
    * @param string|null $fieldName
    * @param string $errorMessage
    * @return ErrorReport
    */
    public function addError($fieldName=null, $errorMessage) {
        if($fieldName!==null) 
             $this->errors[$fieldName] = $errorMessage;
        else $this->addGeneralError($errorMessage);
        return $this;
    }    
    
    /**
    * @param string $errorMessage
    * @return ErrorReport
    */
    public function addGeneralError($errorMessage) {
        $this->generalErrors[] = $errorMessage;
        return $this;
    }

    /**
    * @param string $fieldName
    * @return string[]
    */
    public function getErrors($fieldName=null) {
        if($fieldName && isset($this->errors[$fieldName])) {
            return $this->errors[$fieldName];
        }
        return $this->errors;
    }   
    
    /**
    * @return string[]
    */
    public function getGeneralErrors() {
        return $this->generalErrors;
    }
    
    /**
    * @return boolean
    */
    public function hasErrors() {
        return !empty($this->generalErrors) || !empty($this->errors);
    }     
    
}