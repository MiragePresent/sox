<?php

namespace MiragePresent\Sox;

use MiragePresent\Sox\Classes\InputInterface;

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
     * @return \MiragePresent\Sox\SoxInterface
     */
    public static function concat();

    /**
     *  Set mix mode
     *
     * @return \MiragePresent\Sox\SoxInterface
     */
    public static function mix();

    /**
     *  Add input file
     *
     * @param \MiragePresent\Sox\Classes\InputInterface $input File path
     * @return \MiragePresent\Sox\SoxInterface
     */
    public function addInput(InputInterface $input);

    /**
     *  Output the result
     *
     * @param string $new_file Path to new file
     * @param string $options Output options
     * @return \MiragePresent\Sox\SoxInterface
     */
    public function saveAs(string $new_file, string $options = '');

}