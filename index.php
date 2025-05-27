<?php
// Database connection parameters
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'todolist');
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// PARTIE BACK-END - Traitement des actions
if ($_POST) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        switch($action) {
            case 'new':
                if (!empty($_POST['title'])) {
                    $stmt = $pdo->prepare("INSERT INTO todo (title) VALUES (?)");
                    $stmt->execute([trim($_POST['title'])]);
                }
                break;
                
            case 'delete':
                if (isset($_POST['id'])) {
                    $stmt = $pdo->prepare("DELETE FROM todo WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                }
                break;
                
            case 'toggle':
                if (isset($_POST['id'])) {
                    $stmt = $pdo->prepare("UPDATE todo SET done = 1 - done WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                }
                break;
        }
        
        // Redirection pour éviter la resoumission du formulaire
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Lecture de la liste des tâches (triée par date de création du plus récent au plus ancien)
$stmt = $pdo->query("SELECT * FROM todo ORDER BY created_at DESC");
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList - Gestionnaire de Tâches</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        success: '#10b981',
                        warning: '#f59e0b',
                        danger: '#ef4444'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- PARTIE FRONT-END - Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <h1 class="text-xl font-bold">TodoList</h1>
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm"><?= count($taches) ?> tâche(s)</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Formulaire d'ajout d'une nouvelle tâche -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="bg-green-500 text-white px-6 py-4 rounded-t-lg">
                <h2 class="text-lg font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Ajouter une nouvelle tâche
                </h2>
            </div>
            <div class="p-6">
                <form method="POST" class="flex gap-3">
                    <input type="text" 
                           name="title" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none" 
                           placeholder="Entrez votre tâche..." 
                           required>
                    <button type="submit" 
                            name="action" 
                            value="new" 
                            class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Ajouter
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des tâches -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="bg-blue-500 text-white px-6 py-4 rounded-t-lg">
                <h2 class="text-lg font-semibold flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mes tâches
                </h2>
            </div>
            
            <?php if (empty($taches)): ?>
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-lg">Aucune tâche pour le moment. Ajoutez-en une !</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($taches as $tache): ?>
                        <div class="p-4 <?= $tache['done'] ? 'bg-green-50 border-l-4 border-green-400' : 'bg-yellow-50 border-l-4 border-yellow-400' ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <?php if ($tache['done']): ?>
                                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"></path>
                                            </svg>
                                        <?php endif; ?>
                                        <span class="<?= $tache['done'] ? 'line-through text-gray-500' : 'text-gray-900' ?> text-lg">
                                            <?= htmlspecialchars($tache['title']) ?>
                                        </span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500 ml-8">
                                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Créée le <?= date('d/m/Y à H:i', strtotime($tache['created_at'])) ?>
                                    </div>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <!-- Formulaire pour basculer le statut -->
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $tache['id'] ?>">
                                        <button type="submit" 
                                                name="action" 
                                                value="toggle" 
                                                class="px-3 py-1 text-sm rounded-md transition-colors duration-200 <?= $tache['done'] ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' ?>"
                                                title="<?= $tache['done'] ? 'Marquer comme non terminée' : 'Marquer comme terminée' ?>">
                                            <?php if ($tache['done']): ?>
                                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                                </svg>
                                                Undo
                                            <?php else: ?>
                                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Done
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                    
                                    <!-- Formulaire pour supprimer -->
                                    <form method="POST" 
                                          class="inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                                        <input type="hidden" name="id" value="<?= $tache['id'] ?>">
                                        <button type="submit" 
                                                name="action" 
                                                value="delete" 
                                                class="px-3 py-1 text-sm bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200"
                                                title="Supprimer la tâche">
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3l1.293-1.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L8 9V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                <path d="M3 5a2 2 0 012-2h1a1 1 0 000 2H5v11a2 2 0 002 2h6a2 2 0 002-2V5h-1a1 1 0 100-2h1a2 2 0 012 2v11a4 4 0 01-4 4H7a4 4 0 01-4-4V5z"></path>
                                            </svg>
                                            X
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistiques -->
        <?php if (!empty($taches)): ?>
            <?php 
            $taches_terminees = array_filter($taches, function($t) { return $t['done']; });
            $pourcentage = round((count($taches_terminees) / count($taches)) * 100);
            ?>
            <div class="bg-white rounded-lg shadow-md mt-6 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Progression</h3>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-3">
                    <div class="bg-green-500 h-3 rounded-full transition-all duration-500 ease-out" 
                         style="width: <?= $pourcentage ?>%"></div>
                </div>
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <span><?= count($taches_terminees) ?> sur <?= count($taches) ?> tâche(s) terminée(s)</span>
                    <span class="font-semibold text-green-600"><?= $pourcentage ?>%</span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Animation pour les nouvelles tâches
        document.addEventListener('DOMContentLoaded', function() {
            const taskItems = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-yellow-50"]');
            taskItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.3s ease-out';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
