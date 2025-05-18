<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Личный кабинет — FitLife</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css">
</head>
<body>



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







<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.html">FitLife</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
      aria-controls="navbarContent" aria-expanded="false" aria-label="Переключить навигацию">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="dashboard.html">Личный кабинет</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="php/logout.php">Выйти</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- HTML разметка до блока -->
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


  <h2 class="mt-5 mb-4">Мои запланированные тренировки</h2>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Дата</th>
          <th>Время</th>
          <th>Тренировка</th>
          <th>Тренер</th>
          <th>Статус</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>20.05.2024</td>
          <td>18:00-19:30</td>
          <td>Функциональный тренинг</td>
          <td>Анна Смирнова</td>
          <td><span class="badge bg-success">Подтверждено</span></td>
          <td>
            <button class="btn btn-sm btn-outline-danger">Отменить</button>
          </td>
        </tr>
        <tr>
          <td>22.05.2024</td>
          <td>09:00-10:00</td>
          <td>Персональная тренировка</td>
          <td>Иван Петров</td>
          <td><span class="badge bg-warning text-dark">Ожидание</span></td>
          <td>
            <button class="btn btn-sm btn-outline-danger">Отменить</button>
          </td>
        </tr>
        <tr>
          <td>25.05.2024</td>
          <td>17:00-18:00</td>
          <td>Йога</td>
          <td>Елена Кузнецова</td>
          <td><span class="badge bg-success">Подтверждено</span></td>
          <td>
            <button class="btn btn-sm btn-outline-danger">Отменить</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
    <button class="btn btn-primary me-md-2" type="button">Записаться на новую тренировку</button>
  </div>
</div>

<style>
  .training-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  
  .training-table th {
    background-color: #f8f9fa;
    font-weight: 600;
  }
  
  .training-status-badge {
    font-size: 0.85em;
    padding: 5px 10px;
    border-radius: 20px;
  }
</style>

