<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php
// 计算当前激活的导航项和过滤页面列表
$currentValue = 'home';
$allPages = $this->widget('Widget_Contents_Page_List');
$filteredPages = array();

while($allPages->next()) {
    if ($allPages->fields->headerDisplay == 1) {
        $filteredPages[] = array(
            'slug' => $allPages->slug,
            'title' => $allPages->title,
            'permalink' => $allPages->permalink,
            'icon' => $allPages->fields->navIcon ?: 'pages--outlined'
        );
    }
}

if ($this->is('index')) {
    $currentValue = 'home';
} elseif ($this->is('page')) {
    $tempPages = $this->widget('Widget_Contents_Page_List');
    while($tempPages->next()) {
        if ($this->is('page', $tempPages->slug)) {
            $currentValue = $tempPages->slug;
            break;
        }
    }
} elseif ($this->is('category')) {
    $currentValue = 'category_' . $this->getArchiveSlug();
} elseif ($this->is('archive')) {
    $currentValue = 'archive';
}
?>

<!-- 导航栏 - 在PJAX容器外部，不随页面切换重载 -->
<mdui-navigation-rail divider value="<?php echo $currentValue; ?>" alignment="center">
    <mdui-button-icon icon="search" class="search-trigger" slot="bottom"></mdui-button-icon>
    <mdui-button-icon icon="contrast" class="theme-toggle-rail" slot="bottom"></mdui-button-icon>
    <mdui-navigation-rail-item icon="home--outlined" value="home" href="<?php $this->options->siteUrl(); ?>">首页</mdui-navigation-rail-item>
    <?php if ($this->options->enableIndexPage): ?>
    <mdui-navigation-rail-item icon="article--outlined" value="archive" href="<?php $this->options->siteUrl(); ?>">文章</mdui-navigation-rail-item>
    <?php endif; ?>
    <?php foreach($filteredPages as $page): ?>
    <mdui-navigation-rail-item icon="<?php echo $page['icon']; ?>" value="<?php echo $page['slug']; ?>" href="<?php echo $page['permalink']; ?>"><?php echo $page['title']; ?></mdui-navigation-rail-item>
    <?php endforeach; ?>
</mdui-navigation-rail>

<mdui-top-app-bar>
    <mdui-button-icon class="mswitch" icon="menu"></mdui-button-icon>
    <mdui-top-app-bar-title><?php $this->options->title(); ?></mdui-top-app-bar-title>
    <div style="flex-grow: 1"></div>
    <mdui-button-icon icon="search" class="search-trigger"></mdui-button-icon>
    <!-- 更多操作按钮，等后续用的上时再添加 -->
    <!-- <mdui-button-icon icon="more_vert"></mdui-button-icon> -->
</mdui-top-app-bar>

<mdui-linear-progress id="pjax-progress" style="position:fixed;top:0;left:0;right:0;z-index:9999;display:none;"></mdui-linear-progress>

<mdui-navigation-drawer modal close-on-esc close-on-overlay-click>
    <div style="padding: 25px">
        <mdui-button-icon variant="outlined" class="close" icon="close"></mdui-button-icon>
    </div>
    <mdui-list style="padding-left: 10px;padding-right: 10px">
        <mdui-list-item href="<?php $this->options->siteUrl(); ?>" value="home" <?php echo $currentValue == 'home' ? 'selected' : ''; ?> icon="home--outlined" headline="首页"></mdui-list-item>
        <?php if ($this->options->enableIndexPage): ?>
        <mdui-list-item href="<?php $this->options->siteUrl(); ?>" value="archive" <?php echo $currentValue == 'archive' ? 'selected' : ''; ?> icon="article--outlined" headline="文章"></mdui-list-item>
        <?php endif; ?>
        <?php foreach($filteredPages as $page): ?>
        <mdui-list-item href="<?php echo $page['permalink']; ?>" value="<?php echo $page['slug']; ?>" <?php echo $currentValue == $page['slug'] ? 'selected' : ''; ?> icon="<?php echo $page['icon']; ?>" headline="<?php echo $page['title']; ?>"></mdui-list-item>
        <?php endforeach; ?>
    </mdui-list>
    <div style="padding: 12px 16px;">
        <mdui-segmented-button-group selects="single" full-width id="theme-switch" value="<?php echo $this->options->darkMode ?: 'auto'; ?>">
            <mdui-segmented-button value="light" icon="light_mode">亮色</mdui-segmented-button>
            <mdui-segmented-button value="auto" icon="contrast">跟随系统</mdui-segmented-button>
            <mdui-segmented-button value="dark" icon="dark_mode">暗色</mdui-segmented-button>
        </mdui-segmented-button-group>
    </div>
</mdui-navigation-drawer>

<!-- 全站搜索对话框 -->
<mdui-dialog id="search-dialog" close-on-esc close-on-overlay-click>
    <form method="post" action="<?php $this->options->siteUrl(); ?>" class="search-form" id="search-form">
        <mdui-text-field variant="outlined" name="s" placeholder="输入关键词搜索..." autofocus class="search-input-field" id="search-input"></mdui-text-field>
        <div class="search-actions">
            <mdui-button variant="text" type="button" id="search-cancel">取消</mdui-button>
            <mdui-button variant="filled" type="submit">搜索</mdui-button>
        </div>
    </form>
</mdui-dialog>
