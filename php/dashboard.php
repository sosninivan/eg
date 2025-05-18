<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

try {
    $conn = new PDO("sqlite:php/../database/users.db");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Ошибка подключения к БД: " . $e->getMessage();
    die();
}
?>

<div class="container mt-5">
  <h2 class="mb-4">Мои активные абонементы</h2>
  <div class="row">
    <?php foreach ($subscriptions as $sub): 
        $start = new DateTime($sub['start_date']);
        $end = new DateTime($sub['end_date']);
        $today = new DateTime();
        $total_days = $start->diff($end)->days;
        $remaining_days = max(0, $today->diff($end)->days);
        $remaining_sessions = $sub['total_sessions'] - $sub['used_sessions'];
        $progress = round(($sub['used_sessions'] / $sub['total_sessions']) * 100);
    ?>
      <div class="col-md-6">
        <div class="card mb-4">
          <div class="card-header bg-success text-white">
            <h5><?= htmlspecialchars($sub['type']) ?></h5>
          </div>
          <div class="card-body">
            <p><strong>Срок действия:</strong> <?= $sub['start_date'] ?> - <?= $sub['end_date'] ?></p>
            <p><strong>Осталось дней:</strong> <span class="badge bg-primary"><?= $remaining_days ?></span></p>
            <p><strong>Осталось тренировок:</strong> <span class="badge bg-primary"><?= $remaining_sessions ?> из <?= $sub['total_sessions'] ?></span></p>
            <div class="progress mt-3">
              <div class="progress-bar bg-success" role="progressbar"
                   style="width: <?= $progress ?>%;"
                   aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                   <?= $progress ?>% использовано
              </div>
            </div>
          </div>
        </div> 
      </div>
    <?php endforeach; ?>
  </div>
</div>
