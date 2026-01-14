<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModeratorController extends Controller
{
    public function index()
    {
        $selectedGuildId = session('selected_guild_id');
        
        // Fetch AutoMod Rules
        $rules = DB::table('automod_rules')
            ->where('guild_id', $selectedGuildId)
            ->get()
            ->keyBy('trigger_type');

        // Initialize defaults if not present
        $defaults = ['bad_word', 'link', 'spam'];
        foreach ($defaults as $type) {
            if (!isset($rules[$type])) {
                // Insert default disabled
                DB::table('automod_rules')->insert([
                    'guild_id' => $selectedGuildId,
                    'trigger_type' => $type,
                    'enabled' => 0,
                    'action' => 'delete',
                    'trigger_content' => ''
                ]);
            }
        }
        
        // Re-fetch to get IDs
        $rules = DB::table('automod_rules')
            ->where('guild_id', $selectedGuildId)
            ->get()
            ->keyBy('trigger_type');

        // Fetch Recent Warns
        $warns = DB::table('warns')
            ->where('guild_id', $selectedGuildId)
            ->orderBy('timestamp', 'desc')
            ->limit(50)
            ->get();

        return view('moderator.index', compact('rules', 'warns'));
    }

    public function updateRules(Request $request)
    {
        $selectedGuildId = session('selected_guild_id');
        
        $types = ['bad_word', 'link', 'spam'];

        foreach ($types as $type) {
            $enabled = $request->has("{$type}_enabled") ? 1 : 0;
            $content = $request->input("{$type}_content", '');
            $action = $request->input("{$type}_action", 'delete');

            DB::table('automod_rules')
                ->where('guild_id', $selectedGuildId)
                ->where('trigger_type', $type)
                ->update([
                    'enabled' => $enabled,
                    'trigger_content' => $content,
                    'action' => $action
                ]);
        }

        return redirect()->back()->with('success', 'Moderation rules updated!');
    }

    public function destroyWarn($id)
    {
        $selectedGuildId = session('selected_guild_id');
        
        DB::table('warns')
            ->where('id', $id)
            ->where('guild_id', $selectedGuildId)
            ->delete();

        return redirect()->back()->with('success', 'Warning removed!');
    }
}
