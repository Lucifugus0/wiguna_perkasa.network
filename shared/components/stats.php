<?php
/**
 * Stats Card Component (Small Box)
 *
 * Usage:
 * StatsComponent::render([
 *     'title' => '250',
 *     'subtitle' => 'Total Pelanggan',
 *     'icon' => 'fa-users',
 *     'color' => 'aqua', // aqua, green, yellow, red
 *     'link' => 'pelanggan',
 *     'linkText' => 'More info'
 * ]);
 */

class StatsComponent {
    public static function render($options = []) {
        $title = $options['title'] ?? '0';
        $subtitle = $options['subtitle'] ?? '';
        $icon = $options['icon'] ?? 'fa-info';
        $color = $options['color'] ?? 'aqua';
        $link = $options['link'] ?? '#';
        $linkText = $options['linkText'] ?? 'More info';
        $percentage = $options['percentage'] ?? null;
        $percentageText = $options['percentageText'] ?? '';

        ob_start();
        ?>
        <div class="small-box bg-<?= $color ?>">
            <div class="inner">
                <h3><?= $title ?></h3>
                <p><?= $subtitle ?></p>
                <?php if ($percentage !== null): ?>
                    <p class="stat-percentage">
                        <i class="fa fa-arrow-<?= $percentage >= 0 ? 'up' : 'down' ?>"></i>
                        <?= abs($percentage) ?>% <?= $percentageText ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="icon">
                <i class="fa <?= $icon ?>"></i>
            </div>
            <a href="<?= Router::url($link) ?>" class="small-box-footer">
                <?= $linkText ?> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
}
