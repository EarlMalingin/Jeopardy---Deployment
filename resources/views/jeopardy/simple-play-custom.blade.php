<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Simple Custom Jeopardy Game</title>
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
        
        .timer-ring {
            stroke-dasharray: 283;
            stroke-dashoffset: 283;
            transition: stroke-dashoffset 1s linear;
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
            top: 4px;
            right: 4px;
            font-size: 0.5rem;
            font-weight: bold;
            color: #fca5a5;
            background: rgba(220, 38, 38, 0.2);
            padding: 1px 4px;
            border-radius: 2px;
            z-index: 2;
        }
        
        .answered.correct::after {
            content: 'COMPLETED';
            position: absolute;
            top: 4px;
            right: 4px;
            font-size: 0.5rem;
            font-weight: bold;
            color: #10b981;
            background: rgba(16, 185, 129, 0.2);
            padding: 1px 4px;
            border-radius: 2px;
            z-index: 2;
        }
        
        @media (max-width: 768px) {
            .answered.incorrect::after {
                content: 'X';
                font-size: 0.75rem;
                top: 2px;
                right: 2px;
                padding: 1px 2px;
            }
            
            .answered.correct::after {
                content: '✓';
                font-size: 0.75rem;
                top: 2px;
                right: 2px;
                padding: 1px 2px;
            }
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
            font-size: 1.5rem;
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
            font-size: 1.5rem;
            font-weight: bold;
            color: #fca5a5;
            text-shadow: 0 0 10px rgba(220, 38, 38, 0.5);
            z-index: 2;
        }
        
        @media (max-width: 768px) {
            .answered .checkmark {
                font-size: 1rem;
            }
            
            .answered .x-mark {
                font-size: 1rem;
            }
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
        
        /* Base game board styles - ensure proper grid structure */
        #gameBoard {
            display: grid !important;
            gap: 0.5rem !important;
            margin-bottom: 1rem !important;
            /* Grid template columns will be set by JavaScript to ensure proper alignment */
            grid-auto-flow: row !important; /* Ensure items flow in rows, not columns */
            grid-auto-columns: unset !important; /* Prevent auto-sizing of columns */
        }
        
        .question-value {
            font-size: 1rem !important;
            padding: 0.75rem !important;
            min-height: 60px;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }
        
        /* Category headers */
        #gameBoard > div:not([data-category]) {
            font-size: 1rem !important;
            padding: 0.75rem 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
        }
        
        /* Enhanced Mobile Game Board Responsiveness */
        
        /* Desktop and larger screen optimizations */
        @media (min-width: 1025px) {
            #gameBoard {
                gap: 0.75rem !important;
                margin-bottom: 2rem !important;
            }
            
            .question-value {
                font-size: 1.125rem !important;
                padding: 1rem !important;
                min-height: 80px;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 1.125rem !important;
                padding: 1rem 0.75rem !important;
            }
            
            /* Ensure proper grid structure on desktop - use exact category count */
            #gameBoard {
                display: grid !important;
                /* Grid template columns will be set by JavaScript based on category count */
            }
            
            /* Single category - center the 3-column layout */
            #gameBoard.single-category {
                grid-template-columns: repeat(3, 1fr) !important;
                max-width: 600px !important;
                margin: 0 auto 2rem auto !important;
            }
        }
        
        /* Tablet and medium screen optimizations */
        @media (min-width: 769px) and (max-width: 1024px) {
            #gameBoard {
                gap: 0.625rem !important;
                margin-bottom: 1.5rem !important;
            }
            
            .question-value {
                font-size: 1rem !important;
                padding: 0.75rem !important;
                min-height: 70px;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 1rem !important;
                padding: 0.875rem 0.625rem !important;
            }
            
            /* Ensure proper grid structure on tablets - use exact category count */
            #gameBoard {
                display: grid !important;
                /* Grid template columns will be set by JavaScript based on category count */
            }
            
            /* Single category - center the 3-column layout */
            #gameBoard.single-category {
                grid-template-columns: repeat(3, 1fr) !important;
                max-width: 500px !important;
                margin: 0 auto 1.5rem auto !important;
            }
        }
        
        @media (max-width: 1024px) {
            #gameBoard {
                gap: 0.5rem !important;
                margin-bottom: 1rem !important;
            }
            
            .question-value {
                font-size: 0.875rem !important;
                padding: 0.5rem !important;
                min-height: 55px;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 0.875rem !important;
                padding: 0.75rem 0.5rem !important;
            }
        }
        
        @media (max-width: 768px) {
            #gameBoard {
                gap: 0.375rem !important;
                margin-bottom: 0.75rem !important;
            }
            
            .question-value {
                font-size: 0.75rem !important;
                padding: 0.375rem !important;
                min-height: 50px;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 0.75rem !important;
                padding: 0.5rem 0.25rem !important;
            }
            
            /* Single category - keep 3-column layout but center it */
            #gameBoard.single-category {
                max-width: 300px !important;
                margin: 0 auto 1rem auto !important;
            }
            
            /* For multiple categories, maintain the original grid structure */
            /* Only apply responsive classes for very wide boards that need horizontal scroll */
            #gameBoard.many-categories {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
        }
        
        @media (max-width: 640px) {
            #gameBoard {
                gap: 0.25rem !important;
                margin-bottom: 0.5rem !important;
            }
            
            .question-value {
                font-size: 0.625rem !important;
                padding: 0.25rem !important;
                min-height: 45px;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 0.625rem !important;
                padding: 0.375rem 0.125rem !important;
            }
        }
        
        @media (max-width: 480px) {
            #gameBoard {
                gap: 0.125rem !important;
                margin-bottom: 0.375rem !important;
            }
            
            .question-value {
                font-size: 0.5rem !important;
                padding: 0.125rem !important;
                min-height: 40px;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 0.5rem !important;
                padding: 0.25rem 0.125rem !important;
            }
            
            /* Single category - keep centered layout */
            #gameBoard.single-category {
                max-width: 250px !important;
                margin: 0 auto 0.5rem auto !important;
            }
        }
        
        @media (max-width: 360px) {
            .question-value {
                font-size: 0.45rem !important;
                padding: 0.125rem !important;
                min-height: 35px;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 0.45rem !important;
                padding: 0.25rem 0.125rem !important;
            }
            
            /* Single category - keep centered layout */
            #gameBoard.single-category {
                max-width: 200px !important;
                margin: 0 auto 0.375rem auto !important;
            }
        }
        
        /* Horizontal scroll for very wide game boards */
        @media (max-width: 768px) {
            .game-board-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #4b5563 #1f2937;
            }
            
            .game-board-container::-webkit-scrollbar {
                height: 6px;
            }
            
            .game-board-container::-webkit-scrollbar-track {
                background: #1f2937;
                border-radius: 3px;
            }
            
            .game-board-container::-webkit-scrollbar-thumb {
                background: #4b5563;
                border-radius: 3px;
            }
            
            .game-board-container::-webkit-scrollbar-thumb:hover {
                background: #6b7280;
            }
            
            /* Ensure game board maintains minimum width for readability */
            #gameBoard {
                min-width: max-content;
            }
            
            /* Ensure category headers and question cells maintain proper alignment */
            #gameBoard > div {
                min-width: 80px; /* Minimum width for readability */
            }
        }
        
        /* Improved touch targets for mobile */
        @media (max-width: 768px) {
            .question-value {
                min-height: 44px !important; /* Minimum touch target size */
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
            }
            
            /* Ensure text doesn't overflow */
            .question-value {
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                hyphens: auto !important;
            }
        }
        
        /* Landscape orientation optimizations */
        @media (max-width: 768px) and (orientation: landscape) {
            #gameBoard {
                gap: 0.25rem !important;
            }
            
            .question-value {
                min-height: 35px !important;
                font-size: 0.625rem !important;
                padding: 0.25rem !important;
            }
            
            /* Category headers */
            #gameBoard > div:not([data-category]) {
                font-size: 0.625rem !important;
                padding: 0.375rem 0.25rem !important;
            }
        }
        
        /* Touch-friendly improvements */
        .touch-button {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        
        /* Mobile modal improvements */
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
            
            /* Improve input field for mobile */
            #answerInput {
                font-size: 16px !important; /* Prevent zoom on iOS */
                padding: 0.75rem !important;
                min-height: 44px !important; /* Minimum touch target */
            }
            
            /* Improve question text readability on mobile */
            #questionText {
                font-size: 1.125rem !important;
                line-height: 1.5 !important;
                margin-bottom: 1rem !important;
            }
            
            /* Improve submit button on mobile */
            #submitAnswerBtn {
                min-height: 44px !important;
                font-size: 1rem !important;
                padding: 0.75rem 1rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .mobile-modal {
                padding: 0.5rem;
                margin: 0.125rem;
                max-height: 95vh;
            }
            
            #questionText {
                font-size: 1rem !important;
                margin-bottom: 0.75rem !important;
            }
            
            #answerInput {
                padding: 0.5rem !important;
                min-height: 40px !important;
            }
            
            #submitAnswerBtn {
                min-height: 40px !important;
                font-size: 0.875rem !important;
                padding: 0.5rem 0.75rem !important;
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
        
        /* Prevent horizontal scroll */
        html, body {
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Mobile header optimizations */
        @media (max-width: 768px) {
            header h1 {
                font-size: 1.25rem !important;
            }
            
            header button {
                font-size: 0.75rem !important;
                padding: 0.375rem 0.75rem !important;
            }
        }
        
        @media (max-width: 480px) {
            header h1 {
                font-size: 1rem !important;
            }
            
            header button {
                font-size: 0.625rem !important;
                padding: 0.25rem 0.5rem !important;
            }
        }
        
        /* Mobile scoreboard optimizations */
        @media (max-width: 768px) {
            .timer-container {
                margin-left: 0.5rem !important;
            }
            
            .timer-container .w-10 {
                width: 2rem !important;
                height: 2rem !important;
            }
            
            .timer-container .w-12 {
                width: 2.5rem !important;
                height: 2.5rem !important;
            }
        }
        
        /* Ensure team cards don't overflow and timer has proper spacing */
        #teamsContainer {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .team-card {
            max-width: calc(50% - 0.25rem);
            min-width: 0;
        }
        
        @media (max-width: 640px) {
            .team-card {
                max-width: 100%;
                margin-bottom: 0.5rem;
            }
            
            #teamsContainer {
                justify-content: center;
            }
        }
        
        /* Special handling for 6 teams on mobile */
        @media (max-width: 768px) {
            #teamsContainer {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 1.5rem;
                justify-content: center;
                padding: 1rem;
                max-width: 100%;
            }
            
            .team-card {
                width: calc(33.333% - 1rem);
                min-width: 0;
                margin: 0;
                padding: 1.25rem 1rem !important;
                flex: 0 0 auto;
                box-sizing: border-box;
            }
            
            .team-card .flex-1 {
                min-width: 0;
                overflow: hidden;
            }
            
            .team-card h3 {
                font-size: 1rem !important;
                line-height: 1.4;
                margin-bottom: 0.5rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .team-card p {
                font-size: 1.125rem !important;
                line-height: 1.4;
                margin-bottom: 0.5rem;
            }
            
            .team-card .text-xs {
                font-size: 0.875rem !important;
            }
            
            .team-card .text-sm {
                font-size: 1rem !important;
            }
        }
        
        /* Force 3+3 layout for 6 teams on mobile */
        @media (max-width: 768px) {
            #teamsContainer.six-teams {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 0.75rem;
                justify-content: center;
            }
            
            #teamsContainer.six-teams .team-card {
                width: calc(32% - 0.5rem);
                margin-bottom: 1rem;
                flex: 0 0 auto;
                min-width: 120px;
            }
            
            /* Force break after 3rd card */
            #teamsContainer.six-teams .team-card:nth-child(3) {
                margin-right: 0;
            }
            
            #teamsContainer.six-teams .team-card:nth-child(4) {
                margin-left: 0;
            }
        }
        
        @media (max-width: 480px) {
            #teamsContainer {
                gap: 0.75rem;
                padding: 0.75rem;
                max-width: 100%;
            }
            
            .team-card {
                width: calc(50% - 0.375rem);
                padding: 1rem 0.75rem !important;
                flex: 0 0 auto;
                box-sizing: border-box;
                min-width: 140px;
            }
            
            .team-card h3 {
                font-size: 0.875rem !important;
                margin-bottom: 0.375rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .team-card p {
                font-size: 1rem !important;
                margin-bottom: 0.375rem;
            }
            
            .team-card .text-xs {
                font-size: 0.75rem !important;
            }
            
            .team-card .text-sm {
                font-size: 0.875rem !important;
            }
        }

        /* Mobile-specific optimizations */
        @media (max-width: 768px) {
            .question-value {
                font-size: 0.875rem !important;
                padding: 0.75rem !important;
                min-height: 60px;
            }
            
            .answered {
                min-height: 60px !important;
            }
            
            .answered .checkmark,
            .answered .x-mark {
                font-size: 1rem !important;
            }
            
            .team-card {
                padding: 0.75rem !important;
                margin: 0.25rem !important;
            }
            
            .team-card h3 {
                font-size: 0.875rem !important;
            }
            
            .team-card p {
                font-size: 1.125rem !important;
            }
        }
        
        @media (max-width: 480px) {
            .question-value {
                font-size: 0.75rem !important;
                padding: 0.5rem !important;
                min-height: 50px;
            }
            
            .answered {
                min-height: 50px !important;
            }
            
            .answered .checkmark,
            .answered .x-mark {
                font-size: 0.875rem !important;
            }
            
            .answered.incorrect::after,
            .answered.correct::after {
                font-size: 0.625rem !important;
                top: 1px !important;
                right: 1px !important;
                padding: 0px 1px !important;
            }
            
            .team-card {
                padding: 0.5rem !important;
                margin: 0.125rem !important;
            }
            
            .team-card h3 {
                font-size: 0.75rem !important;
            }
            
            .team-card p {
                font-size: 1rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 z-50 loading-overlay flex items-center justify-center">
        <div class="text-center">
            <div class="loading-spinner w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <h2 class="text-2xl font-bold text-white mb-2">Loading Your Custom Game</h2>
            <p class="text-gray-300">Preparing your personalized Jeopardy experience...</p>
        </div>
    </div>

    <!-- Main Game Container -->
    <div id="gameContainer" class="hidden">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 p-2 sm:p-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <h1 class="text-xl sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
                🎯 Simple Custom Jeopardy 
            </h1>
            <div class="flex items-center space-x-2 sm:space-x-4">
                <button onclick="goToMainMenu()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1 sm:py-2 px-2 sm:px-4 rounded-lg transition-colors text-sm sm:text-base touch-button">
                    Main Menu
                </button>
            </div>
        </div>
    </header>

    <!-- Main Game Screen -->
    <div id="gameScreen" class="min-h-screen">
        <!-- Score Board -->
        <div class="bg-gray-800 border-b border-gray-700 p-2 sm:p-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-6">
                    <!-- Teams Container -->
                    <div id="teamsContainer" class="flex-1 flex flex-wrap justify-center sm:justify-between items-center gap-1 sm:gap-2 w-full">
                        <!-- Team cards will be generated dynamically -->
                    </div>
                    
                    <!-- Question Timer -->
                    <div class="flex flex-col items-center timer-container ml-4 sm:ml-6">
                        <div class="relative">
                            <div id="timerProgress" class="absolute -inset-1 rounded-full border-2 border-green-500" style="background: conic-gradient(from 0deg, #10b981 0deg, transparent 0deg);"></div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-700 rounded-full flex items-center justify-center relative z-10">
                                <span id="timerDisplay" class="text-sm sm:text-base font-bold text-white">30</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Timer</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Game Board -->
        <div class="max-w-7xl mx-auto p-2 sm:p-6 game-board-container">
            <div id="gameBoard" class="grid grid-cols-5 gap-2 sm:gap-4 mb-4 sm:mb-8">
                <!-- Categories will be populated here -->
            </div>

    <!-- Question Modal -->
    <div id="questionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
        <div class="absolute inset-0 bg-black bg-opacity-75"></div>
        <div class="relative bg-gray-800 rounded-2xl p-4 sm:p-8 max-w-2xl w-full mx-2 sm:mx-4 question-card mobile-modal">
            <div class="text-center">
                <h3 id="questionText" class="text-lg sm:text-2xl font-bold text-white mb-4 sm:mb-6"></h3>
                <div class="mb-4 sm:mb-6">
                    <input type="text" id="answerInput" 
                           class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                           placeholder="Type your answer here...">
                </div>
                <div class="flex space-x-2 sm:space-x-4">
                    <button id="submitAnswerBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors text-sm sm:text-base touch-button">
                        Submit Answer
                    </button>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>

        <!-- Game Over Modal -->
        <div id="gameOverModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
            <div class="absolute inset-0 bg-black bg-opacity-75"></div>
            <div class="relative bg-gray-800 rounded-2xl p-4 sm:p-8 max-w-2xl w-full mx-2 sm:mx-4 text-center mobile-modal">
                <div class="mb-4 sm:mb-6">
                    <h2 class="text-2xl sm:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-2 sm:mb-4">
                        🏆 Game Over!
                    </h2>
                    <p id="gameOverMessage" class="text-base sm:text-xl text-gray-300 mb-4 sm:mb-6"></p>
                    <div id="finalScores" class="space-y-3 sm:space-y-4 mb-4 sm:mb-6"></div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                    <button onclick="goToMainMenu()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors text-sm sm:text-base touch-button">
                        Main Menu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        class SimpleCustomJeopardyGame {
            constructor() {
                this.gameState = null;
                this.timer = null;
                this.timerInterval = null;
                this.categories = [];
                this.values = [];
                this.isAdvancingTurn = false; // Add flag to prevent multiple turn advancements
                
                this.initializeEventListeners();
                this.showLoadingScreen();
                this.loadGameState();
            }

            initializeEventListeners() {
                // Question modal buttons
                document.getElementById('submitAnswerBtn').addEventListener('click', () => {
                    this.submitAnswer();
                });

                // Answer input
                document.getElementById('answerInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.submitAnswer();
                    }
                });
            }

            async showLoadingScreen() {
                await new Promise(resolve => setTimeout(resolve, 2000));
                document.getElementById('loadingScreen').classList.add('hidden');
                document.getElementById('gameContainer').classList.remove('hidden');
                document.getElementById('gameScreen').classList.add('slide-in');
            }

            async loadGameState() {
                try {
                    console.log('Loading custom game state...');
                    const response = await fetch('/jeopardy/simple-game-state', {
                method: 'GET',
                headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
                    });

                    console.log('Response status:', response.status);
                    const data = await response.json();
                    console.log('Game state response:', data);
                    
                    if (data.success && data.game_state) {
                        this.gameState = data.game_state;
                        
                        // Debug initial game state
                        console.log('=== Initial Game State Loaded ===');
                        console.log('Current team:', this.gameState.current_team);
                        console.log('Total teams:', this.gameState.team_count);
                        console.log('Teams:');
                        for (let i = 1; i <= this.gameState.team_count; i++) {
                            const team = this.gameState[`team${i}`];
                            console.log(`  Team ${i}: ${team.name} (${team.score} pts)`);
                        }
                        console.log('==============================');
                        
                        // Check if we have custom categories
                        if (this.gameState.custom_categories) {
                            this.categories = Object.keys(this.gameState.custom_categories);
                            const questionCount = this.gameState.question_count || 5;
                            this.values = Array.from({length: questionCount}, (_, i) => i + 1);
                            
                            console.log('Custom game loaded:', {
                                categories: this.categories,
                                values: this.values,
                                teamCount: this.gameState.team_count
                            });
                            
                            this.generateTeamCards();
                            this.updateDisplay();
                            this.createGameBoard();
                            this.updateGameBoard();
                        } else {
                            console.error('No custom categories found');
                            alert('No custom game found. Please create a custom game first.');
                            window.location.href = '/jeopardy/simple-custom-game';
                        }
                    } else {
                        console.log('No game state found, redirecting to custom game creator');
                        window.location.href = '/jeopardy/simple-custom-game';
                    }
                } catch (error) {
                    console.error('Error loading game state:', error);
                    window.location.href = '/jeopardy/simple-custom-game';
                }
            }

            generateTeamCards() {
                const teamsContainer = document.getElementById('teamsContainer');
                teamsContainer.innerHTML = '';
                
                // Add class for 6 teams to force 3+3 layout on mobile
                if (this.gameState.team_count === 6) {
                    teamsContainer.classList.add('six-teams');
                } else {
                    teamsContainer.classList.remove('six-teams');
                }
                
                const teamColors = ['blue', 'red', 'green', 'yellow', 'purple', 'pink'];
                
                for (let i = 1; i <= this.gameState.team_count; i++) {
                    const team = this.gameState[`team${i}`];
                    const color = teamColors[i - 1];
                    
                    const teamCard = document.createElement('div');
                    teamCard.id = `team${i}Card`;
                    teamCard.className = `flex items-center space-x-2 sm:space-x-4 bg-gray-700 rounded-lg p-2 sm:p-4 flex-1 transition-all duration-300 team-card`;
                    
                    teamCard.innerHTML = `
                        <div class="w-3 h-3 sm:w-4 sm:h-4 bg-${color}-500 rounded-full flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <h3 id="team${i}Name" class="font-bold text-sm sm:text-lg text-${color}-400">${team.name}</h3>
                            <p id="team${i}Score" class="text-lg sm:text-2xl font-bold text-white">${team.score} pts</p>
                            <div class="mt-1 sm:mt-2">
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <span class="text-xs sm:text-sm text-gray-400">Time:</span>
                                    <span id="team${i}Timer" class="text-sm sm:text-lg font-bold text-${color}-400">5:00</span>
                                </div>
                                <div class="w-full bg-gray-600 rounded-full h-1 sm:h-2 mt-1">
                                    <div id="team${i}TimerBar" class="bg-${color}-500 h-1 sm:h-2 rounded-full transition-all duration-300" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    teamsContainer.appendChild(teamCard);
                }
            }

            createGameBoard() {
                const gameBoard = document.getElementById('gameBoard');
                gameBoard.innerHTML = '';
                
                // Apply responsive CSS classes based on category count and screen size
                this.applyResponsiveGameBoardClasses();
                
                // For single category games, use a 3-column layout
                if (this.categories.length === 1) {
                    gameBoard.className = 'grid gap-2 sm:gap-4 mb-4 sm:mb-8 single-category';
                    gameBoard.style.gridTemplateColumns = 'repeat(3, 1fr)';
                    gameBoard.style.setProperty('grid-template-columns', 'repeat(3, 1fr)', 'important');
                    
                    // Create category header spanning all 3 columns
                    const categoryDiv = document.createElement('div');
                    categoryDiv.className = 'bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-center py-2 sm:py-4 rounded-lg text-sm sm:text-lg col-span-3';
                    categoryDiv.textContent = this.categories[0];
                    gameBoard.appendChild(categoryDiv);
                    
                    // Create question cells in 3-column layout
                    this.values.forEach(value => {
                        const cell = document.createElement('div');
                        cell.className = 'question-value text-center py-3 sm:py-6 rounded-lg cursor-pointer text-sm sm:text-xl font-bold touch-button';
                        cell.textContent = `${value} ${value === 1 ? 'point' : 'points'}`;
                        cell.dataset.category = this.categories[0];
                        cell.dataset.value = value;
                        
                        cell.addEventListener('click', () => {
                            this.selectQuestion(this.categories[0], value);
                        });
                        
                        gameBoard.appendChild(cell);
                    });
                } else {
                    // Multiple categories layout - maintain proper grid structure
                    const gridCols = this.categories.length;
                    gameBoard.className = 'grid gap-2 sm:gap-4 mb-4 sm:mb-8';
                    gameBoard.style.gridTemplateColumns = `repeat(${gridCols}, 1fr)`;
                    gameBoard.style.setProperty('grid-template-columns', `repeat(${gridCols}, 1fr)`, 'important');

                    // Create category headers
                    this.categories.forEach(category => {
                        const categoryDiv = document.createElement('div');
                        categoryDiv.className = 'bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold text-center py-2 sm:py-4 rounded-lg text-sm sm:text-lg';
                        categoryDiv.textContent = category;
                        gameBoard.appendChild(categoryDiv);
                    });

                    // Create question cells - maintain proper alignment with categories
                    this.values.forEach(value => {
                        this.categories.forEach(category => {
                            const cell = document.createElement('div');
                            cell.className = 'question-value text-center py-3 sm:py-6 rounded-lg cursor-pointer text-sm sm:text-xl font-bold touch-button';
                            cell.textContent = `${value} ${value === 1 ? 'point' : 'points'}`;
                            cell.dataset.category = category;
                            cell.dataset.value = value;
                            
                            cell.addEventListener('click', () => {
                                this.selectQuestion(category, value);
                            });
                            
                            gameBoard.appendChild(cell);
                        });
                    });
                }
            }

            applyResponsiveGameBoardClasses() {
                const gameBoard = document.getElementById('gameBoard');
                const categoryCount = this.categories.length;
                const screenWidth = window.innerWidth;
                
                // Remove all existing responsive classes
                gameBoard.classList.remove(
                    'single-category', 'many-categories'
                );
                
                // Apply classes based on category count and screen size
                if (categoryCount === 1) {
                    // Single category - apply centering class
                    gameBoard.classList.add('single-category');
                } else if (categoryCount > 6 && screenWidth <= 768) {
                    // Many categories on mobile - enable horizontal scroll
                    gameBoard.classList.add('many-categories');
                }
                
                // Ensure proper grid structure is maintained
                // The grid-template-columns will be set by the createGameBoard function
                // and overridden by CSS media queries for responsive behavior
                
                // Force grid display to ensure proper structure
                gameBoard.style.display = 'grid';
                gameBoard.style.gridAutoFlow = 'row';
                gameBoard.style.gridAutoColumns = 'unset';
                
                // Set grid template columns based on category count - this is crucial for alignment
                if (categoryCount === 1) {
                    gameBoard.style.gridTemplateColumns = 'repeat(3, 1fr)';
                } else {
                    gameBoard.style.gridTemplateColumns = `repeat(${categoryCount}, 1fr)`;
                }
                
                // Ensure the grid structure cannot be overridden
                gameBoard.style.setProperty('grid-template-columns', gameBoard.style.gridTemplateColumns, 'important');
            }

            async selectQuestion(category, value) {
                const questionKey = `${category}_${value}`;
                
                // Check if question is already answered
                const isAnswered = this.gameState.answered_questions && this.gameState.answered_questions.some(q => 
                    typeof q === 'string' ? q === questionKey : q.key === questionKey
                );
                
                if (isAnswered) {
                    return; // Question already answered
                }

                try {
                    const response = await fetch('/jeopardy/simple-question', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ category, value })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        console.log('Question selected successfully:', data);
                        this.showQuestion(data.question, data.is_steal_attempt);
                        this.startTimer(data.timer);
                    } else {
                        console.error('Failed to select question:', data.error);
                        console.error('Full error response:', data);
                        alert('Error selecting question: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error selecting question:', error);
                }
            }

            showQuestion(question, isStealAttempt = false) {
                document.getElementById('questionText').textContent = question.question;
            document.getElementById('answerInput').value = '';
            document.getElementById('questionModal').classList.remove('hidden');
            document.getElementById('answerInput').focus();

                // Show steal notification if it's a steal attempt
                if (isStealAttempt) {
                    this.showStealNotification();
                }
            }

            startTimer(duration) {
                // Clear any existing timer
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                }
                
                // Use custom question timer or provided duration, default to 30 seconds
                let timeLeft = duration || (this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30);
                this.updateTimerDisplay(timeLeft);
                
                this.timerInterval = setInterval(() => {
                timeLeft--;
                    
                    // Prevent negative values
                    if (timeLeft < 0) {
                        timeLeft = 0;
                    }
                    
                    this.updateTimerDisplay(timeLeft);
                    
                    // Update team timer only if timer is still running (not expired)
                    if (timeLeft > 0) {
                        const currentTeam = 'team' + this.gameState.current_team;
                        this.gameState[currentTeam].timer = Math.max(0, this.gameState[currentTeam].timer - 1);
                        this.updateTeamTimer(this.gameState.current_team, this.gameState[currentTeam].timer);
                    }
                    
                    // Update timer on backend every 5 seconds
                    if (timeLeft % 5 === 0 && timeLeft > 0) {
                        this.updateTimerOnBackend(timeLeft, this.gameState[currentTeam].timer);
                    }

                if (timeLeft <= 0) {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                        this.timeUp();
                }
            }, 1000);
        }

            updateTimerDisplay(timeLeft) {
                // Ensure timeLeft is not negative
                timeLeft = Math.max(0, timeLeft);
                
                console.log('Updating timer display:', timeLeft);
                document.getElementById('timerDisplay').textContent = timeLeft;
                
                // Get the maximum timer value (custom or default 30)
                const maxTimer = this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30;
                const percentage = (timeLeft / maxTimer) * 100;
                
                // Calculate the angle for the conic gradient (360 degrees = full circle)
                const angle = (percentage / 100) * 360;
                
                console.log('Timer calculation:', { timeLeft, maxTimer, percentage, angle });
                
                const timerProgress = document.getElementById('timerProgress');
                const timerContainer = timerProgress.parentElement;
                
                // Update the conic gradient
                timerProgress.style.background = `conic-gradient(from 0deg, #10b981 0deg, #10b981 ${angle}deg, transparent ${angle}deg, transparent 360deg)`;
                
                // Change color based on time
                if (timeLeft <= 10) {
                    timerProgress.style.background = `conic-gradient(from 0deg, #ef4444 0deg, #ef4444 ${angle}deg, transparent ${angle}deg, transparent 360deg)`;
                    timerContainer.classList.add('pulse-glow');
                } else if (timeLeft <= 20) {
                    timerProgress.style.background = `conic-gradient(from 0deg, #f59e0b 0deg, #f59e0b ${angle}deg, transparent ${angle}deg, transparent 360deg)`;
                    timerContainer.classList.remove('pulse-glow');
                } else {
                    timerProgress.style.background = `conic-gradient(from 0deg, #10b981 0deg, #10b981 ${angle}deg, transparent ${angle}deg, transparent 360deg)`;
                    timerContainer.classList.remove('pulse-glow');
                }
            }

            resetQuestionTimer() {
                // Reset timer to full circle (no countdown)
                const maxTimer = this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30;
                
                const timerProgress = document.getElementById('timerProgress');
                const timerDisplay = document.getElementById('timerDisplay');
                const timerContainer = timerProgress.parentElement;
                
                console.log('Resetting timer:', { maxTimer });
                
                // Show full circle (no countdown)
                timerProgress.style.background = `conic-gradient(from 0deg, #10b981 0deg, #10b981 360deg)`;
                timerContainer.classList.remove('pulse-glow');
                
                // Show max time
                timerDisplay.textContent = maxTimer;
            }

            async submitAnswer() {
                // Clear the timer
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
                
                // Calculate time taken to answer (max timer - remaining time)
                const maxTimer = this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30;
                const timeTaken = maxTimer - parseInt(document.getElementById('timerDisplay').textContent);
                
                // Reset timer display
                this.resetQuestionTimer();
                
                const answer = document.getElementById('answerInput').value;
                const currentTeam = 'team' + this.gameState.current_team;
                
                try {
                    const response = await fetch('/jeopardy/simple-answer', {
                method: 'POST',
                headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    answer: answer,
                            time_taken: timeTaken
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.gameState = data.game_state;
                        
                        this.hideQuestionModal();
                        this.updateDisplay();
                        
                        // Force update game board with a small delay to ensure DOM is ready
                        setTimeout(() => {
                            this.updateGameBoard();
                            
                            // Ensure the current question is marked as answered
                            if (data.question) {
                                const currentQuestionKey = `${data.question.category}_${data.question.value}`;
                                if (!this.gameState.answered_questions.some(q => 
                                    typeof q === 'string' ? q === currentQuestionKey : q.key === currentQuestionKey
                                )) {
                                    this.gameState.answered_questions.push({
                                        key: currentQuestionKey,
                                        correct: data.is_correct
                                    });
                                }
                            }
                            
                            // Force another update after a brief delay to ensure it's applied
                            setTimeout(() => {
                                this.updateGameBoard();
                            }, 100);
                        }, 50);
                        
                        this.showAnswerResult(data.is_correct, data.correct_answer, data.is_steal_attempt, data.points_earned);
                        
                        // Always advance to next team's turn after answering (correct or incorrect)
                        setTimeout(() => {
                            this.advanceToNextTeam();
                        }, 3000); // Wait 3 seconds to show the answer result, then advance turn
                        
                        // Check for game over
                        if (this.gameState.game_over) {
                            this.showGameOver();
                        }
                    }
                } catch (error) {
                    console.error('Error submitting answer:', error);
                }
            }

            async advanceToNextTeam() {
                // Prevent multiple rapid turn advancements
                if (this.isAdvancingTurn) {
                    console.log('Turn advancement already in progress, skipping...');
                    return;
                }
                
                this.isAdvancingTurn = true;
                
                try {
                    // Get current team's timer before advancing
                    const currentTeam = 'team' + this.gameState.current_team;
                    const currentTeamTimer = this.gameState[currentTeam].timer;
                    
                    console.log('=== TURN ADVANCEMENT DEBUG ===');
                    console.log('Current team before advancement:', this.gameState.current_team);
                    console.log('Current team name:', this.gameState[`team${this.gameState.current_team}`].name);
                    console.log('Total teams in game:', this.gameState.team_count);
                    console.log('All teams:');
                    for (let i = 1; i <= this.gameState.team_count; i++) {
                        const team = this.gameState[`team${i}`];
                        console.log(`  Team ${i}: ${team.name}`);
                    }
                    
                    // Call the timer endpoint to advance to next team
                    const response = await fetch('/jeopardy/simple-timer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            time_remaining: 0, // Force turn advancement
                            team_timer: currentTeamTimer
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        console.log('=== TURN ADVANCEMENT RESULT ===');
                        console.log('Previous team was:', this.gameState.current_team, '(', this.gameState[`team${this.gameState.current_team}`].name, ')');
                        console.log('New current team:', data.game_state.current_team, '(', data.game_state[`team${data.game_state.current_team}`].name, ')');
                        console.log('Expected next team should be:', (this.gameState.current_team % this.gameState.team_count) + 1);
                        
                        this.gameState = data.game_state;
                        this.updateDisplay();
                        this.updateGameBoard();
                        
                        // Debug the game state after turn advancement
                        this.debugGameState();
                        
                        // Show notification that turn has advanced
                        this.showTurnAdvancedNotification();
                        
                        // Check for game over
                        if (this.gameState.game_over) {
                            this.showGameOver();
                        }
                    } else {
                        console.error('Failed to advance turn:', data);
                    }
                } catch (error) {
                    console.error('Error advancing to next team:', error);
                } finally {
                    // Reset the flag after a delay to allow for normal turn advancement
                    setTimeout(() => {
                        this.isAdvancingTurn = false;
                    }, 1000);
                }
            }

            async timeUp() {
                // Clear the timer
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
                
                // Reset timer display
                this.resetQuestionTimer();
                
                // Hide the question modal
                this.hideQuestionModal();
                
                // Use the same turn advancement function to prevent conflicts
                console.log('Time expired, advancing to next team...');
                await this.advanceToNextTeam();
                
                // Show notification that time's up and next team's turn
                this.showTimeUpNotification();
            }

            hideQuestionModal() {
                document.getElementById('questionModal').classList.add('hidden');
            }

                        updateDisplay() {
                // Update team names and scores for all teams
                for (let i = 1; i <= this.gameState.team_count; i++) {
                    const team = this.gameState[`team${i}`];
                    const teamNameElement = document.getElementById(`team${i}Name`);
                    const teamScoreElement = document.getElementById(`team${i}Score`);
                    const teamCardElement = document.getElementById(`team${i}Card`);
                    
                    if (teamNameElement) teamNameElement.textContent = team.name;
                    if (teamScoreElement) teamScoreElement.textContent = `${team.score} pts`;
                    if (teamCardElement) {
                        teamCardElement.classList.toggle('team-active', this.gameState.current_team === i);
                        teamCardElement.classList.add('score-animation');
                    }
                    
                    // Update team timers
                    this.updateTeamTimer(i, team.timer);
                }
                
                // Reset question timer to initial state when not answering
                if (!this.timerInterval) {
                    this.resetQuestionTimer();
                }
                
                // Remove score animation after delay
                setTimeout(() => {
                    for (let i = 1; i <= this.gameState.team_count; i++) {
                        const teamCardElement = document.getElementById(`team${i}Card`);
                        if (teamCardElement) {
                            teamCardElement.classList.remove('score-animation');
                        }
                    }
                }, 500);
            }

            updateTeamTimer(teamNumber, seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                const timeString = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
                
                document.getElementById(`team${teamNumber}Timer`).textContent = timeString;
                
                // Update progress bar with custom game timer or default 300 seconds
                const maxGameTimer = this.gameState && this.gameState.custom_game_timer ? this.gameState.custom_game_timer : 300;
                const percentage = (seconds / maxGameTimer) * 100;
                document.getElementById(`team${teamNumber}TimerBar`).style.width = `${percentage}%`;
                
                // Change color based on remaining time
                const timerBar = document.getElementById(`team${teamNumber}TimerBar`);
                const timerText = document.getElementById(`team${teamNumber}Timer`);
                
                if (seconds <= 60) { // Less than 1 minute
                    timerBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
                    timerText.className = 'text-lg font-bold text-red-400';
                } else if (seconds <= 120) { // Less than 2 minutes
                    timerBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
                    timerText.className = 'text-lg font-bold text-yellow-400';
                } else {
                    timerBar.className = `h-2 rounded-full transition-all duration-300 ${teamNumber === 1 ? 'bg-blue-500' : 'bg-red-500'}`;
                    timerText.className = `text-lg font-bold ${teamNumber === 1 ? 'text-blue-400' : 'text-red-400'}`;
                }
            }

            async updateTimerOnBackend(questionTime, teamTime) {
                try {
                    await fetch('/jeopardy/simple-timer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            time_remaining: questionTime,
                            team_timer: teamTime
                        })
                    });
                } catch (error) {
                    console.error('Error updating timer:', error);
                }
            }

            showGameOver() {
                // Find the team with the highest score
                let winnerTeam = null;
                let highestScore = -1;
                
                for (let i = 1; i <= this.gameState.team_count; i++) {
                    const team = this.gameState[`team${i}`];
                    if (team.score > highestScore) {
                        highestScore = team.score;
                        winnerTeam = team;
                    }
                }
                
                document.getElementById('gameOverMessage').textContent = `Congratulations! ${winnerTeam.name} wins!`;
                
                const finalScoresDiv = document.getElementById('finalScores');
                let scoresHTML = `
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-bold text-white mb-2">Final Scores</h3>
                        <div class="grid grid-cols-2 gap-4">
                `;
                
                for (let i = 1; i <= this.gameState.team_count; i++) {
                    const team = this.gameState[`team${i}`];
                    const isWinner = team.name === winnerTeam.name;
                    const colorClass = isWinner ? 'text-green-400' : 'text-gray-400';
                                         scoresHTML += `<div class="${colorClass}">${team.name}: ${team.score} ${team.score === 1 ? 'point' : 'points'}</div>`;
                }
                
                scoresHTML += '</div></div>';
                finalScoresDiv.innerHTML = scoresHTML;
                
                document.getElementById('gameOverModal').classList.remove('hidden');
            }

            updateGameBoard() {
                // Ensure responsive classes are applied
                this.applyResponsiveGameBoardClasses();
                
                const cells = document.querySelectorAll('[data-category][data-value]');
                
                if (cells.length === 0) {
                    return;
                }

                let answeredCount = 0;
                
                cells.forEach((cell, index) => {
                    const category = cell.dataset.category;
                    const value = cell.dataset.value;
                    const questionKey = `${category}_${value}`;
                    
                    // Find if this question is answered and whether it was correct
                    let isAnswered = false;
                    let wasCorrect = false;
                    
                    if (this.gameState.answered_questions) {
                        const answeredQuestion = this.gameState.answered_questions.find(q => 
                            typeof q === 'string' ? q === questionKey : q.key === questionKey
                        );
                        if (answeredQuestion) {
                            isAnswered = true;
                            wasCorrect = typeof answeredQuestion === 'string' ? true : answeredQuestion.correct;
                        }
                    }
                    
                    if (isAnswered) {
                        answeredCount++;
                        
                        // Force remove all classes and add the answered class
                        cell.className = wasCorrect ? 'answered correct' : 'answered incorrect';
                        cell.textContent = ''; // Clear text content since we use CSS pseudo-element for checkmark
                        cell.style.cursor = 'not-allowed';
                        cell.style.pointerEvents = 'none';
                        cell.style.background = wasCorrect ? 
                            'linear-gradient(135deg, #1f2937 0%, #374151 100%)' : 
                            'linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%)';
                        cell.style.color = '#9ca3af';
                        cell.style.opacity = '0.8';
                        cell.style.border = wasCorrect ? '2px solid #4b5563' : '2px solid #dc2626';
                        
                        // Add checkmark or X element
                        const symbol = document.createElement('div');
                        symbol.className = wasCorrect ? 'checkmark' : 'x-mark';
                        symbol.textContent = wasCorrect ? '✓' : '✗';
                        cell.appendChild(symbol);
                    } else {
                        // Ensure unanswered questions are properly styled
                        cell.className = 'question-value text-center py-3 sm:py-6 rounded-lg cursor-pointer text-sm sm:text-xl font-bold touch-button';
                        cell.textContent = `${value} ${value === 1 ? 'point' : 'points'}`;
                        cell.style.cursor = 'pointer';
                        cell.style.pointerEvents = 'auto';
                        cell.style.background = '';
                        cell.style.color = '';
                        cell.style.opacity = '';
                        cell.style.border = '';
                        
                        // Remove any existing symbols
                        const existingSymbol = cell.querySelector('.checkmark, .x-mark');
                        if (existingSymbol) {
                            existingSymbol.remove();
                        }
                    }
                });
                
                const totalQuestions = this.categories.length * this.values.length;
                if (this.gameState.answered_questions && this.gameState.answered_questions.length >= totalQuestions) {
                    this.showGameOver();
                }
            }

            showAnswerResult(isCorrect, correctAnswer, isStealAttempt = false, pointsEarned = 0) {
                let message = '';
                let color = '';
                
                if (isCorrect) {
                    if (isStealAttempt) {
                                                 message = `🎯 STOLEN! +${pointsEarned} ${pointsEarned === 1 ? 'point' : 'points'}!`;
                    } else {
                        message = 'Correct!';
                    }
                    color = 'green';
                } else {
                    if (isStealAttempt) {
                        message = `Steal attempt failed! The answer was: ${correctAnswer}`;
                    } else {
                        message = `Incorrect! The answer was: ${correctAnswer}`;
                    }
                    color = 'red';
                }
                
                // Create centered notification with enhanced styling
                const notification = document.createElement('div');
                notification.className = `fixed inset-0 flex items-center justify-center z-50`;
                
                // Create the notification content
                const notificationContent = document.createElement('div');
                notificationContent.style.cssText = `
                    background: linear-gradient(135deg, ${color === 'green' ? '#10b981' : '#ef4444'} 0%, ${color === 'green' ? '#059669' : '#dc2626'} 100%);
                    border: 3px solid ${color === 'green' ? '#34d399' : '#f87171'};
                    box-shadow: 0 20px 40px rgba(${color === 'green' ? '16, 185, 129' : '239, 68, 68'}, 0.6);
                    font-weight: 700;
                    font-size: 24px;
                    padding: 40px 60px;
                    border-radius: 20px;
                    text-align: center;
                    max-width: 600px;
                    min-width: 400px;
                    z-index: 10000;
                    animation: bounceIn 0.8s ease-out;
                `;
                notificationContent.textContent = message;
                
                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(5px);
                    z-index: 9999;
                `;
                
                notification.appendChild(backdrop);
                notification.appendChild(notificationContent);
                
                document.body.appendChild(notification);
                
                // Add confetti for correct answers
                if (isCorrect) {
                    this.createConfetti();
                }
                
                // Show question removal notification (smaller, top-right)
                setTimeout(() => {
                    const removalNotification = document.createElement('div');
                    removalNotification.className = 'mobile-notification bg-blue-600 text-white px-3 sm:px-4 py-2 sm:py-3 rounded-lg shadow-lg z-50 bounce-in';
                    removalNotification.style.cssText = `
                        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                        border: 2px solid #60a5fa;
                        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
                        font-weight: 600;
                        font-size: 12px;
                        max-width: 200px;
                        text-align: center;
                        z-index: 9999;
                    `;
                    removalNotification.textContent = 'Question removed from board ✓';
                    document.body.appendChild(removalNotification);
                    
                    setTimeout(() => {
                        removalNotification.remove();
                    }, 2000);
                }, 1000);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            createConfetti() {
                for (let i = 0; i < 50; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.animationDelay = Math.random() * 3 + 's';
                    confetti.style.backgroundColor = ['#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6'][Math.floor(Math.random() * 5)];
                    
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => {
                        confetti.remove();
                    }, 3000);
                }
            }

            showTurnAdvancedNotification() {
                const currentTeam = this.gameState[`team${this.gameState.current_team}`];
                const message = `🎯 ${currentTeam.name}'s turn!`;
                
                console.log('Turn advanced to:', currentTeam.name, '(Team', this.gameState.current_team, 'of', this.gameState.team_count, ')');
                
                // Create smaller, more subtle notification
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-blue-600 text-white px-3 py-2 rounded-lg shadow-lg z-50 bounce-in';
                notification.style.cssText = `
                    position: fixed;
                    top: 1rem;
                    right: 1rem;
                    left: 1rem;
                    z-index: 50;
                    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                    border: 2px solid #60a5fa;
                    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                    font-weight: 600;
                    font-size: 0.875rem;
                    max-width: 300px;
                    text-align: center;
                    margin: 0 auto;
                `;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 2 seconds (shorter duration)
                setTimeout(() => {
                    notification.remove();
                }, 2000);
            }

            // Debug function to check current game state
            debugGameState() {
                console.log('=== Current Game State ===');
                console.log('Current team:', this.gameState.current_team);
                console.log('Total teams:', this.gameState.team_count);
                console.log('Teams:');
                for (let i = 1; i <= this.gameState.team_count; i++) {
                    const team = this.gameState[`team${i}`];
                    console.log(`  Team ${i}: ${team.name} (${team.score} pts)`);
                }
                console.log('=======================');
            }

            showTimeUpNotification() {
                const currentTeam = this.gameState[`team${this.gameState.current_team}`];
                const message = `⏰ Time's up! ${currentTeam.name} can now steal the question!`;
                
                // Create temporary notification
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-orange-600 text-white px-4 sm:px-6 py-2 sm:py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            showStealNotification() {
                const message = '🎯 STEAL OPPORTUNITY! Try to answer this question correctly!';
                
                // Create temporary notification
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-purple-600 text-white px-4 sm:px-6 py-2 sm:py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            async resetGame() {
                try {
                    await fetch('/jeopardy/reset', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    this.gameState = null;
                    this.hideQuestionModal();
                    document.getElementById('gameOverModal').classList.add('hidden');
                    window.location.href = '/jeopardy/setup';
                } catch (error) {
                    console.error('Error resetting game:', error);
                }
            }
        }

        function goToMainMenu() {
            window.location.href = '/jeopardy';
        }

        // Initialize the game when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            window.simpleCustomJeopardyGame = new SimpleCustomJeopardyGame();
            
            // Update timer ring and responsive classes on window resize
            window.addEventListener('resize', () => {
                if (window.simpleCustomJeopardyGame) {
                    // Update timer display if timer is running
                    if (window.simpleCustomJeopardyGame.timerInterval) {
                        const currentTime = parseInt(document.getElementById('timerDisplay').textContent);
                        window.simpleCustomJeopardyGame.updateTimerDisplay(currentTime);
                    }
                    
                    // Update responsive game board classes
                    if (window.simpleCustomJeopardyGame.categories && window.simpleCustomJeopardyGame.categories.length > 0) {
                        window.simpleCustomJeopardyGame.applyResponsiveGameBoardClasses();
                    }
                }
            });
        });
    </script>
</body>
</html>
