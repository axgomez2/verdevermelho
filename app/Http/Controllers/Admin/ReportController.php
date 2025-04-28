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
        // Abordagem simplificada para testar se a view está funcionando
        return view('admin.reports.teste');
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
