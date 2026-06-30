<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<!--        </div><!-- end .row -->
<!--    </div>-->
<!--</div><!-- end #body -->

</div><!-- end #pjax-container -->

<div class="scrollToTopBtn-wrapper">
    <mdui-fab icon="keyboard_arrow_up" class="scrollToTopBtn"></mdui-fab>
</div>

<div class="divider">
    <svg aria-hidden="true" width="100%" height="8" fill="none" xmlns="http://www.w3.org/2000/svg">
        <pattern id="a" width="91" height="8" patternUnits="userSpaceOnUse">
            <g>
                <path d="M114 4c-5.067 4.667-10.133 4.667-15.2 0S88.667-.667 83.6 4 73.467 8.667 68.4 4 58.267-.667 53.2 4 43.067 8.667 38 4 27.867-.667 22.8 4 12.667 8.667 7.6 4-2.533-.667-7.6 4s-10.133 4.667-15.2 0S-32.933-.667-38 4s-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0-10.133-4.667-15.2 0-10.133 4.667-15.2 0" stroke="#E1E3E1" stroke-linecap="square">
                    
                </path>
            </g>
        </pattern>
        <rect width="100%" height="100%" fill="url(#a)"></rect>
    </svg>
</div>
<footer id="footer" role="contentinfo">
    <div class="footer-content">
        <span>&copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a></span>
        <span><?php _e('由 <a href="http://www.typecho.org">Typecho</a> 强力驱动'); ?></span>
        <?php if ($this->options->icpNumber): ?>
            <span>
                <mdui-chip href="<?php echo $this->options->icpLink ?: 'https://beian.miit.gov.cn/'; ?>" variant="outlined" icon="verified" target="_blank" rel="noopener"><?php $this->options->icpNumber(); ?></mdui-chip>
            </span>
        <?php endif; ?>
    </div>
</footer>

<?php $this->footer(); ?>

<!-- 代码高亮核心 -->
<script src="<?php $this->options->themeUrl('res/prism/prism-core.min.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('res/prism/prism-autoloader.min.js'); ?>"></script>
<script>
    Prism.plugins.autoloader.languages_path = '<?php $this->options->themeUrl('res/prism/'); ?>';
    // Prism 高亮完成后自动设置 data-language 属性（用于 CSS 伪元素显示语言标签）
    Prism.hooks.add('complete', function(env) {
        var pre = env.element.parentElement;
        if (pre && pre.tagName === 'PRE' && !pre.getAttribute('data-language')) {
            var lang = env.language;
            if (lang && lang !== 'none') {
                pre.setAttribute('data-language', lang.toUpperCase());
            }
        }
    });
</script>

<script src="<?php $this->options->themeUrl('res/spotlight.bundle.js'); ?>"></script>
<script src="<?php $this->options->themeUrl('res/script.js'); ?>"></script>

</body>
</html>