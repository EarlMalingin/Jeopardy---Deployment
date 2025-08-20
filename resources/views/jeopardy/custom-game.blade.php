<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Game Creator - Jeopardy</title>
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
        
        .question-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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
        
        .bounce-in {
            animation: bounceIn 0.8s ease-out;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulseGlow {
            from { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
            to { box-shadow: 0 0 30px rgba(59, 130, 246, 0.8); }
        }
        
        .score-animation {
            animation: scoreUpdate 0.5s ease-out;
        }
        
        @keyframes scoreUpdate {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .team-active {
            border: 3px solid #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }
        
        .question-value {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .answered {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
            color: #9ca3af !important;
            cursor: not-allowed !important;
            opacity: 0.8 !important;
            pointer-events: none !important;
            animation: question-answered 0.5s ease-out, answered-pulse 3s ease-in-out infinite;
            border: 2px solid #4b5563 !important;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 20px rgba(16, 185, 129, 0.1), 0 0 10px rgba(75, 85, 99, 0.3);
        }
        
        .answered.incorrect {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%) !important;
            border: 2px solid #dc2626 !important;
            box-shadow: inset 0 0 20px rgba(220, 38, 38, 0.1), 0 0 10px rgba(220, 38, 38, 0.3);
        }
        
        .answered.incorrect::after {
            content: 'INCORRECT';
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 0.6rem;
            font-weight: bold;
            color: #fca5a5;
            background: rgba(220, 38, 38, 0.2);
            padding: 2px 6px;
            border-radius: 3px;
            z-index: 2;
        }
        
        .answered.correct::after {
            content: 'COMPLETED';
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 0.6rem;
            font-weight: bold;
            color: #10b981;
            background: rgba(16, 185, 129, 0.2);
            padding: 2px 6px;
            border-radius: 3px;
            z-index: 2;
        }
        
        .answered::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            animation: shimmer 2s infinite;
        }
        
        .answered .checkmark {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            font-weight: bold;
            color: #10b981;
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            z-index: 2;
        }
        
        .answered .x-mark {
            content: '✗';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            font-weight: bold;
            color: #fca5a5;
            text-shadow: 0 0 10px rgba(220, 38, 38, 0.5);
            z-index: 2;
        }
        
        @keyframes answered-pulse {
            0%, 100% { 
                box-shadow: 0 0 0 0 rgba(75, 85, 99, 0.4);
            }
            50% { 
                box-shadow: 0 0 0 4px rgba(75, 85, 99, 0.1);
            }
        }
        
        @keyframes question-answered {
            0% { 
                transform: scale(1);
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                border-color: #f59e0b;
            }
            50% { 
                transform: scale(1.05);
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                border-color: #10b981;
            }
            100% { 
                transform: scale(1);
                background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
                border-color: #4b5563;
            }
        }
        
        .answered.incorrect {
            animation: question-answered-incorrect 0.5s ease-out, answered-pulse 3s ease-in-out infinite;
        }
        
        @keyframes question-answered-incorrect {
            0% { 
                transform: scale(1);
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                border-color: #f59e0b;
            }
            50% { 
                transform: scale(1.05);
                background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
                border-color: #dc2626;
            }
            100% { 
                transform: scale(1);
                background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
                border-color: #dc2626;
            }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .loading-overlay {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #f59e0b;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }
        
        .menu-button {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            position: relative;
            overflow: hidden;
        }
        
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
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
        
        .team-count-btn.selected {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-color: #60a5fa;
            transform: scale(1.05);
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
        
        .difficulty-btn.selected[data-difficulty="challenging"] {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-color: #f87171;
        }
        
        /* Add hover effects for form elements */
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:hover {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            transform: translateY(-1px);
        }
        
        .form-select {
            transition: all 0.3s ease;
        }
        
        .form-select:hover {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            transform: translateY(-1px);
        }
        
        .submit-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .submit-btn:hover::before {
            left: 100%;
        }
        
        .back-btn {
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(75, 85, 99, 0.3);
        }
        
        .category-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .category-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
        }
        
        /* Mobile-friendly category improvements */
        @media (max-width: 768px) {
            .category-card {
                padding: 1rem !important;
                margin-bottom: 1rem !important;
            }
            
            .category-card h3 {
                font-size: 1.125rem !important;
                margin-bottom: 0.75rem !important;
            }
            
            .category-card input[type="text"] {
                font-size: 0.875rem !important;
                padding: 0.5rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .category-card label {
                font-size: 0.75rem !important;
                margin-bottom: 0.25rem !important;
            }
            
            /* Force single column layout for questions on mobile */
            .question-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            
            /* Force single column for category name */
            .category-name-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            
            /* Override any existing grid classes */
            .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.xl\\:grid-cols-5 {
                grid-template-columns: 1fr !important;
            }
            
            .grid.grid-cols-1.md\\:grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
        }
        
        @media (max-width: 480px) {
            .category-card {
                padding: 0.75rem !important;
            }
            
            .category-card h3 {
                font-size: 1rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .category-card input[type="text"] {
                font-size: 0.8rem !important;
                padding: 0.375rem !important;
            }
            
            .category-card label {
                font-size: 0.7rem !important;
            }
            
            /* Force single column for all grids on very small screens */
            .grid {
                grid-template-columns: 1fr !important;
            }
        }
        
        /* Touch-friendly improvements */
        .touch-input {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        /* Prevent zoom on input focus for mobile */
        @media (max-width: 768px) {
            input[type="text"], select {
                font-size: 16px !important;
            }
        }
        
        /* Additional mobile improvements */
        @media (max-width: 640px) {
            .text-center h2 {
                font-size: 1.5rem !important;
            }
            
            .text-center p {
                font-size: 0.875rem !important;
            }
            
            .space-y-6 > * + * {
                margin-top: 1rem !important;
            }
        }
        
        /* Ensure proper spacing on very small screens */
        @media (max-width: 480px) {
            .bg-gray-800 {
                padding: 1rem !important;
            }
            
            .text-center {
                margin-bottom: 1.5rem !important;
            }
        }
        
        /* Improve scrolling on mobile */
        body {
            -webkit-overflow-scrolling: touch;
        }
        
        /* Better touch targets */
        .touch-button {
            min-height: 44px;
        }
        
        @media (max-width: 768px) {
            .touch-button {
                min-height: 48px;
            }
        }
        
        /* Comprehensive mobile grid overrides */
        @media (max-width: 768px) {
            /* Override all responsive grid classes for mobile */
            [class*="grid-cols-"] {
                grid-template-columns: 1fr !important;
            }
            
            /* Specific overrides for question grids */
            .question-grid,
            .question-grid[class*="grid-cols-"],
            .mobile-question-grid {
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            
            /* Specific overrides for category name grids */
            .category-name-grid,
            .category-name-grid[class*="grid-cols-"] {
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            
            /* Override main form grids */
            .grid[class*="md:grid-cols-"] {
                grid-template-columns: 1fr !important;
            }
            
            /* Force single column for all question items */
            .question-item {
                width: 100% !important;
                margin-bottom: 0.5rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 p-2 sm:p-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <h1 class="text-xl sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
                🎯 Jeopardy 
            </h1>
            <a href="/jeopardy" class="bg-gray-600 text-white font-bold py-1 sm:py-2 px-2 sm:px-4 rounded-lg transition-colors text-sm sm:text-base touch-button">
                ← Back to Menu
            </a>
        </div>
    </header>

    <!-- Custom Game Creator Screen -->
    <div class="min-h-screen flex items-center justify-center p-2 sm:p-4">
        <div class="max-w-4xl w-full">
            <div class="bg-gray-800 rounded-2xl p-4 sm:p-8 shadow-2xl">
                                                    <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-4">
                        🎯 Custom Game Creator
                    </h2>
                    <p class="text-gray-300 text-sm sm:text-base">Create your own personalized Jeopardy game with custom categories, questions, and settings</p>
                    <p class="text-gray-400 text-xs sm:text-sm mt-2">Maximum 7 players per game</p>
                    @if(request()->has('lobby'))
                    <div class="mt-4 p-3 sm:p-4 bg-blue-600 bg-opacity-20 border border-blue-500 rounded-lg">
                        <p class="text-blue-300 text-xs sm:text-sm">🎮 <strong>Lobby Game:</strong> This custom game will be shared with all players in your lobby</p>
                    </div>
                    @elseif(request()->has('mode') && request()->get('mode') === 'singleplayer')
                    <div class="mt-4 p-3 sm:p-4 bg-green-600 bg-opacity-20 border border-green-500 rounded-lg">
                        <p class="text-green-300 text-xs sm:text-sm">🎯 <strong>Single Player Mode:</strong> Create your own custom game to play solo</p>
                    </div>
                    @endif
                    <button onclick="fillSampleData()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors touch-button text-sm sm:text-base">
                        Fill Sample Data (works with any category count)
                    </button>
                </div>
                
                <form id="customGameForm" class="space-y-6">
                    <!-- Game Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        <!-- Number of Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Number of Categories</label>
                            <select id="categoryCount" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-select touch-input" onchange="updateCategoryInputs()">
                                <option value="1">1 Category</option>
                                <option value="2">2 Categories</option>
                                <option value="3">3 Categories</option>
                                <option value="4">4 Categories</option>
                                <option value="5" selected>5 Categories</option>
                                <option value="6">6 Categories</option>
                                <option value="7">7 Categories</option>
                                <option value="8">8 Categories</option>
                            </select>
                        </div>
                        
                        <!-- Game Timer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Game Timer (minutes)</label>
                            <input type="number" id="gameTimer" min="1" max="60" value="5" 
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-input touch-input">
                        </div>
                        
                        <!-- Question Timer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Question Timer (seconds)</label>
                            <input type="number" id="questionTimer" min="10" max="120" value="30" 
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-input touch-input">
                        </div>
                    </div>
                    
                    <!-- Team Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <!-- Number of Questions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Number of Questions per Category</label>
                            <select id="questionCount" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-select touch-input" onchange="updateCategoryInputs()">
                                <option value="3">3 Questions</option>
                                <option value="4">4 Questions</option>
                                <option value="5" selected>5 Questions</option>
                                <option value="6">6 Questions</option>
                                <option value="7">7 Questions</option>
                                <option value="8">8 Questions</option>
                                <option value="9">9 Questions</option>
                                <option value="10">10 Questions</option>
                                <option value="11">11 Questions</option>
                                <option value="12">12 Questions</option>
                                <option value="13">13 Questions</option>
                                <option value="14">14 Questions</option>
                                <option value="15">15 Questions</option>
                                <option value="16">16 Questions</option>
                                <option value="17">17 Questions</option>
                                <option value="18">18 Questions</option>
                                <option value="19">19 Questions</option>
                                <option value="20">20 Questions</option>
                                <option value="25">25 Questions</option>
                                <option value="30">30 Questions</option>
                                <option value="35">35 Questions</option>
                                <option value="40">40 Questions</option>
                                <option value="45">45 Questions</option>
                                <option value="50">50 Questions</option>
                                <option value="55">55 Questions</option>
                                <option value="60">60 Questions</option>
                            </select>
                        </div>
                    </div>
                    

                    
                    <!-- Single Player Mode Indicator -->
                    <div id="singlePlayerIndicator" class="mt-6 hidden">
                        <div class="p-3 sm:p-4 bg-blue-600 bg-opacity-20 border border-blue-500 rounded-lg">
                            <p class="text-blue-300 text-xs sm:text-sm">🎮 <strong>Single Player Mode:</strong> You'll play against yourself to test your knowledge!</p>
                        </div>
                    </div>
                    
                    <!-- Categories and Questions -->
                    <div id="categoriesContainer" class="space-y-6">
                        <!-- Category inputs will be generated here -->
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <a href="/jeopardy" 
                           class="flex-1 bg-gray-600 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors text-center back-btn touch-button">
                            Back to Menu
                        </a>
                        <button type="submit" id="submitBtn"
                                class="flex-1 bg-gradient-to-r from-green-600 to-blue-600 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-all duration-300 submit-btn touch-button">
                            <span id="submitText">Create & Start Game</span>
                            <span id="submitLoading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Game...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize the custom game form
        document.addEventListener('DOMContentLoaded', function() {
            // Clear any old game state first
            clearOldGameState();
            
            updateCategoryInputs();
            
            // Force mobile layout
            forceMobileLayout();
            
            // Add form submit handler
            document.getElementById('customGameForm').addEventListener('submit', function(e) {
                e.preventDefault();
                startCustomGame();
            });
            
            // Add resize listener to maintain mobile layout
            window.addEventListener('resize', function() {
                forceMobileLayout();
            });
        });
        
        function clearOldGameState() {
            // Clear any old session storage data
            const keysToClear = ['playerId', 'playerTeam', 'lobby_players'];
            keysToClear.forEach(key => {
                if (sessionStorage.getItem(key)) {
                    sessionStorage.removeItem(key);
                    console.log('Cleared old session data:', key);
                }
            });
        }
        
        function forceMobileLayout() {
            if (window.innerWidth <= 768) {
                // Force all grids to single column on mobile
                const grids = document.querySelectorAll('.grid');
                grids.forEach(grid => {
                    grid.style.gridTemplateColumns = '1fr';
                });
                
                // Force question grids to single column
                const questionGrids = document.querySelectorAll('.question-grid, .mobile-question-grid');
                questionGrids.forEach(grid => {
                    grid.style.gridTemplateColumns = '1fr';
                    grid.style.gap = '0.5rem';
                });
                
                // Force category name grids to single column
                const categoryGrids = document.querySelectorAll('.category-name-grid');
                categoryGrids.forEach(grid => {
                    grid.style.gridTemplateColumns = '1fr';
                    grid.style.gap = '0.5rem';
                });
            }
        }

        function updateCategoryInputs() {
            const categoryCount = parseInt(document.getElementById('categoryCount').value);
            const questionCount = parseInt(document.getElementById('questionCount').value);
            const container = document.getElementById('categoriesContainer');
            container.innerHTML = '';
            
            for (let i = 1; i <= categoryCount; i++) {
                const categoryDiv = document.createElement('div');
                categoryDiv.className = 'bg-gray-700 rounded-lg p-4 sm:p-6 category-card';
                
                // Generate question fields dynamically based on question count
                let questionFieldsHTML = '';
                
                // Use responsive grid layout for questions
                questionFieldsHTML = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 question-grid mobile-question-grid" style="grid-template-columns: 1fr;">`;
                
                for (let j = 1; j <= questionCount; j++) {
                    const pointText = j === 1 ? '1 point' : `${j} points`;
                    
                    questionFieldsHTML += `
                        <div class="question-item">
                            <label class="block text-sm font-medium text-gray-300 mb-1">${pointText} Question</label>
                            <input type="text" id="category${i}Q${j}" required 
                                   class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm form-input touch-input"
                                   placeholder="Question">
                            <input type="text" id="category${i}A${j}" required 
                                   class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm mt-1 form-input touch-input"
                                   placeholder="Answer">
                        </div>
                    `;
                }
                
                questionFieldsHTML += '</div>';
                
                categoryDiv.innerHTML = `
                    <h3 class="text-lg font-bold text-white mb-4">Category ${i}</h3>
                    <div class="grid grid-cols-1 gap-4 mb-4 category-name-grid" style="grid-template-columns: 1fr;">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Category Name</label>
                            <input type="text" id="category${i}Name" required 
                                   class="w-full px-4 py-3 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-input touch-input"
                                   placeholder="Enter category name">
                        </div>
                    </div>
                    <div class="space-y-3">
                        ${questionFieldsHTML}
                    </div>
                `;
                container.appendChild(categoryDiv);
            }
            
            // Force mobile layout after updating categories
            setTimeout(forceMobileLayout, 100);
        }





        function fillSampleData() {
            console.log('fillSampleData called');
            
            // Get current category count
            const categoryCount = parseInt(document.getElementById('categoryCount').value);
            console.log('Category count:', categoryCount);
            
            updateCategoryInputs();
            
            // Wait a moment for the DOM to update, then fill category data
            setTimeout(() => {
                console.log('Starting to fill sample data...');
                // Sample category names for different counts
                const categoryNames = {
                    1: ['General Knowledge'],
                    2: ['Science & Nature', 'Entertainment & Pop Culture'],
                    3: ['Subject-Verb Agreement', 'Tenses of Verbs', 'Correct Usage of Pronouns'],
                    4: ['Science & Nature', 'Entertainment & Pop Culture', 'Technology & Innovation', 'History & Politics'],
                    5: ['Science & Nature', 'Entertainment & Pop Culture', 'Technology & Innovation', 'History & Politics', 'Sports & Games'],
                    6: ['Science & Nature', 'Entertainment & Pop Culture', 'Technology & Innovation', 'History & Politics', 'Sports & Games', 'Geography & Travel'],
                    7: ['Science & Nature', 'Entertainment & Pop Culture', 'Technology & Innovation', 'History & Politics', 'Sports & Games', 'Geography & Travel', 'Food & Cooking'],
                    8: ['Science & Nature', 'Entertainment & Pop Culture', 'Technology & Innovation', 'History & Politics', 'Sports & Games', 'Geography & Travel', 'Food & Cooking', 'Literature & Arts']
                };
                
                // Fill category names based on count
                const names = categoryNames[categoryCount] || categoryNames[3];
                for (let i = 1; i <= categoryCount; i++) {
                    const nameElement = document.getElementById(`category${i}Name`);
                    if (nameElement) {
                        nameElement.value = names[i - 1];
                    }
                }
                
                // Fill questions for each category
                const sampleData = {
                    'category1': { // General Knowledge (for 1 category)
                        'Q1': 'What is the capital of France?', 'A1': 'Paris',
                        'Q2': 'How many sides does a triangle have?', 'A2': '3',
                        'Q3': 'What is the largest planet in our solar system?', 'A3': 'Jupiter',
                        'Q4': 'What year did World War II end?', 'A4': '1945',
                        'Q5': 'What is the chemical symbol for gold?', 'A5': 'Au'
                    },
                    'category1_2cat': { // Science & Nature (for 2 categories)
                        'Q1': 'What is the hardest natural substance on Earth?', 'A1': 'Diamond',
                        'Q2': 'What gas do plants absorb from the air?', 'A2': 'Carbon dioxide',
                        'Q3': 'What is the largest organ in the human body?', 'A3': 'Skin',
                        'Q4': 'What is the study of fossils called?', 'A4': 'Paleontology',
                        'Q5': 'What is the closest star to Earth?', 'A5': 'Sun'
                    },
                    'category2_2cat': { // Entertainment & Pop Culture (for 2 categories)
                        'Q1': 'Who played Iron Man in the Marvel movies?', 'A1': 'Robert Downey Jr.',
                        'Q2': 'What year did the first iPhone come out?', 'A2': '2007',
                        'Q3': 'Who wrote "Romeo and Juliet"?', 'A3': 'William Shakespeare',
                        'Q4': 'What is the name of the famous painting by Leonardo da Vinci?', 'A4': 'Mona Lisa',
                        'Q5': 'Who is known as the "King of Pop"?', 'A5': 'Michael Jackson'
                    },
                    'category1_3cat': { // Subject-Verb Agreement 
                        'Q1': 'A car and a bike is / are my means of transportation.', 'A1': 'are',
                        'Q2': 'Each student was / were given a test paper.', 'A2': 'was',
                        'Q3': 'Neither the students nor the teacher know / knows the correct answer.', 'A3': 'knows',
                        'Q4': 'Each of the players have / has submitted their final lineup.', 'A4': 'has',
                        'Q5': 'A. The bouquet of flowers are beautiful. B. The bouquet of flowers is beautiful.', 'A5': 'B'
                    },
                    'category2_3cat': { // Tenses of Verbs
                        'Q1': 'She go / goes to the gym every morning.', 'A1': 'goes',
                        'Q2': 'I was watching / watched a documentary when the lights went out.', 'A2': 'was watching',
                        'Q3': 'They have been worked / have been working on this case for weeks.', 'A3': 'have been working',
                        'Q4': 'By the time you arrive, she will have left / will leave the office.', 'A4': 'will have left',
                        'Q5': 'Which One is Right? A. She said she would come if she finished her task early. B. She said she will come if she finishes her task early.', 'A5': 'A'
                    },
                    'category3_3cat': { // Correct Usage of Pronouns
                        'Q1': 'Maria and me / I went to the library after class.', 'A1': 'I',
                        'Q2': 'Each of the students must submit his or her / their project on time.', 'A2': 'his or her',
                        'Q3': 'Neither James nor the others brought his / their identification cards.', 'A3': 'their',
                        'Q4': 'It was she / her who submitted the winning entry.', 'A4': 'she',
                        'Q5': 'A. Who do you think they chose as the new class representative? B. Who do you think them chose as the new class representative?', 'A5': 'A'
                    },
                    'category1_4cat': { // Science & Nature (for 4+ categories)
                        'Q1': 'What is the hardest natural substance on Earth?', 'A1': 'Diamond',
                        'Q2': 'What gas do plants absorb from the air?', 'A2': 'Carbon dioxide',
                        'Q3': 'What is the largest organ in the human body?', 'A3': 'Skin',
                        'Q4': 'What is the study of fossils called?', 'A4': 'Paleontology',
                        'Q5': 'What is the closest star to Earth?', 'A5': 'Sun'
                    },
                    'category2_4cat': { // Entertainment & Pop Culture (for 4+ categories)
                        'Q1': 'Who played Iron Man in the Marvel movies?', 'A1': 'Robert Downey Jr.',
                        'Q2': 'What year did the first iPhone come out?', 'A2': '2007',
                        'Q3': 'Who wrote "Romeo and Juliet"?', 'A3': 'William Shakespeare',
                        'Q4': 'What is the name of the famous painting by Leonardo da Vinci?', 'A4': 'Mona Lisa',
                        'Q5': 'Who is known as the "King of Pop"?', 'A5': 'Michael Jackson'
                    },
                    'category2': { // Entertainment & Pop Culture (for 5+ categories)
                        'Q1': 'Who played Iron Man in the Marvel movies?', 'A1': 'Robert Downey Jr.',
                        'Q2': 'What year did the first iPhone come out?', 'A2': '2007',
                        'Q3': 'Who wrote "Romeo and Juliet"?', 'A3': 'William Shakespeare',
                        'Q4': 'What is the name of the famous painting by Leonardo da Vinci?', 'A4': 'Mona Lisa',
                        'Q5': 'Who is known as the "King of Pop"?', 'A5': 'Michael Jackson',
                        'Q6': 'Who played Captain America in the Marvel movies?', 'A6': 'Chris Evans',
                        'Q7': 'What year did Facebook launch?', 'A7': '2004',
                        'Q8': 'Who wrote "Hamlet"?', 'A8': 'William Shakespeare',
                        'Q9': 'Who painted "The Starry Night"?', 'A9': 'Vincent van Gogh',
                        'Q10': 'Who is known as the "Queen of Pop"?', 'A10': 'Madonna',
                        'Q11': 'Who played Spider-Man in the Marvel movies?', 'A11': 'Tom Holland',
                        'Q12': 'What year did YouTube launch?', 'A12': '2005',
                        'Q13': 'Who wrote "Macbeth"?', 'A13': 'William Shakespeare',
                        'Q14': 'Who painted "The Scream"?', 'A14': 'Edvard Munch',
                        'Q15': 'Who is known as the "King of Rock"?', 'A15': 'Elvis Presley',
                        'Q16': 'Who played Black Widow in the Marvel movies?', 'A16': 'Scarlett Johansson',
                        'Q17': 'What year did Twitter launch?', 'A17': '2006',
                        'Q18': 'Who wrote "Othello"?', 'A18': 'William Shakespeare',
                        'Q19': 'Who painted "The Last Supper"?', 'A19': 'Leonardo da Vinci',
                        'Q20': 'Who is known as the "Queen of Soul"?', 'A20': 'Aretha Franklin',
                        'Q21': 'Who played Thor in the Marvel movies?', 'A21': 'Chris Hemsworth',
                        'Q22': 'What year did Instagram launch?', 'A22': '2010',
                        'Q23': 'Who wrote "King Lear"?', 'A23': 'William Shakespeare',
                        'Q24': 'Who painted "Guernica"?', 'A24': 'Pablo Picasso',
                        'Q25': 'Who is known as the "King of Blues"?', 'A25': 'B.B. King',
                        'Q26': 'Who played Hulk in the Marvel movies?', 'A26': 'Mark Ruffalo',
                        'Q27': 'What year did Snapchat launch?', 'A27': '2011',
                        'Q28': 'Who wrote "The Tempest"?', 'A28': 'William Shakespeare',
                        'Q29': 'Who painted "The Persistence of Memory"?', 'A29': 'Salvador Dalí',
                        'Q30': 'Who is known as the "Queen of Country"?', 'A30': 'Dolly Parton',
                        'Q31': 'Who played Black Panther in the Marvel movies?', 'A31': 'Chadwick Boseman',
                        'Q32': 'What year did TikTok launch?', 'A32': '2016',
                        'Q33': 'Who wrote "A Midsummer Night\'s Dream"?', 'A33': 'William Shakespeare',
                        'Q34': 'Who painted "The Birth of Venus"?', 'A34': 'Sandro Botticelli',
                        'Q35': 'Who is known as the "King of Jazz"?', 'A35': 'Louis Armstrong',
                        'Q36': 'Who played Doctor Strange in the Marvel movies?', 'A36': 'Benedict Cumberbatch',
                        'Q37': 'What year did WhatsApp launch?', 'A37': '2009',
                        'Q38': 'Who wrote "Much Ado About Nothing"?', 'A38': 'William Shakespeare',
                        'Q39': 'Who painted "The Night Watch"?', 'A39': 'Rembrandt',
                        'Q40': 'Who is known as the "Queen of Jazz"?', 'A40': 'Ella Fitzgerald',
                        'Q41': 'Who played Ant-Man in the Marvel movies?', 'A41': 'Paul Rudd',
                        'Q42': 'What year did LinkedIn launch?', 'A42': '2003',
                        'Q43': 'Who wrote "Twelfth Night"?', 'A43': 'William Shakespeare',
                        'Q44': 'Who painted "The Creation of Adam"?', 'A44': 'Michelangelo',
                        'Q45': 'Who is known as the "King of Reggae"?', 'A45': 'Bob Marley',
                        'Q46': 'Who played Captain Marvel in the Marvel movies?', 'A46': 'Brie Larson',
                        'Q47': 'What year did Reddit launch?', 'A47': '2005',
                        'Q48': 'Who wrote "The Merchant of Venice"?', 'A48': 'William Shakespeare',
                        'Q49': 'Who painted "The School of Athens"?', 'A49': 'Raphael',
                        'Q50': 'Who is known as the "Queen of Rock"?', 'A50': 'Janis Joplin',
                        'Q51': 'Who played Scarlet Witch in the Marvel movies?', 'A51': 'Elizabeth Olsen',
                        'Q52': 'What year did Pinterest launch?', 'A52': '2010',
                        'Q53': 'Who wrote "As You Like It"?', 'A53': 'William Shakespeare',
                        'Q54': 'Who painted "The Sistine Chapel ceiling"?', 'A54': 'Michelangelo',
                        'Q55': 'Who is known as the "King of Hip Hop"?', 'A55': 'Tupac Shakur',
                        'Q56': 'Who played Vision in the Marvel movies?', 'A56': 'Paul Bettany',
                        'Q57': 'What year did Tumblr launch?', 'A57': '2007',
                        'Q58': 'Who wrote "The Taming of the Shrew"?', 'A58': 'William Shakespeare',
                        'Q59': 'Who painted "The Garden of Earthly Delights"?', 'A59': 'Hieronymus Bosch',
                        'Q60': 'Who is known as the "Queen of Hip Hop"?', 'A60': 'Queen Latifah'
                    },
                    'category3_4cat': { // Technology & Innovation (for 4+ categories)
                        'Q1': 'What does CPU stand for?', 'A1': 'Central Processing Unit',
                        'Q2': 'Who founded Microsoft?', 'A2': 'Bill Gates',
                        'Q3': 'What year was the first iPhone released?', 'A3': '2007',
                        'Q4': 'What does HTML stand for?', 'A4': 'HyperText Markup Language',
                        'Q5': 'What is the name of Google\'s mobile operating system?', 'A5': 'Android'
                    },
                    'category3': { // Technology & Innovation (for 5+ categories)
                        'Q1': 'What does CPU stand for?', 'A1': 'Central Processing Unit',
                        'Q2': 'Who founded Microsoft?', 'A2': 'Bill Gates',
                        'Q3': 'What year was the first iPhone released?', 'A3': '2007',
                        'Q4': 'What does HTML stand for?', 'A4': 'HyperText Markup Language',
                        'Q5': 'What is the name of Google\'s mobile operating system?', 'A5': 'Android',
                        'Q6': 'What does RAM stand for?', 'A6': 'Random Access Memory',
                        'Q7': 'Who founded Apple Inc.?', 'A7': 'Steve Jobs',
                        'Q8': 'What does URL stand for?', 'A8': 'Uniform Resource Locator',
                        'Q9': 'What year was the World Wide Web invented?', 'A9': '1989',
                        'Q10': 'What does USB stand for?', 'A10': 'Universal Serial Bus',
                        'Q11': 'Who created Facebook?', 'A11': 'Mark Zuckerberg',
                        'Q12': 'What does VPN stand for?', 'A12': 'Virtual Private Network',
                        'Q13': 'What year was the first computer mouse invented?', 'A13': '1964',
                        'Q14': 'What does AI stand for?', 'A14': 'Artificial Intelligence',
                        'Q15': 'Who invented the telephone?', 'A15': 'Alexander Graham Bell',
                        'Q16': 'What does SSD stand for?', 'A16': 'Solid State Drive',
                        'Q17': 'Who created Twitter?', 'A17': 'Jack Dorsey',
                        'Q18': 'What does API stand for?', 'A18': 'Application Programming Interface',
                        'Q19': 'What year was the first iPhone released?', 'A19': '2007',
                        'Q20': 'What does DNS stand for?', 'A20': 'Domain Name System',
                        'Q21': 'Who created Instagram?', 'A21': 'Kevin Systrom',
                        'Q22': 'What does HTTP stand for?', 'A22': 'HyperText Transfer Protocol',
                        'Q23': 'What year was YouTube founded?', 'A23': '2005',
                        'Q24': 'What does SSL stand for?', 'A24': 'Secure Sockets Layer',
                        'Q25': 'Who created Snapchat?', 'A25': 'Evan Spiegel',
                        'Q26': 'What does FTP stand for?', 'A26': 'File Transfer Protocol',
                        'Q27': 'What year was the first computer built?', 'A27': '1946',
                        'Q28': 'What does IP stand for?', 'A28': 'Internet Protocol',
                        'Q29': 'Who created LinkedIn?', 'A29': 'Reid Hoffman',
                        'Q30': 'What does CSS stand for?', 'A30': 'Cascading Style Sheets',
                        'Q31': 'What year was the internet invented?', 'A31': '1969',
                        'Q32': 'What does JSON stand for?', 'A32': 'JavaScript Object Notation',
                        'Q33': 'Who created WhatsApp?', 'A33': 'Jan Koum',
                        'Q34': 'What does SQL stand for?', 'A34': 'Structured Query Language',
                        'Q35': 'What year was the first email sent?', 'A35': '1971',
                        'Q36': 'What does XML stand for?', 'A36': 'eXtensible Markup Language',
                        'Q37': 'Who created TikTok?', 'A37': 'Zhang Yiming',
                        'Q38': 'What does PHP stand for?', 'A38': 'PHP: Hypertext Preprocessor',
                        'Q39': 'What year was the first website created?', 'A39': '1991',
                        'Q40': 'What does RSS stand for?', 'A40': 'Really Simple Syndication',
                        'Q41': 'Who created Reddit?', 'A41': 'Steve Huffman',
                        'Q42': 'What does IDE stand for?', 'A42': 'Integrated Development Environment',
                        'Q43': 'What year was the first smartphone released?', 'A43': '1994',
                        'Q44': 'What does GUI stand for?', 'A44': 'Graphical User Interface',
                        'Q45': 'Who created Pinterest?', 'A45': 'Ben Silbermann',
                        'Q46': 'What does CLI stand for?', 'A46': 'Command Line Interface',
                        'Q47': 'What year was the first laptop computer released?', 'A47': '1981',
                        'Q48': 'What does SDK stand for?', 'A48': 'Software Development Kit',
                        'Q49': 'Who created Tumblr?', 'A49': 'David Karp',
                        'Q50': 'What does API stand for?', 'A50': 'Application Programming Interface',
                        'Q51': 'What year was the first tablet computer released?', 'A51': '1989',
                        'Q52': 'What does MVC stand for?', 'A52': 'Model-View-Controller',
                        'Q53': 'Who created Discord?', 'A53': 'Jason Citron',
                        'Q54': 'What does CRUD stand for?', 'A54': 'Create, Read, Update, Delete',
                        'Q55': 'What year was the first video game created?', 'A55': '1958',
                        'Q56': 'What does OOP stand for?', 'A56': 'Object-Oriented Programming',
                        'Q57': 'Who created Slack?', 'A57': 'Stewart Butterfield',
                        'Q58': 'What does API stand for?', 'A58': 'Application Programming Interface',
                        'Q59': 'What year was the first computer virus created?', 'A59': '1986',
                        'Q60': 'What does IoT stand for?', 'A60': 'Internet of Things'
                    },
                    'category4_4cat': { // History & Politics (for 4+ categories)
                        'Q1': 'In what year did World War II end?', 'A1': '1945',
                        'Q2': 'Who was the first President of the United States?', 'A2': 'George Washington',
                        'Q3': 'What ancient wonder was located in Alexandria?', 'A3': 'Lighthouse',
                        'Q4': 'What year did the Berlin Wall fall?', 'A4': '1989',
                        'Q5': 'Who was the first female Prime Minister of the UK?', 'A5': 'Margaret Thatcher'
                    },
                    'category4': { // History & Politics (for 5+ categories)
                        'Q1': 'In what year did World War II end?', 'A1': '1945',
                        'Q2': 'Who was the first President of the United States?', 'A2': 'George Washington',
                        'Q3': 'What ancient wonder was located in Alexandria?', 'A3': 'Lighthouse',
                        'Q4': 'What year did the Berlin Wall fall?', 'A4': '1989',
                        'Q5': 'Who was the first female Prime Minister of the UK?', 'A5': 'Margaret Thatcher',
                        'Q6': 'What year did the American Civil War end?', 'A6': '1865',
                        'Q7': 'Who was the first Emperor of Rome?', 'A7': 'Augustus',
                        'Q8': 'What year did the French Revolution begin?', 'A8': '1789',
                        'Q9': 'Who was the first female astronaut?', 'A9': 'Valentina Tereshkova',
                        'Q10': 'What year did the Berlin Wall fall?', 'A10': '1989',
                        'Q11': 'Who was the first President of South Africa?', 'A11': 'Nelson Mandela',
                        'Q12': 'What year did the Cold War end?', 'A12': '1991',
                        'Q13': 'Who was the first female Prime Minister of India?', 'A13': 'Indira Gandhi',
                        'Q14': 'What year did the Russian Revolution occur?', 'A14': '1917',
                        'Q15': 'Who was the first female Supreme Court Justice?', 'A15': 'Sandra Day O\'Connor',
                        'Q16': 'What year did World War I end?', 'A16': '1918',
                        'Q17': 'Who was the first President of the United States?', 'A17': 'George Washington',
                        'Q18': 'What year did the Declaration of Independence get signed?', 'A18': '1776',
                        'Q19': 'Who was the first female Prime Minister of the UK?', 'A19': 'Margaret Thatcher',
                        'Q20': 'What year did the American Revolution begin?', 'A20': '1775',
                        'Q21': 'Who was the first Emperor of China?', 'A21': 'Qin Shi Huang',
                        'Q22': 'What year did the Magna Carta get signed?', 'A22': '1215',
                        'Q23': 'Who was the first female President of a country?', 'A23': 'Vigdis Finnbogadottir',
                        'Q24': 'What year did the Industrial Revolution begin?', 'A24': '1760',
                        'Q25': 'Who was the first King of England?', 'A25': 'Athelstan',
                        'Q26': 'What year did the Spanish Civil War end?', 'A26': '1939',
                        'Q27': 'Who was the first female Nobel Prize winner?', 'A27': 'Marie Curie',
                        'Q28': 'What year did the Vietnam War end?', 'A28': '1975',
                        'Q29': 'Who was the first Emperor of Japan?', 'A29': 'Emperor Jimmu',
                        'Q30': 'What year did the Korean War end?', 'A30': '1953',
                        'Q31': 'Who was the first female astronaut from the US?', 'A31': 'Sally Ride',
                        'Q32': 'What year did the Cuban Missile Crisis occur?', 'A32': '1962',
                        'Q33': 'Who was the first President of France?', 'A33': 'Louis-Napoleon Bonaparte',
                        'Q34': 'What year did the Berlin Wall get built?', 'A34': '1961',
                        'Q35': 'Who was the first female Prime Minister of Canada?', 'A35': 'Kim Campbell',
                        'Q36': 'What year did the Gulf War end?', 'A36': '1991',
                        'Q37': 'Who was the first Emperor of Russia?', 'A37': 'Peter the Great',
                        'Q38': 'What year did the American Civil War begin?', 'A38': '1861',
                        'Q39': 'Who was the first female Prime Minister of Australia?', 'A39': 'Julia Gillard',
                        'Q40': 'What year did the Mexican Revolution begin?', 'A40': '1910',
                        'Q41': 'Who was the first President of Mexico?', 'A41': 'Guadalupe Victoria',
                        'Q42': 'What year did the Russian Revolution begin?', 'A42': '1917',
                        'Q43': 'Who was the first female Prime Minister of New Zealand?', 'A43': 'Jenny Shipley',
                        'Q44': 'What year did the Chinese Revolution occur?', 'A44': '1911',
                        'Q45': 'Who was the first Emperor of Brazil?', 'A45': 'Pedro I',
                        'Q46': 'What year did the Indian Independence movement begin?', 'A46': '1857',
                        'Q47': 'Who was the first female Prime Minister of Pakistan?', 'A47': 'Benazir Bhutto',
                        'Q48': 'What year did the Irish War of Independence end?', 'A48': '1921',
                        'Q49': 'Who was the first President of Ireland?', 'A49': 'Douglas Hyde',
                        'Q50': 'What year did the Spanish-American War end?', 'A50': '1898',
                        'Q51': 'Who was the first female Prime Minister of Israel?', 'A51': 'Golda Meir',
                        'Q52': 'What year did the Philippine Revolution begin?', 'A52': '1896',
                        'Q53': 'Who was the first President of the Philippines?', 'A53': 'Emilio Aguinaldo',
                        'Q54': 'What year did the Korean War begin?', 'A54': '1950',
                        'Q55': 'Who was the first female Prime Minister of Sri Lanka?', 'A55': 'Sirimavo Bandaranaike',
                        'Q56': 'What year did the Vietnam War begin?', 'A56': '1955',
                        'Q57': 'Who was the first President of Vietnam?', 'A57': 'Ho Chi Minh',
                        'Q58': 'What year did the Cambodian Civil War end?', 'A58': '1975',
                        'Q59': 'Who was the first female Prime Minister of Bangladesh?', 'A59': 'Sheikh Hasina',
                        'Q60': 'What year did the Bangladesh Liberation War end?', 'A60': '1971'
                    },
                    'category5': { // Sports & Games
                        'Q1': 'What sport is known as "the beautiful game"?', 'A1': 'Soccer',
                        'Q2': 'How many players are on a basketball court at once?', 'A2': '10',
                        'Q3': 'What is the national sport of Japan?', 'A3': 'Sumo Wrestling',
                        'Q4': 'In what year did the first modern Olympics take place?', 'A4': '1896',
                        'Q5': 'What is the fastest land animal?', 'A5': 'Cheetah'
                    },
                    'category6': { // Geography & Travel
                        'Q1': 'What is the capital of France?', 'A1': 'Paris',
                        'Q2': 'What is the largest ocean on Earth?', 'A2': 'Pacific Ocean',
                        'Q3': 'What is the longest river in the world?', 'A3': 'Nile',
                        'Q4': 'What is the smallest country in the world?', 'A4': 'Vatican City',
                        'Q5': 'What mountain range runs through South America?', 'A5': 'Andes',
                        'Q6': 'What is the capital of Australia?', 'A6': 'Canberra',
                        'Q7': 'What is the largest country in South America?', 'A7': 'Brazil',
                        'Q8': 'What is the capital of Canada?', 'A8': 'Ottawa',
                        'Q9': 'What is the largest island in the world?', 'A9': 'Greenland',
                        'Q10': 'What is the capital of China?', 'A10': 'Beijing',
                        'Q11': 'What is the largest desert in Asia?', 'A11': 'Gobi Desert',
                        'Q12': 'What is the capital of India?', 'A12': 'New Delhi',
                        'Q13': 'What is the largest lake in Africa?', 'A13': 'Lake Victoria',
                        'Q14': 'What is the capital of Russia?', 'A14': 'Moscow',
                        'Q15': 'What is the largest mountain range in Europe?', 'A15': 'Alps',
                        'Q16': 'What is the capital of South Africa?', 'A16': 'Pretoria',
                        'Q17': 'What is the largest river in Africa?', 'A17': 'Nile',
                        'Q18': 'What is the capital of Argentina?', 'A18': 'Buenos Aires',
                        'Q19': 'What is the largest desert in Africa?', 'A19': 'Sahara',
                        'Q20': 'What is the capital of Brazil?', 'A20': 'Brasília',
                        'Q21': 'What is the largest lake in North America?', 'A21': 'Lake Superior',
                        'Q22': 'What is the capital of Mexico?', 'A22': 'Mexico City',
                        'Q23': 'What is the largest river in South America?', 'A23': 'Amazon',
                        'Q24': 'What is the capital of Chile?', 'A24': 'Santiago',
                        'Q25': 'What is the largest mountain range in South America?', 'A25': 'Andes',
                        'Q26': 'What is the capital of Peru?', 'A26': 'Lima',
                        'Q27': 'What is the largest lake in South America?', 'A27': 'Lake Titicaca',
                        'Q28': 'What is the capital of Colombia?', 'A28': 'Bogotá',
                        'Q29': 'What is the largest river in Asia?', 'A29': 'Yangtze',
                        'Q30': 'What is the capital of Venezuela?', 'A30': 'Caracas',
                        'Q31': 'What is the largest lake in Asia?', 'A31': 'Caspian Sea',
                        'Q32': 'What is the capital of Ecuador?', 'A32': 'Quito',
                        'Q33': 'What is the largest mountain range in Asia?', 'A33': 'Himalayas',
                        'Q34': 'What is the capital of Bolivia?', 'A34': 'La Paz',
                        'Q35': 'What is the largest desert in Australia?', 'A35': 'Great Victoria Desert',
                        'Q36': 'What is the capital of Paraguay?', 'A36': 'Asunción',
                        'Q37': 'What is the largest lake in Australia?', 'A37': 'Lake Eyre',
                        'Q38': 'What is the capital of Uruguay?', 'A38': 'Montevideo',
                        'Q39': 'What is the largest river in Australia?', 'A39': 'Murray',
                        'Q40': 'What is the capital of Guyana?', 'A40': 'Georgetown',
                        'Q41': 'What is the largest mountain range in Australia?', 'A41': 'Great Dividing Range',
                        'Q42': 'What is the capital of Suriname?', 'A42': 'Paramaribo',
                        'Q43': 'What is the largest lake in Europe?', 'A43': 'Ladoga',
                        'Q44': 'What is the capital of French Guiana?', 'A44': 'Cayenne',
                        'Q45': 'What is the largest river in Europe?', 'A45': 'Volga',
                        'Q46': 'What is the capital of Falkland Islands?', 'A46': 'Stanley',
                        'Q47': 'What is the largest mountain range in North America?', 'A47': 'Rocky Mountains',
                        'Q48': 'What is the capital of Greenland?', 'A48': 'Nuuk',
                        'Q49': 'What is the largest lake in the world?', 'A49': 'Caspian Sea',
                        'Q50': 'What is the capital of Iceland?', 'A50': 'Reykjavik',
                        'Q51': 'What is the largest river in the world?', 'A51': 'Nile',
                        'Q52': 'What is the capital of Norway?', 'A52': 'Oslo',
                        'Q53': 'What is the largest mountain in the world?', 'A53': 'Mount Everest',
                        'Q54': 'What is the capital of Sweden?', 'A54': 'Stockholm',
                        'Q55': 'What is the largest volcano in the world?', 'A55': 'Mauna Loa',
                        'Q56': 'What is the capital of Finland?', 'A56': 'Helsinki',
                        'Q57': 'What is the largest waterfall in the world?', 'A57': 'Angel Falls',
                        'Q58': 'What is the capital of Denmark?', 'A58': 'Copenhagen',
                        'Q59': 'What is the largest canyon in the world?', 'A59': 'Grand Canyon',
                        'Q60': 'What is the capital of Netherlands?', 'A60': 'Amsterdam'
                    },
                    'category7': { // Food & Cooking
                        'Q1': 'What is the main ingredient in guacamole?', 'A1': 'Avocado',
                        'Q2': 'What country is known for inventing pizza?', 'A2': 'Italy',
                        'Q3': 'What is the most consumed meat in the world?', 'A3': 'Pork',
                        'Q4': 'What is the national dish of Japan?', 'A4': 'Sushi',
                        'Q5': 'What is the world\'s most expensive spice?', 'A5': 'Saffron',
                        'Q6': 'What is the main ingredient in sushi rice?', 'A6': 'Rice',
                        'Q7': 'What country is known for pasta?', 'A7': 'Italy',
                        'Q8': 'What is the main ingredient in hummus?', 'A8': 'Chickpeas',
                        'Q9': 'What country is known for tacos?', 'A9': 'Mexico',
                        'Q10': 'What is the main ingredient in curry?', 'A10': 'Spices',
                        'Q11': 'What country is known for kimchi?', 'A11': 'Korea',
                        'Q12': 'What is the main ingredient in falafel?', 'A12': 'Chickpeas',
                        'Q13': 'What country is known for paella?', 'A13': 'Spain',
                        'Q14': 'What is the main ingredient in tabbouleh?', 'A14': 'Bulgur wheat',
                        'Q15': 'What country is known for pad thai?', 'A15': 'Thailand',
                        'Q16': 'What is the main ingredient in baba ganoush?', 'A16': 'Eggplant',
                        'Q17': 'What country is known for croissants?', 'A17': 'France',
                        'Q18': 'What is the main ingredient in pesto?', 'A18': 'Basil',
                        'Q19': 'What country is known for bratwurst?', 'A19': 'Germany',
                        'Q20': 'What is the main ingredient in gazpacho?', 'A20': 'Tomatoes',
                        'Q21': 'What country is known for borscht?', 'A21': 'Russia',
                        'Q22': 'What is the main ingredient in ratatouille?', 'A22': 'Eggplant',
                        'Q23': 'What country is known for moussaka?', 'A23': 'Greece',
                        'Q24': 'What is the main ingredient in bouillabaisse?', 'A24': 'Fish',
                        'Q25': 'What country is known for goulash?', 'A25': 'Hungary',
                        'Q26': 'What is the main ingredient in risotto?', 'A26': 'Rice',
                        'Q27': 'What country is known for schnitzel?', 'A27': 'Austria',
                        'Q28': 'What is the main ingredient in fondue?', 'A28': 'Cheese',
                        'Q29': 'What country is known for fondue?', 'A29': 'Switzerland',
                        'Q30': 'What is the main ingredient in tapas?', 'A30': 'Various',
                        'Q31': 'What country is known for tapas?', 'A31': 'Spain',
                        'Q32': 'What is the main ingredient in ceviche?', 'A32': 'Fish',
                        'Q33': 'What country is known for ceviche?', 'A33': 'Peru',
                        'Q34': 'What is the main ingredient in empanadas?', 'A34': 'Dough',
                        'Q35': 'What country is known for empanadas?', 'A35': 'Argentina',
                        'Q36': 'What is the main ingredient in arepas?', 'A36': 'Cornmeal',
                        'Q37': 'What country is known for arepas?', 'A37': 'Colombia',
                        'Q38': 'What is the main ingredient in feijoada?', 'A38': 'Black beans',
                        'Q39': 'What country is known for feijoada?', 'A39': 'Brazil',
                        'Q40': 'What is the main ingredient in poutine?', 'A40': 'French fries',
                        'Q41': 'What country is known for poutine?', 'A41': 'Canada',
                        'Q42': 'What is the main ingredient in jerk chicken?', 'A42': 'Chicken',
                        'Q43': 'What country is known for jerk chicken?', 'A43': 'Jamaica',
                        'Q44': 'What is the main ingredient in conch fritters?', 'A44': 'Conch',
                        'Q45': 'What country is known for conch fritters?', 'A45': 'Bahamas',
                        'Q46': 'What is the main ingredient in ackee and saltfish?', 'A46': 'Ackee',
                        'Q47': 'What country is known for ackee and saltfish?', 'A47': 'Jamaica',
                        'Q48': 'What is the main ingredient in callaloo?', 'A48': 'Leafy greens',
                        'Q49': 'What country is known for callaloo?', 'A49': 'Trinidad and Tobago',
                        'Q50': 'What is the main ingredient in roti?', 'A50': 'Flour',
                        'Q51': 'What country is known for roti?', 'A51': 'India',
                        'Q52': 'What is the main ingredient in naan?', 'A52': 'Flour',
                        'Q53': 'What country is known for naan?', 'A53': 'India',
                        'Q54': 'What is the main ingredient in samosas?', 'A54': 'Pastry',
                        'Q55': 'What country is known for samosas?', 'A55': 'India',
                        'Q56': 'What is the main ingredient in biryani?', 'A56': 'Rice',
                        'Q57': 'What country is known for biryani?', 'A57': 'India',
                        'Q58': 'What is the main ingredient in tandoori chicken?', 'A58': 'Chicken',
                        'Q59': 'What country is known for tandoori chicken?', 'A59': 'India',
                        'Q60': 'What is the main ingredient in curry?', 'A60': 'Spices'
                    },
                    'category8': { // Literature & Arts
                        'Q1': 'Who wrote "Romeo and Juliet"?', 'A1': 'William Shakespeare',
                        'Q2': 'What is the name of the famous painting by Leonardo da Vinci?', 'A2': 'Mona Lisa',
                        'Q3': 'Who wrote "Pride and Prejudice"?', 'A3': 'Jane Austen',
                        'Q4': 'What is the name of the famous sculpture by Michelangelo?', 'A4': 'David',
                        'Q5': 'Who wrote "The Great Gatsby"?', 'A5': 'F. Scott Fitzgerald',
                        'Q6': 'Who wrote "To Kill a Mockingbird"?', 'A6': 'Harper Lee',
                        'Q7': 'What is the name of the famous painting by Edvard Munch?', 'A7': 'The Scream',
                        'Q8': 'Who wrote "1984"?', 'A8': 'George Orwell',
                        'Q9': 'What is the name of the famous sculpture by Auguste Rodin?', 'A9': 'The Thinker',
                        'Q10': 'Who wrote "The Catcher in the Rye"?', 'A10': 'J.D. Salinger',
                        'Q11': 'What is the name of the famous painting by Salvador Dali?', 'A11': 'The Persistence of Memory',
                        'Q12': 'Who wrote "Lord of the Flies"?', 'A12': 'William Golding',
                        'Q13': 'What is the name of the famous sculpture by Donatello?', 'A13': 'David',
                        'Q14': 'Who wrote "Animal Farm"?', 'A14': 'George Orwell',
                        'Q15': 'What is the name of the famous painting by Claude Monet?', 'A15': 'Water Lilies',
                        'Q16': 'What is the name of the famous painting by Vincent van Gogh?', 'A16': 'Starry Night',
                        'Q17': 'Who wrote "Jane Eyre"?', 'A17': 'Charlotte Brontë',
                        'Q18': 'What is the name of the famous painting by Pablo Picasso?', 'A18': 'Guernica',
                        'Q19': 'Who wrote "Wuthering Heights"?', 'A19': 'Emily Brontë',
                        'Q20': 'What is the name of the famous sculpture by Bernini?', 'A20': 'Apollo and Daphne',
                        'Q21': 'Who wrote "The Scarlet Letter"?', 'A21': 'Nathaniel Hawthorne',
                        'Q22': 'What is the name of the famous painting by Rembrandt?', 'A22': 'The Night Watch',
                        'Q23': 'Who wrote "Moby Dick"?', 'A23': 'Herman Melville',
                        'Q24': 'What is the name of the famous painting by Sandro Botticelli?', 'A24': 'The Birth of Venus',
                        'Q25': 'Who wrote "Uncle Tom\'s Cabin"?', 'A25': 'Harriet Beecher Stowe',
                        'Q26': 'What is the name of the famous sculpture by Canova?', 'A26': 'Psyche Revived by Cupid\'s Kiss',
                        'Q27': 'Who wrote "The Adventures of Tom Sawyer"?', 'A27': 'Mark Twain',
                        'Q28': 'What is the name of the famous painting by Hieronymus Bosch?', 'A28': 'The Garden of Earthly Delights',
                        'Q29': 'Who wrote "The Call of the Wild"?', 'A29': 'Jack London',
                        'Q30': 'What is the name of the famous painting by Jan van Eyck?', 'A30': 'The Arnolfini Portrait',
                        'Q31': 'Who wrote "The Jungle Book"?', 'A31': 'Rudyard Kipling',
                        'Q32': 'What is the name of the famous sculpture by Cellini?', 'A32': 'Perseus with the Head of Medusa',
                        'Q33': 'Who wrote "Alice\'s Adventures in Wonderland"?', 'A33': 'Lewis Carroll',
                        'Q34': 'What is the name of the famous painting by Pieter Bruegel?', 'A34': 'The Tower of Babel',
                        'Q35': 'Who wrote "Treasure Island"?', 'A35': 'Robert Louis Stevenson',
                        'Q36': 'What is the name of the famous painting by El Greco?', 'A36': 'The Burial of the Count of Orgaz',
                        'Q37': 'Who wrote "The Strange Case of Dr Jekyll and Mr Hyde"?', 'A37': 'Robert Louis Stevenson',
                        'Q38': 'What is the name of the famous sculpture by Verrocchio?', 'A38': 'David',
                        'Q39': 'Who wrote "The Picture of Dorian Gray"?', 'A39': 'Oscar Wilde',
                        'Q40': 'What is the name of the famous painting by Caravaggio?', 'A40': 'The Calling of Saint Matthew',
                        'Q41': 'Who wrote "Dracula"?', 'A41': 'Bram Stoker',
                        'Q42': 'What is the name of the famous painting by Velázquez?', 'A42': 'Las Meninas',
                        'Q43': 'Who wrote "The War of the Worlds"?', 'A43': 'H.G. Wells',
                        'Q44': 'What is the name of the famous sculpture by Ghiberti?', 'A44': 'Gates of Paradise',
                        'Q45': 'Who wrote "The Time Machine"?', 'A45': 'H.G. Wells',
                        'Q46': 'What is the name of the famous painting by Goya?', 'A46': 'The Third of May 1808',
                        'Q47': 'Who wrote "The Invisible Man"?', 'A47': 'H.G. Wells',
                        'Q48': 'What is the name of the famous painting by Delacroix?', 'A48': 'Liberty Leading the People',
                        'Q49': 'Who wrote "The Hound of the Baskervilles"?', 'A49': 'Arthur Conan Doyle',
                        'Q50': 'What is the name of the famous sculpture by Houdon?', 'A50': 'Voltaire',
                        'Q51': 'Who wrote "The Adventures of Sherlock Holmes"?', 'A51': 'Arthur Conan Doyle',
                        'Q52': 'What is the name of the famous painting by Ingres?', 'A52': 'The Grande Odalisque',
                        'Q53': 'Who wrote "The Sign of the Four"?', 'A53': 'Arthur Conan Doyle',
                        'Q54': 'What is the name of the famous painting by Courbet?', 'A54': 'The Origin of the World',
                        'Q55': 'Who wrote "A Study in Scarlet"?', 'A55': 'Arthur Conan Doyle',
                        'Q56': 'Who wrote "The Return of Sherlock Holmes"?', 'A56': 'Arthur Conan Doyle',
                        'Q57': 'What is the name of the famous painting by Manet?', 'A57': 'Olympia',
                        'Q58': 'Who wrote "The Memoirs of Sherlock Holmes"?', 'A58': 'Arthur Conan Doyle',
                        'Q59': 'What is the name of the famous painting by Degas?', 'A59': 'The Ballet Class',
                        'Q60': 'Who wrote "His Last Bow"?', 'A60': 'Arthur Conan Doyle'
                    }
                };
                
                // Fill the form with sample data
                const questionCount = parseInt(document.getElementById('questionCount').value);
                
                for (let i = 1; i <= categoryCount; i++) {
                    let categoryData;
                    
                    // Determine which sample data to use based on category count
                    if (categoryCount === 1) {
                        categoryData = sampleData['category1'];
                    } else if (categoryCount === 2) {
                        categoryData = sampleData[`category${i}_2cat`];
                    } else if (categoryCount === 3) {
                        categoryData = sampleData[`category${i}_3cat`];
                    } else if (categoryCount === 4) {
                        categoryData = sampleData[`category${i}_4cat`];
                    } else {
                        // For 5+ categories, use the existing category data
                        categoryData = sampleData[`category${i}`];
                    }
                    
                    if (categoryData) {
                        // Fill questions up to the selected question count (now supports up to 60)
                        for (let j = 1; j <= Math.min(questionCount, 60); j++) {
                            const questionElement = document.getElementById(`category${i}Q${j}`);
                            const answerElement = document.getElementById(`category${i}A${j}`);
                            if (questionElement && answerElement) {
                                // Use the sample data if available, otherwise generate additional questions
                                if (categoryData[`Q${j}`] && categoryData[`A${j}`]) {
                                    questionElement.value = categoryData[`Q${j}`];
                                    answerElement.value = categoryData[`A${j}`];
                                } else {
                                    // Generate additional sample questions based on category
                                    const additionalQuestions = generateAdditionalQuestions(i, j);
                                    questionElement.value = additionalQuestions.question;
                                    answerElement.value = additionalQuestions.answer;
                                }
                            }
                        }
                    } else {
                        // If no predefined data exists for this category, generate all questions
                        for (let j = 1; j <= Math.min(questionCount, 60); j++) {
                            const questionElement = document.getElementById(`category${i}Q${j}`);
                            const answerElement = document.getElementById(`category${i}A${j}`);
                            if (questionElement && answerElement) {
                                const additionalQuestions = generateAdditionalQuestions(i, j);
                                questionElement.value = additionalQuestions.question;
                                answerElement.value = additionalQuestions.answer;
                            }
                        }
                    }
                }
                
                            console.log('Sample data filled successfully!');
        }, 200); // Increased timeout to ensure DOM is ready
    }

    function generateAdditionalQuestions(categoryIndex, questionNumber) {
        // Additional sample questions for categories beyond the original 5
        const additionalQuestions = {
            1: { // Science & Nature
                6: { question: 'What is the largest mammal on Earth?', answer: 'Blue Whale' },
                7: { question: 'How many continents are there?', answer: '7' },
                8: { question: 'What is the main component of the sun?', answer: 'Hydrogen' },
                9: { question: 'What is the hardest natural substance?', answer: 'Diamond' },
                10: { question: 'What is the largest ocean?', answer: 'Pacific Ocean' },
                11: { question: 'What is the capital of Japan?', answer: 'Tokyo' },
                12: { question: 'How many sides does a hexagon have?', answer: '6' },
                13: { question: 'What is the chemical symbol for silver?', answer: 'Ag' },
                14: { question: 'What is the largest desert in the world?', answer: 'Sahara' },
                15: { question: 'What is the smallest planet in our solar system?', answer: 'Mercury' },
                16: { question: 'What is the largest ocean on Earth?', answer: 'Pacific Ocean' },
                17: { question: 'Who wrote "Romeo and Juliet"?', answer: 'William Shakespeare' },
                18: { question: 'What is the main component of the sun?', answer: 'Hydrogen' },
                19: { question: 'What is the smallest country in the world?', answer: 'Vatican City' },
                20: { question: 'What is the largest mammal?', answer: 'Blue Whale' },
                21: { question: 'What is the capital of Japan?', answer: 'Tokyo' },
                22: { question: 'How many continents are there?', answer: '7' },
                23: { question: 'What is the chemical symbol for water?', answer: 'H2O' },
                24: { question: 'Who painted the Mona Lisa?', answer: 'Leonardo da Vinci' },
                25: { question: 'What is the largest desert in the world?', answer: 'Sahara Desert' },
                26: { question: 'What is the capital of Australia?', answer: 'Canberra' },
                27: { question: 'How many bones are in the human body?', answer: '206' },
                28: { question: 'What is the largest bird?', answer: 'Ostrich' },
                29: { question: 'What is the capital of Brazil?', answer: 'Brasília' },
                30: { question: 'What is the largest lake in the world?', answer: 'Caspian Sea' },
                31: { question: 'What is the capital of Egypt?', answer: 'Cairo' },
                32: { question: 'How many planets are in our solar system?', answer: '8' },
                33: { question: 'What is the largest mountain in the world?', answer: 'Mount Everest' },
                34: { question: 'What is the capital of India?', answer: 'New Delhi' },
                35: { question: 'What is the largest river in the world?', answer: 'Nile' },
                36: { question: 'What is the capital of China?', answer: 'Beijing' },
                37: { question: 'How many states are in the USA?', answer: '50' },
                38: { question: 'What is the largest island in the world?', answer: 'Greenland' },
                39: { question: 'What is the capital of Russia?', answer: 'Moscow' },
                40: { question: 'What is the largest volcano in the world?', answer: 'Mauna Loa' },
                41: { question: 'What is the capital of Canada?', answer: 'Ottawa' },
                42: { question: 'How many chromosomes do humans have?', answer: '46' },
                43: { question: 'What is the largest waterfall in the world?', answer: 'Angel Falls' },
                44: { question: 'What is the capital of Mexico?', answer: 'Mexico City' },
                45: { question: 'What is the largest coral reef?', answer: 'Great Barrier Reef' },
                46: { question: 'What is the capital of Argentina?', answer: 'Buenos Aires' },
                47: { question: 'How many moons does Earth have?', answer: '1' },
                48: { question: 'What is the largest forest in the world?', answer: 'Amazon Rainforest' },
                49: { question: 'What is the capital of South Africa?', answer: 'Pretoria' },
                50: { question: 'What is the largest canyon in the world?', answer: 'Grand Canyon' },
                51: { question: 'What is the capital of Germany?', answer: 'Berlin' },
                52: { question: 'How many elements are in the periodic table?', answer: '118' },
                53: { question: 'What is the largest glacier in the world?', answer: 'Lambert Glacier' },
                54: { question: 'What is the capital of Italy?', answer: 'Rome' },
                55: { question: 'What is the largest bay in the world?', answer: 'Bay of Bengal' },
                56: { question: 'What is the capital of Spain?', answer: 'Madrid' },
                57: { question: 'How many muscles are in the human body?', answer: '600' },
                58: { question: 'What is the largest peninsula in the world?', answer: 'Arabian Peninsula' },
                59: { question: 'What is the capital of Portugal?', answer: 'Lisbon' },
                60: { question: 'What is the largest gulf in the world?', answer: 'Gulf of Mexico' }
            },
            2: { // Entertainment & Pop Culture
                6: { question: 'Who directed the movie "Titanic"?', answer: 'James Cameron' },
                7: { question: 'What year did the first iPhone come out?', answer: '2007' },
                8: { question: 'Who is known as the "King of Rock and Roll"?', answer: 'Elvis Presley' },
                9: { question: 'What is the name of the famous painting by Van Gogh?', answer: 'Starry Night' },
                10: { question: 'Who wrote "The Lord of the Rings"?', answer: 'J.R.R. Tolkien' },
                11: { question: 'What is the name of the fictional town in "Friends"?', answer: 'New York' },
                12: { question: 'Who played Spider-Man in the 2002 movie?', answer: 'Tobey Maguire' },
                13: { question: 'What is the name of the famous sculpture by Rodin?', answer: 'The Thinker' },
                14: { question: 'Who is known as the "Queen of Pop"?', answer: 'Madonna' },
                15: { question: 'What year did the first Star Wars movie release?', answer: '1977' },
                16: { question: 'Who played Captain America in the Marvel movies?', answer: 'Chris Evans' },
                17: { question: 'What year did Facebook launch?', answer: '2004' },
                18: { question: 'Who wrote "Hamlet"?', answer: 'William Shakespeare' },
                19: { question: 'Who painted "The Starry Night"?', answer: 'Vincent van Gogh' },
                20: { question: 'Who is known as the "Queen of Pop"?', answer: 'Madonna' },
                21: { question: 'Who played Spider-Man in the Marvel movies?', answer: 'Tom Holland' },
                22: { question: 'What year did YouTube launch?', answer: '2005' },
                23: { question: 'Who wrote "Macbeth"?', answer: 'William Shakespeare' },
                24: { question: 'Who painted "The Scream"?', answer: 'Edvard Munch' },
                25: { question: 'Who is known as the "King of Rock"?', answer: 'Elvis Presley' },
                26: { question: 'Who played Black Widow in the Marvel movies?', answer: 'Scarlett Johansson' },
                27: { question: 'What year did Twitter launch?', answer: '2006' },
                28: { question: 'Who wrote "Othello"?', answer: 'William Shakespeare' },
                29: { question: 'Who painted "The Last Supper"?', answer: 'Leonardo da Vinci' },
                30: { question: 'Who is known as the "Queen of Soul"?', answer: 'Aretha Franklin' },
                31: { question: 'Who played Thor in the Marvel movies?', answer: 'Chris Hemsworth' },
                32: { question: 'What year did Instagram launch?', answer: '2010' },
                33: { question: 'Who wrote "King Lear"?', answer: 'William Shakespeare' },
                34: { question: 'Who painted "Guernica"?', answer: 'Pablo Picasso' },
                35: { question: 'Who is known as the "King of Blues"?', answer: 'B.B. King' },
                36: { question: 'Who played Hulk in the Marvel movies?', answer: 'Mark Ruffalo' },
                37: { question: 'What year did Snapchat launch?', answer: '2011' },
                38: { question: 'Who wrote "The Tempest"?', answer: 'William Shakespeare' },
                39: { question: 'Who painted "The Persistence of Memory"?', answer: 'Salvador Dalí' },
                40: { question: 'Who is known as the "Queen of Country"?', answer: 'Dolly Parton' },
                41: { question: 'Who played Black Panther in the Marvel movies?', answer: 'Chadwick Boseman' },
                42: { question: 'What year did TikTok launch?', answer: '2016' },
                43: { question: 'Who wrote "A Midsummer Night\'s Dream"?', answer: 'William Shakespeare' },
                44: { question: 'Who painted "The Birth of Venus"?', answer: 'Sandro Botticelli' },
                45: { question: 'Who is known as the "King of Jazz"?', answer: 'Louis Armstrong' },
                46: { question: 'Who played Doctor Strange in the Marvel movies?', answer: 'Benedict Cumberbatch' },
                47: { question: 'What year did WhatsApp launch?', answer: '2009' },
                48: { question: 'Who wrote "Much Ado About Nothing"?', answer: 'William Shakespeare' },
                49: { question: 'Who painted "The Night Watch"?', answer: 'Rembrandt' },
                50: { question: 'Who is known as the "Queen of Jazz"?', answer: 'Ella Fitzgerald' },
                51: { question: 'Who played Ant-Man in the Marvel movies?', answer: 'Paul Rudd' },
                52: { question: 'What year did LinkedIn launch?', answer: '2003' },
                53: { question: 'Who wrote "Twelfth Night"?', answer: 'William Shakespeare' },
                54: { question: 'Who painted "The Creation of Adam"?', answer: 'Michelangelo' },
                55: { question: 'Who is known as the "King of Reggae"?', answer: 'Bob Marley' },
                56: { question: 'Who played Captain Marvel in the Marvel movies?', answer: 'Brie Larson' },
                57: { question: 'What year did Reddit launch?', answer: '2005' },
                58: { question: 'Who wrote "The Merchant of Venice"?', answer: 'William Shakespeare' },
                59: { question: 'Who painted "The School of Athens"?', answer: 'Raphael' },
                60: { question: 'Who is known as the "Queen of Rock"?', answer: 'Janis Joplin' }
            },
            3: { // Technology & Innovation
                6: { question: 'What does RAM stand for?', answer: 'Random Access Memory' },
                7: { question: 'Who founded Apple Inc.?', answer: 'Steve Jobs' },
                8: { question: 'What does URL stand for?', answer: 'Uniform Resource Locator' },
                9: { question: 'What year was the World Wide Web invented?', answer: '1989' },
                10: { question: 'What does USB stand for?', answer: 'Universal Serial Bus' },
                11: { question: 'Who created Facebook?', answer: 'Mark Zuckerberg' },
                12: { question: 'What does VPN stand for?', answer: 'Virtual Private Network' },
                13: { question: 'What year was the first computer mouse invented?', answer: '1964' },
                14: { question: 'What does AI stand for?', answer: 'Artificial Intelligence' },
                15: { question: 'Who invented the telephone?', answer: 'Alexander Graham Bell' },
                16: { question: 'What does CPU stand for?', answer: 'Central Processing Unit' },
                17: { question: 'Who founded Microsoft?', answer: 'Bill Gates' },
                18: { question: 'What does HTML stand for?', answer: 'HyperText Markup Language' },
                19: { question: 'What is the name of Google\'s mobile operating system?', answer: 'Android' },
                20: { question: 'What does SSD stand for?', answer: 'Solid State Drive' },
                21: { question: 'Who created Twitter?', answer: 'Jack Dorsey' },
                22: { question: 'What does API stand for?', answer: 'Application Programming Interface' },
                23: { question: 'What year was the first iPhone released?', answer: '2007' },
                24: { question: 'What does DNS stand for?', answer: 'Domain Name System' },
                25: { question: 'Who created Instagram?', answer: 'Kevin Systrom' },
                26: { question: 'What does HTTP stand for?', answer: 'HyperText Transfer Protocol' },
                27: { question: 'What year was YouTube founded?', answer: '2005' },
                28: { question: 'What does SSL stand for?', answer: 'Secure Sockets Layer' },
                29: { question: 'Who created Snapchat?', answer: 'Evan Spiegel' },
                30: { question: 'What does FTP stand for?', answer: 'File Transfer Protocol' },
                31: { question: 'What year was the first computer built?', answer: '1946' },
                32: { question: 'What does IP stand for?', answer: 'Internet Protocol' },
                33: { question: 'Who created LinkedIn?', answer: 'Reid Hoffman' },
                34: { question: 'What does CSS stand for?', answer: 'Cascading Style Sheets' },
                35: { question: 'What year was the internet invented?', answer: '1969' },
                36: { question: 'What does JSON stand for?', answer: 'JavaScript Object Notation' },
                37: { question: 'Who created WhatsApp?', answer: 'Jan Koum' },
                38: { question: 'What does SQL stand for?', answer: 'Structured Query Language' },
                39: { question: 'What year was the first email sent?', answer: '1971' },
                40: { question: 'What does XML stand for?', answer: 'eXtensible Markup Language' },
                41: { question: 'Who created TikTok?', answer: 'Zhang Yiming' },
                42: { question: 'What does PHP stand for?', answer: 'PHP: Hypertext Preprocessor' },
                43: { question: 'What year was the first website created?', answer: '1991' },
                44: { question: 'What does RSS stand for?', answer: 'Really Simple Syndication' },
                45: { question: 'Who created Reddit?', answer: 'Steve Huffman' },
                46: { question: 'What does IDE stand for?', answer: 'Integrated Development Environment' },
                47: { question: 'What year was the first smartphone released?', answer: '1994' },
                48: { question: 'What does GUI stand for?', answer: 'Graphical User Interface' },
                49: { question: 'Who created Pinterest?', answer: 'Ben Silbermann' },
                50: { question: 'What does CLI stand for?', answer: 'Command Line Interface' },
                51: { question: 'What year was the first laptop computer released?', answer: '1981' },
                52: { question: 'What does SDK stand for?', answer: 'Software Development Kit' },
                53: { question: 'Who created Tumblr?', answer: 'David Karp' },
                54: { question: 'What does API stand for?', answer: 'Application Programming Interface' },
                55: { question: 'What year was the first tablet computer released?', answer: '1989' },
                56: { question: 'What does MVC stand for?', answer: 'Model-View-Controller' },
                57: { question: 'Who created Discord?', answer: 'Jason Citron' },
                58: { question: 'What does CRUD stand for?', answer: 'Create, Read, Update, Delete' },
                59: { question: 'What year was the first video game created?', answer: '1958' },
                60: { question: 'What does OOP stand for?', answer: 'Object-Oriented Programming' }
            },
            4: { // History & Politics
                6: { question: 'What year did the American Civil War end?', answer: '1865' },
                7: { question: 'Who was the first Emperor of Rome?', answer: 'Augustus' },
                8: { question: 'What year did the French Revolution begin?', answer: '1789' },
                9: { question: 'Who was the first female astronaut?', answer: 'Valentina Tereshkova' },
                10: { question: 'What year did the Berlin Wall fall?', answer: '1989' },
                11: { question: 'Who was the first President of South Africa?', answer: 'Nelson Mandela' },
                12: { question: 'What year did the Cold War end?', answer: '1991' },
                13: { question: 'Who was the first female Prime Minister of India?', answer: 'Indira Gandhi' },
                14: { question: 'What year did the Russian Revolution occur?', answer: '1917' },
                15: { question: 'Who was the first female Supreme Court Justice?', answer: 'Sandra Day O\'Connor' },
                16: { question: 'What year did World War I end?', answer: '1918' },
                17: { question: 'Who was the first President of the United States?', answer: 'George Washington' },
                18: { question: 'What year did the Declaration of Independence get signed?', answer: '1776' },
                19: { question: 'Who was the first female Prime Minister of the UK?', answer: 'Margaret Thatcher' },
                20: { question: 'What year did the American Revolution begin?', answer: '1775' },
                21: { question: 'Who was the first Emperor of China?', answer: 'Qin Shi Huang' },
                22: { question: 'What year did the Magna Carta get signed?', answer: '1215' },
                23: { question: 'Who was the first female President of a country?', answer: 'Vigdis Finnbogadottir' },
                24: { question: 'What year did the Industrial Revolution begin?', answer: '1760' },
                25: { question: 'Who was the first King of England?', answer: 'Athelstan' },
                26: { question: 'What year did the Spanish Civil War end?', answer: '1939' },
                27: { question: 'Who was the first female Nobel Prize winner?', answer: 'Marie Curie' },
                28: { question: 'What year did the Vietnam War end?', answer: '1975' },
                29: { question: 'Who was the first Emperor of Japan?', answer: 'Emperor Jimmu' },
                30: { question: 'What year did the Korean War end?', answer: '1953' },
                31: { question: 'Who was the first female astronaut from the US?', answer: 'Sally Ride' },
                32: { question: 'What year did the Cuban Missile Crisis occur?', answer: '1962' },
                33: { question: 'Who was the first President of France?', answer: 'Louis-Napoleon Bonaparte' },
                34: { question: 'What year did the Berlin Wall get built?', answer: '1961' },
                35: { question: 'Who was the first female Prime Minister of Canada?', answer: 'Kim Campbell' },
                36: { question: 'What year did the Gulf War end?', answer: '1991' },
                37: { question: 'Who was the first Emperor of Russia?', answer: 'Peter the Great' },
                38: { question: 'What year did the American Civil War begin?', answer: '1861' },
                39: { question: 'Who was the first female Prime Minister of Australia?', answer: 'Julia Gillard' },
                40: { question: 'What year did the Mexican Revolution begin?', answer: '1910' },
                41: { question: 'Who was the first President of Mexico?', answer: 'Guadalupe Victoria' },
                42: { question: 'What year did the Russian Revolution begin?', answer: '1917' },
                43: { question: 'Who was the first female Prime Minister of New Zealand?', answer: 'Jenny Shipley' },
                44: { question: 'What year did the Chinese Revolution occur?', answer: '1911' },
                45: { question: 'Who was the first Emperor of Brazil?', answer: 'Pedro I' },
                46: { question: 'What year did the Indian Independence movement begin?', answer: '1857' },
                47: { question: 'Who was the first female Prime Minister of Pakistan?', answer: 'Benazir Bhutto' },
                48: { question: 'What year did the Irish War of Independence end?', answer: '1921' },
                49: { question: 'Who was the first President of Ireland?', answer: 'Douglas Hyde' },
                50: { question: 'What year did the Spanish-American War end?', answer: '1898' },
                51: { question: 'Who was the first female Prime Minister of Israel?', answer: 'Golda Meir' },
                52: { question: 'What year did the Philippine Revolution begin?', answer: '1896' },
                53: { question: 'Who was the first President of the Philippines?', answer: 'Emilio Aguinaldo' },
                54: { question: 'What year did the Korean War begin?', answer: '1950' },
                55: { question: 'Who was the first female Prime Minister of Sri Lanka?', answer: 'Sirimavo Bandaranaike' },
                56: { question: 'What year did the Vietnam War begin?', answer: '1955' },
                57: { question: 'Who was the first President of Vietnam?', answer: 'Ho Chi Minh' },
                58: { question: 'What year did the Cambodian Civil War end?', answer: '1975' },
                59: { question: 'Who was the first female Prime Minister of Bangladesh?', answer: 'Sheikh Hasina' },
                60: { question: 'What year did the Bangladesh Liberation War end?', answer: '1971' }
            },
            5: { // Sports & Games
                6: { question: 'What is the national sport of Canada?', answer: 'Ice Hockey' },
                7: { question: 'How many players are on a soccer team?', answer: '11' },
                8: { question: 'What is the oldest Olympic sport?', answer: 'Running' },
                9: { question: 'What is the national sport of Brazil?', answer: 'Soccer' },
                10: { question: 'How many players are on a volleyball team?', answer: '6' },
                11: { question: 'What is the national sport of Australia?', answer: 'Cricket' },
                12: { question: 'How many players are on a baseball team?', answer: '9' },
                13: { question: 'What is the national sport of New Zealand?', answer: 'Rugby' },
                14: { question: 'How many players are on a tennis court?', answer: '2 or 4' },
                15: { question: 'What is the national sport of India?', answer: 'Cricket' },
                16: { question: 'What is the national sport of Japan?', answer: 'Sumo Wrestling' },
                17: { question: 'How many players are on a basketball team?', answer: '5' },
                18: { question: 'What is the national sport of Argentina?', answer: 'Pato' },
                19: { question: 'How many players are on a rugby team?', answer: '15' },
                20: { question: 'What is the national sport of South Korea?', answer: 'Taekwondo' },
                21: { question: 'What is the national sport of Thailand?', answer: 'Muay Thai' },
                22: { question: 'How many players are on a cricket team?', answer: '11' },
                23: { question: 'What is the national sport of Mongolia?', answer: 'Wrestling' },
                24: { question: 'How many players are on a field hockey team?', answer: '11' },
                25: { question: 'What is the national sport of Pakistan?', answer: 'Field Hockey' },
                26: { question: 'What is the national sport of Sri Lanka?', answer: 'Volleyball' },
                27: { question: 'How many players are on a water polo team?', answer: '7' },
                28: { question: 'What is the national sport of Bangladesh?', answer: 'Kabaddi' },
                29: { question: 'How many players are on a handball team?', answer: '7' },
                30: { question: 'What is the national sport of Nepal?', answer: 'Dandi Biyo' },
                31: { question: 'What is the national sport of Bhutan?', answer: 'Archery' },
                32: { question: 'How many players are on a lacrosse team?', answer: '10' },
                33: { question: 'What is the national sport of Malaysia?', answer: 'Sepak Takraw' },
                34: { question: 'How many players are on a badminton court?', answer: '2 or 4' },
                35: { question: 'What is the national sport of Indonesia?', answer: 'Pencak Silat' },
                36: { question: 'What is the national sport of the Philippines?', answer: 'Arnis' },
                37: { question: 'How many players are on a table tennis table?', answer: '2 or 4' },
                38: { question: 'What is the national sport of Vietnam?', answer: 'Vovinam' },
                39: { question: 'How many players are on a curling team?', answer: '4' },
                40: { question: 'What is the national sport of Cambodia?', answer: 'Bokator' },
                41: { question: 'What is the national sport of Laos?', answer: 'Muay Lao' },
                42: { question: 'How many players are on a softball team?', answer: '9' },
                43: { question: 'What is the national sport of Myanmar?', answer: 'Chinlone' },
                44: { question: 'How many players are on a dodgeball team?', answer: '6' },
                45: { question: 'What is the national sport of Singapore?', answer: 'Dragon Boat Racing' },
                46: { question: 'What is the national sport of Brunei?', answer: 'Silat' },
                47: { question: 'How many players are on a kickball team?', answer: '9' },
                48: { question: 'What is the national sport of East Timor?', answer: 'Tais' },
                49: { question: 'How many players are on a ultimate frisbee team?', answer: '7' },
                50: { question: 'What is the national sport of Papua New Guinea?', answer: 'Rugby League' },
                51: { question: 'What is the national sport of Fiji?', answer: 'Rugby Union' },
                52: { question: 'How many players are on a netball team?', answer: '7' },
                53: { question: 'What is the national sport of Samoa?', answer: 'Kilikiti' },
                54: { question: 'How many players are on a rounders team?', answer: '9' },
                55: { question: 'What is the national sport of Tonga?', answer: 'Rugby Union' },
                56: { question: 'What is the national sport of Vanuatu?', answer: 'Cricket' },
                57: { question: 'How many players are on a Gaelic football team?', answer: '15' },
                58: { question: 'What is the national sport of Kiribati?', answer: 'Wrestling' },
                59: { question: 'How many players are on a hurling team?', answer: '15' },
                60: { question: 'What is the national sport of Tuvalu?', answer: 'Kilikiti' }
            },
            6: { // Geography & Travel
                6: { question: 'What is the capital of Australia?', answer: 'Canberra' },
                7: { question: 'What is the largest country in South America?', answer: 'Brazil' },
                8: { question: 'What is the capital of Canada?', answer: 'Ottawa' },
                9: { question: 'What is the largest island in the world?', answer: 'Greenland' },
                10: { question: 'What is the capital of China?', answer: 'Beijing' },
                11: { question: 'What is the largest desert in Asia?', answer: 'Gobi Desert' },
                12: { question: 'What is the capital of India?', answer: 'New Delhi' },
                13: { question: 'What is the largest lake in Africa?', answer: 'Lake Victoria' },
                14: { question: 'What is the capital of Russia?', answer: 'Moscow' },
                15: { question: 'What is the largest mountain range in Europe?', answer: 'Alps' },
                16: { question: 'What is the capital of South Africa?', answer: 'Pretoria' },
                17: { question: 'What is the largest river in Africa?', answer: 'Nile' },
                18: { question: 'What is the capital of Argentina?', answer: 'Buenos Aires' },
                19: { question: 'What is the largest desert in Africa?', answer: 'Sahara' },
                20: { question: 'What is the capital of Brazil?', answer: 'Brasília' },
                21: { question: 'What is the largest lake in North America?', answer: 'Lake Superior' },
                22: { question: 'What is the capital of Mexico?', answer: 'Mexico City' },
                23: { question: 'What is the largest river in South America?', answer: 'Amazon' },
                24: { question: 'What is the capital of Chile?', answer: 'Santiago' },
                25: { question: 'What is the largest mountain range in South America?', answer: 'Andes' },
                26: { question: 'What is the capital of Peru?', answer: 'Lima' },
                27: { question: 'What is the largest lake in South America?', answer: 'Lake Titicaca' },
                28: { question: 'What is the capital of Colombia?', answer: 'Bogotá' },
                29: { question: 'What is the largest river in Asia?', answer: 'Yangtze' },
                30: { question: 'What is the capital of Venezuela?', answer: 'Caracas' },
                31: { question: 'What is the largest lake in Asia?', answer: 'Caspian Sea' },
                32: { question: 'What is the capital of Ecuador?', answer: 'Quito' },
                33: { question: 'What is the largest mountain range in Asia?', answer: 'Himalayas' },
                34: { question: 'What is the capital of Bolivia?', answer: 'La Paz' },
                35: { question: 'What is the largest desert in Australia?', answer: 'Great Victoria Desert' },
                36: { question: 'What is the capital of Paraguay?', answer: 'Asunción' },
                37: { question: 'What is the largest lake in Australia?', answer: 'Lake Eyre' },
                38: { question: 'What is the capital of Uruguay?', answer: 'Montevideo' },
                39: { question: 'What is the largest river in Australia?', answer: 'Murray' },
                40: { question: 'What is the capital of Guyana?', answer: 'Georgetown' },
                41: { question: 'What is the largest mountain range in Australia?', answer: 'Great Dividing Range' },
                42: { question: 'What is the capital of Suriname?', answer: 'Paramaribo' },
                43: { question: 'What is the largest lake in Europe?', answer: 'Ladoga' },
                44: { question: 'What is the capital of French Guiana?', answer: 'Cayenne' },
                45: { question: 'What is the largest river in Europe?', answer: 'Volga' },
                46: { question: 'What is the capital of Falkland Islands?', answer: 'Stanley' },
                47: { question: 'What is the largest mountain range in North America?', answer: 'Rocky Mountains' },
                48: { question: 'What is the capital of Greenland?', answer: 'Nuuk' },
                49: { question: 'What is the largest lake in the world?', answer: 'Caspian Sea' },
                50: { question: 'What is the capital of Iceland?', answer: 'Reykjavik' },
                51: { question: 'What is the largest river in the world?', answer: 'Nile' },
                52: { question: 'What is the capital of Norway?', answer: 'Oslo' },
                53: { question: 'What is the largest mountain in the world?', answer: 'Mount Everest' },
                54: { question: 'What is the capital of Sweden?', answer: 'Stockholm' },
                55: { question: 'What is the largest volcano in the world?', answer: 'Mauna Loa' },
                56: { question: 'What is the capital of Finland?', answer: 'Helsinki' },
                57: { question: 'What is the largest waterfall in the world?', answer: 'Angel Falls' },
                58: { question: 'What is the capital of Denmark?', answer: 'Copenhagen' },
                59: { question: 'What is the largest canyon in the world?', answer: 'Grand Canyon' },
                60: { question: 'What is the capital of Netherlands?', answer: 'Amsterdam' }
            },
            7: { // Food & Cooking
                6: { question: 'What is the main ingredient in sushi rice?', answer: 'Rice' },
                7: { question: 'What country is known for pasta?', answer: 'Italy' },
                8: { question: 'What is the main ingredient in hummus?', answer: 'Chickpeas' },
                9: { question: 'What country is known for tacos?', answer: 'Mexico' },
                10: { question: 'What is the main ingredient in curry?', answer: 'Spices' },
                11: { question: 'What country is known for kimchi?', answer: 'Korea' },
                12: { question: 'What is the main ingredient in falafel?', answer: 'Chickpeas' },
                13: { question: 'What country is known for paella?', answer: 'Spain' },
                14: { question: 'What is the main ingredient in tabbouleh?', answer: 'Bulgur wheat' },
                15: { question: 'What country is known for pad thai?', answer: 'Thailand' },
                16: { question: 'What is the main ingredient in guacamole?', answer: 'Avocado' },
                17: { question: 'What country is known for pizza?', answer: 'Italy' },
                18: { question: 'What is the main ingredient in baba ganoush?', answer: 'Eggplant' },
                19: { question: 'What country is known for sushi?', answer: 'Japan' },
                20: { question: 'What is the main ingredient in tzatziki?', answer: 'Yogurt' },
                21: { question: 'What country is known for croissants?', answer: 'France' },
                22: { question: 'What is the main ingredient in pesto?', answer: 'Basil' },
                23: { question: 'What country is known for bratwurst?', answer: 'Germany' },
                24: { question: 'What is the main ingredient in gazpacho?', answer: 'Tomatoes' },
                25: { question: 'What country is known for borscht?', answer: 'Russia' },
                26: { question: 'What is the main ingredient in ratatouille?', answer: 'Eggplant' },
                27: { question: 'What country is known for moussaka?', answer: 'Greece' },
                28: { question: 'What is the main ingredient in bouillabaisse?', answer: 'Fish' },
                29: { question: 'What country is known for goulash?', answer: 'Hungary' },
                30: { question: 'What is the main ingredient in risotto?', answer: 'Rice' },
                31: { question: 'What country is known for schnitzel?', answer: 'Austria' },
                32: { question: 'What is the main ingredient in fondue?', answer: 'Cheese' },
                33: { question: 'What country is known for fondue?', answer: 'Switzerland' },
                34: { question: 'What is the main ingredient in tapas?', answer: 'Various' },
                35: { question: 'What country is known for tapas?', answer: 'Spain' },
                36: { question: 'What is the main ingredient in ceviche?', answer: 'Fish' },
                37: { question: 'What country is known for ceviche?', answer: 'Peru' },
                38: { question: 'What is the main ingredient in empanadas?', answer: 'Dough' },
                39: { question: 'What country is known for empanadas?', answer: 'Argentina' },
                40: { question: 'What is the main ingredient in arepas?', answer: 'Cornmeal' },
                41: { question: 'What country is known for arepas?', answer: 'Colombia' },
                42: { question: 'What is the main ingredient in feijoada?', answer: 'Black beans' },
                43: { question: 'What country is known for feijoada?', answer: 'Brazil' },
                44: { question: 'What is the main ingredient in poutine?', answer: 'French fries' },
                45: { question: 'What country is known for poutine?', answer: 'Canada' },
                46: { question: 'What is the main ingredient in jerk chicken?', answer: 'Chicken' },
                47: { question: 'What country is known for jerk chicken?', answer: 'Jamaica' },
                48: { question: 'What is the main ingredient in conch fritters?', answer: 'Conch' },
                49: { question: 'What country is known for conch fritters?', answer: 'Bahamas' },
                50: { question: 'What is the main ingredient in ackee and saltfish?', answer: 'Ackee' },
                51: { question: 'What country is known for ackee and saltfish?', answer: 'Jamaica' },
                52: { question: 'What is the main ingredient in callaloo?', answer: 'Leafy greens' },
                53: { question: 'What country is known for callaloo?', answer: 'Trinidad and Tobago' },
                54: { question: 'What is the main ingredient in roti?', answer: 'Flour' },
                55: { question: 'What country is known for roti?', answer: 'India' },
                56: { question: 'What is the main ingredient in naan?', answer: 'Flour' },
                57: { question: 'What country is known for naan?', answer: 'India' },
                58: { question: 'What is the main ingredient in samosas?', answer: 'Pastry' },
                59: { question: 'What country is known for samosas?', answer: 'India' },
                60: { question: 'What is the main ingredient in biryani?', answer: 'Rice' }
            },
            8: { // Literature & Arts
                6: { question: 'Who wrote "To Kill a Mockingbird"?', answer: 'Harper Lee' },
                7: { question: 'What is the name of the famous painting by Edvard Munch?', answer: 'The Scream' },
                8: { question: 'Who wrote "1984"?', answer: 'George Orwell' },
                9: { question: 'What is the name of the famous sculpture by Auguste Rodin?', answer: 'The Thinker' },
                10: { question: 'Who wrote "The Catcher in the Rye"?', answer: 'J.D. Salinger' },
                11: { question: 'What is the name of the famous painting by Salvador Dali?', answer: 'The Persistence of Memory' },
                12: { question: 'Who wrote "Lord of the Flies"?', answer: 'William Golding' },
                13: { question: 'What is the name of the famous sculpture by Donatello?', answer: 'David' },
                14: { question: 'Who wrote "Animal Farm"?', answer: 'George Orwell' },
                15: { question: 'What is the name of the famous painting by Claude Monet?', answer: 'Water Lilies' },
                16: { question: 'Who wrote "The Great Gatsby"?', answer: 'F. Scott Fitzgerald' },
                17: { question: 'What is the name of the famous painting by Vincent van Gogh?', answer: 'Starry Night' },
                18: { question: 'Who wrote "Pride and Prejudice"?', answer: 'Jane Austen' },
                19: { question: 'What is the name of the famous sculpture by Michelangelo?', answer: 'David' },
                20: { question: 'Who wrote "Jane Eyre"?', answer: 'Charlotte Brontë' },
                21: { question: 'What is the name of the famous painting by Leonardo da Vinci?', answer: 'Mona Lisa' },
                22: { question: 'Who wrote "Wuthering Heights"?', answer: 'Emily Brontë' },
                23: { question: 'What is the name of the famous painting by Pablo Picasso?', answer: 'Guernica' },
                24: { question: 'Who wrote "Little Women"?', answer: 'Louisa May Alcott' },
                25: { question: 'What is the name of the famous sculpture by Bernini?', answer: 'Apollo and Daphne' },
                26: { question: 'Who wrote "The Scarlet Letter"?', answer: 'Nathaniel Hawthorne' },
                27: { question: 'What is the name of the famous painting by Rembrandt?', answer: 'The Night Watch' },
                28: { question: 'Who wrote "Moby Dick"?', answer: 'Herman Melville' },
                29: { question: 'What is the name of the famous painting by Sandro Botticelli?', answer: 'The Birth of Venus' },
                30: { question: 'Who wrote "Uncle Tom\'s Cabin"?', answer: 'Harriet Beecher Stowe' },
                31: { question: 'What is the name of the famous sculpture by Canova?', answer: 'Psyche Revived by Cupid\'s Kiss' },
                32: { question: 'Who wrote "The Adventures of Tom Sawyer"?', answer: 'Mark Twain' },
                33: { question: 'What is the name of the famous painting by Hieronymus Bosch?', answer: 'The Garden of Earthly Delights' },
                34: { question: 'Who wrote "The Call of the Wild"?', answer: 'Jack London' },
                35: { question: 'What is the name of the famous painting by Jan van Eyck?', answer: 'The Arnolfini Portrait' },
                36: { question: 'Who wrote "The Jungle Book"?', answer: 'Rudyard Kipling' },
                37: { question: 'What is the name of the famous sculpture by Cellini?', answer: 'Perseus with the Head of Medusa' },
                38: { question: 'Who wrote "Alice\'s Adventures in Wonderland"?', answer: 'Lewis Carroll' },
                39: { question: 'What is the name of the famous painting by Pieter Bruegel?', answer: 'The Tower of Babel' },
                40: { question: 'Who wrote "Treasure Island"?', answer: 'Robert Louis Stevenson' },
                41: { question: 'What is the name of the famous painting by El Greco?', answer: 'The Burial of the Count of Orgaz' },
                42: { question: 'Who wrote "The Strange Case of Dr Jekyll and Mr Hyde"?', answer: 'Robert Louis Stevenson' },
                43: { question: 'What is the name of the famous sculpture by Verrocchio?', answer: 'David' },
                44: { question: 'Who wrote "The Picture of Dorian Gray"?', answer: 'Oscar Wilde' },
                45: { question: 'What is the name of the famous painting by Caravaggio?', answer: 'The Calling of Saint Matthew' },
                46: { question: 'Who wrote "Dracula"?', answer: 'Bram Stoker' },
                47: { question: 'What is the name of the famous painting by Velázquez?', answer: 'Las Meninas' },
                48: { question: 'Who wrote "The War of the Worlds"?', answer: 'H.G. Wells' },
                49: { question: 'What is the name of the famous sculpture by Ghiberti?', answer: 'Gates of Paradise' },
                50: { question: 'Who wrote "The Time Machine"?', answer: 'H.G. Wells' },
                51: { question: 'What is the name of the famous painting by Goya?', answer: 'The Third of May 1808' },
                52: { question: 'Who wrote "The Invisible Man"?', answer: 'H.G. Wells' },
                53: { question: 'What is the name of the famous painting by Delacroix?', answer: 'Liberty Leading the People' },
                54: { question: 'Who wrote "The Hound of the Baskervilles"?', answer: 'Arthur Conan Doyle' },
                55: { question: 'What is the name of the famous sculpture by Houdon?', answer: 'Voltaire' },
                56: { question: 'Who wrote "The Adventures of Sherlock Holmes"?', answer: 'Arthur Conan Doyle' },
                57: { question: 'What is the name of the famous painting by Ingres?', answer: 'The Grande Odalisque' },
                58: { question: 'Who wrote "The Sign of the Four"?', answer: 'Arthur Conan Doyle' },
                59: { question: 'What is the name of the famous painting by Courbet?', answer: 'The Origin of the World' },
                60: { question: 'Who wrote "A Study in Scarlet"?', answer: 'Arthur Conan Doyle' }
            }
        };
        
        // Return additional questions for the specific category and question number
        if (additionalQuestions[categoryIndex] && additionalQuestions[categoryIndex][questionNumber]) {
            return additionalQuestions[categoryIndex][questionNumber];
        }
        
        // Fallback generic questions
        const fallbackQuestions = [
            { question: 'What is the capital of France?', answer: 'Paris' },
            { question: 'How many sides does a triangle have?', answer: '3' },
            { question: 'What is the largest planet in our solar system?', answer: 'Jupiter' },
            { question: 'What year did World War II end?', answer: '1945' },
            { question: 'What is the chemical symbol for gold?', answer: 'Au' },
            { question: 'What is the largest mammal on Earth?', answer: 'Blue Whale' },
            { question: 'How many continents are there?', answer: '7' },
            { question: 'What is the main component of the sun?', answer: 'Hydrogen' },
            { question: 'What is the hardest natural substance?', answer: 'Diamond' },
            { question: 'What is the largest ocean?', answer: 'Pacific Ocean' },
            { question: 'Who wrote "Romeo and Juliet"?', answer: 'William Shakespeare' },
            { question: 'What is the capital of Japan?', answer: 'Tokyo' },
            { question: 'How many bones are in the human body?', answer: '206' },
            { question: 'What is the largest bird?', answer: 'Ostrich' },
            { question: 'What is the chemical symbol for water?', answer: 'H2O' },
            { question: 'Who painted the Mona Lisa?', answer: 'Leonardo da Vinci' },
            { question: 'What is the largest desert in the world?', answer: 'Sahara' },
            { question: 'What is the capital of Australia?', answer: 'Canberra' },
            { question: 'How many planets are in our solar system?', answer: '8' },
            { question: 'What is the largest mountain in the world?', answer: 'Mount Everest' },
            { question: 'What is the capital of India?', answer: 'New Delhi' },
            { question: 'What is the largest river in the world?', answer: 'Nile' },
            { question: 'What is the capital of China?', answer: 'Beijing' },
            { question: 'How many states are in the USA?', answer: '50' },
            { question: 'What is the largest island in the world?', answer: 'Greenland' },
            { question: 'What is the capital of Russia?', answer: 'Moscow' },
            { question: 'What is the largest volcano in the world?', answer: 'Mauna Loa' },
            { question: 'What is the capital of Canada?', answer: 'Ottawa' },
            { question: 'How many chromosomes do humans have?', answer: '46' },
            { question: 'What is the largest waterfall in the world?', answer: 'Angel Falls' },
            { question: 'What is the capital of Mexico?', answer: 'Mexico City' },
            { question: 'What is the largest coral reef?', answer: 'Great Barrier Reef' },
            { question: 'What is the capital of Argentina?', answer: 'Buenos Aires' },
            { question: 'How many moons does Earth have?', answer: '1' },
            { question: 'What is the largest forest in the world?', answer: 'Amazon Rainforest' },
            { question: 'What is the capital of South Africa?', answer: 'Pretoria' },
            { question: 'What is the largest canyon in the world?', answer: 'Grand Canyon' },
            { question: 'What is the capital of Germany?', answer: 'Berlin' },
            { question: 'How many elements are in the periodic table?', answer: '118' },
            { question: 'What is the largest glacier in the world?', answer: 'Lambert Glacier' },
            { question: 'What is the capital of Italy?', answer: 'Rome' },
            { question: 'What is the largest bay in the world?', answer: 'Bay of Bengal' },
            { question: 'What is the capital of Spain?', answer: 'Madrid' },
            { question: 'How many muscles are in the human body?', answer: '600' },
            { question: 'What is the largest peninsula in the world?', answer: 'Arabian Peninsula' },
            { question: 'What is the capital of Portugal?', answer: 'Lisbon' },
            { question: 'What is the largest gulf in the world?', answer: 'Gulf of Mexico' },
            { question: 'What is the capital of Netherlands?', answer: 'Amsterdam' },
            { question: 'How many teeth do adults have?', answer: '32' },
            { question: 'What is the largest archipelago in the world?', answer: 'Malay Archipelago' },
            { question: 'What is the capital of Belgium?', answer: 'Brussels' },
            { question: 'What is the largest strait in the world?', answer: 'Strait of Malacca' },
            { question: 'What is the capital of Switzerland?', answer: 'Bern' },
            { question: 'How many blood types are there?', answer: '8' },
            { question: 'What is the largest atoll in the world?', answer: 'Great Chagos Bank' },
            { question: 'What is the capital of Austria?', answer: 'Vienna' },
            { question: 'What is the largest lagoon in the world?', answer: 'New Caledonia' }
        ];
        
        return fallbackQuestions[(questionNumber - 6) % fallbackQuestions.length];
    }

        async function startCustomGame() {
            console.log('Starting custom game creation...');
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitLoading = document.getElementById('submitLoading');
            
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');
            
            // Get lobby code from URL if present
            const urlParams = new URLSearchParams(window.location.search);
            const lobbyCode = urlParams.get('lobby');
            
            // Collect all form data
            const categoryCount = parseInt(document.getElementById('categoryCount').value);
            const gameTimer = parseInt(document.getElementById('gameTimer').value);
            const questionTimer = parseInt(document.getElementById('questionTimer').value);
            const teamCount = 2; // Fixed to 2 teams
            const questionCount = parseInt(document.getElementById('questionCount').value);
            
            console.log('Form data collected:', {
                categoryCount,
                gameTimer,
                questionTimer,
                teamCount,
                questionCount,
                lobbyCode
            });
            
            // Team names will be automatically assigned when players join the lobby
            const teamNames = ['Team Alpha', 'Team Beta']; // Default team names for lobby
            
            console.log('Using default team names for lobby:', teamNames);
            
            // Collect categories and questions
            const categories = {};
            for (let i = 1; i <= categoryCount; i++) {
                const categoryName = document.getElementById(`category${i}Name`).value;
                if (!categoryName.trim()) {
                    alert(`Please enter a name for Category ${i}`);
                    return;
                }
                
                categories[categoryName.trim()] = {};
                for (let j = 1; j <= questionCount; j++) {
                    const question = document.getElementById(`category${i}Q${j}`).value;
                    const answer = document.getElementById(`category${i}A${j}`).value;
                    
                    if (!question.trim() || !answer.trim()) {
                        alert(`Please fill in all questions and answers for Category ${i}`);
                        return;
                    }
                    
                    categories[categoryName.trim()][j] = [{
                        question: question.trim(),
                        answer: answer.trim()
                    }];
                }
            }
            
            console.log('Categories collected:', categories);
            
            try {
                const requestData = {
                    team_names: teamNames,
                    categories: categories,
                    game_timer: gameTimer * 60, // Convert to seconds
                    question_timer: questionTimer,
                    category_count: categoryCount,
                    question_count: questionCount,
                    lobby_code: lobbyCode // Include lobby code if present
                };
                
                console.log('Sending request to server:', requestData);
                
                const response = await fetch('/jeopardy/start-custom', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(requestData)
                });

                const data = await response.json();
                
                console.log('Server response:', data);
                
                if (data.success) {
                    console.log('Custom game created successfully! Redirecting...');
                    
                    if (lobbyCode) {
                        // If this is for a lobby, redirect to the lobby room
                        window.location.href = `/jeopardy/lobby/${lobbyCode}`;
                    } else {
                        // Redirect to the custom game play page
                        window.location.href = '/jeopardy/play-custom';
                    }
                } else {
                    console.error('Server returned error:', data.error);
                    alert('Error creating custom game: ' + data.error);
                }
            } catch (error) {
                console.error('Error starting custom game:', error);
                alert('Error creating custom game. Please try again.');
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                submitLoading.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
