<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$options = \Widget\Options::alloc();
$themeUrl = $options->themeUrl;
?>

<link rel="stylesheet" href="<?php echo $themeUrl; ?>/res/mdui@2.1.4/mdui.css">
<link rel="stylesheet" href="<?php echo $themeUrl; ?>/res/material-icons.css">
<script src="<?php echo $themeUrl; ?>/res/mdui@2.1.4/mdui.global.js"></script>

<style>
/* M3UI 按钮 */
#wmd-button-row #wmd-m3ui-note-button {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    margin-left: 5px;
    padding: 3px;
    cursor: pointer;
    vertical-align: middle;
}
#wmd-button-row #wmd-m3ui-note-button mdui-icon {
    font-size: 18px;
    color: #5e5e5e;
    transition: color 0.15s;
}
#wmd-button-row #wmd-m3ui-note-button:hover mdui-icon {
    color: #2196f3;
}

/* 下拉菜单容器 */
#m3ui-note-menu-wrap {
    position: relative;
    display: inline-block;
    vertical-align: middle;
}
#m3ui-note-menu {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 4px;
    z-index: 10000;
    min-width: 180px;
}
</style>

<script>
(function() {
    var config = {
        red:   { tag: 'm3ui_error',   label: '错误', icon: 'error',        color: '#e53935' },
        yellow:{ tag: 'm3ui_warning', label: '警告', icon: 'lightbulb',    color: '#ffa000' },
        green: { tag: 'm3ui_success', label: '成功', icon: 'check_circle', color: '#43a047' }
    };

    function insertShortcode(color) {
        var cfg = config[color];
        if (!cfg) return;

        var textarea = document.getElementById('text');
        if (!textarea) return;

        var placeholder = '在此输入' + cfg.label + '内容';
        var shortcode = '[' + cfg.tag + ']' + placeholder + '[/' + cfg.tag + ']';

        var start = textarea.selectionStart;
        var end   = textarea.selectionEnd;
        var text  = textarea.value;
        textarea.value = text.substring(0, start) + shortcode + text.substring(end);

        var cursorPos = start + ('[' + cfg.tag + ']').length;
        textarea.selectionStart = cursorPos;
        textarea.selectionEnd   = cursorPos + placeholder.length;
        textarea.focus();

        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }

    function initButton() {
        var row = document.getElementById('wmd-button-row');
        if (!row) return;
        if (row.querySelector('#wmd-m3ui-note-button')) return;

        // 创建包裹容器
        var wrap = document.createElement('div');
        wrap.id = 'm3ui-note-menu-wrap';

        // 创建触发按钮（带 mdui-icon）
        var btn = document.createElement('div');
        btn.id = 'wmd-m3ui-note-button';
        btn.title = 'M3UI 样式卡片';
        var icon = document.createElement('mdui-icon');
        icon.setAttribute('name', 'palette');
        btn.appendChild(icon);

        // 创建 mdui-menu 下拉
        var menu = document.createElement('mdui-menu');
        menu.id = 'm3ui-note-menu';
        menu.style.display = 'none';

        var keys = ['red', 'yellow', 'green'];
        for (var i = 0; i < keys.length; i++) {
            var cfg = config[keys[i]];
            var item = document.createElement('mdui-menu-item');
            item.setAttribute('value', keys[i]);
            item.setAttribute('icon', cfg.icon);
            item.style.setProperty('--mdui-color-on-surface', cfg.color);
            // 用 icon slot 着色 + 文本
            var iconSlot = document.createElement('mdui-icon');
            iconSlot.setAttribute('slot', 'icon');
            iconSlot.setAttribute('name', cfg.icon);
            iconSlot.style.color = cfg.color;
            item.innerHTML = '';
            item.appendChild(iconSlot);
            item.appendChild(document.createTextNode(cfg.label + '提示'));
            (function(k) {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    insertShortcode(k);
                    menu.style.display = 'none';
                });
            })(keys[i]);
            menu.appendChild(item);
        }

        wrap.appendChild(btn);
        wrap.appendChild(menu);
        row.appendChild(wrap);

        // 点击按钮切换菜单
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.style.display = (menu.style.display === 'none' || !menu.style.display) ? 'block' : 'none';
        });

        // 点击页面其他位置关闭
        document.addEventListener('click', function() {
            menu.style.display = 'none';
        });
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // 轮询等待工具栏出现
    var attempts = 0;
    var timer = setInterval(function() {
        if (document.getElementById('wmd-button-row')) {
            clearInterval(timer);
            // 延迟一帧确保 pagedown 工具栏按钮已渲染
            setTimeout(initButton, 50);
            return;
        }
        if (++attempts > 60) { clearInterval(timer); }
    }, 100);
})();
</script>
