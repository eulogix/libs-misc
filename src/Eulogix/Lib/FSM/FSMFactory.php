<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\FSM;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;
use Finite\StateMachine\StateMachineInterface;

class FSMFactory
{

    /**
     * @param array $states
     * @param object $subject
     * @return StateMachineInterface
     * @throws \Finite\Exception\ObjectException
     */
    public static function simpleLinearFSM(array $states, $subject) {

        $fsm = new StateMachine($subject);

        $statesArray = [];
        $keys = array_keys($states);
        $firstKey = $keys[0];
        $lastKey = array_pop($keys);
        foreach($states as $k=>$s)
            $statesArray[$s] = [
                'type'       => $k == $firstKey ? StateInterface::TYPE_INITIAL : ($k == $lastKey ? StateInterface::TYPE_FINAL : StateInterface::TYPE_NORMAL),
                'properties' => []
            ];

        $loader       = new ArrayLoader([
            'class'       => get_class($subject),
            'states'      => $statesArray,
            'transitions' => self::buildLinearTransitions($statesArray)
        ]);

        @$loader->load($fsm);
        $fsm->initialize();
        return $fsm;

    }

    /**
     * builds forward/back transitions between the states
     * @param array $states
     * @return array
     */
    protected static function buildLinearTransitions($states) {
        $stateKeys = array_keys($states);
        $ret = [];
        for($i = 0; $i<count($stateKeys); $i++) {
            $state = $stateKeys[$i];
            $nextState = isset($stateKeys[$i+1]) ? $stateKeys[$i+1] : $stateKeys[0];
            $prevState = isset($stateKeys[$i-1]) ? $stateKeys[$i-1] : null;

            if($prevState)
                $ret[$state.'_prev'] = [
                    'from' => $state,
                    'to' => $prevState
                ];

            $ret[$state.'_next'] = [
                'from' => $state,
                'to' => $nextState
            ];
        }

        return $ret;
    }

}