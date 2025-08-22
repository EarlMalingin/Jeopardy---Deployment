<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Custom Jeopardy Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
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
        
        .question-value {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .question-value:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
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
            
            .timer-container .w-16 {
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
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        .team-active {
            border: 3px solid #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }
        
        .jeopardy-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .category-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            border: 2px solid #60a5fa;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .game-board {
            background: rgba(31, 41, 55, 0.8);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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
        
        @keyframes question-selected-pulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.8);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
            }
            50% {
                transform: scale(1.02);
                box-shadow: 0 15px 35px rgba(245, 158, 11, 0.6);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        /* Disabled question cells styling */
        .question-value.disabled {
            opacity: 0.4 !important;
            transform: scale(0.98) !important;
            filter: grayscale(30%) !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            transition: all 0.3s ease !important;
        }

        /* Active question cells styling */
        .question-value.active {
            opacity: 1 !important;
            transform: scale(1) !important;
            filter: none !important;
            cursor: pointer !important;
            pointer-events: auto !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3) !important;
        }

        /* Timer styling */
        .timer-container {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        #timerDisplay {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulseGlow {
            from { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
            to { box-shadow: 0 0 30px rgba(59, 130, 246, 0.8); }
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center">
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
                🎯 Custom Jeopardy 
            </h1>
            <div class="flex items-center space-x-2 sm:space-x-4">
                <span class="text-xs sm:text-sm text-gray-300 hidden sm:block">Custom Game</span>
                <div id="lobbyIndicator" class="hidden">
                    <span class="text-xs sm:text-sm text-blue-400">🎮 Lobby</span>
                </div>
                <div id="turnIndicator" class="text-xs sm:text-sm text-green-400 hidden">
                    <span>🎯 Your Turn!</span>
                </div>
                <!-- Host observer indicator removed - host can now participate -->
                
                <a href="/jeopardy" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-1 sm:py-2 px-2 sm:px-4 rounded-lg transition-colors text-sm sm:text-base touch-button">
                    ← Menu
                </a>
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
                        <p class="text-xs sm:text-sm text-gray-400 mt-1 sm:mt-2">Timer</p>
                    </div>
                </div>
            </div>
        </div>

            <!-- Game Board -->
            <div class="max-w-7xl mx-auto p-2 sm:p-6 game-board-container">
                <div id="gameBoard" class="grid gap-2 sm:gap-4 mb-4 sm:mb-8 game-board">
                    <!-- Categories will be populated here -->
                </div>
                
                <!-- Question Modal -->
                <div id="questionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
                    <div class="absolute inset-0 bg-black bg-opacity-75"></div>
                    <div class="relative bg-gray-800 rounded-2xl p-4 sm:p-8 max-w-2xl w-full mx-2 sm:mx-4 mobile-modal">
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
                    <button onclick="createNewCustomGame()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-colors text-sm sm:text-base touch-button">
                        Create New Custom Game
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        class CustomJeopardyGame {
                            constructor() {
                    this.gameState = null;
                    this.timer = null;
                    this.timerInterval = null;
                    this.teamTimerInterval = null; // New interval for team timer updates
                    this.categories = [];
                    this.values = []; // Will be dynamically set based on question count
                    this.lobbyCode = null; // Store lobby code for real-time sync
                    this.syncInterval = null; // Interval for syncing game state
                    this.isProcessingQuestion = false; // Flag to prevent multiple simultaneous requests
                    this.isSubmittingAnswer = false; // Flag to prevent multiple answer submissions
                    this.isAnsweringQuestion = false; // Flag to track when user is actively answering
                    this.hasSelectedQuestion = false; // Flag to track if current player selected the question
                    this.isAdvancingTurn = false; // Flag to prevent multiple turn advancements
                    
                    // Clear any old session storage data
                    this.clearOldGameData();
                    
                    this.initializeEventListeners();
                    this.showLoadingScreen();
                    this.loadGameState();
                }

            clearOldGameData() {
                // Clear any old session storage data that might interfere with new game
                const keysToClear = ['playerId', 'playerTeam', 'lobby_players'];
                keysToClear.forEach(key => {
                    if (sessionStorage.getItem(key)) {
                        sessionStorage.removeItem(key);
                        console.log('Cleared old session data:', key);
                    }
                });
                
                // For single-player games, ensure we clear any observer mode data
                const urlParams = new URLSearchParams(window.location.search);
                const isSinglePlayer = urlParams.get('mode') === 'singleplayer';
                if (isSinglePlayer) {
                    // Force set player team to 1 for single-player games
                    sessionStorage.setItem('playerTeam', '1');
                    console.log('Single-player mode detected - set player team to 1');
                }
            }

            initializeEventListeners() {
                document.getElementById('submitAnswerBtn').addEventListener('click', (e) => {
                    console.log('Submit button clicked');
                    e.preventDefault();
                    e.stopPropagation();
                    this.submitAnswer();
                });

                document.getElementById('answerInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        console.log('Enter key pressed');
                        e.preventDefault();
                        this.submitAnswer();
                    }
                });
            }

            async showLoadingScreen() {
                // Reduced loading time for faster game start
                await new Promise(resolve => setTimeout(resolve, 500));
                document.getElementById('loadingScreen').classList.add('hidden');
                document.getElementById('gameContainer').classList.remove('hidden');
            }

            async loadGameState() {
                try {
                    // Check if we're in a lobby game
                    const urlParams = new URLSearchParams(window.location.search);
                    const lobbyCode = urlParams.get('lobby');
                    
                    console.log('URL params:', window.location.search);
                    console.log('Lobby code from URL:', lobbyCode);
                    
                    // Don't clear session storage - let server handle player assignment
                    console.log('Using server-based player assignment');
                    
                    if (lobbyCode) {
                        // We're in a lobby game, store the lobby code
                        this.lobbyCode = lobbyCode;
                        console.log('Lobby game detected with code:', lobbyCode);
                        document.getElementById('lobbyIndicator').classList.remove('hidden');
                    } else {
                        document.getElementById('lobbyIndicator').classList.add('hidden');
                        console.log('No lobby code found in URL');
                    }
                    
                    // Clear any cached game state and get fresh data
                    if (this.lobbyCode) {
                        // For lobby games, always get fresh state from lobby
                        console.log('Loading fresh game state from lobby...');
                        
                        // Add cache-busting parameter to ensure fresh data
                        const timestamp = new Date().getTime();
                        const lobbyResponse = await fetch('/jeopardy/get-lobby-game-state', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Cache-Control': 'no-cache'
                            },
                            body: JSON.stringify({
                                lobby_code: this.lobbyCode,
                                _t: timestamp // Cache-busting parameter
                            })
                        });
                        
                        const lobbyData = await lobbyResponse.json();
                        if (lobbyData.success && lobbyData.game_state) {
                            this.gameState = lobbyData.game_state;
                            this.categories = Object.keys(lobbyData.game_state.custom_categories || {});
                            console.log('Fresh game state loaded from lobby');
                            console.log('Game state details:', {
                                teamCount: this.gameState.team_count,
                                categories: this.categories,
                                teamNames: this.gameState.team1 ? this.gameState.team1.name : 'N/A'
                            });
                        } else {
                            console.error('Failed to load fresh game state from lobby');
                            console.log('Lobby data received:', lobbyData);
                            
                            // MOBILE-FRIENDLY ERROR HANDLING
                            const isMobile = window.innerWidth <= 768;
                            if (isMobile) {
                                console.log('Mobile device detected, using mobile-friendly error handling');
                                
                                // Try to get game state from session as fallback
                                try {
                                    console.log('Attempting to load game state from session as fallback...');
                                    const sessionResponse = await fetch('/jeopardy/game-state');
                                    const sessionData = await sessionResponse.json();
                                    
                                    if (sessionData.success && sessionData.game_state) {
                                        console.log('Successfully loaded game state from session fallback');
                                        this.gameState = sessionData.game_state;
                                        this.categories = Object.keys(sessionData.game_state.custom_categories || {});
                                        
                                        // Continue with game initialization
                                        this.initializeGameForLobby();
                                        return;
                                    }
                                } catch (sessionError) {
                                    console.error('Session fallback also failed:', sessionError);
                                }
                                
                                // If all else fails, show mobile-friendly error and retry
                                this.showMobileFriendlyError('Game loading failed. Retrying...', () => {
                                    // Retry loading after 3 seconds
                                    setTimeout(() => {
                                        this.loadGameState();
                                    }, 3000);
                                });
                            } else {
                                // Desktop: Redirect back to lobby
                                this.showErrorNotification('Failed to load game state from lobby. Redirecting back to lobby...');
                                setTimeout(() => {
                                    window.location.href = `/jeopardy/lobby/${this.lobbyCode}`;
                                }, 2000);
                            }
                            return;
                        }
                        
                        // Initialize game for lobby games
                        this.initializeGameForLobby();
                        
                        // Force refresh game state to ensure we have latest team data
                        setTimeout(() => {
                            this.forceRefreshGameState();
                        }, 1000);
                    } else {
                        // For non-lobby games, get from session
                        const response = await fetch('/jeopardy/game-state');
                        const data = await response.json();
                    
                        console.log('Game state response:', data);
                        
                        if (data.success && data.game_state) {
                            this.gameState = data.game_state;
                            
                            // For non-lobby games, check if we have custom categories
                            if (data.game_state.custom_categories) {
                                this.categories = Object.keys(data.game_state.custom_categories);
                            } else {
                                console.error('No custom categories found in session');
                                this.showErrorNotification('Game not properly loaded. Please refresh the page.');
                                return;
                            }
                            
                            console.log('Game state loaded successfully:', {
                                teamCount: this.gameState.team_count,
                                categories: this.categories,
                                currentTeam: this.gameState.current_team,
                                hasCustomCategories: !!this.gameState.custom_categories,
                                currentPlayerTeam: data.current_player_team,
                                sessionId: data.session_id,
                                lobbyPlayers: data.lobby_players,
                                hostSessionId: data.host_session_id,
                                teamNames: this.gameState.team1 ? this.gameState.team1.name : 'N/A',
                                fullGameState: this.gameState
                            });
                            
                            // Store session information for host detection
                            if (data.session_id) {
                                sessionStorage.setItem('sessionId', data.session_id);
                            }
                            if (data.host_session_id) {
                                sessionStorage.setItem('hostSessionId', data.host_session_id);
                            }
                            
                            // Store the server-assigned player ID
                            if (data.current_player_team !== undefined) {
                                // Convert team number to player ID (001 = host, 002 = first player, etc.)
                                let playerId;
                                if (data.current_player_team === 0) {
                                    playerId = '001';
                                } else if (data.current_player_team === 1) {
                                    playerId = '002';
                                } else if (data.current_player_team === 2) {
                                    playerId = '003';
                                } else if (data.current_player_team === 3) {
                                    playerId = '004';
                                } else if (data.current_player_team === 4) {
                                    playerId = '005';
                                } else {
                                    playerId = '006';
                                }
                                
                                sessionStorage.setItem('playerId', playerId);
                                sessionStorage.setItem('playerTeam', data.current_player_team); // Keep for backward compatibility
                                console.log('Server assigned player ID:', playerId, 'Team:', data.current_player_team);
                                
                                // Check if player is host
                                if (playerId === '001') {
                                    console.log('Player is host - can participate in game');
                                }
                            } else {
                                // If no team assigned, try to auto-assign immediately
                                console.log('No player team assigned, attempting auto-assignment...');
                                this.autoAssignPlayer();
                            }
                            
                            // Use lobby code from server response if available
                            if (data.lobby_code && !this.lobbyCode) {
                                this.lobbyCode = data.lobby_code;
                                console.log('Lobby code found from server:', this.lobbyCode);
                                document.getElementById('lobbyIndicator').classList.remove('hidden');
                            }
                            
                            console.log('Final lobby code:', this.lobbyCode);
                            console.log('Lobby indicator visible:', !document.getElementById('lobbyIndicator').classList.contains('hidden'));
                            
                            // Optimize: Validate DOM elements and initialize game efficiently
                            if (!this.validateRequiredElements()) {
                                console.error('Required DOM elements missing, cannot initialize game');
                                this.showErrorNotification('Game interface not properly loaded. Please refresh the page.');
                                return;
                            }
                            
                            // Validate game state first
                            this.validateGameState();
                            
                            // Initialize game components immediately
                            this.generateTeamCards();
                            this.createGameBoard();
                            this.updateDisplay();
                            
                            // Optimize: Initialize timer display
                            const customTimer = data.game_state.custom_question_timer || 30;
                            this.initializeTimer(customTimer);
                            
                            // Start real-time synchronization immediately
                            this.startRealTimeSync();
                        } else {
                            console.error('Invalid game state received:', data);
                            
                            // Check if we're in a lobby game
                            if (this.lobbyCode) {
                                // For lobby games, show error and redirect back to lobby instead of custom game creator
                                const isMobile = window.innerWidth <= 768;
                                if (isMobile) {
                                    this.showMobileFriendlyError('Failed to load game state. Please try again.', () => {
                                        setTimeout(() => {
                                            this.loadGameState();
                                        }, 1000);
                                    });
                                } else {
                                    this.showErrorNotification('Failed to load game state. Redirecting back to lobby...');
                                    setTimeout(() => {
                                        window.location.href = `/jeopardy/lobby/${this.lobbyCode}`;
                                    }, 1000);
                                }
                            } else {
                                // For non-lobby games, redirect to custom game creator
                                this.showErrorNotification('No custom game found. Please create a custom game first.');
                                setTimeout(() => {
                                    window.location.href = '/jeopardy/custom-game';
                                }, 1000);
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error loading game state:', error);
                    
                    // Check if we're in a lobby game
                    if (this.lobbyCode) {
                        // For lobby games, redirect back to lobby instead of custom game creator
                        const isMobile = window.innerWidth <= 768;
                        if (isMobile) {
                            this.showMobileFriendlyError('Connection error. Please check your internet and try again.', () => {
                                setTimeout(() => {
                                    this.loadGameState();
                                }, 3000);
                            });
                        } else {
                            this.showErrorNotification('Error loading game state. Redirecting back to lobby...');
                            setTimeout(() => {
                                window.location.href = `/jeopardy/lobby/${this.lobbyCode}`;
                            }, 2000);
                        }
                    } else {
                        // For non-lobby games, redirect to custom game creator
                        this.showErrorNotification('Error loading game state. Please try again.');
                        setTimeout(() => {
                            window.location.href = '/jeopardy/custom-game';
                        }, 2000);
                    }
                }
            }

            startRealTimeSync() {
                // Sync game state every 2 seconds for better performance
                this.syncInterval = setInterval(() => {
                    this.syncGameState();
                }, 2000);
            }

            async syncGameState() {
                try {
                    // Optimize: Skip sync if user is actively answering or submitting
                    if (this.isSubmittingAnswer || this.isAnsweringQuestion) {
                        return;
                    }
                    
                    // Optimize: Skip sync if question modal is currently open and user is the one who selected it
                    const questionModal = document.getElementById('questionModal');
                    if (questionModal && !questionModal.classList.contains('hidden') && this.hasSelectedQuestion) {
                        return;
                    }
                    
                    // Optimize: Get lobby code once and cache it
                    if (!this.lobbyCode) {
                        const urlParams = new URLSearchParams(window.location.search);
                        this.lobbyCode = urlParams.get('lobby');
                        
                        if (!this.lobbyCode) {
                            return;
                        }
                    }

                    const response = await fetch('/jeopardy/get-lobby-game-state', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            lobby_code: this.lobbyCode
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success && data.game_state) {
                        // Validate game state before updating
                        this.validateGameState();
                        // Optimize: Only update if there are meaningful changes
                        this.updateLocalGameState(data.game_state);
                    }
                } catch (error) {
                    console.error('Error syncing game state:', error);
                }
            }

            updateLocalGameState(newGameState) {
                // Validate the new game state first
                if (newGameState && newGameState.team_count) {
                    for (let i = 1; i <= newGameState.team_count; i++) {
                        if (!newGameState[`team${i}`]) {
                            console.warn(`Team ${i} missing from synced game state, creating default team`);
                            newGameState[`team${i}`] = {
                                name: `Team ${i}`,
                                score: 0,
                                timer: newGameState.custom_game_timer || 300
                            };
                        }
                    }
                }
                
                // Only update if there are meaningful changes
                if (JSON.stringify(this.gameState) !== JSON.stringify(newGameState)) {
                    const oldGameState = this.gameState;
                    this.gameState = newGameState;
                    
                    console.log('updateLocalGameState - Old current team:', oldGameState?.current_team, 'New current team:', newGameState.current_team);
                    
                    // Validate game state after update
                    this.validateGameState();
                    
                    // Check if it just became this player's turn
                    const wasMyTurn = oldGameState ? !oldGameState.current_question : false;
                    const isMyTurn = !this.gameState.current_question;
                    
                    if (!wasMyTurn && isMyTurn) {
                        this.showYourTurnNotification();
                    }
                    
                    // Immediately update display and game board
                    this.updateDisplay();
                    this.updateGameBoard();
                    
                    // Sync team timers from other players
                    for (let i = 1; i <= this.gameState.team_count; i++) {
                        const teamKey = `team${i}`;
                        if (newGameState[teamKey] && newGameState[teamKey].timer !== undefined) {
                            this.gameState[teamKey].timer = newGameState[teamKey].timer;
                            this.updateTeamTimer(i, newGameState[teamKey].timer);
                        }
                    }
                    
                    // Handle question timer synchronization from other players
                    if (newGameState.question_timer !== undefined && 
                        (!oldGameState || oldGameState.question_timer !== newGameState.question_timer)) {
                        // Update the timer display without starting a new interval
                        this.updateTimerDisplay(newGameState.question_timer);
                        console.log('Question timer updated from other player:', newGameState.question_timer);
                    }
                    
                                         // Check if a new question was selected
                     if (newGameState.current_question && (!oldGameState || !oldGameState.current_question)) {
                         // Check if this is a simplified question (from other players) or full question (from server)
                         if (newGameState.current_question.selected && !newGameState.current_question.question) {
                             // This is a simplified question from other players - highlight the cell and show timer
                             this.highlightSelectedQuestion(newGameState.current_question);
                                                         // Start timer for other players to see the countdown
                            if (newGameState.question_timer) {
                                this.startTimerForOtherPlayers(newGameState.question_timer);
                            } else {
                                // Use custom question timer if no specific timer provided
                                const customTimer = this.gameState.custom_question_timer || 30;
                                this.startTimerForOtherPlayers(customTimer);
                            }
                             
                             // Show a read-only question modal for other players showing which question was selected
                             this.showQuestionForOtherPlayers(newGameState.current_question, newGameState.current_team);
                         } else {
                             // This is a full question from the server - show it to the current player only
                             this.showQuestion(newGameState.current_question, newGameState.is_steal_attempt || false);
                             this.startTimer(newGameState.question_timer || 30);
                             this.startTeamTimer(); // Start team timer updates
                         }
                                           } else if (!newGameState.current_question && oldGameState && oldGameState.current_question) {
                          // Question was answered - only hide modal if we're not currently answering
                          if (!this.isAnsweringQuestion && !this.isSubmittingAnswer) {
                              this.hideQuestionModal();
                              this.removeQuestionHighlight();
                              if (this.timerInterval) {
                                  clearInterval(this.timerInterval);
                                  this.timerInterval = null;
                              }
                              // Stop team timer when question is answered
                              if (this.teamTimerInterval) {
                                  clearInterval(this.teamTimerInterval);
                                  this.teamTimerInterval = null;
                              }
                          }
                      }
                    
                    // Don't sync timer from server to avoid conflicts
                    // Each player manages their own timer locally
                }
            }

            highlightSelectedQuestion(question) {
                // Highlight the selected question cell for other players
                const cells = document.querySelectorAll('[data-category][data-value]');
                cells.forEach(cell => {
                    const category = cell.dataset.category;
                    const value = cell.dataset.value;
                    
                    if (category === question.category && value == question.value) {
                        cell.classList.add('selected-question');
                        cell.style.background = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
                        cell.style.border = '3px solid #60a5fa';
                        cell.style.boxShadow = '0 0 20px rgba(59, 130, 246, 0.5)';
                        
                        // Add a pulsing animation
                        cell.style.animation = 'question-selected-pulse 2s ease-in-out infinite';
                    }
                });
                
                // Show notification that a question was selected
                this.showQuestionSelectedNotification(question.category, question.value);
            }

            removeQuestionHighlight() {
                // Remove highlight from all question cells
                const cells = document.querySelectorAll('[data-category][data-value]');
                cells.forEach(cell => {
                    cell.classList.remove('selected-question');
                    cell.style.background = '';
                    cell.style.border = '';
                    cell.style.boxShadow = '';
                    cell.style.animation = '';
                });
            }

            showQuestionSelectedNotification(category, value) {
                // Create notification for other players
                const currentTeam = this.gameState[`team${this.gameState.current_team}`];
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-blue-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.textContent = `${currentTeam.name} selected: ${category} - ${value} points`;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            async updateLobbyGameState() {
                try {
                    // Use stored lobby code or get from URL
                    let lobbyCode = this.lobbyCode;
                    
                    if (!lobbyCode) {
                        const urlParams = new URLSearchParams(window.location.search);
                        lobbyCode = urlParams.get('lobby');
                    }
                    
                    if (!lobbyCode) {
                        console.log('No lobby code available for update');
                        return;
                    }

                    console.log('Updating lobby game state with code:', lobbyCode);

                    await fetch('/jeopardy/update-lobby-game-state', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            lobby_code: lobbyCode,
                            game_state: this.gameState
                        })
                    });
                    
                    console.log('Lobby game state updated successfully');
                } catch (error) {
                    console.error('Error updating lobby game state:', error);
                }
            }

            async updateLobbyGameStateWithData(data) {
                try {
                    // Use stored lobby code or get from URL
                    let lobbyCode = this.lobbyCode;
                    
                    if (!lobbyCode) {
                        const urlParams = new URLSearchParams(window.location.search);
                        lobbyCode = urlParams.get('lobby');
                    }
                    
                    if (!lobbyCode) {
                        console.log('No lobby code available for update');
                        return;
                    }

                    console.log('Updating lobby game state with data:', data);

                    await fetch('/jeopardy/update-lobby-game-state', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            lobby_code: lobbyCode,
                            game_state: data
                        })
                    });
                    
                    console.log('Lobby game state updated successfully with data');
                } catch (error) {
                    console.error('Error updating lobby game state with data:', error);
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
                    teamCard.className = `flex items-center space-x-2 sm:space-x-4 bg-gray-700 rounded-lg p-2 sm:p-4 flex-1 transition-all duration-300 team-card ${i > 1 ? 'ml-2' : 'mr-2'}`;
                    
                    teamCard.innerHTML = `
                        <div class="w-3 h-3 sm:w-4 sm:h-4 bg-${color}-500 rounded-full flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <h3 id="team${i}Name" class="font-bold text-sm sm:text-lg text-${color}-400">${team.name}</h3>
                            <p id="team${i}Score" class="text-lg sm:text-2xl font-bold text-white">${team.score} ${team.score === 1 ? 'point' : 'points'}</p>
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
                
                console.log(`Generated ${this.gameState.team_count} team cards`);
            }

            createGameBoard() {
                const gameBoard = document.getElementById('gameBoard');
                gameBoard.innerHTML = '';
                
                // Apply responsive CSS classes based on category count and screen size
                this.applyResponsiveGameBoardClasses();
                
                // For single category games, use a 3-column layout
                const questionCount = this.gameState.question_count || 5;
                this.values = Array.from({length: questionCount}, (_, i) => i + 1);
                
                if (this.categories.length === 1) {
                    // Single category layout - 3 columns, questions arranged in rows
                    gameBoard.className = 'grid gap-2 sm:gap-4 mb-4 sm:mb-8 single-category';
                    gameBoard.style.gridTemplateColumns = 'repeat(3, 1fr)';
                    gameBoard.style.setProperty('grid-template-columns', 'repeat(3, 1fr)', 'important');
                    
                    // Create category header spanning all 3 columns
                    const categoryDiv = document.createElement('div');
                    categoryDiv.className = 'text-center py-2 sm:py-4 rounded-lg text-sm sm:text-lg font-bold category-header col-span-3';
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
                        cell.setAttribute('data-click-attached', 'true');
                        
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
                        categoryDiv.className = 'text-center py-2 sm:py-4 rounded-lg text-sm sm:text-lg font-bold category-header';
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
                            cell.setAttribute('data-click-attached', 'true');
                            
                            gameBoard.appendChild(cell);
                        });
                    });
                }
            }

            async selectQuestion(category, value) {
                console.log('=== SELECT QUESTION START ===');
                console.log('selectQuestion called:', category, value);
                console.log('Current game state:', this.gameState);
                console.log('Current categories:', this.categories);
                
                try {
                    // Validate that game state is properly initialized
                    if (!this.gameState || !this.gameState.custom_categories) {
                        console.error('Game state not properly initialized');
                        this.showErrorNotification('Game not properly loaded. Please refresh the page.');
                        return;
                    }
                    
                    console.log('Game state validation passed');
                    
                    // Validate that the category exists
                    if (!this.gameState.custom_categories[category]) {
                        console.error('Category not found:', category);
                        console.log('Available categories:', Object.keys(this.gameState.custom_categories));
                        this.showErrorNotification('Category not found. Please refresh the page.');
                        return;
                    }
                    
                    console.log('Category validation passed');
                    
                    // Validate that the value exists in the category
                    if (!this.gameState.custom_categories[category][value]) {
                        console.error('Value not found in category:', category, value);
                        console.log('Available values in category:', Object.keys(this.gameState.custom_categories[category]));
                        this.showErrorNotification('Question not found. Please refresh the page.');
                        return;
                    }
                    
                    console.log('Value validation passed');
                    
                    // Check if there's a question being answered
                    const hasCurrentQuestion = this.gameState.current_question;
                    console.log('Question validation check:', 'hasCurrentQuestion:', hasCurrentQuestion);
                    
                    if (hasCurrentQuestion) {
                        console.log('Question in progress, ignoring click');
                        this.showQuestionInProgressNotification();
                        return;
                    }
                    
                    console.log('Question validation passed');
                    
                    // Prevent multiple simultaneous requests
                    if (this.isProcessingQuestion) {
                        console.log('Already processing a question, ignoring click');
                        return;
                    }
                    
                    console.log('Processing flag validation passed');
                    
                    this.isProcessingQuestion = true;
                    console.log('Set processing flag to true');
                    
                    // Add visual feedback to the clicked cell
                    const cell = document.querySelector(`[data-category="${category}"][data-value="${value}"]`);
                    let originalText = '';
                    if (cell) {
                        originalText = cell.textContent;
                        cell.textContent = 'Loading...';
                        cell.style.opacity = '0.7';
                        console.log('Added visual feedback to cell');
                    }
                    
                    const questionKey = `${category}_${value}`;
                    console.log('Question key:', questionKey);
                    
                    // Check if question is already answered
                    const isAnswered = this.gameState.answered_questions && this.gameState.answered_questions.some(q => 
                        typeof q === 'string' ? q === questionKey : q.key === questionKey
                    );
                    
                    if (isAnswered) {
                        console.log('Question already answered, returning');
                        this.isProcessingQuestion = false;
                        // Restore cell appearance
                        if (cell) {
                            cell.textContent = originalText;
                            cell.style.opacity = '1';
                        }
                        return; // Question already answered
                    }
                    
                    console.log('Question not already answered, proceeding...');
                    
                    console.log('About to make fetch request to /jeopardy/question');
                    
                    const response = await fetch('/jeopardy/question', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ category, value })
                    });
                    
                    console.log('Fetch request completed, status:', response.status);
                    
                    const data = await response.json();
                    console.log('Response data:', data);
                    
                    if (data.success && data.question) {
                        console.log('Question selection successful, processing response...');
                        
                        // Validate that we have the required question data
                        if (!data.question.question || !data.question.answer) {
                            console.error('Invalid question data received:', data.question);
                            throw new Error('Invalid question data received from server');
                        }
                        
                        console.log('Question data validation passed');
                        
                        // Validate question structure
                        const requiredFields = ['question', 'answer', 'category', 'value'];
                        const missingFields = requiredFields.filter(field => !data.question[field]);
                        
                        if (missingFields.length > 0) {
                            console.error('Question missing required fields:', missingFields);
                            throw new Error('Question data is incomplete');
                        }
                        
                        console.log('Question structure validation passed');
                        
                        // Update local game state immediately
                        this.gameState.current_question = data.question;
                        this.gameState.question_timer = data.timer;
                        this.gameState.is_steal_attempt = data.is_steal_attempt;
                        
                        console.log('Updated local game state with question:', this.gameState.current_question);
                        
                                                 // Show the question to the current player
                         console.log('About to show question...');
                         this.hasSelectedQuestion = true; // Mark that current player selected this question
                         this.showQuestion(data.question, data.is_steal_attempt);
                         console.log('Question shown successfully');
                        
                        console.log('About to start timer...');
                        this.startTimer(data.timer || this.gameState.custom_question_timer || 30);
                        console.log('Timer started successfully');
                        
                        console.log('About to start team timer...');
                        this.startTeamTimer(); // Start team timer updates
                        console.log('Team timer started successfully');
                        
                        // Create a simplified game state for other players that shows question selection and timer
                        const syncGameState = {
                            ...this.gameState,
                            current_question: {
                                category: data.question.category,
                                value: data.question.value,
                                selected: true
                                // Note: No question or answer content is sent to other players
                            },
                            question_timer: data.timer // Include the timer so other players see it immediately
                        };
                        
                        console.log('About to update lobby game state...');
                        // Update lobby with simplified game state (don't overwrite local state)
                        await this.updateLobbyGameStateWithData(syncGameState);
                        console.log('Lobby game state updated successfully');
                        
                        // Force a quick sync to ensure all players see the question selection and timer
                        setTimeout(() => {
                            this.syncGameState();
                            // Also update the game board to reflect the new state
                            this.updateGameBoard();
                        }, 500);
                        
                        console.log('=== SELECT QUESTION SUCCESS ===');
                    } else {
                        console.error('Question selection failed:', data);
                        
                        // Handle specific error cases
                        if (data.error === 'Not your turn') {
                            console.log('Server confirmed: Not your turn');
                            console.log('Server response data:', data);
                            console.log('Current team from server:', data.current_team);
                            console.log('Player team from server:', data.player_team);
                            console.log('Game state teams:', this.gameState);
                            
                            const currentTeam = this.gameState[`team${data.current_team}`];
                            console.log('Current team object:', currentTeam);
                            
                            if (currentTeam) {
                                this.showNotYourTurnNotification(currentTeam.name);
                            } else {
                                this.showNotYourTurnNotification(`Team ${data.current_team}`);
                            }
                            
                            // Restore cell appearance
                            if (cell) {
                                cell.textContent = originalText;
                                cell.style.opacity = '1';
                            }
                            return; // Don't throw error, just return
                        }
                        
                        // Show error notification and restore cell appearance
                        this.showErrorNotification(data.error || 'Failed to select question');
                        if (cell) {
                            cell.textContent = originalText;
                            cell.style.opacity = '1';
                        }
                        return; // Don't throw error, just return
                    }
                } catch (error) {
                    console.error('=== SELECT QUESTION ERROR ===');
                    console.error('Error selecting question:', error);
                    console.error('Error stack:', error.stack);
                    // Restore cell appearance on error
                    if (cell) {
                        cell.textContent = originalText;
                        cell.style.opacity = '1';
                    }
                    // Show error notification to user
                    this.showErrorNotification('Failed to select question. Please try again.');
                } finally {
                    // Reset the flag after processing
                    this.isProcessingQuestion = false;
                    console.log('Reset processing flag to false');
                    console.log('=== SELECT QUESTION END ===');
                }
            }

                         showQuestion(question, isStealAttempt = false) {
                 console.log('=== SHOW QUESTION START ===');
                 console.log('showQuestion called with:', { question, isStealAttempt });
                 
                 try {
                     // Validate that question has required properties
                     if (!question || !question.question) {
                         console.error('Invalid question object:', question);
                         this.showErrorNotification('Error: Invalid question data');
                         return;
                     }
                     
                     console.log('Question validation passed');
                     
                     const questionTextElement = document.getElementById('questionText');
                     const answerInputElement = document.getElementById('answerInput');
                     const questionModalElement = document.getElementById('questionModal');
                     
                     console.log('DOM elements found:', {
                         questionText: !!questionTextElement,
                         answerInput: !!answerInputElement,
                         questionModal: !!questionModalElement
                     });
                     
                     // Validate that all required elements exist
                     if (!questionTextElement || !answerInputElement || !questionModalElement) {
                         console.error('Required modal elements not found');
                         this.showErrorNotification('Error: Question modal not found');
                         return;
                     }
                     
                     console.log('DOM element validation passed');
                     
                      // This method is now only used for showing full questions to the current player
                      console.log('Current player\'s turn, showing full question');
                      
                      // Show the actual question for the current player
                      questionTextElement.textContent = question.question;
                      
                      // Show the answer input for the current player
                      answerInputElement.style.display = 'block';
                      answerInputElement.value = '';
                      
                      // Restore the submit button functionality
                      const submitBtn = document.getElementById('submitAnswerBtn');
                      submitBtn.textContent = 'Submit Answer';
                      submitBtn.onclick = (e) => {
                          e.preventDefault();
                          e.stopPropagation();
                          this.submitAnswer();
                      };
                      
                      // Set flag to indicate user is actively answering
                      this.isAnsweringQuestion = true;
                      
                      // Focus the answer input
                      answerInputElement.focus();
                     
                     console.log('Question text set successfully');
                     console.log('Showing question modal');
                     questionModalElement.classList.remove('hidden');
                     console.log('Question modal shown');
                     
                     // Show steal notification if it's a steal attempt (only for current player)
                     if (isStealAttempt && this.isCurrentPlayerTurn()) {
                         console.log('Showing steal notification');
                         this.showStealNotification();
                     }
                     
                     console.log('=== SHOW QUESTION SUCCESS ===');
                 } catch (error) {
                     console.error('=== SHOW QUESTION ERROR ===');
                     console.error('Error in showQuestion:', error);
                     console.error('Error stack:', error.stack);
                     this.showErrorNotification('Error displaying question. Please try again.');
                 }
             }

            startTimerForOtherPlayers(duration) {
                console.log('=== START TIMER FOR OTHER PLAYERS START ===');
                console.log('startTimerForOtherPlayers called with duration:', duration);
                
                try {
                    if (this.timerInterval) {
                        console.log('Clearing existing timer interval');
                        clearInterval(this.timerInterval);
                    }
                    
                    let timeLeft = duration || (this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30);
                    console.log('Initial time left for other players:', timeLeft);
                    
                    console.log('Updating timer display for other players');
                    this.updateTimerDisplay(timeLeft);
                    console.log('Timer display updated for other players');
                    
                    console.log('Setting up timer interval for other players');
                    this.timerInterval = setInterval(() => {
                        timeLeft--;
                        
                        if (timeLeft < 0) {
                            timeLeft = 0;
                        }
                        
                        this.updateTimerDisplay(timeLeft);
                        
                        // Update game state timer locally
                        if (this.gameState) {
                            this.gameState.question_timer = timeLeft;
                        }
                        
                        // Sync timer with other players every second
                        this.syncQuestionTimer(timeLeft);
                        
                        if (timeLeft <= 0) {
                            console.log('Timer reached zero for other players, clearing interval');
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                            // Don't call timeUp() for other players - they just see the timer
                        }
                    }, 1000);
                    
                    console.log('Timer interval set successfully for other players');
                    console.log('=== START TIMER FOR OTHER PLAYERS SUCCESS ===');
                } catch (error) {
                    console.error('=== START TIMER FOR OTHER PLAYERS ERROR ===');
                    console.error('Error in startTimerForOtherPlayers:', error);
                    console.error('Error stack:', error.stack);
                }
            }

            startTimer(duration) {
                console.log('=== START TIMER START ===');
                console.log('startTimer called with duration:', duration);
                
                try {
                    if (this.timerInterval) {
                        console.log('Clearing existing timer interval');
                        clearInterval(this.timerInterval);
                    }
                    
                    let timeLeft = duration || (this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30);
                    console.log('Initial time left:', timeLeft);
                    
                    console.log('Updating timer display');
                    this.updateTimerDisplay(timeLeft);
                    console.log('Timer display updated');
                    
                    console.log('Setting up timer interval');
                    this.timerInterval = setInterval(() => {
                        timeLeft--;
                        
                        if (timeLeft < 0) {
                            timeLeft = 0;
                        }
                        
                        this.updateTimerDisplay(timeLeft);
                        
                        // Update game state timer locally
                        if (this.gameState) {
                            this.gameState.question_timer = timeLeft;
                        }
                        
                        // Sync timer with other players every second
                        this.syncQuestionTimer(timeLeft);
                        
                        if (timeLeft <= 0) {
                            console.log('Timer reached zero, clearing interval');
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                            this.timeUp();
                        }
                    }, 1000);
                    
                    console.log('Timer interval set successfully');
                    console.log('=== START TIMER SUCCESS ===');
                } catch (error) {
                    console.error('=== START TIMER ERROR ===');
                    console.error('Error in startTimer:', error);
                    console.error('Error stack:', error.stack);
                }
            }

            async syncQuestionTimer(timeLeft) {
                try {
                    // Use stored lobby code or get from URL
                    let lobbyCode = this.lobbyCode;
                    
                    if (!lobbyCode) {
                        const urlParams = new URLSearchParams(window.location.search);
                        lobbyCode = urlParams.get('lobby');
                    }
                    
                    if (!lobbyCode) {
                        return; // No lobby code, skip sync
                    }

                    // Create a simplified game state with just the timer for synchronization
                    const syncGameState = {
                        ...this.gameState,
                        question_timer: timeLeft
                    };

                    await fetch('/jeopardy/update-lobby-game-state', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            lobby_code: lobbyCode,
                            game_state: syncGameState
                        })
                    });
                    
                    console.log('Question timer synced:', timeLeft);
                } catch (error) {
                    console.error('Error syncing question timer:', error);
                }
            }

            startTeamTimer() {
                // Start a separate interval for team timer updates
                if (this.teamTimerInterval) {
                    clearInterval(this.teamTimerInterval);
                }
                
                this.teamTimerInterval = setInterval(() => {
                    if (this.gameState && this.gameState.current_team) {
                        const currentTeam = 'team' + this.gameState.current_team;
                        if (this.gameState[currentTeam] && this.gameState[currentTeam].timer > 0) {
                            this.gameState[currentTeam].timer = Math.max(0, this.gameState[currentTeam].timer - 1);
                            this.updateTeamTimer(this.gameState.current_team, this.gameState[currentTeam].timer);
                            
                            // Sync timer with other players every 2 seconds
                            if (this.gameState[currentTeam].timer % 2 === 0) {
                                this.updateLobbyGameState();
                            }
                        }
                    }
                }, 1000);
            }

            initializeTimer(maxTimer) {
                const timerProgress = document.getElementById('timerProgress');
                const timerDisplay = document.getElementById('timerDisplay');
                const timerContainer = timerProgress.parentElement;
                
                // Show full circle (no countdown)
                timerProgress.style.background = `conic-gradient(from 0deg, #10b981 0deg, #10b981 360deg)`;
                timerContainer.classList.remove('pulse-glow');
                
                // Show max time
                timerDisplay.textContent = maxTimer;
            }

            updateTimerDisplay(timeLeft) {
                // Ensure timeLeft is not negative
                timeLeft = Math.max(0, timeLeft);
                
                document.getElementById('timerDisplay').textContent = timeLeft;
                
                // Get the maximum timer value (custom or default 30)
                const maxTimer = this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30;
                const percentage = (timeLeft / maxTimer) * 100;
                
                // Calculate the angle for the conic gradient (360 degrees = full circle)
                const angle = (percentage / 100) * 360;
                
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

            async submitAnswer() {
                // Prevent multiple simultaneous submissions
                if (this.isSubmittingAnswer) {
                    console.log('Already submitting answer, ignoring click');
                    return;
                }
                
                this.isSubmittingAnswer = true;
                
                // Add visual feedback
                const submitBtn = document.getElementById('submitAnswerBtn');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Submitting...';
                submitBtn.disabled = true;
                
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
                
                if (this.teamTimerInterval) {
                    clearInterval(this.teamTimerInterval);
                    this.teamTimerInterval = null;
                }
                
                const maxTimer = this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30;
                const timeTaken = maxTimer - parseInt(document.getElementById('timerDisplay').textContent);
                
                this.initializeTimer(maxTimer);
                
                const answer = document.getElementById('answerInput').value;
                
                try {
                    const response = await fetch('/jeopardy/answer', {
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
                        // Store the team that answered before updating game state
                        const teamThatAnswered = this.gameState.current_team;
                        const teamName = this.gameState[`team${teamThatAnswered}`].name;
                        
                        this.gameState = data.game_state;
                        
                        this.hideQuestionModal();
                        this.updateDisplay();
                        
                        setTimeout(() => {
                            this.updateGameBoard();
                            
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
                            
                            setTimeout(() => {
                                this.updateGameBoard();
                            }, 100);
                        }, 50);
                        
                        this.showAnswerResult(data.is_correct, data.correct_answer, data.is_steal_attempt, data.points_earned || 0, teamName);
                        
                        // Immediately update lobby game state after submitting answer
                        await this.updateLobbyGameState();
                        
                        // Force a quick sync to ensure all players see the result
                        setTimeout(() => {
                            this.syncGameState();
                        }, 500);
                        
                        // Always advance to next team's turn after answering (correct or incorrect)
                        setTimeout(() => {
                            this.advanceToNextTeam();
                        }, 2000); // Wait 2 seconds to show the answer result, then advance turn
                        
                        if (this.gameState.game_over) {
                            this.showGameOver();
                        }
                    }
                } catch (error) {
                    console.error('Error submitting answer:', error);
                                 } finally {
                     // Reset the flags and button state after processing
                     this.isSubmittingAnswer = false;
                     this.isAnsweringQuestion = false;
                     this.hasSelectedQuestion = false;
                     submitBtn.textContent = originalText;
                     submitBtn.disabled = false;
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
                    const response = await fetch('/jeopardy/timer', {
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
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
                
                if (this.teamTimerInterval) {
                    clearInterval(this.teamTimerInterval);
                    this.teamTimerInterval = null;
                }
                
                const maxTimer = this.gameState && this.gameState.custom_question_timer ? this.gameState.custom_question_timer : 30;
                this.initializeTimer(maxTimer);
                
                // Show steal notification when timer runs out
                this.showStealNotification();
                
                this.hideQuestionModal();
                
                // Use the same turn advancement function to prevent conflicts
                console.log('Time expired, advancing to next team...');
                await this.advanceToNextTeam();
                
                // Show notification that time's up and next team's turn
                this.showTimeUpNotification();
            }

                         hideQuestionModal() {
                 const questionModal = document.getElementById('questionModal');
                 const answerInput = document.getElementById('answerInput');
                 const submitBtn = document.getElementById('submitAnswerBtn');
                 
                 // Reset the modal state
                 questionModal.classList.add('hidden');
                 
                 // Reset the answer input display and functionality
                 if (answerInput) {
                     answerInput.style.display = 'block';
                     answerInput.value = '';
                 }
                 
                 // Reset the submit button
                 if (submitBtn) {
                     submitBtn.textContent = 'Submit Answer';
                     submitBtn.onclick = (e) => {
                         e.preventDefault();
                         e.stopPropagation();
                         this.submitAnswer();
                     };
                 }
                 
                 // Reset the answering flag and question selection flag
                 this.isAnsweringQuestion = false;
                 this.hasSelectedQuestion = false;
             }

            updateDisplay() {
                try {
                    // Validate game state first
                    if (!this.gameState || !this.gameState.team_count) {
                        console.warn('Game state not ready, skipping display update');
                        return;
                    }
                    
                    for (let i = 1; i <= this.gameState.team_count; i++) {
                        const team = this.gameState[`team${i}`];
                        if (!team) {
                            console.warn(`Team ${i} not found in game state`);
                            continue;
                        }
                        
                        const teamNameElement = document.getElementById(`team${i}Name`);
                        const teamScoreElement = document.getElementById(`team${i}Score`);
                        const teamCardElement = document.getElementById(`team${i}Card`);
                        
                        if (teamNameElement) teamNameElement.textContent = team.name || `Team ${i}`;
                        if (teamScoreElement) teamScoreElement.textContent = `${team.score || 0} ${(team.score || 0) === 1 ? 'point' : 'points'}`;
                        if (teamCardElement) {
                            teamCardElement.classList.toggle('team-active', this.gameState.current_team === i);
                        }
                        
                        this.updateTeamTimer(i, team.timer || 300);
                    }
                
                // Update turn indicator
                const turnIndicator = document.getElementById('turnIndicator');
                
                if (turnIndicator) {
                    try {
                        // Check if this is a single-player game
                        const urlParams = new URLSearchParams(window.location.search);
                        const isSinglePlayer = urlParams.get('mode') === 'singleplayer';
                        
                        // For single-player games, ensure we're not in observer mode
                        if (isSinglePlayer) {
                            // Set player team to 1 for single-player games
                            sessionStorage.setItem('playerTeam', '1');
                        }
                        
                        if (this.isCurrentPlayerTurn()) {
                        turnIndicator.classList.remove('hidden');
                        // Show player name if available
                        const currentPlayerId = this.gameState.current_player_id;
                        const currentTeam = this.gameState[`team${this.gameState.current_team}`];
                        const isHost = currentPlayerId === (this.gameState.host_player_id || '001');
                        
                        // Debug logging
                        console.log('Turn indicator debug:', {
                            currentTeam: this.gameState.current_team,
                            currentTeamData: currentTeam,
                            currentPlayerId: currentPlayerId,
                            teamCount: this.gameState.team_count,
                            isHost: isHost,
                            allTeams: Object.keys(this.gameState).filter(key => key.startsWith('team')).map(key => ({
                                key: key,
                                data: this.gameState[key]
                            }))
                        });
                        
                                                 if (currentTeam && currentTeam.name) {
                            turnIndicator.innerHTML = `<span>🎯 ${currentTeam.name}'s Turn!</span>`;
                        } else if (currentPlayerId) {
                            turnIndicator.innerHTML = `<span>🎯 Player ${currentPlayerId}'s Turn!</span>`;
                        } else {
                            turnIndicator.innerHTML = '<span>🎯 Your Turn!</span>';
                        }
                        turnIndicator.style.background = 'linear-gradient(135deg, #10b981 0%, #34d399 100%)';
                        turnIndicator.style.border = '2px solid #6ee7b7';
                        turnIndicator.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.3)';
                    } else {
                        turnIndicator.classList.remove('hidden');
                        const currentTeam = this.gameState[`team${this.gameState.current_team}`];
                        const currentPlayerId = this.gameState.current_player_id;
                        const isHost = currentPlayerId === (this.gameState.host_player_id || '001');
                        
                        // Debug logging
                        console.log('Other player turn debug:', {
                            currentTeam: this.gameState.current_team,
                            currentTeamData: currentTeam,
                            currentPlayerId: currentPlayerId,
                            teamCount: this.gameState.team_count,
                            isHost: isHost
                        });
                        
                        // Show player name if available, otherwise show team name
                        if (currentTeam && currentTeam.name) {
                            turnIndicator.innerHTML = `<span>⏳ ${currentTeam.name}'s Turn</span>`;
                        } else if (currentPlayerId) {
                            turnIndicator.innerHTML = `<span>⏳ Player ${currentPlayerId}'s Turn</span>`;
                        } else {
                            // Fallback to team number if team data is missing
                            const teamNumber = this.gameState.current_team || 'Unknown';
                            turnIndicator.innerHTML = `<span>⏳ Team ${teamNumber}'s Turn</span>`;
                        }
                        
                        // Additional debugging for team data
                        console.log('Team data debug:', {
                            currentTeamNumber: this.gameState.current_team,
                            currentTeamData: currentTeam,
                            allTeams: Object.keys(this.gameState).filter(key => key.startsWith('team')).map(key => ({
                                key: key,
                                data: this.gameState[key]
                            }))
                        });
                        turnIndicator.style.background = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
                        turnIndicator.style.border = '2px solid #fbbf24';
                        turnIndicator.style.boxShadow = '0 0 20px rgba(245, 158, 11, 0.3)';
                    }
                    } catch (error) {
                        console.error('Error updating turn indicator:', error);
                        // Show a fallback turn indicator
                        turnIndicator.classList.remove('hidden');
                        turnIndicator.innerHTML = '<span>⏳ Loading...</span>';
                        turnIndicator.style.background = 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)';
                    }
                }
                
                // Also update the game board to reflect turn changes
                this.updateGameBoard();
            } catch (error) {
                console.error('Error in updateDisplay:', error);
                // Show error notification
                this.showErrorNotification('Error updating display. Please refresh the page.');
            }
            }

            updateTeamTimer(teamNumber, seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                const timeString = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
                
                const timerElement = document.getElementById(`team${teamNumber}Timer`);
                if (timerElement) {
                    timerElement.textContent = timeString;
                }
                
                const maxGameTimer = this.gameState && this.gameState.custom_game_timer ? this.gameState.custom_game_timer : 300;
                const percentage = (seconds / maxGameTimer) * 100;
                const timerBarElement = document.getElementById(`team${teamNumber}TimerBar`);
                if (timerBarElement) {
                    timerBarElement.style.width = `${percentage}%`;
                }
            }

            showGameOver() {
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
                const cells = document.querySelectorAll('[data-category][data-value]');
                
                if (cells.length === 0) {
                    return;
                }
                
                const hasCurrentQuestion = this.gameState.current_question;
                
                console.log('UpdateGameBoard - hasCurrentQuestion:', hasCurrentQuestion);
                
                cells.forEach((cell, index) => {
                    const category = cell.dataset.category;
                    const value = cell.dataset.value;
                    const questionKey = `${category}_${value}`;
                        
                    let isAnswered = false;
                    let wasCorrect = false;
                    
                    if (this.gameState.answered_questions) {
                        const answeredQuestion = this.gameState.answered_questions.find(q => 
                            typeof q === 'string' ? q === questionKey : q.key === questionKey
                        );
                        if (answeredQuestion) {
                            isAnswered = true;
                            // Debug logging to see what we're getting
                            console.log('Answered question data:', answeredQuestion);
                            console.log('Question key:', questionKey);
                            console.log('Type of answeredQuestion:', typeof answeredQuestion);
                            console.log('answeredQuestion.correct:', answeredQuestion.correct);
                            
                            wasCorrect = typeof answeredQuestion === 'string' ? true : answeredQuestion.correct;
                            console.log('Final wasCorrect value:', wasCorrect);
                        }
                    }
                    
                    if (isAnswered) {
                        cell.className = wasCorrect ? 'answered correct' : 'answered incorrect';
                        cell.textContent = '';
                        cell.style.cursor = 'not-allowed';
                        cell.style.pointerEvents = 'none';
                        
                        const symbol = document.createElement('div');
                        symbol.className = wasCorrect ? 'checkmark' : 'x-mark';
                        symbol.textContent = wasCorrect ? '✓' : '✗';
                        symbol.style.position = 'absolute';
                        symbol.style.top = '50%';
                        symbol.style.left = '50%';
                        symbol.style.transform = 'translate(-50%, -50%)';
                        symbol.style.fontSize = '2rem';
                        symbol.style.fontWeight = 'bold';
                        symbol.style.color = wasCorrect ? '#10b981' : '#fca5a5';
                        symbol.style.zIndex = '2';
                        cell.appendChild(symbol);
                    } else {
                        // Only update the visual properties, don't recreate the element
                        cell.className = 'question-value text-center py-6 rounded-lg text-xl font-bold';
                        cell.textContent = `${value} ${value === 1 ? 'point' : 'points'}`;
                        
                        // Only disable cells if there's a question being answered
                        if (!hasCurrentQuestion) {
                            cell.style.cursor = 'pointer';
                            cell.style.pointerEvents = 'auto';
                            cell.style.opacity = '1';
                            cell.style.transform = 'scale(1)';
                            cell.style.transition = 'all 0.3s ease';
                            cell.style.boxShadow = '0 4px 15px rgba(245, 158, 11, 0.3)';
                        } else {
                            // Disable cell only if there's a question being answered
                            cell.style.cursor = 'not-allowed';
                            cell.style.pointerEvents = 'none';
                            cell.style.opacity = '0.4';
                            cell.style.transform = 'scale(0.98)';
                            cell.style.transition = 'all 0.3s ease';
                            cell.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
                            cell.style.filter = 'grayscale(30%)';
                        }
                        
                        // Remove any existing symbols
                        const existingSymbol = cell.querySelector('.checkmark, .x-mark');
                        if (existingSymbol) {
                            existingSymbol.remove();
                        }
                        
                        // Ensure click event listener is attached (only if not already attached)
                        if (!cell.hasAttribute('data-click-attached')) {
                            cell.addEventListener('click', () => {
                                // Only check if there's a question being answered
                                const hasCurrentQuestion = this.gameState.current_question;
                                
                                console.log('Click event - hasCurrentQuestion:', hasCurrentQuestion);
                                
                                if (!hasCurrentQuestion) {
                                    console.log('Allowing question selection');
                                    this.selectQuestion(category, value);
                                } else {
                                    console.log('Blocking question selection - question in progress');
                                    // Show a visual feedback when clicking on disabled cells
                                    cell.style.transform = 'scale(0.95)';
                                    setTimeout(() => {
                                        cell.style.transform = 'scale(0.98)';
                                    }, 150);
                                    
                                    // Show notification that a question is in progress
                                    this.showQuestionInProgressNotification();
                                }
                            });
                            cell.setAttribute('data-click-attached', 'true');
                        }
                    }
                });
                
                const questionCount = this.gameState.question_count || 5;
                const totalQuestions = this.categories.length * questionCount;
                if (this.gameState.answered_questions && this.gameState.answered_questions.length >= totalQuestions) {
                    this.showGameOver();
                }
            }

                                     showAnswerResult(isCorrect, correctAnswer, isStealAttempt = false, pointsEarned = 0, teamName = '') {
                let message = '';
                let color = '';
                
                // Use the provided team name, or fall back to current team if not provided
                if (!teamName && this.gameState && this.gameState.current_team) {
                    teamName = this.gameState[`team${this.gameState.current_team}`].name;
                }
                
                if (isCorrect) {
                    if (isStealAttempt) {
                        message = `🎯 ${teamName} STOLEN! +${pointsEarned} ${pointsEarned === 1 ? 'point' : 'points'}!`;
                    } else {
                        message = `✅ ${teamName} Correct! +${pointsEarned} ${pointsEarned === 1 ? 'point' : 'points'}!`;
                    }
                    color = 'green';
                } else {
                    if (isStealAttempt) {
                        message = `❌ ${teamName} Steal attempt failed! The answer was: ${correctAnswer}`;
                    } else {
                        message = `❌ ${teamName} Incorrect! The answer was: ${correctAnswer}`;
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
                    font-size: 18px;
                    padding: 20px 30px;
                    border-radius: 20px;
                    text-align: center;
                    max-width: 90vw;
                    min-width: 300px;
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
                
                // Show question removal notification (smaller, mobile-friendly)
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
                }, 4000);
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

            showStealNotification() {
                const message = '🎯 STEAL OPPORTUNITY! Try to answer this question correctly!';
                
                // Create temporary notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-purple-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

                                     isCurrentPlayerTurn() {
                try {
                    // For lobby games, we need to determine which player this is
                    const urlParams = new URLSearchParams(window.location.search);
                    const lobbyCode = urlParams.get('lobby');
                    
                    if (lobbyCode) {
                        // This is a lobby game - use server-assigned player ID
                        const playerId = sessionStorage.getItem('playerId');
                        
                        if (playerId) {
                            // Host can now participate in the game - no observer restrictions
                            
                            // Check if there's a current question being answered
                            if (this.gameState?.current_question) {
                                // If there's a current question, only the player who selected it can interact
                                const isMyTurn = this.gameState?.current_player_id === playerId;
                                console.log(`isCurrentPlayerTurn - Question in progress - Player ID: ${playerId}, Current player ID: ${this.gameState?.current_player_id}, Is my turn: ${isMyTurn}`);
                                return isMyTurn;
                            } else {
                                // No current question - check if it's this player's turn to select a question
                                const isMyTurn = this.gameState?.current_player_id === playerId;
                                console.log(`isCurrentPlayerTurn - No question in progress - Player ID: ${playerId}, Current player ID: ${this.gameState?.current_player_id}, Is my turn: ${isMyTurn}`);
                                return isMyTurn;
                            }
                        } else {
                            console.log('isCurrentPlayerTurn - No player ID assigned yet, allowing interaction');
                            return true; // Allow interaction until ID is assigned
                        }
                    } else {
                        // This is a single player game - always allow interaction
                        console.log('isCurrentPlayerTurn - Single player game, allowing interaction');
                        return true;
                    }
                } catch (error) {
                    console.error('Error in isCurrentPlayerTurn:', error);
                    return false; // Default to false on error
                }
            }



            showNotYourTurnNotification(teamName) {
                const message = `⏳ It's ${teamName}'s turn! Please wait for your turn.`;
                
                // Create temporary notification with enhanced styling
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-yellow-600 text-white px-4 sm:px-6 py-2 sm:py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.style.cssText = `
                    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                    border: 2px solid #fbbf24;
                    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
                    font-weight: 600;
                    font-size: 12px;
                    max-width: 300px;
                    text-align: center;
                `;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Add a subtle pulse animation
                notification.style.animation = 'pulse 2s infinite';
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'fadeOut 0.5s ease-out';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 500);
                }, 3000);
            }

            showQuestionInProgressNotification() {
                const message = `⏳ A question is currently being answered. Please wait...`;
                
                // Create temporary notification with enhanced styling
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.style.cssText = `
                    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                    border: 2px solid #60a5fa;
                    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
                    font-weight: 600;
                    font-size: 12px;
                    max-width: 300px;
                    text-align: center;
                `;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Add a subtle pulse animation
                notification.style.animation = 'pulse 2s infinite';
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'fadeOut 0.5s ease-out';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 500);
                }, 3000);
            }

            showQuestionForOtherPlayers(question, currentTeam) {
                console.log('=== SHOW QUESTION FOR OTHER PLAYERS START ===');
                console.log('showQuestionForOtherPlayers called with:', { question, currentTeam });
                
                try {
                    const questionTextElement = document.getElementById('questionText');
                    const answerInputElement = document.getElementById('answerInput');
                    const questionModalElement = document.getElementById('questionModal');
                    const submitBtn = document.getElementById('submitAnswerBtn');
                    
                    // Validate that all required elements exist
                    if (!questionTextElement || !answerInputElement || !questionModalElement || !submitBtn) {
                        console.error('Required modal elements not found');
                        this.showErrorNotification('Error: Question modal not found');
                        return;
                    }
                    
                    // Show which question was selected (category and points) but not the actual question
                    const currentTeamName = this.gameState[`team${currentTeam}`].name;
                    questionTextElement.textContent = `${currentTeamName} selected: ${question.category} - ${question.value} points`;
                    
                    // Hide the answer input for other players
                    answerInputElement.style.display = 'none';
                    
                    // Change the submit button to a close button
                    submitBtn.textContent = 'Close';
                    submitBtn.onclick = () => {
                        this.hideQuestionModal();
                    };
                    
                    // Don't set the answering flag for other players
                    this.isAnsweringQuestion = false;
                    
                    console.log('Showing question modal for other players');
                    questionModalElement.classList.remove('hidden');
                    console.log('Question modal shown for other players');
                    
                    console.log('=== SHOW QUESTION FOR OTHER PLAYERS SUCCESS ===');
                } catch (error) {
                    console.error('=== SHOW QUESTION FOR OTHER PLAYERS ERROR ===');
                    console.error('Error in showQuestionForOtherPlayers:', error);
                    console.error('Error stack:', error.stack);
                    this.showErrorNotification('Error displaying question information. Please try again.');
                }
            }

            showYourTurnNotification() {
                const currentTeam = this.gameState[`team${this.gameState.current_team}`];
                const message = `🎯 It's your turn, ${currentTeam.name}! Click a question to answer.`;
                
                // Create temporary notification
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-green-600 text-white px-4 sm:px-6 py-2 sm:py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            showErrorNotification(message) {
                // Create temporary notification
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-red-600 text-white px-4 sm:px-6 py-2 sm:py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 5 seconds
                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }

            showSuccessNotification(title, message) {
                // Create temporary notification
                const notification = document.createElement('div');
                notification.className = 'mobile-notification bg-green-600 text-white px-4 sm:px-6 py-2 sm:py-4 rounded-lg shadow-lg z-50 bounce-in';
                notification.style.cssText = `
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    border: 2px solid #34d399;
                    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
                    font-weight: 600;
                    font-size: 12px;
                    max-width: 300px;
                    text-align: center;
                `;
                notification.innerHTML = `
                    <div class="font-bold mb-1">${title}</div>
                    <div class="text-sm">${message}</div>
                `;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            logCurrentState() {
                const playerTeam = sessionStorage.getItem('playerTeam');
                const currentTeam = this.gameState.current_team;
                const hasCurrentQuestion = this.gameState.current_question;
                const isMyTurn = playerTeam ? parseInt(playerTeam) === currentTeam : false;
                
                console.log('=== CURRENT STATE LOG ===');
                console.log('Player Team:', playerTeam);
                console.log('Current Team:', currentTeam);
                console.log('Has Current Question:', hasCurrentQuestion);
                console.log('Is My Turn:', isMyTurn);
                console.log('Game State:', this.gameState);
                console.log('=== END STATE LOG ===');
            }

            // Debug method that can be called from browser console
            debugTurn() {
                const playerTeam = sessionStorage.getItem('playerTeam');
                const currentTeam = this.gameState.current_team;
                const hasCurrentQuestion = this.gameState.current_question;
                const isMyTurn = playerTeam ? parseInt(playerTeam) === currentTeam : false;
                
                console.log('=== TURN DEBUG ===');
                console.log('Player Team:', playerTeam, 'Type:', typeof playerTeam);
                console.log('Current Team:', currentTeam, 'Type:', typeof currentTeam);
                console.log('Player Team (parsed):', playerTeam ? parseInt(playerTeam) : 'null');
                console.log('Has Current Question:', hasCurrentQuestion);
                console.log('Is My Turn:', isMyTurn);
                console.log('Comparison:', playerTeam ? parseInt(playerTeam) === currentTeam : false);
                console.log('=== END TURN DEBUG ===');
                
                return {
                    playerTeam: playerTeam,
                    currentTeam: currentTeam,
                    hasCurrentQuestion: hasCurrentQuestion,
                    isMyTurn: isMyTurn
                };
            }

            // Host observer mode removed - host can now participate in the game
            
            validateGameState() {
                // Ensure all teams exist in the game state
                if (this.gameState && this.gameState.team_count) {
                    for (let i = 1; i <= this.gameState.team_count; i++) {
                        if (!this.gameState[`team${i}`]) {
                            console.warn(`Team ${i} missing from game state, creating default team`);
                            this.gameState[`team${i}`] = {
                                name: `Team ${i}`,
                                score: 0,
                                timer: this.gameState.custom_game_timer || 300
                            };
                        }
                    }
                }
                
                // If team 1 is missing and we have host_player_id, create a default "Host" team
                if (this.gameState && this.gameState.host_player_id && !this.gameState.team1) {
                    console.warn('Team 1 missing but host exists, creating Host team');
                    this.gameState.team1 = {
                        name: 'Host',
                        score: 0,
                        timer: this.gameState.custom_game_timer || 300,
                        is_host: true
                    };
                }
                
                // Ensure current_team is valid
                if (this.gameState && this.gameState.current_team) {
                    if (this.gameState.current_team > this.gameState.team_count || this.gameState.current_team < 1) {
                        console.warn(`Current team ${this.gameState.current_team} is invalid (team count: ${this.gameState.team_count}), resetting to 1`);
                        this.gameState.current_team = 1;
                    }
                } else if (this.gameState) {
                    console.warn('Missing current_team, setting to 1');
                    this.gameState.current_team = 1;
                }
                
                // Ensure current_player_id exists and is valid
                if (this.gameState && !this.gameState.current_player_id && this.gameState.player_ids && this.gameState.player_ids.length > 0) {
                    console.warn('Missing current_player_id, setting to first player');
                    this.gameState.current_player_id = this.gameState.player_ids[0];
                }
                
                console.log('Game state validation complete:', {
                    teamCount: this.gameState?.team_count,
                    currentTeam: this.gameState?.current_team,
                    currentPlayerId: this.gameState?.current_player_id,
                    teams: this.gameState ? Object.keys(this.gameState).filter(key => key.startsWith('team')).map(key => ({
                        key: key,
                        data: this.gameState[key]
                    })) : []
                });
            }

            validateRequiredElements() {
                const requiredElements = [
                    'questionText',
                    'answerInput', 
                    'questionModal',
                    'submitAnswerBtn',
                    'timerDisplay',
                    'timerProgress',
                    'teamsContainer',
                    'gameBoard'
                ];
                
                const missingElements = [];
                
                requiredElements.forEach(elementId => {
                    const element = document.getElementById(elementId);
                    if (!element) {
                        missingElements.push(elementId);
                    }
                });
                
                if (missingElements.length > 0) {
                    console.error('Missing required DOM elements:', missingElements);
                    return false;
                }
                
                return true;
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
            
            // Debug function to check turn status
            debugTurn() {
                console.log('=== Turn Debug ===');
                console.log('Current team:', this.gameState.current_team);
                console.log('Current player ID:', this.gameState.current_player_id);
                console.log('Is my turn:', this.isCurrentPlayerTurn());
                console.log('Team data:', this.gameState[`team${this.gameState.current_team}`]);
                console.log('All teams:');
                for (let i = 1; i <= this.gameState.team_count; i++) {
                    const team = this.gameState[`team${i}`];
                    console.log(`  Team ${i}:`, team);
                }
                console.log('================');
                return {
                    currentTeam: this.gameState.current_team,
                    currentPlayerId: this.gameState.current_player_id,
                    isMyTurn: this.isCurrentPlayerTurn(),
                    teamData: this.gameState[`team${this.gameState.current_team}`]
                };
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
                    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
                    font-weight: 600;
                    font-size: 12px;
                    max-width: 300px;
                    text-align: center;
                `;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'fadeOut 0.5s ease-out';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 500);
                }, 3000);
            }
            
            // Force refresh game state from server
            async forceRefreshGameState() {
                console.log('Force refreshing game state from server...');
                try {
                    const response = await fetch('/jeopardy/get-game-state', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.game_state) {
                        console.log('Received fresh game state from server');
                        this.updateLocalGameState(data.game_state);
                    } else {
                        console.error('Failed to get fresh game state:', data.message);
                    }
                } catch (error) {
                    console.error('Error force refreshing game state:', error);
                }
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
            
            // MOBILE-FRIENDLY HELPER FUNCTIONS
            initializeGameForLobby() {
                console.log('Initializing game for lobby...');
                
                // Optimize: Validate DOM elements and initialize game efficiently
                if (!this.validateRequiredElements()) {
                    console.error('Required DOM elements missing, cannot initialize game');
                    this.showErrorNotification('Game interface not properly loaded. Redirecting back to lobby...');
                    setTimeout(() => {
                        window.location.href = `/jeopardy/lobby/${this.lobbyCode}`;
                    }, 2000);
                    return;
                }
                
                // Optimize: Initialize game components in parallel
                this.generateTeamCards();
                this.createGameBoard();
                this.updateDisplay();
                
                // Optimize: Initialize timer display
                const customTimer = this.gameState.custom_question_timer || 30;
                this.initializeTimer(customTimer);
                
                // Optimize: Start real-time synchronization with delay to avoid conflicts
                setTimeout(() => {
                    this.startRealTimeSync();
                }, 500);
            }
            
            showMobileFriendlyError(message, retryCallback) {
                console.log('Showing mobile-friendly error:', message);
                
                // Create a mobile-friendly error notification
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75';
                errorDiv.innerHTML = `
                    <div class="bg-gray-800 rounded-lg p-6 mx-4 max-w-sm w-full text-center">
                        <div class="text-red-400 text-4xl mb-4">⚠️</div>
                        <h3 class="text-white font-bold text-lg mb-2">Connection Issue</h3>
                        <p class="text-gray-300 text-sm mb-4">${message}</p>
                        <div class="flex space-x-2">
                            <button onclick="this.parentElement.parentElement.parentElement.remove(); window.location.href='/jeopardy/lobby/${this.lobbyCode}'" 
                                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Back to Lobby
                            </button>
                            <button onclick="this.parentElement.parentElement.parentElement.remove(); ${retryCallback ? 'setTimeout(() => { window.customJeopardyGame.loadGameState(); }, 1000);' : ''}" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Retry
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(errorDiv);
                
                // Auto-remove after 10 seconds
                setTimeout(() => {
                    if (errorDiv.parentElement) {
                        errorDiv.remove();
                    }
                }, 10000);
            }
        }

        // Global functions for navigation
        function goToMainMenu() {
            window.location.href = '/jeopardy';
        }

        function createNewCustomGame() {
            window.location.href = '/jeopardy/custom-game';
        }



        // Initialize the game when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile-specific optimizations
            if (window.innerWidth <= 768) {
                console.log('Mobile device detected, applying mobile optimizations');
                
                // Prevent zoom on input focus for iOS
                const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.style.fontSize = '16px';
                    });
                });
                
                // Add touch event listeners for better mobile interaction
                const buttons = document.querySelectorAll('.touch-button, button');
                buttons.forEach(button => {
                    button.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    });
                    
                    button.addEventListener('touchend', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
                
                // Prevent horizontal scroll on mobile
                document.body.style.overflowX = 'hidden';
            }
            
            // Add global error handler to prevent unexpected exits
            window.addEventListener('error', function(event) {
                console.error('Global error caught:', event.error);
                console.error('Error details:', {
                    message: event.message,
                    filename: event.filename,
                    lineno: event.lineno,
                    colno: event.colno
                });
                
                // Show error notification instead of crashing
                if (window.customJeopardyGame && window.customJeopardyGame.showErrorNotification) {
                    window.customJeopardyGame.showErrorNotification('An unexpected error occurred. Please refresh the page.');
                }
                
                // Prevent the error from causing the page to exit
                event.preventDefault();
            });
            
            // Add unhandled promise rejection handler
            window.addEventListener('unhandledrejection', function(event) {
                console.error('Unhandled promise rejection:', event.reason);
                
                // Show error notification instead of crashing
                if (window.customJeopardyGame && window.customJeopardyGame.showErrorNotification) {
                    window.customJeopardyGame.showErrorNotification('A network error occurred. Please check your connection.');
                }
                
                // Prevent the rejection from causing the page to exit
                event.preventDefault();
            });
            
            window.customJeopardyGame = new CustomJeopardyGame();
            
            // Make debug methods available globally
            window.debugTurn = () => {
                if (window.customJeopardyGame) {
                    return window.customJeopardyGame.debugTurn();
                } else {
                    console.log('Game not initialized yet');
                    return null;
                }
            };
            
            // Update responsive classes on window resize
            window.addEventListener('resize', () => {
                if (window.customJeopardyGame && window.customJeopardyGame.categories && window.customJeopardyGame.categories.length > 0) {
                    window.customJeopardyGame.applyResponsiveGameBoardClasses();
                }
            });
            
            // Handle orientation changes for mobile
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    if (window.innerWidth <= 768) {
                        document.body.style.overflowX = 'hidden';
                    }
                }, 500);
            });
        });
    </script>
</body>
</html>
