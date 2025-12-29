<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id'];


if (isset($_GET['month'])) {
    $selected_month = $_GET['month']; 
    $current_month = date('F Y', strtotime($selected_month)); 
} else {
    $selected_month = date('Y-m'); 
    $current_month = date('F Y');  
}

if (isset($_POST['add_category'])) {
    $name = $_POST['cat_name'];
    $stmt = $conn->prepare("INSERT INTO `category` (user_id, type, description) VALUES (?, 'expense', ?)");
    $stmt->bind_param("is", $user_id, $name);
    $stmt->execute();
}

if (isset($_POST['set_budget'])) {
    $cat_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    
    $check = $conn->query("SELECT budget_id FROM `budget` WHERE user_id=$user_id AND category_id=$cat_id AND month='$current_month'");
    
    if($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE `budget` SET amount=? WHERE user_id=? AND category_id=? AND month=?");
        $stmt->bind_param("diis", $amount, $user_id, $cat_id, $current_month);
    } else {
        $stmt = $conn->prepare("INSERT INTO `budget` (user_id, category_id, amount, month) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $user_id, $cat_id, $amount, $current_month);
    }
    $stmt->execute();
}

$sql = "SELECT c.description as name, c.category_id, b.amount as limit_amount, 
        (SELECT IFNULL(SUM(amount),0) FROM `transaction` t 
         WHERE t.category_id = c.category_id 
         AND t.user_id = $user_id 
         AND DATE_FORMAT(t.date, '%M %Y') = '$current_month') as spent
        FROM `category` c 
        LEFT JOIN `budget` b ON c.category_id = b.category_id AND b.month = '$current_month'
        WHERE c.user_id = $user_id AND c.type = 'expense' AND c.description != 'General'";
$budgets = $conn->query($sql);

$cats = $conn->query("SELECT * FROM `category` WHERE user_id = $user_id AND type='expense' AND description != 'General'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Budgets - gemFin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .budget-card { padding: 25px; background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .budget-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .budget-stats { display: flex; justify-content: space-between; font-size: 14px; color: #666; margin-top: 10px; }
        .progress-bg { background: #f0f0f0; border-radius: 20px; height: 12px; width: 100%; overflow: hidden; position: relative; }
        .progress-fill { height: 100%; border-radius: 20px; transition: width 0.5s ease; }
        .fill-green { background: #00C851; } 
        .fill-orange { background: #ffbb33; } 
        .fill-red { background: #ff4444; }    
    </style>
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <h1>Budgets for <?php echo $current_month; ?></h1>
        
        <form method="GET" style="margin-bottom: 20px;">
            <input type="month" name="month" value="<?php echo $selected_month; ?>" onchange="this.form.submit()">
        </form>

        <div style="display:flex; gap:10px; margin-bottom:20px;">
            <button class="btn" style="width:auto" onclick="document.getElementById('catModal').style.display='block'">+ New Category</button>
            <button class="btn" style="width:auto; background:var(--secondary)" onclick="document.getElementById('budgetModal').style.display='block'">Set Budget Limit</button>
        </div>

        <div class="cards-grid" style="grid-template-columns: repeat(2, 1fr);">
            <?php while($row = $budgets->fetch_assoc()): 
                $limit = $row['limit_amount'] ?? 0;
                $spent = $row['spent'];
                $remaining = $limit - $spent;

                if ($limit > 0) {
                    $percent = ($spent / $limit) * 100;
                } else {
                    $percent = ($spent > 0) ? 100 : 0; 
                }

                if($limit == 0) { $color = 'fill-red'; $status = "No Limit Set"; }
                elseif($percent > 100) { $color = 'fill-red'; $status = "Over Budget!"; }
                elseif($percent > 75) { $color = 'fill-orange'; $status = "Warning"; }
                else { $color = 'fill-green'; $status = "On Track"; }
            ?>
            
            <div class="budget-card">
                <div class="budget-header">
                    <h3 style="margin:0;"><?php echo $row['name']; ?></h3>
                    <?php if($limit > 0): ?>
                        <span style="font-weight:bold; color:var(--primary)">
                            <?php 
                                if($remaining >= 0) {
                                    echo "₹" . number_format($remaining) . " Left";
                                } else {
                                    echo "Over by ₹" . number_format(abs($remaining));
                                }
                            ?>
                        </span>
                    <?php else: ?>
                        <span style="font-size:12px; color:#999;">(No Limit)</span>
                    <?php endif; ?>
                </div>

                <div class="progress-bg">
                    <div class="progress-fill <?php echo $color; ?>" style="width: <?php echo min($percent, 100); ?>%"></div>
                </div>

                <div class="budget-stats">
                    <span>Spent: <b>₹<?php echo number_format($spent); ?></b></span>
                    <span>Limit: <b>₹<?php echo number_format($limit); ?></b></span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

<div id="catModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('catModal').style.display='none'">&times;</span>
        <h2>Add Category</h2>
        <form method="POST">
            <input type="text" name="cat_name" placeholder="e.g. Groceries" required>
            <button type="submit" name="add_category" class="btn">Add</button>
        </form>
    </div>
</div>

<div id="budgetModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('budgetModal').style.display='none'">&times;</span>
        <h2>Set Monthly Budget</h2>
        <form method="POST">
            <label>Choose Category:</label>
            <select name="category_id">
                <?php foreach($cats as $c) echo "<option value='{$c['category_id']}'>{$c['description']}</option>"; ?>
            </select>
            <label>Limit Amount (₹):</label>
            <input type="number" name="amount" placeholder="e.g. 500" required>
            <button type="submit" name="set_budget" class="btn">Set Budget</button>
        </form>
    </div>
</div>
</body>
</html>