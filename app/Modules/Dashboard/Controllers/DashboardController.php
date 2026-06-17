<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Mission\Models\Mission;
use App\Modules\Recommendation\Models\Recommendation;
use App\Modules\Entities\Models\Entity;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * GET /dashboard
     * Tableau de bord — KPIs globaux + graphes
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->roles->first()?->name;

        // KPIs missions (groupés par statut)
        $missionsByStatus = Mission::selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->get()
            ->keyBy('statut');

        // KPIs recommandations (groupés par statut)
        $recsByStatus = Recommendation::selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->get()
            ->keyBy('statut');

        // KPIs recommandations (groupés par priorite)
        $recsByPriority = Recommendation::selectRaw('priorite, count(*) as total')
            ->groupBy('priorite')
            ->get()
            ->keyBy('priorite');

        // Recommandations critiques en retard
        $critiquesEnRetard = Recommendation::where('priorite', 'critique')
            ->whereNotIn('statut', ['cloturee', 'non_mise_en_oeuvre'])
            ->where('delai_realisation', '<', now())
            ->count();

        // Taux de conformité global
        $totalRecs = Recommendation::count();
        $closedRecs = Recommendation::where('statut', 'cloturee')->count();
        $tauxConformite = $totalRecs > 0 ? round(($closedRecs / $totalRecs) * 100) : 0;

        // Évolution mensuelle des missions (12 derniers mois)
        $evolutionMissions = Mission::selectRaw('YEAR(created_at) as annee, MONTH(created_at) as mois, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => sprintf('%02d/%d', $item->mois, $item->annee),
                    'total' => (int) $item->total,
                ];
            });

        // Conformité par entité (top 10)
        $conformiteParEntite = Recommendation::selectRaw('
                entities.denomination as entite,
                entities.sigle as sigle,
                COUNT(recommandations.id) as total,
                SUM(CASE WHEN recommandations.statut = "cloturee" THEN 1 ELSE 0 END) as cloturees
            ')
            ->join('missions', 'missions.id', '=', 'recommandations.mission_id')
            ->join('entities', 'entities.id', '=', 'missions.entity_id')
            ->groupBy('entities.id', 'entities.denomination', 'entities.sigle')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $taux = $item->total > 0 ? round(($item->cloturees / $item->total) * 100) : 0;
                return [
                    'entite' => $item->sigle ?: $item->entite,
                    'total' => (int) $item->total,
                    'cloturees' => (int) $item->cloturees,
                    'taux_conformite' => $taux,
                ];
            });

        $data = [
            'missions' => [
                'total'      => Mission::count(),
                'en_cours'   => (int) ($missionsByStatus->get('en_cours')?->total ?? 0),
                'planifiees' => (int) ($missionsByStatus->get('planifiee')?->total ?? 0),
                'cloturees'  => (int) ($missionsByStatus->get('cloturee')?->total ?? 0),
                'suspendues' => (int) ($missionsByStatus->get('suspendue')?->total ?? 0),
            ],
            'recommandations' => [
                'total'               => $totalRecs,
                'formulees'           => (int) ($recsByStatus->get('formulee')?->total ?? 0),
                'transmises'          => (int) ($recsByStatus->get('transmise')?->total ?? 0),
                'en_cours'            => (int) ($recsByStatus->get('en_cours')?->total ?? 0),
                'mise_en_oeuvre'      => (int) ($recsByStatus->get('mise_en_oeuvre')?->total ?? 0),
                'cloturees'           => $closedRecs,
                'reportees'           => (int) ($recsByStatus->get('reportee')?->total ?? 0),
                'non_mise_en_oeuvre'  => (int) ($recsByStatus->get('non_mise_en_oeuvre')?->total ?? 0),
                'critiques_en_retard' => $critiquesEnRetard,
                'par_priorite' => [
                    'critique' => (int) ($recsByPriority->get('critique')?->total ?? 0),
                    'majeur'   => (int) ($recsByPriority->get('majeur')?->total ?? 0),
                    'mineur'   => (int) ($recsByPriority->get('mineur')?->total ?? 0),
                ],
            ],
            'taux_conformite'        => $tauxConformite,
            'entites'                => Entity::count(),
            'utilisateurs'           => User::where('statut', 'actif')->count(),
            'evolution_missions'     => $evolutionMissions,
            'conformite_par_entite'  => $conformiteParEntite,
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Tableau de bord',
            'errors'  => null
        ]);
    }
}