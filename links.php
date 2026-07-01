<?php
/**
 * 友情链接
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('components/header.php');

/**
 * 从 Links 插件读取友链数据
 * Links 插件数据表: %prefix%links
 * 字段: name, url, image, description, user, sort, order
 */
$links = array();
$db = Typecho_Db::get();
$select = $db->select()->from('table.links')->where('order > ?', 0)->order('order', Typecho_Db::SORT_ASC);
try {
    $result = $db->fetchAll($select);
    if ($result) {
        $links = $result;
    }
} catch (Exception $e) {
    // Links 插件未安装或表不存在时静默失败
}

$linkCount = count($links);
?>

<div id="main-post">
    <div class="main-post-title">
        <h2><?php $this->title() ?></h2>
    </div>

    <div class="collect">
        <!-- 友链网格 -->
        <?php if ($linkCount > 0): ?>
        <div class="links-grid">
            <?php foreach ($links as $link): ?>
            <mdui-card variant="outlined" class="link-card" clickable href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" rel="noopener">
                <mdui-avatar src="<?php echo !empty($link['image']) ? htmlspecialchars($link['image']) : ''; ?>"></mdui-avatar>
                <div class="link-info">
                    <h3 class="link-name"><?php echo htmlspecialchars($link['name']); ?></h3>
                    <?php if (!empty($link['description'])): ?>
                    <p class="link-desc"><?php echo htmlspecialchars($link['description']); ?></p>
                    <?php endif; ?>
                </div>
            </mdui-card>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="links-empty">
            <mdui-icon name="link_off"></mdui-icon>
            <span>暂无友情链接</span>
        </div>
        <?php endif; ?>

        <!-- 页面正文内容（用户可在编辑器中自定义说明） -->
        <?php if (!empty($this->content)): ?>
        <div class="link-page-content mdui-prose">
            <?php echo parseMduiNotes($this->content); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* ===== 友链网格 ===== */
.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

/* ===== 友链卡片 ===== */
.link-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    transition: box-shadow 0.2s ease, outline 0.2s ease;
    outline: 2px solid transparent;
    outline-offset: 2px;
    text-decoration: none;
    color: inherit;
    height: 100%;
}

.link-card:hover {
    outline: 2px solid rgb(var(--mdui-color-primary));
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    text-decoration: none;
}

.link-card:focus-visible {
    outline: 2px solid rgb(var(--mdui-color-primary));
}

/* ===== 头像 ===== */
.link-card mdui-avatar {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: var(--mdui-shape-corner-full);
    background-color: rgb(var(--mdui-color-surface-variant));
    --mdui-color-primary: inherit;
}

/* ===== 友链信息 ===== */
.link-info {
    flex: 1;
    min-width: 0;
}

.link-name {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: rgb(var(--mdui-color-on-surface));
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.4;
}

.link-desc {
    margin: 4px 0 0 0;
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
.links-empty {
    padding: 48px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    margin: 24px 0;
}

.links-empty mdui-icon {
    font-size: 48px;
    color: rgb(var(--mdui-color-outline));
}

.links-empty span {
    color: rgb(var(--mdui-color-on-surface-variant));
    font-size: 14px;
}

/* ===== 页面正文内容 ===== */
.link-page-content {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgb(var(--mdui-color-outline-variant));
}

/* ===== 响应式 ===== */
@media (max-width: 480px) {
    .links-grid {
        grid-template-columns: 1fr;
    }

    .link-card {
        padding: 12px;
    }

    .link-card mdui-avatar {
        width: 40px;
        height: 40px;
    }

    .link-name {
        font-size: 15px;
    }
}
</style>

<?php $this->need('components/footer.php'); ?>
