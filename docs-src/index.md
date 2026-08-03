---
layout: home

hero:
  name: 一言 API
  text: ByUsi Studio
  tagline: 收录动画、漫画、游戏、文学、诗词、网易云热评等 12 大分类，为你的网站、小程序、应用提供优美的随机句子接口。
  actions:
    - theme: brand
      text: 立即体验
      link: "#playground"
    - theme: alt
      text: 查看分类
      link: "#categories"

features:
  - icon: ⚡
    title: 纯 PHP 零依赖
    details: 无需数据库、无需 Composer，上传即用。模板化架构，新增输出格式只需一个文件。
  - icon: 🗂
    title: 12 大分类
    details: 动画 / 漫画 / 游戏 / 文学 / 原创 / 网络 / 其他 / 影视 / 诗词 / 网易云 / 哲学 / 抖机灵。
  - icon: 🔄
    title: 十种输出格式
    details: text / json / json5 / xml / ini / toml / js / sh / bat / ps1，覆盖前后端与脚本场景。
  - icon: 📂
    title: 子路径友好
    details: 所有资源相对路径，可部署在域名根目录或任意子路径（如 /yy/）。
---

## 支持的分类 {#categories}

共 12 个分类，点击卡片可快速筛选并跳转至在线测试。

<ClientOnly>
  <CategoryGrid />
  <template #fallback>
    <p style="color: var(--vp-c-text-3);">正在加载分类数据…</p>
  </template>
</ClientOnly>

## 参数说明

| 参数名    | 类型   | 默认值       | 说明                                                                                                                       |
| -------- | ------ | ------------ | -------------------------------------------------------------------------------------------------------------------------- |
| `form`   | string | `text`       | **（保留原有）** 输出格式。可选：`text` / `json` / `json5` / `xml` / `ini` / `toml` / `js` / `sh` / `bat` / `ps1`           |
| `encode` | string | —            | **（新增）** 输出格式别名，优先级高于 `form`，便于与其他一言服务兼容。支持与 `form` 相同的全部 10 种格式。                  |
| `c`      | string | （全部分类） | **（新增）** 分类筛选。单个：`?c=a`；多个用英文逗号：`?c=a,b,c`。不传则随机全部分类。                                        |
| `charset`| string | `utf-8`      | **（新增）** 响应字符集，如 `?charset=gbk`。                                                                                |

::: tip 优先级与重定向
- 当 `encode` 与 `form` 同时存在时，以 `encode` 为准。
- 请求未携带任何 API 参数时，将自动 `302` 重定向至本文档界面（`./docs/`）。
- 请求不支持的格式时返回 `400`，响应体为 JSON，包含 `supported` 数组列出当前所有可用格式。
:::

## 元信息说明

除 `text` 格式仅返回一言内容外，其余 9 种格式都会在响应中包含以下元信息：

| 字段          | 说明                                                                |
| ------------- | ------------------------------------------------------------------- |
| `api`         | 当前 API 的基础地址（根据请求自动计算，含协议、主机与所在子路径）。 |
| `repository`  | 开源仓库地址，包含 `gitee` / `github` / `codeberg` 三个镜像。       |
| `license`     | 开源协议名称，固定为 `AGPLv3`。                                     |
| `license_url` | 开源协议全文地址 `https://www.gnu.org/licenses/agpl-3.0.html`。     |

## 输出格式示例

以下为 10 种输出格式的请求与响应样例（URL 以 `https://your-domain/` 为例，实际由 API 根据请求自动计算）。

:::tabs variant:code

== TEXT
请求 `GET /`（默认 text，全分类随机），返回纯文本一言：

```text
樱花飘落的速度，是每秒五厘米哦。
```

== JSON
请求 `GET /?encode=json&c=a`，返回完整元数据（兼容旧版 `message` 字段）：

```json
{
  "id": 1,
  "hitokoto": "与众不同的生活方式很累人呢，因为找不到借口。",
  "message": "与众不同的生活方式很累人呢，因为找不到借口。",
  "type": "a",
  "from": "幸运星",
  "from_who": null,
  "creator": "跳舞的果果",
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

== JSON5
请求 `GET /?encode=json5`，带注释、未加引号键名、尾随逗号，便于人工阅读：

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

== XML
请求 `GET /?form=xml&c=b,c,d`，从漫画、游戏、文学三个分类随机：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<response>
  <hitokoto><![CDATA[与众不同的生活方式很累人呢，因为找不到借口。]]></hitokoto>
  <type>a</type>
  <from>幸运星</from>
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

== INI
请求 `GET /?form=ini&c=i,j`，适合 ini 解析场景（诗词 + 网易云）：

```ini
; Random Hitokoto API
; License: AGPLv3
[hitokoto]
content = 与众不同的生活方式很累人呢，因为找不到借口。
[meta]
type = a
from = 幸运星
[api]
url = https://your-domain/
[license]
name = AGPLv3
url = https://www.gnu.org/licenses/agpl-3.0.html
```

== TOML
请求 `GET /?encode=toml`：

```toml
# Random Hitokoto API
# License: AGPLv3

