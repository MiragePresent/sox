<?php

namespace MiragePresent\Sox\Classes;

class Input implements InputInterface
{

    /** @var string $sox Sox command */
    protected $sox;

    /** @var string $file */
    protected $file;

    /** @var int|float|null $volumeFactor */
    protected $volumeFactor = null;

    protected $cutPoints = [];

    /** @var bool Pipe mode */
    protected $isPipe = false;

    /**
     *  Input constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->sox = config('sox.sox');
    }

    /**
     *  Static constructor
     * @param string $file
     * @return static
     */
    public static function make(string $file)
    {
        return new static($file);
    }

    /**
     *  Input string
     *
     * @return string
     */
    public function toString()
    {
        /** @var string $pipe_cmd Pipe command format */
        $pipe_cmd = "\"|$this->sox %s $this->file -p %s\"";

        /** @var string $volume Volume settings */
        $volume = '';

        /** @var string $trim Cutting settings */
        $trim = '';

        if (!is_null($this->volumeFactor)) {
            $volume = "-v $this->volumeFactor ";
        }

        if (!empty($this->cutPoints)) {
            if (count($this->cutPoints) == 1) {
                $trim = ' trim ' . $this->cutPoints[0];
            } else {
                $trim = ' trim ' . $this->cutPoints[0] . ' ' . $this->cutPoints[1];
            }
        }

        if ($this->isPipe) {
            return sprintf($pipe_cmd, $volume, $trim);
        }

        return " $volume $this->file $trim";
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

    public function pipe($enabled = true)
    {
        $this->isPipe = $enabled;

        return $this;
    }

}