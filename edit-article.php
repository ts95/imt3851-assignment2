<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once __DIR__ . '/includes/session.php'; ?>
    <?php include_once __DIR__ . '/includes/db.php'; ?>
    <?php include_once __DIR__ . '/includes/head.php'; ?>

    <title>New article &middot; News</title>

    <?php
        require_once __DIR__ . '/tools/Validator.php';
        require_once __DIR__ . '/tools/Helper.php';
        require_once __DIR__ . '/tools/Request.php';

        if (!isset($_REQUEST['id']))
            die("No article ID was specified.");

        $articleId = (integer) $_REQUEST['id'];
        $articleQuery = 'SELECT * FROM article_page WHERE id = ?';
        $article = $db->query($articleQuery, [$articleId])->fetch();

        if (!$article)
            die("This article doesn't exist.");

        $categories = $db->query('SELECT * FROM category')->fetchAll();

        $validator = new \Tools\Validator(array_merge($_POST, $_FILES));

        if (\Tools\Request::method('POST')) {
            $validator->validate('title', function($title, $params, $fail) {
                if (mb_strlen($title) < 4)
                    $fail("Minimum title length is 4 characters.");

                if (mb_strlen($title) > 100)
                    $fail("Maximum title length is 100 characters.");
            });

            $validator->validate('image', function($image, $params, $fail) {
                if ($image['size'] > 5000000)
                    $fail("Max image size is 5 MB.");

                $fileErrors = [
                    1 => 'The uploaded image file exceeds the upload_max_filesize directive in php.ini.',
                    2 => 'The uploaded image file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                    3 => 'The uploaded image file was only partially uploaded.',
                    // 4 => 'No image file was uploaded.',
                    6 => 'Missing a temporary folder.',
                    7 => 'Failed to write image file to disk.',
                    8 => 'A PHP extension stopped the file upload.',
                ];

                if (array_key_exists($image['error'], $fileErrors)) {
                    $fail($fileErrors[$image['error']]);
                } else {
                    if ($image['error'] !== 4) {
                        if (!in_array($image['type'], ['image/jpeg', 'image/png']))
                            $fail("Unallowed image type. Please try a jpg or png file.");
                    }
                }
            });

            $validator->validate('category', function($categoryId, $params, $fail) use($db) {
                $category = $db->query('SELECT COUNT(1) AS count FROM category WHERE id = ?', [
                    $categoryId,
                ])->fetch();

                if ($category->count == 0)
                    $fail("Nonexistant category.");
            });

            $validator->validate('preamble', function($preamble, $params, $fail) {
                if (\Tools\Helper::wordCount($preamble) < 2)
                    $fail("Minimum number of words in the preamble is 2.");

                if (\Tools\Helper::wordCount($preamble) > 2000)
                    $fail("Maximum number of words in the preamble is 2000.");

                if (mb_strlen($preamble) > 300)
                    $fail("Maximum preamble length is 300 characters.");
            });

            $validator->validate('body', function($body, $params, $fail) {
                if (\Tools\Helper::wordCount($body) < 20)
                    $fail("Minimum number of words in the body is 20.");

                if (\Tools\Helper::wordCount($body) > 2000)
                    $fail("Maximum number of words in the body is 2000.");

                if (mb_strlen($body) > 10000)
                    $fail("Maximum body length is 10 000 characters.");
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
                    $image = $validator->getParam('image');

                    // No image was uploaded
                    $db->query('
                        UPDATE article
                        SET
                            category_id = :category_id,
                            title = :title,
                            preamble = :preamble,
                            body = :body
                        WHERE id = :article_id
                    ', [
                        ':category_id' => $validator->getParam('category'),
                        ':title' => $validator->getParam('title'),
                        ':preamble' => $validator->getParam('preamble'),
                        ':body' => $validator->getParam('body'),
                        ':article_id' => $articleId,
                    ]);

                    // Image file was uploaded
                    if ($image['error'] !== 4) {
                        $oldImageName = $db->query('
                            SELECT image_name
                            FROM article
                            WHERE id = :article_id
                        ', [
                            ':article_id' => $articleId,
                        ])->fetchColumn();

                        $imageDir = __DIR__ . '/public/images/';

                        // Delete the old image
                        unlink($imageDir . $oldImageName);

                        $imageExt = pathinfo($image['name'], PATHINFO_EXTENSION);
                        $imageName = \Tools\Helper::randomFilename($imageExt);

                        $db->query('
                            UPDATE article
                            SET
                                image_name = :image_name
                            WHERE id = :article_id
                        ', [
                            ':image_name' => $imageName,
                            ':article_id' => $articleId,
                        ]);

                        move_uploaded_file($image['tmp_name'], $imageDir . $imageName);
                    }

                    header("Location: /article.php?id=$articleId");
                }
            }
        }
    ?>
</head>
<body>
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

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
                    <h1 class="title">Update article</h1>
                </div>
                <hr>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $article->id ?>">

                    <label class="label">Title</label>
                    <p class="control has-icon">
                        <input class="input" type="text" name="title" value="<?= $article->title ?>">
                        <i class="fa fa-font"></i>
                    </p>

                    <label class="label">Image (optional)</label>
                    <p class="control">
                        <input class="file" type="file" name="image">
                    </p>

                    <label class="label">Category</label>
                    <p class="control">
                        <span class="select">
                            <select name="category">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category->id ?>"
                                            <?= $category->id === $article->category_id ? 'selected': '' ?>>
                                        <?= $category->name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </span>
                    </p>

                    <label class="label">Preamble</label>
                    <p class="control">
                        <textarea class="textarea" name="preamble"><?= $article->preamble ?></textarea>
                    </p>

                    <label class="label">Body</label>
                    <p class="control">
                        <textarea class="textarea" name="body"><?= $article->body ?></textarea>
                    </p>

                    <p class="control">
                        <input type="submit" class="button is-primary" name="submit" value="Update article">
                    </p>
                </form>
            </div>
        </div>
    </section>

    <script>
    $.form($('form'), document.location.pathname);
    </script>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
<?= ob_get_clean() ?>