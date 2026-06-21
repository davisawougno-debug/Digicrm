<?php

require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Devis.php';
require_once MODELS_PATH . '/Invoice.php';
require_once MODELS_PATH . '/Prospect.php';
require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/Project.php';
require_once MODELS_PATH . '/User.php';
require_once HELPERS_PATH . '/Session.php';

class SearchController
{
    public static function search(): void
    {
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            Session::setFlash('error', 'Veuillez entrer au moins 2 caractères.');
            header('Location: ' . BASE_URL . '/admin_index.php?module=dashboard');
            exit;
        }

        $query = '%' . $q . '%';

        $clients = Client::search($query);
        $prospects = Prospect::search($query);
        $devis = Devis::search($query);
        $factures = Invoice::search($query);
        $contracts = Contract::search($query);
        $projects = Project::search($query);
        $users = User::search($query);

        $resultsCount = count($clients) + count($prospects) + count($devis)
                      + count($factures) + count($contracts) + count($projects)
                      + count($users);

        $GLOBALS['viewData'] = compact(
            'q', 'clients', 'prospects', 'devis', 'factures',
            'contracts', 'projects', 'users', 'resultsCount'
        );
        $pageTitle = 'Recherche : ' . esc($q);
        require __DIR__ . '/admin_search.php';
        exit;
    }
}
