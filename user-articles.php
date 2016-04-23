<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once __DIR__ . '/includes/session.php'; ?>
    <?php include_once __DIR__ . '/includes/db.php'; ?>
    <?php include_once __DIR__ . '/includes/head.php'; ?>

    <?php
        if (!isset($_GET['id']))
            die("No author ID was specified.");

        $authorId = (integer) $_GET['id'];
        $author = $db->query('
            SELECT *
            FROM user
            WHERE id = ?
        ', [
            $authorId,
        ])->fetch();

        $articles = $db->query('
            SELECT id, title, preamble, image_name
            FROM article_page
            WHERE author_id = ?
        ', [
            $authorId,
        ])->fetchAll();
    ?>

    <title>Articles by <?= $author->name ?> &middot; News</title>
</head>
<body>
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <section class="section">
        <div class="container">
            <h1 class="articles-by">Articles by <?= $author->name ?></h1>

            <?php foreach ($articles as $article): ?>
                <article class="news-item columns">
                    <div class="column is-4">
                        <img class="image" src="/public/images/<?= safe($article->image_name) ?>">
                    </div>
                    <div class="column is-8 content">
                        <h1>
                            <a href="/article.php?id=<?= $article->id ?>">
                                <?= safe($article->title) ?>
                            </a>
                        </h1>
                        <p><?= safe($article->preamble) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if (count($articles) === 0): ?>
                <h3 class="user-no-articles"><?= Auth::user()->name ?> has no articles</h3>
            <?php endif; ?>
        </div>
    </section>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
<?= ob_get_clean() ?>