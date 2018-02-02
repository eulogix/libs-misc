<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Lib\File\Proxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SimpleFileProxy extends BaseFileProxy
{

    protected $contentFile;

    protected $content;

    /**
     * @param string $filePath
     * @param bool $inMemory
     * @return SimpleFileProxy
     * @throws \Exception
     */
    public static function fromFileSystem($filePath, $inMemory = false) {
        if(!file_exists($filePath))
            throw new \Exception("file $filePath does not exist");
        $pi = mb_pathinfo($filePath);
        $f = new self();
        $f->setName($pi['basename']);
        if($inMemory) {
            $f->setContent(file_get_contents($filePath));
            $f->setDetailsFromFile($filePath);
        } else $f->setContentFile($filePath);
        return $f;
    }

    /**
     * @param string $name
     * @param string $id
     * @param string $parentId
     * @param bool $isDir
     * @return SimpleFileProxy
     */
    public static function fromValues($name, $id, $parentId, $isDir = false) {
        $f = new self();
        $f  ->setIsDirectory($isDir)
            ->setName( $name )
            ->setId( $id )
            ->setParentId( $parentId );
        return $f;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            'id' => $this->getId(),
            'parentId' => $this->getParentId(),
            'name' => $this->getName(),
            'extension' => $this->getExtension(),
            'hash' => $this->getHash(),
            'size' => $this->getSize(),
            'lastModificationDate' => $this->getLastModificationDate(),
            'properties' => $this->getProperties(),
        ];
    }

    /**
     * saves the file in the filesystem
     * @param string $fileName
     * @return $this
     */
    public function toFile($fileName)
    {
        if($this->contentFile)
            copy($this->contentFile, $fileName);
        else file_put_contents($fileName, $this->content);
    }

    /**
     * @param mixed $contentFile
     * @param null $sha1Hash if provided, avoids recomputation
     * @return $this
     */
    public function setContentFile($contentFile, $sha1Hash = null)
    {
        $this->content = null;
        $this->contentFile = $contentFile;
        $this->setHash( $sha1Hash ?? sha1_file($contentFile) );
        $this->setDetailsFromFile($contentFile);
        $this->setId(fileinode($contentFile));
        $this->setParentId(fileinode(dirname($contentFile)));
        return $this;
    }

    /**
     * gets the whole content as a string
     * @return mixed
     */
    public function getContent()
    {
        return $this->contentFile ? file_get_contents($this->contentFile) : $this->content;
    }

    /**
     * @param mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->contentFile = null;
        $this->setHash(sha1($content));
        return $this;
    }

    /**
     * @param $contentFile
     */
    protected function setDetailsFromFile($contentFile)
    {
        $this->setSize(filesize($contentFile));
        $this->setCreationDate(\DateTime::createFromFormat('U', filectime($contentFile)));
        $this->setLastModificationDate(\DateTime::createFromFormat('U', filemtime($contentFile)));
        $this->setIsDirectory(is_dir($contentFile));
    }

    public function clear() {
        if($this->contentFile)
            @unlink($this->contentFile);
    }
}