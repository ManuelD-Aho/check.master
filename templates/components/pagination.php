<?php if (isset($currentPage) && isset($totalPages) && $totalPages > 1): ?>
    <nav class="pagination" aria-label="Pagination">
        <ul class="pagination-list">
            <?php if ($currentPage > 1): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($baseUrl); ?>?page=1" class="pagination-link" aria-label="Première page">
                        &laquo; Première
                    </a>
                </li>
                <li>
                    <a href="<?php echo htmlspecialchars($baseUrl); ?>?page=<?php echo $currentPage - 1; ?>" class="pagination-link" aria-label="Page précédente">
                        &lsaquo; Précédent
                    </a>
                </li>
            <?php endif; ?>

            <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                if ($startPage > 1): ?>
                    <li><span class="pagination-ellipsis">...</span></li>
                <?php endif;
                
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li>
                        <?php if ($i === $currentPage): ?>
                            <span class="pagination-link pagination-current" aria-current="page">
                                <?php echo $i; ?>
                            </span>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($baseUrl); ?>?page=<?php echo $i; ?>" class="pagination-link">
                                <?php echo $i; ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endfor;
                
                if ($endPage < $totalPages): ?>
                    <li><span class="pagination-ellipsis">...</span></li>
                <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($baseUrl); ?>?page=<?php echo $currentPage + 1; ?>" class="pagination-link" aria-label="Page suivante">
                        Suivant &rsaquo;
                    </a>
                </li>
                <li>
                    <a href="<?php echo htmlspecialchars($baseUrl); ?>?page=<?php echo $totalPages; ?>" class="pagination-link" aria-label="Dernière page">
                        Dernière &raquo;
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
