<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Http\Requests\StoreBorrowerRequest;
use App\Http\Requests\UpdateBorrowerRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BorrowerController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        if ($user && !$user->can('borrowers.view') && !$user->hasAnyRole(['Super Admin', 'Admin'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk melihat direktori instansi peminjam.');
        }

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

        ActivityLogger::log(
            'CREATE',
            'Master Data Instansi',
            "Menambahkan instansi baru: {$borrower->institution_name}",
            [
                'after' => [
                    'Nama Instansi'    => $borrower->institution_name,
                    'Penanggung Jawab' => $borrower->pic_name,
                    'Kontak'           => $borrower->contact_number,
                    'Alamat'           => $borrower->address,
                ]
            ],
            $request
        );

        return redirect()
            ->route('borrowers.index')
            ->with('success', "Instansi {$borrower->institution_name} berhasil ditambahkan ke dalam Master Data.");
    }

    public function edit(Borrower $borrower): View
    {
        return view('borrowers.edit', compact('borrower'));
    }

    public function update(UpdateBorrowerRequest $request, Borrower $borrower): RedirectResponse
    {
        $oldData = [
            'Nama Instansi'    => $borrower->institution_name,
            'Penanggung Jawab' => $borrower->pic_name,
            'Kontak'           => $borrower->contact_number,
            'Alamat'           => $borrower->address,
        ];

        $borrower->update($request->validated());

        $newData = [
            'Nama Instansi'    => $borrower->institution_name,
            'Penanggung Jawab' => $borrower->pic_name,
            'Kontak'           => $borrower->contact_number,
            'Alamat'           => $borrower->address,
        ];

        ActivityLogger::log(
            'UPDATE',
            'Master Data Instansi',
            "Memperbarui data instansi: {$borrower->institution_name}",
            [
                'before' => $oldData,
                'after'  => $newData,
            ],
            $request
        );

        return redirect()
            ->route('borrowers.index')
            ->with('success', "Data Instansi {$borrower->institution_name} berhasil diperbarui.");
    }
}