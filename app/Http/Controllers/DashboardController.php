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
        $isSuperAdmin = $user->hasRole('Super Admin');
        $isAdmin = $user->hasRole('Admin');
        $isStaff = $user->hasRole('Staff Logistik') || $user->hasRole('Staff');

        // Hak akses membaca data agregat / manajemen keseluruhan:
        $canViewAllData = $isSuperAdmin || $isAdmin || (rescue(fn() => $user->can('reports.view'), false) && !$isStaff);
        $canViewItems   = $canViewAllData || rescue(fn() => $user->can('items.view'), false);
        $canViewStocks  = $canViewAllData || rescue(fn() => $user->can('stocks.view'), false);

        // Jika bukan pemegang hak data agregat, batasi kueri peminjaman HANYA ke data milik user sendiri
        $loanQuery = Loan::query();
        if (!$canViewAllData) {
            $loanQuery->where('user_id', $user->id);
        }

        $totalItems   = $canViewItems ? Item::count() : 0;
        $totalStock   = $canViewItems ? Item::sum('total_qty') : 0;
        $activeLoans  = (clone $loanQuery)->where('status', 'active')->count();
        $overdueLoans = (clone $loanQuery)->where('status', 'active')->where('due_date', '<', now())->count();
        $myLoansCount = (clone $loanQuery)->count();
        $totalUsers   = $isSuperAdmin ? \App\Models\User::count() : 0;
        
        $recentMovements = $canViewStocks ? StockMovement::with(['item', 'user'])->latest()->take(5)->get() : collect();
        $recentLoans = (clone $loanQuery)->with(['borrower', 'user'])->latest()->take(5)->get();
        $lowStockItems = $canViewItems ? Item::where('available_qty', '<', 5)->take(5)->get() : collect();

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

        if ($request->ajax() && !$request->has('cal_month')) {
            return response()->json([
                'labels' => $chartLabels,
                'loans' => $chartLoans,
                'returns' => $chartReturns
            ]);
        }

        $calMonth = (int) $request->query('cal_month', now()->month);
        $calYear = (int) $request->query('cal_year', now()->year);
        $calDate = \Carbon\Carbon::createFromDate($calYear, $calMonth, 1);

        $holidays = app(\App\Services\CalendarService::class)->getHolidaysForCurrentMonth($calYear, $calMonth);

        if ($request->ajax() && $request->has('cal_month')) {
            return view('partials.calendar-widget', compact('calMonth', 'calYear', 'calDate', 'holidays'))->render();
        }

        return view('dashboard', compact(
            'totalItems', 
            'totalStock', 
            'activeLoans', 
            'overdueLoans',
            'myLoansCount',
            'totalUsers',
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
            'canViewAllData',
            'canViewItems',
            'canViewStocks',
            'holidays',
            'calMonth',
            'calYear',
            'calDate'
        ));
    }
}
