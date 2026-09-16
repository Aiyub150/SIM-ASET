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

        return view('dashboard', compact(
            'totalItems', 
            'totalStock', 
            'activeLoans', 
            'overdueLoans',
            'recentMovements',
            'recentLoans',
            'lowStockItems'
        ));
    }
}
