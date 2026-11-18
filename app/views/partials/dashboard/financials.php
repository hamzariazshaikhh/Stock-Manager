<?php if (hasRole(ROLE_OWNER, ROLE_ADMIN)): ?>

    <div class="stat-card">
        <span class="stat-label">Inventory Value</span>
        <strong class="stat-value">PKR <?= e($summary['inventory_value']) ?></strong>
    </div>
<?php endif; ?>
