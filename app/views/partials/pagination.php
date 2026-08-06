<?php if (($pagination['pages'] ?? 1) > 1): ?>
    <?php
    $query = $_GET;
    $current = (int) $pagination['page'];
    $pages = (int) $pagination['pages'];
    ?>
    <nav aria-label="PaginaciÃ³n">
        <ul class="pagination justify-content-end mb-0">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php $query['page'] = $i; ?>
                <li class="page-item <?= $i === $current ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= e(http_build_query($query)) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
