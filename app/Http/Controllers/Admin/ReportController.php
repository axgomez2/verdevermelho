<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VinylView;
use App\Models\VinylMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Exibe a página principal de relatórios.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Total de visualizações
        $totalViews = VinylView::count();
        
        // Total de discos únicos visualizados
        $uniqueVinyls = VinylView::distinct('vinyl_master_id')->count('vinyl_master_id');
        
        // Total de usuários únicos que visualizaram discos
        $uniqueUsers = VinylView::whereNotNull('user_id')->distinct('user_id')->count('user_id');
        
        // Visualizações nos últimos 30 dias (para o gráfico)
        $dateRange = [];
        $startDate = now()->subDays(30);
        
        // Inicializar array com zeros para cada dia
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $dateRange[$date] = 0;
        }
        
        // Obter contagem real de visualizações por dia
        $dailyViews = VinylView::select(
                DB::raw('DATE(viewed_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        
        // Mesclar os dados reais com os zeros inicializados
        foreach ($dailyViews as $date => $count) {
            if (isset($dateRange[$date])) {
                $dateRange[$date] = $count;
            }
        }
        
        // Top 10 discos mais vistos
        $topVinyls = VinylMaster::select(
                'vinyl_masters.id',
                'vinyl_masters.title',
                'vinyl_masters.slug',
                'vinyl_masters.cover_image',
                DB::raw('COUNT(vinyl_views.id) as view_count')
            )
            ->leftJoin('vinyl_views', 'vinyl_masters.id', '=', 'vinyl_views.vinyl_master_id')
            ->with('artists')
            ->groupBy('vinyl_masters.id', 'vinyl_masters.title', 'vinyl_masters.slug', 'vinyl_masters.cover_image')
            ->orderByDesc('view_count')
            ->limit(10)
            ->get();
        
        return view('admin.reports.index', compact(
            'totalViews', 
            'uniqueVinyls', 
            'uniqueUsers', 
            'dateRange', 
            'topVinyls'
        ));
    }
    
    /**
     * Exibe um relatório detalhado dos discos mais vistos.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mostViewed(Request $request)
    {
        $period = $request->input('period', 'all');
        $limit = $request->input('limit', 50);
        
        $query = VinylMaster::select(
                'vinyl_masters.id',
                'vinyl_masters.title',
                'vinyl_masters.slug',
                'vinyl_masters.cover_image',
                DB::raw('COUNT(vinyl_views.id) as view_count')
            )
            ->join('vinyl_views', 'vinyl_masters.id', '=', 'vinyl_views.vinyl_master_id')
            ->with('artists'); // Para mostrar os artistas associados
        
        // Filtrar por período
        switch ($period) {
            case 'today':
                $query->where('vinyl_views.viewed_at', '>=', now()->startOfDay());
                break;
            case 'week':
                $query->where('vinyl_views.viewed_at', '>=', now()->startOfWeek());
                break;
            case 'month':
                $query->where('vinyl_views.viewed_at', '>=', now()->startOfMonth());
                break;
            case 'year':
                $query->where('vinyl_views.viewed_at', '>=', now()->startOfYear());
                break;
        }
        
        $vinyls = $query->groupBy('vinyl_masters.id', 'vinyl_masters.title', 'vinyl_masters.slug', 'vinyl_masters.cover_image')
            ->orderByDesc('view_count')
            ->paginate($limit);
        
        return view('admin.reports.most-viewed', compact('vinyls', 'period'));
    }
    
    /**
     * Exibe as visualizações detalhadas de um disco específico.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function vinylDetails($id)
    {
        $vinyl = VinylMaster::with('artists')->findOrFail($id);
        
        // Obter visualizações por dia para este disco
        $dailyViews = VinylView::select(
                DB::raw('DATE(viewed_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('vinyl_master_id', $id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Visualizações por usuário (top 20)
        $userViews = VinylView::select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(vinyl_views.id) as view_count')
            )
            ->where('vinyl_master_id', $id)
            ->whereNotNull('user_id')
            ->join('users', 'vinyl_views.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('view_count')
            ->take(20)
            ->get();
        
        // Total de visualizações
        $totalViews = VinylView::where('vinyl_master_id', $id)->count();
        $userViewsCount = VinylView::where('vinyl_master_id', $id)->whereNotNull('user_id')->count();
        $guestViewsCount = VinylView::where('vinyl_master_id', $id)->whereNull('user_id')->count();
        
        return view('admin.reports.vinyl-details', compact(
            'vinyl',
            'dailyViews',
            'userViews',
            'totalViews',
            'userViewsCount',
            'guestViewsCount'
        ));
    }
}
