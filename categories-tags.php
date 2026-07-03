<?php
/**
 * 分类与标签
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('components/header.php');

// 获取所有分类
$categories = [];
$db = Typecho_Db::get();
try {
    $categoryRows = $db->fetchAll(
        $db->select('mid', 'name', 'slug', 'description', 'count')
           ->from('table.metas')
           ->where('type = ?', 'category')
           ->where('count > ?', 0)
           ->order('count', Typecho_Db::SORT_DESC)
    );
    $siteUrl = $this->options->siteUrl;
    foreach ($categoryRows as $row) {
        $slug = $row['slug'] ?: $row['mid'];
        $categories[] = [
            'name' => $row['name'],
            'permalink' => $siteUrl . 'category/' . $slug,
            'count' => $row['count'],
            'description' => $row['description']
        ];
    }
} catch (Exception $e) {}

// 获取所有标签
$tags = [];
try {
    $tagRows = $db->fetchAll(
        $db->select('mid', 'name', 'slug', 'count')
           ->from('table.metas')
           ->where('type = ?', 'tag')
           ->where('count > ?', 0)
           ->order('count', Typecho_Db::SORT_DESC)
    );
    $siteUrl = $this->options->siteUrl;
    foreach ($tagRows as $row) {
        $slug = $row['slug'] ?: $row['mid'];
        $tags[] = [
            'name' => $row['name'],
            'permalink' => $siteUrl . 'tag/' . $slug,
            'count' => $row['count']
        ];
    }
} catch (Exception $e) {}

$categoryCount = count($categories);
$tagCount = count($tags);
?>

<div id="main-post">
    <div class="main-post-title">
        <h2><?php $this->title() ?></h2>
    </div>

    <div class="collect">

        <!-- 统计卡片 -->
        <div class="ct-stats">
            <mdui-card variant="outlined" class="ct-stat-card">
                <mdui-icon name="folder" class="ct-stat-icon"></mdui-icon>
                <div class="ct-stat-info">
                    <span class="ct-stat-value"><?php echo $categoryCount; ?></span>
                    <span class="ct-stat-label">个分类</span>
                </div>
            </mdui-card>
            <mdui-card variant="outlined" class="ct-stat-card">
                <mdui-icon name="label" class="ct-stat-icon"></mdui-icon>
                <div class="ct-stat-info">
                    <span class="ct-stat-value"><?php echo $tagCount; ?></span>
                    <span class="ct-stat-label">个标签</span>
                </div>
            </mdui-card>
        </div>

        <!-- 分类区域 -->
        <section class="ct-section">
            <div class="ct-section-header">
                <mdui-icon name="folder" class="ct-section-icon"></mdui-icon>
                <h3 class="ct-section-title">全部分类</h3>
            </div>
            <?php if ($categoryCount > 0): ?>
            <div class="ct-category-grid">
                <?php foreach ($categories as $cat): ?>
                <mdui-card variant="elevated" class="ct-category-card" clickable href="<?php echo $cat['permalink']; ?>">
                    <div class="ct-category-header">
                        <mdui-icon name="folder" class="ct-category-icon"></mdui-icon>
                        <span class="ct-category-name"><?php echo htmlspecialchars($cat['name']); ?></span>
                        <span class="ct-category-count"><?php echo $cat['count']; ?></span>
                    </div>
                    <?php if (!empty($cat['description'])): ?>
                    <p class="ct-category-desc"><?php echo htmlspecialchars($cat['description']); ?></p>
                    <?php endif; ?>
                </mdui-card>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="ct-empty">
                <mdui-icon name="folder_off"></mdui-icon>
                <span>暂无分类</span>
            </div>
            <?php endif; ?>
        </section>

        <!-- 标签区域 -->
        <section class="ct-section">
            <div class="ct-section-header">
                <mdui-icon name="label" class="ct-section-icon"></mdui-icon>
                <h3 class="ct-section-title">全部标签</h3>
            </div>
            <?php if ($tagCount > 0): ?>
            <div class="ct-category-grid">
                <?php foreach ($tags as $tag): ?>
                <mdui-card variant="elevated" class="ct-category-card" clickable href="<?php echo $tag['permalink']; ?>">
                    <div class="ct-category-header">
                        <mdui-icon name="label" class="ct-category-icon"></mdui-icon>
                        <span class="ct-category-name"><?php echo htmlspecialchars($tag['name']); ?></span>
                        <span class="ct-category-count"><?php echo $tag['count']; ?></span>
                    </div>
                </mdui-card>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="ct-empty">
                <mdui-icon name="label_off"></mdui-icon>
                <span>暂无标签</span>
            </div>
            <?php endif; ?>
        </section>

        <!-- 页面正文内容 -->
        <?php if (!empty($this->content)): ?>
        <div class="ct-page-content mdui-prose">
            <?php echo parseMduiNotes($this->content); ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
/* ===== 统计卡片 ===== */
.ct-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 40px;
}

