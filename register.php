<?php
$pageTitle = "Регистрация";
include 'includes/header.php';

// Если пользователь уже авторизован, перенаправляем
if (isLoggedIn()) {
    redirect('account.php');
}

// Обработка формы регистрации
$error = '';
$formSubmitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $formSubmitted = true;
    $fullname = sanitize($_POST['fullname']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $terms = isset($_POST['terms']) ? true : false;
    
    // Валидация
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif (!$terms) {
        $error = 'Необходимо принять условия использования';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } else {
        // Разделяем полное имя на имя и фамилию
        $name_parts = explode(' ', $fullname, 2);
        $first_name = $name_parts[0];
        $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
        
        $userData = [
            'email' => $email,
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => ''
        ];
        
        $result = register($userData);
        if ($result['success']) {
            // Успешная регистрация - редирект
            echo '<script>setTimeout(function() { window.location.href = "account.php"; }, 100);</script>';
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>

<main class="auth-main">
    <div class="auth-container">
        <div class="auth-form-section">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h1>Создать аккаунт</h1>
                    <p>Заполните форму для регистрации</p>
                </div>

                <?php if ($formSubmitted && $error): ?>
                <div class="alert error">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form class="auth-form" method="POST">
                    <div class="form-group <?php echo ($formSubmitted && $error && empty($_POST['fullname'])) ? 'error' : ''; ?>">
                        <label for="fullname">Полное имя</label>
                        <input
                            type="text"
                            id="fullname"
                            name="fullname"
                            placeholder="Иван Иванов"
                            value="<?php echo isset($_POST['fullname']) ? $_POST['fullname'] : ''; ?>"
                            required
                            class="<?php echo ($formSubmitted && $error && empty($_POST['fullname'])) ? 'error-field' : ''; ?>"
                        />
                    </div>

                    <div class="form-group <?php echo ($formSubmitted && $error && empty($_POST['email'])) ? 'error' : ''; ?>">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="your@email.com"
                            value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>"
                            required
                            class="<?php echo ($formSubmitted && $error && empty($_POST['email'])) ? 'error-field' : ''; ?>"
                        />
                    </div>

                    <div class="form-group <?php echo ($formSubmitted && $error && empty($_POST['password'])) ? 'error' : ''; ?>">
                        <label for="password">Пароль</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Создайте надежный пароль"
                            required
                            class="<?php echo ($formSubmitted && $error && empty($_POST['password'])) ? 'error-field' : ''; ?>"
                        />
                        <small>Минимум 6 символов</small>
                    </div>

                    <div class="form-group <?php echo ($formSubmitted && $error && empty($_POST['confirm-password'])) ? 'error' : ''; ?>">
                        <label for="confirm-password">Подтвердите пароль</label>
                        <input
                            type="password"
                            id="confirm-password"
                            name="confirm-password"
                            placeholder="Повторите ваш пароль"
                            required
                            class="<?php echo ($formSubmitted && $error && empty($_POST['confirm-password'])) ? 'error-field' : ''; ?>"
                        />
                    </div>

                    <div class="form-options <?php echo ($formSubmitted && $error && !isset($_POST['terms'])) ? 'error' : ''; ?>">
                        <label class="checkbox-label">
                            <input 
                                type="checkbox" 
                                name="terms" 
                                <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>
                                required 
                            />
                            <span class="checkmark"></span>
                            Я принимаю <a href="terms.php">условия использования</a> и
                            <a href="privacy.php">политику конфиденциальности</a>
                        </label>
                    </div>

                    <button type="submit" name="register" class="auth-submit-btn dark--btn">
                        Создать аккаунт
                    </button>
                </form>

                <div class="auth-divider">
                    <span>Или зарегистрируйтесь с помощью</span>
                </div>

                <div class="social-auth">
                    <button type="button" class="social-btn google-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24">
                            <path
                                fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            />
                            <path
                                fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            />
                            <path
                                fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            />
                            <path
                                fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            />
                        </svg>
                        Google
                    </button>
                </div>

                <div class="auth-switch">
                    <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Обработка формы с выделением ошибок только после отправки
document.querySelector(".auth-form").addEventListener("submit", function (e) {
    const fullname = document.getElementById("fullname");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");
    const terms = document.querySelector('input[name="terms"]');
    let hasError = false;
    
    // Проверка полей
    if (!fullname.value.trim()) {
        hasError = true;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim() || !emailRegex.test(email.value)) {
        hasError = true;
    }
    
    if (!password.value.trim() || password.value.length < 6) {
        hasError = true;
    }
    
    if (!confirmPassword.value.trim() || password.value !== confirmPassword.value) {
        hasError = true;
    }
    
    if (!terms.checked) {
        hasError = true;
    }
    
    if (hasError) {
        e.preventDefault();
        // Сообщение и подсветка будут обработаны сервером после перезагрузки
        return false;
    }
    
    return true;
});

// Сброс стилей ошибок при вводе
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        // Просто даем пользователю вводить, стили сбросятся при следующей отправке
    });
});

// Для чекбокса
document.querySelector('input[name="terms"]').addEventListener('change', function() {
    // Сброс стилей при изменении чекбокса
});
</script>

<?php include 'includes/footer.php'; ?>