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

use Symfony\Component\Validator\ConstraintViolationListInterface as sfConstraintViolationListInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface BeanValidatorInterface
{
    const CONTEXT_DEFAULT = 'default';

    /**
     * @param $fieldName
     * @return mixed
     */
    public function getConstraints($fieldName);

    /**
     * @param $fieldName
     * @param $value
     * @return sfConstraintViolationListInterface
     */
    public function validateField($fieldName, $value);

    /**
     * @param mixed $hash
     * @return ConstraintViolationMap
     */
    public function validateHash($hash);

    /**
     * sets (limits) the validation context
     * @param string[] $contexts
     * @return self
     */
    public function setOperatingContexts($contexts);

    /**
     * returns the operating contexts
     * @return string[]
     */
    public function getOperatingContexts();

    /**
     * Sets the validation constraints for a field
     *
     * @param string $fieldName
     * @param mixed $constraints
     * @param string|string[] $contexts
     * @return $this
     */
    public function setConstraints($fieldName, $constraints, $contexts = self::CONTEXT_DEFAULT);
}