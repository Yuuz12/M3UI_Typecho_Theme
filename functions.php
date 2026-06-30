<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl',
        null,
        null,
        _t('站点 LOGO 地址'),
        _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 LOGO')
    );

    $headerImage = new \Typecho\Widget\Helper\Form\Element\Text(
        'headerImage',
        null,
        null,
        _t('站点头图地址'),
        _t('在这里填入一个图片 URL 地址作为站点头图，不填则不显示头图')
    );

    $enableIndexPage = new \Typecho\Widget\Helper\Form\Element\Radio(
        'enableIndexPage',
        [
            1 => '启用',
            0 => '禁用'
        ],
        0,
        _t('启用文章页面'),
        _t('启用后将在导航栏显示文章入口')
    );

    $icpNumber = new \Typecho\Widget\Helper\Form\Element\Text(
        'icpNumber',
        null,
        null,
        _t('ICP备案号'),
        _t('在这里填入ICP备案号')
    );

    $icpLink = new \Typecho\Widget\Helper\Form\Element\Text(
        'icpLink',
        null,
        'https://beian.miit.gov.cn/',
        _t('备案号链接'),
        _t('在这里填入备案号指向的链接，默认为工信部备案查询网址')
    );

    $accentColor = new \Typecho\Widget\Helper\Form\Element\Text(
        'accentColor',
        null,
        '#6200ee',
        _t('主题强调色'),
        _t('输入十六进制颜色值，例如: #6200ee，将基于此颜色生成整套配色方案')
    );

    $bangumiID = new \Typecho\Widget\Helper\Form\Element\Text(
        'bangumiID',
        null,
        null,
        _t('Bangumi 用户 ID'),
        _t('用于番组计划页面，填写你的 Bangumi 用户 ID（纯数字）')
    );

    $bangumiApiProxy = new \Typecho\Widget\Helper\Form\Element\Text(
        'bangumiApiProxy',
        null,
        null,
        _t('Bangumi API 反代地址'),
        _t('用于番组计划页面，填写 API 反代地址（如 https://api.example.com），不填则默认使用 https://api.bgm.tv')
    );

    $bangumiImgProxy = new \Typecho\Widget\Helper\Form\Element\Text(
        'bangumiImgProxy',
        null,
        null,
        _t('Bangumi 图片反代地址'),
        _t('用于番组计划页面，填写图片反代地址（如 https://img.example.com），不填则默认使用 Bangumi 原始图片地址')
    );

    $form->addInput($logoUrl);
    $form->addInput($headerImage);
    $form->addInput($enableIndexPage);
    $form->addInput($icpNumber);
    $form->addInput($icpLink);
    $form->addInput($accentColor);
    $form->addInput($bangumiID);
    $form->addInput($bangumiApiProxy);
    $form->addInput($bangumiImgProxy);

    $sidebarBlock = new \Typecho\Widget\Helper\Form\Element\Checkbox(
        'sidebarBlock',
        [
            'ShowRecentPosts'    => _t('显示最新文章'),
            'ShowRecentComments' => _t('显示最近回复'),
            'ShowCategory'       => _t('显示分类'),
            'ShowArchive'        => _t('显示归档'),
            'ShowOther'          => _t('显示其它杂项')
        ],
        ['ShowRecentPosts', 'ShowRecentComments', 'ShowCategory', 'ShowArchive', 'ShowOther'],
        _t('侧边栏显示')
    );

    $form->addInput($sidebarBlock->multiMode());
}


function themeFields($layout)
{
    $headerDisplay = new \Typecho\Widget\Helper\Form\Element\Radio('headerDisplay', array(
        '1' => _t('显示'),
        '0' => _t('不显示')
    ), '0', _t('(独立页面)是否显示在头部导航栏'), _t('默认不显示'));
    $layout->addItem($headerDisplay);
    
    $navIcon = new \Typecho\Widget\Helper\Form\Element\Text('navIcon', null, 'pages--outlined', _t('(独立页面)导航栏图标'), _t('请输入MDUI图标名称，例如: article--outlined，默认为 pages--outlined'));
    $layout->addItem($navIcon);
    
    $indexCardImage = new \Typecho\Widget\Helper\Form\Element\Text('indexCardImage', null, null, _t('(文章)首页文章卡片头图链接'), _t('请输入图片URL地址，作为该文章在首页展示的头图，如果不填写则使用默认图片'));
    $layout->addItem($indexCardImage);
}

