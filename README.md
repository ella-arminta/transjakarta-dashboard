# transjakarta-dashboard

# pembagian tugas
Felina -> Perusahaan regular
Jere -> Perusahaan premium
ella -> Pelanggan

# catatan untuk presentasi
catatan pelanggan : 
- karena analisis ini akan ada pada apliaksi tiap pelanggan, maka mencari berdasarkan index nama pelanggan atau paycard id pelanggan akan mempercepat query, hal ini bagus ketika menggunakan mongoDB.
Tujuan menggunakan MONGODB : 
- datasetnya besar,dan akan terus menambah datanya,  menggunakan mongoDB yang lebih hemat penyimpanan dikarenakan scaling secara horizontal.
- Dalam data besar juga menggunakan MONGODB no sql document database, akan lebih cepat menquery kan karena menggunakan indeks untuk mengquery. sedangkan mysql menggunakan.
contoh penggunaan indeks pada mysql berdasarkan contoh yg diberikan sebelumny
Tentu, berikut adalah contoh penggunaan indeks pada MySQL berdasarkan contoh sebelumnya, yaitu mencari entri dalam tabel transaction di mana nilai kolom username sama dengan 'john_doe'.
Membuat Indeks pada Kolom username:
sql
Copy code
CREATE INDEX idx_username ON transaction (username);
Perintah ini akan membuat indeks pada kolom username dalam tabel transaction. Indeks ini akan mempercepat pencarian data berdasarkan nilai username.
Melakukan Query dengan Indeks:
sql
Copy code
SELECT * FROM transaction WHERE username = 'john_doe';
Setelah indeks dibuat, query di atas akan menggunakan indeks untuk mencari nilai 'john_doe' dalam kolom username. MySQL akan secara efisien memproses query ini dengan menggunakan indeks, yang akan mengurangi jumlah baris yang harus diperiksa dalam tabel.
Dengan menggunakan indeks, pencarian data akan menjadi lebih cepat karena MySQL dapat langsung mencari nilai yang cocok dalam indeks, tanpa harus memeriksa setiap baris dalam tabel. Ini memungkinkan kinerja query yang lebih baik, terutama pada tabel dengan volume data yang besar.