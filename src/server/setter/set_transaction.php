<?php

// Handler for setting new transaction
// Connection
require("../connection.php"); // Pastikan path ini benar

// Pastikan hanya process_transaction yang di-set, bukan create_new_transaction lagi
if (isset($_POST["process_transaction"])) {
    // Set timezone
    date_default_timezone_set('Asia/Singapore');

    // --- 1. Get and Validate Data from POST Request ---
    // Pastikan semua data yang dibutuhkan dari frontend tersedia
    $transaction_id_txt = $_POST["transactionIdTxt"] ?? ''; // Ambil dari input ID Transaksi
    $cart_items_json    = $_POST["cartItems"] ?? '[]'; // Array item dalam bentuk JSON string
    $subtotal_before_discount = $_POST["subtotalBeforeDiscount"] ?? 0;
    $discount_percentage = $_POST["discountPercentage"] ?? 0; // Ini persentase diskon dari frontend
    $total_payment      = $_POST["totalPayment"] ?? 0; // Total akhir setelah diskon
    $cash_paid          = $_POST["cashPaid"] ?? 0;
    $money_changes      = $_POST["moneyChanges"] ?? 0;

    // Konversi string numerik menjadi float/int, dan pastikan nilainya valid
    // Hati-hati dengan format dari frontend. .replace(/\D/g,'') di JS untuk angka
    // Kalau di PHP, kita bisa pakai filter_var atau preg_replace untuk membersihkan
    $subtotal_before_discount = floatval(preg_replace('/[^0-9.]/', '', $subtotal_before_discount));
    $discount_percentage      = floatval(preg_replace('/[^0-9.]/', '', $discount_percentage));
    $total_payment            = floatval(preg_replace('/[^0-9.]/', '', $total_payment));
    $cash_paid                = floatval(preg_replace('/[^0-9.]/', '', $cash_paid));
    $money_changes            = floatval(preg_replace('/[^0-9.-]/', '', $money_changes)); // Money changes bisa negatif, jadi izinkan minus

    // Validasi dasar transaction_id_txt
    if (empty($transaction_id_txt) || !is_string($transaction_id_txt)) {
        error_log("Transaction failed: Transaction ID is required or invalid.");
        header("Location: ../../../pages/transaction?code=501&msg=Transaction ID is required.");
        exit();
    }

    // Parse JSON string of cart items
    $cart_items = json_decode($cart_items_json, true); // true for associative array
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart_items)) {
        error_log("Transaction failed: Invalid cart items data. JSON Error: " . json_last_error_msg());
        header("Location: ../../../pages/transaction?code=501&msg=Invalid cart items data.");
        exit();
    }

    if (empty($cart_items)) {
        error_log("Transaction failed: Cart is empty.");
        header("Location: ../../../pages/transaction?code=501&msg=Cart is empty.");
        exit();
    }

    // --- 2. Calculate Discount Amount in Backend (Best Practice: Re-calculate on Backend) ---
    // Ini penting untuk keamanan dan integritas data. Frontend bisa dimanipulasi.
    $actual_discount_amount = 0;
    if ($subtotal_before_discount > 0 && $discount_percentage > 0 && $discount_percentage <= 100) {
        $actual_discount_amount = ($subtotal_before_discount * $discount_percentage) / 100;
    }

    // Pastikan total_payment yang dihitung backend sesuai dengan yang dikirim frontend (toleransi floating point)
    $backend_calculated_total_payment = $subtotal_before_discount - $actual_discount_amount;
    // Anda bisa tambahkan toleransi jika perlu, misal: abs($total_payment - $backend_calculated_total_payment) > 0.01

    // --- 3. Start Transaction (for Atomicity) ---
    // Memastikan semua INSERT berhasil atau tidak sama sekali
    mysqli_begin_transaction($conn);

    try {
        // --- 4. Insert into 'sales' table (Header Transaction) ---
        $current_date = date('Y-m-d H:i:s'); // Format YYYY-MM-DD HH:MM:SS untuk DATETIME MySQL

        // DEBUG LOG ini harus muncul di php error log Anda
        error_log("DEBUG_BIND: transaction_id_txt=" . $transaction_id_txt);
        error_log("DEBUG_BIND: current_date=" . $current_date); // Ini yang paling penting
        error_log("DEBUG_BIND: backend_calculated_total_payment=" . $backend_calculated_total_payment);
        error_log("DEBUG_BIND: cash_paid=" . $cash_paid);
        error_log("DEBUG_BIND: money_changes=" . $money_changes);
        error_log("DEBUG_BIND: actual_discount_amount=" . $actual_discount_amount);
        error_log("DEBUG_BIND: Types string = ssdddd"); // Konfirmasi tipe string

        // Prepared Statement untuk tabel sales
        $sql_sales = "INSERT INTO sales (transaction_id_txt, sale_date, total_amount, cash_paid, money_changes, discount_amount)
                      VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_sales = mysqli_prepare($conn, $sql_sales);
        if (!$stmt_sales) {
            throw new Exception("Error preparing sales statement: " . mysqli_error($conn));
        }

        // Bind parameter ke prepared statement
        // s = string, d = double (float), i = integer
        // Urutan: transaction_id_txt (s), sale_date (s), total_amount (d), cash_paid (d), money_changes (d), discount_amount (d)
        mysqli_stmt_bind_param(
            $stmt_sales,
            "ssdddd", // <<<<< INI HARUS "ssdddd"
            $transaction_id_txt,
            $current_date,
            $backend_calculated_total_payment,
            $cash_paid,
            $money_changes,
            $actual_discount_amount
        );

        // Eksekusi statement
        if (!mysqli_stmt_execute($stmt_sales)) {
            throw new Exception("Error executing sales statement: " . mysqli_stmt_error($stmt_sales));
        }

        // Dapatkan sale_id yang baru saja dibuat
        $sale_id = mysqli_insert_id($conn);
        if (!$sale_id) {
            throw new Exception("Failed to get last inserted sale_id.");
        }

        mysqli_stmt_close($stmt_sales); // Tutup statement sales

        // --- 5. Insert into 'transaction_details' table (Item Details) ---
        $sql_details = "INSERT INTO transaction_details (sale_id, item_id, quantity, item_price)
                        VALUES (?, ?, ?, ?)";
        $stmt_details = mysqli_prepare($conn, $sql_details);
        if (!$stmt_details) {
            throw new Exception("Error preparing details statement: " . mysqli_error($conn));
        }

        // Loop melalui setiap item di keranjang dan simpan
        foreach ($cart_items as $item) {
            $item_id_detail   = $item['item_id'] ?? null;
            $quantity_detail  = $item['quantity'] ?? null;
            $item_price_detail = $item['item_price'] ?? null; // Harga snapshot dari frontend

            // Validasi detail item (contoh sederhana)
            if (!is_numeric($item_id_detail) || !is_numeric($quantity_detail) || $quantity_detail <= 0 || !is_numeric($item_price_detail)) {
                throw new Exception("Invalid item detail data for item ID: " . $item_id_detail);
            }

            // Bind parameter untuk setiap detail item
            mysqli_stmt_bind_param(
                $stmt_details,
                "iidd", // sale_id (i), item_id (i), quantity (i), item_price (d)
                $sale_id,
                $item_id_detail,
                $quantity_detail,
                $item_price_detail
            );

            // Eksekusi statement untuk detail item
            if (!mysqli_stmt_execute($stmt_details)) {
                throw new Exception("Error executing details statement for item " . $item_id_detail . ": " . mysqli_stmt_error($stmt_details));
            }
        }

        mysqli_stmt_close($stmt_details); // Tutup statement details

        // --- 6. Commit Transaction ---
        mysqli_commit($conn);

        // Redirect on success
        header("Location: ../../../pages/transaction?code=200");
        exit(); // Penting untuk menghentikan eksekusi setelah redirect

    } catch (Exception $e) {
        // --- 7. Rollback Transaction on Error ---
        mysqli_rollback($conn);

        // Log error (opsional, tapi disarankan untuk debugging)
        error_log("Transaction failed: " . $e->getMessage());

        // Redirect on error
        $err_msg = urlencode($e->getMessage()); // Encode pesan error untuk URL
        header("Location: ../../../pages/transaction?code=501&msg=$err_msg");
        exit(); // Penting untuk menghentikan eksekusi setelah redirect
    }

} else {
    // Jika akses langsung tanpa POST request yang sesuai
    header("Location: ../../../pages/transaction?code=400&msg=Bad Request");
    exit();
}

?>