// 替换Gravatar头像为国内代理源
function replaceGravatar($avatar)
{
    // 使用Cravatar（国内）作为Gravatar镜像源
    $gravatar_proxy = 'https://cravatar.cn/avatar/';
    
    // 确保替换所有可能的Gravatar URL格式
    $avatar = preg_replace('/(https?:\/\/)?(www|0|1|2|secure|cn)\.gravatar\.com\/avatar\//i', $gravatar_proxy, $avatar);
    
    return $avatar;
}

// 注册Gravatar替换钩子
\Typecho\Plugin::factory('Widget_Abstract_Comments')->gravatar = 'replaceGravatar';

// 编辑器样式卡片插入
function mduiStyleCardsEditor($content)
{
    $themeDir = __DIR__;
    include $themeDir . '/components/editor-style-cards.php';
}
\Typecho\Plugin::factory('admin/write-post.php')->content = 'mduiStyleCardsEditor';
\Typecho\Plugin::factory('admin/write-page.php')->content = 'mduiStyleCardsEditor';

/**
 * 解析 mdui 样式卡片短代码
 * [m3ui_error]文本[/m3ui_error]  -> 红色错误提示卡片
 * [m3ui_warning]文本[/m3ui_warning] -> 黄色警告提示卡片
 * [m3ui_success]文本[/m3ui_success] -> 绿色成功提示卡片
 * [m3ui_collapse title="标题"]内容[/m3ui_collapse] -> 折叠面板（支持嵌套）
 */
function parseMduiNotes($html)
{
    $maps = [
        '/\[m3ui_error\](.*?)\[\/m3ui_error\]/s'   => '<mdui-card variant="filled" class="mdui-note mdui-note-red"><mdui-icon name="error"></mdui-icon><span class="mdui-note-content">$1</span></mdui-card>',
        '/\[m3ui_warning\](.*?)\[\/m3ui_warning\]/s' => '<mdui-card variant="filled" class="mdui-note mdui-note-yellow"><mdui-icon name="lightbulb"></mdui-icon><span class="mdui-note-content">$1</span></mdui-card>',
        '/\[m3ui_success\](.*?)\[\/m3ui_success\]/s' => '<mdui-card variant="filled" class="mdui-note mdui-note-green"><mdui-icon name="check_circle"></mdui-icon><span class="mdui-note-content">$1</span></mdui-card>',
    ];
    $html = preg_replace(array_keys($maps), array_values($maps), $html);

    // 折叠面板短代码：使用栈式解析器处理嵌套（正则在跨 <p> 标签时会失败）
    $html = parseMduiCollapse($html);

    // 清理 Markdown 解析器在卡片间插入的 <br>
    $html = preg_replace('#</mdui-card>\s*<br\s*/?>\s*<mdui-card#', '</mdui-card><mdui-card', $html);
    // 清理包裹 mdui-list 的 <p> 标签（块级元素会被浏览器从 <p> 中拆出）
    $html = preg_replace('#<p>\s*(<mdui-list>.*?</mdui-list>)\s*</p>#s', '$1', $html);
    $html = preg_replace('#<p>\s*</p>#', '', $html);
    return $html;
}

/**
 * 栈式解析折叠面板短代码，支持嵌套和跨 <p> 标签
 */
