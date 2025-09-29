## Inventory Management

Aplikasi Inventory Management ini dikembangkan untuk mengatasi inefisiensi dalam pengelolaan dan pengambilan barang di tempat penelitian, khususnya Dinas Tanaman Pangan dan Hortikultura Provinsi Jawa Barat (DISTANHORTI).
Latar belakang utama pembuatan sistem ini adalah untuk menghilangkan proses manual yang mengharuskan pengecekan fisik di gudang setiap kali ada kebutuhan pengambilan barang.

## Cara Instalasi Project

Pastikan git cli sudah terinstall, kemudian jalankan perintah dibawah
```
1. clone repository
2. copy .env.example rename menjadi .env kemudian atur database di .env
3. composer install
4. php artisan key:generate
5. php artisan migrate --seed
```

## Akun Super Admin
```
email : superadmin@example.com
password : password
```

## Akun Admin
```
email : admin@example.com
password : password
```

## Fitur Aplikasi 
- Terdapat 2 role yaitu : super admin dan admin
- Mengelola Kategori (Super Admin & Admin)
- Mengelola Supplier (Super Admin & Admin)
- Mengelola Barang (Super Admin & Admin)
- Mengelola User (Super Admin & Admin)
- Mengelola Transaksi (Super Admin & Admin)
- Mengelola Roles & Permission (Super Admin)
- Halaman Dashboard dengan berbagai fitur : (Super Admin & Admin) 
- List Transaksi (Super Admin & Admin)
- Search Data
- Responsive

