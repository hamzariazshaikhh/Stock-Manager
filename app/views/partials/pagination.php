<?php
$queryParameters = $_GET;

$previousParameters = $queryParameters;
$previousParameters['page'] = $currentPage - 1;

$nextParameters = $queryParameters;
$nextParameters['page'] = $currentPage + 1;
?>

<?php if ($pageCount > 1): ?>
    <nav class="pagination" aria-label="Pagination">
        <?php if ($currentPage > 1): ?>
            <a
                class="button button-small"
                href="?<?= e(http_build_query($previousParameters)) ?>"
            >
                Previous
            </a>
        <?php endif; ?>

        <span class="pagination-label">
            Page <?= e($currentPage) ?>
            of <?= e($pageCount) ?>
        </span>

        <?php if ($currentPage < $pageCount): ?>
            <a
                class="button button-small"
                href="?<?= e(http_build_query($nextParameters)) ?>"
            >
                Next
            </a>
        <?php endif; ?>
    </nav>
<?php endif; ?>