<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html class="mdui-theme-<?php echo $this->options->darkMode ?: 'auto'; ?>">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
    // 防闪烁：优先读取 localStorage 中的用户选择
    (function(){
        var saved = localStorage.getItem('m3ui-theme');
        if (saved && (saved === 'light' || saved === 'dark' || saved === 'auto')) {
            document.documentElement.classList.remove('mdui-theme-auto','mdui-theme-light','mdui-theme-dark');
            document.documentElement.classList.add('mdui-theme-' + saved);
        }
    })();
    </script>
    <title><?php $this->archiveTitle([
            'category' => _t('分类 %s 下的文章'),
            'search'   => _t('包含关键字 %s 的文章'),
            'tag'      => _t('标签 %s 下的文章'),
            'author'   => _t('%s 发布的文章')
        ], '', ' - '); ?><?php $this->options->title(); ?></title>

    <!-- SEO Meta Tags -->
    <?php if ($this->is('post') || $this->is('page')): ?>
        <meta name="description" content="<?php echo htmlspecialchars(mb_substr(strip_tags($this->content), 0, 150)); ?>">
        <?php if ($this->tags): ?>
            <meta name="keywords" content="<?php echo htmlspecialchars(implode(',', array_column($this->tags, 'name'))); ?>">
        <?php endif; ?>
        <link rel="canonical" href="<?php $this->permalink(); ?>">
        <!-- Open Graph -->
        <meta property="og:title" content="<?php $this->title(); ?>">
        <meta property="og:description" content="<?php echo htmlspecialchars(mb_substr(strip_tags($this->content), 0, 150)); ?>">
        <meta property="og:url" content="<?php $this->permalink(); ?>">
        <meta property="og:type" content="article">
        <?php $coverImg = getCoverImage($this, $this->options); ?>
        <meta property="og:image" content="<?php echo $coverImg; ?>">
        <meta property="og:site_name" content="<?php $this->options->title(); ?>">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?php $this->title(); ?>">
        <meta name="twitter:image" content="<?php echo $coverImg; ?>">
        <!-- JSON-LD -->
        <script type="application/ld+json">
        <?php echo json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->title(),
            'datePublished' => date('c', $this->created),
            'dateModified' => date('c', $this->modified),
            'author' => ['@type' => 'Person', 'name' => $this->author->screenName],
            'image' => $coverImg,
            'url' => $this->permalink,
            'mainEntityOfPage' => $this->permalink
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
        </script>
    <?php elseif ($this->is('index')): ?>
        <meta name="description" content="<?php $this->options->description(); ?>">
        <link rel="canonical" href="<?php $this->options->siteUrl(); ?>">
        <meta property="og:type" content="website">
        <meta property="og:title" content="<?php $this->options->title(); ?>">
        <meta property="og:description" content="<?php $this->options->description(); ?>">
        <meta property="og:url" content="<?php $this->options->siteUrl(); ?>">
    <?php endif; ?>

    <!-- 预加载关键字体 -->
    <link rel="preload" href="<?php $this->options->themeUrl('res/fonts/AlimamaFangYuanTiVF-Thin-2.woff2'); ?>" as="font" type="font/woff2" crossorigin>

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

<?php $this->need('components/sidebar.php'); ?>

<!-- PJAX主容器 -->
<div id="pjax-container">

<?php if (!$this->is('post')): ?>
<header id="header">
    <div class="container">
        <div class="row">
            <mdui-card variant="elevated" id="header-card">
                <?php $headerImageUrl = trim((string)($this->options->headerImage ?? '')); ?>
                <?php if (!empty($headerImageUrl)): ?>
                <img class="img-invert" src="<?php echo htmlspecialchars($headerImageUrl); ?>" alt="<?php $this->options->title(); ?>">
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