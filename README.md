
WePOS - Cafe v.3.42.22 (Free Version)
Updated: 07-01-2021 01:00:00

Cocok untuk:
Cafe/Resto/Rumah Makan (semua penjualan berbasis Cafe)

Terima Kasih sudah Download dan Support WePOS.id, 
WePOS sudah dibuat sejak tahun 2015, update bertahap sesuai kebutuhan Cafe & Resto
untuk versi Retail/Toko: https://github.com/copolatoz/wepos-retail

*untuk auto update: silahkan daftarkan merchant/cafe/resto untuk mendapatkan merchant-key

#Cara Instalasi Aplikasi:
1. Install XAMPP 5.5.24 atau 5.6.32
2. copy-paste folder hasil download aplikasi 'wepos-free' ke xampp/htdocs/

	#copy dan rename file:
	1. copy index.php.org --> index.php
	2. copy .htaccess.org --> .htaccess (edit isi file, sesuaikan dengan nama folder)

	#folder /applications/config 
	1. copy app_config.php.org --> app_config.php
	2. copy config.php.org --> config.php
	3. copy database.php.org --> database.php (edit isi file, sesuaikan dengan nama database)

	#import database: db/database_wepos_free.sql
	1. akses ke http://localhost/phpmyadmin
	2. buat database baru misal: wepos-free
	3. import db/database_wepos_free.sql


3. run di browser sesuai nama folder yang digunakan, default: http://localhost/wepos-free

	#Mengganti URL menjadi http://localhost/nama-cafe-anda
	1. ganti nama folder download 'wepos-free' menjadi 'nama-cafe-anda'
	2. ubah isi/text pada file .htaccess 'wepos-free' menjadi 'nama-cafe-anda'
		*jika .htaccess tidak ditemukan, ubah settingan folder anda agar dapat melihat hidden file dan ekstensi file
		*gunakan editor semisal notepad++ untuk save-as atau membuat/edit file .htaccess
	
	#integrasi dengan WePOS.Cashier - Android:
	1. rename folder utama (default: 'wepos-free') menjadi 'wepos'
	2. ubah isi/text pada file .htaccess (default: 'wepos-free') menjadi 'wepos'


4. untuk setup printer Thermal -> silahkan download extension PHP di website (login wepos.id)

*Untuk Instalasi lengkap bisa baca di dokumentasi (login website, wepos.id)
#Silahkan Donasi untuk versi gratisan yang lebih baik ^^ 
#terima kasih untuk rekan-rekan yang sudah support WePOS

Team WePOS ^^
contact@wepos.id 
081222549676

*SELALU DUKUNG KARYA ANAK BANGSA!! 
*JANGAN MALU UNTUK BERTANYA - GRATIS KONSULTASI





