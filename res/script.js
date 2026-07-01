// ==================== Ajax工具 ====================
const Ajax = {
    get: function(url) {
        return new Promise((rs, rj) => {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200 || xhr.status == 304)
                        rs(xhr.responseText);
                    else
                        rj(xhr.responseText);
                }
            }
            xhr.send();
        });
    },
    post: function(url, data) {
        return new Promise((rs, rj) => {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            // 设置Referer头，帮助Typecho验证请求来源
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200 || xhr.status == 304)
                        rs(xhr.responseText);
                    else
                        rj(xhr.responseText);
                }
            }
            xhr.send(data);
        });
    }
};

// 表单序列化
function objSerialize(form) {
    let res = [],
        current = null,
        i,
        len;
    for (i = 0, len = form.elements.length; i < len; i++) {
        current = form.elements[i];
        if (current.disabled) continue;
        switch (current.type) {
            case "file":
            case "submit":
            case "button":
            case "image":
            case "reset":
            case undefined:
                break;
            case "select-one":
            case "select-multiple":
                if (current.name && current.name.length) {
                    for (let k = 0, optionLen = current.options.length; k < optionLen; k++) {
                        let option = current.options[k];
                        if (option.selected) {
                            let optionValue = "";
                            if (option.hasAttribute) {
                                optionValue = option.hasAttribute("value") ? option.value : option.text;
                            } else {
                                optionValue = option.attributes["value"].specified ? option.value : option.text;
                            }
                            res.push(encodeURIComponent(current.name) + "=" + encodeURIComponent(optionValue));
                        }
                    }
                }
                break;
            case "checkbox":
            case "radio":
                if (current.checked && current.name && current.name.length) {
                    res.push(encodeURIComponent(current.name) + "=" + encodeURIComponent(current.value));
                }
                break;
            default:
                if (current.name && current.name.length) {
                    res.push(encodeURIComponent(current.name) + "=" + encodeURIComponent(current.value));
                }
        }
    }
    return res.join("&");
}

// 显示提示
function showToast(text) {
    if (typeof mdui !== 'undefined') {
        mdui.snackbar({
            message: text,
            placement: 'bottom',
            autoCloseDelay: 3000
        });
    }
}

const navigationDrawer = document.querySelector("mdui-navigation-drawer");
const openButton = document.querySelector(".open");
const closeButton = document.querySelector(".close");
const switchButton = document.querySelector(".mswitch")
const scrollToTopBtn = document.querySelector(".scrollToTopBtn")
const scrollToTopWrapper = document.querySelector(".scrollToTopBtn-wrapper")

// 为各按钮添加事件监听器（如果元素存在）
if (openButton) {
    openButton.addEventListener("click", () => {
        if (navigationDrawer) {
            navigationDrawer.open = true;
        }
    });
}

if (closeButton) {
    closeButton.addEventListener("click", () => {
        if (navigationDrawer) {
            navigationDrawer.open = false;
        }
    });
}

if (switchButton) {
    switchButton.addEventListener("click", function() {
        if (navigationDrawer) {
            if (navigationDrawer.open == false) {
                navigationDrawer.open = true;
            } else {
                navigationDrawer.open = false;
            }
        }
    });
}

// ==================== 全站搜索 ====================

// 搜索对话框
const searchDialog = document.getElementById('search-dialog');
const searchInputs = document.querySelectorAll('.search-trigger');
const searchCancel = document.getElementById('search-cancel');
const searchForm = document.getElementById('search-form');
const searchInput = document.getElementById('search-input');

// 绑定搜索图标点击事件
searchInputs.forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (searchDialog) {
            searchDialog.open = true;
            // 等待对话框展开后聚焦输入框
            setTimeout(function() {
                if (searchInput) searchInput.focus();
            }, 300);
        }
    });
});

// 取消按钮
if (searchCancel) {
    searchCancel.addEventListener('click', function() {
        if (searchDialog) searchDialog.open = false;
    });
}

