<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('components/header.php'); ?>

<header id="header">
    <div class="container">
        <div class="row">
            <mdui-card variant="elevated" clickable id="header-card">
                <img class="img-invert" src="<?php echo getCoverImage($this, $this->options); ?>" alt="<?php $this->options->title(); ?>" loading="lazy">
                <div class="header-content">
                    <h1 class="img-title"><?php $this->title() ?></h1>
                    <h2 class="img-description"><?php $this->author(); ?> / <?php $this->date('Y-m-d'); ?></h2>
                </div>
            </mdui-card>
        </div><!-- end .row -->
    </div>
</header><!-- end #header -->

<article id="main-post" itemtype="http://schema.org/BlogPosting">
    <div class="post-content-container">
        <div class="main-post-content" itemprop="articleBody">
            <div class="post-meta-row">
                <mdui-chip icon="person" disabled><?php $this->author(); ?></mdui-chip>
                <mdui-chip icon="event" disabled><?php $this->date('Y-m-d H:i'); ?></mdui-chip>
                <?php 
                    // 使用 $this->category(',', false) 获取分类列表，但需要获取每个分类的链接
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
                <mdui-chip icon="label">无标签</mdui-chip>
                <?php endif; ?>
                <?php
                    $wordCount = mb_strlen(strip_tags($this->content));
                    $readingMinutes = max(1, ceil($wordCount / 300));
                ?>
                <mdui-chip icon="schedule" disabled><?php echo $readingMinutes; ?> 分钟阅读</mdui-chip>
            </div>
            <div class="mdui-prose">
            <?php echo parseMduiNotes($this->content); ?>
            </div>
        </div>
        
        <!-- 文章目录 -->
        <div id="toc-container" class="contents">
            <div class="title">本页目录</div>
            <mdui-list class="items" id="toc-content"></mdui-list>
        </div>
    </div>
    
    <?php $this->need("components/list-template.php");?>
    
    <!-- 文章上下篇导航 -->
    <?php
    // 使用 cid 作为 created 相同时的次级排序，避免同时间戳文章在上下篇导航中互相跳过
    $prevPost = \Widget\Contents\From::allocWithAlias(
        'prev-near:' . $this->cid,
        ['query' => $this->select()
            ->where('(table.contents.created < ? OR (table.contents.created = ? AND table.contents.cid < ?))', $this->created, $this->created, $this->cid)
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', $this->type)
            ->where("table.contents.password IS NULL OR table.contents.password = ''")
            ->order('table.contents.created', \Typecho\Db::SORT_DESC)
            ->order('table.contents.cid', \Typecho\Db::SORT_DESC)
            ->limit(1)
        ]
    );
    $nextPost = \Widget\Contents\From::allocWithAlias(
        'next-near:' . $this->cid,
        ['query' => $this->select()
            ->where('(table.contents.created > ? AND table.contents.created < ?) OR (table.contents.created = ? AND table.contents.cid > ?)', $this->created, $this->options->time, $this->created, $this->cid)
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', $this->type)
            ->where("table.contents.password IS NULL OR table.contents.password = ''")
            ->order('table.contents.created', \Typecho\Db::SORT_ASC)
            ->order('table.contents.cid', \Typecho\Db::SORT_ASC)
            ->limit(1)
        ]
    );
    ?>
    <div class="post-near">
        <?php if ($prevPost->have()): ?>
            <?php $prevCover = getCoverImage($prevPost, $this->options); ?>
        <mdui-card variant="outlined" class="post-near-card" href="<?php echo $prevPost->permalink; ?>">
            <div class="post-near-inner">
                <div class="post-near-thumb">
                    <img src="<?php echo $prevCover; ?>" alt="<?php echo htmlspecialchars($prevPost->title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                </div>
                <mdui-icon name="arrow_back" class="post-near-icon"></mdui-icon>
                <div class="post-near-info">
                    <span class="post-near-label">上一篇</span>
                    <span class="post-near-title"><?php echo $prevPost->title; ?></span>
                </div>
            </div>
        </mdui-card>
        <?php else: ?>
        <mdui-card variant="outlined" class="post-near-card post-near-disabled">
            <div class="post-near-inner">
                <mdui-icon name="arrow_back" class="post-near-icon"></mdui-icon>
                <div class="post-near-info">
                    <span class="post-near-label">上一篇</span>
                    <span class="post-near-title">已是最新</span>
                </div>
            </div>
        </mdui-card>
        <?php endif; ?>

        <?php if ($nextPost->have()): ?>
            <?php $nextCover = getCoverImage($nextPost, $this->options); ?>
        <mdui-card variant="outlined" class="post-near-card post-near-next" href="<?php echo $nextPost->permalink; ?>">
            <div class="post-near-inner">
                <div class="post-near-info">
                    <span class="post-near-label">下一篇</span>
                    <span class="post-near-title"><?php echo $nextPost->title; ?></span>
                </div>
                <mdui-icon name="arrow_forward" class="post-near-icon"></mdui-icon>
                <div class="post-near-thumb">
                    <img src="<?php echo $nextCover; ?>" alt="<?php echo htmlspecialchars($nextPost->title, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                </div>
            </div>
        </mdui-card>
        <?php else: ?>
        <mdui-card variant="outlined" class="post-near-card post-near-next post-near-disabled">
            <div class="post-near-inner">
                <div class="post-near-info">
                    <span class="post-near-label">下一篇</span>
                    <span class="post-near-title">已是最早</span>
                </div>
                <mdui-icon name="arrow_forward" class="post-near-icon"></mdui-icon>
            </div>
        </mdui-card>
        <?php endif; ?>
    </div>
    
    <?php $this->need('components/comments.php'); ?>

</article>

<?php $this->need('components/footer.php'); ?>
