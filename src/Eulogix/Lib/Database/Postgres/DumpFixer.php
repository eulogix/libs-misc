<?php

/*
 * This file is part of the Eulogix\Lib package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eulogix\Lib\Database\Postgres;

/**
 * Postgres 9.6.8 introduced a new format which is incompatible with ApgDiff.
 * This performs the necessary modifications to make new dumps manageable by the tool.
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DumpFixer
{
    /**
     * @var string
     */
    protected $dumpContent;

    /**
     * DumpFixer constructor.
     * @param string $dump a file or the dump content
     */
    public function __construct(string $dump)
    {
        $this->dumpContent = file_exists($dump) ? file_get_contents($dump) : $dump;
    }

    /**
     * @param string $dump
     */
    public static function fixDump(string $dump) {
        if(file_exists($dump)) {
            $fixer = new self($dump);
            if($fixer->mustBeFixed())
                file_put_contents($dump, $fixer->getFixedDump());
        }
    }

    /**
     * @return string
     */
    public function getFixedDump() {
        if(!$this->mustBeFixed())
            return $this->dumpContent;

        $moddedDump = $this->dumpContent;

        $schemas = array_merge(['public'], $this->getDefinedSchemas());

        /*
        foreach($schemas as $schema) {
            $rx = "/^CREATE (.+?) {$schema}\.(.+?) /im";
            $createStatements = preg_match_all($rx, $moddedDump, $m, PREG_OFFSET_CAPTURE);

            $offset = $m[0][1];
            $moddedDump = substr_replace($moddedDump, "SET search_path = {$schema}, pg_catalog;\n\n", $offset, 0);
            $moddedDump = preg_replace($rx, "CREATE $1 $2 ", $moddedDump);

            $moddedDump = preg_replace("/^COMMENT ON COLUMN {$schema}\.([^ \.]+?)\.([^ \.]+?) IS/im", "COMMENT ON COLUMN $1.$2 IS", $moddedDump);
        }*/

        foreach($schemas as $schema) {
            $setSchemaStatement = "SET search_path = {$schema}, pg_catalog;\n\n";
            $moddedDump = preg_replace("/^CREATE (.+?) {$schema}\.(.+?) /im", "{$setSchemaStatement}CREATE $1 $2 ", $moddedDump);
            $moddedDump = preg_replace("/^COMMENT ON COLUMN {$schema}\.([^ \.]+?)\.([^ \.]+?) IS/im", "{$setSchemaStatement}COMMENT ON COLUMN $1.$2 IS", $moddedDump);
            /* Fix for the postgresql10 dumps */
            $moddedDump = preg_replace("/_seq\s*AS integer\s*START WITH/","_seq\nSTART WITH",$moddedDump);

        }

        return $moddedDump;
    }

    /**
     * @return bool
     */
    public function mustBeFixed() {
        return ((preg_match('/^SET search_path/sim', $this->dumpContent) !== 1) OR
            (preg_match('/_seq\s*AS integer\s*START WITH/', $this->dumpContent) === 1));

    }
    
    protected function getDefinedSchemas() {
        preg_match_all('/^CREATE SCHEMA (.+?);/sim', $this->dumpContent, $m);
        return @$m[1] ?? [];
    }

}