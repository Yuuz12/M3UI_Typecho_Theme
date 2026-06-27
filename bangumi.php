<?php
/**
 * 番组计划
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Bangumi API 封装类
 */
class BangumiAPI
{
    /** @var string Bangumi 用户 ID */
    private $userID;

    /** @var string 缓存目录 */
    private $cacheDir;

    /** @var int 缓存有效期（秒），默认 6 小时 */
    private $cacheTTL = 21600;

    /** @var string API 基础地址 */
    private $apiBase = 'https://api.bgm.tv';

    /** @var string 图片反代地址 */
    private $imgProxy = '';

    public function __construct($userID, $apiProxy = '', $imgProxy = '')
    {
        $this->userID = $userID;
        $this->cacheDir = __DIR__ . '/json';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
        if (!empty($apiProxy)) {
            $this->apiBase = rtrim($apiProxy, '/');
        }
        if (!empty($imgProxy)) {
            $this->imgProxy = rtrim($imgProxy, '/');
        }
    }

    /**
     * 替换图片地址为反代地址
     */
    private function replaceImgUrl($url)
    {
        if (empty($this->imgProxy) || empty($url)) {
            return $url;
        }
        // 将 bangumi 图片域名替换为反代地址
        return preg_replace('/^https?:\/\/[^\/]+/', $this->imgProxy, $url);
    }

