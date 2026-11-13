# ByUsi Studio · 一言 API

> 自托管的随机一言（Hitokoto）API，用 PHP 写成。按 12 个分类收录动画、漫画、游戏、文学、诗词、网易云热评等句子，网站、小程序和应用都能直接调用。

句子数据来自 [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle)，版权归各原作者所有。

---

## 特性

- 运行只需要 PHP 和 `json` 扩展，不用数据库，也不用 Composer。`index.php` 负责分发请求，`lib/helpers.php` 放共用函数，`templates/` 放各格式的模板。
- 分类有 12 个：动画、漫画、游戏、文学、原创、网络、其他、影视、诗词、网易云、哲学、抖机灵。
- 输出支持 `text` / `json` / `json5` / `xml` / `ini` / `toml` / `js` / `sh` / `bat` / `ps1` 十种格式。
- 除 `text` 外，其余格式的响应都会带上 API 地址、开源仓库地址和 AGPLv3 协议信息。
- 每种格式对应 `templates/` 下的一个文件。程序扫描目录得到支持的格式列表，加格式只要加文件。
- `c` 参数可以选单个分类，也可以用英文逗号选多个；不传就在全部 12 个分类里随机。
- 每次请求先随机挑一个分类，再读那一个 JSON 文件，不会一次载入全部数据。
- 内置文档页 `docs.html`，提供分类预览、参数说明、格式示例和在线测试。不带参数访问时会 302 跳转到它。
- API 和文档都用相对路径，放在域名根目录或任意子路径都能用。

---

## 项目结构

```
hitokoto/
├── index.php            # API 入口：参数处理、读取数据、准备元信息、分发模板；无参数时 302 到 docs.html
├── docs.html            # 文档和在线测试页
├── categories.json      # 分类元数据（名称、描述、key、路径）
├── push.bat             # 推送到多个远端的脚本
├── .gitmodules          # sentences-bundle 子模块配置
├── LICENSE              # AGPL-3.0 协议
├── README.md            # 本文件
├── lib/
│   └── helpers.php      # 共用函数：JSON 编码、API 基址、参数读取、Shell/PS1 转义
├── templates/           # 输出格式模板，每个格式一个文件，目录自动发现
│   ├── text.php         # 纯文本，只有句子内容
│   ├── json.php         # JSON，完整元数据
│   ├── json5.php        # JSON5，带注释、键名不加引号
│   ├── xml.php          # XML
│   ├── ini.php          # INI
│   ├── toml.php         # TOML
│   ├── js.php           # JavaScript 变量赋值
│   ├── sh.php           # POSIX Shell 变量
│   ├── bat.php          # Windows 批处理变量
│   └── ps1.php          # PowerShell 变量
└── sentences-bundle/    # 官方句子库，git 子模块
    ├── categories.json  # 分类元数据
    ├── version.json     # 句子库版本
    └── sentences/
        ├── a.json       # 动画
        ├── b.json       # 漫画
        ├── c.json       # 游戏
        ├── d.json       # 文学
        ├── e.json       # 原创
        ├── f.json       # 网络
        ├── g.json       # 其他
        ├── h.json       # 影视
        ├── i.json       # 诗词
        ├── j.json       # 网易云
        ├── k.json       # 哲学
        └── l.json       # 抖机灵
```

---

## 快速开始

### 环境要求

- PHP ≥ 7.0（已在 PHP 8.2 下验证；仅需 `json` 扩展，`mbstring` 为可选，未启用时自动降级）
- 任意 Web 服务器（Apache / Nginx / IIS 等）或 PHP 内置服务器

### 部署方式

1. 把 `index.php`、`docs.html`、`categories.json`、`lib/`、`templates/` 和 `sentences-bundle/` 上传到 Web 服务器。站点根目录或子目录都可以。
2. 确认 PHP 能读取 `sentences-bundle/sentences/` 和 `templates/` 下的文件。
3. 访问 `https://your-domain/` 会 302 跳到 `docs.html`，也可以直接调用 API。

> 文档页就是一个 HTML 文件，不用构建，放哪都能用。

### 本地预览

```bash
php -S 127.0.0.1:8080 -t .
```

然后打开 `http://127.0.0.1:8080/docs.html`。API 请求发往同一地址，例如 `http://127.0.0.1:8080/?encode=json&c=a`。

---

## API 文档

### 请求地址

```
GET /
```

> 服务器会把对该目录的请求解析为 `/index.php`，所以请求地址不用写出脚本名。

### 参数说明

