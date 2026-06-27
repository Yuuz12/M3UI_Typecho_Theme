<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<?php 
// 预先确定当前激活的导航项并获取所有页面
$currentValue = 'home';

// 获取所有页面用于显示和当前页面检测
$allPages = $this->widget('Widget_Contents_Page_List');

// 首先遍历所有页面，收集符合条件的页面并检测当前页面
$filteredPages = array();
while($allPages->next()) {
    // 检查是否符合显示条件
    if (strtolower($allPages->slug) == 'links' or strtolower($allPages->slug) == 'about' or $allPages->fields->headerDisplay == 1) {
        $filteredPages[] = array(
            'slug' => $allPages->slug,
            'title' => $allPages->title,
            'permalink' => $allPages->permalink,
            'icon' => $allPages->fields->navIcon ?: 'pages--outlined'
        );
    }
}

// 检测当前是哪种页面类型，按优先级设置当前值
if ($this->is('index')) {
    $currentValue = 'home';
} elseif ($this->is('page')) {
    // 对于页面，需要检查是哪个具体页面
    $tempPages = $this->widget('Widget_Contents_Page_List');
    while($tempPages->next()) {
        if ($this->is('page', $tempPages->slug)) {
            $currentValue = $tempPages->slug;
            break;
        }
    }
}
?>

<mdui-navigation-rail divider value="<?php echo $currentValue; ?>" alignment="center">
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
</mdui-top-app-bar>

<div id="main-index">
    <div class="main-index-title">
        <h2>404 - <?php _e('页面未找到'); ?></h2>
    </div>
    
    <div class="collect">
        <div class="error-page">
            <mdui-card variant="outlined" class="error-card">
                <div class="error-content">
                    <mdui-icon name="error_outline" class="error-icon"></mdui-icon>
                    <h3 class="error-title"><?php _e('页面未找到'); ?></h3>
                    <p class="error-desc"><?php _e('抱歉，您访问的页面不存在或已被移除。'); ?></p>
                    <div class="error-actions">
                        <mdui-button href="<?php $this->options->siteUrl(); ?>" variant="tonal"><?php _e('返回首页'); ?></mdui-button>
                        <mdui-button onclick="window.history.back();" variant="filled"><?php _e('返回上页'); ?></mdui-button>
                    </div>
                    <div class="error-search">
                        <h4><?php _e('或者尝试搜索:'); ?></h4>
                        <form method="post">
                            <mdui-text-field name="s" placeholder="<?php _e('输入搜索关键词...'); ?>" class="search-input">
                                <mdui-icon slot="start" name="search"></mdui-icon>
                            </mdui-text-field>
                            <mdui-button type="submit" variant="filled"><?php _e('搜索'); ?></mdui-button>
                        </form>
                    </div>
                </div>
            </mdui-card>
        </div>
    </div>
</div>
<?php $this->need('footer.php'); ?>
