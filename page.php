<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('components/header.php'); ?>

<article id="main-post" itemtype="http://schema.org/BlogPosting">
    <div class="main-post-title">
        <h2><?php $this->title() ?></h2>
    </div>
    
    <div class="main-post-content no-toc mdui-prose" itemprop="articleBody">
        <?php echo parseMduiNotes($this->content); ?>
    </div>
    
    <?php $this->need('components/comments.php'); ?>
</article>

<?php $this->need('components/footer.php'); ?>