// 表单提交
if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
        const keyword = searchInput ? searchInput.value.trim() : '';
        if (!keyword) {
            e.preventDefault();
            return;
        }
        // 跳转到搜索结果页
        e.preventDefault();
        const formAction = searchForm.getAttribute('action') || window.location.origin;
        const searchUrl = formAction + (formAction.includes('?') ? '&' : '?') + 's=' + encodeURIComponent(keyword);
        if (searchDialog) searchDialog.open = false;
        // 显示进度条
        const progress = document.getElementById('pjax-progress');
        if (progress) progress.style.display = 'block';
        // 使用PJAX加载（如果可用）
        if (typeof pjax !== 'undefined' && pjax.loadUrl) {
            window.history.pushState({url: searchUrl}, '', searchUrl);
            pjax.loadUrl(searchUrl);
        } else {
            window.location.href = searchUrl;
        }
    });
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth'
    });
}

if (scrollToTopBtn) {
    scrollToTopBtn.addEventListener("click", function() {
        scrollToTop();
    });
}

// 滚动监听：控制回到顶部按钮的显示/隐藏
const SCROLL_THRESHOLD = 500;
let scrollTicking = false;

function handleScrollVisibility() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (!scrollToTopWrapper) return;
    
    if (scrollTop > SCROLL_THRESHOLD) {
        scrollToTopWrapper.classList.add('is-visible');
    } else {
        scrollToTopWrapper.classList.remove('is-visible');
    }
    scrollTicking = false;
}

window.addEventListener('scroll', function() {
    if (!scrollTicking) {
        window.requestAnimationFrame(handleScrollVisibility);
        scrollTicking = true;
    }
}, { passive: true });

// 初始化检查
handleScrollVisibility();

// 生成文章目录
document.addEventListener('DOMContentLoaded', function() {
    // 检查是否在文章详情页
    if (document.querySelector('#main-post')) {
        generateTableOfContents();
        setupSmoothScrolling();
        setupScrollSpy();
    }
});

// 生成目录函数
function generateTableOfContents() {
    // 获取文章内容区域
    const content = document.querySelector('.main-post-content');
    if (!content) return;
    
    // 查找所有标题元素
    const headings = content.querySelectorAll('h1, h2, h3, h4, h5, h6');
    
    if (headings.length === 0) {
        // 如果没有找到标题，隐藏目录容器
        const tocContainer = document.getElementById('toc-container');
        if (tocContainer) {
            tocContainer.style.display = 'none';
            // 添加类标记无目录状态，用于CSS居中布局
            const contentContainer = document.querySelector('.post-content-container');
            if (contentContainer) {
                contentContainer.classList.add('no-toc');
            }
        }
        return;
    }
    
    // 创建目录内容
    const tocContent = document.getElementById('toc-content');
    const tocContainer = document.getElementById('toc-container');
    if (!tocContent) return;
    
    // 清空现有内容
    tocContent.innerHTML = '';
    
    // 存储标题层级信息
    const tocItems = [];
    
    // 为每个标题创建锚点并记录信息
    headings.forEach((heading, index) => {
        // 创建唯一ID作为锚点
        const id = 'toc-heading-' + index;
        heading.id = id;
        
        // 获取标题级别 (h1 -> level 1, h2 -> level 2, etc.)
        const level = parseInt(heading.tagName.charAt(1));
        
        // 生成 slug 化的类名
        const slug = heading.textContent.trim()
            .toLowerCase()
            .replace(/[^\w\u4e00-\u9fff]+/g, '-')
            .replace(/^-+|-+$/g, '') || 'heading-' + index;
        
        // 记录标题信息
        tocItems.push({
            id: id,
            level: level,
            text: heading.textContent.trim(),
            slug: slug,
            element: heading
        });
    });
    
    // 构建目录列表结构
    if (tocItems.length > 0) {
        let prevLevel = 0; // 记录前一个标题的级别
        
        tocItems.forEach(item => {
            // h2 级别与其他级别之间添加分隔线
            if (item.level === 2 && prevLevel !== 0 && prevLevel !== 2) {
                const divider = document.createElement('div');
                divider.className = 'divider';
                tocContent.appendChild(divider);
            }
            
            const listItem = document.createElement('mdui-list-item');
            
            // h1 独立样式，h2 加粗，h3 及以下不加粗
            let levelClass = '';
            if (item.level === 1) {
                levelClass = ' item-h1';
            } else if (item.level <= 2) {
                levelClass = ' item-bold';
            }
            listItem.className = `item item-${item.slug}${levelClass}`;
            listItem.setAttribute('rounded', '');
            listItem.setAttribute('alignment', 'center');
            listItem.setAttribute('headline-line', '1');
            listItem.setAttribute('href', '#' + item.id);
            listItem.setAttribute('data-target', item.id);
            listItem.setAttribute('data-level', item.level);
            
            // 设置标题文本
            listItem.textContent = item.text;
            
            // 添加点击事件
            listItem.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('data-target');
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // 滚动到目标元素
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // 更新活动状态
                    updateActiveTocItem(targetId);
                }
            });
            
            tocContent.appendChild(listItem);
            prevLevel = item.level;
        });
        
        // 确保目录容器显示
        if (tocContainer) {
            tocContainer.style.display = 'block';
        }
        
        // 移动端：将目录移到 post-meta-row 下方
        adjustTocPosition();
    }
}

