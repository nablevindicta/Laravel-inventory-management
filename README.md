## Inventory Management System
Latar belakang pembuatan aplikasi ini adalah pada tempat penelitian saya, untuk melakukan sebuah transaksi peminjaman dan juga pengambilan barang harus melakukan pengecekan barang di gudang terlebih dahulu, oleh karena itu saya mencoba untuk membangung sebuah aplikasi yang diharapkan mampu mempercepat dan mempermudah transaski dan juga pengelolaan barang.

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

## License
Aplikasi ini bersifat open source dapat digunakan oleh siapa pun dengan syarat tidak untuk di perjual belikan.
