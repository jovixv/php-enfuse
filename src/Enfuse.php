<?php

namespace Enfuse;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class Enfuse
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $binPath;

    /**
     * @var string
     */
    protected $downloadPath;

    /**
     * @var callable
     */
    protected $debug;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var string
     */
    protected $filePrefix;


    public function __construct()
    {

    }

    public function setBinPath(string $binPath)
    {
        $this->binPath = $binPath;
    }

    public function getBinPath()
    {
        return $this->binPath;
    }

    public function setDownloadPath(string $downloadPath)
    {
        $this->downloadPath = $downloadPath;
    }

    public function setDebug(callable $debug){
        $this->debug = $debug;
    }
}