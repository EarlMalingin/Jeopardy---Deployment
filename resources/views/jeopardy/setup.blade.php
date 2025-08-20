<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Jeopardy Game Setup</title>
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
        
        .slide-in {
            animation: slideIn 0.6s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .team-count-btn.selected {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-color: #60a5fa;
            transform: scale(1.05);
        }
        
        .team-count-btn.selected:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }
        
        .difficulty-btn.selected {
            transform: scale(1.05);
            border-color: currentColor;
        }
        
        .difficulty-btn.selected[data-difficulty="easy"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-color: #34d399;
        }
        
        .difficulty-btn.selected[data-difficulty="normal"] {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-color: #fbbf24;
        }
        
        .difficulty-btn.selected[data-difficulty="hard"] {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            border-color: #fb923c;
        }
        
        .difficulty-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
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
            position: absolute;
            width: 3px;
            height: 3px;
            background: #3b82f6;
            border-radius: 50%;
            opacity: 0.6;
            animation: particle-float 8s infinite linear;
            pointer-events: none;
        }
        
        @keyframes particle-float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-100px) rotate(180deg); opacity: 0; }
        }
        
        /* Prevent horizontal scroll bar from appearing */
        html, body {
            overflow-x: hidden;
            height: 100vh;
            width: 100vw;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Ensure main container fits properly */
        .main-container {
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Mobile-specific optimizations */
        @media (max-width: 768px) {
            .team-count-btn, .difficulty-btn {
                min-height: 60px;
                padding: 0.75rem !important;
            }
            
            .team-count-btn .text-lg {
                font-size: 1.125rem;
            }
            
            .difficulty-btn .text-lg {
                font-size: 1rem;
            }
            
            .difficulty-btn .text-xs {
                font-size: 0.625rem;
            }
        }
        
        @media (max-width: 480px) {
            .team-count-btn, .difficulty-btn {
                min-height: 50px;
                padding: 0.5rem !important;
            }
            
            .team-count-btn .text-lg {
                font-size: 1rem;
            }
            
            .difficulty-btn .text-lg {
                font-size: 0.875rem;
            }
            
            .difficulty-btn .text-xs {
                font-size: 0.5rem;
            }
        }
        
        /* Touch-friendly button improvements */
        .touch-button {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        /* Mobile form improvements */
        .mobile-form {
            padding: 1rem;
        }
        
        @media (max-width: 768px) {
            .mobile-form {
                padding: 0.75rem;
            }
        }
        
        /* Mobile notification improvements */
        .mobile-notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            left: 1rem;
            z-index: 50;
        }
        
        @media (max-width: 768px) {
            .mobile-notification {
                top: 0.5rem;
                right: 0.5rem;
                left: 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-white main-container">
    <!-- Background Particles -->
    <div id="particles" class="absolute inset-0 pointer-events-none"></div>
    
    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col justify-center p-2 sm:p-4 py-4 sm:py-8">
        <div class="max-w-md w-full mx-auto">
            <div class="bg-gray-800 rounded-2xl p-4 sm:p-8 shadow-2xl mobile-form">
                <!-- Title Section -->
                <div class="text-center mb-6 sm:mb-8">
                    <div class="floating-animation mb-4 sm:mb-6">
                        <h1 class="text-2xl sm:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 glow-text leading-tight">
                            🎯 Welcome to Jeopardy!
                        </h1>
                    </div>
                    <p class="text-sm sm:text-base text-gray-300">Enter your team names to begin the ultimate trivia challenge</p>
                </div>
                
                <form id="setupForm" class="space-y-4 sm:space-y-6">
                    <!-- Team Count Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2 sm:mb-3">Number of Teams</label>
                        <div class="grid grid-cols-5 gap-2 sm:gap-3">
                            <button type="button" class="team-count-btn bg-gray-700 hover:bg-blue-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-blue-400 touch-button" data-teams="2" onclick="selectTeamCount(2, event)">
                                <div class="text-base sm:text-lg font-bold">2</div>
                                <div class="text-xs text-gray-400">Teams</div>
                            </button>
                            <button type="button" class="team-count-btn bg-gray-700 hover:bg-blue-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-blue-400 touch-button" data-teams="3" onclick="selectTeamCount(3, event)">
                                <div class="text-base sm:text-lg font-bold">3</div>
                                <div class="text-xs text-gray-400">Teams</div>
                            </button>
                            <button type="button" class="team-count-btn bg-gray-700 hover:bg-blue-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-blue-400 touch-button" data-teams="4" onclick="selectTeamCount(4, event)">
                                <div class="text-base sm:text-lg font-bold">4</div>
                                <div class="text-xs text-gray-400">Teams</div>
                            </button>
                            <button type="button" class="team-count-btn bg-gray-700 hover:bg-blue-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-blue-400 touch-button" data-teams="5" onclick="selectTeamCount(5, event)">
                                <div class="text-base sm:text-lg font-bold">5</div>
                                <div class="text-xs text-gray-400">Teams</div>
                            </button>
                            <button type="button" class="team-count-btn bg-gray-700 hover:bg-blue-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-blue-400 touch-button" data-teams="6" onclick="selectTeamCount(6, event)">
                                <div class="text-base sm:text-lg font-bold">6</div>
                                <div class="text-xs text-gray-400">Teams</div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Team Names Container -->
                    <div id="teamNamesContainer" class="space-y-3 sm:space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Team 1 Name</label>
                            <input type="text" id="team1Name" required 
                                   class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                   placeholder="Enter Team 1 name">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Team 2 Name</label>
                            <input type="text" id="team2Name" required 
                                   class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                   placeholder="Enter Team 2 name">
                        </div>
                    </div>
                    
                    <!-- Difficulty Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2 sm:mb-3">Game Difficulty</label>
                        <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <button type="button" class="difficulty-btn bg-gray-700 hover:bg-green-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-green-400 touch-button" data-difficulty="easy" onclick="selectDifficulty('easy', event)">
                                <div class="text-base sm:text-lg font-bold text-green-400">Easy</div>
                                <div class="text-xs text-gray-400">Beginner</div>
                            </button>
                            <button type="button" class="difficulty-btn bg-gray-700 hover:bg-yellow-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-yellow-400 touch-button" data-difficulty="normal" onclick="selectDifficulty('normal', event)">
                                <div class="text-base sm:text-lg font-bold text-yellow-400">Normal</div>
                                <div class="text-xs text-gray-400">Balanced</div>
                            </button>
                            <button type="button" class="difficulty-btn bg-gray-700 hover:bg-orange-600 text-white py-2 sm:py-3 px-2 sm:px-4 rounded-lg transition-colors border-2 border-transparent hover:border-orange-400 touch-button" data-difficulty="hard" onclick="selectDifficulty('hard', event)">
                                <div class="text-base sm:text-lg font-bold text-orange-400">Hard</div>
                                <div class="text-xs text-gray-400">Expert</div>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <button type="button" onclick="goToMainMenu()" 
                                class="w-full sm:flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors text-sm sm:text-base touch-button">
                            Back
                        </button>
                        <button type="submit" 
                                class="w-full sm:flex-1 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-all duration-300 transform hover:scale-105 text-sm sm:text-base touch-button">
                            Start Game
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-6 sm:mt-8">
                <p class="text-gray-400 text-xs sm:text-sm px-2">
                    Challenge your knowledge • Test your speed • Have fun!
                </p>
            </div>
        </div>
    </div>

    <script>
        // Team and difficulty selection functionality
        let selectedTeamCount = 2;
        let selectedDifficulty = 'normal';

        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            // Clear any existing particles
            particlesContainer.innerHTML = '';
            
            // Reduce particles on mobile for better performance
            const particleCount = window.innerWidth < 768 ? 15 : 25;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + 'vw';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particle.style.animationDuration = (Math.random() * 4 + 6) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        function selectTeamCount(teamCount, event) {
            selectedTeamCount = teamCount;
            
            // Update button styles
            document.querySelectorAll('.team-count-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            event.target.closest('.team-count-btn').classList.add('selected');
            
            // Generate team name inputs
            generateTeamInputs(teamCount);
        }

        function generateTeamInputs(teamCount) {
            const container = document.getElementById('teamNamesContainer');
            container.innerHTML = '';
            container.className = 'space-y-3 sm:space-y-4';
            
            const teamColors = ['blue', 'red', 'green', 'yellow', 'purple', 'pink'];
            
            for (let i = 1; i <= teamCount; i++) {
                const color = teamColors[i - 1];
                const div = document.createElement('div');
                div.innerHTML = `
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Team ${i} Name</label>
                        <input type="text" id="team${i}Name" required 
                               class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-${color}-500 focus:border-transparent text-sm sm:text-base"
                               placeholder="Enter Team ${i} name">
                    </div>
                `;
                container.appendChild(div);
            }
        }

        function selectDifficulty(difficulty, event) {
            selectedDifficulty = difficulty;
            
            // Update button styles
            document.querySelectorAll('.difficulty-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            event.target.closest('.difficulty-btn').classList.add('selected');
        }

        function goToMainMenu() {
            window.location.href = '/jeopardy';
        }

        // Setup form submission
        document.getElementById('setupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            console.log('Form submitted');
            console.log('Selected team count:', selectedTeamCount);
            console.log('Selected difficulty:', selectedDifficulty);
            
            // Collect team names dynamically
            const teamNames = [];
            for (let i = 1; i <= selectedTeamCount; i++) {
                const teamName = document.getElementById(`team${i}Name`).value;
                console.log(`Team ${i} name:`, teamName);
                if (!teamName.trim()) {
                    alert(`Please enter a name for Team ${i}`);
                    return;
                }
                teamNames.push(teamName.trim());
            }

            console.log('Team names collected:', teamNames);

            try {
                const requestData = {
                    team_names: teamNames,
                    difficulty: selectedDifficulty
                };
                
                console.log('Sending request with data:', requestData);
                
                const response = await fetch('/jeopardy/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(requestData)
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    console.log('Game started successfully, redirecting...');
                    console.log('Game state saved:', data.game_state);
                    
                    // Clear lobby info from session if it exists
                    if (window.lobbyInfo) {
                        fetch('/jeopardy/clear-lobby-info', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        }).catch(error => console.error('Error clearing lobby info:', error));
                    }
                    
                    // Redirect to the game page with the game state
                    window.location.href = '/jeopardy/play';
                } else {
                    console.error('Error starting game:', data);
                    alert('Error starting game: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error starting game:', error);
                alert('Error starting game. Please try again.');
            }
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            
            // Recreate particles on window resize for better mobile experience
            window.addEventListener('resize', () => {
                setTimeout(createParticles, 100);
            });
            
            // Check if we're coming from a lobby
            const lobbyInfo = @json(Session::get('lobby_game_info'));
            
            if (lobbyInfo && lobbyInfo.player_names && lobbyInfo.player_names.length > 0) {
                // We're coming from a lobby, use the player names
                window.lobbyInfo = lobbyInfo; // Store globally for later use
                const playerNames = lobbyInfo.player_names;
                selectedTeamCount = playerNames.length;
                selectedDifficulty = lobbyInfo.difficulty || 'normal';
                
                // Update button styles for the correct team count
                document.querySelectorAll('.team-count-btn').forEach(btn => {
                    btn.classList.remove('selected');
                });
                document.querySelector(`[data-teams="${selectedTeamCount}"]`).classList.add('selected');
                
                document.querySelectorAll('.difficulty-btn').forEach(btn => {
                    btn.classList.remove('selected');
                });
                document.querySelector(`[data-difficulty="${selectedDifficulty}"]`).classList.add('selected');
                
                // Generate team inputs and pre-fill with player names
                generateTeamInputs(selectedTeamCount);
                
                // Pre-fill the team names with actual player names
                setTimeout(() => {
                    for (let i = 0; i < playerNames.length; i++) {
                        const input = document.getElementById(`team${i + 1}Name`);
                        if (input) {
                            input.value = playerNames[i];
                        }
                    }
                }, 100);
                
                // Show a notification that we're using lobby player names
                showLobbyNotification(lobbyInfo);
            } else {
                // Regular setup, use defaults
                selectedTeamCount = 2;
                selectedDifficulty = 'normal';
                
                // Update button styles for initial state
                document.querySelectorAll('.team-count-btn').forEach(btn => {
                    btn.classList.remove('selected');
                });
                document.querySelector('[data-teams="2"]').classList.add('selected');
                
                document.querySelectorAll('.difficulty-btn').forEach(btn => {
                    btn.classList.remove('selected');
                });
                document.querySelector('[data-difficulty="normal"]').classList.add('selected');
            }
        });

        function showLobbyNotification(lobbyInfo) {
            // Create a notification element
            const notification = document.createElement('div');
            notification.className = 'mobile-notification bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg shadow-lg';
            notification.innerHTML = `
                <div class="flex items-center justify-center sm:justify-start">
                    <span class="mr-2">🎮</span>
                    <span class="text-sm sm:text-base">Using player names from lobby: ${lobbyInfo.player_names.join(', ')}</span>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Remove notification after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</body>
</html>
