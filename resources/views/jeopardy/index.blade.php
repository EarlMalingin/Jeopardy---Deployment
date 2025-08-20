<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Jeopardy Game</title>
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
        
        .menu-button {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .menu-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .menu-button:active {
            transform: translateY(0);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
        }
        
        .menu-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .menu-button:hover::before {
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
        
        @keyframes particle-float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-100px) rotate(180deg); opacity: 0; }
        }
        
        /* Prevent scroll bar flickering */
        html, body {
            overflow-x: hidden;
            overflow-y: auto;
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            -webkit-overflow-scrolling: touch;
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
            width: 3px;
            height: 3px;
            background: #3b82f6;
            border-radius: 50%;
            opacity: 0.6;
            animation: particle-float 8s infinite linear;
            pointer-events: none;
            z-index: 1;
        }
        
        /* Mobile-specific optimizations */
        @media (max-width: 768px) {
            .menu-button {
                min-height: 100px;
                padding: 1.5rem !important;
            }
            
            .menu-button .text-5xl {
                font-size: 2.5rem;
            }
            
            .menu-button .w-16 {
                width: 3rem;
                height: 3rem;
            }
            
            .menu-button .w-10 {
                width: 1.5rem;
                height: 1.5rem;
            }
            
            .feature-badges {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .feature-badges span {
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 480px) {
            .menu-button {
                min-height: 90px;
                padding: 1rem !important;
            }
            
            .menu-button h3 {
                font-size: 1.25rem !important;
            }
            
            .menu-button p {
                font-size: 0.875rem !important;
            }
        }
        
        /* Touch-friendly button improvements */
        .touch-button {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        /* Modal improvements for mobile */
        .mobile-modal {
            padding: 1rem;
            margin: 0.5rem;
            max-height: 85vh;
        }
        
        @media (max-width: 768px) {
            .mobile-modal {
                padding: 0.75rem;
                margin: 0.25rem;
                max-height: 90vh;
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-white main-container">
    <!-- Background Particles -->
    <div id="particles" class="absolute inset-0 pointer-events-none"></div>
    
    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col justify-center p-2 sm:p-4 py-4 sm:py-8">
        <div class="max-w-4xl w-full mx-auto">
            <!-- Title Section -->
            <div class="text-center mb-8 sm:mb-12">
                <div class="floating-animation mb-4 sm:mb-6">
                    <h1 class="text-4xl sm:text-6xl md:text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 glow-text leading-tight">
                        🎯 JEOPARDY
                    </h1>
                </div>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-300 mb-6 sm:mb-8 max-w-2xl mx-auto px-2">
                    The ultimate trivia challenge where grammar meets excitement!
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-400 feature-badges">
                    <span class="flex items-center justify-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        2-6 Teams
                    </span>
                    <span class="flex items-center justify-center">
                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                        3 Difficulty Levels
                    </span>
                    <span class="flex items-center justify-center">
                        <span class="w-2 h-2 bg-pink-500 rounded-full mr-2"></span>
                        Dynamic Scoring
                    </span>
                </div>
            </div>

            <!-- Menu Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-w-4xl mx-auto">
                <!-- Start New Game -->
                <div class="menu-button rounded-2xl p-4 sm:p-8 text-center cursor-pointer touch-button" onclick="window.location.href='/jeopardy/setup'">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6 flex justify-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Start New Game</h3>
                    <p class="text-sm sm:text-base text-gray-300">Begin an exciting trivia battle with 2-6 teams</p>
                </div>

                <!-- How to Play -->
                <div class="menu-button rounded-2xl p-4 sm:p-8 text-center cursor-pointer touch-button" onclick="showRules()">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6 flex justify-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">How to Play</h3>
                    <p class="text-sm sm:text-base text-gray-300">Learn the rules and strategies</p>
                </div>

                <!-- Adaptors of Game-Based Learning -->
                <div class="menu-button rounded-2xl p-4 sm:p-8 text-center cursor-pointer touch-button" onclick="showAdaptors()">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6 flex justify-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Adaptors of Game-Based Learning</h3>
                    <p class="text-sm sm:text-base text-gray-300">Meet the students who created this game</p>
                </div>

                <!-- Create/Join Lobby -->
                <div class="menu-button rounded-2xl p-4 sm:p-8 text-center cursor-pointer touch-button" onclick="window.location.href='/jeopardy/lobby'">
                    <div class="text-4xl sm:text-5xl mb-4 sm:mb-6 flex justify-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Create/Join Lobby</h3>
                    <p class="text-sm sm:text-base text-gray-300">Create a new lobby or join an existing one</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 sm:mt-12">
                <p class="text-gray-400 text-xs sm:text-sm px-2">
                    Challenge your knowledge • Test your speed • Have fun!
                </p>
            </div>
        </div>
    </div>

    <!-- Rules Modal -->
    <div id="rulesModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
        <div class="absolute inset-0 bg-black bg-opacity-75"></div>
        <div class="relative bg-gray-800 rounded-2xl p-4 sm:p-8 max-w-2xl w-full mx-2 sm:mx-4 max-h-[85vh] sm:max-h-[80vh] overflow-y-auto mobile-modal">
            <div class="text-center mb-4 sm:mb-6">
                <h2 class="text-2xl sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-2 sm:mb-4">
                    📖 How to Play Jeopardy
                </h2>
            </div>
            
            <div class="space-y-3 sm:space-y-4 text-gray-300">
                <div class="bg-gray-700 rounded-lg p-3 sm:p-4">
                    <h3 class="text-base sm:text-lg font-bold text-white mb-2">🎯 Game Setup</h3>
                    <p class="text-sm sm:text-base">• Choose between 2, 4, or 6 teams</p>
                    <p class="text-sm sm:text-base">• Enter names for each team</p>
                    <p class="text-sm sm:text-base">• Select game difficulty (Easy, Normal, Hard, Challenging)</p>
                    <p class="text-sm sm:text-base">• Each team starts with 0 points</p>
                    <p class="text-sm sm:text-base">• Teams take turns selecting questions</p>
                </div>
                
                <div class="bg-gray-700 rounded-lg p-3 sm:p-4">
                    <h3 class="text-base sm:text-lg font-bold text-white mb-2">⏱️ Timer System</h3>
                    <p class="text-sm sm:text-base">• Each team starts with 5 minutes (300 seconds)</p>
                    <p class="text-sm sm:text-base">• Each question has a 30-second timer</p>
                    <p class="text-sm sm:text-base">• Wrong answers deduct 30 seconds from team's timer</p>
                    <p class="text-sm sm:text-base">• When team timer reaches 0, they can't answer more questions</p>
                </div>
                
                <div class="bg-gray-700 rounded-lg p-3 sm:p-4">
                    <h3 class="text-base sm:text-lg font-bold text-white mb-2">🎯 Scoring</h3>
                    <p class="text-sm sm:text-base">• Correct answers: +points (1-5)</p>
                    <p class="text-sm sm:text-base">• Wrong answers: -points (minimum 0)</p>
                    <p class="text-sm sm:text-base">• Higher values = harder questions</p>
                    <p class="text-sm sm:text-base">• Difficulty affects point values:</p>
                    <p class="text-sm sm:text-base ml-4">- Easy: 0.5-2.5 points</p>
                    <p class="text-sm sm:text-base ml-4">- Normal: 1-5 points</p>
                    <p class="text-sm sm:text-base ml-4">- Hard: 1.5-7.5 points</p>
                    <p class="text-sm sm:text-base ml-4">- Challenging: 2-10 points</p>
                </div>
                
                <div class="bg-gray-700 rounded-lg p-3 sm:p-4">
                    <h3 class="text-base sm:text-lg font-bold text-white mb-2">🏆 Winning</h3>
                    <p class="text-sm sm:text-base">• Answer all 25 questions</p>
                    <p class="text-sm sm:text-base">• Team with highest score wins</p>
                    <p class="text-sm sm:text-base">• Ties are settled by final question</p>
                </div>
            </div>
            
            <div class="text-center mt-4 sm:mt-6">
                <button onclick="hideRules()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors text-sm sm:text-base touch-button">
                    Got it!
                </button>
            </div>
        </div>
    </div>

    <!-- Adaptors Modal -->
    <div id="adaptorsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
        <div class="absolute inset-0 bg-black bg-opacity-75"></div>
        <div class="relative bg-gray-800 rounded-2xl p-4 sm:p-8 max-w-4xl w-full mx-2 sm:mx-4 max-h-[90vh] overflow-y-auto mobile-modal">
            <div class="text-center mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-2 sm:mb-4">
                    🎓 Adaptors of Game-Based Learning
                </h2>
                <p class="text-base sm:text-xl text-gray-300 mb-4 sm:mb-6">Meet the brilliant students who created this educational Jeopardy game</p>
                
                <!-- Group Picture -->
                <div class="max-w-2xl mx-auto mb-4 sm:mb-6">
                    <div class="rounded-xl overflow-hidden border-2 sm:border-4 border-blue-500 shadow-2xl">
                        <img src="/img/group_pic.jpg" 
                             alt="Development Team Group Photo" 
                             class="w-full object-contain">
                    </div>
                    <p class="text-xs sm:text-sm text-gray-400 mt-2">The Research Team</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
                <!-- Team Member 1 -->
                <div class="bg-gray-700 rounded-xl p-4 sm:p-6 hover:bg-gray-600 transition-colors">
                    <div class="text-center mb-3 sm:mb-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto mb-2 sm:mb-3 overflow-hidden border-2 sm:border-4 border-blue-500">
                            <img src="/img/brigoli.jpg" 
                                 alt="Alexa C. Brigoli" 
                                 class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-1 sm:mb-2">Alexa C. Brigoli</h3>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1 sm:mb-2">alexabrigoli03@gmail.com</p>
                    </div>
                    <div class="text-gray-300 space-y-1 sm:space-y-2 text-sm sm:text-base">
                        <p>• Religion: Roman Catholic  </p>
                        <p>• Gender: Female</p>
                        <p>• Nationality: Filipino </p>
                        <p>• Civil Status: Single</p>
                        <p class="text-xs text-gray-400 mt-2 sm:mt-3 pt-2 border-t border-gray-600">
                            📍 Guadalupe, Monterrazas de Cebu
                        </p>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="bg-gray-700 rounded-xl p-4 sm:p-6 hover:bg-gray-600 transition-colors">
                    <div class="text-center mb-3 sm:mb-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto mb-2 sm:mb-3 overflow-hidden border-2 sm:border-4 border-green-500">
                            <img src="/img/Cinco.jpg" 
                                 alt="Sarah Chen" 
                                 class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-1 sm:mb-2">Michaela Gayle A. Cinco</h3>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1 sm:mb-2">michaelagaylecinco@gmail.com </p>
                        
                    </div>
                    <div class="text-gray-300 space-y-1 sm:space-y-2 text-sm sm:text-base">
                        <p>• Religion: Roman Catholic</p>
                        <p>• Gender: Female</p>
                        <p>• Nationality: Filipino</p>
                        <p>• Civil Status: Single</p>
                        <p class="text-xs text-gray-400 mt-2 sm:mt-3 pt-2 border-t border-gray-600">
                            📍 Bankal, Ticgahon 4 Lapu-Lapu City
                        </p>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="bg-gray-700 rounded-xl p-4 sm:p-6 hover:bg-gray-600 transition-colors">
                    <div class="text-center mb-3 sm:mb-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto mb-2 sm:mb-3 overflow-hidden border-2 sm:border-4 border-purple-500">
                            <img src="/img/ecal.jpg" 
                                 alt="Michael Thompson" 
                                 class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-1 sm:mb-2">Mary-Ann P. Ecal</h3>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1 sm:mb-2">act.ecalmaryann@gmail.com</p>
                        
                    </div>
                    <div class="text-gray-300 space-y-1 sm:space-y-2 text-sm sm:text-base">
                        <p>• Religion: Roman Catholic</p>
                        <p>• Gender: Female</p>
                        <p>• Nationality: Filipino</p>
                        <p>• Civil Status: Single</p>
                        <p class="text-xs text-gray-400 mt-2 sm:mt-3 pt-2 border-t border-gray-600">
                            📍 Calawisan, Lapu-Lapu City Cebu
                        </p>
                    </div>
                </div>

                <!-- Team Member 4 -->
                <div class="bg-gray-700 rounded-xl p-4 sm:p-6 hover:bg-gray-600 transition-colors">
                    <div class="text-center mb-3 sm:mb-4">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto mb-2 sm:mb-3 overflow-hidden border-2 sm:border-4 border-yellow-500">
                            <img src="/img/talaugon.jpg" 
                                 alt="Emily Davis" 
                                 class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-1 sm:mb-2">Christyl Ann Talaugon</h3>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1 sm:mb-2">samanthanogualat@gmail.con</p>
                    </div>
                    <div class="text-gray-300 space-y-1 sm:space-y-2 text-sm sm:text-base">
                        <p>• Religion: Roman Catholic</p>
                        <p>• Gender: Female</p>
                        <p>• Nationality: Filipino</p>
                        <p>• Civil Status: Single</p>
                        <p class="text-xs text-gray-400 mt-2 sm:mt-3 pt-2 border-t border-gray-600">
                            📍 B.Rodriguez Ext., Cebu City
                        </p>
                    </div>
                </div>
            </div>

            <!-- Project Information -->
            <div class="mt-6 sm:mt-8 bg-gray-700 rounded-xl p-4 sm:p-6">
                <h3 class="text-xl sm:text-2xl font-bold text-white mb-3 sm:mb-4 text-center">🎯 Project Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 text-gray-300">
                    <div>
                        <h4 class="text-base sm:text-lg font-bold text-blue-400 mb-2">Educational Goals</h4>
                        <ul class="space-y-1 text-sm sm:text-base">
                            <li>• Enhance grammar learning through gamification</li>
                            <li>• Provide interactive learning experiences</li>
                            <li>• Support multiple difficulty levels</li>
                            <li>• Encourage collaborative learning</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-base sm:text-lg font-bold text-green-400 mb-2">Technical Features</h4>
                        <ul class="space-y-1 text-sm sm:text-base">
                            <li>• Real-time multiplayer functionality</li>
                            <li>• Custom game creation tools</li>
                            <li>• Responsive design for all devices</li>
                            <li>• Advanced scoring and timer systems</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="text-center mt-6 sm:mt-8">
                <button onclick="hideAdaptors()" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-2 sm:py-3 px-6 sm:px-8 rounded-lg transition-all duration-300 transform hover:scale-105 text-sm sm:text-base touch-button">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
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

        function showRules() {
            document.getElementById('rulesModal').classList.remove('hidden');
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function hideRules() {
            document.getElementById('rulesModal').classList.add('hidden');
            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        function showAdaptors() {
            document.getElementById('adaptorsModal').classList.remove('hidden');
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function hideAdaptors() {
            document.getElementById('adaptorsModal').classList.add('hidden');
            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const rulesModal = document.getElementById('rulesModal');
            const adaptorsModal = document.getElementById('adaptorsModal');
            
            if (event.target === rulesModal) {
                hideRules();
            }
            if (event.target === adaptorsModal) {
                hideAdaptors();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideRules();
                hideAdaptors();
            }
        });

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            
            // Recreate particles on window resize for better mobile experience
            window.addEventListener('resize', () => {
                setTimeout(createParticles, 100);
            });
        });
    </script>
</body>
</html>
