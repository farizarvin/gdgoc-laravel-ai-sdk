<?php

namespace App\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Attributes\Provider;
use Stringable;

#[Provider('gemini')]
class AssistantAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    protected string $customInstructions;
    protected bool $withTools;

    public function __construct(string $instructions = '', bool $withTools = true)
    {
        $this->customInstructions = $instructions ?: $this->defaultInstructions();
        $this->withTools = $withTools;
    }

    /**
     * Default system instructions.
     */
    protected function defaultInstructions(): string
    {
        return "You are 'Gdgoc Assistant', a helpful, creative, and friendly AI developer assistant built using the Laravel AI SDK.\n"
            . "You can help with code, design, database info, or general discussions.\n"
            . "Always use the 'GetSystemInfo' tool if the user asks about the server time, database stats (like number of users, messages), or OS platforms.\n"
            . "Keep your answers brief, informative, and formatted using Markdown.";
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return $this->customInstructions;
    }

    /**
     * Get the tools available to the agent.
     */
    public function tools(): iterable
    {
        if (! $this->withTools) {
            return [];
        }

        return [
            new \App\Tools\GetSystemInfo(),
        ];
    }
}
