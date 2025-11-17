<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - Football Gabonais</title>
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

        .football-bg:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .football-bg:nth-child(2) { top: 70%; left: 80%; animation-delay: 5s; }
        .football-bg:nth-child(3) { top: 40%; left: 85%; animation-delay: 10s; }
        .football-bg:nth-child(4) { top: 80%; left: 15%; animation-delay: 15s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        .register-container {
            position: relative;
            z-index: 10;
            max-width: 500px;
            width: 100%;
        }

        .register-card {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-flag {
            width: 70px;
            height: 70px;
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
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gabon-green);
            margin-bottom: 5px;
        }

        .logo-subtitle {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .form-title {
            font-size: 1.4rem;
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

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
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

        .btn-register {
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

        .btn-register:hover {
            background: linear-gradient(135deg, var(--dark-green) 0%, #005a3a 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,158,96,0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 20px 0;
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

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #5a6c7d;
            font-size: 0.9rem;
        }

        .login-link a {
            color: var(--gabon-green);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .login-link a:hover {
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

        .password-strength {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #dc3545;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: var(--gabon-yellow);
        }

        .password-strength-bar.strong {
            width: 100%;
            background: var(--gabon-green);
        }

        .password-strength-text {
            font-size: 0.75rem;
            margin-top: 4px;
            color: #7f8c8d;
        }

        @media (max-width: 576px) {
            .register-card { padding: 30px 25px; }
            .logo-title { font-size: 1.4rem; }
            .form-title { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <i class="fas fa-futbol football-bg"></i>
    <i class="fas fa-futbol football-bg"></i>
    <i class="fas fa-futbol football-bg"></i>
    <i class="fas fa-futbol football-bg"></i>

    <div class="register-container">
        <div class="register-card">
            <div class="logo-section">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Flag_of_Gabon.svg/200px-Flag_of_Gabon.svg.png"
                     alt="Drapeau du Gabon"
                     class="logo-flag">
                <h1 class="logo-title">
                    <i class="fas fa-futbol"></i> Football Gabon
                </h1>
                <p class="logo-subtitle">Administration Sportive</p>
            </div>

            <h2 class="form-title">Créer un compte</h2>

            <div id="alertContainer"></div>

            <form id="registerForm" method="POST" action="{{ route('inscription') }}">
                @csrf

                <!-- Nom complet -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-user me-2"></i>Nom complet
                    </label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text"
                               class="form-control with-icon @error('nom') is-invalid @enderror"
                               id="nom"
                               name="nom"
                               value="{{ old('nom') }}"
                               placeholder="Ex: Jean Dupont"
                               required>
                    </div>
                    @error('nom')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope me-2"></i>Adresse email
                    </label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email"
                               class="form-control with-icon @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="votre@email.com"
                               required
                               autocomplete="email">
                    </div>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Rôle -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-user-tag me-2"></i>Rôle
                    </label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                        <option value="editeur" {{ old('role') == 'editeur' ? 'selected' : '' }}>Éditeur</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Administrateur</option>
                    </select>
                    <small class="text-muted">Par défaut: Éditeur</small>
                    @error('role')
                        <small class="text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Mot de passe
                    </label>
                    <div class="input-group">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password"
                               class="form-control with-icon @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="••••••••"
                               required
                               autocomplete="new-password">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="strengthText"></div>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>



                <!-- Bouton d'inscription -->
                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="fas fa-user-plus me-2"></i>
                    <span id="btnText">Créer mon compte</span>
                </button>
            </form>

            <div class="divider">
                <span>OU</span>
            </div>

            <div class="login-link">
                Vous avez déjà un compte?
                <a href="{{ route('login') }}">Se connecter</a>
            </div>
        </div>

        <div class="text-center mt-4">
            <p style="color: rgba(255,255,255,0.8); font-size: 0.85rem;">
                © 2025 Football Gabonais - Tous droits réservés
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirmInput = document.getElementById('password_confirmation');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            if (password.length === 0) {
                strengthBar.className = 'password-strength-bar';
                strengthText.textContent = '';
                return;
            }

            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';

            if (strength <= 2) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Faible';
                strengthText.style.color = '#dc3545';
            } else if (strength <= 3) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Moyen';
                strengthText.style.color = '#e6bd00';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Fort';
                strengthText.style.color = 'var(--gabon-green)';
            }
        });

        // Handle form submission with AJAX
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Vérifier que les mots de passe correspondent
            if (passwordInput.value !== passwordConfirmInput.value) {
                showAlert('danger', 'Les mots de passe ne correspondent pas');
                return;
            }

            const formData = new FormData(this);
            const btnText = document.getElementById('btnText');
            const registerBtn = document.getElementById('registerBtn');
            const originalText = btnText.innerHTML;

            registerBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Création...';

            try {
                const response = await fetch('{{ route("inscription") }}', {
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

                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    registerBtn.disabled = false;
                    btnText.innerHTML = originalText;

                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat().join('<br>');
                        showAlert('danger', errorMessages);
                    } else {
                        showAlert('danger', result.message || 'Erreur lors de l\'inscription');
                    }
                }
            } catch (error) {
                registerBtn.disabled = false;
                btnText.innerHTML = originalText;
                console.error('Erreur:', error);
                showAlert('danger', 'Erreur de communication avec le serveur');
            }
        });

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

            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    </script>
</body>
</html>
