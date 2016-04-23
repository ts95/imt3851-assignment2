<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once __DIR__ . '/../includes/session.php'; ?>
    <?php include_once __DIR__ . '/../includes/db.php'; ?>
    <?php include_once __DIR__ . '/../includes/head.php'; ?>

    <title>Login &middot; News</title>

    <?php
        require_once __DIR__ . '/../tools/Validator.php';
        require_once __DIR__ . '/../tools/Helper.php';
        require_once __DIR__ . '/../tools/Request.php';

        $validator = new \Tools\Validator($_POST);

        if (\Tools\Request::method('POST')) {
            $user = $db->query('SELECT * FROM user WHERE email = ?', [
                $validator->getParam('email'),
            ])->fetch();

            $userValidator = function($value, $params, $fail) use($user) {
                if (!$user || !password_verify($params['password'], $user->password))
                    $fail("Invalid credentials.");
            };

            $validator->validate('email', $userValidator);
            $validator->validate('password', $userValidator);

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
                    <h1 class="title">Log in</h1>
                </div>
                <hr>
                <form method="POST">
                    <label class="label">E-Mail</label>
                    <p class="control has-icon">
                        <input class="input" type="email" name="email">
                        <i class="fa fa-envelope"></i>
                    </p>

                    <label class="label">Password</label>
                    <p class="control has-icon">
                        <input class="input" type="password" name="password">
                        <i class="fa fa-lock"></i>
                    </p>

                    <p class="control">
                        <input type="submit" class="button is-primary" name="submit" value="Log in">
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