.ct-stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    transition: box-shadow 0.2s ease;
}

.ct-stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.ct-stat-card:nth-child(1) .ct-stat-icon { color: rgb(var(--mdui-color-primary)); }
.ct-stat-card:nth-child(2) .ct-stat-icon { color: rgb(var(--mdui-color-secondary)); }

.ct-stat-icon {
    font-size: 36px;
    flex-shrink: 0;
}

.ct-stat-info {
    display: flex;
    flex-direction: column;
}

.ct-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: rgb(var(--mdui-color-on-surface));
    font-family: 'AlimamaFangYuanTiVF', sans-serif;
    line-height: 1.2;
}

.ct-stat-label {
    font-size: 13px;
    color: rgb(var(--mdui-color-on-surface-variant));
}

/* ===== 区块 ===== */
.ct-section {
    margin-bottom: 48px;
}

.ct-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgb(var(--mdui-color-outline-variant));
}

.ct-section-icon {
    font-size: 24px;
    color: rgb(var(--mdui-color-primary));
}

.ct-section-title {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: rgb(var(--mdui-color-on-surface));
    font-family: 'AlimamaFangYuanTiVF', sans-serif;
}

/* ===== 分类网格 ===== */
.ct-category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
}

.ct-category-card {
    padding: 16px 20px;
    transition: box-shadow 0.2s ease, outline 0.2s ease;
    outline: 2px solid transparent;
    outline-offset: 2px;
}

.ct-category-card:hover {
    outline: 2px solid rgb(var(--mdui-color-primary));
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.ct-category-header {
    display: flex;
    align-items: center;
    gap: 8px;
}

.ct-category-icon {
    font-size: 20px;
    color: rgb(var(--mdui-color-primary));
    flex-shrink: 0;
}

.ct-category-name {
    flex: 1;
    font-size: 16px;
    font-weight: 600;
    color: rgb(var(--mdui-color-on-surface));
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.ct-category-count {
    font-size: 13px;
    font-weight: 600;
    color: rgb(var(--mdui-color-on-surface-variant));
    background: rgb(var(--mdui-color-surface-variant));
    padding: 2px 10px;
    border-radius: var(--mdui-shape-corner-full);
    flex-shrink: 0;
}

.ct-category-desc {
    margin: 8px 0 0 0;
    font-size: 13px;
    color: rgb(var(--mdui-color-on-surface-variant));
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.5;
}

/* ===== 空状态 ===== */
.ct-empty {
    padding: 48px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.ct-empty mdui-icon {
    font-size: 48px;
    color: rgb(var(--mdui-color-outline));
}

.ct-empty span {
    color: rgb(var(--mdui-color-on-surface-variant));
    font-size: 14px;
}

/* ===== 页面正文 ===== */
.ct-page-content {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgb(var(--mdui-color-outline-variant));
}

/* ===== 响应式 ===== */
@media (max-width: 768px) {
    .ct-stats {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .ct-stat-card {
        padding: 16px 20px;
    }

    .ct-stat-icon {
        font-size: 32px;
    }

    .ct-stat-value {
        font-size: 24px;
    }

    .ct-category-grid {
        grid-template-columns: 1fr;
    }

    .ct-section-title {
        font-size: 18px;
    }
}
</style>

<?php $this->need('components/footer.php'); ?>