| 参数名    | 类型   | 默认值       | 说明                                                                                          |
| --------- | ------ | ------------ | --------------------------------------------------------------------------------------------- |
| `form`    | string | `text`       | 输出格式，可选：`text` / `json` / `json5` / `xml` / `ini` / `toml` / `js` / `sh` / `bat` / `ps1` |
| `encode`  | string | 无            | 输出格式的别名，优先级高于 `form`，方便兼容其他一言服务。支持与 `form` 相同的 10 种格式。 |
| `c`       | string | 全部分类      | 分类筛选。单个分类用 `?c=a`，多个用英文逗号，如 `?c=a,b,c`。不传就在全部分类里随机。          |
| `charset` | string | `utf-8`      | 响应字符集，如 `?charset=gbk`。                                                  |

> `encode` 和 `form` 同时存在时，以 `encode` 为准。
> 请求不带任何 API 参数时，会 302 跳转到 `./docs.html`。
> 格式不存在时返回 400，响应体是 JSON，`supported` 数组列出当前可用的格式。

### 元信息说明

除 `text` 只返回句子内容外，其余 9 种格式的响应都包含这些元信息：

| 字段           | 说明                                                                  |
| -------------- | --------------------------------------------------------------------- |
| `api`          | 当前 API 的基础地址（根据请求自动计算，含协议、主机与所在子路径）。   |
| `repository`   | 开源仓库地址，包含 `gitee` / `github` / `codeberg` 三个镜像。         |
| `license`      | 开源协议名称，固定为 `AGPLv3`。                                       |
| `license_url`  | 开源协议全文地址 `https://www.gnu.org/licenses/agpl-3.0.html`。      |

### 输出格式

#### `text`（默认）

纯文本，只返回句子内容，不带元信息。

```
樱花飘落的速度，是每秒五厘米哦。
```

#### `json`