    /**
     * 使用 curl 请求远程数据
     */
    private function curlGet($url)
    {
        if (!function_exists('curl_init')) {
            return @file_get_contents($url);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_REFERER, 'https://bgm.tv/');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $content === false) {
            return false;
        }
        return $content;
    }

    /**
     * 检查缓存是否有效
     */
    private function getCache($cacheFile)
    {
        if (!file_exists($cacheFile)) {
            return null;
        }
        $content = json_decode(file_get_contents($cacheFile), true);
        if (!is_array($content) || !isset($content['time']) || !isset($content['data'])) {
            return null;
        }
        if (time() - $content['time'] > $this->cacheTTL) {
            return null;
        }
        return $content;
    }

    /**
     * 写入缓存
     */
    private function setCache($cacheFile, $data)
    {
        $cache = array(
            'time' => time(),
            'data' => $data
        );
        @file_put_contents($cacheFile, json_encode($cache, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 由放送日期推算星期（v0 API 不再返回 air_weekday 字段）
     */
    private function getWeekday($date)
    {
        if (empty($date)) {
            return '';
        }
        $ts = strtotime($date);
        if ($ts === false) {
            return '';
        }
        $weekdays = array('周一', '周二', '周三', '周四', '周五', '周六', '周日');
        // ISO-8601: 1=周一，7=周日
        $w = (int) date('N', $ts);
        return isset($weekdays[$w - 1]) ? $weekdays[$w - 1] : '';
    }

    /**
     * 获取在看番剧列表
     */
    public function getWatching()
    {
        $cacheFile = $this->cacheDir . '/bangumi_watching.json';
        $cache = $this->getCache($cacheFile);

        if ($cache !== null) {
            return $cache['data'];
        }

        // v0 API：subject_type=2 动画，type=3 在看
        $url = $this->apiBase . '/v0/users/' . $this->userID . '/collections?subject_type=2&type=3&limit=50&offset=0';
        $raw = $this->curlGet($url);

        if ($raw === false || $raw === 'null' || $raw === '') {
            return array();
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['data']) || !is_array($data['data'])) {
            return array();
        }

        $result = array();

        foreach ($data['data'] as $item) {
            $subject = $item['subject'] ?? array();
            $date = $subject['date'] ?? '';

            $result[] = array(
                'name' => $subject['name'] ?? '',
                'name_cn' => $subject['name_cn'] ?? $subject['name'] ?? '',
                'url' => 'https://bgm.tv/subject/' . ($subject['id'] ?? ''),
                'ep_status' => $item['ep_status'] ?? 0,
                'eps_count' => $subject['eps'] ?? ($subject['total_episodes'] ?? 0),
                'air_date' => $date,
                'air_weekday' => $this->getWeekday($date),
                'img' => $this->replaceImgUrl(isset($subject['images']['large'])
                    ? str_replace('http://', 'https://', $subject['images']['large']) : ''),
                'id' => $subject['id'] ?? 0,
                'summary' => $subject['short_summary'] ?? ($subject['summary'] ?? ''),
            );
        }

        if (count($result) > 0) {
            $this->setCache($cacheFile, $result);
        }

        return $result;
    }

    /**
     * 获取已看番剧列表
     */
    public function getWatched()
    {
        $cacheFile = $this->cacheDir . '/bangumi_watched.json';
        $cache = $this->getCache($cacheFile);

        if ($cache !== null) {
            return $cache['data'];
        }

        // v0 API：subject_type=2 动画，type=2 看过；分页拉取全部
        $result = array();
        $limit = 50;
        $offset = 0;
        $maxPages = 20; // 安全上限，约 1000 条

        while ($maxPages-- > 0) {
            $url = $this->apiBase . '/v0/users/' . $this->userID . '/collections?subject_type=2&type=2&limit=' . $limit . '&offset=' . $offset;
            $raw = $this->curlGet($url);

            if ($raw === false || $raw === 'null' || $raw === '') {
                break;
            }

            $data = json_decode($raw, true);
            if (!is_array($data) || !isset($data['data']) || !is_array($data['data'])) {
                break;
            }

            foreach ($data['data'] as $item) {
                $subject = $item['subject'] ?? array();
                $date = $subject['date'] ?? '';

                $result[] = array(
                    'name' => $subject['name'] ?? '',
                    'name_cn' => $subject['name_cn'] ?? $subject['name'] ?? '',
                    'url' => 'https://bgm.tv/subject/' . ($subject['id'] ?? ''),
                    'ep_status' => $item['ep_status'] ?? 0,
                    'eps_count' => $subject['eps'] ?? ($subject['total_episodes'] ?? 0),
                    'air_date' => $date,
                    'air_weekday' => $this->getWeekday($date),
                    'img' => $this->replaceImgUrl(isset($subject['images']['large'])
                        ? str_replace('http://', 'https://', $subject['images']['large']) : ''),
                    'id' => $subject['id'] ?? 0,
                );
            }

            $returned = count($data['data']);
            $offset += $returned;
            if ($returned < $limit) {
                break;
            }
        }

        if (count($result) > 0) {
            $this->setCache($cacheFile, $result);
        }

        return $result;
    }
}

// AJAX 接口：返回 JSON 数据，不渲染页面
if (isset($_GET['bangumi_api'])) {
    header('Content-Type: application/json');
    // 释放 PHP 会话锁，避免阻塞后续 PJAX 请求
    @session_write_close();
    $api = new BangumiAPI($this->options->bangumiID, $this->options->bangumiApiProxy, $this->options->bangumiImgProxy);
    $type = $_GET['bangumi_api'];
    if ($type === 'watching') {
        echo json_encode($api->getWatching());
    } elseif ($type === 'watched') {
        echo json_encode($api->getWatched());
    } else {
        echo json_encode(array());
    }
    exit;
}

$this->need('components/header.php');
?>

<div id="main-post">
    <div class="main-post-title">
        <h2><?php $this->title() ?></h2>
    </div>

    <div class="collect" id="bangumi-page" data-api-url="<?php echo $this->permalink(); ?>">
        <!-- 统计卡片 -->
        <div class="bangumi-stats">
            <mdui-card variant="outlined" class="stat-card">
                <mdui-icon name="play_circle" class="stat-icon"></mdui-icon>
                <span class="stat-value" id="bangumi-watching-count">-</span>
                <span class="stat-label">在看</span>
            </mdui-card>
            <mdui-card variant="outlined" class="stat-card">
                <mdui-icon name="check_circle" class="stat-icon"></mdui-icon>
                <span class="stat-value" id="bangumi-watched-count">-</span>
                <span class="stat-label">已看</span>
            </mdui-card>
        </div>

        <!-- Tab 切换 -->
        <mdui-tabs class="bangumi-tabs" value="watching">
            <mdui-tab value="watching">在看</mdui-tab>
            <mdui-tab value="watched">已看</mdui-tab>

            <mdui-tab-panel slot="panel" value="watching">
                <div id="bangumi-watching" class="bangumi-loading">
                    <mdui-linear-progress></mdui-linear-progress>
                </div>
            </mdui-tab-panel>

            <mdui-tab-panel slot="panel" value="watched">
                <div id="bangumi-watched" class="bangumi-loading">
                    <mdui-linear-progress></mdui-linear-progress>
                </div>
            </mdui-tab-panel>
        </mdui-tabs>
    </div>
</div>

<style>
/* ===== 统计卡片 ===== */
.bangumi-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.bangumi-stats .stat-card {
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.bangumi-stats .stat-card:nth-child(1) .stat-icon { color: rgb(var(--mdui-color-primary)); }
.bangumi-stats .stat-card:nth-child(2) .stat-icon { color: rgb(var(--mdui-color-tertiary)); }

.bangumi-stats .stat-icon {
    font-size: 32px;
    margin-bottom: 4px;
}

.bangumi-stats .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: rgb(var(--mdui-color-on-surface));
    font-family: 'AlimamaFangYuanTiVF', sans-serif;
}

.bangumi-stats .stat-label {
    font-size: 13px;
    color: rgb(var(--mdui-color-on-surface-variant));
}

/* ===== Tab ===== */
.bangumi-tabs {
    margin-bottom: 24px;
}

/* ===== 加载中 ===== */
.bangumi-loading {
    padding: 32px 16px;
}

/* ===== 分页 ===== */
.bangumi-page-nav {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 24px;
}

.bangumi-page-nav .bangumi-page-info {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 13px;
    color: rgb(var(--mdui-color-on-surface-variant));
    white-space: nowrap;
}

/* ===== 番剧网格 ===== */
.bangumi-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    padding-top: 16px;
    min-width: 0;
}

