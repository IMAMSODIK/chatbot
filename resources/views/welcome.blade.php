<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeuroAI - Future Intelligence Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;600;700&family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00f7ff;
            --secondary: #6e3bff;
            --accent: #ff2d7a;
            --dark: #0a0a1a;
            --darker: #050510;
            --light: #f0f0ff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Exo 2', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            overflow-x: hidden;
            padding-top: 80px;
        }
        
        /* Fixed Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background: rgba(10, 10, 26, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 247, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 5%;
            z-index: 1000;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 8px;
            display: grid;
            place-items: center;
        }
        
        .logo-icon svg {
            width: 20px;
            height: 20px;
            fill: var(--dark);
        }
        
        .logo-text {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .login-btn {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 10px 24px;
            border-radius: 50px;
            font-family: 'Exo 2', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-btn:hover {
            background: rgba(0, 247, 255, 0.1);
            box-shadow: 0 0 15px rgba(0, 247, 255, 0.3);
        }
        
        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 247, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .login-btn:hover::before {
            left: 100%;
        }
        
        /* Hero Section */
        .hero {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            padding: 0 5%;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            max-width: 600px;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: linear-gradient(90deg, var(--light), var(--primary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-family: 'Rajdhani', sans-serif;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
            color: rgba(240, 240, 255, 0.9);
            max-width: 500px;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            margin-top: 3rem;
        }
        
        .primary-btn, .secondary-btn {
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .primary-btn {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: var(--darker);
            border: none;
            box-shadow: 0 5px 20px rgba(0, 247, 255, 0.3);
        }
        
        .primary-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 247, 255, 0.4);
        }
        
        .secondary-btn {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }
        
        .secondary-btn:hover {
            background: rgba(0, 247, 255, 0.1);
        }
        
        /* AI Visualization */
        .ai-visual {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            width: 45%;
            max-width: 700px;
            z-index: 1;
        }
        
        .circuit-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 70% 30%, rgba(110, 59, 255, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 30% 70%, rgba(0, 247, 255, 0.1) 0%, transparent 30%);
            z-index: 0;
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            background-color: var(--primary);
            border-radius: 50%;
            opacity: 0.6;
            filter: blur(1px);
        }
        
        @media (max-width: 1024px) {
            .ai-visual {
                opacity: 0.3;
                right: -10%;
            }
            
            .hero-title {
                font-size: 3rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero {
                text-align: center;
                justify-content: center;
            }
            
            .hero-content {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .ai-visual {
                display: none;
            }
            
            .cta-buttons {
                flex-direction: column;
                gap: 15px;
                width: 100%;
            }
            
            .primary-btn, .secondary-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Fixed Header -->
    <header class="header">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12,2L1,12L4,12L4,21L10,21L10,15L14,15L14,21L20,21L20,12L23,12L12,2Z"/>
            </svg>
        </div>
        <span class="logo-text">Radar</span>
        <div class="logo">
            <a href="/login" style="text-decoration: none" class="login-btn">Login</a>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="circuit-bg"></div>
        <div class="particles" id="particles-js"></div>
        
        <div class="hero-content">
            <h1 class="hero-title">Welcome to RADAR</h1>
            <p class="hero-subtitle">
                Your trusted partner for effortless, intelligent regulatory compliance. Unlock the future of governance with Radar—where advanced AI streamlines your workflow, ensures accuracy, and empowers your business to stay ahead.
            </p>
            <div class="cta-buttons">
                <button class="primary-btn" onclick="location.href = '/login'">Get Started</button>
            </div>
        </div>
        
        <!-- AI Visualization Image -->
        <div class="ai-visual">
            <svg viewBox="0 0 600 500" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="aiGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#00f7ff"/>
                        <stop offset="100%" stop-color="#6e3bff"/>
                    </linearGradient>
                    <filter id="glow" x="-30%" y="-30%" width="160%" height="160%">
                        <feGaussianBlur stdDeviation="5" result="blur"/>
                        <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                    </filter>
                </defs>
                
                <!-- Neural Network Visualization -->
                <path d="M100,100 Q300,50 500,100 Q550,200 500,300 Q300,350 100,300 Q50,200 100,100 Z" 
                      fill="none" stroke="url(#aiGradient)" stroke-width="2" stroke-dasharray="5,3" opacity="0.7"/>
                
                <!-- AI Core -->
                <circle cx="300" cy="200" r="80" fill="url(#aiGradient)" opacity="0.8" filter="url(#glow)"/>
                
                <!-- Circuit Nodes -->
                <circle cx="200" cy="150" r="8" fill="#00f7ff"/>
                <circle cx="400" cy="150" r="8" fill="#6e3bff"/>
                <circle cx="250" cy="250" r="8" fill="#00f7ff"/>
                <circle cx="350" cy="250" r="8" fill="#6e3bff"/>
                <circle cx="200" cy="300" r="8" fill="#00f7ff"/>
                <circle cx="400" cy="300" r="8" fill="#6e3bff"/>
                
                <!-- Connecting Lines -->
                <line x1="200" y1="150" x2="300" y2="200" stroke="url(#aiGradient)" stroke-width="2" opacity="0.6"/>
                <line x1="400" y1="150" x2="300" y2="200" stroke="url(#aiGradient)" stroke-width="2" opacity="0.6"/>
                <line x1="250" y1="250" x2="300" y2="200" stroke="url(#aiGradient)" stroke-width="2" opacity="0.6"/>
                <line x1="350" y1="250" x2="300" y2="200" stroke="url(#aiGradient)" stroke-width="2" opacity="0.6"/>
                <line x1="200" y1="300" x2="300" y2="200" stroke="url(#aiGradient)" stroke-width="2" opacity="0.6"/>
                <line x1="400" y1="300" x2="300" y2="200" stroke="url(#aiGradient)" stroke-width="2" opacity="0.6"/>
                
                <!-- Floating Particles -->
                <circle cx="150" cy="100" r="3" fill="#00f7ff" opacity="0.8">
                    <animate attributeName="cx" values="150;160;150" dur="5s" repeatCount="indefinite"/>
                    <animate attributeName="cy" values="100;90;100" dur="6s" repeatCount="indefinite"/>
                </circle>
                <circle cx="450" cy="350" r="4" fill="#6e3bff" opacity="0.6">
                    <animate attributeName="cx" values="450;440;450" dur="4s" repeatCount="indefinite"/>
                    <animate attributeName="cy" values="350;340;350" dur="5s" repeatCount="indefinite"/>
                </circle>
            </svg>
        </div>
    </section>
    
    <script>
        // Particle system initialization
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('particles-js');
            const particleCount = window.innerWidth < 768 ? 30 : 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random properties
                const size = Math.random() * 4 + 1;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const delay = Math.random() * 5;
                const duration = 3 + Math.random() * 7;
                const hue = 180 + Math.random() * 40;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.animationDelay = `${delay}s`;
                particle.style.animationDuration = `${duration}s`;
                particle.style.backgroundColor = `hsla(${hue}, 100%, 70%, 0.7)`;
                
                // Animation
                particle.style.animation = `float ${duration}s ease-in-out infinite`;
                
                container.appendChild(particle);
            }
            
            // Floating animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes float {
                    0%, 100% { transform: translate(0, 0); }
                    25% { transform: translate(${Math.random() * 20 - 10}px, ${Math.random() * 20 - 10}px); }
                    50% { transform: translate(${Math.random() * 20 - 10}px, ${Math.random() * 20 - 10}px); }
                    75% { transform: translate(${Math.random() * 20 - 10}px, ${Math.random() * 20 - 10}px); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>