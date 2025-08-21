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
        
        /* Mobile-specific improvements for lobby modals */
        @media (max-width: 768px) {
            /* Modal improvements for mobile */
            .modal-mobile {
                padding: 1rem !important;
                margin: 0.5rem !important;
                max-height: 90vh !important;
                overflow-y: auto !important;
            }
            
            /* Input field improvements for mobile */
            .mobile-input {
                font-size: 16px !important; /* Prevent zoom on iOS */
                padding: 0.75rem !important;
                min-height: 44px !important; /* Minimum touch target */
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation;
            }
            
            /* Button improvements for mobile */
            .mobile-button {
                min-height: 44px !important;
                font-size: 1rem !important;
                padding: 0.75rem 1rem !important;
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation;
            }
            
            /* Modal content improvements */
            .modal-content-mobile {
                max-width: 95vw !important;
                width: 95vw !important;
                margin: 0.25rem !important;
            }
            
            /* Form spacing improvements */
            .mobile-form-space {
                gap: 1rem !important;
            }
            
            /* Text improvements for mobile */
            .mobile-text {
                font-size: 0.875rem !important;
                line-height: 1.4 !important;
            }
            
            .mobile-title {
                font-size: 1.5rem !important;
                margin-bottom: 0.75rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .modal-mobile {
                padding: 0.75rem !important;
                margin: 0.25rem !important;
                max-height: 95vh !important;
            }
            
            .mobile-input {
                font-size: 16px !important;
                padding: 0.5rem !important;
                min-height: 40px !important;
            }
            
            .mobile-button {
                min-height: 40px !important;
                font-size: 0.875rem !important;
                padding: 0.5rem 0.75rem !important;
            }
            
            .mobile-title {
                font-size: 1.25rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .mobile-text {
                font-size: 0.75rem !important;
            }
        }
        
        /* Prevent horizontal scroll on mobile */
        @media (max-width: 768px) {
            body {
                overflow-x: hidden;
                -webkit-overflow-scrolling: touch;
            }
            
            .main-container {
                overflow-x: hidden;
            }
        }
        
        /* Touch-friendly improvements */
        .touch-button {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        /* Better error handling for mobile */
        .mobile-error {
            position: fixed;
            top: 1rem;
            left: 1rem;
            right: 1rem;
            z-index: 10000;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            animation: slideInDown 0.3s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
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
                <div class="lobby-button rounded-2xl p-6 text-center cursor-pointer touch-button" onclick="showCreateLobby()">
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
                <div class="lobby-button rounded-2xl p-6 text-center cursor-pointer touch-button" onclick="createCustomGame()">
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
                <div class="lobby-button rounded-2xl p-6 text-center cursor-pointer touch-button" onclick="showJoinLobby()">
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
        <div class="relative bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 modal-mobile modal-content-mobile">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-4 mobile-title">
                    🏗️ Create Lobby
                </h2>
                <p class="text-gray-300 mobile-text">Set up your lobby and invite others to join</p>
            </div>
            
            <form id="createLobbyForm" class="space-y-4 mobile-form-space">
                <div>
                    <label for="hostName" class="block text-sm font-medium text-gray-300 mb-2 mobile-text">Your Name</label>
                    <input 
                        type="text" 
                        id="hostName" 
                        name="hostName" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mobile-input touch-button"
                        placeholder="Enter your name"
                        required
                        autocomplete="off"
                        autocorrect="off"
                    >
                </div>
                
                <div>
                    <label for="gameType" class="block text-sm font-medium text-gray-300 mb-2 mobile-text">Game Type</label>
                    <select 
                        id="gameType" 
                        name="gameType" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mobile-input touch-button"
                        required
                    >
                        <option value="custom">Custom Game</option>
                    </select>
                </div>
                

                
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="hideCreateLobby()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors mobile-button touch-button"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors mobile-button touch-button"
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
        <div class="relative bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 modal-mobile modal-content-mobile">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-4 mobile-title">
                    🚪 Join Lobby
                </h2>
                <p class="text-gray-300 mobile-text">Enter the lobby code and your name to join</p>
            </div>
            
            <form id="joinLobbyForm" class="space-y-4 mobile-form-space">
                <div>
                    <label for="lobbyCode" class="block text-sm font-medium text-gray-300 mb-2 mobile-text">Lobby Code</label>
                    <input 
                        type="text" 
                        id="lobbyCode" 
                        name="lobbyCode" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mobile-input touch-button"
                        placeholder="Enter 6-digit code"
                        maxlength="6"
                        pattern="[A-Za-z0-9]{6}"
                        required
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="characters"
                    >
                </div>
                
                <div>
                    <label for="playerName" class="block text-sm font-medium text-gray-300 mb-2 mobile-text">Your Name</label>
                    <input 
                        type="text" 
                        id="playerName" 
                        name="playerName" 
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mobile-input touch-button"
                        placeholder="Enter your name"
                        required
                        autocomplete="off"
                        autocorrect="off"
                    >
                </div>
                
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="hideJoinLobby()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors mobile-button touch-button"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors mobile-button touch-button"
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
            // Add mobile-friendly focus with delay
            setTimeout(() => {
                const hostNameInput = document.getElementById('hostName');
                if (hostNameInput) {
                    hostNameInput.focus();
                    // Scroll to input on mobile if needed
                    if (window.innerWidth <= 768) {
                        hostNameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }, 100);
        }

        function hideCreateLobby() {
            document.getElementById('createLobbyModal').classList.add('hidden');
            document.getElementById('createLobbyForm').reset();
            // Clear any mobile errors when hiding
            const existingError = document.querySelector('.mobile-error');
            if (existingError) {
                existingError.remove();
            }
        }

        function showJoinLobby() {
            document.getElementById('joinLobbyModal').classList.remove('hidden');
            // Add mobile-friendly focus with delay
            setTimeout(() => {
                const lobbyCodeInput = document.getElementById('lobbyCode');
                if (lobbyCodeInput) {
                    lobbyCodeInput.focus();
                    // Scroll to input on mobile if needed
                    if (window.innerWidth <= 768) {
                        lobbyCodeInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }, 100);
        }

        function hideJoinLobby() {
            document.getElementById('joinLobbyModal').classList.add('hidden');
            document.getElementById('joinLobbyForm').reset();
            // Clear any mobile errors when hiding
            const existingError = document.querySelector('.mobile-error');
            if (existingError) {
                existingError.remove();
            }
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
            
            // Mobile-friendly validation
            if (!hostName) {
                showMobileError('Please enter your name');
                return;
            }
            
            if (hostName.length < 2) {
                showMobileError('Name must be at least 2 characters long');
                return;
            }
            
            if (hostName.length > 20) {
                showMobileError('Name must be less than 20 characters');
                return;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Creating...';
            submitBtn.disabled = true;
            
            // Add visual feedback for mobile
            submitBtn.style.opacity = '0.7';
            submitBtn.style.transform = 'scale(0.98)';

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
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
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
                    showMobileError('Error creating lobby: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.message.includes('Failed to fetch')) {
                    showMobileError('Network error. Please check your connection and try again.');
                } else {
                    showMobileError('Error creating lobby. Please try again.');
                }
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.transform = 'scale(1)';
            });
        });

        // Mobile-friendly error display function
        function showMobileError(message) {
            // Remove any existing error messages
            const existingError = document.querySelector('.mobile-error');
            if (existingError) {
                existingError.remove();
            }
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mobile-error';
            errorDiv.textContent = message;
            document.body.appendChild(errorDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentElement) {
                    errorDiv.remove();
                }
            }, 5000);
        }
        
        // Mobile-friendly success display function
        function showMobileSuccess(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'mobile-error';
            successDiv.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
            successDiv.style.boxShadow = '0 10px 25px rgba(16, 185, 129, 0.3)';
            successDiv.textContent = message;
            document.body.appendChild(successDiv);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (successDiv.parentElement) {
                    successDiv.remove();
                }
            }, 3000);
        }
        
        // Handle join lobby form submission
        document.getElementById('joinLobbyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const lobbyCode = document.getElementById('lobbyCode').value.toUpperCase();
            const playerName = document.getElementById('playerName').value.trim();
            
            // Mobile-friendly validation
            if (lobbyCode.length !== 6) {
                showMobileError('Please enter a valid 6-digit lobby code');
                return;
            }

            if (!playerName) {
                showMobileError('Please enter your name');
                return;
            }
            
            if (playerName.length < 2) {
                showMobileError('Name must be at least 2 characters long');
                return;
            }
            
            if (playerName.length > 20) {
                showMobileError('Name must be less than 20 characters');
                return;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Joining...';
            submitBtn.disabled = true;
            
            // Add visual feedback for mobile
            submitBtn.style.opacity = '0.7';
            submitBtn.style.transform = 'scale(0.98)';

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
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Join lobby response data:', data);
                if (data.success) {
                    showMobileSuccess('Successfully joined lobby! Redirecting...');
                    hideJoinLobby();
                    console.log('Redirecting to:', data.lobby_url);
                    
                    // Add a small delay for mobile users to see the success message
                    setTimeout(() => {
                        window.location.href = data.lobby_url;
                    }, 1000);
                } else {
                    console.error('Join lobby failed:', data.message);
                    showMobileError('Error joining lobby: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error joining lobby:', error);
                if (error.message.includes('Failed to fetch')) {
                    showMobileError('Network error. Please check your connection and try again.');
                } else {
                    showMobileError('Error joining lobby. Please try again.');
                }
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.transform = 'scale(1)';
            });
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            
            // Mobile-specific optimizations
            if (window.innerWidth <= 768) {
                // Prevent zoom on input focus for iOS
                const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        // Prevent zoom on iOS
                        if (window.innerWidth <= 768) {
                            this.style.fontSize = '16px';
                        }
                    });
                });
                
                // Add touch event listeners for better mobile interaction
                const buttons = document.querySelectorAll('.touch-button');
                buttons.forEach(button => {
                    button.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    });
                    
                    button.addEventListener('touchend', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
            }
            
            // Handle orientation changes
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    // Recalculate layout after orientation change
                    if (window.innerWidth <= 768) {
                        // Force mobile layout
                        document.body.style.overflowX = 'hidden';
                    }
                }, 500);
            });
        });
    </script>
</body>
</html>
