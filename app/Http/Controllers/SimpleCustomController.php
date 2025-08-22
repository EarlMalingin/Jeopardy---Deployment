<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SimpleCustomController extends Controller
{
    public function index()
    {
        return view('jeopardy.simple-custom-game');
    }

    public function play()
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState || !isset($gameState['custom_categories'])) {
            return redirect('/jeopardy/simple-custom-game')->with('error', 'No custom game found. Please create a custom game first.');
        }
        
        return view('jeopardy.simple-play-custom');
    }

    public function startGame(Request $request)
    {
        \Log::info('Starting simple custom game with data: ' . json_encode($request->all()));
        
        $request->validate([
            'team_names' => 'required|array|min:1|max:6',
            'team_names.*' => 'nullable|string|max:50',
            'categories' => 'required|array|min:1|max:8',
            'game_timer' => 'required|integer|min:60|max:3600',
            'question_timer' => 'required|integer|min:10|max:120',
            'category_count' => 'required|integer|min:1|max:8',
            'question_count' => 'required|integer|min:3|max:60'
        ]);

        $teamNames = $request->team_names;
        $teamCount = count($teamNames);
        $categories = $request->categories;
        $gameTimer = $request->game_timer;
        $questionTimer = $request->question_timer;
        $categoryCount = $request->category_count;
        $questionCount = $request->question_count;
        
        // Clear any existing game state
        Session::forget('simple_custom_game');
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
            'question_count' => $questionCount
        ];
        
        // Handle team names
        $finalTeamNames = $teamNames;
        if ($teamCount === 1) {
            if (empty($finalTeamNames[0])) {
                $finalTeamNames[0] = "Player";
            }
        } else {
            // Use provided names or defaults
            for ($i = 0; $i < $teamCount; $i++) {
                if (empty($finalTeamNames[$i])) {
                    $finalTeamNames[$i] = "Team " . ($i + 1);
                }
            }
        }
        
        // Create teams
        for ($i = 1; $i <= $teamCount; $i++) {
            $gameState['team' . $i] = [
                'name' => $finalTeamNames[$i - 1],
                'score' => 0,
                'color' => $teamColors[$i - 1],
                'timer' => $gameTimer
            ];
        }

        Session::put('simple_custom_game', $gameState);
        Session::save();
        
        \Log::info('Simple custom game created successfully: ' . json_encode($gameState));
        
        return response()->json(['success' => true, 'game_state' => $gameState]);
    }

    public function getGameState()
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState) {
            return response()->json(['success' => false, 'error' => 'No game found']);
        }
        
        return response()->json([
            'success' => true,
            'game_state' => $gameState
        ]);
    }

    public function selectQuestion(Request $request)
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }

        $questionKey = $request->category . '_' . $request->value;
        
        // Check if question is already answered
        if (in_array($questionKey, $gameState['answered_questions'] ?? [])) {
            return response()->json(['error' => 'Question already answered'], 400);
        }
        
        // Check if this is a steal attempt (question was timed out)
        $isStealAttempt = in_array($questionKey, $gameState['timed_out_questions'] ?? []);
        
        $question = $this->getQuestion($request->category, $request->value);
        
        if (!$question) {
            return response()->json([
                'success' => false,
                'error' => 'Question not found for the selected category and value'
            ], 404);
        }
        
        $gameState['current_question'] = $question;
        $gameState['question_timer'] = $gameState['custom_question_timer'] ?? 30;
        $gameState['is_steal_attempt'] = $isStealAttempt;
        
        Session::put('simple_custom_game', $gameState);
        Session::save();
        
        return response()->json([
            'success' => true,
            'question' => $question,
            'timer' => $gameState['question_timer'],
            'is_steal_attempt' => $isStealAttempt
        ]);
    }

    public function submitAnswer(Request $request)
    {
        $gameState = Session::get('simple_custom_game');
        $maxTimer = $gameState['custom_question_timer'] ?? 30;
        
        $request->validate([
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:0|max:' . $maxTimer
        ]);

        if (!$gameState || !$gameState['current_question']) {
            return response()->json(['error' => 'No active question'], 400);
        }

        $currentTeam = 'team' . $gameState['current_team'];
        $question = $gameState['current_question'];
        $questionKey = $question['category'] . '_' . ($question['original_value'] ?? $question['value']);
        $isStealAttempt = $gameState['is_steal_attempt'] ?? false;
        $timeTaken = $request->time_taken;

        // Check if the answer is correct
        $userAnswer = strtolower(trim($request->answer));
        $correctAnswer = strtolower(trim($question['answer']));
        $isCorrect = $userAnswer === $correctAnswer;

        if ($isCorrect) {
            $oldScore = $gameState[$currentTeam]['score'];
            $questionValue = $question['original_value'] ?? $question['value'];
            $questionCount = $gameState['question_count'] ?? 5;
            
            // Calculate points based on the new scoring system for 60 questions
            $pointsEarned = $this->calculatePoints($questionValue, $questionCount);
            $gameState[$currentTeam]['score'] += $pointsEarned;
            
            // Deduct time taken from team timer
            $gameState[$currentTeam]['timer'] = max(0, $gameState[$currentTeam]['timer'] - $timeTaken);
        } else {
            $pointsEarned = 0;
            // Deduct 30 seconds for wrong answer
            $gameState[$currentTeam]['timer'] = max(0, $gameState[$currentTeam]['timer'] - 30);
        }

        // Check if current team ran out of time
        if ($gameState[$currentTeam]['timer'] <= 0) {
            $gameState['game_over'] = true;
            $gameState['winner'] = $gameState['current_team'] == 1 ? 2 : 1;
        }

        // Add to answered questions
        if (!isset($gameState['answered_questions'])) {
            $gameState['answered_questions'] = [];
        }
        $gameState['answered_questions'][] = [
            'key' => $questionKey,
            'correct' => $isCorrect
        ];
        
        // Remove from timed out questions if it was a steal attempt
        if ($isStealAttempt && isset($gameState['timed_out_questions'])) {
            $gameState['timed_out_questions'] = array_filter($gameState['timed_out_questions'], function($q) use ($questionKey) {
                return $q !== $questionKey;
            });
        }

        // DO NOT advance turn here - let advanceToNextTeam() handle it
        // This prevents double advancement when the frontend calls advanceToNextTeam()
        
        \Log::info("=== ANSWER SUBMITTED ===");
        \Log::info("Team: {$gameState['current_team']} ({$gameState[$currentTeam]['name']})");
        \Log::info("Answer correct: " . ($isCorrect ? 'YES' : 'NO'));
        \Log::info("Turn advancement will be handled by advanceToNextTeam() if answer was wrong");

        // Clear current question
        $gameState['current_question'] = null;
        $gameState['is_steal_attempt'] = false;

        Session::put('simple_custom_game', $gameState);
        Session::save();

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_answer' => $question['answer'],
            'is_steal_attempt' => $isStealAttempt,
            'points_earned' => $pointsEarned,
            'game_state' => $gameState,
            'question' => $question
        ]);
    }

    public function updateTimer(Request $request)
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }

        $request->validate([
            'time_remaining' => 'required|integer|min:0',
            'team_timer' => 'required|integer|min:0'
        ]);

        $timeRemaining = $request->time_remaining;
        $teamTimer = $request->team_timer;
        $currentTeam = 'team' . $gameState['current_team'];

        // Update team timer
        $gameState[$currentTeam]['timer'] = $teamTimer;

        // If time ran out, switch to next team
        if ($timeRemaining <= 0) {
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
            
            \Log::info("Turn advancement: Team {$oldTeam} -> Team {$gameState['current_team']} (Total teams: {$teamCount})");
            \Log::info("Expected next team: {$expectedNextTeam}");
            \Log::info("Actual next team: {$gameState['current_team']}");
            
            // Add current question to timed out questions for steal attempts
            if ($gameState['current_question']) {
                $questionKey = $gameState['current_question']['category'] . '_' . ($gameState['current_question']['original_value'] ?? $gameState['current_question']['value']);
                if (!isset($gameState['timed_out_questions'])) {
                    $gameState['timed_out_questions'] = [];
                }
                $gameState['timed_out_questions'][] = $questionKey;
            }
            
            $gameState['current_question'] = null;
            $gameState['is_steal_attempt'] = false;
        }

        Session::put('simple_custom_game', $gameState);
        Session::save();

        return response()->json([
            'success' => true,
            'game_state' => $gameState
        ]);
    }

    public function resetGame()
    {
        Session::forget('simple_custom_game');
        Session::save();
        
        return response()->json(['success' => true]);
    }

    public function testTurnAdvancement()
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }
        
        $oldTeam = $gameState['current_team'];
        $teamCount = $gameState['team_count'];
        
        \Log::info("=== TURN ADVANCEMENT TEST ===");
        \Log::info("Current team: {$oldTeam}");
        \Log::info("Total teams: {$teamCount}");
        
        // Test the turn advancement logic
        $newTeam = $oldTeam == $teamCount ? 1 : $oldTeam + 1;
        
        \Log::info("Old team: {$oldTeam} -> New team: {$newTeam}");
        
        // Verify team count is correct
        $actualTeamCount = 0;
        for ($i = 1; $i <= 10; $i++) {
            if (isset($gameState["team{$i}"])) {
                $actualTeamCount = $i;
            } else {
                break;
            }
        }
        
        \Log::info("Declared team count: {$teamCount}, Actual team count: {$actualTeamCount}");
        
        return response()->json([
            'success' => true,
            'old_team' => $oldTeam,
            'new_team' => $newTeam,
            'total_teams' => $teamCount,
            'actual_team_count' => $actualTeamCount,
            'expected_sequence' => []
        ]);
    }

    public function testTurnSequence()
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }
        
        $teamCount = $gameState['team_count'];
        
        \Log::info("=== TURN SEQUENCE TEST ===");
        \Log::info("Total teams: {$teamCount}");
        
        // Test the complete turn sequence
        $sequence = [];
        for ($i = 1; $i <= $teamCount; $i++) {
            $nextTeam = $i == $teamCount ? 1 : $i + 1;
            $teamName = $gameState["team{$i}"]['name'];
            $nextTeamName = $gameState["team{$nextTeam}"]['name'];
            $sequence[] = "Team {$i} ({$teamName}) -> Team {$nextTeam} ({$nextTeamName})";
        }
        
        \Log::info("Expected turn sequence:");
        foreach ($sequence as $step) {
            \Log::info("  {$step}");
        }
        
        return response()->json([
            'success' => true,
            'total_teams' => $teamCount,
            'current_team' => $gameState['current_team'],
            'current_team_name' => $gameState["team{$gameState['current_team']}"]['name'],
            'sequence' => $sequence
        ]);
    }

    public function fixGameState()
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState) {
            return response()->json(['error' => 'No active game'], 400);
        }
        
        // Count actual teams
        $actualTeamCount = 0;
        for ($i = 1; $i <= 10; $i++) {
            if (isset($gameState["team{$i}"])) {
                $actualTeamCount = $i;
            } else {
                break;
            }
        }
        
        // Fix team count if it's wrong
        if ($gameState['team_count'] != $actualTeamCount) {
            $gameState['team_count'] = $actualTeamCount;
            \Log::info("Fixed team count from {$gameState['team_count']} to {$actualTeamCount}");
        }
        
        // Ensure current team is valid
        if ($gameState['current_team'] > $actualTeamCount || $gameState['current_team'] < 1) {
            $gameState['current_team'] = 1;
            \Log::info("Fixed current team to 1");
        }
        
        Session::put('simple_custom_game', $gameState);
        Session::save();
        
        return response()->json([
            'success' => true,
            'fixed_team_count' => $actualTeamCount,
            'current_team' => $gameState['current_team']
        ]);
    }

    private function getQuestion($category, $value)
    {
        $gameState = Session::get('simple_custom_game');
        
        if (!$gameState || !isset($gameState['custom_categories'][$category])) {
            return null;
        }
        
        $categoryData = $gameState['custom_categories'][$category];
        
        if (!isset($categoryData[$value])) {
            return null;
        }
        
        $questionData = $categoryData[$value];
        
        // Handle both array and direct object formats
        if (is_array($questionData) && isset($questionData[0])) {
            $question = $questionData[0];
        } else {
            $question = $questionData;
        }
        
        return [
            'question' => $question['question'],
            'answer' => $question['answer'],
            'category' => $category,
            'value' => $value,
            'original_value' => $value
        ];
    }

    /**
     * Calculate points based on question value and total question count
     * Special scoring system for 50 questions:
     * - 1-10 points = 1 point
     * - 11-20 points = 2 points  
     * - 21-30 points = 3 points
     * - 31-40 points = 4 points
     * - 41-50 points = 5 points
     * Special scoring system for 60 questions:
     * - 1-10 points = 1 point
     * - 11-20 points = 2 points  
     * - 21-40 points = 3 points
     * - 41-50 points = 4 points
     * - 51-60 points = 5 points
     * For other question counts, use the original value as points
     */
    private function calculatePoints($questionValue, $questionCount)
    {
        // Special scoring system for 50 questions
        if ($questionCount == 50) {
            if ($questionValue >= 1 && $questionValue <= 10) {
                return 1;
            } elseif ($questionValue >= 11 && $questionValue <= 20) {
                return 2;
            } elseif ($questionValue >= 21 && $questionValue <= 30) {
                return 3;
            } elseif ($questionValue >= 31 && $questionValue <= 40) {
                return 4;
            } elseif ($questionValue >= 41 && $questionValue <= 50) {
                return 5;
            }
        }
        
        // Special scoring system for 60 questions
        if ($questionCount == 60) {
            if ($questionValue >= 1 && $questionValue <= 10) {
                return 1;
            } elseif ($questionValue >= 11 && $questionValue <= 20) {
                return 2;
            } elseif ($questionValue >= 21 && $questionValue <= 40) {
                return 3;
            } elseif ($questionValue >= 41 && $questionValue <= 50) {
                return 4;
            } elseif ($questionValue >= 51 && $questionValue <= 60) {
                return 5;
            }
        }
        
        // For all other question counts, use the original value as points
        return $questionValue;
    }
}
