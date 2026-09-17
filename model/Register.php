<?php
class Register {
    // Process new user registration
    public static function registerUser() {
        $result = ['result' => false, 'message' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? ($_POST['name'] ?? ''));
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['passwordConfirm'] ?? ($_POST['confirm'] ?? ($_POST['password2'] ?? ''));

            // 1. Required fields check
            if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
                $result['message'] = 'Please fill in all required form fields.';
                return $result;
            }

            // 2. Email syntax validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['message'] = 'Invalid email address format.';
                return $result;
            }

            // 3. Password match confirmation
            if ($password !== $passwordConfirm) {
                $result['message'] = 'Entered passwords do not match.';
                return $result;
            }

            // 4. Password minimum length check
            if (mb_strlen($password, 'UTF-8') < 6) {
                $result['message'] = 'Password must contain at least 6 characters.';
                return $result;
            }

            // 5. Unique email check
            $db = new db();
            $checkUser = $db->getOne("SELECT account_id AS id FROM accounts WHERE email_address = :email LIMIT 1", ['email' => $email]);
            if ($checkUser) {
                $result['message'] = 'A user with this email address is already registered.';
                return $result;
            }

            // 6. Secure password hashing and database insertion
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO accounts (full_name, email_address, pass_hash, access_role, created_timestamp, plain_backup) 
                      VALUES (:username, :email, :password, 'user', NOW(), :pass)";
            
            $insert = $db->execute($query, [
                'username' => $username,
                'email'    => $email,
                'password' => $hash,
                'pass'     => $password
            ]);

            if ($insert) {
                $result['result'] = true;
                $result['message'] = 'Congratulations! You have successfully registered.';
            } else {
                $result['message'] = 'An error occurred while saving data to the database.';
            }
        } else {
            $result['message'] = 'Invalid form submission method.';
        }

        return $result;
    }
}
?>
