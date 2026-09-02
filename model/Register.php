<?php
class Register {
    // Обработка регистрации нового пользователя
    public static function registerUser() {
        $result = ['result' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['passwordConfirm'] ?? ($_POST['password2'] ?? '');

            // 1. Проверка заполнения полей
            if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
                $result['message'] = 'Пожалуйста, заполните все обязательные поля формы.';
                return $result;
            }

            // 2. Валидация формата email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['message'] = 'Введен некорректный адрес электронной почты.';
                return $result;
            }

            // 3. Проверка совпадения паролей
            if ($password !== $passwordConfirm) {
                $result['message'] = 'Введенные пароли не совпадают.';
                return $result;
            }

            // 4. Проверка длины пароля
            if (mb_strlen($password, 'UTF-8') < 6) {
                $result['message'] = 'Пароль должен содержать не менее 6 символов.';
                return $result;
            }

            // 5. Проверка уникальности email в базе данных
            $db = new db();
            $checkUser = $db->getOne("SELECT id FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
            if ($checkUser) {
                $result['message'] = 'Пользователь с таким E-mail адресом уже зарегистрирован.';
                return $result;
            }

            // 6. Хеширование пароля и добавление в БД со статусом 'user'
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, email, password, status, registration_date, pass) 
                      VALUES (:username, :email, :password, 'user', CURDATE(), :pass)";
            
            $insert = $db->execute($query, [
                'username' => $username,
                'email'    => $email,
                'password' => $hash,
                'pass'     => $password
            ]);

            if ($insert) {
                $result['result'] = true;
                $result['message'] = 'Поздравляем! Вы успешно зарегистрировались в системе.';
            } else {
                $result['message'] = 'Произошла ошибка при сохранении данных в базу данных.';
            }
        } else {
            $result['message'] = 'Неверный метод отправки формы.';
        }

        return $result;
    }
}
?>
