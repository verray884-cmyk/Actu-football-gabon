<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - Football Gabonais</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --gabon-green: #009E60;
            --gabon-yellow: #FCD116;
            --gabon-blue: #3A75C4;
            --dark-green: #006B3F;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gabon-green) 0%, var(--dark-green) 50%, var(--gabon-blue) 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(252,209,22,0.1) 0%, transparent 50%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .football-bg {
            position: absolute;
            font-size: 40px;
            color: rgba(255,255,255,0.05);
            animation: float 20s infinite ease-in-out;
        }

        .football-bg:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .football-bg:nth-child(2) {
            top: 70%;
            left: 80%;
            animation-delay: 5s;
        }

        .football-bg:nth-child(3) {
            top: 40%;
            left: 85%;
            animation-delay: 10s;
        }

        .football-bg:nth-child(4) {
            top: 80%;
            left: 15%;
            animation-delay: 15s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        .login-container {
            position: relative;
            z-index: 10;
            max-width: 450px;
            width: 100%;
        }

        .login-card {
            background: white;
            border-radius: 25px;
            padding: 45px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-flag {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid var(--gabon-yellow);
            padding: 8px;
            background: white;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,158,96,0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .logo-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gabon-green);
            margin-bottom: 5px;
        }

        .logo-subtitle {
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--gabon-green);
            box-shadow: 0 0 0 0.2rem rgba(0,158,96,0.15);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 1.1rem;
            z-index: 10;
        }

        .form-control.with-icon {
            padding-left: 50px;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            cursor: pointer;
            font-size: 1.1rem;
            z-index: 10;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--gabon-green);
        }

        .form-check-label {
            color: #5a6c7d;
            font-size: 0.9rem;
        }

        .form-check-input:checked {
            background-color: var(--gabon-green);
            border-color: var(--gabon-green);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--gabon-green) 0%, var(--dark-green) 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
        }

        .btn-login:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--dark-green) 0%, #005a3a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,158,96,0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            color: #7f8c8d;
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            color: #5a6c7d;
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--gabon-green);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: var(--dark-green);
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(0,158,96,0.1);
            color: var(--dark-green);
            border-left: 4px solid var(--gabon-green);
        }

        .alert-danger {
            background: rgba(220,53,69,0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .forgot-password {
            text-align: right;
        }

        .forgot-password a {
            color: var(--gabon-blue);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: var(--gabon-green);
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 30px 25px;
            }

            .logo-title {
                font-size: 1.5rem;
            }

            .form-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Ballons de football flottants -->
    <i class="fas fa-futbol football-bg"></i>
    <i class="fas fa-futbol football-bg"></i>
    <i class="fas fa-futbol football-bg"></i>
    <i class="fas fa-futbol football-bg"></i>

    <div class="login-container">
        <div class="login-card">
            <!-- Logo et titre -->
            <div class="logo-section">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Flag_of_Gabon.svg/200px-Flag_of_Gabon.svg.png"
                     alt="Drapeau du Gabon"
                     class="logo-flag">
                <h1 class="logo-title">
                    <i class="fas fa-futbol"></i> Football Gabon
                </h1>
                <p class="logo-subtitle">Administration Sportive</p>
            </div>

            <!-- Titre du formulaire -->
            <h2 class="form-title">Connexion</h2>

            <!-- Messages d'alerte Laravel -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Container pour les alertes dynamiques -->
            <div id="alertContainer"></div>

            <!-- Formulaire de connexion -->
            <form id="loginForm">
                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope me-2"></i>Adresse email
                    </label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="email"
                               class="form-control with-icon"
                               id="email"
                               name="email"
                               placeholder="votre@email.com"
                               required
                               autocomplete="email">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Mot de passe
                    </label>
                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password"
                               class="form-control with-icon"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required
                               autocomplete="current-password">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>

                <!-- Se souvenir & Mot de passe oublié -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="remember"
                               name="remember">
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Mot de passe oublié?</a>
                    </div>
                </div>

                <!-- Bouton de connexion -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    <span id="btnText">Se connecter</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>OU</span>
            </div>

            <!-- Lien d'inscription -->
            <div class="register-link">
                Vous n'avez pas de compte?
                <a href="{{ route('inscription') }}">Créer un compte</a>
            </div>
        </div>

        <!-- Copyright -->
        <div class="text-center mt-4">
            <p style="color: rgba(255,255,255,0.8); font-size: 0.85rem;">
                © 2025 Football Gabonais - Tous droits réservés
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configuration CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btnText = document.getElementById('btnText');
            const loginBtn = document.getElementById('loginBtn');
            const originalText = btnText.innerHTML;

            // Show loading
            loginBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connexion...';

            try {
                const response = await fetch('{{ route("login.post") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);

                    // Redirect after 1 second
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    loginBtn.disabled = false;
                    btnText.innerHTML = originalText;

                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat().join('<br>');
                        showAlert('danger', errorMessages);
                    } else {
                        showAlert('danger', result.message || 'Erreur de connexion');
                    }
                }
            } catch (error) {
                loginBtn.disabled = false;
                btnText.innerHTML = originalText;
                console.error('Erreur:', error);
                showAlert('danger', 'Erreur de communication avec le serveur');
            }
        });

        // Show alert function
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show`;
            alert.role = 'alert';
            alert.innerHTML = `
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            alertContainer.appendChild(alert);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }

        // Enter key navigation
        document.getElementById('email').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>