<!-- Контент -->
<div class="container mt-4">
  <h1>Добро пожаловать в личный кабинет!</h1>
  <p class="lead">Здесь вы можете просмотреть и изменить свои данные, записаться на тренировки и управлять профилем.</p>
    <!-- Виджет: Загруженность зала -->
    <div class="card">
    <div class="card-header bg-info text-white">
        Текущая загруженность зала
    </div>
    <div class="card-body">
        <p class="card-text">Количество людей в зале сейчас: <strong id="people-count">42</strong> из <strong>100</strong></p>
        
        <div class="progress">
        <div id="load-progress" class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 42%;" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100">
            42%
        </div>
        </div>
    </div>
    </div>


  <!-- Секция с тренировками -->
  <h2 class="mt-5 mb-4">Доступные тренировки</h2>
  <div class="row">
    <!-- Тренировка 1 -->
    <div class="col-md-4">
      <div class="card training-card">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title">Силовая тренировка</h5>
          <span class="badge badge-sport bg-warning text-dark">Бодибилдинг</span>
        </div>
        <div class="card-body">
          <p class="card-text"><strong>Тренер:</strong> Иван Петров</p>
          <p class="card-text"><strong>Опыт:</strong> 8 лет</p>
          <p class="card-text"><strong>Цена:</strong> 1500 руб.</p>
          <p class="card-text"><strong>Описание:</strong> Интенсивная тренировка для набора мышечной массы.</p>
          <a href="#" class="btn btn-primary">Записаться</a>
        </div>
      </div>
    </div>
    
    <!-- Тренировка 2 -->
    <div class="col-md-4">
      <div class="card training-card">
        <div class="card-header bg-success text-white">
          <h5 class="card-title">Функциональный тренинг</h5>
          <span class="badge badge-sport bg-info">Кроссфит</span>
        </div>
        <div class="card-body">
          <p class="card-text"><strong>Тренер:</strong> Анна Смирнова</p>
          <p class="card-text"><strong>Опыт:</strong> 5 лет</p>
          <p class="card-text"><strong>Цена:</strong> 1200 руб.</p>
          <p class="card-text"><strong>Описание:</strong> Развитие силы, выносливости и координации.</p>
          <a href="#" class="btn btn-success">Записаться</a>
        </div>
      </div>
    </div>
    
    <!-- Тренировка 3 -->
    <div class="col-md-4">
      <div class="card training-card">
        <div class="card-header bg-danger text-white">
          <h5 class="card-title">HIIT тренировка</h5>
          <span class="badge badge-sport bg-dark">Кардио</span>
        </div>
        <div class="card-body">
          <p class="card-text"><strong>Тренер:</strong> Максим Иванов</p>
          <p class="card-text"><strong>Опыт:</strong> 6 лет</p>
          <p class="card-text"><strong>Цена:</strong> 1000 руб.</p>
          <p class="card-text"><strong>Описание:</strong> Высокоинтенсивный интервальный тренинг для сжигания жира.</p>
          <a href="#" class="btn btn-danger">Записаться</a>
        </div>
      </div>
    </div>
    
    <!-- Тренировка 4 -->
    <div class="col-md-4">
      <div class="card training-card">
        <div class="card-header bg-warning text-dark">
          <h5 class="card-title">Йога</h5>
          <span class="badge badge-sport bg-light text-dark">Гибкость</span>
        </div>
        <div class="card-body">
          <p class="card-text"><strong>Тренер:</strong> Елена Кузнецова</p>
          <p class="card-text"><strong>Опыт:</strong> 10 лет</p>
          <p class="card-text"><strong>Цена:</strong> 800 руб.</p>
          <p class="card-text"><strong>Описание:</strong> Улучшение гибкости, баланса и релаксации.</p>
          <a href="#" class="btn btn-warning">Записаться</a>
        </div>
      </div>
    </div>
    
    <!-- Тренировка 5 -->
    <div class="col-md-4">
      <div class="card training-card">
        <div class="card-header bg-info text-white">
          <h5 class="card-title">Пауэрлифтинг</h5>
          <span class="badge badge-sport bg-primary">Сила</span>
        </div>
        <div class="card-body">
          <p class="card-text"><strong>Тренер:</strong> Дмитрий Соколов</p>
          <p class="card-text"><strong>Опыт:</strong> 12 лет</p>
          <p class="card-text"><strong>Цена:</strong> 1800 руб.</p>
          <p class="card-text"><strong>Описание:</strong> Тренировка на максимальную силу в базовых упражнениях.</p>
          <a href="#" class="btn btn-info">Записаться</a>
        </div>
      </div>
    </div>
    
    <!-- Тренировка 6 -->
    <div class="col-md-4">
      <div class="card training-card">
        <div class="card-header bg-dark text-white">
          <h5 class="card-title">TRX тренировка</h5>
          <span class="badge badge-sport bg-success">Функционал</span>
        </div>
        <div class="card-body">
          <p class="card-text"><strong>Тренер:</strong> Ольга Морозова</p>
          <p class="card-text"><strong>Опыт:</strong> 7 лет</p>
          <p class="card-text"><strong>Цена:</strong> 1300 руб.</p>
          <p class="card-text"><strong>Описание:</strong> Тренировка с собственным весом на петлях TRX.</p>
          <a href="#" class="btn btn-dark">Записаться</a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .training-card {
    transition: transform 0.3s;
    margin-bottom: 20px;
  }
  .training-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }
  .badge-sport {
    font-size: 0.8em;
    margin-right: 5px;
  }
</style>


