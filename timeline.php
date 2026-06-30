<?php
/**
 * 时间轴
 * 
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('components/header.php');

// 统计文章数据
$postCount = 0;
$categoryCount = 0;
$tagCount = 0;
$tagSet = [];
$categorySet = [];
$this->widget('Widget_Contents_Post_Recent', 'pageSize=1000')->to($statPosts);
while ($statPosts->next()) {
    $postCount++;
    if ($statPosts->categories) {
        foreach ($statPosts->categories as $cat) {
            $categorySet[$cat['mid']] = true;
        }
    }
    if ($statPosts->tags) {
        foreach ($statPosts->tags as $tag) {
            $tagSet[$tag['mid']] = true;
        }
    }
}
$categoryCount = count($categorySet);
$tagCount = count($tagSet);
?>

<div id="main-post">
    <div class="main-post-title">
        <h2><?php $this->title() ?></h2>
    </div>
    
    <div class="collect">
        <!-- 统计卡片 -->
        <div class="timeline-stats">
            <mdui-card variant="outlined" class="stat-card">
                <mdui-icon name="article" class="stat-icon"></mdui-icon>
                <span class="stat-value"><?php echo $postCount; ?></span>
                <span class="stat-label">篇文章</span>
            </mdui-card>
            <mdui-card variant="outlined" class="stat-card">
                <mdui-icon name="folder" class="stat-icon"></mdui-icon>
                <span class="stat-value"><?php echo $categoryCount; ?></span>
                <span class="stat-label">个分类</span>
            </mdui-card>
            <mdui-card variant="outlined" class="stat-card">
                <mdui-icon name="label" class="stat-icon"></mdui-icon>
                <span class="stat-value"><?php echo $tagCount; ?></span>
                <span class="stat-label">个标签</span>
            </mdui-card>
        </div>
        
        <!-- 时间线 -->
        <div class="timeline-wrapper">
            <?php 
            $this->widget('Widget_Contents_Post_Recent', 'pageSize=1000')->to($archives);
            
            $year = 0;
            $mon = 0;
            $i = 0;
            
            while($archives->next()):
                $year_tmp = date('Y', $archives->created);
                $mon_tmp = date('m', $archives->created);
                
                if ($year != $year_tmp || $mon != $mon_tmp):
                    if ($mon != 0):
            ?>
                </div>
                </div>
            <?php endif; ?>
            
            <?php if ($year != $year_tmp): ?>
                <?php if ($i > 0): ?>
                </div>
                <?php endif; ?>
            
            <div class="timeline-year">
                <div class="timeline-year-header">
                    <span class="timeline-year-dot"></span>
                    <h3 class="timeline-year-title"><?php echo $year_tmp; ?> 年</h3>
                </div>
            <?php endif; ?>
            
            <div class="timeline-month">
                <div class="timeline-month-header">
                    <span class="timeline-month-dot"></span>
                    <h4 class="timeline-month-title"><?php echo $mon_tmp; ?> 月</h4>
                </div>
                <div class="post-grid">
            <?php 
                    $year = $year_tmp;
                    $mon = $mon_tmp;
                    $i++;
                endif;
            ?>
            
            <mdui-card clickable variant="elevated" class="post-card" href="<?php $archives->permalink() ?>" itemscope itemtype="http://schema.org/BlogPosting">
                <img class="img-invert" src="<?php echo getCoverImage($archives, $this->options); ?>"></img>
                <div class="post-card-info">
                    <span class="post-title"><?php $archives->title() ?></span>
                    <div class="post-meta-chips">
                        <mdui-chip icon="person" disabled elevated><?php $archives->author(); ?></mdui-chip>
                        <mdui-chip icon="event" disabled elevated><?php echo date('Y-m-d', $archives->created); ?></mdui-chip>
                        <?php 
                            $categories = $archives->categories;
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
                            $tags = $archives->tags;
                            if ($tags): 
                                foreach($tags as $tag): 
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
            
            <?php if ($i > 0): ?>
                </div>
                </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* ===== 统计卡片 ===== */
