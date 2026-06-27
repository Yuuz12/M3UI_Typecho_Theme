<?php function threadedComments($comments, $options) {
    $commentClass = 'mdui-card';
    if ($comments->levels > 0) {
        $commentClass .= ' comment-child';
        if ($comments->levels > 1) {
            $commentClass .= ' comment-child-' . $comments->levels;
        }
    } else {
        $commentClass .= ' comment-parent';
    }
    if ($comments->authorId == $comments->ownerId) {
        $commentClass .= ' comment-by-author';
    }
    $avatarUrl = \Typecho\Common::gravatarUrl($comments->mail, $options->avatarSize, 'g', $options->defaultAvatar, \Typecho\Request::getInstance()->isSecure());
    $statusClass = $comments->status === 'approved' ? '' : ' comment-awaiting';
?>
<mdui-card variant="elevated" class="<?php echo $commentClass . $statusClass; ?>" id="<?php $comments->theId(); ?>">
    <div class="comment-inner">
        <mdui-avatar src="<?php echo $avatarUrl; ?>" alt="<?php $comments->author(); ?>"></mdui-avatar>
        <div class="comment-body">
            <div class="comment-meta">
                <span class="comment-author">
                    <?php $comments->author(); ?>
                </span>
                <?php if ($comments->authorId == $comments->ownerId): ?>
                    <mdui-chip variant="outlined" size="small" icon="verified" disabled>作者</mdui-chip>
                <?php endif; ?>
                <mdui-chip variant="outlined" size="small" icon="schedule" disabled>
                    <?php $comments->date($options->dateFormat); ?>
                </mdui-chip>
            </div>
            <div class="comment-content">
                <?php $comments->content(); ?>
            </div>
            <?php if ($comments->status !== 'approved'): ?>
                <mdui-chip variant="outlined" icon="schedule" class="comment-pending">
                    <?php $options->commentStatus(); ?>
                </mdui-chip>
            <?php endif; ?>
        </div>
    </div>
    <div class="comment-reply">
        <?php $comments->reply('<mdui-button variant="elevated" size="small" icon="reply">' . $options->replyWord . '</mdui-button>'); ?>
    </div>
    <?php if ($comments->children): ?>
        <div class="comment-children">
            <?php $comments->threadedComments($options); ?>
        </div>
    <?php endif; ?>
</mdui-card>
<?php } ?>