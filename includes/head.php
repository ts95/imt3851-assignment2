<?php include_once __DIR__ . '/settings.php'; ?>
<?php
    date_default_timezone_set('UTC');

    // Utility functions

    /**
     * Should be called on text that's echoed from the
     * database since it might contain unexpected HTML.
     */
    function safe($text) {
        return htmlspecialchars($text);
    }

    /**
     * Basically just print_r() with some styling.
     */
    function dd($array) {
        echo '<pre style="padding: 10px;">';
        print_r($array);
        echo '</pre>';
    }
?>

<!-- Meta -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta name="viewport" content="width=device-width,initial-scale=1">

<!-- Miscellaneous -->
<base href="<?= ROOT_PATH ?>">

<!-- Styles -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/public/css/bulma.min.css">
<link rel="stylesheet" href="/public/css/main.css">

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-beta1/jquery.min.js"></script>
<script src="/public/js/form.js"></script>
<script src="/public/js/main.js"></script>