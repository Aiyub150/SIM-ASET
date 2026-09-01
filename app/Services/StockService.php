<?php
namespace App\Services;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class StockService
{
    /**
     * Catat penambahan atau pengurangan stok permanen
     * 
     * @param array $data ['item_id', 'type', 'qty', 'reference_code', 'notes']
     * @param int $userId
     * @return StockMovement
     * @throws Exception
     */
    public function adjustStock(array $data, int $userId): StockMovement
    {
        return DB::transaction(function () use ($data, $userId) {
            
            // 1. Kunci baris barang untuk mencegah Race Condition
            $item = Item::where('id', $data['item_id'])->lockForUpdate()->first();

            if (!$item) {
                throw new Exception("Barang tidak ditemukan.");
            }

            if ($data['qty'] <= 0) {
                throw new Exception("Jumlah mutasi harus lebih besar dari 0.");
            }

            // 2. Tentukan operator penambahan atau pengurangan
            $isAddition = $data['type'] === 'in';
            $balanceBefore = $item->total_qty;

            if ($isAddition) {
                $item->total_qty += $data['qty'];
                $item->available_qty += $data['qty'];
            } else {
                // Untuk kasus 'out', 'broken', 'lost'
                if ($item->available_qty < $data['qty']) {
                    throw new Exception(
                        "Stok tersedia tidak mencukupi untuk dikurangi. " .
                        "Fisik tersedia: {$item->available_qty}, Ingin dikurangi: {$data['qty']}. " .
                        "Pastikan barang tidak sedang dipinjam sebelum dihapus dari sistem."
                    );
                }
                $item->total_qty -= $data['qty'];
                $item->available_qty -= $data['qty'];
            }

            $balanceAfter = $item->total_qty;

            // 3. Simpan perubahan ke Master Item
            $item->save();

            // 4. Catat ke Buku Besar (Ledger)
            return StockMovement::create([
                'reference_code' => $data['reference_code'],
                'item_id'        => $item->id,
                'user_id'        => $userId,
                'type'           => $data['type'],
                'qty'            => $data['qty'],
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'notes'          => $data['notes'],
            ]);
        });
    }
}