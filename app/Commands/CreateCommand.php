<?php

declare(strict_types=1);

namespace Aoc\Commands;

use Codedungeon\PHPCliColors\Color;

class CreateCommand extends Command
{
    public function execute(): int
    {
        $dayDir = $this->getDayDirectory();

        if (is_dir($dayDir)) {
            echo Color::YELLOW . "Warning: " . Color::RESET . "Directory already exists: {$dayDir}\n";
        } else {
            mkdir($dayDir, 0755, true);
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created directory: ";
            echo Color::LIGHT_BLUE . $dayDir . Color::RESET;
            echo "\n";
        }

        // Create A.php
        $pathA = $dayDir . '/A.php';
        if (!file_exists($pathA)) {
            $this->createPartFile($pathA, 'A');
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created file: ";
            echo Color::LIGHT_BLUE . $pathA . Color::RESET;
            echo "\n";
        }

        // Create B.php
        $pathB = $dayDir . '/B.php';
        if (!file_exists($pathB)) {
            $this->createPartFile($pathB, 'B');
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created file: ";
            echo Color::LIGHT_BLUE . $pathB . Color::RESET;
            echo "\n";
        }

        // Create test input file
        $testInputPath = $this->getTestInputPath();
        if (!file_exists($testInputPath)) {
            file_put_contents($testInputPath, "");
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created test file: ";
            echo Color::LIGHT_BLUE . $testInputPath . Color::RESET;
            echo "\n";
        }

        return 0;
    }

    private function createPartFile(string $path, string $part): void
    {
        $stubPath = dirname(__DIR__, 2) . '/stubs/Puzzle.php.stub';
        $content = file_get_contents($stubPath);
        $content = str_replace('{PUZZLE}', $part, $content);
        $content = str_replace('{YEAR}', (string)$this->year, $content);
        $content = str_replace('{DAY}', sprintf('%02d', $this->day), $content);
        file_put_contents($path, $content);
    }
}
