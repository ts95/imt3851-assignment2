<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once __DIR__ . '/../includes/session.php'; ?>
    <?php include_once __DIR__ . '/../includes/db.php'; ?>
    <?php include_once __DIR__ . '/../includes/head.php'; ?>

    <title>Register &middot; News</title>

    <?php
        require_once __DIR__ . '/../tools/Validator.php';
        require_once __DIR__ . '/../tools/Helper.php';
        require_once __DIR__ . '/../tools/Request.php';

        $validator = new \Tools\Validator($_POST);

        if (\Tools\Request::method('POST')) {
            $validator->validate('email', function($email, $params, $fail) use($db) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                    $fail("Invalid E-Mail.");

                if (mb_strlen($email) > 30)
                    $fail("Maximum E-Mail length is 30 characters.");

                $user = $db->query('SELECT * FROM user WHERE email = ?', [
                    $email,
                ])->fetch();

                if ($user)
                    $fail("This E-Mail is already in use.");
            });

            $validator->validate('name', function($name, $params, $fail) {
                if (mb_strlen($name) > 30)
                    $fail("Maximum name length is 30 characters.");

                if (mb_strlen($name) < 3)
                    $fail("Minimum name length is 3 characters.");

                if (mb_strlen($name) >= 3 && \Tools\Helper::wordCount($name) < 2)
                    $fail("Only one name detected. Please enter your full name.");
            });

            $validator->validate('password', function($password, $params, $fail) {
                if (!$password)
                    $fail("Password required.");

                if (mb_strlen($password) < 3)
                    $fail("Minimum password length is 3 characters.");
            });

            $validator->validate('repeatPassword', function($repeatPassword, $params, $fail) {
                if (!$repeatPassword)
                    $fail("You must repeat the password.");

                if ($repeatPassword !== $params['password'])
                    $fail("The repeated password must be equal to the first one.");
            });

            if (\Tools\Request::isXHR()) {
                if ($validator->hasErrors()) {
                    \Tools\Request::json([
                        'ok' => false,
                        'errors' => $validator->getErrors(),
                    ]);
                } else {
                    \Tools\Request::json([
                        'ok' => true,
                    ]);
                }
            } else {
                if (!$validator->hasErrors()) {
                    $db->query('INSERT INTO user(email, name, password) VALUES(?, ?, ?)', [
                        $validator->getParam('email'),
                        $validator->getParam('name'),
                        password_hash($validator->getParam('password'), PASSWORD_DEFAULT),
                    ]);

                    $user = $db->query('SELECT * FROM user WHERE email = ?', [
                        $validator->getParam('email'),
                    ])->fetch();

                    Auth::login($user);

                    header('Location: /');
                }
            }
        }
    ?>
</head>
<body>
    <?php include_once __DIR__ . '/../includes/navbar.php'; ?>

    <section class="section">
        <div class="container">
            <div class="column is-half is-offset-3">
                <?php if ($validator->hasErrors()): ?>
                    <div class="notification is-danger content">
                        <button class="delete"></button>
                        <ul>
                        <?php foreach ($validator->getErrors() as $param => $messages): ?>
                            <?php foreach ($messages as $message): ?>
                                <li><?= $message ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="heading">
                    <h1 class="title">Register</h1>
                </div>
                <hr>
                <form method="POST">
                    <label class="label">E-Mail</label>
                    <p class="control has-icon">
                        <input class="input" type="email" name="email">
                        <i class="fa fa-envelope"></i>
                    </p>

                    <label class="label">Full name</label>
                    <p class="control has-icon">
                        <input class="input" type="text" name="name">
                        <i class="fa fa-user"></i>
                    </p>

                    <label class="label">Password</label>
                    <p class="control has-icon">
                        <input class="input" type="password" name="password">
                        <i class="fa fa-lock"></i>
                    </p>

                    <label class="label">Repeat password</label>
                    <p class="control has-icon">
                        <input class="input" type="password" name="repeatPassword">
                        <i class="fa fa-lock"></i>
                    </p>

                    <p class="control">
                        <button class="button is-primary" name="submit">Register</button>
                    </p>
                </form>
            </div>
        </div>
    </section>

    <script>
    $.form($('form'), document.location.pathname);
    </script>

    <?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
<?= ob_get_clean() ?>