<?php

namespace Uriel\DesafioTtrPhp\lib;

class ProcessData
{
    private $olderCsv;
    private $newCsv;

    public function __construct($newCsv = null, $olderCsv = null)
    {
        $this->newCsv   = $newCsv;
        $this->olderCsv = $olderCsv;
    }

    public function compareData()
    {
        return 'Hello World';
    }
}