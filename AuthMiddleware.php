<?php
/**
 * AuthMiddleware - Vérification d'authentification
 *
 * Vérifie que l'utilisateur est connecté avant d'accéder
 * aux pages protégées. Gère également le timeout de session.
 */

class AuthMiddleware
{
    /**
     * Vérifie si l'utilisateur est authentifié
     * Redirige vers la page de connexion si ce n'est pas le cas
     */
    public static function check(): void
    {
        Session::start();

        if (!Session::isAuthenticated()) {
            Session::setFlash('error', 'Veuillez vous connecter pour accéder à cette page.');
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }

        if (!Session::checkTimeout()) {
            Session::setFlash('error', 'Votre session a expiré. Veuillez vous reconnecter.');
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }

    /**
     * Vérifie que l'utilisateur est connecté et retourne ses informations
     */
    public static function getUser(): array
    {
        self::check();

        $user = User::findById(Session::get('user_id'));

        if (!$user || $user['statut'] !== 'actif') {
            Session::destroy();
            Session::setFlash('error', 'Votre compte a été désactivé.');
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }

        return $user;
    }

    /**
     * Empêche un utilisateur déjà connecté d'accéder à la page de connexion
     */
    public static function guest(): void
    {
        Session::start();

        if (Session::isAuthenticated()) {
            $dashboards = [
                'admin'       => '/admin_index.php',
                'commercial'  => '/dashboard_commercial.php',
                'chef_projet' => '/dashboard_chef.php',
                'employe'     => '/dashboard_employe.php',
            ];
            $role = Session::get('user_role');
            header('Location: ' . BASE_URL . ($dashboards[$role] ?? '/login.php'));
            exit;
        }
    }
}
