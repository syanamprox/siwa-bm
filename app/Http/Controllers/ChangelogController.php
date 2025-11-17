<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class ChangelogController extends Controller
{
    /**
     * Display changelog page.
     */
    public function indexView()
    {
        return view('admin.changelog.index');
    }

    /**
     * Get git commit history.
     */
    public function index(): JsonResponse
    {
        try {
            // Get git log information
            $gitPath = base_path('.git');

            if (!File::exists($gitPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Git repository not found'
                ], 404);
            }

            // Get git log with formatting
            $command = 'cd ' . base_path() . ' && git log --oneline --pretty=format:"%h|%an|%ad|%s" --date=format:"%Y-%m-%d %H:%M:%S" -50';

            $output = shell_exec($command);
            $lines = array_filter(explode("\n", $output));

            $commits = [];

            foreach ($lines as $line) {
                $parts = explode('|', $line, 4);
                if (count($parts) >= 4) {
                    $commits[] = [
                        'hash' => $parts[0],
                        'author' => $parts[1],
                        'date' => $parts[2],
                        'message' => $parts[3],
                        'short_hash' => substr($parts[0], 0, 7)
                    ];
                }
            }

            // Get current branch info
            $branchCommand = 'cd ' . base_path() . ' && git branch --show-current';
            $currentBranch = trim(shell_exec($branchCommand));

            // Get latest tag
            $tagCommand = 'cd ' . base_path() . ' && git describe --tags --abbrev=0';
            $latestTag = trim(shell_exec($tagCommand));

            return response()->json([
                'success' => true,
                'data' => [
                    'current_branch' => $currentBranch,
                    'latest_tag' => $latestTag ?: 'No tags',
                    'commits' => $commits,
                    'total_commits' => count($commits)
                ],
                'message' => 'Changelog data loaded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load changelog: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed commit information.
     */
    public function show($hash): JsonResponse
    {
        try {
            // Get detailed commit info
            $command = 'cd ' . base_path() . " && git show --pretty=format:\"%h|%an|%ad|%s|%b\" --name-only {$hash}";
            $output = shell_exec($command);

            $lines = explode("\n", $output);
            $headerLine = array_shift($lines);

            $parts = explode('|', $headerLine, 5);
            if (count($parts) < 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commit not found'
                ], 404);
            }

            // Parse files changed
            $files = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && !str_starts_with($line, 'diff') && !str_starts_with($line, 'index') &&
                    !str_starts_with($line, '---') && !str_starts_with($line, '+++') &&
                    !str_starts_with($line, '@@') && !str_contains($line, '+') && !str_contains($line, '-')) {
                    $files[] = $line;
                }
            }

            // Get stats
            $statsCommand = 'cd ' . base_path() . " && git show --stat --format=\"\" {$hash}";
            $statsOutput = shell_exec($statsCommand);

            return response()->json([
                'success' => true,
                'data' => [
                    'hash' => $parts[0],
                    'author' => $parts[1],
                    'date' => $parts[2],
                    'subject' => $parts[3],
                    'body' => $parts[4],
                    'files' => array_unique($files),
                    'stats' => trim($statsOutput)
                ],
                'message' => 'Commit detail loaded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load commit details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system information
     */
    public function systemInfo(): JsonResponse
    {
        try {
            $laravelVersion = app()->version();
            $phpVersion = PHP_VERSION;

            // Get git info
            $gitPath = base_path('.git');
            $gitInfo = [];

            if (File::exists($gitPath)) {
                $gitInfo['current_branch'] = trim(shell_exec('cd ' . base_path() . ' && git branch --show-current'));
                $gitInfo['latest_commit'] = trim(shell_exec('cd ' . base_path() . ' && git log -1 --pretty=format:"%h - %s"'));
                $gitInfo['total_commits'] = trim(shell_exec('cd ' . base_path() . ' && git rev-list --count HEAD'));
                $gitInfo['last_update'] = trim(shell_exec('cd ' . base_path() . ' && git log -1 --format="%cd" --date=iso'));
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'laravel_version' => $laravelVersion,
                    'php_version' => $phpVersion,
                    'environment' => config('app.env'),
                    'git_info' => $gitInfo,
                    'app_url' => config('app.url'),
                    'timezone' => config('app.timezone')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load system info: ' . $e->getMessage()
            ], 500);
        }
    }
}