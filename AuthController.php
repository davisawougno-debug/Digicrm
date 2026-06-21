<?php
/**
 * AuthController - Gestion de l'authentification
 *
 * Traite les actions de connexion et de déconnexion.
 */

require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Activity.php';

class AuthController
{
    /**
     * Traite la connexion de l'utilisateur
     *
     * @return array Tableau avec 'success' (bool) et 'message' (string)
     */
    public static function login(): array
    {
        $response = [
            'success' => false,
            'message' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response['message'] = 'Méthode non autorisée.';
            return $response;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        Validation::reset();
        Validation::required('email', 'Email', $email);
        Validation::required('password', 'Mot de passe', $password);

        if (!empty($email)) {
            Validation::email('email', $email);
        }

        if (Validation::hasErrors()) {
            $response['message'] = 'Veuillez corriger les erreurs ci-dessous.';
            $_SESSION['validation_errors'] = Validation::errors();
            return $response;
        }

        $user = User::findByEmail($email);

        if (!$user) {
            $response['message'] = 'Aucun compte trouvé avec cette adresse email.';
            return $response;
        }

        if ($user['statut'] === 'inactif') {
            Activity::log($user['id'], 'connexion_refusee', 'Tentative de connexion sur un compte désactivé.');
            $response['message'] = 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.';
            return $response;
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            $response['message'] = 'Mot de passe incorrect.';
            return $response;
        }

        if (Security::needsRehash($user['password'])) {
            User::updatePassword($user['id'], Security::hashPassword($password));
        }

        Session::regenerate();

        Session::set('user_id', $user['id']);
        Session::set('user_nom', $user['nom']);
        Session::set('user_prenom', $user['prenom']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);
        Session::set('last_activity', time());

        User::updateLastLogin($user['id']);

        Activity::log($user['id'], 'connexion', 'Connexion réussie');

        $response['success'] = true;
        $response['message'] = 'Connexion réussie !';

        return $response;
    }

    /**
     * Traite la déconnexion
     */
    public static function logout(): void
    {
        Session::start();

        $userId = Session::get('user_id');
        if ($userId) {
            Activity::log($userId, 'deconnexion', 'Déconnexion utilisateur');
        }

        Session::destroy();
        Session::setFlash('success', 'Vous avez été déconnecté avec succès.');
    }
}
