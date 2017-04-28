<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Validation;

use Symfony\Component\Validator\ConstraintViolationList as sfConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface as sfConstraintViolationListInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ConstraintViolationMap {

    /**
     * @var sfConstraintViolationListInterface[]
     */
    private $violations = [];

    /**
     * @param string $fieldName
     * @param sfConstraintViolationListInterface $violations
     * @return $this
     */
    public function setFieldViolations($fieldName, sfConstraintViolationListInterface $violations) {
        $this->violations[ $fieldName ] = $violations;
        return $this;
    }

    /**
     * @param string $fieldName
     * @return sfConstraintViolationListInterface
     */
    public function getFieldViolations($fieldName) {
        if(!isset($this->violations[ $fieldName ])) {
            $this->setFieldViolations($fieldName, new sfConstraintViolationList());
        }
        return $this->violations[ $fieldName ];
    }

    /**
     * @return sfConstraintViolationListInterface[]
     */
    public function getViolations() {
        return $this->violations;
    }

    /**
     * returns a flat list of all the violations encountered, useful to check and count the errors globally
     * @return sfConstraintViolationListInterface
     */
    public function getFlatViolations() {
        $returnList = new sfConstraintViolationList();
        foreach($this->violations as $fieldViolations) {
            $returnList->addAll( $fieldViolations );
        }
        return $returnList;
    }

} 