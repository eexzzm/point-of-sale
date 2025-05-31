<?php

require("../../src/server/connection.php");
require("../../src/server/getter/get_items.php");
require("../../src/server/helper/print_alert.php");
require("../../src/server/helper/auth.php");
  
if ( !auth() ) header("Location: ../login");

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Manifest -->
    <link rel="manifest" href="../../manifest.json" type="aplication/json" >
    <meta name="theme-color" content="#0d6efd">

    <!-- Icon -->
    <link rel="icon" href="../../src/public/images/icons/icon-144x144.png" size="144x144" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="../../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../node_modules/@fortawesome/fontawesome-free/css/all.css">

    <title>POS - Create new transaction</title>
  </head>
  <body>
    <!-- Navbar -->
    <section data-root="navbar"></section>
    <!-- Jumbotron -->
    <!-- <section data-root="jumbotron"></section> -->

    <!-- Main -->
    <main class="container-md px-4 mt-5">
      
      <!-- Breadcumb -->
      <div class="mb-5" data-root="breadcumb"></div>
      
      <h1 class="mb-3">
        Create new transaction
      </h1>
      
      <!-- Alert -->
      <?php print_alert() ?>
      
      <!-- Form -->
      <form method="POST" action="../../src/server/setter/set_transaction.php" id="transactionForm">
        <div class="mb-3">
            <label for="transactionId" class="form-label">ID Transaction</label>
            <input type="text" placeholder="ex: TRX001" name="transactionId" class="form-control form-control-lg" id="transactionId" aria-describedby="idTransactionHelp" required>
            <div id="idTransactionHelp" class="form-text">transaction ID is used to mark each transaction</div>
        </div>

        <div class="card p-3 mb-4">
            <h5 class="card-title mb-3">Add Item to Cart</h5>
            <div class="mb-3">
                <input type="hidden" name="selectedItemId" id="selectedItemId" value="" required>
                <label for="nameOfItem" class="form-label">Name of item</label>
                <select data-role="select-items" class="form-select form-select-lg" aria-label=".form-select-lg example" id="nameOfItem">
                  <option selected="" value="">--Choose item</option>
                  <!-- get item per id -->
                  <?php
                      foreach ( get_items($conn) as $item )
                      {
                        $item_id = $item["item_id"];
                        $item_name = $item["item_name"];
                        $item_price = $item["item_price"];
                        echo <<<EOT
                          <option value="$item_id@$item_name@$item_price"> $item_name </option>
EOT;
                      }
                    ?>
                </select>
            </div>
            <!-- amount of item -->
            <label for="itemQuantity" class="form-label">Quantity</label>
            <div class="input-group mb-3">
              <span class="input-group-text">Pcs</span>
              <input type="number" placeholder="ex: 2" name="itemQuantity" class="form-control form-control-lg" id="itemQuantity" value="1" min="1" required>
            </div>
            
            <!-- price -->
            <label for="currentPricePerUnit" class="form-label">Price per unit</label>
            <div class="input-group mb-3">
              <span class="input-group-text">Rp.</span>
              <input type="text" name="currentPricePerUnit" class="form-control form-control-lg" id="currentPricePerUnit" readonly>
            </div>
            
            <button type="button" class="btn btn-info" id="addItemToCartBtn">Add to Cart</button>
          </div>
          
          <!-- cart -->
          <div class="card p-3 mb-4">
            <h5 class="card-title mb-3">Shopping Cart</h5>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price per unit</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="cartItemsTableBody">
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="4" class="text-end">Subtotal (before discount)</th>
                      <td id="cartSubtotal">Rp. 0</td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <button type="button" class="btn btn-outline-danger mt-3" id="clearCartBtn">Clear Cart</button>
            </div>
            
            <!-- cart -->
        <div class="card p-3 mb-4">
            <h5 class="card-title mb-3">Payment</h5>
            <label for="discountInput" class="form-label">Discount (%)</label>
            <div class="input-group mb-3">
                <input type="number" name="discountInput" class="form-control form-control-lg" id="discountInput" value="0" min="0" max="100">
                <span class="input-group-text">%</span>
            </div>

            <label for="finalTotal" class="form-label">Final Total</label>
            <div class="input-group mb-3">
                <span class="input-group-text">Rp.</span>
                <input type="text" name="finalTotal" class="form-control form-control-lg" id="finalTotal" readonly value="0">
            </div>

            <label for="cashInput" class="form-label">Cash</label>
            <div class="input-group mb-3">
                <span class="input-group-text">Rp.</span>
                <input type="text" placeholder="ex: 45.000" name="cashInput" class="form-control form-control-lg" id="cashInput" required>
            </div>

            <label for="moneyChangesOutput" class="form-label">Money Changes</label>
            <div class="input-group mb-3">
                <span class="input-group-text">Rp.</span>
                <input type="text" name="moneyChangesOutput" class="form-control form-control-lg" id="moneyChangesOutput" readonly>
            </div>
        </div>

        <button name="process_transaction" class="btn btn-primary btn-lg" type="submit">Process Transaction</button>
        <button class="btn btn-secondary btn-lg" type="reset" id="resetFormBtn">Reset Form</button>
    </form>
    </main>    
    
    <!-- Footer -->
    <section data-root="footer"></section>
    
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="../../src/main.js" type="module" charset="utf-8"></script>
    <script src="../../src/views/Transaction.js" type="module" charset="utf-8"></script>
  </body>
</html>