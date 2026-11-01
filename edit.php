<?php
    include './includes/db.php';
    require_once './includes/functions.php';

    if (!isset($_GET['ID']) || !is_numeric($_GET['ID'])) {
    die("Invalid item ID.");
    }

    $ID = (int) $_GET['ID'];
    $row = edit_item($conn, $ID);
    if($row['Total_Quantity'] != $row['Quantity']){
        echo "<p style='color: red;'>There are unreturned items.</p>";
        //header("Location: index.php");
        exit();
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="topbar">
        <a href="main.html" class="brand">
            <span class="brand-name">InvenTrack</span>
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

    <main>
    <div class="form-box">
        <form action="items.php" method="POST">
            <h1 class="page-title">Change Item</h1>
            <p class="text-muted">Enter new details below</p>
            <div class="mb-3">
                <label for="item-name"  class="form-label">Item Name:</label>
                <input
                type="text"
                class="form-control"
                id="item-name"
                name="itemName"
                value="<?php echo htmlspecialchars($row['Item_Name']); ?>"
                required
                >
            </div>

            <div class="mb-3">
                <label for="item-quantity"  class="form-label">Quantity:</label>
                <input
                type="number"
                class="form-control"
                id="item-quantity"
                name="quantity"
                min="0"
                value="<?php echo htmlspecialchars($row['Quantity']); ?>"
                required
                >
            </div>

            <div class="mb-3">
                <input
                type="hidden"
                class="form-control"
                id="item-ID"
                name="ID"
                value="<?php echo $ID; ?>"
                >
            </div>
            <button type="submit" name="updateItem" class="btn btn-primary">UPDATE</button>
            <a href="main.html" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    </main>
</body>
</html>