[hitokoto]
content = "与众不同的生活方式很累人呢，因为找不到借口。"

[meta]
type = "a"
from = "幸运星"
length = 22

[api]
url = "https://your-domain/"

[license]
name = "AGPLv3"
url = "https://www.gnu.org/licenses/agpl-3.0.html"
```

== JS
请求 `GET /?encode=js&c=a`，前端可通过 `<script src>` 直接引入并读取 `hitokoto` 变量：

```js
// Random Hitokoto API
// License: AGPLv3
var hitokoto = {
  hitokoto: "与众不同的生活方式很累人呢，因为找不到借口。",
  type: "a",
  from: "幸运星",
  api: "https://your-domain/",
  license: "AGPLv3"
};
```

== SH
请求 `GET /?encode=sh`，可直接 `eval` 用于 Shell 脚本：

```sh
#!/bin/sh
# Random Hitokoto API
# License: AGPLv3

HITOKOTO='与众不同的生活方式很累人呢，因为找不到借口。'
HITOKOTO_TYPE='a'
HITOKOTO_FROM='幸运星'
API_URL='https://your-domain/'
LICENSE='AGPLv3'
```

== BAT
请求 `GET /?encode=bat`，Windows 批处理变量，可直接 `call` 引入：

```bat
@echo off
REM Random Hitokoto API
REM License: AGPLv3

set "HITOKOTO=与众不同的生活方式很累人呢，因为找不到借口。"
set "HITOKOTO_TYPE=a"
set "HITOKOTO_FROM=幸运星"
set "API_URL=https://your-domain/"
set "LICENSE=AGPLv3"
```

== PS1
请求 `GET /?encode=ps1`，PowerShell 变量，驼峰命名：

```powershell
# Random Hitokoto API
# License: AGPLv3

$Hitokoto = '与众不同的生活方式很累人呢，因为找不到借口。'
$HitokotoType = 'a'
$HitokotoFrom = '幸运星'
$ApiUrl = 'https://your-domain/'
$License = 'AGPLv3'
```

:::

### 更多调用场景

| 场景                         | 请求                                                |
| ---------------------------- | --------------------------------------------------- |
| 最简调用（默认 text，全分类） | `GET /`                                             |
| JSON 格式 + 指定动画分类      | `GET /?encode=json&c=a`                             |
| 多分类 + XML                  | `GET /?form=xml&c=b,c,d`                            |
| 前端直接引入 JS 变量          | `<script src="/?encode=js&c=a"></script>`           |
| Shell 脚本中取值              | `eval "$(curl -s '/?encode=sh')"; echo $HITOKOTO`  |
| GBK 字符集                    | `GET /?encode=json&charset=gbk`                     |

## 在线测试 {#playground}

支持全部 10 种输出格式、字符集切换与分类筛选，实时预览请求 URL 与响应结果。

<ClientOnly>
  <Playground />
  <template #fallback>
    <p style="color: var(--vp-c-text-3);">正在加载在线测试组件…</p>
  </template>
</ClientOnly>

::: tip 工作原理
为避免一次性载入全部 12 个 JSON 文件，API 采用「先随机分类、再按需加载」策略：根据参数 `c` 确定候选分类 → 过滤出本地有效文件 → 随机选取一个分类 → 仅读取该分类对应的 `sentences/{key}.json` → 随机抽取一条并附加元信息。每次请求的内存占用仅与单个分类文件大小相关。
:::

## 鸣谢

- [hitokoto-osc/sentences-bundle](https://github.com/hitokoto-osc/sentences-bundle) —— 句子数据来源
- [hitokoto.cn](https://hitokoto.cn) —— 一言官方服务，本项目接口设计参考其协议
- [VitePress](https://vitepress.dev) · [vitepress-plugin-tabs](https://vitepress-plugins.sapphi.red/tabs/) —— 文档框架与标签页插件
