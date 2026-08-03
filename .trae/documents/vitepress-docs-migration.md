# 将 docs.html 迁移为 VitePress 文档站

## Context（背景）

当前 `docs.html` 是一个 845 行的单文件静态文档页（hero + 分类卡片 + 参数表 + 调用示例 + 在线测试 Playground），通过运行时 `getApiBase()` 计算当前目录来支持「整个项目部署在任意子路径（`/`、`/yy/`、`/foo/bar/`）」。

用户要求改用 **VitePress + vitepress-plugin-tabs** 重写文档，且必须保留「构建一次、部署到任意子目录均正常访问」的能力。

迁移后：文档从项目根级单文件变为 VitePress 构建的多文件静态站，部署在项目根下的 `docs/` 子目录；`index.php` 无参数时重定向到 `./docs/`；交互能力（分类卡片实时条数、Playground 在线测试）由自定义 Vue 组件承载；输出格式示例用 `vitepress-plugin-tabs` 的 `variant:code` 标签页展示。

## 目标目录结构（用户已选：源码在 docs-src/，直出 docs/，gitignore 产物）

```
hitokoto/
├── docs-src/                          # VitePress 源码（纳入 git）
│   ├── .vitepress/
│   │   ├── config.ts                  # base:'./'、cleanUrls:false、outDir:'../docs'、tabsMarkdownPlugin
│   │   ├── cache/                     # gitignored
│   │   └── theme/
│   │       ├── index.ts               # 扩展默认主题 + enhanceAppWithTabs + 注册组件
│   │       ├── custom.css             # 蓝(#1E88E5)+黄(#F0B429) 配色、500ms 过渡、禁流光
│   │       ├── components/
│   │       │   ├── Playground.vue
│   │       │   └── CategoryGrid.vue
│   │       └── composables/
│   │           └── useDocsStore.ts
│   └── index.md                       # 单页文档源
├── docs/                              # 构建产物（gitignored，部署为 ./docs/）
│   ├── index.html
│   └── assets/
├── package.json                       # devDeps: vitepress、vitepress-plugin-tabs、vue
├── index.php                          # 重定向 ./docs.html → ./docs/
├── categories.json / sentences/ / templates/ / lib/  # 不变
└── README.md                          # 更新文档界面段
```

## 关键技术决策

1. **`base: './'` + `cleanUrls: false` + 单页（仅 `index.md`）**：VitePress 输出相对资源路径（`./assets/...`），同一份产物在 `/docs/`、`/yy/docs/`、`/foo/bar/docs/` 均能加载资源。单页避免多页 SPA 在相对 base 下硬刷新深层路由 404。**页内跳转一律用锚点 `#xxx`，禁止绝对路由 `/xxx`。**

2. **API 基址「上一级」逻辑**：文档从根级迁到 `docs/` 子目录，API（`index.php`）位于文档的父目录。客户端 `computeApiBase()`：取当前页目录 → 去尾斜杠 → 再取上一级目录。开发模式（`import.meta.env.DEV`）直接返回 `http://127.0.0.1:8080/` 以便联调本地 PHP。已验证 `/docs/`、`/yy/docs/`、`/foo/bar/docs/`、带/不带 `index.html`、带/不带尾斜杠均正确（无尾斜杠由服务器 301 补齐）。

3. **所有 fetch 改用 apiBase 拼接**（原 `./categories.json` 相对路径在子目录下会 404）：
   - `${apiBase}categories.json`、`${apiBase}sentences/${key}.json`
   - Playground 请求：`${apiBase}?encode=...&c=...`

4. **vitepress-plugin-tabs 当前 API**：
   - config.ts：`markdown: { config(md) { md.use(tabsMarkdownPlugin) } }`（`import { tabsMarkdownPlugin } from 'vitepress-plugin-tabs'`）
   - theme/index.ts：`import { enhanceAppWithTabs } from 'vitepress-plugin-tabs/client'`，在 `enhanceApp({ app })` 内调用 `enhanceAppWithTabs(app)`
   - 语法：`:::tabs variant:code == JSON \n```json ...``` \n == XML \n ... :::`
   - **不使用 `key:` 持久化**，避免 localStorage 在 SSR 端缺失导致水合不一致。

