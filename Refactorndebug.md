Fitur maupun permasalahan yang perlu diperbaiki:
- setelah input barang baru di Master barang muncul notif " Barang <strong>Meja Lipat 120cmX60cmX40CM</strong> berhasil ditambahkan dengan SKU <code>FURN-002</code>. " seharusnya tag html tidak dimunculkan di notif success or failed
- setelah menambahkan data instansi baru menampilkan halaman error yang berisi Symfony\Component\ErrorHandler\Error\FatalError
app\Http\Requests\StoreBorrowerRequest.php:10
Cannot declare class App\Http\Controllers\BorrowerController, because the name is already in use
- Filter periode di fitur Laporan Rekaptulasi Bulanan sebaiknya dibuat option calender sehingga user dapat memilih bulan maupun tahun dengan lebih mudah dengan tampilan kalender dan opsi tahun yang sudah mencakup masa depan atau beberapa tahun kedepan
- 