# ByUsi Studio · 一言 API

> 一个轻量、自托管的随机一言（Hitokoto）API，基于 PHP 实现，收录动画、漫画、游戏、文学、诗词、网易云热评等 12 大分类，为你的网站、小程序、应用提供优美的随机句子接口。

本一言库基于 [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle) 构建，感谢原作者及所有贡献者。

---

## 特性

- **纯 PHP 零依赖** —— 单文件 `index.php`，无需数据库，无需 Composer，上传即用。
- 📚 **12 大分类** —— 动画 / 漫画 / 游戏 / 文学 / 原创 / 网络 / 其他 / 影视 / 诗词 / 网易云 / 哲学 / 抖机灵。
- **多格式输出** —— 支持 `json` / `text` / `xml` / `ini` 四种响应格式。
- **灵活筛选** —— 支持单分类或多分类组合随机，参数向下兼容原一言协议。
- **性能优化** —— 先随机选定分类，再按需加载对应 JSON 文件，避免一次性载入全部数据。
- **内置文档界面** —— 无参数访问时自动重定向至 `docs.html`，提供分类预览、参数说明与在线测试 Playground。
- **子路径友好** —— 所有资源均使用相对路径，可部署在域名根目录或子路径（如 `/yy/`）。

---

## 项目结构

```
hitokoto/
├── index.php            # API 入口（核心逻辑）
├── docs.html            # HTML 文档界面 + 在线测试 Playground
├── categories.json      # 分类元数据（名称、描述、key、路径）
├── LICENSE              # AGPL-3.0 协议
├── README.md            # 本文件
└── sentences/           # 分类句子库（按分类拆分的 JSON 文件）
    ├── a.json           # 动画
    ├── b.json           # 漫画
    ├── c.json           # 游戏
    ├── d.json           # 文学
    ├── e.json           # 原创
    ├── f.json           # 网络
    ├── g.json           # 其他
    ├── h.json           # 影视
    ├── i.json           # 诗词
    ├── j.json           # 网易云
    ├── k.json           # 哲学
    └── l.json           # 抖机灵
```

---

## 🚀 快速开始

### 环境要求

- PHP ≥ 7.0（需启用 `json`、`mbstring` 扩展）
- 任意 Web 服务器（Apache / Nginx / IIS 等）或 PHP 内置服务器

### 部署方式

1. 将整个目录上传至 Web 服务器的站点根目录（或子目录）。
2. 确保 `sentences/` 目录及其下 JSON 文件可被 PHP 读取。
3. 访问 `https://your-domain/` 即可看到文档界面，或直接调用 API。

### 本地预览（PHP 内置服务器）

```bash
php -S 127.0.0.1:8080 -t .
```

浏览器打开 `http://127.0.0.1:8080/` 即可。

---

## 📖 API 文档

### 请求地址

```
GET /index.php
```

### 参数说明

| 参数名   | 类型   | 默认值       | 说明                                                                 |
| -------- | ------ | ------------ | -------------------------------------------------------------------- |
| `form`   | string | `text`       | **（保留原有）** 输出格式。可选：`text` / `json` / `xml` / `ini`    |
| `encode` | string | —            | **（新增）** 输出格式别名，优先级高于 `form`，便于兼容其他一言服务。 |
| `c`      | string | （全部分类） | **（新增）** 分类筛选。单个：`?c=a`；多个用英文逗号：`?c=a,b,c`。   |
| `charset`| string | `utf-8`      | **（新增）** 响应字符集，如 `?charset=gbk`。                         |

> **优先级规则**：当 `encode` 与 `form` 同时存在时，以 `encode` 为准。
> **无参数访问**：当请求未携带任何 API 参数时，将自动 `302` 重定向至 `./docs.html`。

### 输出格式

#### `text`（默认）

纯文本，仅返回一言内容。

```
樱花飘落的速度，是每秒五厘米哦。
```

#### `json`

返回完整元数据，同时保留兼容旧版的 `message` 字段。

```json
{
  "id": 1,
  "uuid": "9818ecda-9cbf-4f2a-9af8-8136ef39cfcd",
  "hitokoto": "与众不同的生活方式很累人呢，因为找不到借口。",
  "message": "与众不同的生活方式很累人呢，因为找不到借口。",
  "type": "a",
  "from": "幸运星",
  "from_who": null,
  "creator": "跳舞的果果",
  "creator_uid": 0,
  "reviewer": 0,
  "commit_from": "web",
  "created_at": "1468605909",
  "length": 22
}
```