5. **SSR/水合安全**：`Playground.vue`、`CategoryGrid.vue` 在 `index.md` 中用 `<ClientOnly>` 包裹（带占位 fallback）；`useDocsStore` 的 `apiBase` 用 `ref('')` 初始空串、在 `onMounted` 内惰性计算；所有 `window`/`location`/`fetch`/`navigator.clipboard`/`performance`/`scrollIntoView` 仅在 `onMounted` 或事件回调中使用。

6. **示例 URL 改静态**：原 `fillExampleUrls()` 动态填充示例代码块 URL 的行为不再保留，示例改用静态 `https://your-domain/` 占位（与 README 一致）。动态 URL 需求由 Playground 的实时预览覆盖。

7. **产物 gitignore**：`/docs/`（锚定根，仅忽略构建产物目录）、`docs-src/.vitepress/cache/`。开发机 `npm run docs:build` 后上传 `docs/` 内容到服务器 `docs/`。

## 文件清单

### 新建

| 文件 | 要点 |
|---|---|
| `package.json` | `devDeps`：`vitepress@^1`、`vitepress-plugin-tabs`、`vue`；`scripts`：`docs:dev`→`vitepress dev docs-src`、`docs:build`→`vitepress build docs-src`、`docs:preview`→`vitepress preview docs-src` |
| `docs-src/.vitepress/config.ts` | `base:'./'`、`cleanUrls:false`、`outDir:'../docs'`、`title`/`description`、`markdown.config` 注入 `tabsMarkdownPlugin` |
| `docs-src/.vitepress/theme/index.ts` | `extends: DefaultTheme`；`enhanceApp` 内 `enhanceAppWithTabs(app)` + 注册 `Playground`/`CategoryGrid`（`app.component(name, () => import('./components/X.vue'))`）；`import './custom.css'` |
| `docs-src/.vitepress/theme/custom.css` | 覆盖 `--vp-c-brand-1/2/3` 为蓝 `#1E88E5` 系；黄 `#F0B429` 系点缀；全局 `--vp-transition: 500ms`；禁用 glow/streamer；hero/卡片/playground 自定义样式（移植原 `docs.html` light yellow+blue 审美，保留 500ms 过渡与入场动画，去流光） |
| `docs-src/.vitepress/theme/composables/useDocsStore.ts` | 共享状态：`apiBase: Ref<string>`（onMounted 计算）、`selectedCats: Set`（响应式）、`runTestSignal: Ref<number>`（卡片点击→Playground watch 触发测试）；`computeApiBase()`、`requestTestFromCat(key)` |
| `docs-src/.vitepress/theme/components/CategoryGrid.vue` | fetch `${apiBase}categories.json` + 各 `sentences/{key}.json` 显示实时条数；卡片点击 → 清空 selectedCats + 选中该 key + `document.getElementById('playground').scrollIntoView()` + `requestTestFromCat(key)`（520ms 后由 Playground watch 触发）；保留原配色动画（500ms、无流光） |
| `docs-src/.vitepress/theme/components/Playground.vue` | 移植原 Playground 全部交互：格式 select(10) / 字符集 select(2) / 分类 chips(从 categories 渲染) / URL 预览(apiBase 拼接) / 发送请求 / 结果展示（JSON 渲染名言+《from》+原文，其余格式原文）/ 复制 URL/结果 / 状态标签；watch `runTestSignal` 同步 selectedCats 并 `runTest()` |
| `docs-src/index.md` | hero frontmatter（`layout: doc` 或自定义 hero）→ `<ClientOnly><CategoryGrid /></ClientOnly>`（带"正在加载…"fallback）→ 参数说明 markdown 表 + 元信息说明 → 调用示例 `:::tabs variant:code`（10 格式各一 tab：请求行 + 静态响应样例）→ `<ClientOnly><Playground /></ClientOnly>` → 页脚鸣谢 |

