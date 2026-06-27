<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<header id="header">
    <div class="container">
        <div class="row">
            <mdui-card variant="elevated" id="header-card">
                <img class="img-invert" src="<?php echo getCoverImage($this, $this->options); ?>"></img>
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
            </div>
            <div class="mdui-prose">
            <?php $this->content(); ?>
            </div>
        </div>
        
        <!-- 文章目录 -->
        <div id="toc-container">
            <div class="toc-header">
                <mdui-icon name="menu_book"></mdui-icon>
                <span>文章目录</span>
            </div>
            <mdui-list id="toc-content"></mdui-list>
        </div>
    </div>
    
    <?php $this->need("list-template.php");?>
    <?php $this->need('comments.php'); ?>
    
    <!-- <ul class="post-near">
        <li>上一篇: <?php $this->thePrev('%s', '没有了'); ?></li>
        <li>下一篇: <?php $this->theNext('%s', '没有了'); ?></li>
    </ul> -->
    
</article>

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