// 根据屏幕尺寸调整目录位置
function adjustTocPosition() {
    const tocContainer = document.getElementById('toc-container');
    const postMetaRow = document.querySelector('.post-meta-row');
    const postContent = document.querySelector('.main-post-content');
    const contentContainer = document.querySelector('.post-content-container');
    if (!tocContainer) return;
    
    if (window.innerWidth <= 1299 && postMetaRow && postContent) {
        // 移动端：插入到 post-meta-row 之后，文章内容内部
        if (tocContainer.parentElement !== postContent) {
            postMetaRow.insertAdjacentElement('afterend', tocContainer);
        }
    } else if (contentContainer) {
        // 桌面端：放回 post-content-container
        if (tocContainer.parentElement !== contentContainer) {
            contentContainer.appendChild(tocContainer);
        }
    }
}

// 窗口尺寸变化时重新调整
let tocResizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(tocResizeTimer);
    tocResizeTimer = setTimeout(adjustTocPosition, 150);
});

// 设置平滑滚动
function setupSmoothScrolling() {
    const tocLinks = document.querySelectorAll('#toc-content a');
    
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-target');
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                // 滚动到目标元素
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // 更新活动状态
                updateActiveTocItem(targetId);
            }
        });
    });
}

// 设置交叉观察器以跟踪当前活动的目录项
// 滚动监听，更新目录激活状态
function setupScrollSpy() {
    const headings = Array.from(document.querySelectorAll('.main-post-content h1, .main-post-content h2, .main-post-content h3, .main-post-content h4, .main-post-content h5, .main-post-content h6'));
    
    if (headings.length === 0) return;
    
    let ticking = false;
    
    // 移除之前可能存在的滚动监听（通过标记函数）
    if (window._m3uiScrollHandler) {
        window.removeEventListener('scroll', window._m3uiScrollHandler, { passive: true });
    }
    
    // 监听滚动事件
    const onScroll = () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                updateActiveTocOnScroll(headings);
                ticking = false;
            });
            ticking = true;
        }
    };
    
    window._m3uiScrollHandler = onScroll;
    window.addEventListener('scroll', onScroll, { passive: true });
    // 初始化一次
    updateActiveTocOnScroll(headings);
}

// 根据滚动位置更新激活的目录项
function updateActiveTocOnScroll(headings) {
    const scrollY = window.scrollY;
    const offset = 100; // 偏移量，考虑固定头部等
    
    let currentId = '';
    
    // 找到当前滚动位置下方最近的标题
    for (let i = 0; i < headings.length; i++) {
        const heading = headings[i];
        const headingTop = heading.getBoundingClientRect().top + scrollY;
        
        if (headingTop - offset <= scrollY) {
            currentId = heading.id;
        } else {
            break;
        }
    }
    
    // 如果还没到第一个标题，不激活任何项
    if (!currentId && headings.length > 0) {
        const firstHeadingTop = headings[0].getBoundingClientRect().top + scrollY;
        if (firstHeadingTop - offset > scrollY) {
            // 在第一个标题之前，不激活任何项
            document.querySelectorAll('#toc-content .item').forEach(item => {
                item.classList.remove('toc-active');
            });
            return;
        }
    }
    
    // 更新激活状态
    if (currentId) {
        updateActiveTocItem(currentId);
    }
}

// 更新活动的目录项
function updateActiveTocItem(activeId) {
    // 移除所有活动状态
    document.querySelectorAll('#toc-content .item').forEach(item => {
        item.classList.remove('toc-active');
    });
    
    // 为当前活动的列表项添加活动状态
    const activeItem = document.querySelector(`#toc-content .item[data-target="${activeId}"]`);
    if (activeItem) {
        activeItem.classList.add('toc-active');
    }
}

