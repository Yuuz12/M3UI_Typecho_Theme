<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('components/header.php'); ?>

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
<?php $this->need('components/footer.php'); ?>
