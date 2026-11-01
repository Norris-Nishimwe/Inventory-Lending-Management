<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <header class="topbar">
        <a href="main.html" class="brand">
            <span class="brand-name">Equipement Lending</span>
        </a>
        <nav class="nav">
            <a href="main.html">Home</a>
            <a href="items.php">Items</a>
            <a href="add_item.php" class="active">Add Item</a>
            <a href="loan.php">New Loan</a>
            <a href="active_loan.php">Loans</a>
            <a href="returned_loan.php">Returned</a>
        </nav>
    </header>

    <main>
    <div class="form-box">

        <form action="index.php" method="POST">
            <h1 class="page-title">Add New Item</h1>
            <p class="text-muted">Enter the item details below.</p>
            <div class="mb-3">
                <label for="item-name" class="form-label">Item Name:</label>
                <input
                type="text"
                class="form-control"
                id="item-name"
                name="itemName"
                placeholder="e.g., Apple"
                required
                >
            </div>

            <div class="mb-3">
                <label for="item-quantity" class="form-label">Total Quantity:</label>
                <input
                type="number"
                class="form-control"
                id="item-quantity"
                name="totalQuantity"
                min="1"
                max="100"
                value="1"
                required
                >
            </div>

            <button type="submit" class="btn btn-primary" name="addItem">Submit</button>
            <a href="main.html" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    </main>
</body>
</html>