<!-- Добавьте этот код после секции с тренировками -->
<div class="container mt-5">
  <h2 class="mb-4">Абонементы</h2>
  <div class="row">
    <!-- Абонемент на 3 месяца -->
    <div class="col-md-4">
      <div class="card pricing-card">
        <div class="card-header bg-primary text-white text-center py-3">
          <h3>3 месяца</h3>
          <h4 class="my-0 font-weight-normal">Стандарт</h4>
        </div>
        <div class="card-body text-center">
          <h2 class="card-title pricing-card-title">12 000 ₽</h2>
          <ul class="list-unstyled mt-3 mb-4">
            <li>Доступ ко всем тренажерам</li>
            <li>3 групповые тренировки</li>
            <li>1 персональная тренировка</li>
            <li>Фитнес-тестирование</li>
          </ul>
          <button type="button" class="btn btn-lg btn-block btn-primary">Выбрать</button>
        </div>
      </div>
    </div>
    
    <!-- Абонемент на 6 месяцев -->
    <div class="col-md-4">
      <div class="card pricing-card">
        <div class="card-header bg-success text-white text-center py-3">
          <h3>6 месяцев</h3>
          <h4 class="my-0 font-weight-normal">Оптима</h4>
        </div>
        <div class="card-body text-center">
          <h2 class="card-title pricing-card-title">20 000 ₽</h2>
          <ul class="list-unstyled mt-3 mb-4">
            <li>Доступ ко всем тренажерам</li>
            <li>8 групповых тренировок</li>
            <li>3 персональные тренировки</li>
            <li>Фитнес-тестирование 2 раза</li>
            <li>Скидка 10% на питание</li>
          </ul>
          <button type="button" class="btn btn-lg btn-block btn-success">Выбрать</button>
        </div>
      </div>
    </div>
    
    <!-- Абонемент на 1 год -->
    <div class="col-md-4">
      <div class="card pricing-card">
        <div class="card-header bg-warning text-dark text-center py-3">
          <h3>1 год</h3>
          <h4 class="my-0 font-weight-normal">Премиум</h4>
        </div>
        <div class="card-body text-center">
          <h2 class="card-title pricing-card-title">35 000 ₽</h2>
          <ul class="list-unstyled mt-3 mb-4">
            <li>Доступ ко всем тренажерам</li>
            <li>20 групповых тренировок</li>
            <li>6 персональных тренировок</li>
            <li>Фитнес-тестирование 4 раза</li>
            <li>Скидка 15% на питание</li>
            <li>Бесплатная сауна 1 раз в месяц</li>
          </ul>
          <button type="button" class="btn btn-lg btn-block btn-warning">Выбрать</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Добавьте эти стили в ваш файл css/style.css -->
<style>
  .pricing-card {
    transition: all 0.3s;
    border: none;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
  }
  
  .pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  }
  
  .pricing-card .card-header {
    border-bottom: none;
  }
  
  .pricing-card-title {
    font-size: 2.5rem;
    margin: 15px 0;
  }
  
  .pricing-card ul li {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
    margin: 0 20px;
  }
  
  .pricing-card .btn-block {
    width: 80%;
    margin: 0 auto;
    border-radius: 50px;
    font-weight: bold;
  }
</style>


<!-- Форма обратной связи -->
<div class="container mt-5 mb-5">
  <div class="card">
    <div class="card-header bg-info text-white">
      <h3>Обратная связь</h3>
      <p class="mb-0">Отправьте ваше сообщение руководству фитнес-зала</p>
    </div>
    <div class="card-body">
      <form id="feedbackForm">
        <!-- Тип сообщения -->
        <div class="mb-3">
          <label for="messageType" class="form-label">Тип сообщения</label>
          <select class="form-select" id="messageType" required>
            <option value="" selected disabled>Выберите тип сообщения</option>
            <option value="complaint">Жалоба</option>
            <option value="suggestion">Предложение</option>
            <option value="thanks">Благодарность</option>
            <option value="question">Вопрос</option>
            <option value="other">Другое</option>
          </select>
        </div>
        
        <!-- Тема сообщения -->
        <div class="mb-3">
          <label for="subject" class="form-label">Тема</label>
          <input type="text" class="form-control" id="subject" placeholder="Кратко опишите суть обращения" required>
        </div>
        
        <!-- Текст сообщения -->
        <div class="mb-3">
          <label for="message" class="form-label">Подробности</label>
          <textarea class="form-control" id="message" rows="5" placeholder="Опишите ваше обращение подробно..." required></textarea>
        </div>
        
        <!-- Контактные данные -->
        <div class="mb-3">
          <label for="contact" class="form-label">Контакт для ответа (email или телефон)</label>
          <input type="text" class="form-control" id="contact" placeholder="Как с вами связаться?" required>
        </div>
        
        <!-- Прикрепление файлов -->
        <div class="mb-3">
          <label for="attachment" class="form-label">Прикрепить файл (необязательно)</label>
          <input class="form-control" type="file" id="attachment">
          <div class="form-text">Максимальный размер файла: 5MB</div>
        </div>
        
        <!-- Кнопки отправки -->
        <div class="d-flex justify-content-between">
          <button type="reset" class="btn btn-outline-secondary">Очистить форму</button>
          <button type="submit" class="btn btn-primary">Отправить сообщение</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('feedbackForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Собираем данные формы
  const formData = {
    type: document.getElementById('messageType').value,
    subject: document.getElementById('subject').value,
    message: document.getElementById('message').value,
    contact: document.getElementById('contact').value,
    attachment: document.getElementById('attachment').files[0]?.name || 'Нет'
  };
  
  // Здесь должна быть логика отправки на сервер
  console.log('Отправка данных:', formData);
  
  // Временное уведомление об успешной отправке
  alert('Ваше сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.');
  this.reset();
});
</script>

