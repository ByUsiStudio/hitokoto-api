# ByUsi Studio · 一言 API

> 一个轻量、自托管的随机一言（Hitokoto）API，基于 PHP 实现，收录动画、漫画、游戏、文学、诗词、网易云热评等 12 大分类，为你的网站、小程序、应用提供优美的随机句子接口。

本一言库基于 [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle) 构建，感谢原作者及所有贡献者。

---

## 特性

- **纯 PHP 零依赖** —— 无需数据库，无需 Composer，上传即用。核心逻辑由 `index.php`（分发器）、`lib/helpers.php`（共享辅助函数）与 `templates/`（独立输出模板）组成。
- **12 大分类** —— 动画 / 漫画 / 游戏 / 文学 / 原创 / 网络 / 其他 / 影视 / 诗词 / 网易云 / 哲学 / 抖机灵。
- **十种输出格式** —— 支持 `text` / `json` / `json5` / `xml` / `ini` / `toml` / `js` / `sh` / `bat` / `ps1`，覆盖前后端与脚本场景。
- **元信息自描述** —— 除 `text` 格式外，其余格式均在响应中包含 API 地址、开源仓库地址与开源协议（AGPLv3）。
- **模板可扩展** —— 每种输出格式对应 `templates/` 下一个独立文件，支持的格式列表由目录自动发现，新增格式只需新增模板文件。
- **灵活筛选** —— 支持单分类或多分类组合随机，参数向下兼容原一言协议。
- **性能优化** —— 先随机选定分类，再按需加载对应 JSON 文件，避免一次性载入全部数据。
- **内置文档界面** —— 基于 VitePress + vitepress-plugin-tabs 构建，无参数访问时自动重定向至 `./docs/`，提供分类预览、参数说明、多格式标签页示例与在线测试 Playground。
- **子路径友好** —— API 与文档资源均使用相对路径，VitePress 以 `base: './'` 构建，可部署在域名根目录或任意子路径（如 `/yy/`），同一份构建产物无需重新构建。

---

## 项目结构

```
hitokoto/
├── index.php            # API 入口（参数处理、数据加载、元信息准备、模板分发；无参数时 302 至 ./docs/）
├── categories.json      # 分类元数据（名称、描述、key、路径）
├── LICENSE              # AGPL-3.0 协议
├── README.md            # 本文件
├── package.json         # 文档构建脚本与依赖（vitepress / vitepress-plugin-tabs / vue）
├── docs/                # VitePress 构建产物（gitignored，部署为 ./docs/）
├── docs-src/            # VitePress 文档源码
│   ├── .vitepress/
│   │   ├── config.ts    # base:'./' / cleanUrls:false / outDir:'../docs' / tabs 插件
│   │   └── theme/       # 主题定制（蓝黄配色）+ Playground.vue / CategoryGrid.vue 组件
│   ├── index.md         # 单页文档源（hero + 分类 + 参数 + 格式示例 tabs + 在线测试）
│   └── router.php       # dev-only：给 .json 加 CORS 头（不部署）
├── lib/
│   └── helpers.php      # 共享辅助函数（JSON 编码、API 基址、Shell/PS1 转义）
├── templates/           # 输出格式模板（每格式一个文件，自动发现）
│   ├── text.php         # 纯文本（仅一言内容，无元信息）
│   ├── json.php         # JSON 完整元数据
│   ├── json5.php        # JSON5（带注释、未加引号键名）
│   ├── xml.php          # XML
│   ├── ini.php          # INI
│   ├── toml.php         # TOML
│   ├── js.php           # JavaScript 变量赋值
│   ├── sh.php           # POSIX Shell 变量
│   ├── bat.php          # Windows 批处理变量
│   └── ps1.php          # PowerShell 变量
└── sentences-bundle/
    └── sentences/
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

## 快速开始

### 环境要求

- PHP ≥ 7.0（已在 PHP 8.2 下验证；仅需 `json` 扩展，`mbstring` 为可选，未启用时自动降级）
- 任意 Web 服务器（Apache / Nginx / IIS 等）或 PHP 内置服务器

### 部署方式

1. **构建文档**（需 Node.js ≥ 18，仅在开发机执行一次）：在项目根运行 `npm install` 与 `npm run docs:build`，产物生成在 `docs/`。
2. 将 `index.php`、`categories.json`、`lib/`、`templates/`、`sentences/` 与构建出的 `docs/` 一起上传至 Web 服务器（站点根目录或子目录均可）。
3. 确保 `sentences/` 与 `templates/` 目录及其下文件可被 PHP 读取。
4. 访问 `https://your-domain/` 即会 302 跳转至文档界面，或直接调用 API。

> `docs/` 是 VitePress 构建产物（已 gitignore），同一份产物可部署到根目录或任意子路径而无需重新构建。

### 本地预览

API（PHP 内置服务器）+ 文档（VitePress dev）联调：

```bash
# 终端 A：API（用 router.php 给 .json 补 CORS 头，供 5173 跨域 fetch）
php -S 127.0.0.1:8080 docs-src/router.php -t .

# 终端 B：文档开发模式
npm install
npm run docs:dev      # http://localhost:5173/
```

文档构建预览（产物在 `docs/`）：

```bash
npm run docs:build
npm run docs:preview  # http://localhost:4173/
```

---

## API 文档

### 请求地址

```
GET /
```

