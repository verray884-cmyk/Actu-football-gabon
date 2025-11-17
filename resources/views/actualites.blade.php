<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des Actualités - Football Gabonais</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --gabon-green: #009E60;
            --gabon-yellow: #FCD116;
            --gabon-blue: #3A75C4;
            --dark-green: #006B3F;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eff5 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--gabon-green) 0%, var(--dark-green) 100%);
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 2rem 1.5rem;
            border-bottom: 2px solid rgba(252, 209, 22, 0.3);
            text-align: center;
        }

        .sidebar-brand img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid var(--gabon-yellow);
            padding: 5px;
            background: white;
            margin-bottom: 10px;
        }

        .sidebar-brand h3 {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
        }

        .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 0.95rem 1.2rem;
            border-radius: 0.75rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            font-weight: 500;
            margin: 0.25rem 0.75rem;
            text-decoration: none;
        }

        .nav-link:hover {
            background: rgba(252, 209, 22, 0.2);
            color: white;
            transform: translateX(8px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--gabon-yellow) 0%, #f0c000 100%);
            color: var(--dark-green);
            font-weight: 600;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 30px;
        }

        .top-navbar {
            background: white;
            border-bottom: 4px solid var(--gabon-yellow);
            padding: 1.5rem 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .page-title {
            color: var(--gabon-green);
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border-left: 5px solid var(--gabon-green);
            transition: transform 0.3s;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card.yellow-border {
            border-left-color: var(--gabon-yellow);
        }

        .stats-card.blue-border {
            border-left-color: var(--gabon-blue);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stats-icon.green {
            background: rgba(0,158,96,0.1);
            color: var(--gabon-green);
        }

        .stats-icon.yellow {
            background: rgba(252,209,22,0.1);
            color: #e6bd00;
        }

        .stats-icon.blue {
            background: rgba(58,117,196,0.1);
            color: var(--gabon-blue);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .stats-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--gabon-green) 0%, var(--dark-green) 100%);
            color: white;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .card-body-custom {
            padding: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--gabon-green);
            box-shadow: 0 0 0 0.2rem rgba(0,158,96,0.15);
        }

        .btn-gabon {
            background: linear-gradient(135deg, var(--gabon-green) 0%, var(--dark-green) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-gabon:hover {
            background: linear-gradient(135deg, var(--dark-green) 0%, #005a3a 100%);
            color: white;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }

        .table-custom {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-custom thead th {
            background: var(--gabon-green);
            color: white;
            font-weight: 600;
            padding: 15px;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table-custom thead th:first-child {
            border-radius: 10px 0 0 10px;
        }

        .table-custom thead th:last-child {
            border-radius: 0 10px 10px 0;
        }

        .table-custom tbody tr {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .table-custom tbody tr:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: scale(1.01);
        }

        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
            border: none;
        }

        .table-custom tbody tr td:first-child {
            border-radius: 10px 0 0 10px;
        }

        .table-custom tbody tr td:last-child {
            border-radius: 0 10px 10px 0;
        }

        .preview-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid var(--gabon-green);
        }

        .media-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            border: 3px solid var(--gabon-green);
            margin-top: 10px;
        }

        .badge-publie {
            background: var(--gabon-green);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-brouillon {
            background: var(--gabon-yellow);
            color: var(--dark-green);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-categorie {
            background: var(--gabon-blue);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 500;
            font-size: 0.8rem;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-action.edit {
            background: rgba(252,209,22,0.15);
            color: #e6bd00;
        }

        .btn-action.edit:hover {
            background: var(--gabon-yellow);
            color: var(--dark-green);
        }

        .btn-action.delete {
            background: rgba(220,53,69,0.15);
            color: #dc3545;
        }

        .btn-action.delete:hover {
            background: #dc3545;
            color: white;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 64px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .upload-progress {
            margin-top: 10px;
        }

        .progress {
            height: 25px;
            border-radius: 10px;
        }

        .progress-bar {
            background: linear-gradient(135deg, var(--gabon-green) 0%, var(--dark-green) 100%);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Flag_of_Gabon.svg/200px-Flag_of_Gabon.svg.png" alt="Gabon">
            <h3><i class="fas fa-futbol"></i> Football Gabon</h3>
            <small class="text-muted">Administration Sportive</small>
        </div>

        <ul class="nav flex-column mt-4">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-chart-line"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('equipes.store') }}">
                    <i class="fas fa-shield-alt"></i> Équipes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('actualites.store') }}">
                    <i class="fas fa-newspaper"></i> Actualités
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('interview.store') }}">
                    <i class="fas fa-microphone"></i> Interviews
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('matchs.store') }}">
                    <i class="fas fa-medal"></i> Résultats-Matchs
                </a>
            </li>
            <li class="nav-item mt-3">
                <form action="{{ route('deconnexion') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="nav-link" style="border: none; background: transparent; width: 100%; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <h1 class="page-title">
                <i class="fas fa-newspaper me-2"></i>Gestion des Actualités
            </h1>
        </div>

        <!-- Notifications -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Erreurs :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div id="alertContainer"></div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon green">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stats-number">{{ $totalArticles ?? 0 }}</div>
                    <div class="stats-label">Articles Total</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card yellow-border">
                    <div class="stats-icon yellow">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number">{{ $publishedArticles ?? 0 }}</div>
                    <div class="stats-label">Publiés</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card blue-border">
                    <div class="stats-icon blue">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number">{{ $draftArticles ?? 0 }}</div>
                    <div class="stats-label">Brouillons</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon green">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stats-number">{{ $totalCategories ?? 0 }}</div>
                    <div class="stats-label">Catégories</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Formulaire -->
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="content-card">
                    <div class="card-header-custom">
                        <i class="fas fa-plus-circle me-2"></i><span id="formTitle">Nouvel Article</span>
                    </div>
                    <div class="card-body-custom">
                        <form id="actualiteForm" enctype="multipart/form-data">
                            <input type="hidden" id="actualiteId">
                            <input type="hidden" id="currentMedia">

                            <div class="mb-3">
                                <label class="form-label">Administrateur *</label>
                                <select class="form-select" name="admin_id" id="admin_id" required>
                                    <option value="">Choisir...</option>
                                    @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Titre *</label>
                                <input type="text" class="form-control" name="titre" id="titre" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contenu *</label>
                                <textarea class="form-control" name="contenu" id="contenu" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Média (Image/Vidéo)</label>
                                <input type="file" class="form-control" name="media" id="media" accept="image/*,video/*">
                                <small class="text-muted">JPG, PNG, GIF, MP4, MOV, AVI, WEBM (Max 50Mo)</small>
                                <div id="mediaPreview"></div>
                                <div id="uploadProgress" class="upload-progress" style="display: none;">
                                    <div class="progress">
                                        <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catégorie *</label>
                                <select class="form-select" name="categorie" id="categorie" required>
                                    <option value="">Choisir...</option>
                                    <option value="Équipe Nationale">Équipe Nationale</option>
                                    <option value="Championnat Gabonais">Championnat Gabonais</option>
                                    <option value="Transferts">Transferts</option>
                                    <option value="Interviews">Interviews</option>
                                    <option value="CAF">Compétitions CAF</option>
                                    <option value="Formation">Formation & Jeunes</option>
                                    <option value="Actualités">Actualités Générales</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de publication *</label>
                                <input type="datetime-local" class="form-control" name="date_publication" id="date_publication" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="publie" value="1" id="publie">
                                    <label class="form-check-label" for="publie">
                                        Publier immédiatement
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-gabon flex-fill" id="submitBtn">
                                    <i class="fas fa-save me-2"></i><span id="btnText">Enregistrer</span>
                                </button>
                                <button type="button" class="btn btn-cancel" id="cancelBtn" style="display:none;" onclick="cancelEdit()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Liste -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="content-card">
                    <div class="card-header-custom">
                        <i class="fas fa-list me-2"></i>Liste des Articles (<span id="articleCount">{{ $actualites->count() }}</span>)
                    </div>
                    <div class="card-body-custom">
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>Média</th>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="actualitesTable">
                                    @forelse($actualites as $actualite)
                                    <tr data-actualite-id="{{ $actualite->id }}">
                                        <td>
                                            @if($actualite->image)
                                                @if($actualite->isVideo())
                                                    <i class="fas fa-video fa-3x text-primary"></i>
                                                @else
                                                    <img src="{{ asset('storage/' . $actualite->image) }}" alt="Image" class="preview-img">
                                                @endif
                                            @else
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ Str::limit($actualite->titre, 50) }}</strong><br>
                                            <small class="text-muted">{{ $actualite->admin->nom ?? 'Inconnu' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge-categorie">{{ $actualite->categorie }}</span>
                                        </td>
                                        <td>
                                            @if($actualite->publie)
                                                <span class="badge-publie">Publié</span>
                                            @else
                                                <span class="badge-brouillon">Brouillon</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $actualite->date_publication->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn-action edit" onclick="editActualite({{ $actualite->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-action delete" onclick="deleteActualite({{ $actualite->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h5>Aucun article</h5>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);

        // Preview media
        document.getElementById('media').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('mediaPreview');

            if (file) {
                const reader = new FileReader();
                const isVideo = file.type.startsWith('video/');

                reader.onload = function(e) {
                    if (isVideo) {
                        preview.innerHTML = `<video src="${e.target.result}" class="media-preview" controls></video>`;
                    } else {
                        preview.innerHTML = `<img src="${e.target.result}" class="media-preview">`;
                    }
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });

        // Submit form avec support upload progressif
        document.getElementById('actualiteForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const actualiteId = document.getElementById('actualiteId').value;
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const progressDiv = document.getElementById('uploadProgress');
            const progressBar = document.getElementById('progressBar');

            let url = actualiteId ? `/actualites/${actualiteId}` : '/actualites';
            if (actualiteId) {
                formData.append('_method', 'PUT');
            }

            // Désactiver le bouton
            submitBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi...';

            // Afficher la barre de progression si fichier volumineux
            const fileInput = document.getElementById('media');
            if (fileInput.files[0] && fileInput.files[0].size > 5000000) {
                progressDiv.style.display = 'block';
            }

            try {
                const xhr = new XMLHttpRequest();

                // Progress listener
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressBar.textContent = Math.round(percentComplete) + '%';
                    }
                });

                xhr.open('POST', url);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.onload = function() {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Enregistrer';
                    progressDiv.style.display = 'none';
                    progressBar.style.width = '0%';

                    if (xhr.status >= 200 && xhr.status < 300) {
                        const result = JSON.parse(xhr.responseText);
                        showAlert('success', actualiteId ? 'Article modifié !' : 'Article créé !');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        try {
                            const error = JSON.parse(xhr.responseText);
                            const errorMsg = error.errors ? Object.values(error.errors).flat().join('<br>') : error.message;
                            showAlert('danger', errorMsg || 'Erreur lors de l\'enregistrement');
                        } catch {
                            showAlert('danger', 'Erreur serveur');
                        }
                    }
                };

                xhr.onerror = function() {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Enregistrer';
                    progressDiv.style.display = 'none';
                    showAlert('danger', 'Erreur de connexion');
                };

                xhr.send(formData);

            } catch (error) {
                submitBtn.disabled = false;
                btnText.textContent = 'Enregistrer';
                progressDiv.style.display = 'none';
                console.error('Erreur:', error);
                showAlert('danger', 'Erreur inattendue');
            }
        });

        // Edit actualite - VERSION CORRIGÉE
        async function editActualite(id) {
            try {
                const response = await fetch(`/actualites/${id}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    throw new Error('Erreur lors du chargement');
                }

                const result = await response.json();

                if (result.success && result.data) {
                    const data = result.data;

                    // Remplir le formulaire
                    document.getElementById('actualiteId').value = data.id;
                    document.getElementById('admin_id').value = data.admin_id;
                    document.getElementById('titre').value = data.titre;
                    document.getElementById('contenu').value = data.contenu;
                    document.getElementById('categorie').value = data.categorie;
                    document.getElementById('date_publication').value = data.date_publication;
                    document.getElementById('publie').checked = data.publie;

                    // Afficher aperçu média existant
                    const preview = document.getElementById('mediaPreview');
                    if (data.image) {
                        document.getElementById('currentMedia').value = data.image;
                        if (data.is_video) {
                            preview.innerHTML = `
                                <div class="mt-2">
                                    <small class="text-muted">Vidéo actuelle :</small>
                                    <video src="${data.image}" class="media-preview" controls></video>
                                </div>`;
                        } else {
                            preview.innerHTML = `
                                <div class="mt-2">
                                    <small class="text-muted">Image actuelle :</small>
                                    <img src="${data.image}" class="media-preview">
                                </div>`;
                        }
                    }

                    // Modifier l'interface
                    document.getElementById('formTitle').textContent = 'Modifier l\'Article';
                    document.getElementById('btnText').textContent = 'Mettre à jour';
                    document.getElementById('cancelBtn').style.display = 'inline-block';

                    // Scroll vers le formulaire
                    document.querySelector('.content-card').scrollIntoView({ behavior: 'smooth' });

                    showAlert('info', 'Article chargé pour modification');
                } else {
                    throw new Error('Données invalides');
                }

            } catch (error) {
                console.error('Erreur:', error);
                showAlert('danger', 'Erreur lors du chargement de l\'article');
            }
        }

        // Cancel edit
        function cancelEdit() {
            document.getElementById('actualiteForm').reset();
            document.getElementById('actualiteId').value = '';
            document.getElementById('currentMedia').value = '';
            document.getElementById('mediaPreview').innerHTML = '';
            document.getElementById('formTitle').textContent = 'Nouvel Article';
            document.getElementById('btnText').textContent = 'Enregistrer';
            document.getElementById('cancelBtn').style.display = 'none';
            document.getElementById('date_publication').value = new Date().toISOString().slice(0, 16);
        }

        // Delete actualite
        async function deleteActualite(id) {
            if (!confirm('Confirmer la suppression de cet article ?')) {
                return;
            }

            try {
                const response = await fetch(`/actualites/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success || response.ok) {
                    showAlert('success', 'Article supprimé !');

                    // Animation de suppression
                    const row = document.querySelector(`[data-actualite-id="${id}"]`);
                    if (row) {
                        row.style.transition = 'all 0.3s';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';

                        setTimeout(() => {
                            row.remove();

                            // Vérifier s'il reste des articles
                            const remaining = document.querySelectorAll('#actualitesTable tr[data-actualite-id]');
                            if (remaining.length === 0) {
                                document.getElementById('actualitesTable').innerHTML = `
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h5>Aucun article</h5>
                                            </div>
                                        </td>
                                    </tr>`;
                            }

                            // Mettre à jour compteur
                            const count = parseInt(document.getElementById('articleCount').textContent) - 1;
                            document.getElementById('articleCount').textContent = Math.max(0, count);
                        }, 300);
                    }
                } else {
                    showAlert('danger', result.message || 'Erreur lors de la suppression');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showAlert('danger', 'Erreur de connexion');
            }
        }

        // Show alert
        function showAlert(type, message) {
            const container = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : (type === 'info' ? 'alert-info' : 'alert-danger');
            const icon = type === 'success' ? 'fa-check-circle' : (type === 'info' ? 'fa-info-circle' : 'fa-exclamation-circle');

            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show`;
            alert.innerHTML = `
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            container.appendChild(alert);

            setTimeout(() => {
                new bootstrap.Alert(alert).close();
            }, 5000);
        }
    </script>
</body>
</html>
