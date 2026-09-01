<?php
namespace App\Services;

use App\Models\Loan;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Exception;

class LoanService
{
    /**
     * Proses pembuatan transaksi peminjaman baru
     * 
     * @param array $data Data dari request yang sudah divalidasi
     * @param int $userId ID admin yang sedang login
     * @return Loan
     * @throws Exception
     */
    public function createLoan(array $data, int $userId): Loan
    {
        // DB::transaction akan membatalkan SEMUA perubahan database jika terjadi 1 saja error (Rollback)
        return DB::transaction(function () use ($data, $userId) {
            
            // 1. Generate Kode Peminjaman Unik (Format: PJM-YYYYMMDD-XXXX)
            $datePrefix = now()->format('Ymd');
            $todayLoansCount = Loan::whereDate('created_at', now()->toDateString())->count();
            $loanCode = 'PJM-' . $datePrefix . '-' . str_pad($todayLoansCount + 1, 4, '0', STR_PAD_LEFT);

            // 2. Insert Data ke Tabel loans (Header)
            $loan = Loan::create([
                'loan_code'   => $loanCode,
                'borrower_id' => $data['borrower_id'],
                'user_id'     => $userId,
                'borrow_date' => $data['borrow_date'],
                'due_date'    => $data['due_date'],
                'status'      => 'active', // Langsung aktif saat dipinjam
                'notes'       => $data['notes'] ?? null,
            ]);

            // 3. Proses setiap barang yang dipinjam
            foreach ($data['items'] as $itemData) {
                
                // KRITIS: lockForUpdate() Mencegah Race Condition!
                $item = Item::where('id', $itemData['item_id'])->lockForUpdate()->first();

                if (!$item) {
                    throw new Exception("Barang dengan ID {$itemData['item_id']} tidak ditemukan.");
                }

                // 4. Validasi Stok Ekstra Ketat
                if ($item->available_qty < $itemData['qty']) {
                    throw new Exception("Stok tidak mencukupi untuk barang: {$item->name}. Tersedia: {$item->available_qty}, Diminta: {$itemData['qty']}");
                }

                // 5. Kurangi stok TERSEDIA saja. Total fisik (total_qty) tidak disentuh.
                $item->available_qty -= $itemData['qty'];
                $item->save();

                // 6. Insert Data ke Tabel loan_items (Detail)
                $loan->loanItems()->create([
                    'item_id'      => $item->id,
                    'qty'          => $itemData['qty'],
                    'returned_qty' => 0, // Baru dipinjam, belum ada yang dikembalikan
                ]);
            }

            return $loan;
        });
    }
    
    /**
     * Proses pengembalian barang (mendukung pengembalian parsial)
     * 
     * @param int $loanId ID Transaksi peminjaman
     * @param array $data Data barang yang dikembalikan
     * @param int $userId ID admin yang memproses
     * @return Loan
     * @throws Exception
     */
    public function processReturn(int $loanId, array $data, int $userId): Loan
    {
        return DB::transaction(function () use ($loanId, $data, $userId) {
            
            // 1. Kunci Transaksi Header (Mencegah admin lain memproses ID yang sama bersamaan)
            $loan = Loan::where('id', $loanId)->lockForUpdate()->first();

            if (!$loan) {
                throw new Exception("Transaksi peminjaman tidak ditemukan.");
            }

            if ($loan->status === 'completed') {
                throw new Exception("Transaksi ini sudah selesai sepenuhnya. Tidak bisa mengembalikan barang lagi.");
            }

            // 2. Proses pengembalian per barang
            foreach ($data['items'] as $returnData) {
                
                // KRITIS: Kita mengunci tabel pivot (Detail Peminjaman)
                $loanItem = $loan->loanItems()
                                 ->where('id', $returnData['loan_item_id'])
                                 ->lockForUpdate()
                                 ->first();
                
                if (!$loanItem) {
                    throw new Exception("Item peminjaman dengan ID {$returnData['loan_item_id']} tidak valid untuk transaksi ini.");
                }

                // Kalkulasi ketat sisa hutang
                $sisaHutang = $loanItem->qty - $loanItem->returned_qty;

                if ($returnData['return_qty'] <= 0) {
                    throw new Exception("Jumlah pengembalian harus lebih besar dari 0.");
                }

                if ($returnData['return_qty'] > $sisaHutang) {
                    throw new Exception("Manipulasi terdeteksi! Jumlah pengembalian ({$returnData['return_qty']}) melebihi sisa hutang ({$sisaHutang}) untuk barang ID: {$loanItem->item_id}.");
                }

                // 3. Update Detail Peminjaman (Tambah barang yang dikembalikan)
                $loanItem->returned_qty += $returnData['return_qty'];
                $loanItem->save();

                // 4. Kembalikan stok ke Gudang Utama (Master Item)
                $item = Item::where('id', $loanItem->item_id)->lockForUpdate()->first();
                $item->available_qty += $returnData['return_qty'];
                $item->save();
            }

            // 5. Verifikasi Status Transaksi Otomatis
            // Cek apakah masih ada barang di keranjang ini yang belum kembali 100%
            $hasPendingItems = $loan->loanItems()->whereColumn('returned_qty', '<', 'qty')->exists();

            if (!$hasPendingItems) {
                // Jika semua barang sudah kembali, tutup transaksi
                $loan->status = 'completed';
                $loan->return_date = now(); // Catat tanggal pengembalian lunas
                $loan->save();
            }

            return $loan->refresh();
        });
    }
}