<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$cats_result = $conn->query("SELECT * FROM `category` WHERE user_id = $user_id ORDER BY description ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_transaction'])) {
    $type = $_POST['type']; 
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $category_id = $_POST['category_id'];

    $today = date('Y-m-d');
    if ($date > $today) {
        echo "<script>
                alert('🚫 Error: You cannot add transactions for future dates!');
                window.location.href='dashboard.php';
              </script>";
        exit();
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO `transaction` (user_id, category_id, amount, description, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iidss", $user_id, $category_id, $amount, $description, $date);
        $stmt->execute();
        $trans_id = $conn->insert_id;

        if ($type == 'expense') {
            $stmt2 = $conn->prepare("INSERT INTO `expense` (transaction_id, merchant) VALUES (?, ?)");
            $stmt2->bind_param("is", $trans_id, $description);
            $stmt2->execute();
            $stmt3 = $conn->prepare("UPDATE `balance` SET current_balance = current_balance - ? WHERE user_id = ?");
            $stmt3->bind_param("di", $amount, $user_id);
            $stmt3->execute();
        } else {
            $stmt2 = $conn->prepare("INSERT INTO `income` (transaction_id, source) VALUES (?, ?)");
            $stmt2->bind_param("is", $trans_id, $description);
            $stmt2->execute();
            $stmt3 = $conn->prepare("UPDATE `balance` SET current_balance = current_balance + ? WHERE user_id = ?");
            $stmt3->bind_param("di", $amount, $user_id);
            $stmt3->execute();
        }
        $conn->commit();
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
    }
}


$bal_sql = "SELECT current_balance FROM `balance` WHERE user_id = $user_id";
$bal_res = $conn->query($bal_sql);
$balance = ($bal_res->num_rows > 0) ? $bal_res->fetch_assoc()['current_balance'] : 0.00;

$exp_sql = "SELECT SUM(t.amount) as total FROM `transaction` t JOIN `expense` e ON t.transaction_id = e.transaction_id WHERE t.user_id = $user_id";
$exp_res = $conn->query($exp_sql);
$total_expense = $exp_res->fetch_assoc()['total'] ?? 0.00;

$inc_sql = "SELECT SUM(t.amount) as total FROM `transaction` t JOIN `income` i ON t.transaction_id = i.transaction_id WHERE t.user_id = $user_id";
$inc_res = $conn->query($inc_sql);
$total_income = $inc_res->fetch_assoc()['total'] ?? 0.00;

$hist_sql = "SELECT t.*, c.description as cat_name FROM `transaction` t 
             JOIN `category` c ON t.category_id = c.category_id 
             WHERE t.user_id = $user_id 
             ORDER BY date DESC, transaction_id DESC 
             LIMIT 10";
$history = $conn->query($hist_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - gemFin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1>Hello, <?php echo $_SESSION['first_name']; ?> 👋</h1>
            <button class="btn" style="width:auto" onclick="document.getElementById('addModal').style.display='block'">+ Add Transaction</button>
        </header>

        <div class="cards-grid">
            <div class="card">
                <h3>Current Balance</h3>
                <p>₹<?php echo number_format($balance, 2); ?></p>
            </div>
            <div class="card">
                <h3>Total Income</h3>
                <p class="text-green">+₹<?php echo number_format($total_income, 2); ?></p>
            </div>
            <div class="card">
                <h3>Total Expense</h3>
                <p class="text-red">-₹<?php echo number_format($total_expense, 2); ?></p>
            </div>
        </div>

        <h3>Recent Activity</h3>
        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #eee; border-radius: 10px;">
            <table>
                <thead>
                    <tr style="position: sticky; top: 0; background: #fff; z-index: 1; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $history->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td>
                            <span style="background:#eee; padding:2px 8px; border-radius:10px; font-size:12px;">
                                <?php echo $row['cat_name']; ?>
                            </span>
                        </td>
                        <td style="font-weight:bold;">
                            <span style="<?php echo ($row['amount'] > 0) ? 'color:var(--secondary)' : 'color:var(--green)'; ?>">
                                ₹<?php echo number_format($row['amount'], 2); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        <h2 style="color:var(--primary)">Add Transaction</h2>
        <form method="POST">
            <input type="hidden" name="add_transaction" value="1">
            
            <label>Type</label>
            <select name="type" id="txnType" onchange="filterCategories()">
                <option value="expense">Expense (-)</option>
                <option value="income">Income (+)</option>
            </select>

            <label>Category</label>
            <select name="category_id" id="txnCategory" required>
                <?php 
                if ($cats_result->num_rows > 0) {
                    while($cat = $cats_result->fetch_assoc()) {
                        echo "<option value='" . $cat['category_id'] . "'>" . $cat['description'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No Categories Found</option>";
                }
                ?>
            </select>

            <label>Amount (₹)</label>
            <input type="number" step="0.01" name="amount" placeholder="0.00" required>
            
            <label>Description</label>
            <input type="text" name="description" placeholder="e.g. Grocery Store" required>
            
            <label>Date</label>
            <input type="date" name="date" max="<?php echo date('Y-m-d'); ?>" required>

            <button type="submit" class="btn">Save</button>
        </form>
    </div>
</div>

<script>
    const allCategories = [];
    document.addEventListener("DOMContentLoaded", function() {
        const catSelect = document.getElementById('txnCategory');
        for (let i = 0; i < catSelect.options.length; i++) {
            allCategories.push({
                value: catSelect.options[i].value,
                text: catSelect.options[i].text
            });
        }
        filterCategories();
    });

    function filterCategories() {
        const type = document.getElementById('txnType').value;
        const catSelect = document.getElementById('txnCategory');
        
        catSelect.innerHTML = ''; 

        allCategories.forEach(opt => {
            if (type === 'income') {
                if (opt.text.trim() === 'General') {
                    let newOption = new Option(opt.text, opt.value);
                    catSelect.add(newOption);
                }
            } else {
                if (opt.text.trim() !== 'General') {
                    let newOption = new Option(opt.text, opt.value);
                    catSelect.add(newOption);
                }
            }
        });
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            document.getElementById('addModal').style.display = "none";
        }
    }
</script>

</body>
</html>