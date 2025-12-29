<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id'];

$filter_type = $_GET['filter'] ?? 'this_year';
$start_date = "";
$end_date = "";

if ($filter_type == 'this_year') {
    $start_date = date('Y-01-01');
    $end_date = date('Y-12-31');
} elseif ($filter_type == 'last_year') {
    $start_date = date('Y-01-01', strtotime('-1 year'));
    $end_date = date('Y-12-31', strtotime('-1 year'));
} elseif ($filter_type == 'last_6_months') {
    $start_date = date('Y-m-d', strtotime('-6 months'));
    $end_date = date('Y-m-d');
} elseif ($filter_type == 'custom') {
    $start_date = $_GET['start_date'] ?? date('Y-01-01');
    $end_date = $_GET['end_date'] ?? date('Y-12-31');
}

$sql_trend = "
    SELECT 
        DATE_FORMAT(t.date, '%Y-%m') as month_year,
        DATE_FORMAT(t.date, '%M %Y') as display_date,
        SUM(CASE WHEN i.income_id IS NOT NULL THEN t.amount ELSE 0 END) as total_income,
        SUM(CASE WHEN e.expense_id IS NOT NULL THEN t.amount ELSE 0 END) as total_expense
    FROM `transaction` t
    LEFT JOIN `income` i ON t.transaction_id = i.transaction_id
    LEFT JOIN `expense` e ON t.transaction_id = e.transaction_id
    WHERE t.user_id = $user_id AND t.date BETWEEN '$start_date' AND '$end_date'
    GROUP BY month_year
    ORDER BY month_year ASC
";
$trend_res = $conn->query($sql_trend);

$trend_labels = [];
$trend_income = [];
$trend_expense = [];
$trend_net = [];

while($row = $trend_res->fetch_assoc()) {
    $trend_labels[] = $row['display_date'];
    $trend_income[] = $row['total_income'];
    $trend_expense[] = $row['total_expense'];
    $trend_net[] = $row['total_income'] - $row['total_expense'];
}


$cat_sql = "
    SELECT c.description, SUM(t.amount) as total 
    FROM `transaction` t ,`category` c,`expense` e
    WHERE t.transaction_id = e.transaction_id AND t.category_id = c.category_id AND 
    t.user_id = $user_id AND c.type='expense' 
    AND t.date BETWEEN '$start_date' AND '$end_date'
    GROUP BY c.category_id
";
$cat_data = $conn->query($cat_sql);

$cat_labels = [];
$cat_amounts = [];
while($r = $cat_data->fetch_assoc()) {
    $cat_labels[] = $r['description'];
    $cat_amounts[] = $r['total'];
}


$total_inc_sum = array_sum($trend_income);
$total_exp_sum = array_sum($trend_expense);
$net_balance = $total_inc_sum - $total_exp_sum;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - gemFin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .filter-bar { background: white; padding: 15px; border-radius: 10px; display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .filter-bar select, .filter-bar input { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .summary-card { text-align: center; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .big-num { font-size: 24px; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <h1>Financial Reports 📊</h1>
        
        <form method="GET" class="filter-bar">
            <label><strong>Time Period:</strong></label>
            <select name="filter" onchange="toggleCustomDate(this.value)">
                <option value="this_year" <?php if($filter_type=='this_year') echo 'selected'; ?>>This Year</option>
                <option value="last_year" <?php if($filter_type=='last_year') echo 'selected'; ?>>Last Year</option>
                <option value="last_6_months" <?php if($filter_type=='last_6_months') echo 'selected'; ?>>Last 6 Months</option>
                <option value="custom" <?php if($filter_type=='custom') echo 'selected'; ?>>Custom Range</option>
            </select>

            <div id="custom-dates" style="display: <?php echo ($filter_type=='custom') ? 'flex' : 'none'; ?>; gap:10px;">
                <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                <span>to</span>
                <input type="date" name="end_date" value="<?php echo $end_date; ?>">
            </div>

            <button type="submit" class="btn" style="width:auto; padding: 8px 20px;">Apply Filter</button>
        </form>

        <div class="cards-grid" style="margin-bottom: 30px;">
            <div class="summary-card">
                <span style="color:#777">Total Income</span>
                <div class="big-num text-green">₹<?php echo number_format($total_inc_sum); ?></div>
            </div>
            <div class="summary-card">
                <span style="color:#777">Total Expenses</span>
                <div class="big-num text-red">₹<?php echo number_format($total_exp_sum); ?></div>
            </div>
            <div class="summary-card">
                <span style="color:#777">Net Flow</span>
                <div class="big-num" style="color: <?php echo ($net_balance >= 0) ? 'var(--green)' : 'var(--red)'; ?>">
                    ₹<?php echo number_format($net_balance); ?>
                </div>
            </div>
        </div>
        
        <div class="cards-grid" style="grid-template-columns: 2fr 1fr;">
            <div class="card">
                <h3>Monthly Trends (Income vs Expense vs Net)</h3>
                <div style="position: relative; height: 400px; width: 100%;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="card">
                <h3>Expenses by Category</h3>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="catChart"></canvas>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function toggleCustomDate(val) {
        document.getElementById('custom-dates').style.display = (val === 'custom') ? 'flex' : 'none';
    }

    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    
    new Chart(ctxTrend, {
        type: 'line', 
        data: {
            labels: <?php echo json_encode($trend_labels); ?>, 
            datasets: [
                {
                    label: 'Income',
                    data: <?php echo json_encode($trend_income); ?>,
                    borderColor: '#00C851', 
                    backgroundColor: '#00C851',
                    borderWidth: 3,
                    tension: 0.4, 
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false
                },
                {
                    label: 'Expense',
                    data: <?php echo json_encode($trend_expense); ?>,
                    borderColor: '#ff4444', 
                    backgroundColor: '#ff4444',
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false
                },
                {
                    label: 'Net Balance',
                    data: <?php echo json_encode($trend_net); ?>,
                    borderColor: '#2A2A2E', 
                    backgroundColor: '#2A2A2E',
                    borderWidth: 2,
                    borderDash: [5, 5], 
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index', 
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12 }
                    },
                    onClick: function(e, legendItem, legend) {
                        const index = legendItem.datasetIndex;
                        const ci = legend.chart;
                        if (ci.isDatasetVisible(index)) {
                            ci.hide(index);
                            legendItem.hidden = true;
                        } else {
                            ci.show(index);
                            legendItem.hidden = false;
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#000',
                    bodyColor: '#000',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0' } 
                },
                x: {
                    grid: { display: false } 
                }
            }
        }
    });

    const ctxCat = document.getElementById('catChart');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($cat_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($cat_amounts); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                borderWidth: 0, 
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
</body>
</html>