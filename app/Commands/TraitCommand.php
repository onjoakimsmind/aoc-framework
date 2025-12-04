<?php

namespace AoC\Commands;

class TraitCommand
{
    private string $year;
    private string $day;
    private string $name;

    public function __construct(string $date, string $name)
    {
        [$this->year, $this->day] = $this->parseDate($date);
        $this->name = $name;
    }

    private function parseDate(string $date): array
    {
        if (preg_match('/^(\d{4})\/(\d{1,2})$/', $date, $matches)) {
            return [$matches[1], str_pad($matches[2], 2, '0', STR_PAD_LEFT)];
        }
        throw new \InvalidArgumentException("Invalid date format. Use YYYY/DD");
    }

    public function execute(): void
    {
        $solutionDir = __DIR__ . "/../../app/Solutions/{$this->year}/{$this->day}";

        if (!is_dir($solutionDir)) {
            echo "Error: Solution directory does not exist: {$solutionDir}\n";
            exit(1);
        }

        $traitFile = "{$solutionDir}/{$this->name}.php";

        if (file_exists($traitFile)) {
            echo "Error: Trait file already exists: {$traitFile}\n";
            exit(1);
        }

        $stubContent = file_get_contents(__DIR__ . '/../../stubs/Trait.php.stub');
        $traitContent = str_replace(
            ['{YEAR}', '{DAY}', '{Name}'],
            [$this->year, $this->day, $this->name],
            $stubContent
        );

        file_put_contents($traitFile, $traitContent);

        echo "Created trait: {$traitFile}\n";
    }
}
