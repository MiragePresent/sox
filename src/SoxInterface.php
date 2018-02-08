<?php

namespace MiragePresent\Sox;


interface SoxInterface
{

    /**
     *  SoxInterface constructor.
     *
     * @param string $mode Edit mode
     */
    public function __construct(string $mode = '');

    /**
     *  Set concat mode
     *
     * @return $this
     */
    public function concat();

    /**
     *  Set mix mode
     *
     * @return $this
     */
    public function mix();

    /**
     *  Add input file
     *
     * @param \MiragePresent\Sox\Classes\InputInterface $input File path
     * @return $this
     */
    public function addInput(\MiragePresent\Sox\Classes\InputInterface $input);

    /**
     *  Output the result
     *
     * @param string $new_file Path to new file
     * @param array $options Output options
     * @return $this
     */
    public function saveAs(string $new_file, array $options = []);

}