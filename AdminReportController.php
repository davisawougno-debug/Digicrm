<?php

require_once MODELS_PATH . '/Devis.php';
require_once MODELS_PATH . '/Invoice.php';
require_once MODELS_PATH . '/Payment.php';
require_once MODELS_PATH . '/Client.php';
require_once MODELS_PATH . '/Contract.php';
require_once MODELS_PATH . '/Prospect.php';
require_once MODELS_PATH . '/Activity.php';
require_once HELPERS_PATH . '/Session.php';

class ReportController
{
    public static function rapports(string $action = 'index'): void
    {
        switch ($action) {
            case 'index':
                $pdo = getPDO();

                $TotalClient = Client::countAll();
                $TotalProspect = Prospect::countAll();
                $TotalDevis = Devis::countAll();
                $TotalContrat = Contract::countAll();
                $TotalFacture = Invoice::countAll();
                $TotalPaiement = Payment::countAll();

                $stmt = $pdo->query("SELECT COALESCE(SUM(montant_ht), 0) FROM devis");
                $CaDevis = (float)$stmt->fetchColumn();
                $stmt = $pdo->query("SELECT COALESCE(SUM(montant_ht), 0) FROM invoices");
                $CaFacture = (float)$stmt->fetchColumn();
                $stmt = $pdo->query("SELECT COALESCE(SUM(montant), 0) FROM payments");
                $CaPercu = (float)$stmt->fetchColumn();

                $stmt = $pdo->query("SELECT statut, COUNT(*) as nb FROM devis GROUP BY statut");
                $devisStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                $stmt = $pdo->query("SELECT statut, COUNT(*) as nb FROM invoices GROUP BY statut");
                $factureStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

                $devisParMois = $pdo->query("
                    SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as nb
                    FROM devis GROUP BY mois ORDER BY mois DESC LIMIT 12
                ")->fetchAll(PDO::FETCH_ASSOC);

                $factureParMois = $pdo->query("
                    SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as nb
                    FROM invoices GROUP BY mois ORDER BY mois DESC LIMIT 12
                ")->fetchAll(PDO::FETCH_ASSOC);

                $paiementParMois = $pdo->query("
                    SELECT DATE_FORMAT(date_paiement, '%Y-%m') as mois, SUM(montant) as total
                    FROM payments GROUP BY mois ORDER BY mois DESC LIMIT 12
                ")->fetchAll(PDO::FETCH_ASSOC);

                $activities = Activity::getAll(50);

                $GLOBALS['viewData'] = compact(
                    'TotalClient', 'TotalProspect', 'TotalDevis', 'TotalContrat',
                    'TotalFacture', 'TotalPaiement', 'CaDevis', 'CaFacture', 'CaPercu',
                    'devisStats', 'factureStats',
                    'devisParMois', 'factureParMois', 'paiementParMois',
                    'activities'
                );
                $pageTitle = 'Rapports';
                require __DIR__ . '/admin_rapports.php';
                exit;

            case 'chiffre-affaires':
                $annee = $_GET['annee'] ?? date('Y');
                $pdo = getPDO();

                $caDevis = $pdo->prepare("
                    SELECT MONTH(created_at) as mois, COALESCE(SUM(montant_ht), 0) as total
                    FROM devis WHERE YEAR(created_at) = :annee
                    GROUP BY MONTH(created_at) ORDER BY mois
                ");
                $caDevis->execute([':annee' => $annee]);
                $caDevisData = $caDevis->fetchAll(PDO::FETCH_ASSOC);

                $caFacture = $pdo->prepare("
                    SELECT MONTH(created_at) as mois, COALESCE(SUM(montant_ht), 0) as total
                    FROM invoices WHERE YEAR(created_at) = :annee
                    GROUP BY MONTH(created_at) ORDER BY mois
                ");
                $caFacture->execute([':annee' => $annee]);
                $caFactureData = $caFacture->fetchAll(PDO::FETCH_ASSOC);

                $caPercu = $pdo->prepare("
                    SELECT MONTH(date_paiement) as mois, COALESCE(SUM(montant), 0) as total
                    FROM payments WHERE YEAR(date_paiement) = :annee
                    GROUP BY MONTH(date_paiement) ORDER BY mois
                ");
                $caPercu->execute([':annee' => $annee]);
                $caPercuData = $caPercu->fetchAll(PDO::FETCH_ASSOC);

                $GLOBALS['viewData'] = compact('annee', 'caDevisData', 'caFactureData', 'caPercuData');
                $pageTitle = "Chiffre d'affaires $annee";
                require __DIR__ . '/admin_rapports.php';
                exit;

            case 'export':
                $format = $_GET['format'] ?? 'csv';
                $type = $_GET['type'] ?? 'devis';
                $filename = "export_{$type}_" . date('Y-m-d') . ".{$format}";
                header('Content-Type: text/csv; charset=utf-8');
                header("Content-Disposition: attachment; filename=\"{$filename}\"");
                $output = fopen('php://output', 'w');

                fputs($output, "\xEF\xBB\xBF");

                switch ($type) {
                    case 'devis':
                        fputcsv($output, ['ID', 'Numéro', 'Client', 'Montant HT', 'TVA', 'Montant TTC', 'Statut', 'Date']);
                        $devisList = Devis::getAll();
                        foreach ($devisList as $d) {
                            fputcsv($output, [
                                $d['id'], $d['numero'], $d['client_name'] ?? '',
                                $d['montant_ht'], $d['tva'], $d['montant_ttc'],
                                $d['statut'], $d['created_at']
                            ]);
                        }
                        break;
                    case 'factures':
                        fputcsv($output, ['ID', 'Numéro', 'Client', 'Montant HT', 'TVA', 'Montant TTC', 'Statut', 'Date']);
                        $factureList = Invoice::getAll();
                        foreach ($factureList as $f) {
                            fputcsv($output, [
                                $f['id'], $f['numero_facture'], $f['client_name'] ?? '',
                                $f['montant_ht'], $f['tva'], $f['montant_ttc'],
                                $f['statut'], $f['created_at']
                            ]);
                        }
                        break;
                    case 'paiements':
                        fputcsv($output, ['ID', 'Facture', 'Montant', 'Mode', 'Date', 'Référence']);
                        $payments = Payment::getAll();
                        foreach ($payments as $p) {
                            fputcsv($output, [
                                $p['id'], $p['invoice_id'], $p['montant'],
                                $p['mode_paiement'], $p['date_paiement'], $p['reference']
                            ]);
                        }
                        break;
                    case 'clients':
                        fputcsv($output, ['ID', 'Nom', 'Email', 'Téléphone', 'Ville', 'Statut']);
                        $clients = Client::getAll();
                        foreach ($clients as $c) {
                            fputcsv($output, [
                                $c['id'], $c['nom'], $c['email'],
                                $c['telephone'], $c['ville'], $c['statut']
                            ]);
                        }
                        break;
                }
                fclose($output);
                exit;

            default:
                header('Location: ' . BASE_URL . '/admin_index.php?module=rapports');
                exit;
        }
    }
}
