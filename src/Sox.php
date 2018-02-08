<?php

namespace MiragePresent\Sox;


use MiragePresent\Sox\Classes\Input;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use MiragePresent\Sox\Classes\Option;

class Sox implements SoxInterface
{

    /** @var string $sox Sox command */
    protected $sox;

    /** @var  string $mode Input File Combining  */
    protected $mode = '';

    /** @var \Illuminate\Support\Collection $inputs Inputs */
    protected $inputs;

    /**  @var array $output Output settings */
    protected $output = [
        'path' => '',
        'options' => []
    ];

    protected $modes = [
        'mix' => '-m',
        'concat' => ''
    ];

    /**
     * Sox constructor.
     * @param string $mode Available `mix`, `concat`
     * @throws \InvalidArgumentException
     */
    public function __construct(string $mode = 'concat')
    {
        $this->verifyMode($mode);

        $this->sox = config('sox.sox');
        $this->mode = $this->modes[$mode];
        $this->inputs = collect();
    }

    /**
     *  Command string
     *
     * @return string
     * @throws SoxException
     */
    public function __toString()
    {
        return $this->getCommand();
    }

    /**
     *  Create SoX input instance
     * @param string $file
     * @return \MiragePresent\Sox\Classes\Input
     */
    public static function input(string $file)
    {
        return Input::make($file);
    }

    /**
     *  Set edit mode `concat`
     *
     * @return $this
     */
    public function concat()
    {
        $this->mode = $this->modes['concat'];

        return $this;
    }

    /**
    /**
     *  Set edit mode `concat`
     *
     * @return $this
     */
    public function mix()
    {
        $this->mode = $this->modes['mix'];

        return $this;
    }


    /**
     *  Add input file
     *
     * @param \MiragePresent\Sox\Classes\InputInterface $input File path
     * @return $this
     */
    public function addInput(\MiragePresent\Sox\Classes\InputInterface $input)
    {

        $this->inputs->push($input);

        return $this;
    }

    /**
     *  Output settings
     *
     * @param string $new_file
     * @param array $options
     * @return $this
     */
    public function saveAs(string $new_file, array $options = [])
    {
        $this->output['path'] = $new_file;
        $this->output['options'] = $options;

        return $this;
    }

    /**
     * @return string
     * @throws SoxException
     */
    public function getCommand()
    {
        /** @var  $command_format */
        $command_format = "$this->sox $this->mode inputs... output";

        /** @var string $inputs_string */
        $inputs_string = $this->convertInputs();

        /** @var string $output_string */
        $output_string = $this->convertOutput();

        return str_replace(['inputs...', 'output'], [$inputs_string, $output_string], $command_format);
    }

    /**
     * @throws SoxException
     */
    public function process()
    {
        /** @var \Symfony\Component\Process\Process $process */
        $process = new Process($this->getCommand());

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

    }


    /**
     *  Get option as string
     *
     * @return string
     * @throws SoxException
     */
    private function convertInputs()
    {
        if ($this->inputs->isEmpty()) {
            throw new SoxException('Inputs are not specified');
        }

        return collect($this->inputs)
            ->reduce(function ($string, Input $input) {
                return is_null($string) ? $input->toString() : $string . ' ' . $input->toString();
            });
    }

    /**
     * @return string
     */
    private function convertOutput()
    {
        $options_string = collect($this->output['options'])
            ->map(function ($value, $option) {
                return Option::make($option, $value);
            })
            ->filter(function (Option $option) {
                return $option->isValid();
            })
            ->implode(' ');

        return $options_string . ' ' . $this->output['path'];
    }

    /**
     *  Filter options
     *
     * @param array $options
     * @return array
     */
    private function filterOptions(array $options)
    {
        return collect($options)
            ->map(function ($value, $option) {
                return Option::make($option, $value);
            })
            ->filter(function (Option $option) {
                return $option->isValid();
            });
    }

    /**
     *  Get options string
     *
     * @param \Illuminate\Support\Collection $options
     * @return string
     */
    private function convertOptions($options)
    {
        return $options
            ->map(function (Option $option) {
                return (string)$option;
            })
            ->implode(' ');
    }

    /**
     * @param string $mode
     * @throws \InvalidArgumentException
     */
    private function verifyMode(string $mode)
    {
        if ( ! array_key_exists($mode, $this->modes) ) {
            $modes_string = "`" . implode("`, ", array_keys($this->modes)) . "`";
            $message = "The mode `$mode`` is not supported. Supported modes are: $modes_string." ;
            throw new \InvalidArgumentException( $message );
        }
    }

}