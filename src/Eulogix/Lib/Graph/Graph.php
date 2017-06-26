<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Graph;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Graph {

    /**
     * @var array
     */
    private $vertices = [], $edges = [], $edgeMap = [];

    /**
     * @param string $id
     * @param mixed $data
     */
    public function addVertex($id, $data=null) {
        $this->vertices[$id] = ['data'=>$data];
    }

    /**
     * @param string $id
     * @param string $from
     * @param string $to
     * @param int $weight
     * @param mixed $data
     * @throws \Exception
     */
    public function addEdge($id=null, $from, $to, $weight=null, $data=null) {

        $this->getVertex($from);
        $this->getVertex($to);

        $edge =
            [
            'from'=>$from,
            'to'=>$to,
            'weight'=>$weight ? $weight : 1,
            'data'=>$data
        ];

        if($id)
             $this->edges[$id] = $edge;
        else $this->edges[] = $edge;
        $this->edgeMap[$from][$to] = $edge;
    }

    /**
     * @param string $id
     * @return array
     * @throws \Exception
     */
    public function getVertex($id) {
        if(!$this->hasVertex($id))
            throw new \Exception("Vertex $id not found");
        return $this->vertices[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasVertex($id) {
        return isset($this->vertices[$id]);
    }

    /**
     * @param string $id1
     * @param string $id2
     * @param bool $directed
     * @return bool
     */
    public function hasEdge($id1, $id2, $directed=false) {
        return isset($this->edgeMap[$id1][$id2]) || (!$directed && isset($this->edgeMap[$id2][$id1]));
    }

    /**
     * @param string $id
     */
    public function removeVertex($id) {
        unset($this->vertices[$id]);
        $delVertices = [];
        foreach($this->edges as $k=>$v) {
            if($v['from']==$id || $v['to']==$id)
                $delVertices[]=$k;
        }
        foreach($delVertices as $dk)
            unset($this->edges[$dk]);
    }

    public function removeEdges() {
        $this->edges = [];
        $this->edgeMap = [];
    }

    /**
     * @return array
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * @return string[]
     */
    public function getVertexIds()
    {
        return array_keys( $this->vertices );
    }

    /**
     * @return array
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * @param string $id
     * @return array
     */
    public function getEdgesFromId($id)
    {
        $ret = [];
        $edges = $this->getEdges();
        foreach($edges as $e)
            if($e['from']==$id)
                $ret[] = $e;
        return $ret;
    }

    /**
     * @param $id
     * @return array
     */
    public function getEdgesToId($id)
    {
        $ret = [];
        $edges = $this->getEdges();
        foreach($edges as $e)
            if($e['to']==$id)
                $ret[] = $e;
        return $ret;
    }

    /**
     * @param $id
     * @return array
     */
    public function getEdgesInvolvingId($id)
    {
        $ret = [];
        $edges = $this->getEdges();
        foreach($edges as $e)
            if(($e['from']==$id) || ($e['to']==$id))
                $ret[] = $e;
        return $ret;
    }

    protected function DFS()
    {
        $DFSTempArray = [
            'v' => [],
            'visits' => []
        ];
        foreach ($this->vertices as $vertexId => $vertexData) {
            $DFSTempArray['v'][$vertexId] = [
                'color' => 'WHITE',
                'parent' => null
            ];
        }
        $time = 0;
        foreach ($this->vertices as $vertexId => $vertexData) {
            if ($DFSTempArray['v'][ $vertexId ][ 'color' ] == 'WHITE') {
                $DFSTempArray['lastIteration'] = [];
                $this->DFSVisit($vertexId, $time, $DFSTempArray);
                $DFSTempArray['visits'][$vertexId] = $DFSTempArray['lastIteration'];
                unset($DFSTempArray['lastIteration']);
            }
        }
        return $DFSTempArray;
    }

    protected function DFSVisit($vertexId, &$time = 0, &$DFSTempArray)
    {
        $DFSTempArray['lastIteration'][] = $vertexId;

        $DFSTempArray['v'][ $vertexId ][ 'color' ] = 'GRAY';
        $DFSTempArray['v'][ $vertexId ][ 'd' ] = $time++;

        foreach ($DFSTempArray['v'] as $vertex => $values) {
            if ($this->hasEdge($vertexId, $vertex, true)) {
                if ($DFSTempArray['v'][ $vertex ][ 'color' ] == 'WHITE') {
                    $DFSTempArray['v'][ $vertex ][ 'parent' ] = $vertexId;
                    $this->DFSVisit($vertex, $time, $DFSTempArray);
                }
            }
        }
        $DFSTempArray['v'][ $vertexId ][ 'color' ] = 'BLACK';
        $DFSTempArray['v'][ $vertexId ][ 'f' ] = $time++;
    }

    /**
     * @param int $sortSpec
     * @throws \Exception
     */
    public function TopologicalVertexSort($sortSpec = SORT_DESC)
    {
        $DFSTempArray = $this->DFS();

        uasort($DFSTempArray['v'], function($a, $b) use ($sortSpec) {
            return $sortSpec == SORT_DESC ? $b['f'] <=> $a['f'] : $a['f'] <=> $b['f'];
        });

        $sortedVertices = [];
        foreach($DFSTempArray['v'] as $k => $v)
            $sortedVertices[$k] = $this->getVertex($k);
        $this->vertices = $sortedVertices;
    }

    /**
     * @param bool $includeSingleNodes
     * @return array
     */
    public function getStronglyConnectedComponents($includeSingleNodes = false) {
        $wkGraph = clone $this;
        $wkGraph->TopologicalVertexSort();

        $tG = $wkGraph->getTransposedGraph();
        $DFSTempArray = $tG->DFS();

        return array_filter($DFSTempArray['visits'], function($visit) use($includeSingleNodes) {
            return $includeSingleNodes || count($visit) > 1;
        });
    }

    /**
     * inverts the order of edges
     * @return Graph
     */
    public function getTransposedGraph() {
        $tg = clone $this;
        $tg->removeEdges();
        foreach($this->getEdges() as $edgeId => $edge)
            $tg->addEdge($edgeId, $edge['to'], $edge['from'], $edge['weight'], $edge['data']);
        return $tg;
    }

    /**
     * @param array $diagramMetadata
     * @return array
     */
    public function getVisJsData( $diagramMetadata = null ) {
        $nodes = [];
        $edges = [];

        $gn = $this->getVertices();
        foreach($gn as $id=>$n) {

            $node =  array_merge(
                [
                    'id'=>$id,
                    'shape'=>isset($n['data']['image'])? 'image' : null
                ],
                $n['data'],
                isset($diagramMetadata['positions'][$id]) ? $diagramMetadata['positions'][$id] : []
            );

            $nodes[] = $node;
        }

        $gv = $this->getEdges();
        foreach($gv as $id=>$v) {
            $base = [
                'id'=>$id,
                'length'=>$v['weight'],
            ];

            if(isset($v['data']['label']))
                $base['label'] = $v['data']['label'];

            if(isset($v['data']['title']))
                $base['title'] = $v['data']['title'];

            $edge = array_merge($base, $v);

            $edges[] = $edge;
        }
        return ['nodes'=>$nodes, 'edges'=>$edges];
    }

}