<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils;

class CsvIterator implements \Iterator
{
    // configuration
    private $csvDelimiter = ';';
    private $csvEnclosure = '"';
    private $csvEscape = '\\';

    private $file;
    private $stream;
    private $currentLine;
    private $counter;
    private $headings;

    public function __construct($file)
    {
        $this->file = $file;
        $this->rewind();
    }

    public function rewind()
    {
        if ($this->stream !== null)
            fclose($this->stream);
        $this->stream = fopen($this->file, 'r');
        $this->counter = 0;

        $headingsLine = fgets($this->stream);
        $headings = str_getcsv($headingsLine, $this->csvDelimiter, $this->csvEnclosure, $this->csvEscape);
        $this->headings = array_map('trim', $headings);
    }

    public function current()
    {
        return $this->parseLine($this->currentLine);
    }

    public function key()
    {
        return $this->counter;
    }

    public function next()
    {
        $this->currentLine = fgets($this->stream);
        $this->counter ++;
    }

    public function valid()
    {
        return (feof($this->stream) === false);
    }

    public function getHeadings()
    {
        return $this->headings;
    }

    protected function parseLine($line)
    {
        if ($line === '' || $line === null)
            return null;

        $values = str_getcsv($line, $this->csvDelimiter, $this->csvEnclosure, $this->csvEscape);

        $record = [];
        foreach ($this->headings as $pos => $name) {
            if (isset($values[$pos]) === false)
                $record[$name] = null;
            else
                $record[$name] = trim($values[$pos]);
        }

        return $record;
    }
}