<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<article id="main-post" itemtype="http://schema.org/BlogPosting">
    <div class="main-post-title">
        <h2><?php $this->title() ?></h2>
    </div>
    
    <div class="main-post-content mdui-prose" itemprop="articleBody">
        <?php $this->content(); ?>
    </div>
    
    <?php $this->need('comments.php'); ?>
</article>

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
