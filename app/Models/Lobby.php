<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lobby extends Model
{
    use HasFactory;

    protected $fillable = [
        'lobby_code',
        'host_name',
        'game_settings',
        'players',
        'status',
        'game_state'
    ];

    protected $casts = [
        'game_settings' => 'array',
        'players' => 'array',
        'game_state' => 'array'
    ];

    public static function generateLobbyCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        } while (self::where('lobby_code', $code)->exists());

        return $code;
    }

    public function addPlayer($playerName)
    {
        $players = $this->players ?? [];
        $players[] = [
            'name' => $playerName,
            'joined_at' => now()->toISOString()
        ];
        $this->players = $players;
        $this->save();
    }

    public function addPlayerWithId($playerName, $playerId)
    {
        $players = $this->players ?? [];
        $players[] = [
            'id' => $playerId,
            'name' => $playerName,
            'joined_at' => now()->toISOString()
        ];
        $this->players = $players;
        $this->save();
    }

    public function removePlayer($playerName)
    {
        $players = $this->players ?? [];
        $players = array_filter($players, function($player) use ($playerName) {
            return $player['name'] !== $playerName;
        });
        $this->players = array_values($players);
        $this->save();
    }

    public function getPlayerCount()
    {
        return count($this->players ?? []);
    }
}
