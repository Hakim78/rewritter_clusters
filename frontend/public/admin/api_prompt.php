<?php
/**
 * API pour gérer les templates de prompts en base de données
 * Fichier: frontend/public/admin/api_prompt.php
 */

header('Content-Type: application/json');

// Démarrer la session
if (!isset($_SESSION)) {
    session_start();
}

require_once '../../includes/functions.php';
require_once '../../config/database.php';

// Vérifier que l'utilisateur est connecté et admin (version API)
if (!isset($_SESSION['user']) || !isset($_SESSION['auth_token'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Non authentifié'
    ]);
    exit;
}

if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'error' => 'Accès refusé. Droits administrateur requis.'
    ]);
    exit;
}

// Récupérer le workflow sélectionné
$workflow = $_GET['workflow'] ?? '1';
$workflow = in_array($workflow, ['1', '2', '3']) ? intval($workflow) : 1;

$action = $_GET['action'] ?? '';

// Connexion BDD
try {
    $pdo = getDBConnection();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de connexion à la base de données'
    ]);
    exit;
}

switch ($action) {
    case 'load':
        // Charger le template actif
        try {
            $stmt = $pdo->prepare("
                SELECT pt.*, u.name as author_name
                FROM prompt_templates pt
                LEFT JOIN users u ON pt.created_by = u.id
                WHERE pt.workflow_id = ? AND pt.is_active = TRUE
                ORDER BY pt.version DESC
                LIMIT 1
            ");
            $stmt->execute([$workflow]);
            $template = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($template) {
                echo json_encode([
                    'success' => true,
                    'content' => $template['content'],
                    'version' => $template['version'],
                    'lastModified' => strtotime($template['created_at']),
                    'author' => $template['author_name'],
                    'notes' => $template['notes']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Aucun template actif trouvé pour ce workflow'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur de lecture : ' . $e->getMessage()
            ]);
        }
        break;

    case 'save':
        // Sauvegarder un nouveau template (versioning)
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $content = $input['content'] ?? '';
            $notes = $input['notes'] ?? '';

            if (empty($content)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Contenu vide'
                ]);
                exit;
            }

            // Vérifier que les variables critiques sont présentes
            $requiredVars = [
                '{DOMAIN}',
                '{KEYWORD}',
                '{GUIDELINE}',
                '{SITE_URL}',
                '{CONTENT_TONE}',
                '{TARGET_AUDIENCE}',
                '{MAIN_TOPICS}',
                '{SEO_OPPORTUNITIES}',
                '{CONTENT_GAPS}',
                '{CONTENT_STRATEGY}',
                '{KEYWORD_OPPORTUNITIES}',
                '{INTERNAL_LINKS}',
                '{EXTERNAL_REFS}',
                '{CURRENT_DATE}'
            ];

            $missingVars = [];
            foreach ($requiredVars as $var) {
                if (strpos($content, $var) === false) {
                    $missingVars[] = $var;
                }
            }

            if (!empty($missingVars)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Variables manquantes : ' . implode(', ', $missingVars)
                ]);
                exit;
            }

            $pdo->beginTransaction();

            // Désactiver tous les templates actifs pour ce workflow
            $stmt = $pdo->prepare("
                UPDATE prompt_templates
                SET is_active = FALSE
                WHERE workflow_id = ? AND is_active = TRUE
            ");
            $stmt->execute([$workflow]);

            // Obtenir le prochain numéro de version
            $stmt = $pdo->prepare("
                SELECT COALESCE(MAX(version), 0) + 1 as next_version
                FROM prompt_templates
                WHERE workflow_id = ?
            ");
            $stmt->execute([$workflow]);
            $nextVersion = $stmt->fetch(PDO::FETCH_ASSOC)['next_version'];

            // Insérer le nouveau template
            $stmt = $pdo->prepare("
                INSERT INTO prompt_templates
                (workflow_id, version, content, created_by, is_active, notes)
                VALUES (?, ?, ?, ?, TRUE, ?)
            ");
            $stmt->execute([
                $workflow,
                $nextVersion,
                $content,
                $_SESSION['user']['id'],
                $notes ?: "Version $nextVersion"
            ]);

            $templateId = $pdo->lastInsertId();

            // Logger dans l'audit trail
            $stmt = $pdo->prepare("
                INSERT INTO prompt_audit_log
                (template_id, action, user_id, ip_address, user_agent, details)
                VALUES (?, 'create', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $templateId,
                $_SESSION['user']['id'],
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                json_encode([
                    'workflow' => $workflow,
                    'version' => $nextVersion,
                    'content_length' => strlen($content)
                ])
            ]);

            $pdo->commit();

            // Récupérer la date de création
            $stmt = $pdo->prepare("SELECT created_at FROM prompt_templates WHERE id = ?");
            $stmt->execute([$templateId]);
            $created = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => 'Template enregistré avec succès',
                'version' => $nextVersion,
                'template_id' => $templateId,
                'lastModified' => strtotime($created['created_at'])
            ]);

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Erreur sauvegarde template: " . $e->getMessage() . " | " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'error' => 'Erreur de sauvegarde : ' . $e->getMessage(),
                'debug' => [
                    'workflow' => $workflow,
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
        }
        break;

    case 'versions':
        // Lister toutes les versions d'un workflow
        try {
            $stmt = $pdo->prepare("
                SELECT pt.*, u.name as author_name
                FROM prompt_templates pt
                LEFT JOIN users u ON pt.created_by = u.id
                WHERE pt.workflow_id = ?
                ORDER BY pt.version DESC
            ");
            $stmt->execute([$workflow]);
            $versions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'versions' => $versions
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur de récupération des versions : ' . $e->getMessage()
            ]);
        }
        break;

    case 'activate':
        // Activer une version spécifique (rollback)
        try {
            $versionId = $_POST['version_id'] ?? 0;

            if (!$versionId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de version manquant'
                ]);
                exit;
            }

            $pdo->beginTransaction();

            // Vérifier que la version existe et appartient au bon workflow
            $stmt = $pdo->prepare("
                SELECT id, workflow_id, version FROM prompt_templates WHERE id = ?
            ");
            $stmt->execute([$versionId]);
            $version = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$version || $version['workflow_id'] != $workflow) {
                $pdo->rollBack();
                echo json_encode([
                    'success' => false,
                    'error' => 'Version introuvable'
                ]);
                exit;
            }

            // Désactiver toutes les versions actives
            $stmt = $pdo->prepare("
                UPDATE prompt_templates
                SET is_active = FALSE
                WHERE workflow_id = ? AND is_active = TRUE
            ");
            $stmt->execute([$workflow]);

            // Activer la version sélectionnée
            $stmt = $pdo->prepare("
                UPDATE prompt_templates
                SET is_active = TRUE
                WHERE id = ?
            ");
            $stmt->execute([$versionId]);

            // Logger dans l'audit trail
            $stmt = $pdo->prepare("
                INSERT INTO prompt_audit_log
                (template_id, action, user_id, ip_address, user_agent, details)
                VALUES (?, 'activate', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $versionId,
                $_SESSION['user']['id'],
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                json_encode([
                    'workflow' => $workflow,
                    'version' => $version['version'],
                    'action' => 'rollback'
                ])
            ]);

            $pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Version activée avec succès',
                'version' => $version['version']
            ]);

        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'error' => 'Erreur d\'activation : ' . $e->getMessage()
            ]);
        }
        break;

    case 'view':
        // Voir une version spécifique
        try {
            $versionId = $_GET['version_id'] ?? 0;

            if (!$versionId) {
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de version manquant'
                ]);
                exit;
            }

            $stmt = $pdo->prepare("
                SELECT pt.*, u.name as author_name
                FROM prompt_templates pt
                LEFT JOIN users u ON pt.created_by = u.id
                WHERE pt.id = ? AND pt.workflow_id = ?
            ");
            $stmt->execute([$versionId, $workflow]);
            $template = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($template) {
                // Logger la consultation
                $stmt = $pdo->prepare("
                    INSERT INTO prompt_audit_log
                    (template_id, action, user_id, ip_address, user_agent)
                    VALUES (?, 'view', ?, ?, ?)
                ");
                $stmt->execute([
                    $versionId,
                    $_SESSION['user']['id'],
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);

                echo json_encode([
                    'success' => true,
                    'content' => $template['content'],
                    'version' => $template['version'],
                    'created_at' => $template['created_at'],
                    'author' => $template['author_name'],
                    'notes' => $template['notes'],
                    'is_active' => (bool)$template['is_active']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Version introuvable'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur de lecture : ' . $e->getMessage()
            ]);
        }
        break;

    case 'audit':
        // Récupérer l'historique d'audit
        try {
            $limit = $_GET['limit'] ?? 50;

            $stmt = $pdo->prepare("
                SELECT pal.*, pt.version, pt.workflow_id, u.name as username
                FROM prompt_audit_log pal
                JOIN prompt_templates pt ON pal.template_id = pt.id
                JOIN users u ON pal.user_id = u.id
                WHERE pt.workflow_id = ?
                ORDER BY pal.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$workflow, intval($limit)]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'logs' => $logs
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur de récupération de l\'audit : ' . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'error' => 'Action non reconnue'
        ]);
        break;
}