<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div id="comments" style="margin-top: 24px;">
    <?php if ($this->allow('comment')): ?>
    <?php $this->comments()->to($comments); ?>
    <?php if ($comments->have()): ?>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <mdui-icon name="chat_bubble_outline" style="color: rgb(var(--mdui-color-primary));"></mdui-icon>
            <h3 style="margin: 0;"><?php $this->commentsNum(_t('暂无评论'), _t('1 条评论'), _t('%d 条评论')); ?></h3>
        </div>

        <?php $comments->listComments([
            'before' => '<div class="comment-grid">',
            'after' => '</div>',
            'avatarSize' => 40,
            'dateFormat' => 'Y年n月j日 H:i'
        ]); ?>

        <?php if ($comments->have()):
            ob_start();
            $comments->pageNav('&laquo;', '&raquo;', 3, '...');
            $navHtml = ob_get_clean();
            // 解析 pageNav 输出的 <li><a href="...">text</a></li> 结构，重建为 mdui-chip
            if (preg_match_all('/<li[^>]*class="([^"]*)"[^>]*>.*?<a\s+href="([^"]*)"[^>]*>(.*?)<\/a>.*?<\/li>/s', $navHtml, $matches, PREG_SET_ORDER)) {
                echo '<div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-top:16px;">';
                foreach ($matches as $m) {
                    $class = $m[1];
                    $href = $m[2];
                    $text = trim(strip_tags($m[3]));
                    if ($class === 'current') {
                        echo '<mdui-chip href="' . htmlspecialchars($href) . '" variant="assist" selected>' . $text . '</mdui-chip>';
                    } elseif ($class === 'prev' || $class === 'next') {
                        $icon = ($class === 'prev') ? 'chevron_left' : 'chevron_right';
                        echo '<mdui-chip href="' . htmlspecialchars($href) . '" variant="assist" icon="' . $icon . '"></mdui-chip>';
                    } else {
                        echo '<mdui-chip href="' . htmlspecialchars($href) . '" variant="assist">' . $text . '</mdui-chip>';
                    }
                }
                echo '</div>';
            }
        endif; ?>

    <?php endif; ?>

    <div id="<?php $this->respondId(); ?>" class="respond" style="margin-top: 32px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                <mdui-icon name="edit_note" style="color: rgb(var(--mdui-color-primary));"></mdui-icon>
                <h3 id="response" style="margin: 0;"><?php _e('添加新评论'); ?></h3>
            </div>
            
            <div class="cancel-comment-reply" style="margin-bottom: 12px;">
                <?php $comments->cancelReply(); ?>
            </div>

            <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
                <?php if ($this->user->hasLogin()): ?>
                    <mdui-card variant="outlined" class="comment-user-info">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <mdui-avatar src="<?php echo \Typecho\Common::gravatarUrl($this->user->mail, 80, 'g', null, \Typecho\Request::getInstance()->isSecure()); ?>" alt="<?php $this->user->screenName(); ?>"></mdui-avatar>
                                <div>
                                    <p style="margin: 0; font-weight: 500;"><?php $this->user->screenName(); ?></p>
                                    <p style="margin: 4px 0 0 0; font-size: 12px; color: rgb(var(--mdui-color-on-surface-variant));">已登录</p>
                                </div>
                            </div>
                            <mdui-button variant="tonal" size="small" href="<?php $this->options->logoutUrl(); ?>"><?php _e('退出'); ?></mdui-button>
                        </div>
                    </mdui-card>
                <?php else: ?>
                    <mdui-text-field variant="outlined" type="text" name="author" id="author" label="<?php _e('称呼'); ?>" required value="<?php $this->remember('author'); ?>" style="margin-bottom: 12px;"></mdui-text-field>
                    <mdui-text-field variant="outlined" type="email" name="mail" id="mail" label="<?php _e('电子邮箱'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> value="<?php $this->remember('mail'); ?>" style="margin-bottom: 12px;"></mdui-text-field>
                    <mdui-text-field variant="outlined" type="url" name="url" id="url" label="<?php _e('网站'); ?>" placeholder="https://"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?> value="<?php $this->remember('url'); ?>" style="margin-bottom: 12px;"></mdui-text-field>
                <?php endif; ?>
                
                <mdui-text-field variant="outlined" autosize min-rows="4" max-rows="6" type="textarea" name="text" id="textarea" label="评论内容" required style="margin-bottom: 16px;"><?php $this->remember('text'); ?></mdui-text-field>
                
                <mdui-button type="submit" variant="filled" icon="send"><?php _e('提交评论'); ?></mdui-button>
            </form>
        </div>
    <?php else: ?>
        <mdui-card variant="outlined" style="width: 100%; padding: 24px; text-align: center; margin-top: 24px;">
            <mdui-icon name="chat_bubble_off" style="font-size: 48px; color: rgb(var(--mdui-color-on-surface-variant));"></mdui-icon>
            <p style="margin-top: 12px; color: rgb(var(--mdui-color-on-surface-variant));"><?php _e('评论已关闭'); ?></p>
        </mdui-card>
    <?php endif; ?>
</div>
