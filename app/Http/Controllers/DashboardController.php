<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Loan;
use App\Models\StockMovement;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isStaff = $user->hasRole('Staff Logistik') || $user->hasRole('Staff');
        $isAdmin = $user->hasRole('Admin');
        $isSuperAdmin = $user->hasRole('Super Admin');

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

        // Chart Data Filter (hari, bulan, tahun)
        $period = $request->query('period', 'bulan'); // default 'bulan'
        $chartLabels = [];
        $chartLoans = [];
        $chartReturns = [];

        if ($period === 'hari') {
            for ($i = 6; $i >= 0; $i--) {
                $day = now()->subDays($i);
                $chartLabels[] = $day->translatedFormat('d M');
                
                $chartLoans[] = (clone $loanQuery)->whereDate('borrow_date', $day->format('Y-m-d'))->count();
                $chartReturns[] = (clone $loanQuery)->whereDate('return_date', $day->format('Y-m-d'))
                    ->whereIn('status', ['completed', 'returned_partial'])
                    ->count();
            }
        } elseif ($period === 'tahun') {
            for ($i = 4; $i >= 0; $i--) {
                $year = now()->subYears($i);
                $chartLabels[] = $year->format('Y');
                
                $chartLoans[] = (clone $loanQuery)->whereYear('borrow_date', $year->year)->count();
                $chartReturns[] = (clone $loanQuery)->whereYear('return_date', $year->year)
                    ->whereIn('status', ['completed', 'returned_partial'])
                    ->count();
            }
        } else {
            // default: bulan
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
        }

        if ($request->ajax()) {
            return response()->json([
                'labels' => $chartLabels,
                'loans' => $chartLoans,
                'returns' => $chartReturns
            ]);
        }

        $holidays = app(\App\Services\CalendarService::class)->getHolidaysForCurrentMonth();

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
            'chartReturns',
            'period',
            'isStaff',
            'isAdmin',
            'isSuperAdmin',
            'holidays'
        ));
    }
}
