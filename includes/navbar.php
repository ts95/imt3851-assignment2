<section class="hero">
    <div class="hero-header">
        <header class="header">
            <div class="container">
                <div class="header-left">
                    <span class="header-item">
                        <a href="/">News</a>
                    </span>
                    <span class="header-item">
                        <div class="tabs is-toggle">
                            <ul>
                                <?php $categoryId = isset($categoryId) ? $categoryId : NULL; ?>
                                <?php $categories = $db->query('SELECT * FROM category')->fetchAll(); ?>
                                <?php foreach ($categories as $category): ?>
                                    <li <?= $category->id == $categoryId ? 'class="is-active"' : ''; ?>>
                                        <a href="/?category=<?= $category->id ?>">
                                            <?= $category->name ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </span>
                </div>
                <span class="header-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <div class="header-right header-menu">
                <?php if (Auth::check()): ?>
                    <span class="header-item">
                        <a href="/new-article.php">New article</a>
                    </span>
                    <span class="header-item">
                        <a href="/user-articles.php?id=<?= Auth::user()->id ?>">
                            <?= Auth::user()->name ?>
                        </a>
                    </span>
                    <span class="header-item">
                        <a href="/auth/logout.php">Log out</a>
                    </span>
                <?php else: ?>
                    <span class="header-item">
                        <a href="/auth/login.php">Login</a>
                    </span>
                    <span class="header-item">
                        <a href="/auth/register.php">Register</a>
                    </span>
                <?php endif; ?>
                </div>
            </div>
        </header>
    </div>
</section>