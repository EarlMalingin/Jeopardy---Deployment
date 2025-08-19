<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Jeopardy Game

A modern, web-based Jeopardy game built with Laravel and JavaScript, featuring beautiful animations, real-time scoring, and an interactive timer system.

## 🎯 Features

### Game Features
- **Two Team Competition**: Support for two teams with customizable names
- **Real-time Scoring**: Dynamic score updates with visual animations
- **Interactive Timer**: 30-second countdown timer with visual feedback
- **Timer Penalty**: Wrong answers deduct 10 seconds from the timer
- **Question Categories**: 5 categories with 5 questions each (100-500 points)
- **Visual Feedback**: Confetti animations for correct answers
- **Team Turn Management**: Automatic team switching after each question

### Design Features
- **Modern UI**: Beautiful gradient designs and smooth animations
- **Loading Screen**: Animated loading screen with spinner
- **Responsive Design**: Works on desktop and mobile devices
- **Dark Theme**: Elegant dark color scheme
- **Smooth Animations**: Fade-in, slide-in, and bounce animations
- **Visual Timer**: Circular progress indicator with color changes
- **Hover Effects**: Interactive card hover animations

### Technical Features
- **Laravel Backend**: Robust PHP backend with session management
- **AJAX Communication**: Real-time game state updates
- **CSRF Protection**: Secure form submissions
- **Session Management**: Persistent game state across requests
- **Error Handling**: Graceful error handling and user feedback

## 🚀 Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 11
- Web server (Apache/Nginx) or PHP built-in server

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Jeopardy
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Start the development server**
   ```bash
   php artisan serve
   ```

5. **Access the game**
   - Open your browser and go to `http://localhost:8000`
   - Click on "🎯 Play Jeopardy" to start the game

## 🎮 How to Play

### Game Setup
1. Enter names for both teams
2. Click "Start Game" to begin

### Gameplay
1. **Select Questions**: Click on any dollar amount on the game board
2. **Answer Questions**: Type your answer in the input field
3. **Score Points**: 
   - Correct answers: Add points to your score
   - Wrong answers: Lose points and 10 seconds from timer
4. **Timer Management**: Each question has a 30-second timer
5. **Team Turns**: Teams automatically switch after each question

### Scoring System
- **100 Points**: Easy questions
- **200 Points**: Medium questions  
- **300 Points**: Harder questions

- **500 Points**: Expert level questions

### Categories
- **Science**: Chemistry, physics, biology, and astronomy
- **History**: World history, important dates, and historical figures
- **Geography**: Countries, capitals, landmarks, and natural features
- **Entertainment**: Movies, TV shows, music, and celebrities
- **Sports**: Various sports, athletes, and sporting events

## 🎨 Design Highlights

### Animations
- **Loading Spinner**: Rotating animation during game load
- **Fade Effects**: Smooth transitions between screens
- **Score Animations**: Bounce effects when scores update
- **Confetti**: Celebratory animation for correct answers
- **Timer Ring**: Circular progress with color changes
- **Hover Effects**: Card lift and scale animations

### Color Scheme
- **Primary**: Blue gradients (#3B82F6 to #8B5CF6)
- **Secondary**: Red accents (#EF4444)
- **Success**: Green (#10B981)
- **Warning**: Yellow (#F59E0B)
- **Background**: Dark gray (#111827)

### Interactive Elements
- **Question Cards**: Hover effects and click animations
- **Timer Display**: Visual countdown with color progression
- **Team Cards**: Active team highlighting with glow effects
- **Modal Windows**: Smooth open/close animations
- **Form Inputs**: Focus states and validation feedback

## 🔧 Technical Implementation

### Backend (Laravel)
- **JeopardyController**: Handles all game logic and API endpoints
- **Session Management**: Stores game state in Laravel sessions
- **Question Database**: Built-in question bank with 25 questions
- **API Endpoints**: RESTful API for game interactions

### Frontend (JavaScript)
- **ES6 Classes**: Object-oriented game management
- **AJAX Requests**: Real-time communication with backend
- **DOM Manipulation**: Dynamic UI updates
- **Event Handling**: User interaction management
- **Animation Control**: CSS and JavaScript animations

### Key Features Implementation
- **Timer System**: JavaScript setInterval with visual feedback
- **Score Tracking**: Real-time score updates with animations
- **Question Management**: Dynamic question loading and validation
- **Team Switching**: Automatic turn management
- **Game State**: Persistent session-based game state

## 🎯 Game Rules

1. **Team Setup**: Two teams with custom names
2. **Question Selection**: Teams take turns selecting questions
3. **Answering**: 30 seconds to answer each question
4. **Scoring**: 
   - Correct: +points
   - Incorrect: -points and -10 seconds
5. **Timer**: Visual countdown with color changes
6. **Game End**: All questions must be answered

## 🚀 Future Enhancements

- **Multiple Rounds**: Double Jeopardy and Final Jeopardy
- **Sound Effects**: Audio feedback for interactions
- **Leaderboards**: Persistent high scores
- **Custom Questions**: Admin panel for question management
- **Multiplayer**: Real-time multiplayer support
- **Mobile App**: Native mobile application
- **Themes**: Multiple visual themes
- **Difficulty Levels**: Adjustable question difficulty

## 📱 Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Mobile browsers

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

**Enjoy playing Jeopardy! 🎯✨**