#### `xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<response>
  <id>1</id>
  <uuid>9818ecda-9cbf-4f2a-9af8-8136ef39cfcd</uuid>
  <hitokoto><![CDATA[与众不同的生活方式很累人呢，因为找不到借口。]]></hitokoto>
  <message><![CDATA[与众不同的生活方式很累人呢，因为找不到借口。]]></message>
  <type>a</type>
  <from>幸运星</from>
  <from_who></from_who>
  <creator>跳舞的果果</creator>
  <created_at>1468605909</created_at>
  <length>22</length>
</response>
```

#### `ini`

```ini
; Random Hitokoto API
; Category: a
[hitokoto]
content = 与众不同的生活方式很累人呢，因为找不到借口。
[quote]
content = 与众不同的生活方式很累人呢，因为找不到借口。
[meta]
type = a
from = 幸运星
from_who =
creator = 跳舞的果果
created_at = 1468605909
length = 22
```

### 调用示例

| 场景                         | 请求                                            |
| ---------------------------- | ----------------------------------------------- |
| 最简调用（默认 text，全分类） | `GET /index.php`                                |
| JSON 格式 + 指定动画分类      | `GET /index.php?encode=json&c=a`                |
| 多分类 + XML                  | `GET /index.php?form=xml&c=b,c,d`               |
| INI 格式 + 诗词 + 网易云      | `GET /index.php?form=ini&c=i,j`                 |
| GBK 字符集                    | `GET /index.php?encode=json&charset=gbk`        |

### cURL 示例

```bash
# 纯文本一言
curl "https://your-domain/index.php"

# JSON 格式，限定动画与游戏分类
curl "https://your-domain/index.php?encode=json&c=a,c"
```

---

## 🗂️ 分类一览

| key | 名称   | 说明                                           |
| --- | ------ | ---------------------------------------------- |
| `a` | 动画   | Anime - 动画                                   |
| `b` | 漫画   | Comic - 漫画                                   |
| `c` | 游戏   | Game - 游戏                                    |
| `d` | 文学   | Literature - 主要收录现代文学：小说、散文、戏剧 |
| `e` | 原创   | Original - 原创                                |
| `f` | 网络   | Internet - 来自网络                            |
| `g` | 其他   | Other - 其他                                   |
| `h` | 影视   | Video - 影视                                   |
| `i` | 诗词   | Poem - 主要收录中国古代文学：诗、歌、词、赋、曲 |
| `j` | 网易云 | NCM - 主要收录网易云音乐热评                   |
| `k` | 哲学   | Philosophy - 哲学                              |
| `l` | 抖机灵 | Funny - 抖机灵                                 |

---

## 工作原理

为避免一次性将全部 12 个 JSON 文件载入内存，API 采用「先随机分类、再按需加载」的策略：

1. 根据参数 `c` 确定候选分类集合（未传则为全部 `a-l`）。
2. 过滤出本地实际存在的有效分类文件。
3. 从有效分类中**随机选取一个**分类。
4. 仅读取该分类对应的 `sentences/{key}.json` 文件。
5. 从句子数组中随机抽取一条返回。

这样无论总数据量多大，每次请求的内存占用都仅与单个分类文件大小相关。

---

## 🖥️ 文档界面

项目内置一个可视化文档界面 `docs.html`，提供：

- 分类卡片预览与实时句子数统计
- 参数说明表
- 调用示例展示
- 在线测试 Playground（支持格式 / 字符集 / 分类筛选，实时预览请求 URL 与响应结果）

直接访问站点根目录即可进入。

---

## 更新句子库

句子数据来自 [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle) 仓库的 `dist/` 目录，按分类拆分为 `a.json` ~ `l.json`。

更新方式：

```bash
# 克隆官方句子包
git clone https://github.com/hitokoto-osc/sentences-bundle.git /tmp/bundle

# 用 dist 目录下的分类文件覆盖本地 sentences/
cp /tmp/bundle/dist/*.json ./sentences/
```

更新后无需重启服务，下一次请求即生效。

---

## 许可协议

本项目采用 [GNU Affero General Public License v3.0](./LICENSE) 协议。

句子数据版权归各句子原作者所有，数据来源遵循 [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle) 的相关声明。

---

## 鸣谢

- [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle) —— 句子数据来源
- [hitokoto.cn](https://hitokoto.cn) —— 一言官方服务，本项目接口设计参考其协议

---

> Powered by ByUsi Studio
