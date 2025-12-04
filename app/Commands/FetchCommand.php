<?php

declare(strict_types=1);

namespace AoC\Commands;

use Codedungeon\PHPCliColors\Color;
use GuzzleHttp\Client;
use Dotenv\Dotenv;

class FetchCommand extends Command
{
    private function createPartFile(string $path, string $part): void
    {
        $stubPath = dirname(__DIR__, 2) . '/stubs/Puzzle.php.stub';
        $content = file_get_contents($stubPath);

        // Replace placeholders
        $content = str_replace('{PUZZLE}', $part, $content);
        $content = str_replace('{YEAR}', (string)$this->year, $content);
        $content = str_replace('{DAY}', sprintf('%02d', $this->day), $content);

        file_put_contents($path, $content);
    }

    public function execute(): int
    {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $session = $_ENV['AOC_SESSION'] ?? null;
        if (!$session) {
            echo Color::RED . "Error: " . Color::RESET . "AOC_SESSION not found in .env file\n";
            return 1;
        }

        $dayDir = $this->getDayDirectory();
        if (!is_dir($dayDir)) {
            mkdir($dayDir, 0755, true);
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created directory: ";
            echo Color::LIGHT_BLUE . $dayDir . Color::RESET;
            echo "\n";
        }

        // Create puzzle files if they don't exist
        $pathA = $dayDir . '/A.php';
        if (!file_exists($pathA)) {
            $this->createPartFile($pathA, 'A');
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created file: ";
            echo Color::LIGHT_BLUE . $pathA . Color::RESET;
            echo "\n";
        }

        $pathB = $dayDir . '/B.php';
        if (!file_exists($pathB)) {
            $this->createPartFile($pathB, 'B');
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created file: ";
            echo Color::LIGHT_BLUE . $pathB . Color::RESET;
            echo "\n";
        }

        // Create test input file if it doesn't exist
        $testInputPath = $this->getTestInputPath();
        if (!file_exists($testInputPath)) {
            file_put_contents($testInputPath, "");
            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Created test file: ";
            echo Color::LIGHT_BLUE . $testInputPath . Color::RESET;
            echo "\n";
        }

        $inputPath = $this->getInputPath();
        if (file_exists($inputPath)) {
            echo Color::YELLOW . "Warning: " . Color::RESET . "Input file already exists: {$inputPath}\n";
            return 0;
        }

        // Fetch input from Advent of Code
        $url = "https://adventofcode.com/{$this->year}/day/{$this->day}/input";
        try {
            $client = new Client();
            $response = $client->get($url, [
                'headers' => [
                    'Cookie' => "session={$session}",
                    'User-Agent' => 'github.com/your-username/aoc-cli'
                ]
            ]);

            $input = $response->getBody()->getContents();
            file_put_contents($inputPath, $input);

            echo Color::GREEN . "✓ " . Color::RESET;
            echo "Fetched input: ";
            echo Color::LIGHT_BLUE . $inputPath . Color::RESET;
            echo "\n";

            return 0;
        } catch (\Exception $e) {
            echo Color::RED . "Error: " . Color::RESET . "Failed to fetch input: " . $e->getMessage() . "\n";
            return 1;
        }
    }
}
