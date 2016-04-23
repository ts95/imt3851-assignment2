<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once __DIR__ . '/includes/session.php'; ?>
    <?php include_once __DIR__ . '/includes/db.php'; ?>
    <?php include_once __DIR__ . '/includes/head.php'; ?>
    <?php
        if (!isset($_GET['id']))
            die("No article ID was specified.");

        $articleId = (integer) $_GET['id'];
        $articleQuery = 'SELECT * FROM article_page WHERE id = ?';
        $article = $db->query($articleQuery, [$articleId])->fetch();

        if (!$article)
            die("This article doesn't exist.");

        $categoryId = $article->category_id;
    ?>

    <title><?= $article->title ?> &middot; News</title>
</head>
<body>
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <section class="section">
        <div class="container content">
            <article class="news-article">
                <img class="image" src="/public/images/<?= safe($article->image_name) ?>">

                <div class="column is-10">
                    <h1><?= safe($article->title) ?></h1>
                    <p><?= safe($article->preamble) ?></p>
                    <p>
                        <a class="button is-primary" href="/user-articles.php?id=<?= $article->author_id ?>">
                            <span class="icon">
                                <i class="fa fa-user"></i>
                            </span>
                            <span><?= safe($article->author) ?></span>
                        </a>
                    </p>
                    <p><?= safe($article->body) ?></p>

                    <?php if (Auth::check() and (Auth::user()->id === $article->author_id or Auth::user()->admin)): ?>
                        <a class="button" href="/edit-article.php?id=<?= $article->id ?>">Edit article</a>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
<?= ob_get_clean() ?>