### 修改

| 文件 | 修改 |
|---|---|
| `index.php` | 第 39 行 `header('Location: ./docs.html', true, 302);` → `header('Location: ./docs/', true, 302);` |
| `.gitignore` | 末尾追加：`/docs/` 与 `docs-src/.vitepress/cache/` |
| `README.md` | 「项目结构」加 `docs-src/`、`docs/`、`package.json`；「文档界面」段改为 VitePress 文档 + `npm run docs:build` + 上传 `docs/` 到服务器；「子路径友好」补充 `base:'./'` 说明；「快速开始」加 Node 构建说明 |

### 删除

| 文件 | 说明 |
|---|---|
| `docs.html` | 子目录集成测试通过后删除 |

## 实施顺序

1. 建 `package.json` → `npm install`（装 vitepress + vitepress-plugin-tabs + vue）。
2. 建 `config.ts`、`theme/index.ts`、`theme/custom.css`。
3. 建 `useDocsStore.ts`（apiBase 惰性计算 + selectedCats + runTestSignal）。
4. 建 `CategoryGrid.vue`、`Playground.vue`（移植原 `docs.html` 交互，fetch 改 apiBase 拼接）。
5. 建 `docs-src/index.md`（hero + ClientOnly 组件 + 参数表 + tabs 示例 + 页脚）。
6. 改 `index.php` 重定向、`.gitignore`、`README.md`。
7. `npm run docs:dev` 联调（配 PHP + dev-only `router.php` 加 `.json` 的 CORS 头）。
8. `npm run docs:build` + 子目录集成测试。
9. 通过后删除 `docs.html`。

## 验证

### 构建与预览
```powershell
npm install
npm run docs:build        # 产物 -> docs/
npm run docs:preview      # 开 http://localhost:4173/，验证渲染/tabs/组件挂载
```
（preview 服务器无 categories/sentences，Playground 请求失败属预期，仅验证构建完整性。）

### 开发模式（真实 API 数据）
```powershell
# 终端 A：PHP 内置服务器 + dev router 给 .json 加 CORS 头
php -S 127.0.0.1:8080 docs-src/router.php -t .
# 终端 B：VitePress dev
npm run docs:dev          # http://localhost:5173/
```
`docs-src/router.php`（dev-only，可选加入清单）：对 `.json` 请求 `header('Access-Control-Allow-Origin: *'); return false;` 交还静态处理器。开发模式 `computeApiBase` 走 DEV 分支返回 `http://127.0.0.1:8080/`，分类卡片与 Playground 均可真实工作。

### 子目录集成测试（关键：证明「构建一次、任意子目录部署」）
1. `npm run docs:build`。
2. 准备部署镜像 `D:\hitokoto-deploy\`（拷贝 `index.php`、`categories.json`、`lib/`、`templates/`、`sentences/`，并把 `docs/*` 内容放入 `docs/`）。
3. **根部署**：`php -S 127.0.0.1:8080 -t D:\hitokoto-deploy`，开 `http://127.0.0.1:8080/` → 302 到 `/docs/` → 分类卡片显示真实条数 → Playground 请求 `http://127.0.0.1:8080/?encode=json` 返回并渲染名言。
4. **子路径 `/yy/`**：镜像拷到 `D:\webroot\yy\`，`php -S 127.0.0.1:8080 -t D:\webroot`，开 `http://127.0.0.1:8080/yy/` → 302 到 `/yy/docs/` → 卡片 fetch `http://127.0.0.1:8080/yy/categories.json`（200）→ Playground URL 预览 `http://127.0.0.1:8080/yy/?encode=json`。
5. **三层 `/foo/bar/`**：再嵌一层镜像，验证 `/foo/bar/docs/` 下 apiBase 算到 `http://127.0.0.1:8080/foo/bar/`。
6. **尾斜杠**：`/yy/docs`（无斜杠）应 301 到 `/yy/docs/`。

通过 4–6 即证明子目录部署成立。可选用浏览器自动化（browser_use）抓取分类卡片条数与 Playground 响应做回归断言。
