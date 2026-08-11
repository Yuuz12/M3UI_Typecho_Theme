<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('components/header.php'); ?>

<article id="main-post" itemtype="http://schema.org/BlogPosting">
    <div class="main-post-title">
        <h2><?php $this->title() ?></h2>
    </div>
    
    <div class="main-post-content no-toc mdui-prose" itemprop="articleBody">
        <?php echo parseMduiNotes($this->content); ?>
    </div>
    
    <?php
    // 引入评论回调模板（threadedComments），与 post.php 保持一致，
    // 否则评论列表会回退到 Typecho 默认 <li> 结构而丢失 mdui 卡片样式
    $this->need('components/list-template.php');
    $this->need('components/comments.php');
    ?>
</article>

<?php $this->need('components/footer.php'); ?>