<!-- Стили для формы (добавьте в css/style.css) -->
<style>
#feedbackForm {
  max-width: 800px;
  margin: 0 auto;
}

.form-select, .form-control {
  border-radius: 0.25rem;
}

.btn-outline-secondary {
  margin-right: 10px;
}
</style>


  <!-- Место для динамического контента -->
  <div id="dashboard-content" class="mt-4">
    <!-- Здесь будет содержимое личного кабинета -->
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Пример: можно заменить на ajax-запрос к PHP
  const currentCount = 42;
  const maxCount = 100;

  const percent = Math.round((currentCount / maxCount) * 100);
  document.getElementById('people-count').textContent = currentCount;
  const progressBar = document.getElementById('load-progress');
  progressBar.style.width = percent + '%';
  progressBar.setAttribute('aria-valuenow', currentCount);
  progressBar.textContent = percent + '%';

  if (percent > 80) {
    progressBar.classList.remove('bg-success');
    progressBar.classList.add('bg-danger');
  } else if (percent > 50) {
    progressBar.classList.remove('bg-success');
    progressBar.classList.add('bg-warning');
  }
</script>

<script>
// Функция для получения загруженности (например, с сервера или мок-данные)
function fetchGymLoad() {
    // Пример использования случайных данных. В боевом режиме здесь должен быть fetch с вашего API.
    const currentPeople = Math.floor(Math.random() * 101); // от 0 до 100
    const maxCapacity = 100;

    const percentage = Math.round((currentPeople / maxCapacity) * 100);

    // Обновляем DOM
    document.getElementById("people-count").innerText = currentPeople;
    const progressBar = document.getElementById("load-progress");
    progressBar.style.width = percentage + "%";
    progressBar.setAttribute("aria-valuenow", percentage);
    progressBar.innerText = percentage + "%";

    // Цвет в зависимости от загруженности
    progressBar.classList.remove("bg-success", "bg-warning", "bg-danger");

    if (percentage < 50) {
        progressBar.classList.add("bg-success");
    } else if (percentage < 80) {
        progressBar.classList.add("bg-warning");
    } else {
        progressBar.classList.add("bg-danger");
    }
}

// Запускаем обновление при загрузке и каждые 30 секунд
document.addEventListener("DOMContentLoaded", function () {
    fetchGymLoad();
    setInterval(fetchGymLoad, 30000); // обновление каждые 30 секунд
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('php/get_subscriptions.php')
        .then(res => res.json())
        .then(data => {
            const container = document.querySelector('.row');
            container.innerHTML = '';

            if (data.error) {
                container.innerHTML = '<p class="text-danger">Ошибка загрузки данных</p>';
                return;
            }

            data.forEach(sub => {
                const card = `
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5>${sub.name}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Срок действия:</strong> ${sub.start_date} - ${sub.end_date}</p>
                            <p><strong>Осталось дней:</strong> <span class="badge bg-primary">${sub.days_left}</span></p>
                            <p><strong>Осталось тренировок:</strong> 
                                <span class="badge bg-primary">${sub.remaining_trainings} из ${sub.total_trainings}</span>
                            </p>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-success" role="progressbar" style="width: ${sub.percent_used}%;" 
                                    aria-valuenow="${sub.percent_used}" aria-valuemin="0" aria-valuemax="100">
                                    ${sub.percent_used}% использовано
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                container.innerHTML += card;
            });
        })
        .catch(err => console.error('Ошибка:', err));
});
</script>


</body>
</html>
