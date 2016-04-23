<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once __DIR__ . '/includes/session.php'; ?>
    <?php include_once __DIR__ . '/includes/db.php'; ?>
    <?php include_once __DIR__ . '/includes/head.php'; ?>

    <title>News</title>

    <?php
        $categoryId = isset($_GET['category']) ? $_GET['category'] : 1;

        $articles = $db->query('
            SELECT id, title, preamble, image_name
            FROM article_page
            WHERE category_id = ?
        ', [
            $categoryId,
        ])->fetchAll();
    ?>
</head>
<body>
    <?php include_once __DIR__ . '/includes/navbar.php'; ?>

    <section class="section">
        <div class="container">
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
                <h1 class="no-articles">No articles</h1>
            <?php endif; ?>
        </div>
    </section>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
<?= ob_get_clean() ?>