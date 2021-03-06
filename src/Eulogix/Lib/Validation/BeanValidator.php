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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

use Symfony\Component\Validator\ValidatorInterface as SfValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation as sfConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList as sfConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface as sfConstraintViolationListInterface;

class BeanValidator implements BeanValidatorInterface
{
    /**
     * @var SfValidatorInterface
     */
    protected $validator;

    protected $constraints = [];

    protected $contexts = [self::CONTEXT_DEFAULT];

    /**
     * @param SfValidatorInterface $validator
     */
    public function __construct(SfValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function setConstraints($fieldName, $constraints, $contexts = null) {
        if(!empty($constraints) && !isset($constraints['operator'])) {
            $constraints = ConstraintBuilder::_ALL($constraints);
        }
        if(!$contexts)
            $contexts = [ self::CONTEXT_DEFAULT ];
        elseif(!is_array($contexts))
            $contexts = [ $contexts ];

        foreach($contexts as $context)
            $this->constraints[ $fieldName ][ $context ] = $constraints;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConstraints($fieldName) {
        $ret = [];
        foreach($this->contexts as $context) {
            if(isset($this->constraints[ $fieldName ][ $context ]))
                $ret = array_merge($ret, $this->constraints[ $fieldName ][ $context ]);
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function validateHash($hash) {
        $returnMap = new ConstraintViolationMap();
        foreach($hash as $fieldName => $fieldValue) {
            $returnMap->setFieldViolations($fieldName, $this->validateField($fieldName, $fieldValue));
        }
        return $returnMap;
    }

    /**
     * @inheritdoc
     */
    public function validateField( $fieldName, $value ) {
        $fieldConstraints = $this->getConstraints($fieldName);
        if( isset($fieldConstraints['operator']) ) {
            return $this->doValidateField( $value, $fieldConstraints['operator'], $fieldConstraints['constraints'] );
        }
        return new sfConstraintViolationList();
    }

    /**
     * @param $value
     * @param $operator
     * @param $constraints
     * @return sfConstraintViolationListInterface
     */
    private function doValidateField($value, $operator, $constraints) {
        $returnList = new sfConstraintViolationList();

        foreach($constraints as $c) {
            $violationList = new sfConstraintViolationList();
            if(isset($c['operator'])) {
                $violationList = $this->doValidateField($value, $c['operator'], $c['constraints']);
            } else {

                $args=null;
                if(isset($c['arg'])){
                    $args = array('value'=>$c['arg']);
                }
                if(isset($c['args'])){
                    $args = $c['args'];
                }

                if( $sfConstraint = $this->sfConstraintFactory($c['constraint'], $args) ) {

                    if(isset($c['messages'])) {
                        foreach($c['messages'] as $messageKey => $message)
                            $sfConstraint->$messageKey = $message;
                    } elseif(isset($c['message'])) $sfConstraint->message = $c['message'];

                    $violationList = $this->validator->validate($value, $sfConstraint);
                } else {
                    $violationList->add( new sfConstraintViolation(null, "class not found {{class}}", ["class"=>$c['constraint']], null, null, null) );
                }
            }

            switch($operator) {
                case 'or'  : {
                    if($violationList->count()==0) {
                        return $violationList;
                    }
                    break;
                }
                case 'not'  : {
                    if($violationList->count()==0) {
                        $violationList->add( new sfConstraintViolation("FAIL NOT ".@$c['constraint'], "FAIL {{class}}", ["class"=>$c['constraint']], null, null, null) );
                        return $violationList;
                    }
                    break;
                }
                case 'and' : {
                    $returnList->addAll( $violationList );
                    break;
                }
            }
        }
        return $returnList;
    }

    private function sfConstraintFactory( $type, $args=null ) {
        $ns = "Symfony\\Component\\Validator\\Constraints\\$type";
        if(class_exists($ns)) {
            return new $ns($args);
        }
        return false;
    }

    /**
     * sets (limits) the validation context
     * @param string[] $contexts
     * @return self
     */
    public function setOperatingContexts($contexts)
    {
        $this->contexts = $contexts;
    }

    /**
     * returns the operating contexts
     * @return string[]
     */
    public function getOperatingContexts()
    {
        return $this->contexts;
    }
}