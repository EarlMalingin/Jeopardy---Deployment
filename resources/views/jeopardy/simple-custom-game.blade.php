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
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mt-4">
                        <button onclick="fillSampleData()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors touch-button text-sm sm:text-base">
                            Fill Sample Data
                        </button>
                        <button onclick="testForm()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors touch-button text-sm sm:text-base">
                            Test Form Submission
                        </button>
                    </div>
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

        function fillSampleData() {
            console.log('fillSampleData called');
            
            // Get current category count
            const categoryCount = parseInt(document.getElementById('categoryCount').value);
            console.log('Category count:', categoryCount);
            
            updateCategoryInputs();
            
            // Wait a moment for the DOM to update, then fill category data
            setTimeout(() => {
                console.log('Starting to fill sample data...');
                
                // Fill team names based on team count
                const teamCount = parseInt(document.getElementById('customTeamCount').value);
                if (teamCount === 1) {
                    // Single player
                const playerNameInput = document.getElementById('customTeam1Name');
                if (playerNameInput) playerNameInput.value = 'Player';
                } else {
                    // Multiple teams
                    const teamNames = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta'];
                    for (let i = 1; i <= teamCount; i++) {
                        const teamNameInput = document.getElementById(`customTeam${i}Name`);
                        if (teamNameInput) {
                            teamNameInput.value = `Team ${teamNames[i - 1]}`;
                        }
                    }
                }
                
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
                const names = categoryNames[categoryCount] || categoryNames[5];
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
                        'Q5': 'What is the chemical symbol for gold?', 'A5': 'Au',
                        'Q6': 'What is the largest ocean on Earth?', 'A6': 'Pacific Ocean',
                        'Q7': 'Who wrote "Romeo and Juliet"?', 'A7': 'William Shakespeare',
                        'Q8': 'What is the main component of the sun?', 'A8': 'Hydrogen',
                        'Q9': 'What is the smallest country in the world?', 'A9': 'Vatican City',
                        'Q10': 'What is the largest mammal?', 'A10': 'Blue Whale',
                        'Q11': 'What is the capital of Japan?', 'A11': 'Tokyo',
                        'Q12': 'How many continents are there?', 'A12': '7',
                        'Q13': 'What is the chemical symbol for water?', 'A13': 'H2O',
                        'Q14': 'Who painted the Mona Lisa?', 'A14': 'Leonardo da Vinci',
                        'Q15': 'What is the largest desert in the world?', 'A15': 'Sahara Desert',
                        'Q16': 'What is the capital of Australia?', 'A16': 'Canberra',
                        'Q17': 'How many bones are in the human body?', 'A17': '206',
                        'Q18': 'What is the largest bird?', 'A18': 'Ostrich',
                        'Q19': 'What is the capital of Brazil?', 'A19': 'Brasília',
                        'Q20': 'What is the largest lake in the world?', 'A20': 'Caspian Sea',
                        'Q21': 'What is the capital of Egypt?', 'A21': 'Cairo',
                        'Q22': 'How many planets are in our solar system?', 'A22': '8',
                        'Q23': 'What is the largest mountain in the world?', 'A23': 'Mount Everest',
                        'Q24': 'What is the capital of India?', 'A24': 'New Delhi',
                        'Q25': 'What is the largest river in the world?', 'A25': 'Nile',
                        'Q26': 'What is the capital of China?', 'A26': 'Beijing',
                        'Q27': 'How many states are in the USA?', 'A27': '50',
                        'Q28': 'What is the largest island in the world?', 'A28': 'Greenland',
                        'Q29': 'What is the capital of Russia?', 'A29': 'Moscow',
                        'Q30': 'What is the largest volcano in the world?', 'A30': 'Mauna Loa',
                        'Q31': 'What is the capital of Canada?', 'A31': 'Ottawa',
                        'Q32': 'How many chromosomes do humans have?', 'A32': '46',
                        'Q33': 'What is the largest waterfall in the world?', 'A33': 'Angel Falls',
                        'Q34': 'What is the capital of Mexico?', 'A34': 'Mexico City',
                        'Q35': 'What is the largest coral reef?', 'A35': 'Great Barrier Reef',
                        'Q36': 'What is the capital of Argentina?', 'A36': 'Buenos Aires',
                        'Q37': 'How many moons does Earth have?', 'A37': '1',
                        'Q38': 'What is the largest forest in the world?', 'A38': 'Amazon Rainforest',
                        'Q39': 'What is the capital of South Africa?', 'A39': 'Pretoria',
                        'Q40': 'What is the largest canyon in the world?', 'A40': 'Grand Canyon',
                        'Q41': 'What is the capital of Germany?', 'A41': 'Berlin',
                        'Q42': 'How many elements are in the periodic table?', 'A42': '118',
                        'Q43': 'What is the largest glacier in the world?', 'A43': 'Lambert Glacier',
                        'Q44': 'What is the capital of Italy?', 'A44': 'Rome',
                        'Q45': 'What is the largest bay in the world?', 'A45': 'Bay of Bengal',
                        'Q46': 'What is the capital of Spain?', 'A46': 'Madrid',
                        'Q47': 'How many muscles are in the human body?', 'A47': '600',
                        'Q48': 'What is the largest peninsula in the world?', 'A48': 'Arabian Peninsula',
                        'Q49': 'What is the capital of Portugal?', 'A49': 'Lisbon',
                        'Q50': 'What is the largest gulf in the world?', 'A50': 'Gulf of Mexico',
                        'Q51': 'What is the capital of Netherlands?', 'A51': 'Amsterdam',
                        'Q52': 'How many teeth do adults have?', 'A52': '32',
                        'Q53': 'What is the largest archipelago in the world?', 'A53': 'Malay Archipelago',
                        'Q54': 'What is the capital of Belgium?', 'A54': 'Brussels',
                        'Q55': 'What is the largest strait in the world?', 'A55': 'Strait of Malacca',
                        'Q56': 'What is the capital of Switzerland?', 'A56': 'Bern',
                        'Q57': 'How many blood types are there?', 'A57': '8',
                        'Q58': 'What is the largest atoll in the world?', 'A58': 'Great Chagos Bank',
                        'Q59': 'What is the capital of Austria?', 'A59': 'Vienna',
                        'Q60': 'What is the largest lagoon in the world?', 'A60': 'New Caledonia'
                    },
                    'category1_2cat': { // Science & Nature (for 2 categories)
                        'Q1': 'What is the hardest natural substance on Earth?', 'A1': 'Diamond',
                        'Q2': 'What gas do plants absorb from the air?', 'A2': 'Carbon dioxide',
                        'Q3': 'What is the largest organ in the human body?', 'A3': 'Skin',
                        'Q4': 'What is the study of fossils called?', 'A4': 'Paleontology',
                        'Q5': 'What is the closest star to Earth?', 'A5': 'Sun',
                        'Q6': 'What is the chemical symbol for oxygen?', 'A6': 'O',
                        'Q7': 'What is the largest cell in the human body?', 'A7': 'Egg cell',
                        'Q8': 'What is the study of weather called?', 'A8': 'Meteorology',
                        'Q9': 'What is the smallest unit of life?', 'A9': 'Cell',
                        'Q10': 'What is the chemical symbol for carbon?', 'A10': 'C',
                        'Q11': 'What is the largest bone in the human body?', 'A11': 'Femur',
                        'Q12': 'What is the study of animals called?', 'A12': 'Zoology',
                        'Q13': 'What is the chemical symbol for nitrogen?', 'A13': 'N',
                        'Q14': 'What is the largest muscle in the human body?', 'A14': 'Gluteus maximus',
                        'Q15': 'What is the study of plants called?', 'A15': 'Botany',
                        'Q16': 'What is the chemical symbol for iron?', 'A16': 'Fe',
                        'Q17': 'What is the largest artery in the human body?', 'A17': 'Aorta',
                        'Q18': 'What is the study of the Earth called?', 'A18': 'Geology',
                        'Q19': 'What is the chemical symbol for gold?', 'A19': 'Au',
                        'Q20': 'What is the largest vein in the human body?', 'A20': 'Vena cava',
                        'Q21': 'What is the study of the universe called?', 'A21': 'Astronomy',
                        'Q22': 'What is the chemical symbol for silver?', 'A22': 'Ag',
                        'Q23': 'What is the largest gland in the human body?', 'A23': 'Liver',
                        'Q24': 'What is the study of the ocean called?', 'A24': 'Oceanography',
                        'Q25': 'What is the chemical symbol for copper?', 'A25': 'Cu',
                        'Q26': 'What is the largest organelle in a cell?', 'A26': 'Nucleus',
                        'Q27': 'What is the study of the atmosphere called?', 'A27': 'Atmospheric science',
                        'Q28': 'What is the chemical symbol for zinc?', 'A28': 'Zn',
                        'Q29': 'What is the largest part of the brain?', 'A29': 'Cerebrum',
                        'Q30': 'What is the study of the environment called?', 'A30': 'Ecology',
                        'Q31': 'What is the chemical symbol for lead?', 'A31': 'Pb',
                        'Q32': 'What is the largest nerve in the human body?', 'A32': 'Sciatic nerve',
                        'Q33': 'What is the study of the mind called?', 'A33': 'Psychology',
                        'Q34': 'What is the chemical symbol for mercury?', 'A34': 'Hg',
                        'Q35': 'What is the largest blood vessel?', 'A35': 'Aorta',
                        'Q36': 'What is the study of the past called?', 'A36': 'History',
                        'Q37': 'What is the chemical symbol for tin?', 'A37': 'Sn',
                        'Q38': 'What is the largest joint in the human body?', 'A38': 'Knee',
                        'Q39': 'What is the study of society called?', 'A39': 'Sociology',
                        'Q40': 'What is the chemical symbol for aluminum?', 'A40': 'Al',
                        'Q41': 'What is the largest tendon in the human body?', 'A41': 'Achilles tendon',
                        'Q42': 'What is the study of language called?', 'A42': 'Linguistics',
                        'Q43': 'What is the chemical symbol for silicon?', 'A43': 'Si',
                        'Q44': 'What is the largest ligament in the human body?', 'A44': 'Anterior cruciate ligament',
                        'Q45': 'What is the study of numbers called?', 'A45': 'Mathematics',
                        'Q46': 'What is the chemical symbol for phosphorus?', 'A46': 'P',
                        'Q47': 'What is the largest cartilage in the human body?', 'A47': 'Thyroid cartilage',
                        'Q48': 'What is the study of matter called?', 'A48': 'Physics',
                        'Q49': 'What is the chemical symbol for sulfur?', 'A49': 'S',
                        'Q50': 'What is the largest membrane in the human body?', 'A50': 'Skin',
                        'Q51': 'What is the study of energy called?', 'A51': 'Thermodynamics',
                        'Q52': 'What is the chemical symbol for chlorine?', 'A52': 'Cl',
                        'Q53': 'What is the largest cavity in the human body?', 'A53': 'Abdominal cavity',
                        'Q54': 'What is the study of light called?', 'A54': 'Optics',
                        'Q55': 'What is the chemical symbol for argon?', 'A55': 'Ar',
                        'Q56': 'What is the largest system in the human body?', 'A56': 'Integumentary system',
                        'Q57': 'What is the study of sound called?', 'A57': 'Acoustics',
                        'Q58': 'What is the chemical symbol for neon?', 'A58': 'Ne',
                        'Q59': 'What is the largest tissue in the human body?', 'A59': 'Connective tissue',
                        'Q60': 'What is the study of electricity called?', 'A60': 'Electromagnetism'
                    },
                    'category2_2cat': { // Entertainment & Pop Culture (for 2 categories)
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
                    'category1_3cat': { // Subject-Verb Agreement 
                        'Q1': 'A car and a bike is / are my means of transportation.', 'A1': 'are',
                        'Q2': 'Each student was / were given a test paper.', 'A2': 'was',
                        'Q3': 'Neither the students nor the teacher know / knows the correct answer.', 'A3': 'knows',
                        'Q4': 'Each of the players have / has submitted their final lineup.', 'A4': 'has',
                        'Q5': 'A. The bouquet of flowers are beautiful. B. The bouquet of flowers is beautiful.', 'A5': 'B',
                        'Q6': 'The team of players is / are ready for the match.', 'A6': 'is',
                        'Q7': 'Either John or his friends is / are coming to the party.', 'A7': 'are',
                        'Q8': 'The committee have / has made their decision.', 'A8': 'has',
                        'Q9': 'Neither the book nor the magazines was / were found.', 'A9': 'were',
                        'Q10': 'The family is / are going on vacation.', 'A10': 'is',
                        'Q11': 'Some of the students has / have completed the assignment.', 'A11': 'have',
                        'Q12': 'The news is / are spreading quickly.', 'A12': 'is',
                        'Q13': 'Both the teacher and the students was / were present.', 'A13': 'were',
                        'Q14': 'The majority of voters support / supports the new policy.', 'A14': 'support',
                        'Q15': 'The staff is / are having a meeting.', 'A15': 'is',
                        'Q16': 'Not only the students but also the teacher was / were late.', 'A16': 'was',
                        'Q17': 'The police is / are investigating the case.', 'A17': 'are',
                        'Q18': 'The audience was / were clapping loudly.', 'A18': 'was',
                        'Q19': 'Either the cat or the dogs is / are making noise.', 'A19': 'are',
                        'Q20': 'The jury have / has reached a verdict.', 'A20': 'has',
                        'Q21': 'The class is / are taking a test.', 'A21': 'is',
                        'Q22': 'Neither the manager nor the employees was / were satisfied.', 'A22': 'were',
                        'Q23': 'The government is / are implementing new policies.', 'A23': 'is',
                        'Q24': 'Both the car and the motorcycle need / needs repair.', 'A24': 'need',
                        'Q25': 'The public is / are demanding answers.', 'A25': 'is',
                        'Q26': 'Either the president or the vice president is / are attending.', 'A26': 'is',
                        'Q27': 'The faculty have / has approved the curriculum.', 'A27': 'has',
                        'Q28': 'The crowd was / were cheering for their team.', 'A28': 'was',
                        'Q29': 'Not only the children but also the parent was / were excited.', 'A29': 'was',
                        'Q30': 'The board of directors is / are meeting today.', 'A30': 'is',
                        'Q31': 'The majority of people believe / believes in climate change.', 'A31': 'believe',
                        'Q32': 'The crew is / are preparing for takeoff.', 'A32': 'is',
                        'Q33': 'Either the teacher or the students is / are responsible.', 'A33': 'are',
                        'Q34': 'The council have / has made a decision.', 'A34': 'has',
                        'Q35': 'The orchestra was / were performing beautifully.', 'A35': 'was',
                        'Q36': 'Neither the book nor the movie was / were interesting.', 'A36': 'was',
                        'Q37': 'The staff is / are working overtime.', 'A37': 'is',
                        'Q38': 'Both the cat and the dog like / likes to play.', 'A38': 'like',
                        'Q39': 'The committee is / are divided on this issue.', 'A39': 'is',
                        'Q40': 'The public have / has the right to know.', 'A40': 'has',
                        'Q41': 'Either the doctor or the nurses is / are available.', 'A41': 'are',
                        'Q42': 'The team is / are celebrating their victory.', 'A42': 'is',
                        'Q43': 'The majority of voters has / have spoken.', 'A43': 'have',
                        'Q44': 'The family is / are going to the beach.', 'A44': 'is',
                        'Q45': 'Not only the students but also the teacher was / were surprised.', 'A45': 'was',
                        'Q46': 'The police is / are patrolling the area.', 'A46': 'are',
                        'Q47': 'The audience was / were captivated by the performance.', 'A47': 'was',
                        'Q48': 'Either the manager or the employees is / are responsible.', 'A48': 'are',
                        'Q49': 'The jury is / are deliberating the case.', 'A49': 'is',
                        'Q50': 'The class is / are studying for the exam.', 'A50': 'is',
                        'Q51': 'Neither the teacher nor the students was / were prepared.', 'A51': 'were',
                        'Q52': 'The government is / are passing new laws.', 'A52': 'is',
                        'Q53': 'Both the car and the truck need / needs maintenance.', 'A53': 'need',
                        'Q54': 'The public is / are concerned about safety.', 'A54': 'is',
                        'Q55': 'Either the president or the secretary is / are available.', 'A55': 'is',
                        'Q56': 'The faculty is / are reviewing the proposals.', 'A56': 'is',
                        'Q57': 'The crowd was / were gathering in the square.', 'A57': 'was',
                        'Q58': 'Not only the children but also the parents was / were invited.', 'A58': 'were',
                        'Q59': 'The board is / are discussing the budget.', 'A59': 'is',
                        'Q60': 'The majority of citizens support / supports the mayor.', 'A60': 'support'
                    },
                    'category2_3cat': { // Tenses of Verbs
                        'Q1': 'She go / goes to the gym every morning.', 'A1': 'goes',
                        'Q2': 'I was watching / watched a documentary when the lights went out.', 'A2': 'was watching',
                        'Q3': 'They have been worked / have been working on this case for weeks.', 'A3': 'have been working',
                        'Q4': 'By the time you arrive, she will have left / will leave the office.', 'A4': 'will have left',
                        'Q5': 'Which One is Right?   A. She said she would come if she finished her task early.  B. She said she will come if she finishes her task early. ', 'A5': 'A',
                        'Q6': 'He work / works in a bank.', 'A6': 'works',
                        'Q7': 'They were playing / played football when it started to rain.', 'A7': 'were playing',
                        'Q8': 'I have lived / have been living here for five years.', 'A8': 'have been living',
                        'Q9': 'By next week, I will have finished / will finish the project.', 'A9': 'will have finished',
                        'Q10': 'She told me she would call / will call me later.', 'A10': 'would call',
                        'Q11': 'The sun rise / rises in the east.', 'A11': 'rises',
                        'Q12': 'We were having / had dinner when the phone rang.', 'A12': 'were having',
                        'Q13': 'He has worked / has been working here since 2010.', 'A13': 'has been working',
                        'Q14': 'By the end of the month, she will have saved / will save enough money.', 'A14': 'will have saved',
                        'Q15': 'He said he would help / will help us tomorrow.', 'A15': 'would help',
                        'Q16': 'Water boil / boils at 100 degrees Celsius.', 'A16': 'boils',
                        'Q17': 'I was reading / read a book when you called.', 'A17': 'was reading',
                        'Q18': 'They have known / have been knowing each other for years.', 'A18': 'have known',
                        'Q19': 'By tomorrow, I will have completed / will complete the assignment.', 'A19': 'will have completed',
                        'Q20': 'She promised she would visit / will visit us next week.', 'A20': 'would visit',
                        'Q21': 'The train arrive / arrives at 3 PM.', 'A21': 'arrives',
                        'Q22': 'He was cooking / cooked dinner when I arrived.', 'A22': 'was cooking',
                        'Q23': 'I have studied / have been studying English for ten years.', 'A23': 'have been studying',
                        'Q24': 'By next year, they will have built / will build the new bridge.', 'A24': 'will have built',
                        'Q25': 'He mentioned he would attend / will attend the meeting.', 'A25': 'would attend',
                        'Q26': 'The earth revolve / revolves around the sun.', 'A26': 'revolves',
                        'Q27': 'We were watching / watched TV when the power went out.', 'A27': 'were watching',
                        'Q28': 'She has taught / has been teaching for twenty years.', 'A28': 'has been teaching',
                        'Q29': 'By the deadline, I will have submitted / will submit the report.', 'A29': 'will have submitted',
                        'Q30': 'They assured us they would deliver / will deliver on time.', 'A30': 'would deliver',
                        'Q31': 'The clock strike / strikes twelve at midnight.', 'A31': 'strikes',
                        'Q32': 'He was driving / drove when the accident happened.', 'A32': 'was driving',
                        'Q33': 'I have visited / have been visiting Paris three times.', 'A33': 'have visited',
                        'Q34': 'By the weekend, she will have finished / will finish the book.', 'A34': 'will have finished',
                        'Q35': 'He said he would return / will return the book tomorrow.', 'A35': 'would return',
                        'Q36': 'The moon orbit / orbits the earth.', 'A36': 'orbits',
                        'Q37': 'They were discussing / discussed the plan when I joined.', 'A37': 'were discussing',
                        'Q38': 'He has written / has been writing novels for fifteen years.', 'A38': 'has been writing',
                        'Q39': 'By the party, I will have prepared / will prepare everything.', 'A39': 'will have prepared',
                        'Q40': 'She promised she would call / will call me back.', 'A40': 'would call',
                        'Q41': 'The bell ring / rings at 8 AM.', 'A41': 'rings',
                        'Q42': 'I was sleeping / slept when the alarm went off.', 'A42': 'was sleeping',
                        'Q43': 'They have traveled / have been traveling around the world.', 'A43': 'have been traveling',
                        'Q44': 'By the concert, he will have practiced / will practice the song.', 'A44': 'will have practiced',
                        'Q45': 'He mentioned he would join / will join us for dinner.', 'A45': 'would join',
                        'Q46': 'The seasons change / changes throughout the year.', 'A46': 'change',
                        'Q47': 'She was painting / painted when I visited her.', 'A47': 'was painting',
                        'Q48': 'I have lived / have been living in this city all my life.', 'A48': 'have been living',
                        'Q49': 'By the exam, I will have studied / will study all the material.', 'A49': 'will have studied',
                        'Q50': 'They said they would support / will support our cause.', 'A50': 'would support',
                        'Q51': 'The wind blow / blows from the west.', 'A51': 'blows',
                        'Q52': 'He was working / worked overtime when I called.', 'A52': 'was working',
                        'Q53': 'She has played / has been playing piano since childhood.', 'A53': 'has been playing',
                        'Q54': 'By the wedding, they will have planned / will plan everything.', 'A54': 'will have planned',
                        'Q55': 'He assured us he would be / will be there on time.', 'A55': 'would be',
                        'Q56': 'The flowers bloom / blooms in spring.', 'A56': 'bloom',
                        'Q57': 'We were celebrating / celebrated when the news arrived.', 'A57': 'were celebrating',
                        'Q58': 'I have read / have been reading this book for a month.', 'A58': 'have been reading',
                        'Q59': 'By the deadline, she will have completed / will complete the project.', 'A59': 'will have completed',
                        'Q60': 'They promised they would help / will help us move.', 'A60': 'would help'
                    },
                    'category3_3cat': { // Correct Usage of Pronouns
                        'Q1': 'Maria and me / I went to the library after class.', 'A1': 'I',
                        'Q2': 'Each of the students must submit his or her / their project on time.', 'A2': 'his or her',
                        'Q3': 'Neither James nor the others brought his / their identification cards.', 'A3': 'their',
                        'Q4': 'It was she / her who submitted the winning entry.', 'A4': 'she',
                        'Q5': 'A. Who do you think they chose as the new class representative? B. Who do you think them chose as the new class representative? ', 'A5': 'A.',
                        'Q6': 'John and I / me are going to the movies.', 'A6': 'I',
                        'Q7': 'Everyone should bring their / his or her own lunch.', 'A7': 'his or her',
                        'Q8': 'The teacher gave the assignment to she / her.', 'A8': 'her',
                        'Q9': 'It was they / them who won the competition.', 'A9': 'they',
                        'Q10': 'Between you and I / me, I think he\'s wrong.', 'A10': 'me',
                        'Q11': 'Each student must complete their / his or her homework.', 'A11': 'his or her',
                        'Q12': 'The gift is for he / him.', 'A12': 'him',
                        'Q13': 'It was we / us who organized the event.', 'A13': 'we',
                        'Q14': 'Nobody knows the answer except I / me.', 'A14': 'me',
                        'Q15': 'Everyone brought their / his or her own supplies.', 'A15': 'his or her',
                        'Q16': 'The letter was addressed to she / her.', 'A16': 'her',
                        'Q17': 'It was they / them who called you.', 'A17': 'they',
                        'Q18': 'The secret is between you and I / me.', 'A18': 'me',
                        'Q19': 'Each person should do their / his or her best.', 'A19': 'his or her',
                        'Q20': 'The invitation was for he / him.', 'A20': 'him',
                        'Q21': 'It was we / us who found the solution.', 'A21': 'we',
                        'Q22': 'Nobody can help except I / me.', 'A22': 'me',
                        'Q23': 'Everyone should bring their / his or her own book.', 'A23': 'his or her',
                        'Q24': 'The message was for she / her.', 'A24': 'her',
                        'Q25': 'It was they / them who arrived first.', 'A25': 'they',
                        'Q26': 'The decision is between you and I / me.', 'A26': 'me',
                        'Q27': 'Each student must submit their / his or her assignment.', 'A27': 'his or her',
                        'Q28': 'The package was delivered to he / him.', 'A28': 'him',
                        'Q29': 'It was we / us who solved the problem.', 'A29': 'we',
                        'Q30': 'Nobody understands except I / me.', 'A30': 'me',
                        'Q31': 'Everyone should complete their / his or her work.', 'A31': 'his or her',
                        'Q32': 'The call was for she / her.', 'A32': 'her',
                        'Q33': 'It was they / them who organized the party.', 'A33': 'they',
                        'Q34': 'The choice is between you and I / me.', 'A34': 'me',
                        'Q35': 'Each person must bring their / his or her own equipment.', 'A35': 'his or her',
                        'Q36': 'The email was sent to he / him.', 'A36': 'him',
                        'Q37': 'It was we / us who planned the trip.', 'A37': 'we',
                        'Q38': 'Nobody can solve this except I / me.', 'A38': 'me',
                        'Q39': 'Everyone should do their / his or her own research.', 'A39': 'his or her',
                        'Q40': 'The letter was written to she / her.', 'A40': 'her',
                        'Q41': 'It was they / them who won the game.', 'A41': 'they',
                        'Q42': 'The agreement is between you and I / me.', 'A42': 'me',
                        'Q43': 'Each student must complete their / his or her project.', 'A43': 'his or her',
                        'Q44': 'The message was for he / him.', 'A44': 'him',
                        'Q45': 'It was we / us who found the answer.', 'A45': 'we',
                        'Q46': 'Nobody knows except I / me.', 'A46': 'me',
                        'Q47': 'Everyone should bring their / his or her own materials.', 'A47': 'his or her',
                        'Q48': 'The invitation was sent to she / her.', 'A48': 'her',
                        'Q49': 'It was they / them who started the project.', 'A49': 'they',
                        'Q50': 'The decision rests between you and I / me.', 'A50': 'me',
                        'Q51': 'Each person must submit their / his or her own work.', 'A51': 'his or her',
                        'Q52': 'The call was made to he / him.', 'A52': 'him',
                        'Q53': 'It was we / us who created the plan.', 'A53': 'we',
                        'Q54': 'Nobody can help except I / me.', 'A54': 'me',
                        'Q55': 'Everyone should complete their / his or her own task.', 'A55': 'his or her',
                        'Q56': 'The email was addressed to she / her.', 'A56': 'her',
                        'Q57': 'It was they / them who finished first.', 'A57': 'they',
                        'Q58': 'The choice lies between you and I / me.', 'A58': 'me',
                        'Q59': 'Each student must do their / his or her own homework.', 'A59': 'his or her',
                        'Q60': 'The package was delivered to he / him.', 'A60': 'him'
                    },
                    'category1_4cat': { // Science & Nature (for 4+ categories)
                        'Q1': 'What is the hardest natural substance on Earth?', 'A1': 'Diamond',
                        'Q2': 'What gas do plants absorb from the air?', 'A2': 'Carbon dioxide',
                        'Q3': 'What is the largest organ in the human body?', 'A3': 'Skin',
                        'Q4': 'What is the study of fossils called?', 'A4': 'Paleontology',
                        'Q5': 'What is the closest star to Earth?', 'A5': 'Sun',
                        'Q6': 'What is the chemical symbol for oxygen?', 'A6': 'O',
                        'Q7': 'What is the largest cell in the human body?', 'A7': 'Egg cell',
                        'Q8': 'What is the study of weather called?', 'A8': 'Meteorology',
                        'Q9': 'What is the smallest unit of life?', 'A9': 'Cell',
                        'Q10': 'What is the chemical symbol for carbon?', 'A10': 'C',
                        'Q11': 'What is the largest bone in the human body?', 'A11': 'Femur',
                        'Q12': 'What is the study of animals called?', 'A12': 'Zoology',
                        'Q13': 'What is the chemical symbol for nitrogen?', 'A13': 'N',
                        'Q14': 'What is the largest muscle in the human body?', 'A14': 'Gluteus maximus',
                        'Q15': 'What is the study of plants called?', 'A15': 'Botany',
                        'Q16': 'What is the chemical symbol for iron?', 'A16': 'Fe',
                        'Q17': 'What is the largest artery in the human body?', 'A17': 'Aorta',
                        'Q18': 'What is the study of the Earth called?', 'A18': 'Geology',
                        'Q19': 'What is the chemical symbol for gold?', 'A19': 'Au',
                        'Q20': 'What is the largest vein in the human body?', 'A20': 'Vena cava',
                        'Q21': 'What is the study of the universe called?', 'A21': 'Astronomy',
                        'Q22': 'What is the chemical symbol for silver?', 'A22': 'Ag',
                        'Q23': 'What is the largest gland in the human body?', 'A23': 'Liver',
                        'Q24': 'What is the study of the ocean called?', 'A24': 'Oceanography',
                        'Q25': 'What is the chemical symbol for copper?', 'A25': 'Cu',
                        'Q26': 'What is the largest organelle in a cell?', 'A26': 'Nucleus',
                        'Q27': 'What is the study of the atmosphere called?', 'A27': 'Atmospheric science',
                        'Q28': 'What is the chemical symbol for zinc?', 'A28': 'Zn',
                        'Q29': 'What is the largest part of the brain?', 'A29': 'Cerebrum',
                        'Q30': 'What is the study of the environment called?', 'A30': 'Ecology',
                        'Q31': 'What is the chemical symbol for lead?', 'A31': 'Pb',
                        'Q32': 'What is the largest nerve in the human body?', 'A32': 'Sciatic nerve',
                        'Q33': 'What is the study of the mind called?', 'A33': 'Psychology',
                        'Q34': 'What is the chemical symbol for mercury?', 'A34': 'Hg',
                        'Q35': 'What is the largest blood vessel?', 'A35': 'Aorta',
                        'Q36': 'What is the study of the past called?', 'A36': 'History',
                        'Q37': 'What is the chemical symbol for tin?', 'A37': 'Sn',
                        'Q38': 'What is the largest joint in the human body?', 'A38': 'Knee',
                        'Q39': 'What is the study of society called?', 'A39': 'Sociology',
                        'Q40': 'What is the chemical symbol for aluminum?', 'A40': 'Al',
                        'Q41': 'What is the largest tendon in the human body?', 'A41': 'Achilles tendon',
                        'Q42': 'What is the study of language called?', 'A42': 'Linguistics',
                        'Q43': 'What is the chemical symbol for silicon?', 'A43': 'Si',
                        'Q44': 'What is the largest ligament in the human body?', 'A44': 'Anterior cruciate ligament',
                        'Q45': 'What is the study of numbers called?', 'A45': 'Mathematics',
                        'Q46': 'What is the chemical symbol for phosphorus?', 'A46': 'P',
                        'Q47': 'What is the largest cartilage in the human body?', 'A47': 'Thyroid cartilage',
                        'Q48': 'What is the study of matter called?', 'A48': 'Physics',
                        'Q49': 'What is the chemical symbol for sulfur?', 'A49': 'S',
                        'Q50': 'What is the largest membrane in the human body?', 'A50': 'Skin',
                        'Q51': 'What is the study of energy called?', 'A51': 'Thermodynamics',
                        'Q52': 'What is the chemical symbol for chlorine?', 'A52': 'Cl',
                        'Q53': 'What is the largest cavity in the human body?', 'A53': 'Abdominal cavity',
                        'Q54': 'What is the study of light called?', 'A54': 'Optics',
                        'Q55': 'What is the chemical symbol for argon?', 'A55': 'Ar',
                        'Q56': 'What is the largest system in the human body?', 'A56': 'Integumentary system',
                        'Q57': 'What is the study of sound called?', 'A57': 'Acoustics',
                        'Q58': 'What is the chemical symbol for neon?', 'A58': 'Ne',
                        'Q59': 'What is the largest tissue in the human body?', 'A59': 'Connective tissue',
                        'Q60': 'What is the study of electricity called?', 'A60': 'Electromagnetism'
                    },
                    'category2_4cat': { // Entertainment & Pop Culture (for 4+ categories)
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
                    'category4_4cat': { // History & Politics (for 4+ categories)
                        'Q1': 'In what year did World War II end?', 'A1': '1945',
                        'Q2': 'Who was the first President of the United States?', 'A2': 'George Washington',
                        'Q3': 'What ancient wonder was located in Alexandria?', 'A3': 'Lighthouse',
                        'Q4': 'What year did the Berlin Wall fall?', 'A4': '1989',
                        'Q5': 'Who was the first female Prime Minister of the UK?', 'A5': 'Margaret Thatcher'
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
                        'Q5': 'What mountain range runs through South America?', 'A5': 'Andes'
                    },
                    'category7': { // Food & Cooking
                        'Q1': 'What is the main ingredient in guacamole?', 'A1': 'Avocado',
                        'Q2': 'What country is known for inventing pizza?', 'A2': 'Italy',
                        'Q3': 'What is the most consumed meat in the world?', 'A3': 'Pork',
                        'Q4': 'What is the national dish of Japan?', 'A4': 'Sushi',
                        'Q5': 'What is the world\'s most expensive spice?', 'A5': 'Saffron'
                    },
                    'category8': { // Literature & Arts
                        'Q1': 'Who wrote "Romeo and Juliet"?', 'A1': 'William Shakespeare',
                        'Q2': 'What is the name of the famous painting by Leonardo da Vinci?', 'A2': 'Mona Lisa',
                        'Q3': 'Who wrote "Pride and Prejudice"?', 'A3': 'Jane Austen',
                        'Q4': 'What is the name of the famous sculpture by Michelangelo?', 'A4': 'David',
                        'Q5': 'Who wrote "The Great Gatsby"?', 'A5': 'F. Scott Fitzgerald'
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
                        // Map category numbers to available data
                        const categoryMap = {
                            1: sampleData['category1'],
                            2: sampleData['category2_4cat'], // Use 4cat data for category 2
                            3: sampleData['category3_4cat'], // Use 4cat data for category 3
                            4: sampleData['category4_4cat'], // Use 4cat data for category 4
                            5: sampleData['category5'],
                            6: sampleData['category6'],
                            7: sampleData['category7'],
                            8: sampleData['category8']
                        };
                        categoryData = categoryMap[i];
                    }
                    
                    if (categoryData) {
                        // Fill questions up to the selected question count (now supports up to 60)
                        for (let j = 1; j <= Math.min(questionCount, 60); j++) {
                            const questionElement = document.getElementById(`category${i}Q${j}`);
                            const answerElement = document.getElementById(`category${i}A${j}`);
                            if (questionElement && answerElement) {
                                // Use the sample data if available, otherwise generate generic questions
                                if (categoryData[`Q${j}`] && categoryData[`A${j}`]) {
                                questionElement.value = categoryData[`Q${j}`];
                                answerElement.value = categoryData[`A${j}`];
                                } else {
                                    // Generate generic questions for missing data
                                    questionElement.value = `Sample question ${j} for category ${i}`;
                                    answerElement.value = `Sample answer ${j} for category ${i}`;
                                }
                            }
                        }
                    }
                }
                
                console.log('Sample data filled successfully!');
            }, 200);
        }

        function testForm() {
            console.log('Testing form data collection...');
            
            // Test if we can collect basic form data
            const categoryCount = parseInt(document.getElementById('categoryCount').value);
            const gameTimer = parseInt(document.getElementById('gameTimer').value);
            const questionTimer = parseInt(document.getElementById('questionTimer').value);
            const teamCount = parseInt(document.getElementById('customTeamCount').value);
            const questionCount = parseInt(document.getElementById('questionCount').value);
            
            console.log('Basic form data:', {
                categoryCount,
                gameTimer,
                questionTimer,
                teamCount,
                questionCount
            });
            
            // Test if we can collect team names
            const teamNames = [];
            for (let i = 1; i <= teamCount; i++) {
                const teamNameInput = document.getElementById(`customTeam${i}Name`);
                if (teamNameInput) {
                    const teamName = teamNameInput.value.trim();
                    teamNames.push(teamName || `Player ${i}`);
                } else {
                    teamNames.push(`Player ${i}`);
                }
            }
            
            console.log('Team names:', teamNames);
            
            // Test if we can collect categories
            const categories = {};
            for (let i = 1; i <= categoryCount; i++) {
                const categoryName = document.getElementById(`category${i}Name`);
                if (categoryName) {
                    console.log(`Category ${i} name:`, categoryName.value);
                    categories[categoryName.value.trim() || `Category ${i}`] = {};
                    
                    for (let j = 1; j <= questionCount; j++) {
                        const question = document.getElementById(`category${i}Q${j}`);
                        const answer = document.getElementById(`category${i}A${j}`);
                        if (question && answer) {
                            categories[categoryName.value.trim() || `Category ${i}`][j] = [{
                                question: question.value.trim() || `Question ${j}`,
                                answer: answer.value.trim() || `Answer ${j}`
                            }];
                        }
                    }
                }
            }
            
            console.log('Categories collected:', categories);
            
            alert('Form test completed! Check console for details.');
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