// 评论表单提交处理 - 使用AJAX提交（兼容PJAX环境）
function setupCommentForm() {
    const commentForm = document.getElementById('comment-form');
    
    if (!commentForm) return;
    
    // 移除onsubmit属性
    commentForm.removeAttribute('onsubmit');
    
    // 拦截表单提交事件（每次都重新绑定，因为表单可能被移动）
    commentForm.onsubmit = function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('mdui-button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
        
        // 使用 FormData 收集表单数据，兼容 mdui Web Component
        const formData = new FormData(this);
        // 收集 mdui-text-field 的值（Web Component 不在 form.elements 中）
        this.querySelectorAll('mdui-text-field[name]').forEach(function(field) {
            const name = field.getAttribute('name');
            const value = field.value;
            if (name && value !== undefined) {
                formData.set(name, value);
            }
        });
        // 转换为 URL 编码字符串
        const params = new URLSearchParams(formData).toString();
        const actionUrl = this.getAttribute('action');
        
        Ajax.post(actionUrl, params).then(function(response) {
            // 检查响应中是否包含评论列表
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = response;
            
            const newComments = tempDiv.querySelector('#comments');
            if (newComments) {
                // 替换评论区内容
                const oldComments = document.querySelector('#comments');
                if (oldComments) {
                    oldComments.outerHTML = newComments.outerHTML;
                }
                
                // 检查是否提交成功（没有错误提示）
                const error = tempDiv.querySelector('.error');
                if (!error) {
                    // 提交成功，清空评论内容
                    const textArea = document.querySelector('#textarea');
                    if (textArea) {
                        textArea.value = '';
                    }
                    showToast('评论提交成功');
                }
            } else {
                // 如果响应中没有评论区，可能是成功提交但返回了其他页面
                // 检查是否有错误信息
                const errorMsg = tempDiv.querySelector('.error, .alert-danger');
                if (errorMsg) {
                    showToast(errorMsg.textContent.trim());
                } else {
                    showToast('评论提交成功');
                    // 尝试重新加载当前页面
                    window.location.reload();
                }
            }
            
            if (submitBtn) {
                submitBtn.disabled = false;
            }
            
            // 重新初始化评论表单
            setupCommentForm();
        }).catch(function(error) {
            showToast('评论提交失败，请稍后重试');
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        });
        
        return false;
    };
    
    // 拦截回复按钮点击，在表单移动后重新绑定事件
    document.querySelectorAll('.comment-reply a').forEach(function(replyLink) {
        if (replyLink.dataset.intercepted) return;
        replyLink.dataset.intercepted = 'true';
        replyLink.addEventListener('click', function() {
            // 让 TypechoComment.reply() 先执行移动表单
            setTimeout(function() {
                setupCommentForm();
            }, 50);
        });
    });
    
    // 拦截取消回复按钮
    const cancelLink = document.querySelector('.cancel-comment-reply a');
    if (cancelLink && !cancelLink.dataset.intercepted) {
        cancelLink.dataset.intercepted = 'true';
        cancelLink.addEventListener('click', function() {
            setTimeout(function() {
                setupCommentForm();
            }, 50);
        });
    }
}

// 初始化Spotlight图片查看器
function initSpotlight() {
    // 为文章内容中的图片添加spotlight类
    const images = document.querySelectorAll('.main-post-content img');
    images.forEach(img => {
        // 排除某些特定类的图片
        if (!img.classList.contains('bq') && !img.classList.contains('no-spotlight')) {
            img.classList.add('spotlight');
        }
    });
    
    // 如果Spotlight库存在，则初始化
    if (typeof Spotlight !== 'undefined') {
        Spotlight.init({
            selector: '.spotlight',
            showTitle: true,
            showDownload: false,
            showAutofit: true,
            showFullscreen: true,
            showControls: true,
            keyboard: true
        });
    }
    
    // 监听Spotlight的打开/关闭事件，防止页面滚动
    document.addEventListener('spotlightshow', function() {
        document.body.classList.add('spotlight-open');
    });
    
    document.addEventListener('spotlightclose', function() {
        document.body.classList.remove('spotlight-open');
    });
}

// 代码块复制按钮
function initCodeBlockCopy() {
    const pres = document.querySelectorAll('.main-post-content pre');
    pres.forEach(function(pre) {
        if (pre.querySelector('.code-copy-btn')) return;
        const btn = document.createElement('mdui-button-icon');
        btn.setAttribute('icon', 'content_copy');
        btn.className = 'code-copy-btn';
        btn.addEventListener('click', function() {
            navigator.clipboard.writeText(pre.textContent).then(function() {
                showToast('已复制');
            }).catch(function() {
                showToast('复制失败');
            });
        });
        pre.appendChild(btn);
    });
}

