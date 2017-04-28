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

class ConstraintBuilder {

    public static function AbstractComparison_() { return array('constraint'=>'AbstractComparison'); }
    public static function All_() { return array('constraint'=>'All'); }
    public static function Blank_() { return array('constraint'=>'Blank'); }
    public static function Callback_() { return array('constraint'=>'Callback'); }
    public static function CardScheme_() { return array('constraint'=>'CardScheme'); }
    public static function Choice_() { return array('constraint'=>'Choice'); }
    public static function Collection_() { return array('constraint'=>'Collection'); }
    public static function Count_($min=null, $max=null) { return array('constraint'=>'Count', 'args'=>['min'=>$min, 'max'=>$max], 'messages'=>['minMessage', 'maxMessage', 'exactMessage']); }
    public static function Country_() { return array('constraint'=>'Country'); }
    public static function Currency_() { return array('constraint'=>'Currency'); }
    public static function Date_() { return array('constraint'=>'Date'); }
    public static function DateTime_() { return array('constraint'=>'DateTime'); }
    public static function Email_() { return array('constraint'=>'Email'); }
    public static function EqualTo_($v) { return array('constraint'=>'EqualTo', 'arg'=>$v); }
    public static function Existence_() { return array('constraint'=>'Existence'); }
    public static function Expression_() { return array('constraint'=>'Expression'); }
    public static function False_() { return array('constraint'=>'False'); }
    public static function File_() { return array('constraint'=>'File'); }
    public static function GreaterThan_($v) { return array('constraint'=>'GreaterThan', 'arg'=>$v); }
    public static function GreaterThanOrEqual_() { return array('constraint'=>'GreaterThanOrEqual'); }
    public static function GroupSequence_() { return array('constraint'=>'GroupSequence'); }
    public static function GroupSequenceProvider_() { return array('constraint'=>'GroupSequenceProvider'); }
    public static function Iban_() { return array('constraint'=>'Iban'); }
    public static function IdenticalTo_() { return array('constraint'=>'IdenticalTo'); }
    public static function Image_() { return array('constraint'=>'Image'); }
    public static function Ip_() { return array('constraint'=>'Ip'); }
    public static function Isbn_() { return array('constraint'=>'Isbn'); }
    public static function Issn_() { return array('constraint'=>'Issn'); }
    public static function Language_() { return array('constraint'=>'Language'); }
    public static function Length_() { return array('constraint'=>'Length'); }
    public static function LessThan_() { return array('constraint'=>'LessThan'); }
    public static function LessThanOrEqual_() { return array('constraint'=>'LessThanOrEqual'); }
    public static function Locale_() { return array('constraint'=>'Locale'); }
    public static function Luhn_() { return array('constraint'=>'Luhn'); }
    public static function NotBlank_() { return array('constraint'=>'NotBlank'); }
    public static function NotEqualTo_($v) { return array('constraint'=>'NotEqualTo', 'arg'=>$v); }
    public static function NotIdenticalTo_() { return array('constraint'=>'NotIdenticalTo'); }
    public static function NotNull_() { return array('constraint'=>'NotNull'); }
    public static function Null_() { return array('constraint'=>'IsNull'); }
    public static function Optional_() { return array('constraint'=>'Optional'); }
    public static function Range_() { return array('constraint'=>'Range'); }
    public static function Regex_() { return array('constraint'=>'Regex'); }
    public static function Required_() { return array('constraint'=>'Required'); }
    public static function Time_() { return array('constraint'=>'Time'); }
    public static function True_() { return array('constraint'=>'True'); }
    public static function Type_() { return array('constraint'=>'Type'); }
    public static function Url_() { return array('constraint'=>'Url'); }
    public static function Valid_() { return array('constraint'=>'Valid'); }


    public static function _ALL() {
        $c = array();
        for($i=0;$i<func_num_args();$i++) {
            $c[] = func_get_arg($i);
        }
        return array('operator'=>'and', 'constraints'=>$c);
    }

    public static function _EITHER() {
        $c = array();
        for($i=0;$i<func_num_args();$i++) {
            $c[] = func_get_arg($i);
        }
        return array('operator'=>'or', 'constraints'=>$c);
    }

}