.timeline-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 40px;
}

.stat-card {
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.stat-card:nth-child(1) .stat-icon { color: rgb(var(--mdui-color-primary)); }
.stat-card:nth-child(2) .stat-icon { color: rgb(var(--mdui-color-tertiary)); }
.stat-card:nth-child(3) .stat-icon { color: rgb(var(--mdui-color-secondary)); }

.stat-icon {
    font-size: 32px;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: rgb(var(--mdui-color-on-surface));
    font-family: 'AlimamaFangYuanTiVF', sans-serif;
    line-height: 1.2;
}

.stat-label {
    font-size: 13px;
    color: rgb(var(--mdui-color-on-surface-variant));
}

/* ===== 时间线主结构 ===== */
.timeline-wrapper {
    position: relative;
    padding-left: 40px;
}

/* 竖线 */
.timeline-wrapper::before {
    content: '';
    position: absolute;
    top: 8px;
    bottom: 8px;
    left: 19px;
    width: 2px;
    background: linear-gradient(
        to bottom,
        rgb(var(--mdui-color-primary)) 0%,
        rgb(var(--mdui-color-outline-variant)) 8%,
        rgb(var(--mdui-color-outline-variant)) 92%,
        transparent 100%
    );
    border-radius: 1px;
}

/* ===== 年份 ===== */
.timeline-year {
    margin-bottom: 48px;
}

.timeline-year-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    position: relative;
}

.timeline-year-dot {
    position: absolute;
    left: -29px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: rgb(var(--mdui-color-primary));
    border: 3px solid rgb(var(--mdui-color-surface));
    box-shadow: 0 0 0 2px rgb(var(--mdui-color-primary));
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.timeline-year:hover .timeline-year-dot {
    transform: scale(1.2);
}

.timeline-year-title {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    font-family: 'AlimamaFangYuanTiVF', sans-serif;
    padding: 6px 16px;
    background: rgb(var(--mdui-color-primary-container));
    color: rgb(var(--mdui-color-on-primary-container));
    border-radius: var(--mdui-shape-corner-full);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.timeline-year:hover .timeline-year-title {
    transform: translateX(4px);
}

/* ===== 月份 ===== */
.timeline-month {
    margin-bottom: 32px;
}

.timeline-month-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    position: relative;
}

.timeline-month-dot {
    position: absolute;
    left: -25px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgb(var(--mdui-color-secondary));
    border: 2px solid rgb(var(--mdui-color-surface));
    box-shadow: 0 0 0 2px rgb(var(--mdui-color-secondary));
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.timeline-month:hover .timeline-month-dot {
    transform: scale(1.25);
}

.timeline-month-title {
    margin: 0;
    color: rgb(var(--mdui-color-on-surface));
    font-size: 18px;
    font-weight: 600;
    font-family: 'AlimamaFangYuanTiVF', sans-serif;
    transition: color 0.3s ease;
}

.timeline-month:hover .timeline-month-title {
    color: rgb(var(--mdui-color-secondary));
}

/* ===== 响应式 ===== */
@media (max-width: 768px) {
    .timeline-stats {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .stat-card {
        flex-direction: row;
        justify-content: flex-start;
        gap: 16px;
        padding: 16px;
    }
    
    .stat-icon {
        margin-bottom: 0;
    }
    
    .stat-value {
        font-size: 24px;
    }
    
    .timeline-wrapper {
        padding-left: 28px;
    }
    
    .timeline-wrapper::before {
        left: 13px;
    }
    
    .timeline-year-dot {
        left: -21px;
        width: 12px;
        height: 12px;
    }
    
    .timeline-month-dot {
        left: -19px;
        width: 8px;
        height: 8px;
    }
    
    .timeline-year-title {
        font-size: 22px;
        padding: 4px 12px;
    }
    
    .timeline-month-title {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .timeline-year {
        margin-bottom: 32px;
    }
    
    .timeline-year-title {
        font-size: 18px;
    }
    
    .timeline-month-title {
        font-size: 14px;
    }
}
</style>

<?php $this->need('components/footer.php'); ?>
