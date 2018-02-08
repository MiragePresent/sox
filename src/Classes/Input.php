<?php

namespace MiragePresent\Sox\Classes;

class Input implements InputInterface
{

    /** @var string $sox Sox command */
    protected $sox;

    /** @var string $file */
    protected $file;

    /** @var \Illuminate\Support\Collection */
    protected $options;

    /** @var \Illuminate\Support\Collection */
    protected $effects;

    /** @var int $volumeFactor */
    protected $volumeFactor = 1.0;

    protected $cutPoints = [];

    /**
     *  Input constructor.
     * @param string $file
     * @param array $options
     * @param array $effects
     */
    public function __construct(string $file, $options = [], $effects = [])
    {
        $this->verifyFile($file);

        $this->file = $file;
        $this->sox = config('sox.sox');

        $this->options = collect($options)
            ->map(function ($value, $option) {
                return Option::make($option, $value);
            });

        $this->effects = collect($effects)
            ->map(function ($settings, $effect) {
                return Effect::make($effect, $settings);
            });
    }

    /**
     *  Static constructor
     * @param string $file
     * @param array $options
     * @param array $effects
     * @return static
     */
    public static function make(string $file, $options = [], $effects = [])
    {
        return new static($file, $options, $effects);
    }

    /**
     *  Input string
     *
     * @return string
     */
    public function toString()
    {
        $string = "\"|$this->sox ";

        if ($this->volumeFactor !== 1.0 && $this->volumeFactor !== 1) {
            $string .= "-v $this->volumeFactor ";
        }

        $string .= $this->file . ' -p';

        if (!empty($this->cutPoints)) {
            if (count($this->cutPoints) == 1) {
                $string .= ' trim ' . $this->cutPoints[0];
            } else {
                $string .= ' trim ' . $this->cutPoints[0] . ' ' . $this->cutPoints[1];
            }
        }

        return $string . "\"";
    }


    /*
     * =====================================================================================
     *                                  Edit methods
     * =====================================================================================
     */


    /**
     *  Set/Get volume factor
     * @param null|float $factor
     * @return float|$this
     */
    public function volume($factor = null)
    {
        if (is_null($factor)) {
            return $this->volumeFactor;
        } elseif (is_int($factor) || is_float($factor)) {
            $this->volumeFactor = $factor;
            return $this;
        }

        throw new \InvalidArgumentException('Volume factor is invalid');
    }

    /**
     *  Set/Get cut points
     *
     * @param null $start
     * @param null $duration
     * @return $this|array
     * @throws \InvalidArgumentException
     */
    public function cut($start = null, $duration = null)
    {
        if (is_null($start) && is_null($duration)) {
            return $this->cutPoints;
        } elseif (!is_null($start) && is_null($duration)) {
            $this->cutPoints = [$start];
            return $this;
        } else {
            $this->cutPoints = [$start, $duration];
            return $this;
        }
    }


    /*
     *
     *
     *
     */

    /**
     * @param string $file
     * @throws \InvalidArgumentException
     */
    private function verifyFile(string $file) {
        if ( ! file_exists( $file ) ) {
            throw new \InvalidArgumentException( "Input file was not found" );
        } elseif ( ! is_file( $file ) ) {
            throw new \InvalidArgumentException( "Input is directory" );
        }
    }

}