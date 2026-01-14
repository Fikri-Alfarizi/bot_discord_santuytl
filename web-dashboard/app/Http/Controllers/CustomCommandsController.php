<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomCommandsController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        
        $commands = DB::table('custom_commands')
            ->where('guild_id', $guildId)
            ->orderBy('trigger')
            ->get();
            
        // Decode embed data for view
        $commands->transform(function ($cmd) {
            if ($cmd->embed_data) {
                $cmd->embed_data = json_decode($cmd->embed_data, true);
            }
            return $cmd;
        });

        return view('custom-commands.index', compact('commands'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'trigger' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('custom_commands')->where(function ($query) use ($guildId) {
                    return $query->where('guild_id', $guildId);
                })->ignore($request->id),
            ],
            'response' => 'nullable|string|max:2000',
            'is_embed' => 'boolean',
            'embed_title' => 'nullable|string|max:256',
            'embed_description' => 'nullable|string|max:4096',
            'embed_color' => 'nullable|string',
            'embed_image' => 'nullable|url',
        ]);

        $embedData = null;
        if ($request->is_embed) {
            $embedData = [
                'title' => $request->embed_title,
                'description' => $request->embed_description,
                'color' => $request->embed_color,
                'image' => $request->embed_image,
            ];
        }

        if ($request->id) {
            // Update
            DB::table('custom_commands')
                ->where('id', $request->id)
                ->where('guild_id', $guildId)
                ->update([
                    'trigger' => $request->trigger,
                    'response' => $request->response,
                    'is_embed' => $request->is_embed ?? false,
                    'embed_data' => $embedData ? json_encode($embedData) : null,
                    'updated_at' => now(),
                ]);
            $message = 'Command updated successfully!';
        } else {
            // Create
            DB::table('custom_commands')->insert([
                'guild_id' => $guildId,
                'trigger' => $request->trigger,
                'response' => $request->response,
                'is_embed' => $request->is_embed ?? false,
                'embed_data' => $embedData ? json_encode($embedData) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message = 'Command created successfully!';
        }

        return redirect()->route('custom-commands.index', ['guildId' => $guildId])->with('success', $message);
    }

    public function destroy($id)
    {
        $guildId = session('selected_guild_id');
        
        DB::table('custom_commands')
            ->where('id', $id)
            ->where('guild_id', $guildId)
            ->delete();

        return redirect()->route('custom-commands.index', ['guildId' => $guildId])->with('success', 'Command deleted successfully!');
    }
}
