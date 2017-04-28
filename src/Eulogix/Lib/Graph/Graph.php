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
     * @var array
     */
    private $topologicalSort = [];

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

        $vertex =
            [
            'from'=>$from,
            'to'=>$to,
            'weight'=>$weight ? $weight : 1,
            'data'=>$data
        ];

        if($id)
             $this->edges[$id] = $vertex;
        else $this->edges[] = $vertex;
        $this->edgeMap[$from][$to] = $vertex;
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

    /**
     * @return array
     */
    public function getVertices()
    {
        return $this->vertices;
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
        $DFSTempArray = [];
        foreach ($this->vertices as $vertexId => $vertexData) {
            $DFSTempArray[$vertexId] = [
                'color' => 'WHITE',
                'parent' => null
            ];
        }
        $time = 0;
        foreach ($this->vertices as $vertexId => $vertexData) {
            if ($DFSTempArray[ $vertexId ][ 'color' ] == 'WHITE')
                $this->DFSVisit($vertexId, $time, $DFSTempArray);
        }
        return $DFSTempArray;
    }

    protected function DFSVisit($vertexId, &$time = 0, &$DFSTempArray)
    {
        $DFSTempArray[ $vertexId ][ 'color' ] = 'GRAY';
        $DFSTempArray[ $vertexId ][ 'd' ] = $time++;
        foreach ($DFSTempArray as $vertex => $values) {
            if ($this->hasEdge($vertexId, $vertex, true)) {
                if ($DFSTempArray[ $vertex ][ 'color' ] == 'WHITE') {
                    $DFSTempArray[ $vertex ][ 'parent' ] = $vertexId;
                    $this->DFSVisit($vertex, $time, $DFSTempArray);
                }
            }
        }
        $DFSTempArray[ $vertexId ][ 'color' ] = 'BLACK';
        $DFSTempArray[ $vertexId ][ 'f' ] = $time++;
    }

    /**
     * @param int $sortSpec
     * @throws \Exception
     */
    public function TopologicalVertexSort($sortSpec = SORT_ASC)
    {
        $this->topologicalSort = array();
        $DFSTempArray = $this->DFS();

        uasort($DFSTempArray, function($a, $b) use ($sortSpec) {
            return $sortSpec == SORT_DESC ? $a['f'] <=> $b['f'] : $b['f'] <=> $a['f'];
        });

        $sortedVertices = [];
        foreach($DFSTempArray as $k => $v)
            $sortedVertices[$k] = $this->getVertex($k);
        $this->vertices = $sortedVertices;
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