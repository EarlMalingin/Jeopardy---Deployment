<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy - Lobby Selection</title>
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
        
        .lobby-button {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .lobby-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .lobby-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .lobby-button:hover::before {
            left: 100%;
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
            width: 4px;
            height: 4px;
            background: #3b82f6;
            border-radius: 50%;
            animation: particle-float 6s infinite linear;
        }
        
        @keyframes particle-float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }
        
        /* Prevent scroll bar flickering */
        html, body {
            overflow-x: hidden;
            overflow-y: auto;
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* Ensure main container fits properly */
        .main-container {
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Fix particle positioning to prevent layout shifts */
        .particle {
            position: fixed;
            pointer-events: none;
            z-index: 1;
        }

        /* Beautiful Popup Notification */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .popup-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .popup-content {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            transform: scale(0.7) translateY(20px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .popup-overlay.show .popup-content {
            transform: scale(1) translateY(0);
        }

        .popup-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .popup-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        .popup-title {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 16px;
        }

        .popup-message {
            color: #d1d5db;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .lobby-code {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 12px;
            margin: 16px 0;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .share-link {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 12px;
            margin: 16px 0;
            font-size: 14px;
            color: #9ca3af;
            word-break: break-all;
        }

        .popup-button {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 16px;
        }

        .popup-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }

        .popup-button:active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-900 text-white main-container">
    <!-- Popup Notification -->
    <div id="popupOverlay" class="popup-overlay">
        <div class="popup-content">
            <div class="popup-icon">🎉</div>
            <h2 class="popup-title" id="popupTitle">Lobby Created Successfully!</h2>
            <p class="popup-message" id="popupMessage">Your lobby has been created and is ready for players to join.</p>
            <div class="lobby-code" id="popupLobbyCode">ABC123</div>
            <div class="share-link" id="popupShareLink">Share this link with others</div>
            <button class="popup-button" onclick="closePopup()">Enter Lobby</button>
        </div>
    </div>

    <!-- Background Particles -->
    <div id="particles" class="absolute inset-0 pointer-events-none"></div>
    
    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col justify-center p-4 py-8">
        <div class="max-w-4xl w-full mx-auto">
            <!-- Back Button -->
            <div class="mb-8">
                <button onclick="window.location.href='/jeopardy'" class="flex items-center text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Main Menu
                </button>
            </div>

            <!-- Title Section -->
            <div class="text-center mb-12">
                <div class="floating-animation mb-6">
                    <h1 class="text-5xl md:text-7xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 glow-text">
                        🎮 Lobby
                    </h1>
                </div>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-2xl mx-auto">
                    Choose how you want to play with others
                </p>
                <div class="flex justify-center space-x-4 text-sm text-gray-400">
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        Create Lobby
                    </span>
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Custom Games
                    </span>
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                        Join Games
                    </span>
                    <span class="flex items-center">
                        <span class="w-2 h-2 bg-pink-500 rounded-full mr-2"></span>
                        Max 7 Players
                    </span>
                </div>
            </div>

            <!-- Lobby Options -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
                <!-- Create Lobby -->
                <div class="lobby-button rounded-2xl p-6 text-center cursor-pointer" onclick="showCreateLobby()">
                    <div class="text-5xl mb-4 flex justify-center">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Create Lobby</h3>
                    <p class="text-gray-300 text-sm">Start a new multiplayer game and invite others to join</p>
                </div>

                <!-- Create Custom Game -->
                <div class="lobby-button rounded-2xl p-6 text-center cursor-pointer" onclick="createCustomGame()">
                    <div class="text-5xl mb-4 flex justify-center">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Create Custom Game</h3>
                    <p class="text-gray-300 text-sm">Create your own custom game with categories and questions (1-6 teams)</p>
                </div>

                <!-- Join Lobby -->
                <div class="lobby-button rounded-2xl p-6 text-center cursor-pointer" onclick="showJoinLobby()">
                    <div class="text-5xl mb-4 flex justify-center">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Join Lobby</h3>
                    <p class="text-gray-300 text-sm">Enter a lobby code to join an existing game</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-12">
                <p class="text-gray-400 text-sm">
                    Connect • Compete • Conquer
                </p>
            </div>
        </div>
    </div>

    <!-- Create Lobby Modal -->
    <div id="createLobbyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black bg-opacity-75"></div>
        <div class="relative bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-4">
                    🏗️ Create Lobby
                </h2>
                <p class="text-gray-300">Set up your lobby and invite others to join</p>
            </div>
            
            <form id="createLobbyForm" class="space-y-4">
                <div>
                    <label for="hostName" class="block text-sm font-medium text-gray-300 mb-2">Your Name</label>
                    <input 
                        type="text" 
                        id="hostName" 
                        name="hostName" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your name"
                        required
                    >
                </div>
                
                <div>
                    <label for="gameType" class="block text-sm font-medium text-gray-300 mb-2">Game Type</label>
                    <select 
                        id="gameType" 
                        name="gameType" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                        <option value="custom">Custom Game</option>
                    </select>
                </div>
                

                
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="hideCreateLobby()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                    >
                        Create Lobby
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Join Lobby Modal -->
    <div id="joinLobbyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black bg-opacity-75"></div>
        <div class="relative bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-4">
                    🚪 Join Lobby
                </h2>
                <p class="text-gray-300">Enter the lobby code and your name to join</p>
            </div>
            
            <form id="joinLobbyForm" class="space-y-4">
                <div>
                    <label for="lobbyCode" class="block text-sm font-medium text-gray-300 mb-2">Lobby Code</label>
                    <input 
                        type="text" 
                        id="lobbyCode" 
                        name="lobbyCode" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter 6-digit code"
                        maxlength="6"
                        pattern="[A-Za-z0-9]{6}"
                        required
                    >
                </div>
                
                <div>
                    <label for="playerName" class="block text-sm font-medium text-gray-300 mb-2">Your Name</label>
                    <input 
                        type="text" 
                        id="playerName" 
                        name="playerName" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your name"
                        required
                    >
                </div>
                
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="hideJoinLobby()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                    >
                        Join Game
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            // Clear any existing particles
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

        function showCreateLobby() {
            document.getElementById('createLobbyModal').classList.remove('hidden');
            document.getElementById('hostName').focus();
        }

        function hideCreateLobby() {
            document.getElementById('createLobbyModal').classList.add('hidden');
            document.getElementById('createLobbyForm').reset();
        }

        function showJoinLobby() {
            document.getElementById('joinLobbyModal').classList.remove('hidden');
            document.getElementById('lobbyCode').focus();
        }

        function hideJoinLobby() {
            document.getElementById('joinLobbyModal').classList.add('hidden');
            document.getElementById('joinLobbyForm').reset();
        }

        function createCustomGame() {
            // Redirect directly to simple custom game creator
            window.location.href = '/jeopardy/simple-custom-game';
        }



        function showPopup(title, message, lobbyCode, shareLink) {
            document.getElementById('popupTitle').textContent = title;
            document.getElementById('popupMessage').textContent = message;
            document.getElementById('popupLobbyCode').textContent = lobbyCode;
            document.getElementById('popupShareLink').textContent = shareLink;
            document.getElementById('popupOverlay').classList.add('show');
        }

        function closePopup() {
            document.getElementById('popupOverlay').classList.remove('show');
            // Redirect to custom game creator
            if (window.lobbyUrl && window.gameType) {
                window.location.href = '/jeopardy/custom-game?lobby=' + window.lobbyCode;
            }
        }



        // Handle create lobby form submission
        document.getElementById('createLobbyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const hostName = document.getElementById('hostName').value.trim();
            const gameType = document.getElementById('gameType').value;
            
            if (!hostName) {
                alert('Please enter your name');
                return;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Creating...';
            submitBtn.disabled = true;



            const gameSettings = {
                game_type: gameType
            };

            fetch('/jeopardy/create-lobby', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    host_name: hostName,
                    game_settings: gameSettings
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideCreateLobby();
                    
                    // Store data for redirection
                    window.lobbyUrl = data.lobby_url;
                    window.gameType = gameType;
                    window.lobbyCode = data.lobby_code;
                    
                    showPopup(
                        'Lobby Created Successfully!',
                        'Your custom game lobby has been created. You\'ll be redirected to create your custom game first.',
                        data.lobby_code,
                        data.lobby_url
                    );
                } else {
                    alert('Error creating lobby: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating lobby. Please try again.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Handle join lobby form submission
        document.getElementById('joinLobbyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const lobbyCode = document.getElementById('lobbyCode').value.toUpperCase();
            const playerName = document.getElementById('playerName').value.trim();
            
            if (lobbyCode.length !== 6) {
                alert('Please enter a valid 6-digit lobby code');
                return;
            }

            if (!playerName) {
                alert('Please enter your name');
                return;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Joining...';
            submitBtn.disabled = true;

            fetch('/jeopardy/join-lobby', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    lobby_code: lobbyCode,
                    player_name: playerName
                })
            })
            .then(response => {
                console.log('Join lobby response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Join lobby response data:', data);
                if (data.success) {
                    hideJoinLobby();
                    console.log('Redirecting to:', data.lobby_url);
                    window.location.href = data.lobby_url;
                } else {
                    console.error('Join lobby failed:', data.message);
                    alert('Error joining lobby: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error joining lobby:', error);
                alert('Error joining lobby. Please try again.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
        });
    </script>
</body>
</html>
