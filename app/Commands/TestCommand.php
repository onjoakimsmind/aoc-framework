<?php

declare(strict_types=1);

namespace AoC\Commands;

use Codedungeon\PHPCliColors\Color;

class TestCommand extends Command
{
    private ?string $part;

    public function __construct(int $year, int $day, ?string $part = null)
    {
        parent::__construct($year, $day);
        $this->part = $part;
    }

    public function execute(): int
    {
        $dayDir = $this->getDayDirectory();
        $testInputPath = $this->getTestInputPath();

        if (!file_exists($testInputPath)) {
            echo Color::RED . "Error: " . Color::RESET . "Test input file not found at {$testInputPath}\n";
            echo "Create a file with test input at: " . Color::YELLOW . $testInputPath . Color::RESET . "\n";
            return 1;
        }

        $testInput = file_get_contents($testInputPath);
        $namespace = sprintf('AoC\\Solutions\\Y%d\\D%02d', $this->year, $this->day);
        $parts = $this->part ? [$this->part] : ['A', 'B'];
        $totalPassed = 0;
        $totalFailed = 0;
        $hasAnyFailures = false;

        try {
            foreach ($parts as $part) {
                echo Color::CYAN . "Running tests for {$this->year} day {$this->day} (part {$part})...\n" . Color::RESET;
                $partFile = $dayDir . "/{$part}.php";
                if (!file_exists($partFile)) {
                    echo Color::RED . "Error: " . Color::RESET . "File not found: {$partFile}\n";
                    return 1;
                }

                require_once $partFile;

                $className = $namespace . '\\' . $part;
                if (!class_exists($className)) {
                    echo Color::RED . "Error: " . Color::RESET . "Class {$className} not found\n";
                    return 1;
                }
                $instance = new $className($testInput);
                if (!method_exists($instance, 'test')) {
                    echo Color::RED . "Error: " . Color::RESET . "test() method not found in class {$part}\n";
                    return 1;
                }
                // Create separate test runner for each part
                $runner = new \AoC\Testing\TestRunner();
                $instance->test($runner, $testInput);
                $runner->printResults();
                $totalPassed += $runner->getPassed();
                $totalFailed += $runner->getFailed();
                if ($runner->hasFailures()) {
                    $hasAnyFailures = true;
                }
                echo "\n";
            }
            // Print summary if running both parts
            if (count($parts) > 1) {
                $total = $totalPassed + $totalFailed;
                echo Color::BOLD . Color::CYAN . "=== Test Summary ===" . Color::RESET . "\n";
                echo "Total: {$total} test(s), ";
                echo Color::GREEN . "{$totalPassed} passed" . Color::RESET . ", ";
                echo Color::RED . "{$totalFailed} failed" . Color::RESET . "\n";
            }
            return $hasAnyFailures ? 1 : 0;
        } catch (\Throwable $e) {
            echo Color::BOLD . Color::RED . "✗ Test error: " . Color::RESET . $e->getMessage() . "\n";
            echo Color::DARK_GRAY . "  in {$e->getFile()}:{$e->getLine()}\n" . Color::RESET;
            return 1;
        }
    }
}
