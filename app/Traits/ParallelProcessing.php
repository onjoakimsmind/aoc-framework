<?php

namespace AoC\Traits;

trait ParallelProcessing
{
    /**
     * Process items in parallel using pcntl_fork (Unix only, falls back to sequential)
     *
     * @param array $items Items to process
     * @param callable $callback Function to apply to each item
     * @param int $workers Number of parallel workers (default: CPU cores)
     * @return array Results in the same order as input items
     */
    protected function parallel(array $items, callable $callback, int $workers = null): array
    {
        if (!function_exists('pcntl_fork') || !function_exists('pcntl_waitpid')) {
            // Fallback to sequential processing if pcntl is not available
            return array_map($callback, $items);
        }

        if ($workers === null) {
            $workers = $this->getCpuCores();
        }

        $workers = max(1, min($workers, count($items)));

        if ($workers === 1 || count($items) === 1) {
            return array_map($callback, $items);
        }

        $chunks = array_chunk($items, (int) ceil(count($items) / $workers), true);
        $tmpFiles = [];
        $pids = [];

        foreach ($chunks as $chunkIndex => $chunk) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'parallel_');
            $tmpFiles[$chunkIndex] = $tmpFile;

            $pid = pcntl_fork();

            if ($pid === -1) {
                throw new \RuntimeException("Failed to fork process");
            } elseif ($pid === 0) {
                // Child process
                $chunkResults = [];
                foreach ($chunk as $key => $item) {
                    $chunkResults[$key] = $callback($item);
                }
                file_put_contents($tmpFile, serialize($chunkResults));
                exit(0);
            } else {
                // Parent process - store PID
                $pids[] = $pid;
            }
        }

        // Wait for all children to complete
        foreach ($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        // Collect results
        $results = [];
        foreach ($tmpFiles as $tmpFile) {
            if (file_exists($tmpFile)) {
                $chunkResults = unserialize(file_get_contents($tmpFile));
                if ($chunkResults !== false) {
                    $results = array_merge($results, $chunkResults);
                }
                unlink($tmpFile);
            }
        }

        return $results;
    }

    /**
     * Map items in parallel (alias for parallel)
     *
     * @param array $items Items to process
     * @param callable $callback Function to apply to each item
     * @param int $workers Number of parallel workers
     * @return array Results in the same order as input items
     */
    protected function parallelMap(array $items, callable $callback, int $workers = null): array
    {
        return $this->parallel($items, $callback, $workers);
    }

    /**
     * Get the number of CPU cores
     *
     * @return int Number of CPU cores
     */
    private function getCpuCores(): int
    {
        $cores = 1;

        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $cores = count($matches[0]);
        } elseif (DIRECTORY_SEPARATOR === '\\') {
            // Windows
            $process = @popen('wmic cpu get NumberOfCores', 'rb');
            if ($process !== false) {
                fgets($process);
                $cores = (int) fgets($process);
                pclose($process);
            }
        } elseif (PHP_OS === 'Darwin') {
            // macOS
            $process = @popen('sysctl -n hw.ncpu', 'rb');
            if ($process !== false) {
                $cores = (int) fgets($process);
                pclose($process);
            }
        }

        return max(1, $cores);
    }
}
