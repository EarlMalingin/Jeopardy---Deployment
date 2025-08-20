<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Custom Game Creator - Jeopardy</title>

    
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
                    <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-4">
                        🎯 Custom Game Creator
                    </h2>
                    <p class="text-gray-300">Create your own personalized Jeopardy game with custom categories, questions, and settings</p>
                    <p class="text-gray-400 text-sm mt-2">Support for 1-6 teams</p>
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
                        <!-- Number of Teams -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Number of Teams</label>
                            <select id="customTeamCount" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-select touch-input" onchange="updateCustomTeamInputs()">
                                <option value="1" selected>1 Player (Singleplayer)</option>
                                <option value="2">2 Teams</option>
                                <option value="3">3 Teams</option>
                                <option value="4">4 Teams</option>
                                <option value="5">5 Teams</option>
                                <option value="6">6 Teams</option>
                            </select>
                        </div>
                        
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
                    
                    <!-- Player Name Container -->
                    <div id="customTeamNamesContainer" class="mt-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Team Names (Optional)</label>
                        <p class="text-gray-400 text-sm mb-4" id="teamNamesDescription">Enter your name for the singleplayer game</p>
                        <div id="customTeamInputs" class="space-y-3">
                            <input type="text" id="customTeam1Name" 
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-input touch-input"
                                   placeholder="Enter your name (optional)">
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
            updateCustomTeamInputs();
            
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

        function updateCustomTeamInputs() {
            const teamCount = parseInt(document.getElementById('customTeamCount').value);
            const container = document.getElementById('customTeamInputs');
            const description = document.getElementById('teamNamesDescription');
            container.innerHTML = '';
            
            // Update description based on team count
            if (teamCount === 1) {
                description.textContent = 'Enter your name for the singleplayer game';
            } else {
                description.textContent = `Enter names for ${teamCount} teams (optional). Teams will be assigned automatically if names are not provided.`;
            }
            
            for (let i = 1; i <= teamCount; i++) {
                // Create label for the team
                const label = document.createElement('label');
                label.className = 'block text-sm font-medium text-gray-300 mb-2';
                
                if (teamCount === 1) {
                    label.textContent = 'Player Name';
                } else {
                    label.textContent = `Team ${i} Name`;
                }
                container.appendChild(label);
                
                // Create input field
                const input = document.createElement('input');
                input.type = 'text';
                input.id = `customTeam${i}Name`;
                input.required = false;
                input.className = 'w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-4 form-input touch-input';
                
                if (teamCount === 1) {
                    input.placeholder = 'Enter your name (optional)';
                } else {
                    input.placeholder = `Enter Team ${i} name (optional)`;
                }
                
                container.appendChild(input);
            }
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
            
            // Clear any existing game state first
            try {
                await fetch('/jeopardy/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                console.log('Cleared existing game state');
            } catch (error) {
                console.log('No existing game state to clear or error clearing:', error);
            }
            
            // Collect all form data
            const categoryCount = parseInt(document.getElementById('categoryCount').value);
            const gameTimer = parseInt(document.getElementById('gameTimer').value);
            const questionTimer = parseInt(document.getElementById('questionTimer').value);
            const teamCount = parseInt(document.getElementById('customTeamCount').value);
            const questionCount = parseInt(document.getElementById('questionCount').value);
            
            console.log('Form data collected:', {
                categoryCount,
                gameTimer,
                questionTimer,
                teamCount,
                questionCount
            });
            
            // Collect team names
            const teamNames = [];
            const defaultTeamNames = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta'];
            
            for (let i = 1; i <= teamCount; i++) {
                const teamNameInput = document.getElementById(`customTeam${i}Name`);
                if (teamNameInput) {
                    const teamName = teamNameInput.value.trim();
                    if (teamCount === 1) {
                        teamNames.push(teamName || 'Player'); // Use default if empty
                } else {
                        teamNames.push(teamName || `Team ${defaultTeamNames[i - 1]}`); // Use default if empty
                    }
                } else {
                    if (teamCount === 1) {
                        teamNames.push('Player');
                    } else {
                        teamNames.push(`Team ${defaultTeamNames[i - 1]}`);
                    }
                }
            }
            
            console.log('Team names collected:', teamNames);
            
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
                // Determine game mode based on team count
                const gameMode = teamCount === 1 ? 'singleplayer' : 'multiplayer';
                
                const requestData = {
                    team_names: teamNames,
                    categories: categories,
                    game_timer: gameTimer * 60, // Convert to seconds
                    question_timer: questionTimer,
                    category_count: categoryCount,
                    question_count: questionCount,
                    game_mode: gameMode
                };
                
                console.log('Sending request to server:', requestData);
                console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const response = await fetch('/jeopardy/simple-start-custom', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                console.log('Server response:', data);
                
                if (data.success) {
                    console.log('Custom game created successfully! Redirecting...');
                    console.log('Game state created:', data.game_state);
                    // Redirect to the simple custom game play page
                    window.location.href = '/jeopardy/simple-play-custom';
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