// 页面加载完成后初始化
function onPageReady() {
    // 初始化Spotlight
    initSpotlight();

    // 设置评论表单
    if (document.querySelector('#comments')) {
        setupCommentForm();
    }

    // 代码高亮：Prism hook 会自动设置 data-language，只需调用 highlightAll
    if (typeof Prism !== 'undefined') {
        Prism.highlightAll();
    }

    // 代码块复制按钮
    initCodeBlockCopy();
}

// ==================== 主题模式切换 ====================

function initThemeSwitch() {
    var group = document.getElementById('theme-switch');
    if (!group) return;

    // 从 localStorage 恢复选中状态
    var saved = localStorage.getItem('m3ui-theme');
    if (saved && (saved === 'light' || saved === 'dark' || saved === 'auto')) {
        group.value = saved;
    }

    // 避免重复绑定
    if (group.getAttribute('data-bound')) return;
    group.setAttribute('data-bound', '1');

    group.addEventListener('change', function(e) {
        var value = e.target.value;
        if (!value) return;
        applyTheme(value);
        localStorage.setItem('m3ui-theme', value);
    });
}

function applyTheme(mode) {
    document.documentElement.classList.remove('mdui-theme-auto', 'mdui-theme-light', 'mdui-theme-dark');
    document.documentElement.classList.add('mdui-theme-' + mode);
    // 更新 rail 按钮图标
    var railBtn = document.querySelector('.theme-toggle-rail');
    if (railBtn) {
        var icons = { light: 'light_mode', dark: 'dark_mode', auto: 'contrast' };
        railBtn.setAttribute('icon', icons[mode] || 'contrast');
    }
}

// navigation-rail 主题按钮：点击循环切换 light → auto → dark
function initThemeToggleRail() {
    var btn = document.querySelector('.theme-toggle-rail');
    if (!btn) return;
    if (btn.getAttribute('data-bound')) return;
    btn.setAttribute('data-bound', '1');

    btn.addEventListener('click', function() {
        var current = localStorage.getItem('m3ui-theme') || (document.documentElement.classList.contains('mdui-theme-dark') ? 'dark' : document.documentElement.classList.contains('mdui-theme-light') ? 'light' : 'auto');
        var order = ['light', 'auto', 'dark'];
        var idx = order.indexOf(current);
        var next = order[(idx + 1) % order.length];
        applyTheme(next);
        localStorage.setItem('m3ui-theme', next);
        // 同步抽屉里的 SegmentedButton
        var group = document.getElementById('theme-switch');
        if (group) group.value = next;
    });

    // 初始化图标
    var saved = localStorage.getItem('m3ui-theme');
    if (saved) applyTheme(saved);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        onPageReady();
        initThemeSwitch();
        initThemeToggleRail();
    });
} else {
    onPageReady();
    initThemeSwitch();
    initThemeToggleRail();
}

// ==================== PJAX 功能 ====================

