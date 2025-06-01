### About
Aplikasi point of sale berbasis website yang dibangun menggunakan tech stack JS - PHP - MySQL.

Aplikasi ini dapat membantu dalam melakukan pencatatan setiap transaksi toko serta mengelola item atau inventaris toko. 

### Languages and Tools
- JavaScript
- PHP
- MySQL
- Bootstrap 5
- Font Awesome 5
- DataTables
- JQuery
- NPM
- Git

### Features
Pada aplikasi ini terdapat dua jenis role akun, yang masing - masing role memiliki hak yang berbeda :
#### Khusus Owner
- Delete transaction list
- Delete item list

#### Owner dan Staff
- Login
- Logout
- Create new transaction
- Create new item
- Update item
- See all transaction lists
- See all items list

### How To Start
- Clone this repository with this command <code>git clone https://github.com/eexzzm/point-of-sale.git</code>
- Then, use this command <code>cd point-of-sale</code>
- Install all dependencies <code>npm install</code>
- Create new database with the name <code>DB_Pos</code> and then import tables with using file <code>DB.sql</code>
- If you have different config for the connection, you can edit the connection with using file <code>src/server/connection.php</code>
- Turn on your web server application / XAMPP / MAMPP / LAMPP
- Open the application with your favorite browser