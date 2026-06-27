<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html class="mdui-theme-auto">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php $this->archiveTitle([
            'category' => _t('分类 %s 下的文章'),
            'search'   => _t('包含关键字 %s 的文章'),
            'tag'      => _t('标签 %s 下的文章'),
            'author'   => _t('%s 发布的文章')
        ], '', ' - '); ?><?php $this->options->title(); ?></title>

    <!-- 自有CSS -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('res/style.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('res/mdui@2.1.4/mdui.css'); ?>">
    <script src="<?php $this->options->themeUrl('res/mdui@2.1.4/mdui.global.js'); ?>"></script>
    <!-- Material Icons 字体 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('res/material-icons.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('res/material-icons-outlined.css'); ?>">

    <!-- PJAX库 -->
    <script src="<?php $this->options->themeUrl('res/pjax.min.js'); ?>"></script>

    <!-- 通过自有函数输出HTML头部信息 -->
    <?php $this->header(); ?>
</head>
<body>

<?php 
// 预先确定当前激活的导航项并获取所有页面
$currentValue = 'home';
$allPages = $this->widget('Widget_Contents_Page_List');
$filteredPages = array();
while($allPages->next()) {
    if (strtolower($allPages->slug) == 'links' or strtolower($allPages->slug) == 'about' or $allPages->fields->headerDisplay == 1) {
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
}
?>

<!-- 导航栏 - 在PJAX容器外部，不随页面切换重载 -->
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
    <mdui-top-app-bar-title><?php $this->options->title(); ?></mdui-top-app-bar-title>
    <div style="flex-grow: 1"></div>
    <mdui-button-icon icon="more_vert"></mdui-button-icon>
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
</mdui-navigation-drawer>

<!-- PJAX主容器 -->
<div id="pjax-container">

<?php if (!$this->is('post')): ?>
<header id="header">
    <div class="container">
        <div class="row">
            <mdui-card variant="elevated" clickable id="header-card">
                <?php $headerImageUrl = trim((string)($this->options->headerImage ?? '')); ?>
                <?php if (!empty($headerImageUrl)): ?>
                <img class="img-invert" src="<?php echo htmlspecialchars($headerImageUrl); ?>"></img>
                <?php endif; ?>
                <div class="header-content">
                    <h1 class="img-title"><?php $this->options->title(); ?></h1>
                    <h2 class="img-description"><?php $this->options->description(); ?></h2>
                </div>
            </mdui-card>
        </div><!-- end .row -->
    </div>
</header><!-- end #header -->
<?php endif; ?>

<!-- 代码高亮JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

<!-- 自定义配色方案脚本 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 获取主题设置的强调色
    const accentColor = '<?php echo $this->options->accentColor ?: "#6200ee"; ?>';

    // 检查MDUI库是否已加载
    if (typeof mdui !== 'undefined') {
        // 应用强调色为主色调
        if (accentColor && accentColor !== '#6200ee') {
            try {
                // 使用MDUI的setColorScheme方法设置主色调
                mdui.setColorScheme(accentColor, 'primary');
                
                // 同时更新相关CSS变量作为备用方案
                document.documentElement.style.setProperty('--mdui-color-primary', hexToRgb(accentColor));
            } catch (e) {
                console.warn('Failed to set color scheme:', e);
                // 如果setColorScheme失败，尝试直接设置CSS变量
                document.documentElement.style.setProperty('--mdui-color-primary', hexToRgb(accentColor));
            }
        }
    }

    // 为所有代码块添加语言标识
    const pres = document.querySelectorAll('pre[class*="language-"]');
    pres.forEach(function(pre) {
        // 提取语言名称
        const classes = pre.className.split(' ');
        const languageClass = classes.find(cls => cls.startsWith('language-'));
        if (languageClass) {
            const languageName = languageClass.replace('language-', '').toUpperCase();
            pre.setAttribute('data-language', languageName);
        }
    });
});

// 辅助函数：将十六进制颜色转换为RGB值
function hexToRgb(hex) {
    // 移除 # 符号
    hex = hex.replace('#', '');
    
    // 解析十六进制值
    const bigint = parseInt(hex, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    
    return `${r} ${g} ${b}`;
}
</script>