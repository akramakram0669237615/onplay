<?php

$page = 'الرئيسية';

require __DIR__ . '/includes/header.php';

$types = [
    'banners'       => 'البنرات',
    'categories'    => 'التصنيفات',
    'channels'      => 'القنوات',
    'matches'       => 'المباريات',
    'movies'        => 'الأفلام',
    'series'        => 'المسلسلات',
    'notifications' => 'الإشعارات'
];

?>

<h1>مرحباً في ONPLAY</h1>

<p class="muted">
    تحكم بالمحتوى والحسابات وواجهة التطبيق من مكان واحد.
</p>

<div class="stats">

    <?php foreach ($types as $key => $name): ?>

        <?php
        $items = db_read($key);

        if (!is_array($items)) {
            $items = [];
        }
        ?>

        <a
            class="stat"
            href="content.php?type=<?= urlencode($key) ?>"
        >

            <span>
                <?= e($name) ?>
            </span>

            <b>
                <?= count($items) ?>
            </b>

        </a>

    <?php endforeach; ?>

</div>


<div class="panel">

    <h3>روابط API</h3>

    <code>/api/v1/home</code>

    <code>/api/v1/channels</code>

    <code>/api/v1/matches</code>

    <code>/api/v1/app/config</code>

</div>


<?php require __DIR__ . '/includes/footer.php'; ?>