返回完整元数据，其中 `message` 字段用于兼容旧版调用。响应还带 API 地址、仓库和协议信息。

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
  "length": 22,
  "api": "https://your-domain/",
  "repository": {
    "gitee": "https://gitee.com/byusistudio/hitokoto-api",
    "github": "https://github.com/ByUsiStudio/hitokoto-api",
    "codeberg": "https://codeberg.org/ByUsiStudio/hitokoto-api"
  },
  "license": "AGPLv3",
  "license_url": "https://www.gnu.org/licenses/agpl-3.0.html"
}
```

#### `json5`

[JSON5](https://json5.org/) 格式，带注释、键名不加引号、允许尾随逗号，方便人阅读。字段和 `json` 一样。

```json5
{
  // 一言数据
  id: 1,
  hitokoto: "与众不同的生活方式很累人呢，因为找不到借口。",
  message: "与众不同的生活方式很累人呢，因为找不到借口。", // 兼容旧版
  type: "a",
  from: "幸运星",
  length: 22,
  // 元信息：API 地址、开源仓库、开源协议
  api: "https://your-domain/",
  repository: {
    gitee: "https://gitee.com/byusistudio/hitokoto-api",
    github: "https://github.com/ByUsiStudio/hitokoto-api",
    codeberg: "https://codeberg.org/ByUsiStudio/hitokoto-api",
  },
  license: "AGPLv3",
  license_url: "https://www.gnu.org/licenses/agpl-3.0.html",
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
  <api>https://your-domain/</api>
  <repository>
    <gitee>https://gitee.com/byusistudio/hitokoto-api</gitee>
    <github>https://github.com/ByUsiStudio/hitokoto-api</github>
    <codeberg>https://codeberg.org/ByUsiStudio/hitokoto-api</codeberg>
  </repository>
  <license>AGPLv3</license>
  <license_url>https://www.gnu.org/licenses/agpl-3.0.html</license_url>
</response>
```

#### `ini`

```ini
; Random Hitokoto API
; Category: a
; License: AGPLv3
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
[api]
url = https://your-domain/
[repository]
gitee = https://gitee.com/byusistudio/hitokoto-api
github = https://github.com/ByUsiStudio/hitokoto-api
codeberg = https://codeberg.org/ByUsiStudio/hitokoto-api
[license]
name = AGPLv3
url = https://www.gnu.org/licenses/agpl-3.0.html
```

#### `toml`

```toml
# Random Hitokoto API
# Category: a
# License: AGPLv3

[hitokoto]
content = "与众不同的生活方式很累人呢，因为找不到借口。"

[meta]
id = 1
uuid = "9818ecda-9cbf-4f2a-9af8-8136ef39cfcd"
type = "a"
from = "幸运星"
from_who = null
creator = "跳舞的果果"
creator_uid = 0
created_at = "1468605909"
length = 22

[api]
url = "https://your-domain/"

[repository]
gitee = "https://gitee.com/byusistudio/hitokoto-api"
github = "https://github.com/ByUsiStudio/hitokoto-api"
codeberg = "https://codeberg.org/ByUsiStudio/hitokoto-api"

[license]
name = "AGPLv3"
url = "https://www.gnu.org/licenses/agpl-3.0.html"
```

#### `js`

赋值给全局变量 `hitokoto`。前端用 `<script src="...?encode=js">` 引入后就能读到这个变量。

```javascript
// Random Hitokoto API
// Category: a
// License: AGPLv3
var hitokoto = {
  id: 1,
  uuid: "9818ecda-9cbf-4f2a-9af8-8136ef39cfcd",
  hitokoto: "与众不同的生活方式很累人呢，因为找不到借口。",
  message: "与众不同的生活方式很累人呢，因为找不到借口。",
  type: "a",
  from: "幸运星",
  from_who: null,
  creator: "跳舞的果果",
  creator_uid: 0,
  created_at: "1468605909",
  length: 22,
  api: "https://your-domain/",
  repository: {
    gitee: "https://gitee.com/byusistudio/hitokoto-api",
    github: "https://github.com/ByUsiStudio/hitokoto-api",
    codeberg: "https://codeberg.org/ByUsiStudio/hitokoto-api"
  },
  license: "AGPLv3",
  license_url: "https://www.gnu.org/licenses/agpl-3.0.html"
};
```

#### `sh`

POSIX Shell 变量，可以直接 `eval` 到脚本里。值用单引号包起来，内部单引号会转义。

```sh
#!/bin/sh
# Random Hitokoto API
# Category: a
# License: AGPLv3

HITOKOTO_ID='1'
HITOKOTO_UUID='9818ecda-9cbf-4f2a-9af8-8136ef39cfcd'
HITOKOTO='与众不同的生活方式很累人呢，因为找不到借口。'
HITOKOTO_TYPE='a'
HITOKOTO_FROM='幸运星'
HITOKOTO_FROM_WHO=''
HITOKOTO_CREATOR='跳舞的果果'
HITOKOTO_LENGTH='22'
API_URL='https://your-domain/'
REPO_GITEE='https://gitee.com/byusistudio/hitokoto-api'
REPO_GITHUB='https://github.com/ByUsiStudio/hitokoto-api'
REPO_CODEBERG='https://codeberg.org/ByUsiStudio/hitokoto-api'
LICENSE='AGPLv3'
LICENSE_URL='https://www.gnu.org/licenses/agpl-3.0.html'
```

#### `bat`

Windows 批处理变量，可以直接 `call` 引入。变量名和 `sh` 格式一致。

```bat
@echo off
REM Random Hitokoto API
REM Category: a
REM License: AGPLv3

set "HITOKOTO_ID=1"
set "HITOKOTO_UUID=9818ecda-9cbf-4f2a-9af8-8136ef39cfcd"
set "HITOKOTO=与众不同的生活方式很累人呢，因为找不到借口。"
set "HITOKOTO_TYPE=a"
set "HITOKOTO_FROM=幸运星"
set "HITOKOTO_FROM_WHO="
set "HITOKOTO_CREATOR=跳舞的果果"
set "HITOKOTO_LENGTH=22"
set "API_URL=https://your-domain/"
set "REPO_GITEE=https://gitee.com/byusistudio/hitokoto-api"
set "REPO_GITHUB=https://github.com/ByUsiStudio/hitokoto-api"
set "REPO_CODEBERG=https://codeberg.org/ByUsiStudio/hitokoto-api"
set "LICENSE=AGPLv3"
set "LICENSE_URL=https://www.gnu.org/licenses/agpl-3.0.html"
```

#### `ps1`

PowerShell 变量，使用驼峰命名。值用单引号包裹，内部单引号转义成两个单引号。

```powershell
# Random Hitokoto API
# Category: a
# License: AGPLv3

$HitokotoId = '1'
$HitokotoUuid = '9818ecda-9cbf-4f2a-9af8-8136ef39cfcd'
$Hitokoto = '与众不同的生活方式很累人呢，因为找不到借口。'
$HitokotoType = 'a'
$HitokotoFrom = '幸运星'
$HitokotoFromWho = ''
$HitokotoCreator = '跳舞的果果'
$HitokotoLength = '22'
$ApiUrl = 'https://your-domain/'
$RepoGitee = 'https://gitee.com/byusistudio/hitokoto-api'
$RepoGithub = 'https://github.com/ByUsiStudio/hitokoto-api'
$RepoCodeberg = 'https://codeberg.org/ByUsiStudio/hitokoto-api'
$License = 'AGPLv3'
$LicenseUrl = 'https://www.gnu.org/licenses/agpl-3.0.html'
```

### 调用示例

| 场景                         | 请求                                            |
| ---------------------------- | ----------------------------------------------- |
| 最简调用（默认 text，全分类） | `GET /`                                         |
| JSON 格式 + 指定动画分类      | `GET /?encode=json&c=a`                         |
| JSON5 格式 + 指定分类         | `GET /?encode=json5&c=a`                        |
| 多分类 + XML                  | `GET /?form=xml&c=b,c,d`                        |
| INI 格式 + 诗词 + 网易云      | `GET /?form=ini&c=i,j`                          |
| TOML 格式                     | `GET /?encode=toml`                             |
| 前端直接引入 JS 变量          | `<script src="/?encode=js&c=a"></script>`       |
| Shell 脚本中取值              | `eval "$(curl -s '/?encode=sh')"; echo $HITOKOTO` |
| GBK 字符集                    | `GET /?encode=json&charset=gbk`                 |

### cURL 示例

```bash
# 纯文本一言
curl "https://your-domain/"

# JSON 格式，限定动画与游戏分类
curl "https://your-domain/?encode=json&c=a,c"

# Shell 变量并直接取值
eval "$(curl -s 'https://your-domain/?encode=sh')"
echo "$HITOKOTO —— $HITOKOTO_FROM"
```

---

## 分类一览

| key | 名称   | 说明                                             |
| --- | ------ | ------------------------------------------------ |
| `a` | 动画   | Anime - 动画                                     |
| `b` | 漫画   | Comic - 漫画                                     |
| `c` | 游戏   | Game - 游戏                                      |
| `d` | 文学   | Literature - 主要收录现代文学：小说、散文、戏剧   |
| `e` | 原创   | Original - 原创                                  |
| `f` | 网络   | Internet - 来自网络                              |
| `g` | 其他   | Other - 其他                                     |
| `h` | 影视   | Video - 影视                                     |
| `i` | 诗词   | Poem - 主要收录中国古代文学：诗、歌、词、赋、曲   |
| `j` | 网易云 | NCM - 主要收录网易云音乐热评                     |
| `k` | 哲学   | Philosophy - 哲学                                |
| `l` | 抖机灵 | Funny - 抖机灵                                   |

---

## 工作原理

为了避免一次把 12 个 JSON 文件全部载入内存，API 先随机选分类，再按需读取：

1. 按参数 `c` 确定候选分类。没传就是全部 `a-l`。
2. 过滤掉本地不存在的分类文件。
3. 从剩下的分类里随机选一个。
4. 只读取这个分类对应的 `sentences-bundle/sentences/{key}.json`。
5. 从句子数组里随机抽一条，准备元信息（API 地址、仓库、协议）。
6. 引入 `templates/{form}.php` 输出对应格式。

这样不管句子库多大，每次请求占用的内存只和一个分类文件的大小有关。

> 支持的格式列表由扫描 `templates/` 目录得到。新增格式只需要在该目录加一个 `{form}.php`，不用改 `index.php`。

---

## 文档界面

`docs.html` 是一个独立的 HTML 文件，样式和脚本都写在文件里。页面包含：

- 12 个分类的卡片，以及每个分类的句子数量
- 参数说明和元信息说明
- 10 种输出格式的调用示例
- 在线测试区，可以选格式、字符集和分类，实时看到请求 URL 和响应

页面按当前地址算出 API 基址（`docs.html` 所在目录），所以放在域名根目录还是子目录都能正常请求。所有资源都是相对路径，同一个文件可以直接复制到任意子路径。

不需要构建步骤。无参数访问 `index.php` 时会 302 跳到这个页面。

---

## 更新句子库

句子数据来自 [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle)，按分类拆成 `a.json` 到 `l.json`，放在子模块 `sentences-bundle/sentences/` 下。

首次克隆时要带上子模块：

```bash
git clone --recurse-submodules <本仓库地址>
```

已经克隆过的仓库可以这样更新句子库：

```bash
git submodule update --remote sentences-bundle
```

更新后不用重启服务，下一次请求就会读到新数据。

---

## 新增输出格式

加一种输出格式分两步：

1. 在 `templates/` 下新建 `{form}.php`。模板里可以用 `index.php` 准备好的变量（`$hitokoto`、`$from`、`$type`、`$apiUrl`、`$repositories`、`$licenseName`、`$licenseUrl` 等），也可以用 `lib/helpers.php` 里的函数（`JSON_OUT`、`escSh()`、`escPs1()`）。
2. 不用注册。`index.php` 扫描目录后会自动把它加入 `supported` 列表。

模板只负责设置 `Content-Type` 头并输出内容。写法可以参考 `templates/json.php`。

---

## 许可协议

本项目采用 [GNU Affero General Public License v3.0](./LICENSE) 协议。

句子数据版权归各句子原作者所有，数据来源遵循 [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle) 的相关声明。

---

## 鸣谢

- [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle)：句子数据来源
- [hitokoto.cn](https://hitokoto.cn)：一言官方服务，本项目接口设计参考其协议

---

> Powered by ByUsi Studio
