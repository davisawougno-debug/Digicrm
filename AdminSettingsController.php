<?php

require_once MODELS_PATH . '/Parametre.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Security.php';
require_once HELPERS_PATH . '/Session.php';

class SettingsController
{
    public static function parametres(string $action = 'index'): void
    {
        switch ($action) {
            case 'index':
                $params = Parametre::getAll();
                $GLOBALS['viewData'] = compact('params');
                $pageTitle = 'Paramètres';
                require __DIR__ . '/admin_parametres.php';
                exit;

            case 'save':
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    header('Location: ' . BASE_URL . '/admin_index.php?module=parametres');
                    exit;
                }
                if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                    Session::setFlash('error', 'Erreur de validation du formulaire.');
                    header('Location: ' . BASE_URL . '/admin_index.php?module=parametres');
                    exit;
                }

                foreach ($_POST as $cle => $valeur) {
                    if (in_array($cle, ['csrf_token', 'action', 'module'])) {
                        continue;
                    }
                    if (is_array($valeur)) {
                        continue;
                    }
                    Parametre::set($cle, trim((string)$valeur));
                }

                if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (in_array($_FILES['logo']['type'], $allowedTypes)) {
                        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                        $filename = 'logo.' . $ext;
                        $dest = ASSETS_PATH . '/images/' . $filename;
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
                            Parametre::set('logo', $filename);
                        }
                    }
                }

                Activity::log(Session::get('user_id'), 'parametres', 'Modification des paramètres');
                Session::setFlash('success', 'Paramètres enregistrés avec succès.');
                header('Location: ' . BASE_URL . '/admin_index.php?module=parametres');
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=parametres');
                exit;
        }
    }
}
