<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-purple-500 selection:text-white">

    <!-- Top header -->
    <header class="border-b border-slate-900 bg-slate-950/80 backdrop-blur sticky top-0 z-40 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-blue-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <span class="text-white font-bold text-lg">G</span>
            </div>
            <div>
                <h1 class="text-md font-bold tracking-wide bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-indigo-200 to-blue-400">
                    GDGOC Laravel AI Chatbot
                </h1>
                <p class="text-xs text-slate-400">
                    Powered by Laravel AI SDK &bull;
                    <span class="text-indigo-400 font-mono font-semibold">{{ $selectedModel }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <button wire:click="startNewChat" class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center space-x-2">
                <span>🔄 New Chat</span>
            </button>
            <span class="inline-flex items-center rounded-full {{ $isSending ? 'bg-amber-500/10 ring-amber-500/20 text-amber-400' : 'bg-emerald-500/10 ring-emerald-500/20 text-emerald-400' }} px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset transition-all">
                {{ $isSending ? '⚙ Processing...' : '● Online' }}
            </span>
        </div>
    </header>

    <!-- Main layout -->
    <div class="flex-1 max-w-7xl w-full mx-auto grid grid-cols-1 lg:grid-cols-4 overflow-hidden">

        <!-- ═══════════════════════════════════════════
             SIDEBAR — Interactive AI SDK Explorer
             ═══════════════════════════════════════════ -->
        <aside class="hidden lg:flex flex-col border-r border-slate-900 bg-slate-950/20 overflow-y-auto">

            <!-- Sidebar header -->
            <div class="px-4 py-4 border-b border-slate-900">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">AI SDK Explorer</p>
                <p class="text-xs text-slate-600 mt-0.5">Click each module to configure</p>
            </div>

            <div class="flex-1 py-2">

                <!-- ── 1. MODELS ────────────────────────────── -->
                <div class="border-b border-slate-900/60">
                    <!-- Header (clickable) -->
                    <button wire:click="toggleSection('models')"
                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-slate-900/40 transition group text-left">
                        <div class="flex items-center space-x-2.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded bg-purple-500/15 text-purple-400 text-[10px] font-bold">1</span>
                            <span class="text-xs font-semibold text-slate-200 group-hover:text-white transition">Models</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20">
                                ACTIVE
                            </span>
                        </div>
                        <svg class="h-3 w-3 text-slate-600 transition-transform {{ $activeSection === 'models' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    @if($activeSection === 'models')
                    <div class="px-4 pb-4 space-y-3">
                        <p class="text-[10px] text-slate-500 leading-relaxed">
                            The <span class="text-purple-300 font-mono">#[Model]</span> attribute selects which Gemini model processes your prompt. Changing this affects speed, capability, and rate limits.
                        </p>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Active Model</label>
                            <select wire:model.live="selectedModel"
                                class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500/60 focus:ring-1 focus:ring-purple-500/30">
                                @foreach($availableModels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-[10px] space-y-1.5">
                            <div class="flex justify-between text-slate-500">
                                <span>Speed</span>
                                <span class="text-slate-300 font-mono">
                                    {{ match($selectedModel) {
                                        'gemini-2.5-flash' => '⚡⚡⚡',
                                        'gemini-2.0-flash' => '⚡⚡⚡',
                                        'gemini-2.0-flash-lite' => '⚡⚡⚡⚡',
                                        default => '⚡⚡'
                                    } }}
                                </span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>RPM (free)</span>
                                <span class="text-slate-300 font-mono">
                                    {{ match($selectedModel) {
                                        'gemini-2.5-flash' => '10',
                                        'gemini-2.0-flash' => '15',
                                        'gemini-2.0-flash-lite' => '30',
                                        default => '10'
                                    } }} req/min
                                </span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="px-4 pb-3">
                        <div class="flex items-center justify-between">
                            <code class="text-[10px] text-purple-300 font-mono bg-purple-500/5 px-2 py-0.5 rounded border border-purple-500/10">{{ $selectedModel }}</code>
                            @if($lastResponseMs)
                            <span class="text-[10px] text-slate-500">{{ $lastResponseMs }}ms</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- ── 2. PROMPTS ────────────────────────────── -->
                <div class="border-b border-slate-900/60">
                    <button wire:click="toggleSection('prompts')"
                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-slate-900/40 transition group text-left">
                        <div class="flex items-center space-x-2.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded bg-blue-500/15 text-blue-400 text-[10px] font-bold">2</span>
                            <span class="text-xs font-semibold text-slate-200 group-hover:text-white transition">Prompts</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20">
                                ACTIVE
                            </span>
                        </div>
                        <svg class="h-3 w-3 text-slate-600 transition-transform {{ $activeSection === 'prompts' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    @if($activeSection === 'prompts')
                    <div class="px-4 pb-4 space-y-3">
                        <p class="text-[10px] text-slate-500 leading-relaxed">
                            The system prompt is sent to the model as <span class="text-blue-300 font-mono">instructions()</span> via the Agent class. It shapes the AI's personality and behavior for the entire session.
                        </p>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">System Instructions</label>
                            <textarea wire:model.live.debounce.500ms="customInstructions"
                                rows="6"
                                class="w-full bg-slate-900 border border-slate-800 text-slate-200 text-[11px] font-mono rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500/60 focus:ring-1 focus:ring-blue-500/30 resize-none leading-relaxed"
                                placeholder="Write your system prompt here..."></textarea>
                        </div>
                        <button wire:click="resetInstructions"
                            class="w-full py-1.5 text-[10px] font-semibold text-slate-400 hover:text-slate-200 border border-slate-800 hover:border-slate-700 rounded-lg transition">
                            ↺ Reset to Default
                        </button>
                    </div>
                    @else
                    <div class="px-4 pb-3">
                        <p class="text-[10px] text-slate-500 italic leading-relaxed line-clamp-2">
                            "{{ strlen($customInstructions) > 80 ? substr($customInstructions, 0, 80) . '...' : $customInstructions }}"
                        </p>
                    </div>
                    @endif
                </div>

                <!-- ── 3. AGENTS ────────────────────────────── -->
                <div class="border-b border-slate-900/60">
                    <button wire:click="toggleSection('agents')"
                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-slate-900/40 transition group text-left">
                        <div class="flex items-center space-x-2.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded bg-indigo-500/15 text-indigo-400 text-[10px] font-bold">3</span>
                            <span class="text-xs font-semibold text-slate-200 group-hover:text-white transition">Agents</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold {{ $isSending ? 'bg-amber-500/10 text-amber-400 ring-amber-500/20 animate-pulse' : 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20' }} ring-1">
                                {{ $isSending ? 'RUNNING' : 'READY' }}
                            </span>
                        </div>
                        <svg class="h-3 w-3 text-slate-600 transition-transform {{ $activeSection === 'agents' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    @if($activeSection === 'agents')
                    <div class="px-4 pb-4 space-y-3">
                        <p class="text-[10px] text-slate-500 leading-relaxed">
                            The <span class="text-indigo-300 font-mono">AssistantAgent</span> class implements <span class="text-slate-400 font-mono">Agent</span>, <span class="text-slate-400 font-mono">Conversational</span>, and <span class="text-slate-400 font-mono">HasTools</span>. It orchestrates all 5 modules.
                        </p>
                        <div class="space-y-1.5 text-[10px]">
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                <span class="text-slate-400 font-mono">Agent class</span>
                                <code class="text-indigo-300">AssistantAgent</code>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                <span class="text-slate-400 font-mono">Provider</span>
                                <code class="text-indigo-300">gemini</code>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                <span class="text-slate-400 font-mono">Requests sent</span>
                                <code class="text-indigo-300">{{ $requestCount }}</code>
                            </div>
                            @if($lastResponseMs)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                <span class="text-slate-400 font-mono">Last response</span>
                                <code class="text-indigo-300">{{ $lastResponseMs }}ms</code>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="px-4 pb-3">
                        <div class="flex items-center space-x-2">
                            <code class="text-[10px] text-indigo-300 font-mono">AssistantAgent</code>
                            @if($requestCount > 0)
                            <span class="text-[10px] text-slate-600">· {{ $requestCount }} calls</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- ── 4. TOOLS ────────────────────────────── -->
                <div class="border-b border-slate-900/60">
                    <button wire:click="toggleSection('tools')"
                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-slate-900/40 transition group text-left">
                        <div class="flex items-center space-x-2.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded bg-emerald-500/15 text-emerald-400 text-[10px] font-bold">4</span>
                            <span class="text-xs font-semibold text-slate-200 group-hover:text-white transition">Tools</span>
                            @if(count($lastToolCalls) > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 animate-pulse">
                                CALLED!
                            </span>
                            @elseif(!$toolsEnabled)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-500/10 text-slate-500 ring-1 ring-slate-500/20">
                                OFF
                            </span>
                            @else
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20">
                                READY
                            </span>
                            @endif
                        </div>
                        <svg class="h-3 w-3 text-slate-600 transition-transform {{ $activeSection === 'tools' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    @if($activeSection === 'tools')
                    <div class="px-4 pb-4 space-y-3">
                        <p class="text-[10px] text-slate-500 leading-relaxed">
                            Tools let the AI call your PHP code automatically. The <span class="text-emerald-300 font-mono">GetSystemInfo</span> tool returns server stats. Try asking: <em class="text-slate-400">"jam berapa sekarang?"</em>
                        </p>

                        <!-- Toggle -->
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-[10px] font-semibold text-slate-300">Enable Tools</p>
                                <p class="text-[10px] text-slate-500">Allow AI to call GetSystemInfo</p>
                            </div>
                            <button wire:click="$toggle('toolsEnabled')"
                                class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $toolsEnabled ? 'bg-emerald-600' : 'bg-slate-700' }}">
                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform {{ $toolsEnabled ? 'translate-x-4.5' : 'translate-x-0.5' }}"></span>
                            </button>
                        </label>

                        <!-- Last tool calls -->
                        @if(count($lastToolCalls) > 0)
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Last Tool Calls</p>
                            <div class="space-y-1">
                                @foreach($lastToolCalls as $toolName)
                                <div class="flex items-center space-x-2 p-2 rounded-lg bg-emerald-900/20 border border-emerald-800/30">
                                    <span class="text-emerald-400 text-xs">⚙</span>
                                    <code class="text-[10px] text-emerald-300 font-mono">{{ $toolName }}</code>
                                    <span class="ml-auto text-[9px] text-emerald-600">executed</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="p-2 rounded-lg bg-slate-900/40 border border-slate-800/40 text-center">
                            <p class="text-[10px] text-slate-500">No tool calls yet</p>
                            <p class="text-[10px] text-slate-600 mt-0.5">Ask about server time or DB stats</p>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="px-4 pb-3 flex items-center space-x-2">
                        <code class="text-[10px] text-emerald-300 font-mono">GetSystemInfo</code>
                        @if(count($lastToolCalls) > 0)
                        <span class="text-[10px] text-emerald-600">✓ called {{ count($lastToolCalls) }}x</span>
                        @else
                        <span class="text-[10px] text-slate-600">{{ $toolsEnabled ? 'ready' : 'disabled' }}</span>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- ── 5. MEMORY ────────────────────────────── -->
                <div>
                    <button wire:click="toggleSection('memory')"
                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-slate-900/40 transition group text-left">
                        <div class="flex items-center space-x-2.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded bg-amber-500/15 text-amber-400 text-[10px] font-bold">5</span>
                            <span class="text-xs font-semibold text-slate-200 group-hover:text-white transition">Memory</span>
                            @if($conversationId)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20">
                                {{ $messageCount }} msgs
                            </span>
                            @else
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-500/10 text-slate-500 ring-1 ring-slate-500/20">
                                EMPTY
                            </span>
                            @endif
                        </div>
                        <svg class="h-3 w-3 text-slate-600 transition-transform {{ $activeSection === 'memory' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    @if($activeSection === 'memory')
                    <div class="px-4 pb-4 space-y-3">
                        <p class="text-[10px] text-slate-500 leading-relaxed">
                            Uses <span class="text-amber-300 font-mono">RemembersConversations</span> trait. Messages are persisted to SQLite and re-injected as context on every request, giving the AI memory.
                        </p>
                        <div class="space-y-1.5 text-[10px]">
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                <span class="text-slate-400">Storage</span>
                                <code class="text-amber-300">SQLite</code>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                <span class="text-slate-400">Messages</span>
                                <code class="text-amber-300">{{ $messageCount }}</code>
                            </div>
                            @if($conversationId)
                            <div class="p-2 rounded-lg bg-slate-900/60 border border-slate-800/60">
                                <p class="text-slate-400 mb-0.5">Conversation ID</p>
                                <code class="text-amber-300 break-all">{{ substr($conversationId, 0, 18) }}...</code>
                            </div>
                            @endif
                        </div>
                        @if($conversationId)
                        <button wire:click="startNewChat"
                            class="w-full py-1.5 text-[10px] font-semibold text-rose-400 hover:text-rose-300 border border-rose-900/50 hover:border-rose-800 rounded-lg transition">
                            🗑 Clear Memory & New Chat
                        </button>
                        @endif
                    </div>
                    @else
                    <div class="px-4 pb-3">
                        @if($conversationId)
                        <code class="text-[10px] text-amber-300 font-mono">{{ substr($conversationId, 0, 12) }}...</code>
                        @else
                        <span class="text-[10px] text-slate-600">No active session</span>
                        @endif
                    </div>
                    @endif
                </div>

            </div>

            <!-- Sidebar footer -->
            <div class="px-4 py-3 border-t border-slate-900 bg-slate-950/40">
                <p class="text-[10px] text-slate-600">GDGOC Laravel Sesi 2 · AI SDK Demo</p>
            </div>
        </aside>

        <!-- ═══════════════════════════════════════════
             MAIN CHAT PANEL
             ═══════════════════════════════════════════ -->
        <main class="lg:col-span-3 flex flex-col h-[calc(100vh-73px)] bg-slate-950">

            <!-- Messages Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto px-6 py-8 space-y-6"
                x-data x-init="
                    const el = $el;
                    const observer = new MutationObserver(() => el.scrollTop = el.scrollHeight);
                    observer.observe(el, { childList: true, subtree: true });
                    el.scrollTop = el.scrollHeight;
                ">

                @if($errorMessage)
                    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-start space-x-2">
                        <span>⚠️</span>
                        <div><span class="font-bold">Error:</span> {{ $errorMessage }}</div>
                    </div>
                @endif

                @if(count($messages) === 0)
                    <!-- Empty State -->
                    <div class="h-full flex flex-col items-center justify-center text-center py-12 px-4 space-y-5">
                        <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-purple-600/20 to-indigo-600/20 border border-purple-500/20 flex items-center justify-center text-3xl">
                            👋
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-200">Welcome to GDGOC AI Chatbot!</h3>
                            <p class="text-xs text-slate-400 max-w-md mt-2 leading-relaxed">
                                Gunakan <strong class="text-indigo-400">sidebar kiri</strong> untuk mengkonfigurasi model, prompt, dan tools secara live.<br>
                                Coba tanya: <em class="text-indigo-400 font-mono">"jam berapa sekarang dan berapa user di DB?"</em>
                            </p>
                        </div>
                        <div class="flex flex-wrap justify-center gap-2 text-[10px]">
                            <span class="px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-slate-400">💡 Click sidebar modules to configure</span>
                            <span class="px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-slate-400">🔧 Toggle tools on/off</span>
                            <span class="px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-slate-400">📝 Edit system prompt live</span>
                        </div>
                    </div>
                @else
                    <!-- Chat messages -->
                    @foreach($messages as $msg)
                        @if($msg->role->value === 'user')
                            <!-- User Message -->
                            <div class="flex justify-end items-start space-x-3 max-w-3xl ml-auto">
                                <div class="flex flex-col items-end">
                                    <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-none px-4 py-2.5 text-sm shadow-md">
                                        {{ $msg->content }}
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-1">You</span>
                                </div>
                                <div class="h-8 w-8 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-xs font-bold text-indigo-300">U</div>
                            </div>

                        @elseif($msg->role->value === 'assistant')
                            <!-- Assistant Message -->
                            <div class="flex justify-start items-start space-x-3 max-w-3xl mr-auto">
                                <div class="h-8 w-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-xs font-bold text-purple-300 flex-shrink-0">AI</div>
                                <div class="flex flex-col items-start">
                                    <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl rounded-tl-none px-4 py-2.5 text-sm shadow-md text-slate-200 max-w-none">
                                        {!! nl2br(preg_replace(
                                            ['/\*\*(.*?)\*\*/', '/`(.*?)`/'],
                                            ['<strong class="text-purple-400 font-bold">$1</strong>', '<code class="bg-slate-950 px-1 py-0.5 rounded text-amber-300 font-mono text-xs border border-slate-800">$1</code>'],
                                            e($msg->content)
                                        )) !!}

                                        @if(isset($msg->toolCalls) && $msg->toolCalls->isNotEmpty())
                                            <div class="mt-3 pt-2 border-t border-slate-800/80 flex flex-wrap gap-2">
                                                @foreach($msg->toolCalls as $call)
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[10px] font-mono bg-emerald-900/30 text-emerald-300 border border-emerald-800/50">
                                                        <span>⚙</span>
                                                        <strong>{{ $call->name }}</strong>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-500 mt-1">Gdgoc Assistant · {{ $selectedModel }}</span>
                                </div>
                            </div>

                        @elseif($msg->role->value === 'tool_result')
                            <!-- Tool Result (educational) -->
                            <div class="max-w-2xl mr-auto ml-11 p-3.5 rounded-xl bg-emerald-950/20 border border-emerald-900/30 text-xs font-mono space-y-2">
                                <div class="text-slate-400 flex items-center space-x-2 text-[10px]">
                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="font-semibold text-emerald-500">TOOL RESULT</span>
                                </div>
                                @foreach($msg->toolResults as $result)
                                    <div class="text-emerald-400 whitespace-pre-wrap text-[11px]">{{ is_string($result->result) ? $result->result : json_encode($result->result, JSON_PRETTY_PRINT) }}</div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                @endif

                @if($isSending)
                    <!-- Thinking indicator -->
                    <div class="flex justify-start items-start space-x-3 max-w-xl mr-auto">
                        <div class="h-8 w-8 rounded-lg bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-xs font-bold text-purple-300">AI</div>
                        <div class="bg-slate-900/40 border border-slate-800/50 rounded-2xl rounded-tl-none px-4 py-3 text-sm flex items-center space-x-2">
                            <span class="h-2 w-2 rounded-full bg-purple-400 animate-bounce"></span>
                            <span class="h-2 w-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:0.2s"></span>
                            <span class="h-2 w-2 rounded-full bg-blue-400 animate-bounce" style="animation-delay:0.4s"></span>
                            <span class="text-[10px] text-slate-500 ml-1">{{ $selectedModel }}...</span>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Input Form -->
            <div class="p-6 border-t border-slate-900 bg-slate-950/50 backdrop-blur">
                <form wire:submit="sendMessage" class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.200ms="input"
                        wire:keydown.enter.prevent="sendMessage"
                        placeholder="Type your message... (Enter to send)"
                        class="w-full bg-slate-900/80 hover:bg-slate-900 focus:bg-slate-900 text-slate-100 rounded-xl px-5 py-4 pr-24 border border-slate-800 focus:border-indigo-500/80 outline-none transition focus:ring-1 focus:ring-indigo-500/50 placeholder-slate-500 text-sm shadow-inner"
                        @if($isSending) disabled @endif
                    />
                    <button
                        type="submit"
                        class="absolute right-2.5 top-2.5 h-10 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                        @if($isSending) disabled @endif
                    >
                        @if($isSending)
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        @else
                            Send 🚀
                        @endif
                    </button>
                </form>
                <div class="mt-2 text-[10px] text-slate-600 flex justify-between">
                    <span>{{ $toolsEnabled ? '🔧 Tools enabled' : '⚫ Tools disabled' }} · Model: {{ $selectedModel }}</span>
                    @if($conversationId)
                        <span>Session: <strong class="font-mono text-indigo-500">{{ substr($conversationId, 0, 8) }}...</strong></span>
                    @endif
                </div>
            </div>

        </main>
    </div>
</div>