.bangumi-card, .bangumi-card-mini {
    overflow: hidden;
    transition: box-shadow 0.2s ease, outline 0.2s ease;
    outline: 2px solid transparent;
    outline-offset: 2px;
    height: 100%;
    min-width: 0;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
}

.bangumi-card:hover,
.bangumi-card-mini:hover {
    outline: 2px solid rgb(var(--mdui-color-primary));
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.bangumi-card:focus-visible,
.bangumi-card-mini:focus-visible {
    outline: 2px solid rgb(var(--mdui-color-primary));
}

/* ===== 封面图 ===== */
.bangumi-cover {
    width: 100%;
    aspect-ratio: 3 / 4;
    overflow: hidden;
}

.bangumi-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* ===== 卡片信息 ===== */
.bangumi-info {
    padding: 16px;
    overflow: hidden;
    min-width: 0;
}

.bangumi-title {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 700;
    color: rgb(var(--mdui-color-on-surface));
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}

.bangumi-subtitle {
    margin: 0 0 8px 0;
    font-size: 12px;
    color: rgb(var(--mdui-color-on-surface-variant));
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.bangumi-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
}

.bangumi-meta mdui-chip {
    --mdui-chip-label-text-size: 11px;
    --mdui-chip-leading-icon-size: 12px;
}

.bangumi-progress-wrapper {
    margin-top: 8px;
}

/* ===== 空状态 ===== */
.bangumi-empty {
    padding: 48px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    margin: 24px 0;
}

.bangumi-empty mdui-icon {
    font-size: 48px;
    color: rgb(var(--mdui-color-outline));
}

.bangumi-empty span {
    color: rgb(var(--mdui-color-on-surface-variant));
    font-size: 14px;
}

/* ===== 响应式 ===== */
@media (max-width: 768px) {
    .bangumi-stats {
        grid-template-columns: 1fr;
    }

    .bangumi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 480px) {
    .bangumi-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php $this->need('components/sidebar.php'); ?>
<?php $this->need('components/footer.php'); ?>
