<?php
/**
 * a material design 3 Typecho theme
 *
 * @package M3UI
 * @author Yuuz12
 * @version 1.1.5
 * @link https://yuuz12.top
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div id="main-index">
    <div class="main-index-title">
        <h2>文章 & 日记</h2>
    </div>
    <div class="collect">
        <div class="post-grid">
        <?php while ($this->next()): ?>
            <mdui-card clickable variant="elevated" class="post-card" href="<?php $this->permalink() ?>" itemscope itemtype="http://schema.org/BlogPosting">
                <img class="img-invert" src="<?php echo getCoverImage($this, $this->options); ?>"></img>
                <div class="post-card-info">
                    <span class="post-title"><?php $this->title() ?></span>
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
                        <mdui-chip icon="folder" elevated>无分类</mdui-chip>
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
                        <mdui-chip icon="label" disabled elevated>无标签</mdui-chip>
                        <?php endif; ?>
                    </div>
                </div>
            </mdui-card>
        <?php endwhile; ?>
        </div>
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


<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
