<?php

use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('dump')) {
    /**
     * Dump the passed variables and continue execution.
     *
     * @param  mixed  ...$vars
     * @return void
     */
    function dump(...$vars)
    {
        foreach ($vars as $var) {
            VarDumper::dump($var);
        }
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed  ...$vars
     * @return never
     */
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            VarDumper::dump($var);
        }

        exit(1);
    }
}
