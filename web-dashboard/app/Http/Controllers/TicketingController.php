<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TicketingController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        
        $config = DB::table('ticket_configs')->where('guild_id', $guildId)->first();
        
        // Form default values
        if (!$config) {
            $config = (object) [
                'category_id' => '',
                'support_role_id' => '',
                'log_channel_id' => '',
                'ticket_message' => 'Silahkan klik tombol di bawah untuk membuat tiket.',
                'ticket_embed_title' => 'Support Ticket',
                'ticket_embed_description' => 'Kami akan segera membantu anda. Mohon tunggu sebentar.',
                'ticket_embed_color' => '#6c63ff'
            ];
        }

        return view('ticketing.index', compact('config'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'category_id' => 'required|string',
            'support_role_id' => 'required|string',
            'log_channel_id' => 'required|string',
            'ticket_message' => 'nullable|string',
            'ticket_embed_title' => 'nullable|string',
            'ticket_embed_description' => 'nullable|string',
            'ticket_embed_color' => 'nullable|string',
        ]);

        DB::table('ticket_configs')->updateOrInsert(
            ['guild_id' => $guildId],
            [
                'category_id' => $request->category_id,
                'support_role_id' => $request->support_role_id,
                'log_channel_id' => $request->log_channel_id,
                'ticket_message' => $request->ticket_message,
                'ticket_embed_title' => $request->ticket_embed_title,
                'ticket_embed_description' => $request->ticket_embed_description,
                'ticket_embed_color' => $request->ticket_embed_color,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Settings saved successfully!');
    }

    public function deploy(Request $request)
    {
        $guildId = session('selected_guild_id');
        $request->validate(['channel_id' => 'required|string']);

        $config = DB::table('ticket_configs')->where('guild_id', $guildId)->first();

        if (!$config) {
            return redirect()->back()->with('error', 'Please save settings first!');
        }

        // Deploy Panel Logic (Send to Discord API via Bot)
        // We will execute a command or use existing mechanism from WelcomeChannelController concept
        // For simplicity, we trigger a bot message via API or store a "pending_deploy" flag if full API isnt ready.
        // Assuming we have a direct endpoint or we just save specific deploy request to DB for bot to poll? 
        // Actually, let's use the direct Axios approach from the Bot side if we had the API.
        // Since we are monolithic-ish here (same repo), we can't easily call JS function from PHP.
        // STRATEGY: We will send a special payload to a "deploy" endpoint if we had one OR 
        // we can just utilize the bot's ability to read from DB? No, that's passive.
        
        // BEST APPROACH for this setup:
        // We actually need the BOT to send the message. 
        // We'll use the 'commands' table or similar? No.
        // We'll send a POST request to the Bot if it has an API server (it doesn't seem to have valid one running).
        // ALTERNATIVE: Use a simple queue table 'bot_actions' -> Bot polls it every 5s?
        // OR better: Just return success here and tell User "Done manually" for now?
        // NO, the user expects it to work.
        
        // Let's implement a simple file-based IPC or DB-based IPC.
        // We'll add a 'pending_actions' or just assume the Bot has an API?
        // Wait, the Bot checks DB. Let's create a `bot_actions` table.
        
        // Actually for now, let's assume we implement a simple `bot_actions` table for these triggers.
        // But to avoid complexity, I'll see if I can just use a queue?
        
        // Let's create a lightweight `bot_actions` table migration implicitly? No that's hacky.
        // I will add a migration for `bot_actions` next step? Or better:
        // Just POST to the bot? The bot process is running `npm start`. Does it expose Express?
        // Checking `index.js`... (Previous logs don't show express setup in main file, but `user.service.js` mentions LARAVEL_API_URL).
        
        // Let's stick to standard practice: PHP writes to DB 'pending_actions' or similar?
        // Or simply, since I can't restart bot to add API, I will just display "Deploy Panel" Instructions?
        // No, I want it automated.
        
        // I'll check if I can modify the bot to listen to a new table `bot_actions` or similar.
        // Let's check `ready.js` again. I see `dailyYap`.
        // I will add a poller in `ready.js` to check for `pending_messages` table?
        
        // Let's create `bot_outbox` table.
        // Migration: `2025_01_01_000012_create_bot_outbox_table.php`
        // Schema: id, guild_id, channel_id, type (ticket_panel), payload (json), processed (bool).
        
        // For this step, I'll stick to Controller code first assuming that table exists.
        
        $payload = [
            'title' => $config->ticket_embed_title,
            'description' => $config->ticket_embed_description,
            'color' => $config->ticket_embed_color,
            'button_text' => 'Open Ticket',
            'custom_id' => 'ticket_create'
        ];

        DB::table('bot_outbox')->insert([
            'guild_id' => $guildId,
            'channel_id' => $request->channel_id,
            'type' => 'ticket_panel',
            'payload' => json_encode($payload),
            'processed' => false,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Support Panel deployed! (Bot will send it shortly)');
    }
}
