<?php
/**
 * Reusable Card Component
 *
 * Usage:
 * Component::card([
 *     'title' => 'Card Title',
 *     'subtitle' => 'Optional subtitle',
 *     'icon' => 'fa-users',
 *     'content' => 'Card content here',
 *     'footer' => 'Optional footer',
 *     'type' => 'info' // primary, success, warning, danger, info
 * ]);
 */

class CardComponent {
    public static function render($options = []) {
        $title = $options['title'] ?? '';
        $subtitle = $options['subtitle'] ?? '';
        $icon = $options['icon'] ?? 'fa-info-circle';
        $content = $options['content'] ?? '';
        $footer = $options['footer'] ?? '';
        $type = $options['type'] ?? 'default';
        $collapsible = $options['collapsible'] ?? false;
        $collapsed = $options['collapsed'] ?? false;

        ob_start();
        ?>
        <div class="box box-<?= $type ?><?= $collapsed ? ' collapsed-box' : '' ?>">
            <?php if ($title): ?>
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?php if ($icon): ?>
                        <i class="fa <?= $icon ?>"></i>
                    <?php endif; ?>
                    <?= $title ?>
                </h3>
                <?php if ($subtitle): ?>
                    <p class="box-subtitle"><?= $subtitle ?></p>
                <?php endif; ?>
                <?php if ($collapsible): ?>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-<?= $collapsed ? 'plus' : 'minus' ?>"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="box-body">
                <?= $content ?>
            </div>
            <?php if ($footer): ?>
            <div class="box-footer">
                <?= $footer ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Alias untuk kemudahan
class Component {
    public static function card($options) {
        return CardComponent::render($options);
    }
}
