<?php 
    require_once 'includes/db.php';
    require_once 'includes/functions.php';

    $sql = "SELECT * FROM items WHERE Quantity > 0 ORDER BY Item_Name ASC";
    $result = mysqli_query($conn, $sql);
    if (isset($_POST['addLoan'])){
    $Item_ID = (int) $_POST['itemID'];
    $Borrower_Name = (trim($_POST['employee_name']));
    $Quantity = (int) $_POST['quantity'];

    $message = create_loan(
        $conn,
        $Item_ID, 
        $Borrower_Name, 
        $Quantity,
    );
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="topbar">
        <a href="main.html" class="brand">
            <span class="brand-name">Inventory & Lending</span>
        </a>
        <nav class="nav">
            <a href="main.html">Home</a>
            <a href="items.php">Items</a>
            <a href="add_item.php">Add Item</a>
            <a href="loan.php" class="active">New Loan</a>
            <a href="active_loan.php">Loans</a>
            <a href="returned_loan.php">Returned</a>
        </nav>
    </header>

    <main>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    <div class="form-box">
    <h1 class="page-title">New Loan</h1>
    <form action="loan.php" method="POST">
        <div class="mb-3">
            <label for="employee_name" class="form-label">Name & Surname:</label>
            <input 
            type="text" 
            id="employee_name" 
            name="employee_name" 
            class="form-control"
            placeholder="Enter name and surname" 
            required
            >
        </div>

        <div class="mb-3">

            <label for="itemID" class="form-label"> Select Item </label>
            <select
                class="form-select"
                id="itemID"
                name="itemID"
                required
            >
                <option value="" selected disabled>
                    Choose an item
                </option>

                <?php while ($item = mysqli_fetch_assoc($result)): ?>

                    <option
                        value="<?php echo $item['Item_ID']; ?>"
                    >
                        <?php echo htmlspecialchars($item['Item_Name']); ?>
                        (<?php echo $item['Quantity']; ?> units available)
                    </option>

                <?php endwhile; ?>

            </select>

        </div>

        

        <div class="mb-3">

            <label for="quantity" class="form-label">
                Enter Quantity
            </label>

            <input
                type="number"
                id="quantity"
                name="quantity"
                class="form-control"
                min="1"
                placeholder="Enter quantity"
                required
            >

        </div>

        <div class="d-flex gap-2">

            <button type="submit"
                    name="addLoan"
                    class="btn btn-primary">
                <i class="fa-solid fa-check me-2"></i>
                Save
            </button>

            <a href="items.php" class="btn btn-secondary">
                Cancel
            </a>

        </div>
    </form>
    <?php if (mysqli_num_rows($result) === 0): ?>
        <p class="text-danger mt-3 mb-0">
            No items are currently available for borrowing.
        </p>
    <?php endif; ?>
    </div>
    </main>
    
</body>
</html>
