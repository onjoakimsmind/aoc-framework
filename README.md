[![Advent of Code](https://github.com/onjoakimsmind/aoc/actions/workflows/aoc.yml/badge.svg)](https://github.com/onjoakimsmind/aoc/actions/workflows/aoc.yml)

# Advent of Code Wrapper (PHP)

A command-line wrapper for solving Advent of Code puzzles with automatic input fetching and organized structure.

## Setup

1. Install dependencies:

    ```bash
    composer install
    ```

2. Get your session cookie from adventofcode.com (browser dev tools → Application/Storage → Cookies)

3. Create a `.env` file from the example and add your session token:

    ```bash
    cp .env.example .env
    # Edit .env and add your AOC_SESSION token
    ```

## Usage

### Workflow

The typical workflow for solving an AoC puzzle:

1. **Create the puzzle structure** (or use fetch to do this automatically)
2. **Add test input** from the puzzle's example to `test.txt`
3. **Implement your solution** in `A.php` and `B.php`
4. **Run tests** to verify with example data
5. **Run the solution** against real input

### Command Format

All commands support the convenient `YYYY/DD` format:

```bash
php aoc <command> YYYY/DD [options]
```

Examples:

- `php aoc create 2025/5` - Create day 5 of 2025
- `php aoc fetch 2025/1` - Fetch input for day 1
- `php aoc test 2025/1` - Test day 1 (both parts)
- `php aoc test 2025/1 -p A` - Test only part A
- `php aoc run 2025/1` - Run day 1 (both parts)
- `php aoc run 2025/1 -p B` - Run only part B

### Commands

#### Create puzzle structure

Creates the directory and files for a new puzzle:

```bash
php aoc create                       # Create today's puzzle
php aoc create 2025/5                # Create specific day

# Creates:
# - app/Solutions/2025/05/A.php (Part A from template)
# - app/Solutions/2025/05/B.php (Part B from template)
# - app/Solutions/2025/05/test.txt (empty)
```

#### Fetch puzzle input

Downloads input from Advent of Code and creates puzzle structure if needed:

```bash
php aoc fetch                        # Fetch today's input
php aoc fetch 2025/5                 # Fetch specific day

# Creates:
# - app/Solutions/2025/05/input.txt (downloaded from AOC)
# - app/Solutions/2025/05/A.php (if doesn't exist)
# - app/Solutions/2025/05/B.php (if doesn't exist)
# - app/Solutions/2025/05/test.txt (if doesn't exist)
```

#### Run tests

Runs your test methods with input from `test.txt`:

```bash
php aoc test                         # Test today's puzzle (both parts)
php aoc test 2025/5                  # Test specific day (both parts)
php aoc test 2025/5 -p A             # Test specific day, part A only
php aoc test 2025/5 -p B             # Test specific day, part B only

# Reads test input from: app/Solutions/2025/05/test.txt
# Runs the test() method in A.php and/or B.php
# Shows pass/fail with colored output
```

#### Run solution

Runs your solution with real input:

```bash
php aoc run                          # Run today's puzzle (both parts)
php aoc run 2025/5                   # Run specific day (both parts)
php aoc run 2025/5 -p A              # Run specific day, part A only
php aoc run 2025/5 -p B              # Run specific day, part B only

# Reads real input from: app/Solutions/2025/05/input.txt
# Runs solve() method on classes A and/or B
# Shows results with timing
```

## File Structure

```
aoc/
├── aoc                       # Main CLI script
├── .env                      # Environment configuration (gitignored)
├── .env.example              # Example environment file
├── app/
│   ├── Commands/             # Command classes
│   ├── Testing/              # Testing framework
│   └── Solutions/            # All puzzle solutions
│       └── YYYY/             # Year directories (e.g., 2025/)
│           └── DD/           # Day directories (e.g., 01/, 02/)
│               ├── A.php     # Part A solution
│               ├── B.php     # Part B solution
│               ├── input.txt # Puzzle input (from AOC)
│               └── test.txt  # Test input (from examples)
├── bootstrap/                # Bootstrap files
└── stubs/
    └── Puzzle.php.stub       # Template for new puzzles
```

## Step-by-Step Example

Here's a complete example for solving Day 1 of 2025:

### 1. Create puzzle structure

```bash
php aoc create 2025/1
```

This creates:

- `app/Solutions/2025/01/A.php` (Part A template)
- `app/Solutions/2025/01/B.php` (Part B template)
- `app/Solutions/2025/01/test.txt` (empty - for test input)

### 2. Add test input

Copy the example input from the puzzle description to `app/Solutions/2025/01/test.txt`:

```bash
# Example: if the puzzle shows this test case:
echo "1abc2
pqr3stu8vwx
a1b2c3d4e5f
treb7uchet" > app/Solutions/2025/01/test.txt
```

### 3. Implement solution

Edit `app/Solutions/2025/01/A.php` and implement the `solve()` method:

```php
<?php

declare(strict_types=1);

namespace AoC\Solutions\Y2025\D01;

use Aoc\Testing\TestRunner;

class A
{
    private string $inputData;

    public function __construct(string $inputData)
    {
        $this->inputData = trim($inputData);
    }

    public function solve(): int
    {
        // Your solution for part A
        return $result;
    }

    public function test(TestRunner $t, string $testInput): void
    {
        $t->assertEquals(142, $this->solve(), 'Part A');
    }
}
```

Similarly implement `B.php` for part B.

### 4. Run tests

```bash
# Test both parts
php aoc test 2025/1
# ✓ All tests passed! (2/2)

# Or test individual parts
php aoc test 2025/1 -p A
php aoc test 2025/1 -p B
```

### 5. Fetch real input and run

```bash
php aoc fetch 2025/1          # Downloads input.txt
php aoc run 2025/1            # Run your solution
# Part A: 54388 (took 2.45ms)
# Part B: 53515 (took 3.12ms)
```

## Solution Format

Each day's solution consists of two separate files (`A.php` and `B.php`) for parts A and B:

### Part A (app/Solutions/2025/01/A.php)

```php
<?php

declare(strict_types=1);

namespace AoC\Solutions\Y2025\D01;

use Aoc\Testing\TestRunner;

class A
{
    private string $inputData;

    public function __construct(string $inputData)
    {
        $this->inputData = trim($inputData);
    }

    public function solve(): int
    {
        // Your solution for part A
        return $result;
    }

    public function test(TestRunner $t, string $testInput): void
    {
        // Tests for part A
        $t->assertEquals(expected, $this->solve(), 'Part A description');
    }
}
```

### Part B (app/Solutions/2025/01/B.php)

```php
<?php

declare(strict_types=1);

namespace AoC\Solutions\Y2025\D01;

use Aoc\Testing\TestRunner;

class B
{
    private string $inputData;

    public function __construct(string $inputData)
    {
        $this->inputData = trim($inputData);
    }

    public function solve(): int
    {
        // Your solution for part B
        return $result;
    }

    public function test(TestRunner $t, string $testInput): void
    {
        // Tests for part B
        $t->assertEquals(expected, $this->solve(), 'Part B description');
    }
}
```

**Namespace Convention:**

- Format: `AoC\Solutions\Y{YYYY}\D{DD}`
- Examples:
  - `AoC\Solutions\Y2025\D01` for 2025/01
  - `AoC\Solutions\Y2025\D25` for 2025/25
  - `AoC\Solutions\Y2024\D15` for 2024/15
- Namespaces are automatically generated by `create` and `fetch` commands
- Each part has its own file for better organization and independent testing

## Testing Framework

### Test Input Files

Tests read input from the `test.txt` file in each day's directory:

```
app/Solutions/2025/01/
├── A.php        # Part A solution
├── B.php        # Part B solution
├── input.txt    # Real puzzle input (from AOC)
└── test.txt     # Test input (from puzzle examples)
```

### TestRunner API

The `TestRunner` class provides assertion methods:

```php
public function test(TestRunner $t, string $testInput): void {
    // Compare values
    $t->assertEquals(expected, actual, 'Description');

    // Boolean assertions
    $t->assertTrue($value > 0, 'Value should be positive');
    $t->assertFalse(empty($data), 'Data should not be empty');
}
```

### Test Output

**When all tests pass:**

```
Running tests for 2025 day 1 (part A)...
✓ All tests passed! (1/1)

Running tests for 2025 day 1 (part B)...
✓ All tests passed! (1/1)

=== Test Summary ===
Total: 2 test(s), 2 passed, 0 failed
```

**When tests fail:**

```
Running tests for 2025 day 1 (part A)...
✗ 1 test(s) failed! (0/1 passed)

Failure 1: Part A calculation
  Expected: 142
  Actual:   100
```

### Separate Testing

Each part is in its own file with its own `test()` method, allowing independent testing:

```bash
php aoc test 2025/1      # Runs both A.php and B.php tests
php aoc test 2025/1 -p A # Only runs A.php tests
php aoc test 2025/1 -p B # Only runs B.php tests
```

This is useful when:

- Part A is complete but Part B isn't ready
- You want to focus on debugging one specific part
- Parts have different test scenarios
- You're iterating on one part without affecting the other

### Tips

- Add multiple assertions to test edge cases
- Use descriptive messages for each assertion
- Test with the exact examples from the puzzle description
- Run tests frequently while developing your solution
- Test each part separately as you develop them
- Use `dump()` and `dd()` (Laravel helpers) for debugging
