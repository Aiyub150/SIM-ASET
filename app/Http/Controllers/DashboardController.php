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
        $totalItems = Item::count();
        $totalStock = Item::sum('total_qty');
        $activeLoans = Loan::where('status', 'active')->count();
        $overdueLoans = Loan::where('status', 'active')->where('due_date', '<', now())->count();
        
        $recentMovements = StockMovement::with(['item', 'user'])->latest()->take(5)->get();
        $recentLoans = Loan::with(['borrower', 'user'])->latest()->take(5)->get();
        $lowStockItems = Item::where('available_qty', '<', 5)->take(5)->get(); // Example for low stock

        // Chart Data (6 bulan terakhir)
        $chartLabels = [];
        $chartLoans = [];
        $chartReturns = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M Y');
            
            $chartLoans[] = Loan::whereYear('borrow_date', $month->year)
                ->whereMonth('borrow_date', $month->month)
                ->count();
                
            $chartReturns[] = Loan::whereYear('return_date', $month->year)
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
