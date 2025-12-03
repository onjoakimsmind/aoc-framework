<?php

declare(strict_types=1);

use Clue\Commander\Router;
use Aoc\Commands\CreateCommand;
use Aoc\Commands\FetchCommand;
use Aoc\Commands\RunCommand;
use Aoc\Commands\TestCommand;
use Codedungeon\PHPCliColors\Color;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registerCommands();
    }

    private function parseDate(array $args): array
    {
        // Check for YYYY/DD format as first argument
        foreach ($args as $key => $value) {
            if (is_string($value) && preg_match('#^(\d{4})/(\d{1,2})$#', $value, $matches)) {
                return [(int)$matches[1], (int)$matches[2]];
            }
        }

        // Fall back to flags or defaults
        $year = $args['year'] ?? 2025;
        $day = $args['day'] ?? (int)date('d');

        return [$year, $day];
    }

    private function registerCommands(): void
    {
        // Format: create YYYY/DD or create -y YYYY -d DD or create
        $this->router->add('create [<date>] [--year|-y <year:uint>] [--day|-d <day:uint>]', function (array $args) {
            [$year, $day] = $this->parseDate($args);

            $cmd = new CreateCommand($year, $day);
            return $cmd->execute();
        });

        $this->router->add('fetch [<date>] [--year|-y <year:uint>] [--day|-d <day:uint>]', function (array $args) {
            [$year, $day] = $this->parseDate($args);

            $cmd = new FetchCommand($year, $day);
            return $cmd->execute();
        });

        $this->router->add('run [<date>] [--year|-y <year:uint>] [--day|-d <day:uint>] [--part|-p <part>]', function (array $args) {
            [$year, $day] = $this->parseDate($args);
            $part = isset($args['part']) ? strtoupper($args['part']) : null;

            if ($part !== null && !in_array($part, ['A', 'B'])) {
                echo Color::RED . "Error: " . Color::RESET . "Part must be A or B\n";
                return 1;
            }

            $cmd = new RunCommand($year, $day, $part);
            return $cmd->execute();
        });

        $this->router->add('test [<date>] [--year|-y <year:uint>] [--day|-d <day:uint>] [--part|-p <part>]', function (array $args) {
            [$year, $day] = $this->parseDate($args);
            $part = isset($args['part']) ? strtoupper($args['part']) : null;

            if ($part !== null && !in_array($part, ['A', 'B'])) {
                echo Color::RED . "Error: " . Color::RESET . "Part must be A or B\n";
                return 1;
            }

            $cmd = new TestCommand($year, $day, $part);
            return $cmd->execute();
        });

        $app = $this;
        $this->router->add('[--help|-h]', function () use ($app) {
            $app->showHelp();
            return 0;
        });

        $this->router->add('', function () use ($app) {
            $app->showHelp();
            return 1;
        });
    }

    public function handleCommand(): int
    {
        try {
            $result = $this->router->execArgv();
            return is_int($result) ? $result : 0;
        } catch (\Exception $e) {
            echo Color::RED . "Error: " . Color::RESET . $e->getMessage() . "\n";
            return 1;
        }
    }


    public function showHelp(): void
    {
        echo Color::BOLD . Color::LIGHT_CYAN . "Advent of Code CLI\n\n" . Color::RESET;
        
        echo Color::YELLOW . "Usage:\n" . Color::RESET;
        echo "  php aoc <command> [YYYY/DD] [options]\n\n";
        
        echo Color::YELLOW . "Options:\n" . Color::RESET;
        echo "  " . Color::GREEN . "-p, --part <A|B>" . Color::RESET . "  Part to run (for 'run' and 'test' commands)\n\n";
        
        echo Color::YELLOW . "Commands:\n" . Color::RESET;
        echo "  " . Color::LIGHT_PURPLE . "create [YYYY/DD]" . Color::RESET . "    Create puzzle directory and file\n";
        echo "  " . Color::LIGHT_PURPLE . "fetch [YYYY/DD]" . Color::RESET . "     Fetch puzzle input from AOC\n";
        echo "  " . Color::LIGHT_PURPLE . "run [YYYY/DD]" . Color::RESET . "       Run puzzle solver\n";
        echo "  " . Color::LIGHT_PURPLE . "test [YYYY/DD]" . Color::RESET . "      Run puzzle tests\n\n";
        
        echo Color::YELLOW . "Date Format:\n" . Color::RESET;
        echo "  " . Color::CYAN . "YYYY/DD" . Color::RESET . "             Year and day (e.g., 2024/5 for Dec 5, 2024)\n";
        echo "                      Day can be single digit (5) or zero-padded (05)\n";
        echo "                      If omitted, uses today's date (defaults to 2025)\n\n";
        
        echo Color::YELLOW . "Examples:\n" . Color::RESET;
        echo "  php aoc create                 " . Color::DARK_GRAY . "# Create today's puzzle\n" . Color::RESET;
        echo "  php aoc create 2024/5          " . Color::DARK_GRAY . "# Create Dec 5, 2024 (2024/05/)\n" . Color::RESET;
        echo "  php aoc fetch 2024/5           " . Color::DARK_GRAY . "# Fetch input for Dec 5, 2024\n" . Color::RESET;
        echo "  php aoc run 2024/5             " . Color::DARK_GRAY . "# Run both parts for Dec 5\n" . Color::RESET;
        echo "  php aoc run 2024/5 -p A        " . Color::DARK_GRAY . "# Run only part A\n" . Color::RESET;
        echo "  php aoc test 2024/5            " . Color::DARK_GRAY . "# Run tests for both parts\n" . Color::RESET;
        echo "  php aoc test 2024/5 -p A       " . Color::DARK_GRAY . "# Run only part A tests\n" . Color::RESET;
    }
}

return new App();
