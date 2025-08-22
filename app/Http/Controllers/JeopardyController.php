<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class JeopardyController extends Controller
{
    public function index()
    {
        return view('jeopardy.index');
    }

    public function setup()
    {
        return view('jeopardy.setup');
    }

    public function play()
    {
        return view('jeopardy.play');
    }

    public function startGame(Request $request)
    {
        $request->validate([
            'team_names' => 'required|array|min:2|max:6',
            'team_names.*' => 'required|string|max:50',
            'difficulty' => 'required|string|in:easy,normal,hard,challenging'
        ]);

        $teamNames = $request->team_names;
        $teamCount = count($teamNames);
        $difficulty = $request->difficulty;
        
        $teamColors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
        
        $gameState = [
            'team_count' => $teamCount,
            'current_team' => 1,
            'question_timer' => 30,
            'current_question' => null,
            'game_started' => true,
            'answered_questions' => [],
            'round' => 1,
            'difficulty' => $difficulty
        ];
        
        // Create teams dynamically
        for ($i = 1; $i <= $teamCount; $i++) {
            $gameState['team' . $i] = [
                'name' => $teamNames[$i - 1],
                'score' => 0,
                'color' => $teamColors[$i - 1],
                'timer' => 300 // 5 minutes in seconds
            ];
        }

        Session::put('jeopardy_game', $gameState);
        Session::save(); // Ensure session is saved immediately
        
        return response()->json(['success' => true, 'game_state' => $gameState]);
    }

    public function startCustomGame(Request $request)
    {
        \Log::info('Starting custom game creation with data: ' . json_encode($request->all()));
        
        // Add specific logging for single-player games
        if ($request->game_mode === 'singleplayer') {
            \Log::info('Single-player custom game detected');
        }
        
        $request->validate([
            'team_names' => 'required|array|min:1|max:6',
            'team_names.*' => 'nullable|string|max:50', // Make team names optional
            'categories' => 'required|array|min:1|max:8',
            'game_timer' => 'required|integer|min:60|max:3600', // 1 minute to 1 hour
            'question_timer' => 'required|integer|min:10|max:120', // 10 seconds to 2 minutes
            'category_count' => 'required|integer|min:1|max:8',
            'question_count' => 'required|integer|min:3|max:60',
            'lobby_code' => 'nullable|string|size:6'
        ]);

        $teamNames = $request->team_names;
        $teamCount = count($teamNames);
        $categories = $request->categories;
        
        \Log::info('Team names: ' . json_encode($teamNames));
        \Log::info('Team count: ' . $teamCount);
        $gameTimer = $request->game_timer;
        $questionTimer = $request->question_timer;
        $categoryCount = $request->category_count;
        $questionCount = $request->question_count;
        $lobbyCode = $request->lobby_code;
        
        // If lobby code is provided, update the lobby with game rules
        if ($lobbyCode) {
            $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($lobbyCode))->first();
            
            if (!$lobby) {
                return response()->json(['success' => false, 'error' => 'Lobby not found'], 404);
            }
            
            // Store game rules in the lobby (without revealing questions/answers)
            $gameRules = [
                'game_type' => 'custom', // Add this line to set game type
                'team_names' => $teamNames, // Store the team names as provided (will be processed when game starts)
                'team_count' => $teamCount,
                'game_timer' => $gameTimer,
                'question_timer' => $questionTimer,
                'category_count' => $categoryCount,
                'question_count' => $questionCount,
                'categories' => array_keys($categories), // Only store category names, not questions
                'custom_categories' => $categories, // Store full data for when game starts
                'game_created' => true
            ];
            
            \Log::info('Custom game created with team names: ' . json_encode($teamNames));
            
            $lobby->game_settings = array_merge($lobby->game_settings ?? [], $gameRules);
            $lobby->game_state = null; // Clear any existing game state when creating new game
            $lobby->save();
            
            \Log::info('Custom game rules stored in lobby: ' . json_encode($gameRules));
            
            return response()->json(['success' => true, 'message' => 'Custom game created for lobby']);
        }
        
        // If no lobby code, create regular custom game in session
        // Clear any existing game state to ensure fresh start
        Session::forget('jeopardy_game');
        Session::forget('current_player_team');
        Session::forget('is_host_observer');
        Session::forget('lobby_players');
        Session::forget('player_name');
        Session::forget('current_player_id');
        Session::save();
        
        $teamColors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
        
        $gameState = [
            'team_count' => $teamCount,
            'current_team' => 1,
            'question_timer' => $questionTimer,
            'current_question' => null,
            'game_started' => true,
            'answered_questions' => [],
            'timed_out_questions' => [],
            'round' => 1,
            'difficulty' => 'custom',
            'custom_categories' => $categories,
            'custom_game_timer' => $gameTimer,
            'custom_question_timer' => $questionTimer,
            'category_count' => $categoryCount,
            'question_count' => $questionCount,
            'game_mode' => $request->game_mode ?? 'multiplayer' // Add game mode to distinguish single-player
        ];
        
        // Preserve custom team names if provided, otherwise use defaults
        $finalTeamNames = $teamNames;
        
        // Handle single-player games
        if ($teamCount === 1) {
            if (empty($finalTeamNames[0])) {
                $finalTeamNames[0] = "Player";
            }
        } else if ($teamCount >= 2) {
            // Only override if team names are empty or default values
            if (empty($finalTeamNames[0]) || $finalTeamNames[0] === "Team 1" || $finalTeamNames[0] === "Host") {
                $finalTeamNames[0] = "Player 1";
            }
            if (empty($finalTeamNames[1]) || $finalTeamNames[1] === "Team 2") {
                $finalTeamNames[1] = "Player 2";
            }
        }
        
        // Create teams dynamically
        for ($i = 1; $i <= $teamCount; $i++) {
            $gameState['team' . $i] = [
                'name' => $finalTeamNames[$i - 1],
                'score' => 0,
                'color' => $teamColors[$i - 1],
                'timer' => $gameTimer // Use custom game timer
            ];
        }

        Session::put('jeopardy_game', $gameState);
        Session::save(); // Ensure session is saved immediately
        
        \Log::info('Custom game created successfully: ' . json_encode($gameState));
        
        return response()->json(['success' => true, 'game_state' => $gameState]);
    }

    public function startCustomGameFromLobby(Request $request)
    {
        try {
            $request->validate([
                'lobby_code' => 'required|string|size:6'
            ]);

            $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($request->lobby_code))->first();

            if (!$lobby) {
                return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
            }

            if (!($lobby->game_settings['game_created'] ?? false)) {
                return response()->json(['success' => false, 'message' => 'Custom game not created yet'], 400);
            }

        // Optimize: Load all data at once to reduce database queries
        $settings = $lobby->game_settings ?? [];
        $players = $lobby->players ?? [];
        
        // Validate required settings
        if (!isset($settings['custom_categories']) || empty($settings['custom_categories'])) {
            return response()->json(['success' => false, 'message' => 'Custom categories not found in game settings'], 400);
        }
        
        $gameTimer = $settings['game_timer'] ?? 300;
        $questionTimer = $settings['question_timer'] ?? 30;
        
        // Calculate team count based on non-host players only (host is observer)
        $teamCount = count($players) - 1; // Subtract 1 for host
        if ($teamCount < 1) {
            $teamCount = 1; // Minimum 1 team
        }
        
        // Optimize: Build team names and player IDs in one pass
        $finalTeamNames = [];
        $playerIds = [];
        $teamColors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
        
        // Include only non-host players for playable teams
        for ($i = 1; $i < count($players); $i++) {
            if (isset($players[$i])) {
                $finalTeamNames[] = $players[$i]['name'];
                $playerIds[] = $players[$i]['id'] ?? '00' . ($i + 1);
            }
        }
        
        // Store host ID separately for observer reference
        $hostId = $players[0]['id'] ?? '001';
        
        // Optimize: Build game state efficiently
        $gameState = [
            'team_count' => $teamCount,
            'current_team' => 1,
            'current_player_id' => $playerIds[0] ?? '002', // Start with first visible player
            'player_ids' => $playerIds,
            'host_player_id' => $hostId, // Store host ID separately
            'question_timer' => $questionTimer,
            'current_question' => null,
            'game_started' => true,
            'answered_questions' => [],
            'timed_out_questions' => [],
            'round' => 1,
            'difficulty' => 'custom',
            'custom_categories' => $settings['custom_categories'] ?? [],
            'custom_game_timer' => $gameTimer,
            'custom_question_timer' => $questionTimer,
            'category_count' => $settings['category_count'] ?? 5,
            'question_count' => $settings['question_count'] ?? 5
        ];
        
        // Optimize: Build teams array efficiently (host is observer, not a team)
        for ($i = 1; $i <= $teamCount; $i++) {
            $gameState['team' . $i] = [
                'name' => $finalTeamNames[$i - 1] ?? "Team $i",
                'score' => 0,
                'color' => $teamColors[$i - 1] ?? '#3B82F6',
                'timer' => $gameTimer
            ];
        }
        
        // Debug logging
        \Log::info('Team names set:', $finalTeamNames);
        \Log::info('Player IDs set:', $playerIds);
        
        // Extract team names for logging
        $teamNames = [];
        for ($i = 1; $i <= $teamCount; $i++) {
            $teamKey = 'team' . $i;
            if (isset($gameState[$teamKey])) {
                $teamNames[] = $gameState[$teamKey]['name'];
            }
        }
        \Log::info('Game state teams:', $teamNames);

        // Optimize: Single database update
        $lobby->game_state = $gameState;
        $lobby->status = 'playing';
        $lobby->save();

        // Clear any existing game state and set the new one
        Session::forget('jeopardy_game');
        Session::put('jeopardy_game', $gameState);
        Session::save();
        
        \Log::info('New game state loaded for lobby: ' . $request->lobby_code);
        \Log::info('Game state keys: ' . json_encode(array_keys($gameState)));
        
        return response()->json(['success' => true, 'message' => 'Custom game loaded successfully']);
        } catch (\Exception $e) {
            \Log::error('Error in startCustomGameFromLobby: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    // Optimized method to get real-time game state from lobby
    public function getLobbyGameState(Request $request)
    {
        $request->validate([
            'lobby_code' => 'required|string|size:6',
            '_t' => 'nullable|numeric' // Cache-busting parameter
        ]);

        $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($request->lobby_code))->first();

        if (!$lobby) {
            return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
        }

        $gameState = $lobby->game_state ?? null;
        
        if (!$gameState) {
            return response()->json(['success' => false, 'message' => 'No active game found'], 404);
        }

        // Return essential game state data including custom categories
        $essentialGameState = [
            'team_count' => $gameState['team_count'],
            'current_team' => $gameState['current_team'],
            'current_player_id' => $gameState['current_player_id'] ?? null,
            'player_ids' => $gameState['player_ids'] ?? [],
            'current_question' => $gameState['current_question'],
            'answered_questions' => $gameState['answered_questions'] ?? [],
            'game_started' => $gameState['game_started'] ?? false,
            'custom_question_timer' => $gameState['custom_question_timer'] ?? 30,
            'custom_categories' => $gameState['custom_categories'] ?? [],
            'difficulty' => $gameState['difficulty'] ?? 'custom',
            'category_count' => $gameState['category_count'] ?? 5,
            'question_count' => $gameState['question_count'] ?? 5
        ];

        // Add team data
        for ($i = 1; $i <= $gameState['team_count']; $i++) {
            $teamKey = 'team' . $i;
            if (isset($gameState[$teamKey])) {
                $essentialGameState[$teamKey] = [
                    'name' => $gameState[$teamKey]['name'],
                    'score' => $gameState[$teamKey]['score'],
                    'timer' => $gameState[$teamKey]['timer']
                ];
            }
        }

        // Debug logging
        $teamNames = [];
        for ($i = 1; $i <= $essentialGameState['team_count']; $i++) {
            $teamKey = 'team' . $i;
            if (isset($essentialGameState[$teamKey])) {
                $teamNames[] = $essentialGameState[$teamKey]['name'];
            }
        }
        \Log::info('getLobbyGameState - Team names being returned:', $teamNames);
        
        return response()->json(['success' => true, 'game_state' => $essentialGameState]);
    }

    // New method to update game state in lobby
    public function updateLobbyGameState(Request $request)
    {
        $request->validate([
            'lobby_code' => 'required|string|size:6',
            'game_state' => 'required|array'
        ]);

        $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($request->lobby_code))->first();

        if (!$lobby) {
            return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
        }

        $lobby->game_state = $request->game_state;
        $lobby->save();

        return response()->json(['success' => true, 'message' => 'Game state updated']);
    }

    public function getGameState()
    {
        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState) {
            return response()->json(['success' => false, 'message' => 'No active game found']);
        }
        
        // Try to find the lobby code for this game and get the most up-to-date game state
        $lobbyCode = null;
        if (isset($gameState['difficulty']) && $gameState['difficulty'] === 'custom') {
            $lobbies = \App\Models\Lobby::where('status', 'playing')->get();
            foreach ($lobbies as $lobby) {
                if (isset($lobby->game_state) && 
                    isset($lobby->game_state['custom_categories']) && 
                    $lobby->game_state['custom_categories'] === $gameState['custom_categories']) {
                    $lobbyCode = $lobby->lobby_code;
                    
                    // Use the lobby game state if it's more recent (has custom team names)
                    if (isset($lobby->game_state) && is_array($lobby->game_state)) {
                        // Check if lobby has custom team names
                        $hasCustomNames = false;
                        for ($i = 1; $i <= ($lobby->game_state['team_count'] ?? 0); $i++) {
                            $teamKey = "team{$i}";
                            if (isset($lobby->game_state[$teamKey]) && 
                                isset($lobby->game_state[$teamKey]['name']) && 
                                !in_array($lobby->game_state[$teamKey]['name'], ['Player 1', 'Player 2', 'Team 1', 'Team 2'])) {
                                $hasCustomNames = true;
                                break;
                            }
                        }
                        
                        if ($hasCustomNames) {
                            $gameState = $lobby->game_state;
                            \Log::info('Using lobby game state with custom team names');
                        }
                    }
                    break;
                }
            }
        }
        
        // Get current player team for debugging
        $currentPlayerTeam = $this->getCurrentPlayerTeam();
        
        // Check if current question should be shown to this player
        $currentPlayerId = $this->getCurrentPlayerId();
        $questionOwner = $gameState['question_owner'] ?? null;
        
        // Only show question to the player who owns it
        if (isset($gameState['current_question']) && $questionOwner !== $currentPlayerId) {
            // Hide question from other players
            $gameState['current_question'] = null;
            $gameState['question_owner'] = null;
        }
        
        // Debug logging for game state request
        \Log::info('Game state request debug:');
        \Log::info('Session ID: ' . Session::getId());
        \Log::info('Current player team: ' . $currentPlayerTeam);
        \Log::info('Current player ID: ' . $currentPlayerId);
        \Log::info('Question owner: ' . $questionOwner);
        \Log::info('Lobby players: ' . json_encode(Session::get('lobby_players', [])));
        \Log::info('Game state current team: ' . ($gameState['current_team'] ?? 'N/A'));
        
        return response()->json([
            'success' => true, 
            'game_state' => $gameState,
            'lobby_code' => $lobbyCode,
            'current_player_team' => $currentPlayerTeam,
            'session_id' => Session::getId(),
            'host_session_id' => Session::get('host_session_id'),
            'lobby_players' => Session::get('lobby_players', [])
        ]);
    }

    private function getCurrentPlayerTeam()
    {
        // Get the current player's team from session
        $playerTeam = Session::get('current_player_team');
        
        if ($playerTeam !== null) {
            return $playerTeam;
        }
        
        // If no team is assigned, try to determine based on lobby
        $gameState = Session::get('jeopardy_game');
        
        // Check if this is a single-player game
        if (isset($gameState['game_mode']) && $gameState['game_mode'] === 'singleplayer') {
            // For single-player games, always assign to team 1
            $playerTeam = 1;
            Session::put('current_player_team', 1);
            Session::save();
            
            \Log::info("Single-player game detected - assigned to team 1");
            return $playerTeam;
        }
        
        if (isset($gameState['difficulty']) && $gameState['difficulty'] === 'custom') {
            // Find the lobby for this game
            $lobbies = \App\Models\Lobby::where('status', 'playing')->get();
            foreach ($lobbies as $lobby) {
                if (isset($lobby->game_state) && 
                    isset($lobby->game_state['custom_categories']) && 
                    $lobby->game_state['custom_categories'] === $gameState['custom_categories']) {
                    
                    // Get the current user's session ID to identify them
                    $sessionId = Session::getId();
                    
                    // Check if this player is the host
                    $isHost = $this->isCurrentPlayerHost($lobby);
                    
                    if ($isHost) {
                        // Host can participate but doesn't have turns - assign to team 1
                        $playerTeam = 1;
                        Session::put('current_player_team', 1);
                        Session::put('is_host', true);
                        Session::save();
                        
                        \Log::info("Host assigned to team 1 (can participate) for session ID {$sessionId}");
                        return $playerTeam;
                    }
                    
                    // For non-host players, assign them to teams based on their order of joining
                    // Get the lobby players and find this session's position
                    $players = $lobby->players ?? [];
                    $playerIndex = -1;
                    
                    // Find this player's index in the lobby (skip host at index 0)
                    for ($i = 1; $i < count($players); $i++) {
                        // For now, we'll use a simple approach: assign based on session order
                        // In a real implementation, you'd store the player name when they join
                        if ($i === 1) {
                            $playerTeam = 2; // Host is team 1, so non-host players start at team 2
                        } elseif ($i === 2) {
                            $playerTeam = 3;
                        } else {
                            $playerTeam = min($i + 1, $gameState['team_count']);
                        }
                        break;
                    }
                    
                    // If we couldn't determine the team, use session-based assignment as fallback
                    if ($playerTeam === null) {
                        $existingPlayers = Session::get('lobby_players', []);
                        
                        if (isset($existingPlayers[$sessionId])) {
                            // Player already assigned
                            $playerTeam = $existingPlayers[$sessionId];
                        } else {
                            // New player - assign to next available team
                            $assignedTeams = array_values($existingPlayers);
                            $nextTeam = 2; // Start from team 2 since host is team 1
                            
                            // Find the next available team number
                            while (in_array($nextTeam, $assignedTeams)) {
                                $nextTeam++;
                            }
                            
                            // Make sure we don't exceed the team count
                            if ($nextTeam <= $gameState['team_count']) {
                                $playerTeam = $nextTeam;
                                $existingPlayers[$sessionId] = $nextTeam;
                                Session::put('lobby_players', $existingPlayers);
                                Session::put('current_player_team', $nextTeam);
                                Session::save();
                                
                                \Log::info("Fallback: Assigned player with session ID {$sessionId} to team {$nextTeam}");
                            } else {
                                // All teams are taken, assign to team 1 as fallback
                                $playerTeam = 1;
                                Session::put('current_player_team', 1);
                                Session::save();
                                
                                \Log::info("All teams taken, assigned player with session ID {$sessionId} to team 1 as fallback");
                            }
                        }
                    } else {
                        // Store the assigned team
                        Session::put('current_player_team', $playerTeam);
                        Session::save();
                        
                        \Log::info("Assigned player with session ID {$sessionId} to team {$playerTeam}");
                    }
                    
                    break;
                }
            }
        }
        
        return $playerTeam;
    }

    private function isCurrentPlayerHost($lobby)
    {
        // Check if current session belongs to the host
        $sessionId = Session::getId();
        
        // Get the host's session ID from the lobby
        $hostSessionId = Session::get('host_session_id');
        
        // If we have a stored host session ID, compare it
        if ($hostSessionId && $hostSessionId === $sessionId) {
            return true;
        }
        
        // Alternative: check if this session created the lobby
        // This is a fallback method
        $lobbyCreatedBySession = Session::get('lobby_created_by_session');
        if ($lobbyCreatedBySession && $lobbyCreatedBySession === $sessionId) {
            return true;
        }
        
        return false;
    }

    private function getPlayerNameFromSession($lobby, $sessionId)
    {
        // Try to get player name from session storage
        $playerName = Session::get('player_name');
        
        if ($playerName) {
            return $playerName;
        }
        
        // If not in session, try to determine from lobby players
        // This is a fallback method - in a real implementation, you'd store the player name when they join
        $players = $lobby->players ?? [];
        
        // For now, we'll use a simple approach: check if this session is the host
        if ($this->isCurrentPlayerHost($lobby)) {
            return $lobby->host_name ?? 'Host';
        }
        
        // For other players, we need to store their names when they join the game
        // This would require updating the frontend to send player names
        return null;
    }

    private function findTeamByPlayerName($gameState, $playerName)
    {
        // Find which team this player belongs to by matching their name
        $teamCount = $gameState['team_count'] ?? 0;
        
        for ($i = 1; $i <= $teamCount; $i++) {
            $teamKey = 'team' . $i;
            if (isset($gameState[$teamKey]) && isset($gameState[$teamKey]['name'])) {
                if ($gameState[$teamKey]['name'] === $playerName) {
                    return $i;
                }
            }
        }
        
        return null;
    }

    public function selectQuestion(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'value' => 'required|integer'
        ]);

        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }

        // Debug logging for turn validation
        \Log::info('=== TURN VALIDATION DEBUG ===');
        \Log::info('Game state difficulty: ' . ($gameState['difficulty'] ?? 'not set'));
        \Log::info('Game mode: ' . ($gameState['game_mode'] ?? 'not set'));
        \Log::info('Is custom game: ' . (isset($gameState['difficulty']) && $gameState['difficulty'] === 'custom' ? 'yes' : 'no'));
        \Log::info('Is multiplayer: ' . (isset($gameState['game_mode']) && $gameState['game_mode'] === 'multiplayer' ? 'yes' : 'no'));
        
        // Check if this is a custom game and validate turn
        if (isset($gameState['difficulty']) && $gameState['difficulty'] === 'custom') {
            $currentPlayerId = $gameState['current_player_id'] ?? '001';
            $playerId = $this->getCurrentPlayerId();
            
            // Debug logging for turn validation
            \Log::info('Turn validation debug:');
            \Log::info('Current player ID: ' . $currentPlayerId);
            \Log::info('Player ID: ' . $playerId);
            \Log::info('Session ID: ' . Session::getId());
            
                    // Host can participate but doesn't have turns - no restrictions
            
            // Check if it's this player's turn
            if ($playerId === null) {
                \Log::info('Turn validation failed: Player has no valid ID');
                return response()->json([
                    'success' => false,
                    'error' => 'Player not properly assigned',
                    'current_player_id' => $currentPlayerId,
                    'player_id' => $playerId
                ], 403);
            }
            
            // Check if it's this player's turn
            if ($playerId === $currentPlayerId) {
                \Log::info('Turn validation passed: Player ID ' . $playerId . ' === Current player ID ' . $currentPlayerId);
            } else {
                \Log::info('Turn validation failed: Player ID ' . $playerId . ' !== Current player ID ' . $currentPlayerId);
                // Get the current team name for better error message
                $currentTeamName = 'Unknown Team';
                if (isset($gameState['current_team']) && isset($gameState['team' . $gameState['current_team']])) {
                    $currentTeamName = $gameState['team' . $gameState['current_team']]['name'];
                }
                
                return response()->json([
                    'success' => false,
                    'error' => "It's {$currentTeamName}'s turn! Please wait for your turn.",
                    'current_player_id' => $currentPlayerId,
                    'player_id' => $playerId,
                    'current_team' => $gameState['current_team'] ?? 'unknown',
                    'current_team_name' => $currentTeamName
                ], 403);
            }
        }

        $questionKey = $request->category . '_' . $request->value;
        
        // Debug logging
        \Log::info('Selecting question: ' . $questionKey);
        \Log::info('Current answered questions: ' . json_encode($gameState['answered_questions'] ?? []));
        \Log::info('Timed out questions: ' . json_encode($gameState['timed_out_questions'] ?? []));
        
        // Check if question is already answered
        if (in_array($questionKey, $gameState['answered_questions'] ?? [])) {
            \Log::info('Question already answered, rejecting: ' . $questionKey);
            return response()->json(['error' => 'Question already answered'], 400);
        }
        
        // Check if this is a steal attempt (question was timed out)
        $isStealAttempt = in_array($questionKey, $gameState['timed_out_questions'] ?? []);
        
        if ($isStealAttempt) {
            \Log::info('Steal attempt detected for question: ' . $questionKey);
        }

        $gameState = Session::get('jeopardy_game');
        $difficulty = $gameState['difficulty'] ?? 'normal';
        
        \Log::info('Getting question for category: ' . $request->category . ', value: ' . $request->value . ', difficulty: ' . $difficulty);
        
        $question = $this->getQuestion($request->category, $request->value, $difficulty);
        
        \Log::info('Question retrieved: ' . json_encode($question));
        
        // Check if question was found
        if (!$question) {
            \Log::error('Question not found for category: ' . $request->category . ', value: ' . $request->value);
            return response()->json([
                'success' => false,
                'error' => 'Question not found for the selected category and value'
            ], 404);
        }
        
        // Store question only for the current player's session
        $currentPlayerId = $gameState['current_player_id'] ?? '001';
        $gameState['current_question'] = $question;
        $gameState['question_owner'] = $currentPlayerId; // Track who owns the current question
        $gameState['question_selected_at'] = time(); // Track when question was selected
        
        // Use custom question timer if available, otherwise default to 30
        $questionTimer = $gameState['custom_question_timer'] ?? 30;
        $gameState['question_timer'] = $questionTimer;
        $gameState['is_steal_attempt'] = $isStealAttempt; // Track if this is a steal attempt
        
        Session::put('jeopardy_game', $gameState);
        Session::save(); // Ensure session is saved immediately
        
        // Update lobby game state if this is a lobby game
        $this->updateLobbyGameStateIfNeeded($gameState);
        
        return response()->json([
            'success' => true,
            'question' => $question,
            'question_owner' => $currentPlayerId,
            'timer' => $questionTimer,
            'is_steal_attempt' => $isStealAttempt
        ]);
    }

    public function submitAnswer(Request $request)
    {
        $gameState = Session::get('jeopardy_game');
        $maxTimer = $gameState['custom_question_timer'] ?? 30;
        
        $request->validate([
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:0|max:' . $maxTimer // Time taken to answer in seconds
        ]);

        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState || !$gameState['current_question']) {
            return response()->json(['error' => 'No active question'], 400);
        }

        // Check if this is a custom game and validate turn
        if (isset($gameState['difficulty']) && $gameState['difficulty'] === 'custom') {
            $currentPlayerId = $gameState['current_player_id'] ?? '001';
            $playerId = $this->getCurrentPlayerId();
            $questionOwner = $gameState['question_owner'] ?? null;
            
            // Debug logging for answer validation
            \Log::info('Answer validation debug:');
            \Log::info('Current player ID: ' . $currentPlayerId);
            \Log::info('Player ID: ' . $playerId);
            \Log::info('Question owner: ' . $questionOwner);
            \Log::info('Session ID: ' . Session::getId());
            
            // Check if player has a valid ID
            if ($playerId === null) {
                \Log::info('Answer validation failed: Player has no valid ID');
                return response()->json([
                    'success' => false,
                    'error' => 'Player not properly assigned'
                ], 403);
            }
            
            // Check if it's this player's turn and they own the question
            if ($playerId !== $currentPlayerId) {
                \Log::info('Answer validation failed: Not player\'s turn');
                return response()->json([
                    'success' => false,
                    'error' => 'Not your turn to answer'
                ], 403);
            }
            
            // Check if this player owns the current question
            if ($questionOwner !== $playerId) {
                \Log::info('Answer validation failed: Player does not own the question');
                return response()->json([
                    'success' => false,
                    'error' => 'You do not own the current question'
                ], 403);
            }
        }

        $currentTeam = 'team' . $gameState['current_team'];
        $question = $gameState['current_question'];
        
        // Validate question structure
        if (!isset($question['answer']) || !isset($question['category']) || !isset($question['value'])) {
            \Log::error('Invalid question structure in submitAnswer: ' . json_encode($question));
            return response()->json([
                'success' => false,
                'error' => 'Invalid question data'
            ], 400);
        }
        
        $questionKey = $question['category'] . '_' . ($question['original_value'] ?? $question['value']);
        $isStealAttempt = $gameState['is_steal_attempt'] ?? false;
        $timeTaken = $request->time_taken;

        // Automatically check if the answer is correct
        $userAnswer = strtolower(trim($request->answer));
        $correctAnswer = strtolower(trim($question['answer']));
        $isCorrect = $userAnswer === $correctAnswer;
        
        // Debug logging for answer validation
        \Log::info('Answer validation debug:');
        \Log::info('User answer: "' . $userAnswer . '"');
        \Log::info('Correct answer: "' . $correctAnswer . '"');
        \Log::info('Is correct: ' . ($isCorrect ? 'true' : 'false'));
        \Log::info('Answer comparison: ' . ($userAnswer === $correctAnswer ? 'true' : 'false'));

        if ($isCorrect) {
            $oldScore = $gameState[$currentTeam]['score'];
            $gameState[$currentTeam]['score'] += ($question['original_value'] ?? $question['value']);
            $stealMessage = $isStealAttempt ? ' (STOLEN!)' : '';
            \Log::info("Correct answer{$stealMessage}: {$oldScore} + " . ($question['original_value'] ?? $question['value']) . " = {$gameState[$currentTeam]['score']}");
            
            // Deduct the time taken to answer from the player's timer
            $oldTimer = $gameState[$currentTeam]['timer'];
            $gameState[$currentTeam]['timer'] = max(0, $gameState[$currentTeam]['timer'] - $timeTaken);
            \Log::info("Time deduction for correct answer: {$oldTimer} - {$timeTaken} = {$gameState[$currentTeam]['timer']}");
        } else {
            // No point deduction for wrong answer - only deduct time penalty
            $stealMessage = $isStealAttempt ? ' (steal attempt failed)' : '';
            \Log::info("Incorrect answer{$stealMessage}: No points deducted, only time penalty");
            // Deduct 30 seconds from the current team's timer for wrong answer
            $gameState[$currentTeam]['timer'] = max(0, $gameState[$currentTeam]['timer'] - 30);
        }

        // Check if current team ran out of time
        if ($gameState[$currentTeam]['timer'] <= 0) {
            $gameState['game_over'] = true;
            $gameState['winner'] = $gameState['current_team'] == 1 ? 2 : 1;
        }

        // Add to answered questions with correctness information
        if (!isset($gameState['answered_questions'])) {
            $gameState['answered_questions'] = [];
        }
        $answeredQuestionData = [
            'key' => $questionKey,
            'correct' => $isCorrect
        ];
        $gameState['answered_questions'][] = $answeredQuestionData;
        
        // Debug logging for answered question storage
        \Log::info('Stored answered question data: ' . json_encode($answeredQuestionData));
        \Log::info('Full answered questions array: ' . json_encode($gameState['answered_questions']));
        
        // Remove from timed out questions if it was a steal attempt
        if ($isStealAttempt && isset($gameState['timed_out_questions'])) {
            $gameState['timed_out_questions'] = array_filter($gameState['timed_out_questions'], function($q) use ($questionKey) {
                return $q !== $questionKey;
            });
            \Log::info('Question removed from timed out questions: ' . $questionKey);
        }
        
        // Check if all questions have been answered (5 categories × 5 values = 25 questions)
        if (count($gameState['answered_questions']) >= 25) {
            $gameState['game_over'] = true;
            // Find the team with the highest score
            $winnerTeam = 1;
            $highestScore = $gameState['team1']['score'];
            for ($i = 2; $i <= $gameState['team_count']; $i++) {
                if ($gameState["team$i"]['score'] > $highestScore) {
                    $highestScore = $gameState["team$i"]['score'];
                    $winnerTeam = $i;
                }
            }
            $gameState['winner'] = $winnerTeam;
        }
        
        // Debug logging
        \Log::info('Question answered: ' . $questionKey);
        \Log::info('Question value: ' . ($question['original_value'] ?? $question['value']));
        \Log::info('Is correct: ' . ($isCorrect ? 'true' : 'false'));
        \Log::info('Team score before: ' . $gameState[$currentTeam]['score']);
        \Log::info('Current team: ' . $currentTeam);
        \Log::info('All team scores before: ' . json_encode([
            'team1' => $gameState['team1']['score'] ?? 'N/A',
            'team2' => $gameState['team2']['score'] ?? 'N/A'
        ]));
        \Log::info('Answered questions array: ' . json_encode($gameState['answered_questions']));
        \Log::info('Game state keys: ' . json_encode(array_keys($gameState)));
        
        // DO NOT advance turn here - let the frontend handle turn advancement
        // This prevents double advancement and ensures proper turn flow
        \Log::info("=== ANSWER SUBMITTED ===");
        \Log::info("Team: {$gameState['current_team']} ({$gameState[$currentTeam]['name']})");
        \Log::info("Answer correct: " . ($isCorrect ? 'YES' : 'NO'));
        \Log::info("Turn advancement will be handled by frontend");
        
        // Clear current question and ownership
        $gameState['current_question'] = null;
        $gameState['question_owner'] = null;
        $gameState['question_timer'] = null;
        $gameState['is_steal_attempt'] = false;
        
        \Log::info('All team scores after: ' . json_encode([
            'team1' => $gameState['team1']['score'] ?? 'N/A',
            'team2' => $gameState['team2']['score'] ?? 'N/A'
        ]));
        \Log::info('New current team: ' . $gameState['current_team']);
        
        Session::put('jeopardy_game', $gameState);
        Session::save(); // Ensure session is saved immediately
        
        // Update lobby game state if this is a lobby game
        $this->updateLobbyGameStateIfNeeded($gameState);
        
        \Log::info('Session saved. Current session data: ' . json_encode(Session::get('jeopardy_game')));
        
        return response()->json([
            'success' => true,
            'game_state' => $gameState,
            'correct_answer' => $question['answer'],
            'is_correct' => $isCorrect,
            'is_steal_attempt' => $isStealAttempt,
            'points_earned' => $isCorrect ? ($question['original_value'] ?? $question['value']) : 0
        ]);
    }

    // Helper method to update lobby game state if needed
    private function updateLobbyGameStateIfNeeded($gameState)
    {
        // Check if this is a lobby game by looking for lobby-related session data
        // This is a simplified approach - in a real implementation, you might want to store the lobby code in session
        if (isset($gameState['difficulty']) && $gameState['difficulty'] === 'custom') {
            // Try to find the lobby that has this game state
            $lobbies = \App\Models\Lobby::where('status', 'playing')->get();
            foreach ($lobbies as $lobby) {
                if (isset($lobby->game_state) && 
                    isset($lobby->game_state['custom_categories']) && 
                    $lobby->game_state['custom_categories'] === $gameState['custom_categories']) {
                    
                    // Create a copy of game state for synchronization
                    $syncGameState = $gameState;
                    
                    // Remove question content for other players but keep metadata
                    if (isset($syncGameState['current_question'])) {
                        // Keep only category, value, and owner info - remove question and answer content
                        $syncGameState['current_question'] = [
                            'category' => $syncGameState['current_question']['category'],
                            'value' => $syncGameState['current_question']['value'],
                            'selected' => true // Flag to indicate a question was selected
                        ];
                    }
                    
                    // Keep question timer for synchronization - all players should see the same timer
                    // The question timer will be synced every second to keep all players in sync
                    
                    $lobby->game_state = $syncGameState;
                    $lobby->save();
                    \Log::info('Updated lobby game state for lobby: ' . $lobby->lobby_code);
                    break;
                }
            }
        }
    }

    public function updateTimer(Request $request)
    {
        $gameState = Session::get('jeopardy_game');
        $maxTimer = $gameState['custom_question_timer'] ?? 30;
        $maxGameTimer = $gameState['custom_game_timer'] ?? 300;
        
        $request->validate([
            'time_remaining' => 'required|integer|min:0|max:' . $maxTimer,
            'team_timer' => 'required|integer|min:0|max:' . $maxGameTimer
        ]);

        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }

        $gameState['question_timer'] = $request->time_remaining;
        $currentTeam = 'team' . $gameState['current_team'];
        $gameState[$currentTeam]['timer'] = $request->team_timer;
        
        if ($request->time_remaining <= 0) {
            // Question time's up - but don't mark as answered, allow opposing team to steal
            if ($gameState['current_question']) {
                $question = $gameState['current_question'];
                $questionKey = $question['category'] . '_' . ($question['original_value'] ?? $question['value']);
                
                \Log::info('Timer expired for question: ' . $questionKey . ' - allowing steal opportunity');
                
                // Deduct time from the team that failed to answer (current team before switching)
                $failedTeam = 'team' . $gameState['current_team'];
                $gameState[$failedTeam]['timer'] = max(0, $gameState[$failedTeam]['timer'] - 30);
                \Log::info("Deducted 30 seconds from {$failedTeam} due to timer expiration. New time: {$gameState[$failedTeam]['timer']}");
                
                // Add to timed out questions for steal opportunities
                if (!isset($gameState['timed_out_questions'])) {
                    $gameState['timed_out_questions'] = [];
                }
                $gameState['timed_out_questions'][] = $questionKey;
                
                \Log::info('Question added to timed out questions for stealing: ' . $questionKey);
            }
            
            // Switch teams (cycle through all teams)
            $oldTeam = $gameState['current_team'];
            $gameState['current_team'] = $gameState['current_team'] >= $gameState['team_count'] ? 1 : $gameState['current_team'] + 1;
            $gameState['current_question'] = null;
            
            \Log::info("Team switched from team{$oldTeam} to team{$gameState['current_team']} due to timer expiration - steal opportunity available");
        }
        
        Session::put('jeopardy_game', $gameState);
        Session::save(); // Ensure session is saved immediately
        
        // Don't update lobby game state for timer to avoid conflicts
        // Each player manages their own timer independently
        
        return response()->json(['success' => true, 'game_state' => $gameState]);
    }

    public function advanceTurn(Request $request)
    {
        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }

        $oldTeam = $gameState['current_team'];
        $teamCount = $gameState['team_count'];
        
        // Log the current state before advancement
        \Log::info("=== TURN ADVANCEMENT DEBUG ===");
        \Log::info("Current team: {$oldTeam}");
        \Log::info("Total teams: {$teamCount}");
        \Log::info("All teams:");
        for ($i = 1; $i <= $teamCount; $i++) {
            $teamName = $gameState["team{$i}"]['name'];
            \Log::info("  Team {$i}: {$teamName}");
        }
        
        // Calculate expected next team
        $expectedNextTeam = ($oldTeam % $teamCount) + 1;
        
        // Advance to next team
        $gameState['current_team'] = $gameState['current_team'] == $teamCount ? 1 : $gameState['current_team'] + 1;
        
        // Update current player ID to match the new team
        if (isset($gameState['player_ids']) && isset($gameState['current_team'])) {
            $newTeamIndex = $gameState['current_team'] - 1; // Convert to 0-based index
            \Log::info("Turn advancement debug - Team: {$gameState['current_team']}, Index: {$newTeamIndex}, Player IDs: " . json_encode($gameState['player_ids']));
            
            if (isset($gameState['player_ids'][$newTeamIndex])) {
                $gameState['current_player_id'] = $gameState['player_ids'][$newTeamIndex];
                \Log::info("Updated current player ID to: {$gameState['current_player_id']} for team {$gameState['current_team']}");
            } else {
                \Log::warning("Player ID not found for team {$gameState['current_team']}, available IDs: " . json_encode($gameState['player_ids']));
                // Fallback: use the first available player ID
                if (!empty($gameState['player_ids'])) {
                    $gameState['current_player_id'] = $gameState['player_ids'][0];
                    \Log::info("Fallback: Using first player ID: {$gameState['current_player_id']}");
                }
            }
        }
        
        \Log::info("Turn advancement: Team {$oldTeam} -> Team {$gameState['current_team']} (Total teams: {$teamCount})");
        \Log::info("Expected next team: {$expectedNextTeam}");
        \Log::info("Actual next team: {$gameState['current_team']}");
        \Log::info("Current player ID: " . ($gameState['current_player_id'] ?? 'NOT SET'));
        
        // Clear current question and ownership
        $gameState['current_question'] = null;
        $gameState['question_owner'] = null;
        $gameState['question_timer'] = null;
        $gameState['is_steal_attempt'] = false;

        Session::put('jeopardy_game', $gameState);
        Session::save();

        // Update lobby game state if this is a lobby game
        $this->updateLobbyGameStateIfNeeded($gameState);

        return response()->json([
            'success' => true,
            'game_state' => $gameState
        ]);
    }

    public function resetGame()
    {
        Session::forget('jeopardy_game');
        Session::save(); // Ensure session is saved immediately
        
        // Also clear any lobby game states that might be active
        $lobbies = \App\Models\Lobby::where('status', 'playing')->get();
        foreach ($lobbies as $lobby) {
            $lobby->game_state = null;
            $lobby->status = 'waiting';
            $lobby->save();
        }
        
        return response()->json(['success' => true]);
    }

    public function resetLobbyGame(Request $request)
    {
        $request->validate([
            'lobby_code' => 'required|string|size:6'
        ]);

        $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($request->lobby_code))->first();

        if (!$lobby) {
            return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
        }

        // Clear the lobby's game state and reset status
        $lobby->game_state = null;
        $lobby->status = 'waiting';
        $lobby->save();

        return response()->json(['success' => true, 'message' => 'Lobby game reset successfully']);
    }

    public function testDeduction()
    {
        $testScore = 5;
        $deduction = 3;
        $result = max(0, $testScore - $deduction);
        
        \Log::info("Test deduction: {$testScore} - {$deduction} = {$result}");
        
        return response()->json([
            'test_score' => $testScore,
            'deduction' => $deduction,
            'result' => $result,
            'working' => $result === 2
        ]);
    }

    public function debugGameState()
    {
        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game']);
        }
        
        return response()->json([
            'game_state' => $gameState,
            'team_scores' => [
                'team1' => $gameState['team1']['score'] ?? 'N/A',
                'team2' => $gameState['team2']['score'] ?? 'N/A'
            ],
            'current_team' => $gameState['current_team'] ?? 'N/A',
            'answered_questions' => $gameState['answered_questions'] ?? []
        ]);
    }

    public function debugPlayer()
    {
        $gameState = Session::get('jeopardy_game');
        $currentPlayerTeam = $this->getCurrentPlayerTeam();
        
        return response()->json([
            'current_player_team' => $currentPlayerTeam,
            'session_id' => Session::getId(),
            'lobby_players' => Session::get('lobby_players', []),
            'game_state' => $gameState ? [  
                'current_team' => $gameState['current_team'] ?? null,
                'team_count' => $gameState['team_count'] ?? null,
                'difficulty' => $gameState['difficulty'] ?? null
            ] : null
        ]);
    }

    public function switchPlayer(Request $request)
    {
        $request->validate([
            'team' => 'required|integer|min:1|max:6'
        ]);
        
        $team = $request->team;
        $sessionId = Session::getId();
        
        // Get current game state to validate team number
        $gameState = Session::get('jeopardy_game');
        if ($gameState && isset($gameState['team_count']) && $team > $gameState['team_count']) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid team number'
            ], 400);
        }
        
        // Update the player's team
        Session::put('current_player_team', $team);
        
        // Update lobby players if this is a lobby game
        $existingPlayers = Session::get('lobby_players', []);
        $existingPlayers[$sessionId] = $team;
        Session::put('lobby_players', $existingPlayers);
        Session::save();
        
        \Log::info("Player switched to team {$team} for session {$sessionId}");
        
        return response()->json([
            'success' => true,
            'player_team' => $team,
            'message' => "Switched to Team {$team}"
        ]);
    }

    public function autoAssignPlayer(Request $request)
    {
        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState) {
            return response()->json([
                'success' => false,
                'error' => 'No active game found'
            ], 400);
        }
        
        $sessionId = Session::getId();
        $existingPlayers = Session::get('lobby_players', []);
        
        // Check if this player is the host
        $hostSessionId = Session::get('host_session_id');
        $isHost = ($hostSessionId === $sessionId);
        
        if ($isHost) {
            // Host can participate but doesn't have turns - assign to team 1
            $existingPlayers[$sessionId] = 1;
            Session::put('lobby_players', $existingPlayers);
            Session::put('current_player_team', 1);
            Session::put('is_host', true);
            Session::save();
            
            return response()->json([
                'success' => true,
                'player_team' => 1,
                'message' => "Host assigned to Team 1 (can participate)"
            ]);
        }
        
        // For non-host players, assign to available teams (start from team 2 since host is team 1)
        $assignedTeams = array_values($existingPlayers);
        $nextTeam = 2;
        
        // Find the next available team number
        while (in_array($nextTeam, $assignedTeams)) {
            $nextTeam++;
        }
        
        // Check if this session is already assigned
        if (isset($existingPlayers[$sessionId])) {
            $assignedTeam = $existingPlayers[$sessionId];
            return response()->json([
                'success' => true,
                'player_team' => $assignedTeam,
                'message' => "Already assigned to Team {$assignedTeam}"
            ]);
        }
        
        // Find the next available team
        $assignedTeams = array_values($existingPlayers);
        $nextTeam = 1;
        
        while (in_array($nextTeam, $assignedTeams)) {
            $nextTeam++;
        }
        
        // Make sure we don't exceed the team count
        if ($nextTeam <= $gameState['team_count']) {
            $existingPlayers[$sessionId] = $nextTeam;
            Session::put('lobby_players', $existingPlayers);
            Session::put('current_player_team', $nextTeam);
            Session::save();
            
            \Log::info("Auto-assigned player with session ID {$sessionId} to team {$nextTeam}");
            
            return response()->json([
                'success' => true,
                'player_team' => $nextTeam,
                'message' => "Auto-assigned to Team {$nextTeam}"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'All teams are already taken'
            ], 400);
        }
    }

    private function getQuestion($category, $value, $difficulty = 'normal')
    {
        // Check if this is a custom game
        $gameState = Session::get('jeopardy_game');
        if ($gameState && isset($gameState['custom_categories'])) {
            \Log::info('Using custom categories for question');
            \Log::info('Available categories: ' . json_encode(array_keys($gameState['custom_categories'])));
            \Log::info('Requested category: ' . $category . ' (type: ' . gettype($category) . ')');
            \Log::info('Requested value: ' . $value . ' (type: ' . gettype($value) . ')');
            
            // Use custom categories
            $customCategories = $gameState['custom_categories'];
            
            if (isset($customCategories[$category])) {
                \Log::info('Category found: ' . $category);
                \Log::info('Available values in category: ' . json_encode(array_keys($customCategories[$category])));
                
                // Try both string and integer versions of the value
                $questionData = null;
                if (isset($customCategories[$category][$value])) {
                    $questionData = $customCategories[$category][$value];
                    \Log::info('Value found in category: ' . $value);
                } else if (isset($customCategories[$category][(string)$value])) {
                    $questionData = $customCategories[$category][(string)$value];
                    \Log::info('Value found as string in category: ' . (string)$value);
                } else if (isset($customCategories[$category][(int)$value])) {
                    $questionData = $customCategories[$category][(int)$value];
                    \Log::info('Value found as int in category: ' . (int)$value);
                }
                
                if ($questionData) {
                    \Log::info('Question data structure: ' . json_encode($questionData));
                    
                    // Handle different possible data structures
                    $selectedQuestion = null;
                    
                    if (is_array($questionData)) {
                        if (isset($questionData[0]) && is_array($questionData[0])) {
                            // Array of questions format - take the first question
                            $selectedQuestion = $questionData[0];
                        } else if (isset($questionData['question']) && isset($questionData['answer'])) {
                            // Direct question format
                            $selectedQuestion = $questionData;
                        } else if (count($questionData) > 0) {
                            // Single question in array format
                            $selectedQuestion = $questionData;
                        }
                    } else if (is_object($questionData)) {
                        // Convert object to array
                        $selectedQuestion = (array)$questionData;
                    }
                    
                    // Additional validation and fixing
                    if ($selectedQuestion) {
                        // Ensure we have the required fields
                        if (!isset($selectedQuestion['question']) || !isset($selectedQuestion['answer'])) {
                            \Log::error('Question missing required fields. Available fields: ' . json_encode(array_keys($selectedQuestion)));
                            
                            // Try to fix common field name variations
                            if (isset($selectedQuestion['text']) && !isset($selectedQuestion['question'])) {
                                $selectedQuestion['question'] = $selectedQuestion['text'];
                            }
                            if (isset($selectedQuestion['ans']) && !isset($selectedQuestion['answer'])) {
                                $selectedQuestion['answer'] = $selectedQuestion['ans'];
                            }
                            if (isset($selectedQuestion['correct_answer']) && !isset($selectedQuestion['answer'])) {
                                $selectedQuestion['answer'] = $selectedQuestion['correct_answer'];
                            }
                        }
                        
                        // Final validation
                        if (isset($selectedQuestion['question']) && isset($selectedQuestion['answer'])) {
                            \Log::info('Custom question found: ' . json_encode($selectedQuestion));
                            
                            return [
                                'question' => $selectedQuestion['question'],
                                'answer' => $selectedQuestion['answer'],
                                'category' => $category,
                                'value' => $value,
                                'original_value' => $value
                            ];
                        } else {
                            \Log::error('Invalid question structure after processing: ' . json_encode($selectedQuestion));
                        }
                    }
                } else {
                    \Log::error('Value ' . $value . ' not found in category ' . $category);
                    \Log::error('Available values: ' . json_encode(array_keys($customCategories[$category])));
                }
            } else {
                \Log::error('Category ' . $category . ' not found in custom categories');
                \Log::error('Available categories: ' . json_encode(array_keys($customCategories)));
                
                // Try case-insensitive category search as fallback
                foreach ($customCategories as $catKey => $catData) {
                    if (strtolower($catKey) === strtolower($category)) {
                        \Log::info('Found category with different case: ' . $catKey);
                        
                        // Try to find the value in this category
                        if (isset($catData[$value])) {
                            $questionData = $catData[$value];
                            \Log::info('Found question data with case-insensitive search');
                            
                            // Process the question data with the same logic as above
                            $selectedQuestion = null;
                            if (is_array($questionData)) {
                                if (isset($questionData[0]) && is_array($questionData[0])) {
                                    $selectedQuestion = $questionData[0];
                                } else if (isset($questionData['question']) && isset($questionData['answer'])) {
                                    $selectedQuestion = $questionData;
                                } else if (count($questionData) > 0) {
                                    $selectedQuestion = $questionData;
                                }
                            }
                            
                            if ($selectedQuestion && isset($selectedQuestion['question']) && isset($selectedQuestion['answer'])) {
                                return [
                                    'question' => $selectedQuestion['question'],
                                    'answer' => $selectedQuestion['answer'],
                                    'category' => $category,
                                    'value' => $value,
                                    'original_value' => $value
                                ];
                            }
                        }
                    }
                }
            }
            
            \Log::error('Custom question not found for category: ' . $category . ', value: ' . $value);
            \Log::error('Full custom categories structure: ' . json_encode($customCategories));
            return null;
        }
        
        // Original predefined questions logic
        // Adjust question value based on difficulty
        $difficultyMultipliers = [
            'easy' => 0.5,      // Half points for easy
            'normal' => 1.0,    // Normal points
            'hard' => 1.5,      // 1.5x points for hard
            'challenging' => 2.0 // Double points for challenging
        ];
        
        $multiplier = $difficultyMultipliers[$difficulty] ?? 1.0;
        $adjustedValue = round($value * $multiplier);
        
        // Different question sets for each difficulty level
        $questions = [];
        
        if ($difficulty === 'easy') {
        $questions = [
                'Animals' => [
                    1 => [['question' => 'What animal says "meow"?', 'answer' => 'Cat']],
                    2 => [['question' => 'What animal has a long neck and spots?', 'answer' => 'Giraffe']],
                    3 => [['question' => 'What animal is known as "man\'s best friend"?', 'answer' => 'Dog']],
                    4 => [['question' => 'What animal is pink and lives on a farm?', 'answer' => 'Pig']],
                    5 => [['question' => 'What animal is the king of the jungle?', 'answer' => 'Lion']]
                ],
                'Colors' => [
                    1 => [['question' => 'What color is the sky on a sunny day?', 'answer' => 'Blue']],
                    2 => [['question' => 'What color is grass?', 'answer' => 'Green']],
                    3 => [['question' => 'What color is a banana?', 'answer' => 'Yellow']],
                    4 => [['question' => 'What color is a stop sign?', 'answer' => 'Red']],
                    5 => [['question' => 'What color is snow?', 'answer' => 'White']]
                ],
                'Food' => [
                    1 => [['question' => 'What fruit is red and grows on trees?', 'answer' => 'Apple']],
                    2 => [['question' => 'What do you put on bread to make a sandwich?', 'answer' => 'Butter']],
                    3 => [['question' => 'What drink comes from cows?', 'answer' => 'Milk']],
                    4 => [['question' => 'What vegetable is orange and grows underground?', 'answer' => 'Carrot']],
                    5 => [['question' => 'What food do you eat on your birthday?', 'answer' => 'Cake']]
                ],
                'Numbers' => [
                    1 => [['question' => 'How many fingers do you have on one hand?', 'answer' => '5']],
                    2 => [['question' => 'How many days are in a week?', 'answer' => '7']],
                    3 => [['question' => 'How many legs does a dog have?', 'answer' => '4']],
                    4 => [['question' => 'How many sides does a triangle have?', 'answer' => '3']],
                    5 => [['question' => 'How many months are in a year?', 'answer' => '12']]
                ],
                'Shapes' => [
                    1 => [['question' => 'What shape is a circle?', 'answer' => 'Round']],
                    2 => [['question' => 'What shape has four equal sides?', 'answer' => 'Square']],
                    3 => [['question' => 'What shape is a stop sign?', 'answer' => 'Octagon']],
                    4 => [['question' => 'What shape is a pizza slice?', 'answer' => 'Triangle']],
                    5 => [['question' => 'What shape is a rectangle?', 'answer' => 'Oblong']]
                ]
            ];
        } elseif ($difficulty === 'normal') {
            $questions = [
                'Science' => [
                    1 => [['question' => 'What is the chemical symbol for gold?', 'answer' => 'Au']],
                    2 => [['question' => 'What planet is known as the Red Planet?', 'answer' => 'Mars']],
                    3 => [['question' => 'What is the largest organ in the human body?', 'answer' => 'Skin']],
                    4 => [['question' => 'What is the atomic number of carbon?', 'answer' => '6']],
                    5 => [['question' => 'What is the speed of light in miles per second?', 'answer' => '186,282']]
            ],
            'History' => [
                    1 => [['question' => 'In what year did World War II end?', 'answer' => '1945']],
                    2 => [['question' => 'Who was the first President of the United States?', 'answer' => 'George Washington']],
                    3 => [['question' => 'What ancient wonder was located in Alexandria?', 'answer' => 'Lighthouse of Alexandria']],
                    4 => [['question' => 'In what year did Columbus discover America?', 'answer' => '1492']],
                    5 => [['question' => 'What was the name of the ship that sank in 1912?', 'answer' => 'Titanic']]
            ],
            'Geography' => [
                    1 => [['question' => 'What is the capital of France?', 'answer' => 'Paris']],
                    2 => [['question' => 'What is the largest ocean on Earth?', 'answer' => 'Pacific Ocean']],
                    3 => [['question' => 'What is the longest river in the world?', 'answer' => 'Nile']],
                    4 => [['question' => 'What is the smallest country in the world?', 'answer' => 'Vatican City']],
                    5 => [['question' => 'What mountain range runs through South America?', 'answer' => 'Andes']]
                ],
                'Entertainment' => [
                    1 => [['question' => 'Who played Iron Man in the Marvel Cinematic Universe?', 'answer' => 'Robert Downey Jr.']],
                    2 => [['question' => 'What is the name of the fictional town where The Simpsons live?', 'answer' => 'Springfield']],
                    3 => [['question' => 'What year did the first Star Wars movie release?', 'answer' => '1977']],
                    4 => [['question' => 'Who wrote the Harry Potter series?', 'answer' => 'J.K. Rowling']],
                    5 => [['question' => 'What is the name of the fictional bar in Cheers?', 'answer' => 'Cheers']]
                ],
                'Sports' => [
                    1 => [['question' => 'What sport is known as "the beautiful game"?', 'answer' => 'Soccer/Football']],
                    2 => [['question' => 'How many players are on a basketball court at once?', 'answer' => '10']],
                    3 => [['question' => 'What is the national sport of Japan?', 'answer' => 'Sumo Wrestling']],
                    4 => [['question' => 'In what year did the first modern Olympics take place?', 'answer' => '1896']],
                    5 => [['question' => 'What is the fastest land animal?', 'answer' => 'Cheetah']]
                ]
            ];
        } elseif ($difficulty === 'hard') {
            $questions = [
                'Chemistry' => [
                    1 => [['question' => 'What is the molecular formula for glucose?', 'answer' => 'C6H12O6']],
                    2 => [['question' => 'What element has the atomic number 79?', 'answer' => 'Gold']],
                    3 => [['question' => 'What is the chemical symbol for tungsten?', 'answer' => 'W']],
                    4 => [['question' => 'What is the pH of a neutral solution?', 'answer' => '7']],
                    5 => [['question' => 'What gas do plants absorb during photosynthesis?', 'answer' => 'Carbon dioxide']]
                ],
                'World History' => [
                    1 => [['question' => 'In what year did the Berlin Wall fall?', 'answer' => '1989']],
                    2 => [['question' => 'Who was the first Emperor of Rome?', 'answer' => 'Augustus']],
                    3 => [['question' => 'What ancient city was destroyed by Mount Vesuvius?', 'answer' => 'Pompeii']],
                    4 => [['question' => 'In what year did the French Revolution begin?', 'answer' => '1789']],
                    5 => [['question' => 'What was the name of the first human in space?', 'answer' => 'Yuri Gagarin']]
                ],
                'Astronomy' => [
                    1 => [['question' => 'What is the largest planet in our solar system?', 'answer' => 'Jupiter']],
                    2 => [['question' => 'What is the closest star to Earth?', 'answer' => 'Sun']],
                    3 => [['question' => 'What galaxy do we live in?', 'answer' => 'Milky Way']],
                    4 => [['question' => 'What is the name of the first man to walk on the moon?', 'answer' => 'Neil Armstrong']],
                    5 => [['question' => 'What is the study of celestial objects called?', 'answer' => 'Astronomy']]
                ],
                'Literature' => [
                    1 => [['question' => 'Who wrote "Pride and Prejudice"?', 'answer' => 'Jane Austen']],
                    2 => [['question' => 'What is the name of the main character in "1984"?', 'answer' => 'Winston Smith']],
                    3 => [['question' => 'Who wrote "The Great Gatsby"?', 'answer' => 'F. Scott Fitzgerald']],
                    4 => [['question' => 'What is the name of the island in "Lord of the Flies"?', 'answer' => 'Piggy']],
                    5 => [['question' => 'Who wrote "To Kill a Mockingbird"?', 'answer' => 'Harper Lee']]
                ],
                'Technology' => [
                    1 => [['question' => 'What does CPU stand for?', 'answer' => 'Central Processing Unit']],
                    2 => [['question' => 'What year was the first iPhone released?', 'answer' => '2007']],
                    3 => [['question' => 'What does HTML stand for?', 'answer' => 'HyperText Markup Language']],
                    4 => [['question' => 'Who founded Microsoft?', 'answer' => 'Bill Gates']],
                    5 => [['question' => 'What does URL stand for?', 'answer' => 'Uniform Resource Locator']]
                ]
            ];
        } elseif ($difficulty === 'challenging') {
            $questions = [
                'Quantum Physics' => [
                    1 => [['question' => 'What is the name of the famous thought experiment involving a cat?', 'answer' => 'Schrödinger\'s Cat']],
                    2 => [['question' => 'What does E=mc² represent?', 'answer' => 'Mass-energy equivalence']],
                    3 => [['question' => 'What is the uncertainty principle named after?', 'answer' => 'Heisenberg']],
                    4 => [['question' => 'What is quantum entanglement also known as?', 'answer' => 'Spooky action at a distance']],
                    5 => [['question' => 'What is the name of the particle that gives mass to other particles?', 'answer' => 'Higgs boson']]
                ],
                'Ancient Civilizations' => [
                    1 => [['question' => 'What was the capital of the Aztec Empire?', 'answer' => 'Tenochtitlan']],
                    2 => [['question' => 'What ancient wonder was located in Babylon?', 'answer' => 'Hanging Gardens']],
                    3 => [['question' => 'What was the name of the first emperor of China?', 'answer' => 'Qin Shi Huang']],
                    4 => [['question' => 'What ancient city was the capital of the Byzantine Empire?', 'answer' => 'Constantinople']],
                    5 => [['question' => 'What was the name of the ancient Egyptian sun god?', 'answer' => 'Ra']]
                ],
                'Advanced Mathematics' => [
                    1 => [['question' => 'What is the value of pi to 5 decimal places?', 'answer' => '3.14159']],
                    2 => [['question' => 'What is the square root of negative one?', 'answer' => 'i']],
                    3 => [['question' => 'What is the name of the theorem that states a² + b² = c²?', 'answer' => 'Pythagorean theorem']],
                    4 => [['question' => 'What is the derivative of x²?', 'answer' => '2x']],
                    5 => [['question' => 'What is the sum of the first 100 natural numbers?', 'answer' => '5050']]
                ],
                'Classical Music' => [
                    1 => [['question' => 'Who composed "The Four Seasons"?', 'answer' => 'Antonio Vivaldi']],
                    2 => [['question' => 'What is the name of Beethoven\'s 5th Symphony?', 'answer' => 'Fate Symphony']],
                    3 => [['question' => 'Who composed "The Magic Flute"?', 'answer' => 'Wolfgang Amadeus Mozart']],
                    4 => [['question' => 'What is the name of Bach\'s most famous organ work?', 'answer' => 'Toccata and Fugue in D minor']],
                    5 => [['question' => 'Who composed "The Nutcracker"?', 'answer' => 'Pyotr Ilyich Tchaikovsky']]
                ],
                'Philosophy' => [
                    1 => [['question' => 'Who said "I think, therefore I am"?', 'answer' => 'René Descartes']],
                    2 => [['question' => 'What is the name of Plato\'s most famous work?', 'answer' => 'The Republic']],
                    3 => [['question' => 'Who wrote "Thus Spoke Zarathustra"?', 'answer' => 'Friedrich Nietzsche']],
                    4 => [['question' => 'What is the study of knowledge called?', 'answer' => 'Epistemology']],
                    5 => [['question' => 'Who founded the Academy in Athens?', 'answer' => 'Plato']]
                ]
            ];
        }

        // Get the questions for the specific category and value
        $categoryQuestions = $questions[$category][$value] ?? [];
        
        if (empty($categoryQuestions)) {
            return null;
        }
        
        // Since there's only one question per category-value, get the first (and only) question
        $selectedQuestion = $categoryQuestions[0];
        
        // Format the question for return
        $question = [
            'question' => $selectedQuestion['question'],
            'answer' => $selectedQuestion['answer'],
            'category' => $category,
            'value' => $adjustedValue,
            'original_value' => $value // Store the original value
        ];
        
        return $question;
    }

    public function getCategories(Request $request)
    {
        $difficulty = $request->get('difficulty', 'normal');
        
        $categories = [];
        
        if ($difficulty === 'easy') {
            $categories = ['Animals', 'Colors', 'Food', 'Numbers', 'Shapes'];
        } elseif ($difficulty === 'normal') {
            $categories = ['Science', 'History', 'Geography', 'Entertainment', 'Sports'];
        } elseif ($difficulty === 'hard') {
            $categories = ['Chemistry', 'World History', 'Astronomy', 'Literature', 'Technology'];
        } elseif ($difficulty === 'challenging') {
            $categories = ['Quantum Physics', 'Ancient Civilizations', 'Advanced Mathematics', 'Classical Music', 'Philosophy'];
        } else {
            // Default to normal categories
            $categories = ['Science', 'History', 'Geography', 'Entertainment', 'Sports'];
        }
        
        return response()->json([
            'categories' => $categories,
            'difficulty' => $difficulty
        ]);
    }

    public function customGameCreator()
    {
        return view('jeopardy.custom-game');
    }

    public function simpleCustomGameCreator()
    {
        return view('jeopardy.simple-custom-game');
    }

    public function simplePlayCustomGame()
    {
        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState || !isset($gameState['custom_categories'])) {
            return redirect('/jeopardy')->with('error', 'No custom game found. Please create a custom game first.');
        }
        
        return view('jeopardy.simple-play-custom');
    }

    public function debugSession()
    {
        $gameState = Session::get('jeopardy_game');
        $sessionId = Session::getId();
        
        return response()->json([
            'session_id' => $sessionId,
            'has_game_state' => !empty($gameState),
            'game_state_keys' => $gameState ? array_keys($gameState) : [],
            'custom_categories' => $gameState['custom_categories'] ?? null,
            'game_mode' => $gameState['game_mode'] ?? 'not set',
            'difficulty' => $gameState['difficulty'] ?? 'not set',
            'current_player_id' => $gameState['current_player_id'] ?? 'not set',
            'full_game_state' => $gameState
        ]);
    }

    public function playCustomGame()
    {
        $gameState = Session::get('jeopardy_game');
        
        if (!$gameState || !isset($gameState['custom_categories'])) {
            return redirect('/jeopardy')->with('error', 'No custom game found. Please create a custom game first.');
        }
        
        return view('jeopardy.play-custom');
    }

    public function lobbySelection()
    {
        return view('jeopardy.lobby');
    }

    public function createLobby(Request $request)
    {
        $request->validate([
            'host_name' => 'required|string|max:50',
            'game_settings' => 'required|array'
        ]);

        $lobby = new \App\Models\Lobby();
        $lobby->lobby_code = \App\Models\Lobby::generateLobbyCode();
        $lobby->host_name = $request->host_name;
        $lobby->game_settings = $request->game_settings;
        $lobby->players = [
            [
                'id' => '001', // Host gets ID 001
                'name' => $request->host_name,
                'joined_at' => now()->toISOString()
            ]
        ];
        $lobby->status = 'waiting';
        $lobby->save();

        // Store the host's session ID and player name for later identification
        $sessionId = Session::getId();
        Session::put('host_session_id', $sessionId);
        Session::put('lobby_created_by_session', $sessionId);
        Session::put('player_name', $request->host_name);
        Session::put('current_player_id', '001');
        Session::put('is_host', true);
        Session::save();
        
        \Log::info("Host session ID stored: {$sessionId} for lobby: {$lobby->lobby_code}");

        return response()->json([
            'success' => true,
            'lobby_code' => $lobby->lobby_code,
            'lobby_url' => url("/jeopardy/lobby/{$lobby->lobby_code}")
        ]);
    }

    public function joinLobby(Request $request)
    {
        $request->validate([
            'lobby_code' => 'required|string|size:6',
            'player_name' => 'required|string|max:50'
        ]);

        $lobbyCode = strtoupper($request->lobby_code);
        $lobby = \App\Models\Lobby::where('lobby_code', $lobbyCode)->first();

        if (!$lobby) {
            return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
        }

        if ($lobby->status !== 'waiting') {
            return response()->json(['success' => false, 'message' => 'Game has already started'], 400);
        }

        // Check if lobby is full (maximum 7 players including host)
        $existingPlayers = $lobby->players ?? [];
        if (count($existingPlayers) >= 7) {
            return response()->json(['success' => false, 'message' => 'Lobby is full. Maximum 7 players allowed.'], 400);
        }

        // Check if player name already exists
        foreach ($existingPlayers as $player) {
            if ($player['name'] === $request->player_name) {
                return response()->json(['success' => false, 'message' => 'Player name already taken'], 400);
            }
        }

        // Generate player ID (001, 002, 003, etc.)
        $playerId = $this->generatePlayerId($lobby);
        
        // Check if we can generate a player ID
        if ($playerId === null) {
            return response()->json(['success' => false, 'message' => 'Lobby is full. Maximum 7 players allowed.'], 400);
        }
        
        // Add player with ID
        $lobby->addPlayerWithId($request->player_name, $playerId);
        
        // Store player name in session for later identification
        Session::put('player_name', $request->player_name);
        Session::put('current_player_id', $playerId);
        // Ensure player is not marked as host observer
        Session::forget('is_host_observer');
        Session::save();

        return response()->json([
            'success' => true,
            'lobby_code' => $lobby->lobby_code,
            'lobby_url' => url("/jeopardy/lobby/{$lobby->lobby_code}"),
            'player_id' => $playerId
        ]);
    }

    public function lobbyRoom($code)
    {
        $lobbyCode = strtoupper($code);
        $lobby = \App\Models\Lobby::where('lobby_code', $lobbyCode)->first();

        if (!$lobby) {
            return redirect('/jeopardy/lobby')->with('error', 'Lobby not found');
        }

        return view('jeopardy.lobby-room', compact('lobby'));
    }

    public function startLobbyGame(Request $request, $code)
    {
        $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($code))->first();

        if (!$lobby) {
            return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
        }

        if ($lobby->status !== 'waiting') {
            return response()->json(['success' => false, 'message' => 'Game has already started'], 400);
        }

        // Store player names from lobby for use in game setup
        $players = $lobby->players ?? [];
        $playerNames = [];
        
        // Include all players including host since they can now participate
        for ($i = 0; $i < count($players); $i++) {
            if (isset($players[$i])) {
                $playerNames[] = $players[$i]['name'];
            }
        }

        // Store the lobby info in session for the setup page
        Session::put('lobby_game_info', [
            'lobby_code' => $lobby->lobby_code,
            'player_names' => $playerNames,
            'host_name' => $lobby->host_name,
            'difficulty' => $lobby->game_settings['difficulty'] ?? 'normal'
        ]);
        Session::save();

        $lobby->status = 'playing';
        $lobby->save();

        return response()->json(['success' => true]);
    }

    public function getLobbyStatus($code)
    {
        $lobby = \App\Models\Lobby::where('lobby_code', strtoupper($code))->first();

        if (!$lobby) {
            return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
        }

        return response()->json([
            'success' => true,
            'lobby' => [
                'status' => $lobby->status,
                'game_settings' => $lobby->game_settings,
                'players' => $lobby->players
            ]
        ]);
    }

    public function clearLobbyInfo(Request $request)
    {
        Session::forget('lobby_game_info');
        Session::save();
        
        return response()->json(['success' => true]);
    }

    public function debugTeamAssignment(Request $request)
    {
        $gameState = Session::get('jeopardy_game');
        $sessionId = Session::getId();
        $currentPlayerTeam = Session::get('current_player_team');
        $lobbyPlayers = Session::get('lobby_players', []);
        
        $debugInfo = [
            'session_id' => $sessionId,
            'current_player_team' => $currentPlayerTeam,
            'lobby_players' => $lobbyPlayers,
            'game_state_current_team' => $gameState['current_team'] ?? 'N/A',
            'game_state_teams' => []
        ];
        
        if ($gameState) {
            $teamCount = $gameState['team_count'] ?? 0;
            for ($i = 1; $i <= $teamCount; $i++) {
                $teamKey = 'team' . $i;
                if (isset($gameState[$teamKey])) {
                    $debugInfo['game_state_teams'][$teamKey] = $gameState[$teamKey];
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'debug_info' => $debugInfo
        ]);
    }

    private function generatePlayerId($lobby)
    {
        $players = $lobby->players ?? [];
        $existingIds = [];
        
        // Collect existing player IDs
        foreach ($players as $player) {
            if (isset($player['id'])) {
                $existingIds[] = (int)$player['id'];
            }
        }
        
        // Find the next available ID (limit to 7 players)
        $nextId = 1;
        while (in_array($nextId, $existingIds) && $nextId <= 7) {
            $nextId++;
        }
        
        // If we've reached the limit, return null
        if ($nextId > 7) {
            return null;
        }
        
        // Format as 3-digit string (001, 002, 003, etc.)
        return str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    private function getCurrentPlayerId()
    {
        // First, check if player ID is already assigned in session
        $playerId = Session::get('current_player_id');
        
        if ($playerId !== null) {
            return $playerId;
        }
        
        // If no ID is assigned, try to determine based on lobby
        $gameState = Session::get('jeopardy_game');
        
        if (isset($gameState['difficulty']) && $gameState['difficulty'] === 'custom') {
            // Find the lobby for this game
            $lobbies = \App\Models\Lobby::where('status', 'playing')->get();
            foreach ($lobbies as $lobby) {
                if (isset($lobby->game_state) && 
                    isset($lobby->game_state['custom_categories']) && 
                    $lobby->game_state['custom_categories'] === $gameState['custom_categories']) {
                    
                    // Get the current user's session ID to identify them
                    $sessionId = Session::getId();
                    
                    // Check if this player is the host
                    $isHost = $this->isCurrentPlayerHost($lobby);
                    
                    if ($isHost) {
                        // Host can participate - assign ID 001
                        $playerId = '001';
                        Session::put('current_player_id', $playerId);
                        Session::put('is_host', true);
                        Session::save();
                        
                        \Log::info("Host assigned to participate (ID 001) for session ID {$sessionId}");
                        return $playerId;
                    }
                    
                    // For non-host players, find their ID from the lobby players
                    $players = $lobby->players ?? [];
                    $playerName = Session::get('player_name');
                    
                    // Try to find this player's ID by matching their name
                    foreach ($players as $player) {
                        if (isset($player['name']) && $player['name'] === $playerName) {
                            $playerId = $player['id'] ?? '002'; // Default to 002 if no ID found
                            Session::put('current_player_id', $playerId);
                            Session::save();
                            
                            \Log::info("Found player ID {$playerId} for player {$playerName}");
                            return $playerId;
                        }
                    }
                    
                    // If player not found in lobby, this might be a new session
                    // Try to assign them to an available ID
                    $assignedIds = [];
                    foreach ($players as $player) {
                        if (isset($player['id'])) {
                            $assignedIds[] = (int)$player['id'];
                        }
                    }
                    
                    // Find next available ID (skip 001 as it's for host)
                    $nextId = 2;
                    while (in_array($nextId, $assignedIds)) {
                        $nextId++;
                    }
                    
                    $playerId = str_pad($nextId, 3, '0', STR_PAD_LEFT);
                    Session::put('current_player_id', $playerId);
                    Session::save();
                    
                    \Log::info("Assigned new player ID {$playerId} for session ID {$sessionId}");
                    return $playerId;
                }
            }
        }
        
        return $playerId;
    }


}
