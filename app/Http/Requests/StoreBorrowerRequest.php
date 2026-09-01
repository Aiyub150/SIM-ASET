<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Http\Requests\StoreBorrowerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BorrowerController extends Controller
{
    public function index(): View
    {
        $borrowers = Borrower::orderBy('institution_name', 'asc')->paginate(15);
        return view('borrowers.index', compact('borrowers'));
    }

    public function create(): View
    {
        return view('borrowers.create');
    }

    public function store(StoreBorrowerRequest $request): RedirectResponse
    {
        $borrower = Borrower::create($request->validated());

        return redirect()
            ->route('borrowers.index')
            ->with('success', "Instansi {$borrower->institution_name} berhasil ditambahkan ke dalam Master Data.");
    }
}