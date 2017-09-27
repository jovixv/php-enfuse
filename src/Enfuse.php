<?php
declare(strict_types=1);

namespace Enfuse;


use Enfuse\Exceptions\ExecutableNotFoundException;
use SebastianBergmann\GlobalState\RuntimeException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;
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


    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

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

    /**
     * @param callable $debug Process::ERR
     */
    public function setDebug(callable $debug){
        $this->debug = $debug;
    }

    /**
     * @return echo ERROR + OUTPUT FROM app.
     */
    public function debugOn(){
        $this->setDebug(function($type, $bufer){
            if (\Symfony\Component\Process\Process::ERR == $type){
                echo 'ERR > '. $bufer.PHP_EOL;
            }else{
                echo 'OUTPUT > '. $bufer.PHP_EOL;
            }
        });
    }

    /**
     * Create command line arguments.
     *
     * @return array
     */
    public function createCommandLine() :array { // TO DO... refactor
        $arguments = [];

        foreach ($this->options as $options => $value) {

             if ( strpos($options,'-') === 0 ){ // check options with one "-" and without "="

                 if ( is_bool($value) AND $value == 1){
                     $arguments[] = sprintf('%s', $options);
                 }elseif ( !is_bool($value) ){
                     $arguments[] = sprintf('%s %s',$options,$value);
                 }

             }else{ //check options with "--" symbols.

                 if ( is_bool($value) AND $value == 1){
                     $arguments[] = sprintf('--%s', $options);
                 }elseif (!is_bool($value)){
                     $arguments[] = sprintf('--%s=%s',$options,$value);
                 }

             }

        }
print_r($arguments);
        return $arguments;
    }

    /**
     * $arguments generated in startEnfuse
     *
     * @param array $arguments
     * @return Process
     */
    protected function createProcess(array $arguments = []) :Process
    {
        $binPath = $this->binPath ?: (new ExecutableFinder())->find('enfuse');

        if (null === $binPath) {
            throw new ExecutableNotFoundException('"enfuse" executable was not found. Did you forgot to add it to environment variables? Or set it via $enfuse->setBinPath(\'/usr/bin/enfuse\').');
        }

        array_unshift($arguments, $binPath);

        $process = new Process($arguments);
        $process->setTimeout($this->timeout);

        if ($this->downloadPath){
            $process->setWorkingDirectory($this->downloadPath);
        }

        return $process;
    }

    public function startEnfuse(){

        if (!$this->downloadPath){
            throw new RuntimeException('No download path was set');
        }

       $process = $this->createProcess($this->createCommandLine());

        try{
           $process->mustRun(is_callable($this->debug) ? $this->debug : NULL);
        } catch (\Exception $e){

        }

    }

    protected function configureOptions(OptionsResolver $resolver){

        $options = [
            //Common Options
            'version' => 'bool',
            'help'    => 'bool', // more information about avalible fields.
            'levels'  => 'int',
            'output'  => 'string',
            'wrap'    => 'string',
            'compression' =>'string',
            'layer-selector'=>'string',
            //Extended Options
            '-b'=>'int',
            'ciecam'=>'bool',
            'no-ciecam'=>'bool',
            'fallback-profile'=>'string',
            'depth'=>'string',
            '-g'=>'bool',
            '-f'=>'string',
            '-m'=>'int',
            //Fusion options
            'exposure-weight'=>'int|float',
            'saturation-weight'=>'int|float',
            'contrast-weight'=>'int|float',
            'entropy-weight'=>'int|float',
            'exposure-mu'=>'int|float',
            'exposure-sigma'=>'int|float',
            'soft-mask'=>'bool',
            'hard-mask'=>'bool',
            //Expert Options
            'exposure-cutoff'=>'string',
            'contrast-window-size'=>'int',
            'gray-projector'=>'string',
            'contrast-edge-scale'=>'string',
            'contrast-min-curvature'=>'string',
            'entropy-window-size'=>'int',
            'entropy-cutoff'=>'string',
            'save-masks'=>'string',
            'load-masks'=>'string',
        ];

        $resolver->setDefined(array_keys($options));

    }
}