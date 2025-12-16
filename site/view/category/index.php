<?php require './layout/header.php' ?>

<h1>Danh mục</h1>
<div class="list-group">
    <?php if (empty($categories)): ?>
        <div class="alert alert-info">Chưa có danh mục.</div>
    <?php else:
        foreach ($categories as $cat): ?>
             <a href="index.php?c=document&a=index&category_id=<?= $cat->getId() ?>"
                class="list-group-item list-group-item-action">
                <?= htmlspecialchars($cat->getName()) ?>
                <small class="text-muted">(<?= htmlspecialchars($cat->getCode()) ?>)</small>
                <div class="small"><?= htmlspecialchars($cat->getDescription()) ?></div>
            </a>
        <?php endforeach; endif; ?>
</div>

<?php require './layout/footer.php' ?>