// 初始化PJAX
document.addEventListener('DOMContentLoaded', function() {
    // 检查PJAX库是否已加载
    if (typeof Pjax !== 'undefined') {
        // 创建PJAX实例
        const pjax = new Pjax({
            // 只处理链接，不处理表单（避免影响评论提交）
            elements: "a:not([target='_blank']):not([no-pjax])",
            // 选择器 - 哪些内容会被PJAX替换
            selectors: [
                "title",
                "#pjax-container"
            ],
            // 切换元素
            switches: {
                "#pjax-container": function(oldEl, newEl, options) {
                    // 自定义切换动画
                    oldEl.style.opacity = '0';
                    setTimeout(function() {
                        oldEl.innerHTML = newEl.innerHTML;
                        oldEl.style.opacity = '1';
                        // 内容替换完成后触发自定义事件
                        oldEl.dispatchEvent(new CustomEvent('pjax:content-ready'));
                    }, 200);
                    return true;
                }
            },
            // 缓存
            cacheBust: false,
            // 超时时间
            timeout: 10000
        });

        // 统一的链接点击处理（避免多个监听器冲突）
        document.addEventListener('click', function(e) {
            // 检查是否点击了目录中的项，如果是则跳过（目录项用于页面内跳转）
            if (e.target.closest('#toc-content .item')) {
                return;
            }

            // 检查是否点击了mdui-chip
            const chip = e.target.closest('mdui-chip[href]');
            if (chip) {
                const href = chip.getAttribute('href');
                if (href) {
                    const target = chip.getAttribute('target');
                    const isExternal = href.startsWith('http') && !href.includes(window.location.hostname);
                    
                    if (target === '_blank' || isExternal) {
                        return; // 外部链接不拦截
                    }
                    
                    e.preventDefault();
                    e.stopPropagation();
                    window.history.pushState({url: href}, '', href);
                    pjax.loadUrl(href);
                    return;
                }
            }

            // 检查是否点击了mdui-card
            const card = e.target.closest('mdui-card[href]');
            if (card) {
                const href = card.getAttribute('href');
                if (href) {
                    const target = card.getAttribute('target');
                    const isExternal = href.startsWith('http') && !href.includes(window.location.hostname);
                    
                    if (target === '_blank' || isExternal) {
                        return; // 外部链接不拦截
                    }
                    
                    e.preventDefault();
                    e.stopPropagation();
                    window.history.pushState({url: href}, '', href);
                    pjax.loadUrl(href);
                    return;
                }
            }

            // 检查是否点击了分页按钮
            const pageButton = e.target.closest('mdui-button[href]');
            if (pageButton) {
                const href = pageButton.getAttribute('href');
                if (href) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.history.pushState({url: href}, '', href);
                    pjax.loadUrl(href);
                    return;
                }
            }

            // 检查是否点击了分页链接
            const pageLink = e.target.closest('.pageNav a[href]');
            if (pageLink) {
                const href = pageLink.getAttribute('href');
                if (href) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.history.pushState({url: href}, '', href);
                    pjax.loadUrl(href);
                    return;
                }
            }

            // 检查是否点击了导航链接
            const navItem = e.target.closest('mdui-navigation-rail-item[href], mdui-list-item[href]');
            if (navItem) {
                const href = navItem.getAttribute('href');
                if (href) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.history.pushState({url: href}, '', href);
                    pjax.loadUrl(href);
                    // 关闭导航抽屉
                    const navigationDrawer = document.querySelector('mdui-navigation-drawer');
                    if (navigationDrawer) {
                        navigationDrawer.open = false;
                    }
                    return;
                }
            }
        });

        // 处理浏览器后退/前进按钮
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.url) {
                pjax.loadUrl(e.state.url);
            } else {
                pjax.loadUrl(window.location.href);
            }
        });

        // PJAX开始加载时
        document.addEventListener('pjax:send', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            var progress = document.getElementById('pjax-progress');
            if (progress) progress.style.display = 'block';
        });

        // PJAX内容替换完成后重新初始化
        document.querySelector('#pjax-container').addEventListener('pjax:content-ready', function() {
            var progress = document.getElementById('pjax-progress');
            if (progress) progress.style.display = 'none';
            reinitializeAfterPjax();
        });

        // 兼容首次加载（非PJAX）
        document.addEventListener('pjax:success', function() {
            // 首次加载或无switch的情况
            if (!document.querySelector('#pjax-container')) {
                reinitializeAfterPjax();
            }
        });

        // PJAX加载失败时
        document.addEventListener('pjax:error', function() {
            console.error('PJAX加载失败');
        });
    }
});

// PJAX加载完成后重新初始化所有功能
function reinitializeAfterPjax() {
    // 重新初始化MDUI组件
    if (typeof mdui !== 'undefined') {
        // MDUI会自动处理大部分组件，但可能需要手动刷新某些组件
    }

    // 重新初始化导航抽屉
    const navigationDrawer = document.querySelector("mdui-navigation-drawer");
    if (navigationDrawer) {
        navigationDrawer.open = false;
    }

    // 重新初始化滚动到顶部按钮
    const scrollToTopBtn = document.querySelector(".scrollToTopBtn");
    if (scrollToTopBtn) {
        scrollToTopBtn.addEventListener("click", function() {
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });
        });
    }

    // 重新初始化代码高亮（Prism hook 会自动设置 data-language）
    if (typeof Prism !== 'undefined') {
        Prism.highlightAll();
    }

    // 代码块复制按钮
    initCodeBlockCopy();

    // 重新初始化Spotlight
    initSpotlight();

    // 如果在文章页面，重新生成目录
    const mainPost = document.querySelector('#main-post');
    if (mainPost) {
        generateTableOfContents();
        setupSmoothScrolling();
        setupScrollSpy();
    }

    // 重新设置评论表单
    if (document.querySelector('#comments')) {
        setupCommentForm();
    }

    // 重新初始化主题切换器
    initThemeSwitch();
    initThemeToggleRail();

    // 重新应用主题色
    const accentColor = document.querySelector('meta[name="theme-color"]')?.content || '#6200ee';
    if (typeof mdui !== 'undefined' && accentColor !== '#6200ee') {
        try {
            mdui.setColorScheme(accentColor, 'primary');
        } catch (e) {
            console.warn('Failed to set color scheme:', e);
        }
    }

    // 初始化番组计划页面
    initBangumi();
}

