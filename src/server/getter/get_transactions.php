<?php

//Handler for query all records from table transactions 

function get_transactions( $conn )
{
  //Create sql inner join
  // Menggabungkan tabel sales (header transaksi), transaction_details (detail item), dan items (informasi produk)
  $sql = "
    SELECT
        s.transaction_id_txt,
        td.transaction_id,
        td.quantity AS transaction_amount,
        s.total_amount AS transaction_total,
        s.cash_paid AS transaction_cash,
        s.money_changes AS transaction_money_changes, 
        s.sale_date AS transaction_create_at, 
        i.item_id_txt,                
        i.item_name,                  
        td.item_price                 
    FROM
        sales s
    INNER JOIN
        transaction_details td ON s.sale_id = td.sale_id
    INNER JOIN
        items i ON td.item_id = i.item_id
    ORDER BY
        s.sale_date DESC,             -- Urutkan berdasarkan tanggal transaksi terbaru
        s.transaction_id_txt ASC,     -- Kemudian berdasarkan ID transaksi teks
        td.transaction_id ASC         -- Kemudian berdasarkan ID detail item (untuk konsistensi)
  ";
  
  //query
  $results = mysqli_query($conn, $sql);
  
  //Wrapper
  $rows = [];
    //Save each row into wrapper
    while ( $res = mysqli_fetch_assoc( $results ))
    {
        array_push( $rows, $res );
    }
    
    return $rows;
}

?>