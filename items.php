<?php
// 1. Import the database connection
include 'includes/db.php';
include 'includes/functions.php';

$message = "";

if (isset($_POST['addItem'])) {

    $name = trim($_POST['itemName']);
    $Total_Quantity = (int) $_POST['totalQuantity'];

    $message = add_item(
        $conn,
        $name, 
        $Total_Quantity
    ); 
}else if (isset($_POST['deleteItem'])) {

    $ID = (int) $_POST['itemID'];

    $message = delete_item(
        $conn,
        $ID
    );
}else if (isset($_POST['updateItem'])) {

    $name = trim($_POST['itemName']);
    $quantity = (int) $_POST['quantity'];
    $ID = (int) $_POST['ID'];

    $message = update_item(
        $conn,
        $name, 
        $quantity,
        $ID
    );
}else if (isset($_POST['editItem'])) {

    $ID = (int) $_POST['itemID'];
    header("Location: edit.php?ID=" . $ID);

    exit();
// }else if (isset($_POST['addLoan'])){
//     $Item_ID = (int) $_POST['itemID'];
//     $Borrower_Name = (trim($_POST['employee_name']));
//     $Quantity = (int) $_POST['quantity'];

//     $message = create_loan(
//         $conn,
//         $Item_ID, 
//         $Borrower_Name, 
//         $Quantity,
//     );
}else if(isset($_POST['returnLoan'])){
    $Loan_ID = (int) $_POST['loanID'];
    $Return_Quantity = (int) $_POST['returnquantity'];

    $message = return_loan(
        $conn,
        $Loan_ID, 
        $Return_Quantity,
    );

}else  if (!empty($message)) { ?>

    <div class="alert alert-info" role="alert">
        <?php echo htmlspecialchars($message); ?>
    </div>

<?php } ?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    
    <title>Inventory Management System</title>
</head>
<body>
    <header class="topbar">
        <a href="main.html" class="brand">
            <span class="brand-name">Inventory Management</span>
        </a>
        <nav class="nav">
            <a href="main.html">Home</a>
            <a href="items.php" class="active">Items</a>
            <a href="add_item.php">Add Item</a>
            <a href="loan.php">New Loan</a>
            <a href="active_loan.php">Loans</a>
            <a href="returned_loan.php">Returned</a>
        </nav>
    </header>

    <?php if (!empty($message)) { ?>
    <div id="successAlert" class="alert theme-success" style="background-color:lightgreen"role="alert">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php } ?>

    <div class="container">
        <a href="add_item.php" class="btn btn-primary">+ Add New Item</a>
    </div>

    <table  class="table table-hover table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Item Name</th>
                <th scope="col">Available Quantity</th>
                <th scope="col">Total Quantity</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                //include 'includes/db.php';

                $sql = "SELECT * FROM items WHERE Is_Deleted = FALSE ORDER BY Item_ID ASC";
                $result = mysqli_query($conn, $sql);
                $counter = 1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr class="loan-row">
                            <td> <?php echo $counter++; ?> </td>
                            <td> <?php echo $row['Item_Name'] ?> </td>
                            <td> <?php echo $row['Quantity'] ?> </td>
                            <td> <?php echo $row['Total_Quantity'] ?> </td>
                            <td>
                                <div style="display: flex; align-items: center; margin: 0;">
                                    <form method="POST"
                                        onsubmit="if (<?php echo $row['Total_Quantity'] != $row['Quantity'] ? 'true' : 'false'; ?>) {
                                            alert('Cannot edit item: There are unreturned items.');
                                            return false;
                                        }
                                        return true;"
                                        style="display: inline;">
                                        <input type="hidden" name="itemID" value="<?php echo $row['Item_ID']; ?>">
                                        <button type="submit" name="editItem"
                                                style="background: transparent; border: none; color: black; cursor: pointer;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </form>


                                    <form method="POST"
                                        onsubmit="if (<?php echo $row['Total_Quantity'] != $row['Quantity'] ? 'true' : 'false'; ?>) {
                                            alert('Cannot delete item: There are unreturned items.');
                                            return false;
                                        }
                                        return confirm('Are you sure you want to delete this item?');"
                                        style="display: inline;">
                                        <input type="hidden" name="itemID"
                                            value="<?php echo $row['Item_ID']; ?>">
                                        <button type="submit" name="deleteItem"
                                                style="background: transparent; border: none; color: red; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                 </div>
                                <!-- <a href="index.php" style="color: red" title="Delete Item"><i class="fa-solid fa-trash"></i></a> -->
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr ><td colspan='5' style ='text-align: center;'>No items found</td></tr>";
                }
            ?>
        
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const alertMessage = document.getElementById("successAlert");

            if (alertMessage) {

                setTimeout(function () {

                    alertMessage.style.transition = "opacity 0.5s ease";
                    alertMessage.style.opacity = "0";

                    setTimeout(function () {
                        alertMessage.remove();
                    }, 500);

                }, 5000);

            }

        });
    </script>
</body>
</html>
