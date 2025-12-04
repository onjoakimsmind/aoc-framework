<?php

declare(strict_types=1);

namespace AoC\Commands;

abstract class Command
{
    protected int $year;
    protected int $day;

    public function __construct(int $year, int $day)
    {
        $this->year = $year;
        $this->day = $day;
    }

    abstract public function execute(): int;

    protected function getPuzzlePath(): string
    {
        return sprintf('%s/%d/%02d/puzzle.php', dirname(__DIR__, 2), $this->year, $this->day);
    }

    protected function getInputPath(): string
    {
        return sprintf('%s/app/Solutions/%d/%02d/input.txt', dirname(__DIR__, 2), $this->year, $this->day);
    }

    protected function getDayDirectory(): string
    {
        return sprintf('%s/app/Solutions/%d/%02d', dirname(__DIR__, 2), $this->year, $this->day);
    }

    protected function getTestInputPath(): string
    {
        return sprintf('%s/app/Solutions/%d/%02d/test.txt', dirname(__DIR__, 2), $this->year, $this->day);
    }
}
