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

/*
function themeFields($layout)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl',
        null,
        null,
        _t('站点LOGO地址'),
        _t('在这里填入一个图片URL地址, 以在网站标题前加上一个LOGO')
    );
    $layout->addItem($logoUrl);
}
*/

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

/**
 * 获取文章封面图
 * 优先级：自定义字段 indexCardImage > 默认图 img/empty.png
 * 
 * @param Widget $widget 文章组件对象（$this 或 $archives）
 * @param Widget_Options $options 主题选项对象（$this->options）
 * @return string 图片URL
 */
function getCoverImage($widget, $options)
{
    // 1. 检查自定义字段 indexCardImage
    $customImage = trim((string)($widget->fields->indexCardImage ?? ''));
    if (!empty($customImage)) {
        return $customImage;
    }

    // 2. 默认图片
    return $options->themeUrl('img/empty.png');
}
