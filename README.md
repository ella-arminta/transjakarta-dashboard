# transjakarta-dashboard

# pembagian tugas
Felina -> Perusahaan regular
Jere -> Perusahaan premium
ella -> Pelanggan

# Notesdr ella
Database mongo db Format:
Transaction
<!-- {
  "_id": {
    "$oid": "6671c109b103dc230c0a78e4"
  },
  "transID": "ATBB895U7P81BP",
  "payCardID": {
    "$numberLong": "4368520028973555"
  },
  "corridorID": "JAK.84",
  "corridorName": "Terminal Kampung Melayu - Kapin Raya",
  "direction": 1,
  "tapInStops": "B01844P",
  "tapInStopsName": "Kav Marinir Kalimalang",
  "tapInStopsLat": -6.247975,
  "tapInStopsLon": 106.92659,
  "stopStartSeq": 12,
  "tapInTime": "2022-06-03 06:11:03",
  "tapOutStops": "B00140P",
  "tapOutStopsName": "Billy Moon",
  "tapOutStopsLat": -6.248271,
  "tapOutStopsLon": 106.93093,
  "stopEndSeq": 14,
  "tapOutTime": "2022-06-03 06:56:23",
  "payAmount": 0,
  "year": 2022,
  "month": 6
} -->
PayCard
<!-- {
  "_id": {
    "$oid": "665bd3206da5c2a44739a9a6"
  },
  "payCardID": {
    "$numberLong": "60403114535"
  },
  "payCardBank": "flazz",
  "payCardName": "R. Alika Budiyanto",
  "payCardSex": "F",
  "payCardBirthDate": 1990,
  "age": 34
} -->

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