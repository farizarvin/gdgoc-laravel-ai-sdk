<?php

namespace App\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use Illuminate\Support\Facades\DB;

class GetSystemInfo implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Gets current server details including time, OS platform, and SQLite database stats.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $time = now()->toDateTimeString();
        $os = PHP_OS_FAMILY;
        
        // Count database tables stats
        $userCount = 0;
        try {
            $userCount = DB::table('users')->count();
        } catch (\Throwable $e) {
            // Table might not exist yet
        }

        $msgCount = 0;
        try {
            $msgCount = DB::table('agent_conversation_messages')->count();
        } catch (\Throwable $e) {
            // Table might not exist yet
        }

        return "Server Time: {$time} | OS: {$os} | Users in DB: {$userCount} | Messages stored: {$msgCount}";
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return []; // No arguments required
    }
}
