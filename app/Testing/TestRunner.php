<?php

declare(strict_types=1);

namespace Aoc\Testing;

use Codedungeon\PHPCliColors\Color;

class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected === $actual) {
            $this->passed++;
        } else {
            $this->failed++;
            $this->failures[] = [
                'message' => $message ?: 'Assertion failed',
                'expected' => $expected,
                'actual' => $actual,
            ];
        }
    }

    public function assertTrue(bool $condition, string $message = ''): void
    {
        $this->assertEquals(true, $condition, $message);
    }

    public function assertFalse(bool $condition, string $message = ''): void
    {
        $this->assertEquals(false, $condition, $message);
    }

    public function getPassed(): int
    {
        return $this->passed;
    }

    public function getFailed(): int
    {
        return $this->failed;
    }

    public function hasFailures(): bool
    {
        return $this->failed > 0;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function printResults(): void
    {
        $total = $this->passed + $this->failed;
        if ($this->failed === 0) {
            echo Color::BOLD . Color::GREEN . "✓ All tests passed! " . Color::RESET;
            echo "({$this->passed}/{$total})\n";
        } else {
            echo Color::BOLD . Color::RED . "✗ {$this->failed} test(s) failed! " . Color::RESET;
            echo "({$this->passed}/{$total} passed)\n\n";
            foreach ($this->failures as $i => $failure) {
                echo Color::RED . "Failure " . ($i + 1) . ": " . Color::RESET . $failure['message'] . "\n";
                echo "  Expected: " . Color::GREEN . $this->formatValue($failure['expected']) . Color::RESET . "\n";
                echo "  Actual:   " . Color::RED . $this->formatValue($failure['actual']) . Color::RESET . "\n\n";
            }
        }
    }

    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        return (string) $value;
    }
}