> 服务器会将对该目录的请求自动解析为 `/index.php`，故请求地址无需显式写出脚本名。

### 参数说明

| 参数名    | 类型   | 默认值       | 说明                                                                                          |
| --------- | ------ | ------------ | --------------------------------------------------------------------------------------------- |
| `form`    | string | `text`       | **（保留原有）** 输出格式。可选：`text` / `json` / `json5` / `xml` / `ini` / `toml` / `js` / `sh` / `bat` / `ps1` |
| `encode`  | string | —            | **（新增）** 输出格式别名，优先级高于 `form`，便于与其他一言服务兼容。支持与 `form` 相同的全部 10 种格式。 |
| `c`       | string | （全部分类） | **（新增）** 分类筛选。单个：`?c=a`；多个用英文逗号：`?c=a,b,c`。不传则随机全部分类。          |
| `charset` | string | `utf-8`      | **（新增）** 响应字符集，如 `?charset=gbk`。                                                  |

> **优先级规则**：当 `encode` 与 `form` 同时存在时，以 `encode` 为准。
> **无参数访问**：当请求未携带任何 API 参数时，将自动 `302` 重定向至 `./docs.html`。
> **无效格式**：请求不支持的格式时返回 `400`，响应体为 JSON，包含 `supported` 数组列出当前所有可用格式。

### 元信息说明

除 `text` 格式仅返回一言内容外，其余 9 种格式都会在响应中包含以下元信息：

| 字段           | 说明                                                                  |
| -------------- | --------------------------------------------------------------------- |
| `api`          | 当前 API 的基础地址（根据请求自动计算，含协议、主机与所在子路径）。   |
| `repository`   | 开源仓库地址，包含 `gitee` / `github` / `codeberg` 三个镜像。         |
| `license`      | 开源协议名称，固定为 `AGPLv3`。                                       |
| `license_url`  | 开源协议全文地址 `https://www.gnu.org/licenses/agpl-3.0.html`。      |

### 输出格式

#### `text`（默认）

纯文本，仅返回一言内容，不包含任何元信息。

```
樱花飘落的速度，是每秒五厘米哦。
```

#### `json`

返回完整元数据，同时保留兼容旧版的 `message` 字段，并附带 API 地址、仓库与协议信息。

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

带注释、未加引号键名、尾随逗号的 [JSON5](https://json5.org/)，便于人工阅读。字段与 `json` 一致。

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

赋值给全局变量 `hitokoto`，前端可通过 `<script src="...?encode=js">` 直接引入并读取。

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

POSIX Shell 变量，可直接 `eval` 用于 Shell 脚本。值用单引号包裹并转义。

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

Windows 批处理变量，可直接 `call` 引入。变量名与 `sh` 对应。

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

PowerShell 变量，使用驼峰命名，值用单引号包裹并转义。

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

为避免一次性将全部 12 个 JSON 文件载入内存，API 采用「先随机分类、再按需加载」的策略：

1. 根据参数 `c` 确定候选分类集合（未传则为全部 `a-l`）。
2. 过滤出本地实际存在的有效分类文件。
3. 从有效分类中**随机选取一个**分类。
4. 仅读取该分类对应的 `sentences/{key}.json` 文件。
5. 从句子数组中随机抽取一条，准备元信息（API 地址、仓库、协议）。
6. 包含 `templates/{form}.php` 输出对应格式。

这样无论总数据量多大，每次请求的内存占用都仅与单个分类文件大小相关。

> 支持的格式列表由 `templates/` 目录自动扫描得到，新增格式只需在该目录新增一个 `{form}.php` 文件即可，无需修改 `index.php`。

---

## 文档界面

项目内置一个基于 **VitePress + vitepress-plugin-tabs** 的可视化文档站点，源码位于 `docs-src/`，构建产物位于 `docs/`（部署为 `./docs/`），提供：

- 分类卡片预览与实时句子数统计（`CategoryGrid` 组件）
- 参数说明表与元信息说明
- 10 种输出格式示例（`vitepress-plugin-tabs` 标签页展示）
- 在线测试 Playground（`Playground` 组件，支持全部 10 种格式 / 字符集 / 分类筛选，实时预览请求 URL 与响应结果）

文档界面在客户端根据当前地址自动计算 API 基址（取文档所在目录的上一级，即 `index.php` 所在目录），因此无论部署在根目录还是任意子路径均能正确显示与请求。VitePress 以 `base: './'` 构建，所有资源为相对路径，同一份产物可部署到任意子路径而无需重新构建。

### 构建文档

```bash
npm install
npm run docs:build
```

将 `docs/` 内容上传至服务器 `docs/` 目录即可。直接访问站点根目录会 302 跳转至 `./docs/`。

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

## 新增输出格式

得益于模板化架构，扩展一种新的输出格式只需两步：

1. 在 `templates/` 目录下新建 `{form}.php` 文件，文件内可使用 `index.php` 准备好的变量（`$hitokoto`、`$from`、`$type`、`$apiUrl`、`$repositories`、`$licenseName`、`$licenseUrl` 等）与 `lib/helpers.php` 中的辅助函数（`JSON_OUT`、`escSh()`、`escPs1()`）。
2. 完成。`index.php` 会自动发现该格式并加入 `supported` 列表，无需注册或修改入口逻辑。

模板文件只需负责设置 `Content-Type` 头并输出对应格式的内容即可，可参考现有的 `templates/json.php` 等实现。

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
