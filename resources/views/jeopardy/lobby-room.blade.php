<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Room - Jeopardy</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .jeopardy-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .floating-animation {
            animation: float 4s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .glow-text {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
        
        .particle {
            position: fixed;
            width: 3px;
            height: 3px;
            background: #3b82f6;
            border-radius: 50%;
            opacity: 0.6;
            animation: particle-float 8s infinite linear;
            pointer-events: none;
            z-index: 1;
        }
        
        @keyframes particle-float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-100px) rotate(180deg); opacity: 0; }
        }
        
        .player-card {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }
        
        .player-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .host-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .copy-button {
            transition: all 0.3s ease;
        }
        
        .copy-button:hover {
            transform: scale(1.05);
        }
        
        .copy-button.copied {
            background: #10b981 !important;
        }
        
        /* Mobile-specific improvements for lobby room */
        @media (max-width: 768px) {
            /* Prevent horizontal scroll */
            body {
                overflow-x: hidden;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Improve button touch targets */
            button {
                min-height: 44px !important;
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation;
            }
            
            /* Improve input fields */
            input {
                font-size: 16px !important; /* Prevent zoom on iOS */
                min-height: 44px !important;
            }
            
            /* Improve modal responsiveness */
            .modal-content {
                max-width: 95vw !important;
                margin: 0.5rem !important;
                padding: 1rem !important;
            }
            
            /* Better spacing for mobile */
            .space-y-4 > * + * {
                margin-top: 1rem !important;
            }
            
            /* Improve text readability */
            .text-sm {
                font-size: 0.875rem !important;
            }
            
            .text-lg {
                font-size: 1.125rem !important;
            }
        }
        
        @media (max-width: 480px) {
            /* Even smaller screens */
            .text-2xl {
                font-size: 1.5rem !important;
            }
            
            .text-xl {
                font-size: 1.25rem !important;
            }
            
            button {
                min-height: 48px !important;
                font-size: 1rem !important;
            }
        }

        /* Beautiful Enhanced Notification System */
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            pointer-events: none;
        }

        .notification {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.95) 0%, rgba(55, 65, 81, 0.95) 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 16px;
            min-width: 360px;
            max-width: 450px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            transform: translateX(100%) scale(0.95);
            opacity: 0;
            pointer-events: auto;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .notification::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899, #f59e0b);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        .notification::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 50%, rgba(255, 255, 255, 0.02) 100%);
            pointer-events: none;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .notification.show {
            animation: slideInRightEnhanced 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        .notification.hide {
            animation: slideOutRightEnhanced 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        @keyframes slideInRightEnhanced {
            0% {
                transform: translateX(100%) scale(0.8);
                opacity: 0;
            }
            50% {
                transform: translateX(-10px) scale(1.02);
                opacity: 0.8;
            }
            100% {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes slideOutRightEnhanced {
            0% {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translateX(100%) scale(0.8);
                opacity: 0;
            }
        }

        .notification.success {
            border-left: 5px solid #10b981;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(31, 41, 55, 0.95) 100%);
        }

        .notification.success::before {
            background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7, #a7f3d0);
        }

        .notification.error {
            border-left: 5px solid #ef4444;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(31, 41, 55, 0.95) 100%);
        }

        .notification.error::before {
            background: linear-gradient(90deg, #ef4444, #f87171, #fca5a5, #fecaca);
        }

        .notification.warning {
            border-left: 5px solid #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(31, 41, 55, 0.95) 100%);
        }

        .notification.warning::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24, #fcd34d, #fde68a);
        }

        .notification.info {
            border-left: 5px solid #3b82f6;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(31, 41, 55, 0.95) 100%);
        }

        .notification.info::before {
            background: linear-gradient(90deg, #3b82f6, #60a5fa, #93c5fd, #bfdbfe);
        }

        .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }

        .notification-title {
            font-weight: 700;
            font-size: 18px;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .notification-close {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #9ca3af;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .notification-close:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .notification-message {
            color: #e5e7eb;
            font-size: 15px;
            line-height: 1.6;
            position: relative;
            z-index: 2;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .notification-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0 0 20px 20px;
            overflow: hidden;
            z-index: 3;
        }

        .notification-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
            background-size: 200% 100%;
            animation: progressShrink 5s linear forwards, progressShimmer 2s ease-in-out infinite;
        }

        @keyframes progressShrink {
            from { width: 100%; }
            to { width: 0%; }
        }

        @keyframes progressShimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Enhanced pulse animation for important notifications */
        .notification.pulse {
            animation: enhancedPulse 3s ease-in-out infinite;
        }

        @keyframes enhancedPulse {
            0%, 100% {
                box-shadow: 
                    0 25px 50px rgba(0, 0, 0, 0.4),
                    0 0 0 1px rgba(255, 255, 255, 0.05);
                transform: scale(1);
            }
            50% {
                box-shadow: 
                    0 25px 50px rgba(0, 0, 0, 0.4),
                    0 0 0 1px rgba(255, 255, 255, 0.05),
                    0 0 0 6px rgba(59, 130, 246, 0.2),
                    0 0 20px rgba(59, 130, 246, 0.3);
                transform: scale(1.02);
            }
        }

        /* Floating animation for notifications */
        .notification.float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        /* Bounce animation for success notifications */
        .notification.bounce {
            animation: bounce 0.8s ease-out;
        }

        @keyframes bounce {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Notification Container -->
    <div id="notificationContainer" class="notification-container"></div>

    <!-- Background Particles -->
    <div id="particles" class="absolute inset-0 pointer-events-none"></div>
    
    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col p-4 py-8">
        <div class="max-w-6xl w-full mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <button onclick="window.location.href='/jeopardy/lobby'" class="flex items-center text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Lobby
                </button>
                
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
                        🎮 Lobby Room
                    </h1>
                    <p class="text-gray-400 text-sm">Waiting for players...</p>
                </div>
                
                <div class="w-20"></div> <!-- Spacer for centering -->
            </div>

            <!-- Lobby Info -->
            <div class="bg-gray-800 rounded-2xl p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-white mb-2">Lobby Code</h3>
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-2xl font-mono font-bold text-blue-400">{{ $lobby->lobby_code }}</span>
                            <button onclick="copyLobbyCode()" id="copyBtn" class="copy-button bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm">
                                Copy
                            </button>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-white mb-2">Players</h3>
                        <p class="text-2xl font-bold text-green-400">{{ count($lobby->players) }}</p>
                        <p class="text-xs text-gray-400 mt-1">Host can participate</p>
                    </div>
                    
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-white mb-2">Status</h3>
                        <span class="px-3 py-1 rounded-full text-sm font-bold 
                            @if($lobby->status === 'waiting') bg-yellow-600 text-yellow-100
                            @elseif($lobby->status === 'playing') bg-green-600 text-green-100
                            @else bg-gray-600 text-gray-100 @endif">
                            {{ ucfirst($lobby->status) }}
                        </span>
                    </div>
                </div>
                
                <!-- Share Link -->
                <div class="mt-6 text-center">
                    <h3 class="text-lg font-bold text-white mb-2">Share this link with others:</h3>
                    <div class="flex items-center justify-center space-x-2">
                        <input type="text" value="{{ url("/jeopardy/lobby/{$lobby->lobby_code}") }}" 
                               id="shareLink" readonly
                               class="flex-1 max-w-md px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                        <button onclick="copyShareLink()" class="copy-button bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>

            <!-- Players List -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 text-center">Players in Lobby</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($lobby->players as $player)
                    <div class="player-card rounded-xl p-4 text-center">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1">{{ $player['name'] }}</h3>
                        @if($player['name'] === $lobby->host_name)
                        <span class="host-badge text-white text-xs px-2 py-1 rounded-full">Host</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Game Rules Section (for custom games) -->
            @if(($lobby->game_settings['game_type'] ?? 'standard') === 'custom' && ($lobby->game_settings['game_created'] ?? false))
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 text-center">🎯 Custom Game Rules</h2>
                <div class="bg-gray-800 rounded-2xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Game Settings -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-blue-400 mb-2">Game Settings</h3>
                            <div class="space-y-2 text-sm text-gray-300">
                                <div>Categories: <span class="text-white font-bold">{{ $lobby->game_settings['category_count'] ?? 'N/A' }}</span></div>
                                <div>Questions per Category: <span class="text-white font-bold">{{ $lobby->game_settings['question_count'] ?? 'N/A' }}</span></div>
                                <div>Game Timer: <span class="text-white font-bold">{{ floor(($lobby->game_settings['game_timer'] ?? 300) / 60) }} minutes</span></div>
                                <div>Question Timer: <span class="text-white font-bold">{{ $lobby->game_settings['question_timer'] ?? 'N/A' }} seconds</span></div>
                            </div>
                        </div>

                        <!-- Teams -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-green-400 mb-2">Teams</h3>
                            <div class="space-y-1 text-sm text-gray-300">
                                @if(isset($lobby->game_state) && is_array($lobby->game_state))
                                    @php
                                        $teamCount = $lobby->game_state['team_count'] ?? 0;
                                        $hasTeams = false;
                                    @endphp
                                    
                                    @for($i = 1; $i <= $teamCount; $i++)
                                        @php
                                            $teamKey = "team{$i}";
                                            if (isset($lobby->game_state[$teamKey]) && isset($lobby->game_state[$teamKey]['name'])) {
                                                $hasTeams = true;
                                            }
                                        @endphp
                                        
                                        @if(isset($lobby->game_state[$teamKey]) && isset($lobby->game_state[$teamKey]['name']))
                                            <div class="text-white font-bold">{{ $lobby->game_state[$teamKey]['name'] }}</div>
                                        @endif
                                    @endfor
                                    
                                    @if(!$hasTeams)
                                        <div class="text-gray-400">Will be set when players join</div>
                                    @endif
                                @else
                                    <div class="text-gray-400">Will be set when players join</div>
                                @endif
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-purple-400 mb-2">Categories</h3>
                            <div class="space-y-1 text-sm text-gray-300">
                                @if(isset($lobby->game_settings['categories']))
                                    @foreach($lobby->game_settings['categories'] as $category)
                                        <div class="text-white font-bold">{{ $category }}</div>
                                    @endforeach
                                @else
                                    <div class="text-gray-500">No categories set</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Game Status -->
                    <div class="mt-6 text-center">
                        <div class="inline-flex items-center px-4 py-2 bg-green-600 bg-opacity-20 border border-green-500 rounded-lg">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-green-400 font-bold">Custom Game Ready!</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-2">The host has created the custom game. Questions and answers are hidden until the game starts.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Game Rules Section (for single-player games) -->
            @if(($lobby->game_settings['game_type'] ?? 'standard') === 'custom_singleplayer')
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 text-center">🎯 Single-Player Game Rules</h2>
                <div class="bg-gray-800 rounded-2xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Game Settings -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-blue-400 mb-2">Game Settings</h3>
                            <div class="space-y-2 text-sm text-gray-300">
                                <div>Categories: <span class="text-white font-bold">Will be set</span></div>
                                <div>Questions per Category: <span class="text-white font-bold">Will be set</span></div>
                                <div>Game Timer: <span class="text-white font-bold">Will be set</span></div>
                                <div>Question Timer: <span class="text-white font-bold">Will be set</span></div>
                                <div>Mode: <span class="text-white font-bold">Single Player</span></div>
                            </div>
                        </div>

                        <!-- Teams -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-green-400 mb-2">Player</h3>
                            <div class="space-y-1 text-sm text-gray-300">
                                <div class="text-white font-bold">Single Player Mode</div>
                                <div class="text-gray-400">You'll play against yourself</div>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-purple-400 mb-2">Categories</h3>
                            <div class="space-y-1 text-sm text-gray-300">
                                <div class="text-gray-400">Will be set when game is created</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Game Status -->
                    <div class="mt-6 text-center">
                        <div class="inline-flex items-center px-4 py-2 bg-green-600 bg-opacity-20 border border-green-500 rounded-lg">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-green-400 font-bold">Single-Player Game Ready!</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-2">Single-player custom game. You'll create your own categories and questions.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Game Rules Section (for standard games) -->
            @if(($lobby->game_settings['game_type'] ?? 'standard') === 'standard')
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-6 text-center">🎯 Standard Game Rules</h2>
                <div class="bg-gray-800 rounded-2xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Game Settings -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-blue-400 mb-2">Game Settings</h3>
                            <div class="space-y-2 text-sm text-gray-300">
                                <div>Categories: <span class="text-white font-bold">5</span></div>
                                <div>Questions per Category: <span class="text-white font-bold">5</span></div>
                                <div>Game Timer: <span class="text-white font-bold">5 minutes</span></div>
                                <div>Question Timer: <span class="text-white font-bold">30 seconds</span></div>
                                <div>Difficulty: <span class="text-white font-bold">{{ ucfirst($lobby->game_settings['difficulty'] ?? 'Normal') }}</span></div>
                            </div>
                        </div>

                        <!-- Teams -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-green-400 mb-2">Teams</h3>
                            <div class="space-y-1 text-sm text-gray-300">
                                @if(isset($lobby->game_state) && is_array($lobby->game_state))
                                    @php
                                        $teamCount = $lobby->game_state['team_count'] ?? 0;
                                        $hasTeams = false;
                                    @endphp
                                    
                                    @for($i = 1; $i <= $teamCount; $i++)
                                        @php
                                            $teamKey = "team{$i}";
                                            if (isset($lobby->game_state[$teamKey]) && isset($lobby->game_state[$teamKey]['name'])) {
                                                $hasTeams = true;
                                            }
                                        @endphp
                                        
                                        @if(isset($lobby->game_state[$teamKey]) && isset($lobby->game_state[$teamKey]['name']))
                                            <div class="text-white font-bold">{{ $lobby->game_state[$teamKey]['name'] }}</div>
                                        @endif
                                    @endfor
                                    
                                    @if(!$hasTeams)
                                        <div class="text-gray-400">Will be set when players join</div>
                                    @endif
                                @else
                                    <div class="text-gray-400">Will be set when players join</div>
                                @endif
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-purple-400 mb-2">Categories</h3>
                            <div class="space-y-1 text-sm text-gray-300">
                                <div class="text-white font-bold">Science</div>
                                <div class="text-white font-bold">History</div>
                                <div class="text-white font-bold">Geography</div>
                                <div class="text-white font-bold">Entertainment</div>
                                <div class="text-white font-bold">Sports</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Game Status -->
                    <div class="mt-6 text-center">
                        <div class="inline-flex items-center px-4 py-2 bg-green-600 bg-opacity-20 border border-green-500 rounded-lg">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-green-400 font-bold">Standard Game Ready!</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-2">Standard Jeopardy game with predefined categories and questions. Host can participate.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Start Game Button (only for host) -->
            @if($lobby->status === 'waiting')
            <div class="text-center">
                <button onclick="startGame()" 
                        class="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105">
                    @if(($lobby->game_settings['game_type'] ?? 'standard') === 'custom_singleplayer')
                        @if($lobby->game_settings['game_created'] ?? false)
                            🚀 Start Single-Player Game
                        @else
                            🎯 Create Single-Player Game
                        @endif
                    @elseif(($lobby->game_settings['game_type'] ?? 'standard') === 'custom')
                        @if($lobby->game_settings['game_created'] ?? false)
                            🚀 Start Custom Game
                        @else
                            🎯 Create Custom Game
                        @endif
                    @else
                        🚀 Start Game
                    @endif
                </button>
                <p class="text-gray-400 text-sm mt-2">
                    @if(($lobby->game_settings['game_type'] ?? 'standard') === 'custom_singleplayer')
                        @if($lobby->game_settings['game_created'] ?? false)
                            Only the host can start the single-player game
                        @else
                            Only the host can create the single-player game
                        @endif
                    @elseif(($lobby->game_settings['game_type'] ?? 'standard') === 'custom')
                        @if($lobby->game_settings['game_created'] ?? false)
                            Only the host can start the custom game
                        @else
                            Only the host can create the custom game
                        @endif
                    @else
                        Only the host can start the game
                    @endif
                </p>
            </div>
            @endif

            <!-- Game Started Message -->
            @if($lobby->status === 'playing')
            <div class="text-center">
                <div class="bg-green-600 text-white font-bold py-4 px-8 rounded-xl text-lg">
                    🎯 Game Started!
                </div>
                <p class="text-gray-400 text-sm mt-2">Redirecting to game...</p>
                
            <!-- Manual Redirect Button (appears after 10 seconds if auto-redirect fails) -->
            <div id="manualRedirectSection" class="mt-4 hidden">
                <p class="text-yellow-400 text-sm mb-2">Automatic redirect didn't work? Click below:</p>
                <button onclick="manualRedirectToGame()" 
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg text-sm transition-all duration-300 transform hover:scale-105">
                    🎮 Join Game Manually
                </button>
            </div>
            
            <!-- Mobile-specific immediate manual redirect button -->
            <div id="mobileManualRedirectSection" class="mt-4 hidden">
                <p class="text-red-400 text-sm mb-2 font-bold">📱 Mobile Device Detected - Click to Join Game:</p>
                <button onclick="manualRedirectToGame()" 
                        class="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-all duration-300 transform hover:scale-105 w-full">
                    🎮 JOIN GAME NOW
                </button>
            </div>
            
            <!-- Mobile-specific immediate manual redirect button -->
            <div id="mobileManualRedirectSection" class="mt-4 hidden">
                <p class="text-red-400 text-sm mb-2 font-bold">📱 Mobile Device Detected - Click to Join Game:</p>
                <button onclick="manualRedirectToGame()" 
                        class="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition-all duration-300 transform hover:scale-105 w-full">
                    🎮 JOIN GAME NOW
                </button>
            </div>
                
                <!-- Reset Game Button (only for host) -->
                <div class="mt-4">
                    <button onclick="resetGame()" 
                            class="bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-lg text-sm transition-all duration-300 transform hover:scale-105">
                        🔄 Reset Game
                    </button>
                    <p class="text-gray-400 text-xs mt-1">Only the host can reset the game</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Beautiful Notification System
        class NotificationSystem {
            constructor() {
                this.container = document.getElementById('notificationContainer');
                this.notifications = [];
            }

            show(options) {
                const {
                    type = 'info',
                    title = '',
                    message = '',
                    duration = 5000,
                    icon = this.getIcon(type),
                    autoClose = true,
                    animation = 'default'
                } = options;

                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                
                // Add special animation classes based on type
                if (type === 'success') {
                    notification.classList.add('bounce');
                } else if (type === 'error') {
                    notification.classList.add('pulse');
                } else if (type === 'warning') {
                    notification.classList.add('float');
                }
                
                notification.innerHTML = `
                    <div class="notification-header">
                        <div class="notification-title">
                            <div class="notification-icon">${icon}</div>
                            ${title}
                        </div>
                        <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="notification-message">${message}</div>
                    ${autoClose ? '<div class="notification-progress"><div class="notification-progress-bar"></div></div>' : ''}
                `;

                this.container.appendChild(notification);

                // Trigger animation
                setTimeout(() => {
                    notification.classList.add('show');
                }, 10);

                // Auto remove
                if (autoClose && duration > 0) {
                    setTimeout(() => {
                        this.hide(notification);
                    }, duration);
                }

                return notification;
            }

            hide(notification) {
                notification.classList.add('hide');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 500);
            }

            getIcon(type) {
                const icons = {
                    success: '🎉',
                    error: '💥',
                    warning: '⚡',
                    info: '💡'
                };
                return icons[type] || icons.info;
            }

            success(title, message, duration = 5000) {
                return this.show({ type: 'success', title, message, duration });
            }

            error(title, message, duration = 7000) {
                return this.show({ type: 'error', title, message, duration });
            }

            warning(title, message, duration = 6000) {
                return this.show({ type: 'warning', title, message, duration });
            }

            info(title, message, duration = 5000) {
                return this.show({ type: 'info', title, message, duration });
            }
        }

        // Initialize notification system
        const notifications = new NotificationSystem();

        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            particlesContainer.innerHTML = '';
            
            for (let i = 0; i < 25; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + 'vw';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particle.style.animationDuration = (Math.random() * 4 + 6) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        function copyLobbyCode() {
            const code = '{{ $lobby->lobby_code }}';
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyBtn');
                btn.textContent = 'Copied!';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = 'Copy';
                    btn.classList.remove('copied');
                }, 2000);

                notifications.success(
                    'Lobby Code Copied!',
                    `The lobby code "${code}" has been copied to your clipboard.`,
                    3000
                );
            }).catch(() => {
                notifications.error(
                    'Copy Failed',
                    'Unable to copy to clipboard. Please copy manually.',
                    4000
                );
            });
        }

        function copyShareLink() {
            const link = document.getElementById('shareLink').value;
            navigator.clipboard.writeText(link).then(() => {
                notifications.success(
                    'Link Copied!',
                    'The lobby link has been copied to your clipboard.',
                    3000
                );
            }).catch(() => {
                notifications.error(
                    'Copy Failed',
                    'Unable to copy to clipboard. Please copy manually.',
                    4000
                );
            });
        }

        function manualRedirectToGame() {
            const gameType = '{{ $lobby->game_settings["game_type"] ?? "standard" }}';
            const lobbyCode = '{{ $lobby->lobby_code }}';
            const isMobile = window.innerWidth <= 768;
            
            console.log('MANUAL redirect triggered:', { gameType, lobbyCode, isMobile });
            
            // ENHANCED URL generation with mobile-specific parameters
            let redirectUrl;
            if (gameType === 'custom') {
                // Add mobile-specific parameters to help with game state loading
                const mobileParams = isMobile ? '&mobile=1&t=' + Date.now() : '';
                redirectUrl = `/jeopardy/play-custom?lobby=${lobbyCode}${mobileParams}`;
            } else {
                redirectUrl = '/jeopardy/setup';
            }
            
            console.log('Generated manual redirect URL:', redirectUrl);
            
            if (isMobile) {
                // MOBILE: Try multiple redirect methods for manual redirect
                console.log('MOBILE: Manual redirect with multiple methods');
                
                // Method 1: Standard redirect
                try {
                    window.location.href = redirectUrl;
                } catch (error) {
                    console.error('MOBILE: Manual redirect method 1 failed:', error);
                    
                    // Method 2: Replace
                    try {
                        window.location.replace(redirectUrl);
                    } catch (error2) {
                        console.error('MOBILE: Manual redirect method 2 failed:', error2);
                        
                        // Method 3: Assign
                        try {
                            window.location.assign(redirectUrl);
                        } catch (error3) {
                            console.error('MOBILE: Manual redirect method 3 failed:', error3);
                            
                            // Method 4: Open in same window
                            try {
                                window.open(redirectUrl, '_self');
                            } catch (error4) {
                                console.error('MOBILE: Manual redirect method 4 failed:', error4);
                                
                                // Method 5: Create and click link
                                try {
                                    const link = document.createElement('a');
                                    link.href = redirectUrl;
                                    link.style.display = 'none';
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);
                                } catch (error5) {
                                    console.error('MOBILE: All manual redirect methods failed!');
                                    alert('Redirect failed. Please copy this URL and paste it in your browser: ' + redirectUrl);
                                }
                            }
                        }
                    }
                }
            } else {
                // DESKTOP: Standard redirect
                window.location.href = redirectUrl;
            }
        }
        
        function resetGame() {
            if (confirm('Are you sure you want to reset the game? This will clear all progress and return to the lobby.')) {
                fetch('/jeopardy/reset-lobby-game', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        lobby_code: '{{ $lobby->lobby_code }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        notifications.success(
                            'Game Reset!',
                            'The game has been reset. Refreshing the lobby...',
                            3000
                        );
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        notifications.error(
                            'Error Resetting Game',
                            data.message || 'Unknown error occurred',
                            5000
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    notifications.error(
                        'Connection Error',
                        'Unable to reset the game. Please try again.',
                        5000
                    );
                });
            }
        }

        function startGame() {
            const gameType = '{{ $lobby->game_settings["game_type"] ?? "standard" }}';
            const gameCreated = '{{ $lobby->game_settings["game_created"] ?? false }}';
            
            console.log('Starting game with:', { gameType, gameCreated });
            
            if (gameType === 'custom' || gameType === 'custom_singleplayer') {
                if (gameCreated === '1' || gameCreated === 'true') {
                    // Custom game is already created, start it
                    console.log('Starting custom game...');
                    // Store the custom game data in session and redirect to play
                    fetch('/jeopardy/start-custom-game-from-lobby', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                lobby_code: '{{ $lobby->lobby_code }}'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Response from start-custom-game-from-lobby:', data);
                            if (data.success) {
                                const gameTypeText = gameType === 'custom_singleplayer' ? 'Single-Player Game' : 'Custom Game';
                                notifications.success(
                                    gameTypeText + ' Started!',
                                    'All players will be redirected to the game automatically...',
                                    3000
                                );
                                setTimeout(() => {
                                    redirectToGame('custom', '{{ $lobby->lobby_code }}');
                                }, 1500);
                            } else {
                                notifications.error(
                                    'Error Starting Game',
                                    data.message || 'Unknown error occurred',
                                    5000
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            notifications.error(
                                'Connection Error',
                                'Unable to start the game. Please try again.',
                                5000
                            );
                        });
                } else {
                    // Custom game not created yet, redirect to custom game creator
                    console.log('Custom game not created, redirecting to creator...');
                    const gameTypeText = gameType === 'custom_singleplayer' ? 'Single-Player Game Setup' : 'Custom Game Setup';
                    const redirectText = gameType === 'custom_singleplayer' ? 'Redirecting to Single-Player Game Creator...' : 'Redirecting to Custom Game Creator...';
                    notifications.info(
                        gameTypeText,
                        'You need to create the custom game first. ' + redirectText,
                        3000
                    );
                    
                    setTimeout(() => {
                        const url = gameType === 'custom_singleplayer' 
                            ? '/jeopardy/custom-game?lobby={{ $lobby->lobby_code }}&mode=singleplayer'
                            : '/jeopardy/custom-game?lobby={{ $lobby->lobby_code }}';
                        window.location.href = url;
                    }, 1500);
                }
            } else {
                // For standard games, start immediately
                console.log('Starting standard game...');
                notifications.info(
                    'Starting Game',
                    'Are you sure you want to start the game? All players will be redirected.',
                    3000
                );
                
                setTimeout(() => {
                    fetch('/jeopardy/lobby/{{ $lobby->lobby_code }}/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            notifications.success(
                                'Game Started!',
                                'Redirecting all players to the game...',
                                3000
                            );
                            setTimeout(() => {
                                redirectToGame('standard', '{{ $lobby->lobby_code }}');
                            }, 1500);
                        } else {
                            notifications.error(
                                'Error Starting Game',
                                data.message || 'Unknown error occurred',
                                5000
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        notifications.error(
                            'Connection Error',
                            'Unable to start the game. Please try again.',
                            5000
                        );
                    });
                }, 1000);
            }
        }

        // Enhanced mobile-friendly redirect function with multiple fallback methods
        function redirectToGame(gameType, lobbyCode) {
            console.log('Redirecting to game:', { gameType, lobbyCode, isMobile: window.innerWidth <= 768 });
            
            // Add mobile-specific delay and user feedback
            if (window.innerWidth <= 768) {
                console.log('Mobile device detected, using enhanced mobile redirect logic');
                
                notifications.info(
                    'Game Starting!',
                    'Redirecting you to the game...',
                    2000
                );
                
                // Multiple redirect attempts for mobile
                const redirectAttempts = [
                    () => {
                        const redirectUrl = gameType === 'custom' 
                            ? `/jeopardy/play-custom?lobby=${lobbyCode}`
                            : '/jeopardy/setup';
                        console.log('Mobile redirect attempt 1:', redirectUrl);
                        window.location.href = redirectUrl;
                    },
                    () => {
                        const redirectUrl = gameType === 'custom' 
                            ? `/jeopardy/play-custom?lobby=${lobbyCode}`
                            : '/jeopardy/setup';
                        console.log('Mobile redirect attempt 2 (replace):', redirectUrl);
                        window.location.replace(redirectUrl);
                    },
                    () => {
                        const redirectUrl = gameType === 'custom' 
                            ? `/jeopardy/play-custom?lobby=${lobbyCode}`
                            : '/jeopardy/setup';
                        console.log('Mobile redirect attempt 3 (assign):', redirectUrl);
                        window.location.assign(redirectUrl);
                    }
                ];
                
                // Try multiple redirect methods with delays
                redirectAttempts.forEach((attempt, index) => {
                    setTimeout(() => {
                        try {
                            attempt();
                        } catch (error) {
                            console.error(`Mobile redirect attempt ${index + 1} failed:`, error);
                        }
                    }, 2000 + (index * 1000)); // 2s, 3s, 4s delays
                });
                
            } else {
                // Desktop redirect (faster)
                console.log('Desktop device detected, using fast redirect');
                if (gameType === 'custom') {
                    window.location.href = `/jeopardy/play-custom?lobby=${lobbyCode}`;
                } else {
                    window.location.href = '/jeopardy/setup';
                }
            }
        }
        
        // ULTRA-RELIABLE MOBILE-FIRST STATUS CHECKING SYSTEM
        let lastPlayerCount = {{ count($lobby->players) }};
        let redirectInProgress = false;
        let statusCheckCount = 0;
        let failedChecks = 0;
        let maxFailedChecks = 5;
        let isMobile = window.innerWidth <= 768;
        
        console.log('Initializing ULTRA-RELIABLE status checking system');
        console.log('Device type:', isMobile ? 'MOBILE' : 'DESKTOP');
        console.log('Lobby code:', '{{ $lobby->lobby_code }}');
        
        // AGGRESSIVE status checking for mobile devices
        const statusCheckIntervals = [];
        
        if (isMobile) {
            // MOBILE: Check every 1 second for maximum responsiveness
            statusCheckIntervals.push(setInterval(() => {
                performStatusCheck('mobile-aggressive');
            }, 1000));
            
            // MOBILE: Backup check every 2 seconds
            statusCheckIntervals.push(setInterval(() => {
                performStatusCheck('mobile-backup');
            }, 2000));
            
            // MOBILE: Emergency check every 3 seconds
            statusCheckIntervals.push(setInterval(() => {
                performStatusCheck('mobile-emergency');
            }, 3000));
        } else {
            // DESKTOP: Standard checks
            statusCheckIntervals.push(setInterval(() => {
                performStatusCheck('desktop-primary');
            }, 3000));
            
            statusCheckIntervals.push(setInterval(() => {
                performStatusCheck('desktop-backup');
            }, 5000));
        }
        
        function performStatusCheck(type) {
            if (redirectInProgress) {
                console.log(`${type}: Redirect already in progress, skipping`);
                return;
            }
            
            statusCheckCount++;
            console.log(`${type}: Status check #${statusCheckCount} for lobby {{ $lobby->lobby_code }}`);
            
            // Use multiple endpoints with cache-busting
            const timestamp = Date.now();
            const endpoints = [
                `/jeopardy/lobby/{{ $lobby->lobby_code }}/status?t=${timestamp}`,
                `/jeopardy/lobby/{{ $lobby->lobby_code }}/status?cache=${timestamp}`,
                `/jeopardy/lobby/{{ $lobby->lobby_code }}/status?mobile=${isMobile}&t=${timestamp}`
            ];
            
            const endpoint = endpoints[Math.floor(Math.random() * endpoints.length)];
            
            fetch(endpoint, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log(`${type}: Response status:`, response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(`${type}: Response data:`, data);
                failedChecks = 0; // Reset failed checks on success
                
                if (data.success) {
                    const currentStatus = data.lobby.status;
                    const gameType = data.lobby.game_settings?.game_type || 'standard';
                    const currentPlayerCount = data.lobby.players?.length || 0;
                    
                    console.log(`${type}: Lobby status:`, { 
                        currentStatus, 
                        gameType, 
                        currentPlayerCount,
                        lastPlayerCount,
                        isMobile: isMobile
                    });
                    
                    if (currentStatus === 'playing') {
                        redirectInProgress = true;
                        console.log(`${type}: GAME IS PLAYING! Initiating ULTRA-RELIABLE redirect...`);
                        
                        // Clear all intervals immediately
                        statusCheckIntervals.forEach(interval => clearInterval(interval));
                        
                        // MOBILE: Show immediate notification
                        if (isMobile) {
                            notifications.info(
                                '🎮 Game Started!',
                                'The host has started the game. Redirecting you NOW...',
                                2000
                            );
                        }
                        
                        // ULTRA-RELIABLE redirect with multiple attempts
                        performUltraReliableRedirect(gameType, '{{ $lobby->lobby_code }}');
                        
                        // Show manual redirect button after 5 seconds (mobile) or 8 seconds (desktop)
                        const fallbackDelay = isMobile ? 5000 : 8000;
                        setTimeout(() => {
                            const manualRedirectSection = document.getElementById('manualRedirectSection');
                            if (manualRedirectSection) {
                                manualRedirectSection.classList.remove('hidden');
                                console.log('Manual redirect button shown as fallback');
                            }
                        }, fallbackDelay);
                        
                    } else if (currentPlayerCount !== lastPlayerCount) {
                        lastPlayerCount = currentPlayerCount;
                        console.log(`${type}: Player count changed, reloading page...`);
                        location.reload();
                    }
                } else {
                    console.error(`${type}: Status check failed:`, data.message);
                    failedChecks++;
                }
            })
            .catch(error => {
                console.error(`${type}: Status check error:`, error);
                failedChecks++;
                
                // Show manual redirect option after fewer failed checks on mobile
                const maxChecksForManual = isMobile ? 3 : 5;
                if (failedChecks >= maxChecksForManual) {
                    console.log(`Too many failed checks (${failedChecks}), showing manual redirect option`);
                    const manualRedirectSection = document.getElementById('manualRedirectSection');
                    if (manualRedirectSection) {
                        manualRedirectSection.classList.remove('hidden');
                    }
                }
                
                if (isMobile) {
                    console.log('MOBILE ERROR DETAILS:', {
                        error: error.message,
                        lobbyCode: '{{ $lobby->lobby_code }}',
                        userAgent: navigator.userAgent,
                        failedChecks: failedChecks,
                        timestamp: new Date().toISOString()
                    });
                }
            });
        }
        
        // ULTRA-RELIABLE redirect function with multiple fallback methods
        function performUltraReliableRedirect(gameType, lobbyCode) {
            console.log('ULTRA-RELIABLE redirect initiated:', { gameType, lobbyCode, isMobile });
            
            // ENHANCED URL generation with mobile-specific parameters
            let redirectUrl;
            if (gameType === 'custom') {
                // Add mobile-specific parameters to help with game state loading
                const mobileParams = isMobile ? '&mobile=1&t=' + Date.now() : '';
                redirectUrl = `/jeopardy/play-custom?lobby=${lobbyCode}${mobileParams}`;
            } else {
                redirectUrl = '/jeopardy/setup';
            }
            
            console.log('Generated redirect URL:', redirectUrl);
            
            if (isMobile) {
                // MOBILE: Multiple redirect attempts with different methods
                console.log('MOBILE: Using ULTRA-RELIABLE redirect methods');
                
                // Method 1: Immediate redirect
                setTimeout(() => {
                    console.log('MOBILE: Attempt 1 - window.location.href');
                    try {
                        window.location.href = redirectUrl;
                    } catch (error) {
                        console.error('MOBILE: Attempt 1 failed:', error);
                    }
                }, 1000);
                
                // Method 2: window.location.replace
                setTimeout(() => {
                    console.log('MOBILE: Attempt 2 - window.location.replace');
                    try {
                        window.location.replace(redirectUrl);
                    } catch (error) {
                        console.error('MOBILE: Attempt 2 failed:', error);
                    }
                }, 2000);
                
                // Method 3: window.location.assign
                setTimeout(() => {
                    console.log('MOBILE: Attempt 3 - window.location.assign');
                    try {
                        window.location.assign(redirectUrl);
                    } catch (error) {
                        console.error('MOBILE: Attempt 3 failed:', error);
                    }
                }, 3000);
                
                // Method 4: Force redirect with window.open
                setTimeout(() => {
                    console.log('MOBILE: Attempt 4 - window.open');
                    try {
                        window.open(redirectUrl, '_self');
                    } catch (error) {
                        console.error('MOBILE: Attempt 4 failed:', error);
                    }
                }, 4000);
                
                // Method 5: Create and click a link
                setTimeout(() => {
                    console.log('MOBILE: Attempt 5 - Create and click link');
                    try {
                        const link = document.createElement('a');
                        link.href = redirectUrl;
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } catch (error) {
                        console.error('MOBILE: Attempt 5 failed:', error);
                    }
                }, 5000);
                
            } else {
                // DESKTOP: Standard redirect
                console.log('DESKTOP: Using standard redirect');
                window.location.href = redirectUrl;
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            
            // Check if game has already started and redirect immediately
            const currentStatus = '{{ $lobby->status }}';
            const gameType = '{{ $lobby->game_settings["game_type"] ?? "standard" }}';
            
            if (currentStatus === 'playing') {
                console.log('Game already started, redirecting immediately...');
                redirectInProgress = true;
                redirectToGame(gameType, '{{ $lobby->lobby_code }}');
                
                // Set up fallback timer for manual redirect
                setTimeout(() => {
                    const manualRedirectSection = document.getElementById('manualRedirectSection');
                    if (manualRedirectSection) {
                        manualRedirectSection.classList.remove('hidden');
                        console.log('Manual redirect button shown as fallback');
                    }
                }, 10000); // Show manual redirect after 10 seconds
                
                return;
            }
            
            // Show welcome notification
            setTimeout(() => {
                notifications.success(
                    'Welcome to the Lobby!',
                    'Share the lobby code with others to start playing together.',
                    4000
                );
            }, 1000);
            
            // Mobile-specific optimizations
            if (window.innerWidth <= 768) {
                console.log('Mobile device detected, applying mobile optimizations');
                
                // Show mobile-specific manual redirect button immediately
                setTimeout(() => {
                    const mobileManualRedirectSection = document.getElementById('mobileManualRedirectSection');
                    if (mobileManualRedirectSection) {
                        mobileManualRedirectSection.classList.remove('hidden');
                        console.log('Mobile manual redirect button shown immediately');
                    }
                }, 2000); // Show after 2 seconds for mobile users
                
                // Add touch event listeners for better mobile interaction
                const buttons = document.querySelectorAll('button');
                buttons.forEach(button => {
                    button.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    });
                    
                    button.addEventListener('touchend', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
                
                // Prevent zoom on input focus for iOS
                const inputs = document.querySelectorAll('input');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.style.fontSize = '16px';
                    });
                });
            }
        });
    </script>
</body>
</html>
