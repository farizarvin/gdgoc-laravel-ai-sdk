<?php

namespace App\Livewire;

use App\Agents\AssistantAgent;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\ConversationStore;

class Chatbot extends Component
{
    // ─── Chat State ──────────────────────────────────────────
    public string $input = '';
    public ?string $conversationId = null;
    public string $errorMessage = '';
    public bool $isSending = false;

    // ─── Module Config (Sidebar) ──────────────────────────────
    public string $selectedModel = 'gemini-2.5-flash';
    public string $customInstructions = '';
    public bool $toolsEnabled = true;

    // ─── Live Stats (Sidebar Progress) ───────────────────────
    public array $lastToolCalls = [];
    public ?float $lastResponseMs = null;
    public int $requestCount = 0;
    public int $messageCount = 0;
    public ?string $activeSection = null; // which sidebar section is expanded

    /** Available Gemini models for the selector */
    public array $availableModels = [
        'gemini-2.5-flash'      => 'Gemini 2.5 Flash (Latest)',
        'gemini-2.0-flash'      => 'Gemini 2.0 Flash (Stable)',
        'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash Lite (Fast)',
    ];

    public function mount(): void
    {
        $this->conversationId     = Session::get('active_chat_id');
        $this->customInstructions = $this->defaultInstructions();
        $this->refreshStats();
        $this->checkDatabaseSetup();
    }

    // ─── Agent ───────────────────────────────────────────────

    protected function buildAgent(): AssistantAgent
    {
        $user  = auth()->user() ?? (object) ['id' => 999];
        $agent = new AssistantAgent($this->customInstructions, $this->toolsEnabled);

        if ($this->conversationId) {
            $agent->continue($this->conversationId, $user);
        } else {
            $agent->forUser($user);
        }

        return $agent;
    }

    // ─── Messages ────────────────────────────────────────────

    public function getMessagesProperty(): array
    {
        if (! $this->conversationId) {
            return [];
        }

        try {
            /** @var ConversationStore $store */
            $store = app(ConversationStore::class);

            return $store->getLatestConversationMessages($this->conversationId, 100)->all();
        } catch (\Throwable) {
            return [];
        }
    }

    // ─── Chat Actions ─────────────────────────────────────────

    public function sendMessage(): void
    {
        $this->validate(['input' => 'required|string|max:5000']);

        $this->errorMessage = '';
        $this->isSending    = true;

        if (! config('ai.providers.gemini.key')) {
            $this->errorMessage = 'GEMINI_API_KEY is not set in your .env file.';
            $this->isSending    = false;

            return;
        }

        try {
            $agent = $this->buildAgent();
            $start = microtime(true);

            // Dispatch to Gemini — passes selected model override
            $response = $agent->prompt($this->input, model: $this->selectedModel);

            // ── Capture live stats ──
            $this->lastResponseMs = round((microtime(true) - $start) * 1000);
            $this->lastToolCalls  = $response->toolCalls
                ->map(fn ($tc) => $tc->name)
                ->values()
                ->all();
            $this->requestCount++;

            // ── Persist conversation ID ──
            $newId = $agent->currentConversation();
            if ($newId && $newId !== $this->conversationId) {
                $this->conversationId = $newId;
                Session::put('active_chat_id', $this->conversationId);
            }

            $this->refreshStats();
            $this->input = '';

        } catch (\Throwable $e) {
            $this->errorMessage = 'AI Error: ' . $e->getMessage();
        } finally {
            $this->isSending = false;
        }
    }

    public function startNewChat(): void
    {
        Session::forget('active_chat_id');
        $this->conversationId = null;
        $this->errorMessage   = '';
        $this->input          = '';
        $this->lastToolCalls  = [];
        $this->lastResponseMs = null;
        $this->requestCount   = 0;
        $this->messageCount   = 0;
    }

    // ─── Sidebar ─────────────────────────────────────────────

    /** Toggle a sidebar section open/closed */
    public function toggleSection(string $section): void
    {
        $this->activeSection = $this->activeSection === $section ? null : $section;
    }

    /** Reset system prompt to default */
    public function resetInstructions(): void
    {
        $this->customInstructions = $this->defaultInstructions();
    }

    // ─── Helpers ─────────────────────────────────────────────

    protected function refreshStats(): void
    {
        $this->messageCount = count($this->messages);
    }

    protected function checkDatabaseSetup(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $this->errorMessage = 'Database connection failed.';
        }
    }

    protected function defaultInstructions(): string
    {
        return "You are 'Gdgoc Assistant', a helpful, creative, and friendly AI developer assistant built using the Laravel AI SDK.\n"
            . "You can help with code, design, database info, or general discussions.\n"
            . "Always use the 'GetSystemInfo' tool if the user asks about the server time, database stats (like number of users, messages), or OS platforms.\n"
            . "Keep your answers brief, informative, and formatted using Markdown.";
    }

    public function render()
    {
        return view('livewire.chatbot', [
            'messages' => $this->messages,
        ])->layout('layouts.app', ['title' => 'GDGOC AI Chatbot']);
    }
}
