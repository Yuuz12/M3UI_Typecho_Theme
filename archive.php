<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('components/header.php'); ?>

<div id="main-index">
    <div class="main-index-title">
        <h2><?php $this->archiveTitle([
                'category' => _t('分类: %s'),
                'search'   => _t('搜索: %s'),
                'tag'      => _t('标签: %s'),
                'author'   => _t('作者: %s')
            ], '', ''); ?></h2>
    </div>
    
    <div class="collect">
        <?php if ($this->have()): ?>
            <div class="post-grid">
                <?php while ($this->next()): ?>
                    <mdui-card clickable variant="elevated" class="post-card" href="<?php $this->permalink() ?>" itemscope itemtype="http://schema.org/BlogPosting">
                        <img class="img-invert" src="<?php echo getCoverImage($this, $this->options); ?>"></img>
                        <div class="post-card-info">
                            <span class="post-title" itemprop="name headline"><?php $this->title() ?></a></span>
                            <div class="post-meta-chips">
                                <mdui-chip icon="person" disabled elevated><?php $this->author(); ?></mdui-chip>
                                <mdui-chip icon="event" disabled elevated><?php $this->date('Y-m-d'); ?></mdui-chip>
                                <?php 
                                $categories = $this->categories;
                                if ($categories && is_array($categories)):
                                    foreach($categories as $category):
                                ?>
                                <mdui-chip icon="folder" href="<?php echo $category['permalink']; ?>"><?php echo $category['name']; ?></mdui-chip>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                <mdui-chip icon="folder">无分类</mdui-chip>
                                <?php 
                                    endif; 
                                ?>
                                <?php 
                                if ($this->tags): 
                                    foreach($this->tags as $tag): 
                                ?>
                                <mdui-chip icon="label" href="<?php echo $tag['permalink']; ?>"><?php echo $tag['name']; ?></mdui-chip>
                                <?php 
                                    endforeach;
                                else: 
                                ?>
                                <mdui-chip icon="label">暂无</mdui-chip>
                                <?php endif; ?>
                            </div>
                        </div>
                    </mdui-card>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <mdui-card variant="outlined" class="no-results-card">
                    <div class="no-results-content">
                        <mdui-icon name="search_off" class="no-results-icon"></mdui-icon>
                        <h3 class="no-results-title"><?php _e('没有找到内容'); ?></h3>
                        <p class="no-results-desc"><?php _e('抱歉，没有找到相关内容。请尝试使用其他关键词搜索。'); ?></p>
                        <div class="no-results-search">
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
        <?php endif; ?>
        <div style="display:flex;justify-content:space-between;margin-top:24px;">
            <?php if ($this->getCurrentPage() > 1):
                ob_start(); $this->pageLink('上一页'); $prevHtml = ob_get_clean();
                preg_match('/href="([^"]*)"/', $prevHtml, $prevMatch);
            ?>
            <mdui-button variant="filled" icon="arrow_back" href="<?php echo $prevMatch[1]; ?>">上一页</mdui-button>
            <?php endif; ?>
            <?php if ($this->getCurrentPage() < $this->getTotalPage()):
                ob_start(); $this->pageLink('下一页', 'next'); $nextHtml = ob_get_clean();
                preg_match('/href="([^"]*)"/', $nextHtml, $nextMatch);
            ?>
            <mdui-button variant="filled" end-icon="arrow_forward" href="<?php echo $nextMatch[1]; ?>" style="margin-left:auto;">下一页</mdui-button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->need('components/footer.php'); ?>