// 番组计划页面初始化
function initBangumi() {
    var page = document.getElementById('bangumi-page');
    if (!page) return;
    if (page.getAttribute('data-loaded')) return;
    page.setAttribute('data-loaded', 'true');

    var apiUrl = page.getAttribute('data-api-url');

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ===== 番组分页（每页 30 条）=====
    var BANGUMI_PAGE_SIZE = 30;
    var watchingData = [];
    var watchedData = [];
    var watchingPage = 1;
    var watchedPage = 1;

    function buildWatchingCard(item) {
        var subtitle = item.name && item.name !== item.name_cn
            ? '<p class="bangumi-subtitle">' + escapeHtml(item.name) + '</p>' : '';
        var chips = '';
        if (item.eps_count > 0) {
            chips += '<mdui-chip icon="movie" disabled elevated>' + item.ep_status + ' / ' + item.eps_count + '</mdui-chip>';
        }
        if (item.air_weekday) {
            chips += '<mdui-chip icon="schedule" disabled elevated>' + escapeHtml(item.air_weekday) + '</mdui-chip>';
        }
        if (item.air_date) {
            chips += '<mdui-chip icon="event" disabled elevated>' + escapeHtml(item.air_date) + '</mdui-chip>';
        }
        var progress = item.eps_count > 0
            ? '<div class="bangumi-progress-wrapper"><mdui-linear-progress value="' + item.ep_status + '" max="' + item.eps_count + '"></mdui-linear-progress></div>' : '';

        return '<mdui-card variant="elevated" class="bangumi-card" clickable href="' + escapeHtml(item.url) + '" target="_blank" rel="noopener">' +
            '<div class="bangumi-cover"><img src="' + escapeHtml(item.img) + '" alt="' + escapeHtml(item.name_cn) + '" loading="lazy"></div>' +
            '<div class="bangumi-info">' +
            '<h3 class="bangumi-title">' + escapeHtml(item.name_cn) + '</h3>' +
            subtitle +
            '<div class="bangumi-meta">' + chips + '</div>' +
            progress +
            '</div></mdui-card>';
    }

    function buildWatchedCard(item) {
        var subtitle = item.name && item.name !== item.name_cn
            ? '<p class="bangumi-subtitle">' + escapeHtml(item.name) + '</p>' : '';
        var chips = '';
        if (item.eps_count > 0) {
            chips += '<mdui-chip icon="movie" disabled elevated>' + item.ep_status + ' / ' + item.eps_count + '</mdui-chip>';
        }
        if (item.air_weekday) {
            chips += '<mdui-chip icon="schedule" disabled elevated>' + escapeHtml(item.air_weekday) + '</mdui-chip>';
        }
        if (item.air_date) {
            chips += '<mdui-chip icon="event" disabled elevated>' + escapeHtml(item.air_date) + '</mdui-chip>';
        }
        var progress = item.eps_count > 0
            ? '<div class="bangumi-progress-wrapper"><mdui-linear-progress value="' + item.ep_status + '" max="' + item.eps_count + '"></mdui-linear-progress></div>' : '';

        return '<mdui-card variant="elevated" class="bangumi-card" clickable href="' + escapeHtml(item.url) + '" target="_blank" rel="noopener">' +
            '<div class="bangumi-cover"><img src="' + escapeHtml(item.img) + '" alt="' + escapeHtml(item.name_cn) + '" loading="lazy"></div>' +
            '<div class="bangumi-info">' +
            '<h3 class="bangumi-title">' + escapeHtml(item.name_cn) + '</h3>' +
            subtitle +
            '<div class="bangumi-meta">' + chips + '</div>' +
            progress +
            '</div></mdui-card>';
    }

    // 渲染单页：网格 + 文章列表同款分页（上一页/下一页）
    function renderBangumiPage(container, data, page, cardBuilder, isWatched) {
        var totalPages = Math.max(1, Math.ceil(data.length / BANGUMI_PAGE_SIZE));
        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages;

        var start = (page - 1) * BANGUMI_PAGE_SIZE;
        var slice = data.slice(start, start + BANGUMI_PAGE_SIZE);

        var html = '<div class="bangumi-grid">';
        slice.forEach(function(item) { html += cardBuilder(item); });
        html += '</div>';

        // 分页控件（与 index.php / archive.php 文章列表保持一致）
        if (totalPages > 1) {
            html += '<div class="bangumi-page-nav">';
            if (page > 1) {
                html += '<mdui-button variant="filled" icon="arrow_back" data-bangumi-page="' + (page - 1) + '">上一页</mdui-button>';
            } else {
                html += '<span></span>';
            }
            html += '<span class="bangumi-page-info">第 ' + page + ' / ' + totalPages + ' 页</span>';
            if (page < totalPages) {
                html += '<mdui-button variant="filled" end-icon="arrow_forward" data-bangumi-page="' + (page + 1) + '" style="margin-left:auto;">下一页</mdui-button>';
            } else {
                html += '<span style="margin-left:auto;"></span>';
            }
            html += '</div>';
        }

        container.innerHTML = html;

        // 绑定分页按钮
        container.querySelectorAll('[data-bangumi-page]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var newPage = parseInt(btn.getAttribute('data-bangumi-page'), 10);
                if (isWatched) {
                    renderWatchedPage(newPage);
                } else {
                    renderWatchingPage(newPage);
                }
                requestAnimationFrame(function() {
                    var page = document.getElementById('bangumi-page');
                    if (page) page.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });
        });
    }

    function renderWatchingPage(page) {
        watchingPage = page;
        renderBangumiPage(document.getElementById('bangumi-watching'), watchingData, page, buildWatchingCard, false);
    }

    function renderWatchedPage(page) {
        watchedPage = page;
        renderBangumiPage(document.getElementById('bangumi-watched'), watchedData, page, buildWatchedCard, true);
    }

    function renderWatching(data) {
        watchingData = data;
        watchingPage = 1;
        document.getElementById('bangumi-watching-count').textContent = data.length;

        if (data.length === 0) {
            var container = document.getElementById('bangumi-watching');
            container.innerHTML = '<mdui-card variant="outlined" class="bangumi-empty">' +
                '<mdui-icon name="inbox"></mdui-icon>' +
                '<span>最近没有在看呢，看看以前都看过什么？</span>' +
                '<mdui-button variant="tonal" icon="check_circle" id="bangumi-switch-to-watched">查看已看</mdui-button>' +
                '</mdui-card>';
            var btn = document.getElementById('bangumi-switch-to-watched');
            if (btn) {
                btn.addEventListener('click', function() {
                    var tabs = document.querySelector('.bangumi-tabs');
                    if (tabs) tabs.value = 'watched';
                });
            }
            return;
        }

        renderWatchingPage(1);
    }

    function renderWatched(data) {
        watchedData = data;
        watchedPage = 1;
        document.getElementById('bangumi-watched-count').textContent = data.length;

        if (data.length === 0) {
            document.getElementById('bangumi-watched').innerHTML = '<mdui-card variant="outlined" class="bangumi-empty"><mdui-icon name="inbox"></mdui-icon><span>暂无已看的番剧</span></mdui-card>';
            return;
        }

        renderWatchedPage(1);
    }

    function renderError(containerId) {
        var container = document.getElementById(containerId);
        container.innerHTML = '<mdui-card variant="outlined" class="bangumi-empty"><mdui-icon name="cloud_off"></mdui-icon><span>数据加载失败，请稍后重试</span></mdui-card>';
    }

    // 并行请求在看和已看数据
    fetch(apiUrl + '?bangumi_api=watching')
        .then(function(res) { return res.json(); })
        .then(renderWatching)
        .catch(function() { renderError('bangumi-watching'); });

    fetch(apiUrl + '?bangumi_api=watched')
        .then(function(res) { return res.json(); })
        .then(renderWatched)
        .catch(function() { renderError('bangumi-watched'); });
}

// 初始页面加载时初始化
initBangumi();