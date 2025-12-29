<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id'];

if (isset($_POST['add_goal'])) {
    $desc = $_POST['description'];
    $target = $_POST['target'];
    $date = $_POST['date'];
    $conn->query("INSERT INTO `saving_goals` (user_id, description, target_amount, target_date) VALUES ($user_id, '$desc', '$target', '$date')");
}

if (isset($_POST['add_money'])) {
    $goal_id = $_POST['goal_id'];
    $amount = $_POST['amount'];
    $conn->query("UPDATE `saving_goals` SET current_amount = current_amount + $amount WHERE goal_id=$goal_id");
}

$goals = $conn->query("SELECT * FROM `saving_goals` WHERE user_id = $user_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Goals - gemFin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card.completed { border: 2px solid #FFD700; background: #fffdf0; }
        .text-gold { color: #d4af37; font-weight: bold; }
        .congrats-box { text-align: center; background: #d4af37; color: white; padding: 10px; border-radius: 8px; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <header style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Saving Goals 🎯</h1>
            <button class="btn" style="width:auto" onclick="document.getElementById('goalModal').style.display='block'">+ New Goal</button>
        </header>

        <div class="cards-grid">
            <?php while($row = $goals->fetch_assoc()): 
                $current = $row['current_amount'];
                $target = $row['target_amount'];
                $percent = ($target > 0) ? ($current / $target) * 100 : 0;
                $is_completed = ($current >= $target);
                $card_class = $is_completed ? 'completed' : '';
                $bar_color = $is_completed ? '#FFD700' : 'var(--green)'; 
            ?>
            <div class="card <?php echo $card_class; ?>">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3><?php echo $row['description']; ?></h3>
                    <?php if($is_completed): ?>
                        <span style="background:#FFD700; color:#fff; padding:2px 8px; border-radius:10px; font-size:12px; font-weight:bold;">DONE</span>
                    <?php endif; ?>
                </div>

                <h2 style="color:var(--primary); margin-bottom:5px;">
                    ₹<?php echo number_format($current); ?>
                    <span style="font-size:14px; color:#777; font-weight:normal;"> / ₹<?php echo number_format($target); ?></span>
                </h2>
                
                <div style="display:flex; justify-content:space-between; font-size:12px; color:#555;">
                    <span>Progress</span>
                    <span><?php echo number_format($percent, 1); ?>%</span>
                </div>
                <div style="background:#eee; height:12px; border-radius:10px; margin:5px 0 15px 0; overflow:hidden;">
                    <div style="height:100%; width:<?php echo min($percent, 100); ?>%; background:<?php echo $bar_color; ?>; transition: width 0.5s;"></div>
                </div>
                
                <?php if($is_completed): ?>
                    <div class="congrats-box">
                        🎉 Target Completed!
                    </div>
                <?php else: ?>
                    <form method="POST" style="display:flex; gap:5px;">
                        <input type="hidden" name="goal_id" value="<?php echo $row['goal_id']; ?>">
                        <input type="number" name="amount" placeholder="Add ₹" style="flex:1; margin:0;" required>
                        <button type="submit" name="add_money" class="btn" style="width:auto; font-size:14px;">Save</button>
                    </form>
                <?php endif; ?>

            </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

<div id="goalModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('goalModal').style.display='none'">&times;</span>
        <h2>New Goal</h2>
        <form method="POST">
            <input type="text" name="description" placeholder="Goal Name (e.g. Car)" required>
            <input type="number" name="target" placeholder="Target Amount (₹)" required>
            <input type="date" name="date" required>
            <button type="submit" name="add_goal" class="btn">Create Goal</button>
        </form>
    </div>
</div>
</body>
</html>