function parseMduiCollapse($html)
{
    $pattern = '/\[m3ui_collapse(?:\s+title="([^"]*)")?\]|\[\/m3ui_collapse\]/';
    if (!preg_match($pattern, $html)) {
        return $html;
    }

    preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE);

    $stack = [];
    $result = '';
    $lastPos = 0;

    foreach ($matches[0] as $idx => $matchInfo) {
        $tag = $matchInfo[0];
        $pos = $matchInfo[1];

        // 将标签前的文本追加到当前上下文
        $textBefore = substr($html, $lastPos, $pos - $lastPos);
        if (!empty($stack)) {
            $stack[count($stack) - 1]['content'] .= $textBefore;
        } else {
            $result .= $textBefore;
        }

        if (strpos($tag, '[/') === 0) {
            // 闭合标签：弹出栈顶并生成 HTML
            if (!empty($stack)) {
                $item = array_pop($stack);
                $content = $item['content'];
                // 清理 Markdown 在边界插入的 </p> 和 <p>
                $content = preg_replace('#^\s*</p>\s*#i', '', $content);
                $content = preg_replace('#\s*<p>\s*$#i', '', $content);
                $content = trim($content);

                $htmlOut = '<mdui-list><mdui-collapse><mdui-collapse-item><mdui-list-item slot="header" icon="expand_more">' . htmlspecialchars($item['title']) . '</mdui-list-item><div class="m3ui-collapse-body">' . $content . '</div></mdui-collapse-item></mdui-collapse></mdui-list>';

                if (!empty($stack)) {
                    $stack[count($stack) - 1]['content'] .= $htmlOut;
                } else {
                    $result .= $htmlOut;
                }
            }
        } else {
            // 开标签：压入栈
            $title = !empty($matches[1][$idx][0]) ? $matches[1][$idx][0] : '点击展开';
            $stack[] = ['title' => $title, 'content' => ''];
        }

        $lastPos = $pos + strlen($tag);
    }

    // 追加最后一个标签之后的文本
    $remaining = substr($html, $lastPos);
    if (!empty($stack)) {
        // 未闭合的标签：原样输出内容
        while (!empty($stack)) {
            $item = array_pop($stack);
            $unclosed = '[m3ui_collapse title="' . $item['title'] . '"]' . $item['content'];
            if (!empty($stack)) {
                $stack[count($stack) - 1]['content'] .= $unclosed;
            } else {
                $result .= $unclosed;
            }
        }
        $result .= $remaining;
    } else {
        $result .= $remaining;
    }

    return $result;
}

/**
 * 获取文章封面图
 * 优先级：自定义字段 indexCardImage > 渐变占位（根据标题hash生成SVG）
 * 
 * @param Widget $widget 文章组件对象（$this 或 $archives）
 * @param Widget_Options $options 主题选项对象（$this->options）
 * @return string 图片URL或data URI
 */
function getCoverImage($widget, $options)
{
    // 1. 检查自定义字段 indexCardImage
    $customImage = trim((string)($widget->fields->indexCardImage ?? ''));
    if (!empty($customImage)) {
        return $customImage;
    }

    // 2. 生成渐变占位的 SVG data URI
    $title = $widget->title ?? '';
    $hash = md5($title);
    
    // Material 3 配色选择
    $colorPairs = [
        ['#E8DEF8', '#FCE4EC'],
        ['#D0BCFF', '#F8BBD0'],
        ['#C8E6C9', '#E8F5E9'],
        ['#BBDEFB', '#E3F2FD'],
        ['#FFF9C4', '#FFFDE7'],
        ['#FFCCBC', '#FBE9E7'],
        ['#E0BBE4', '#F3E5F5'],
        ['#A5D6A7', '#E8F5E9'],
        ['#90CAF9', '#BBDEFB'],
        ['#AB47BC', '#CE93D8'],
        ['#4DB6AC', '#B2DFDB'],
        ['#9575CD', '#B39DDB'],
        ['#F06292', '#F48FB1'],
        ['#7986CB', '#9FA8DA'],
        ['#A1887F', '#BCAAA4'],
        ['#F9A825', '#FFD54F'],
    ];
    
    $pairIndex = hexdec(substr($hash, 0, 8)) % count($colorPairs);
    $colors = $colorPairs[$pairIndex];
    
    $angle = (hexdec(substr($hash, 2, 4)) % 360);
    
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 200"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="0%" gradientTransform="rotate(' . $angle . ')"><stop offset="0%" style="stop-color:' . $colors[0] . '"/><stop offset="100%" style="stop-color:' . $colors[1] . '"/></linearGradient></defs><rect width="400" height="200" fill="url(#g)"/></svg>';
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}
