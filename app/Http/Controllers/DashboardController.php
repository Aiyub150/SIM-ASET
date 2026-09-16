<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Loan;
use App\Models\StockMovement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isStaff = $user->hasRole('Staff Logistik') || $user->hasRole('Staff');

        $loanQuery = Loan::query();
        if ($isStaff) {
            $loanQuery->where('user_id', $user->id);
        }

        $totalItems = Item::count();
        $totalStock = Item::sum('total_qty');
        $activeLoans = (clone $loanQuery)->where('status', 'active')->count();
        $overdueLoans = (clone $loanQuery)->where('status', 'active')->where('due_date', '<', now())->count();
        
        $recentMovements = $isStaff ? collect() : StockMovement::with(['item', 'user'])->latest()->take(5)->get();
        $recentLoans = (clone $loanQuery)->with(['borrower', 'user'])->latest()->take(5)->get();
        $lowStockItems = Item::where('available_qty', '<', 5)->take(5)->get(); // Example for low stock

        // Chart Data (6 bulan terakhir)
        $chartLabels = [];
        $chartLoans = [];
        $chartReturns = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M Y');
            
            $chartLoans[] = (clone $loanQuery)->whereYear('borrow_date', $month->year)
                ->whereMonth('borrow_date', $month->month)
                ->count();
                
            $chartReturns[] = (clone $loanQuery)->whereYear('return_date', $month->year)
                ->whereMonth('return_date', $month->month)
                ->whereIn('status', ['completed', 'returned_partial'])
                ->count();
        }

        return view('dashboard', compact(
            'totalItems', 
            'totalStock', 
            'activeLoans', 
            'overdueLoans',
            'recentMovements',
            'recentLoans',
            'lowStockItems',
            'chartLabels',
            'chartLoans',
            'chartReturns'
        ));
    }
}
