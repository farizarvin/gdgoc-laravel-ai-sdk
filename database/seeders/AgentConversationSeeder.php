<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgentConversationSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: hapus data lama sebelum seed ulang
        DB::table('agent_conversation_messages')->delete();
        DB::table('agent_conversations')->delete();

        // Ambil user demo yang sudah di-seed
        $demoUserId    = DB::table('users')->where('email', 'demo@gdgoc.dev')->value('id');
        $trainerUserId = DB::table('users')->where('email', 'arvin@gdgoc.dev')->value('id');

        // ── Percakapan 1: Demo tentang 5 modul AI SDK (user: demo) ──────────
        $this->seedConversation(
            userId: $demoUserId,
            title: 'Belajar 5 Modul Laravel AI SDK',
            messages: [
                ['user', 'Halo! Kamu siapa?'],
                ['assistant', "Halo! Saya **Gdgoc Assistant**, AI developer assistant yang dibangun menggunakan **Laravel AI SDK**.\n\nSaya bisa membantu kamu dengan:\n- 💻 Pertanyaan seputar coding & Laravel\n- 🗄️ Info database & statistik server\n- 🤖 Pertanyaan umum seputar AI & development\n\nAda yang bisa saya bantu?"],

                ['user', 'Jelaskan 5 modul Laravel AI SDK'],
                ['assistant', "Laravel AI SDK memiliki **5 modul inti**:\n\n1. **Models** — Memilih model AI (Gemini, OpenAI, dll) via `#[Model]` attribute\n2. **Prompts** — System instructions via method `instructions()` di Agent class\n3. **Agents** — Kelas PHP yang implement `Agent`, `Conversational`, `HasTools`\n4. **Tools** — PHP code yang dipanggil AI secara otomatis (function calling)\n5. **Memory** — Menyimpan history percakapan ke database via `RemembersConversations`\n\nSemua modul ini bekerja bersama di chatbot ini! 🚀"],

                ['user', 'Berapa jumlah user di database dan jam berapa sekarang?'],
                ['assistant', "Berikut informasi dari server:\n\n**Server Time:** 2026-06-13 10:30:15\n**OS:** Darwin (macOS)\n**Users in DB:** 10\n**Messages stored:** 3\n\nData ini diambil secara real-time menggunakan **Tool Calling** — AI memanggil `GetSystemInfo` PHP class secara otomatis! ⚙️", true],
            ]
        );

        // ── Percakapan 2: Trainer nanya tentang tools ────────────────────────
        $this->seedConversation(
            userId: $trainerUserId,
            title: 'Demo Tool Calling ke Peserta',
            messages: [
                ['user', 'Coba jelaskan cara kerja tool calling di Laravel AI SDK'],
                ['assistant', "Tool calling bekerja seperti ini:\n\n```\nUser → Prompt → Agent\n                  ↓\n            AI putuskan: perlu tool?\n                  ↓ Ya\n            SDK panggil Tool::handle()\n                  ↓\n            Hasil dikembalikan ke AI\n                  ↓\n            AI buat jawaban final\n```\n\nContoh tool di project ini:\n```php\nclass GetSystemInfo implements Tool {\n    public function handle(Request \$r): string {\n        return 'Time: '.now().' | Users: '.DB::table('users')->count();\n    }\n}\n```\n\nCool, kan? 🔧"],

                ['user', 'Gimana AI tahu kapan harus panggil tool?'],
                ['assistant', "AI tahu kapan harus memanggil tool berdasarkan **deskripsi tool** yang kita definisikan di method `description()`:\n\n```php\npublic function description(): string {\n    return 'Gets current server details including time, OS platform, \n            and SQLite database stats.';\n}\n```\n\nAI membaca deskripsi ini dan mencocokkan dengan **intent** user. Kalau user tanya soal jam atau stats database → AI otomatis trigger tool.\n\nKamu tidak perlu tulis logic if-else sama sekali! 🎯"],
            ]
        );

        // ── Percakapan 3: Guest user (id: 999) — memory demo ────────────────
        $this->seedConversation(
            userId: 999,
            title: 'Session Demo Memory (Guest)',
            messages: [
                ['user', 'Nama saya Budi, saya peserta GDGOC dari Semarang'],
                ['assistant', "Halo **Budi** dari Semarang! 👋\n\nSenang bertemu kamu! GDGOC (Google Developer Groups on Campus) adalah komunitas yang keren untuk belajar teknologi Google bersama-sama.\n\nAda yang bisa saya bantu hari ini?"],

                ['user', 'Saya mau belajar Laravel AI SDK, dari mana mulainya?'],
                ['assistant', "Bagus sekali, **Budi**! Ini roadmap belajar Laravel AI SDK yang recommended:\n\n**1. Setup** (15 menit)\n- Install `laravel/ai` via Composer\n- Daftarkan API key Gemini di `.env`\n- Publish config: `php artisan vendor:publish --tag=ai-config`\n\n**2. Buat Agent pertama** (30 menit)\n- `php artisan make:agent MyFirstAgent`\n- Implement `instructions()` method\n- Test dengan `agent->prompt('Hello!')`\n\n**3. Tambah Tool** (30 menit)\n- `php artisan make:tool MyTool`\n- Implement `handle()` dan `schema()`\n- Tambahkan ke `tools()` di Agent\n\n**4. Tambah Memory** (15 menit)\n- Implement `Conversational` + `HasTools`\n- Use trait `RemembersConversations`\n- Run migration\n\nLangkah mana yang mau kamu mulai dulu, Budi?"],
            ]
        );

        $count = DB::table('agent_conversations')->count();
        $msgCount = DB::table('agent_conversation_messages')->count();

        $this->command->info("✅ Conversations seeded: {$count} conversations, {$msgCount} messages");
    }

    /**
     * Helper: seed satu conversation beserta semua message-nya.
     *
     * @param  array<int, array{0: string, 1: string, 2?: bool}>  $messages
     */
    private function seedConversation(int|null $userId, string $title, array $messages): void
    {
        $conversationId = (string) Str::uuid7();
        $now            = now();

        // Insert conversation
        DB::table('agent_conversations')->insert([
            'id'         => $conversationId,
            'user_id'    => $userId,
            'title'      => $title,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $delay = 0;

        foreach ($messages as $message) {
            $role        = $message[0];
            $content     = $message[1];
            $hasToolCall = $message[2] ?? false;

            $messageId   = (string) Str::uuid7();
            $timestamp   = $now->copy()->addSeconds($delay);
            $toolCalls   = '[]';
            $toolResults = '[]';

            // Jika message assistant punya tool call, simulasikan
            if ($role === 'assistant' && $hasToolCall) {
                $toolCallId = (string) Str::uuid();
                $toolCalls  = json_encode([[
                    'id'        => $toolCallId,
                    'name'      => 'GetSystemInfo',
                    'arguments' => [],
                ]]);

                // Insert tool result message dulu (sebelum assistant message yang final)
                DB::table('agent_conversation_messages')->insert([
                    'id'              => (string) Str::uuid7(),
                    'conversation_id' => $conversationId,
                    'user_id'         => $userId,
                    'agent'           => 'App\\Agents\\AssistantAgent',
                    'role'            => 'tool_result',
                    'content'         => '',
                    'attachments'     => '[]',
                    'tool_calls'      => '[]',
                    'tool_results'    => json_encode([[
                        'id'     => $toolCallId,
                        'name'   => 'GetSystemInfo',
                        'result' => 'Server Time: 2026-06-13 10:30:15 | OS: Darwin | Users in DB: 10 | Messages stored: 3',
                    ]]),
                    'usage' => '[]',
                    'meta'  => '[]',
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                $delay += 2;
                $timestamp = $now->copy()->addSeconds($delay);
            }

            DB::table('agent_conversation_messages')->insert([
                'id'              => $messageId,
                'conversation_id' => $conversationId,
                'user_id'         => $userId,
                'agent'           => 'App\\Agents\\AssistantAgent',
                'role'            => $role,
                'content'         => $content,
                'attachments'     => '[]',
                'tool_calls'      => $toolCalls,
                'tool_results'    => '[]',
                'usage'           => json_encode(['input_tokens' => rand(50, 300), 'output_tokens' => rand(100, 500)]),
                'meta'            => '[]',
                'created_at'      => $timestamp,
                'updated_at'      => $timestamp,
            ]);

            $delay += rand(5, 15);
        }

        // Update conversation updated_at ke timestamp pesan terakhir
        DB::table('agent_conversations')
            ->where('id', $conversationId)
            ->update(['updated_at' => $now->copy()->addSeconds($delay)]);
    }
}
