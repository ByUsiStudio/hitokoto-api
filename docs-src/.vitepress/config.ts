import { defineConfig } from 'vitepress'
import { tabsMarkdownPlugin } from 'vitepress-plugin-tabs'

// 文档源位于 docs-src/，构建产物直出到项目根的 docs/（部署为 ./docs/）。
// base:'./' + cleanUrls:false + 单页：保证「构建一次，部署到任意子目录均正常访问」。
// 文档部署在 <根>/docs/，API（index.php）位于其父目录，客户端据此计算 API 基址。
export default defineConfig({
  title: 'ByUsi Studio · 一言 API',
  description: '收录动画、漫画、游戏、文学、诗词、网易云热评等 12 大分类的随机一言 API 文档',
  lang: 'zh-CN',
  base: './',
  cleanUrls: false,
  outDir: '../docs',
  lastUpdated: true,

  head: [
    ['meta', { name: 'viewport', content: 'width=device-width, initial-scale=1.0' }]
  ],

  markdown: {
    lineNumbers: false,
    config(md) {
      md.use(tabsMarkdownPlugin)
    }
  },

  themeConfig: {
    outline: { level: [2, 3], label: '本页目录' },
    docFooter: { prev: '上一页', next: '下一页' },
    lastUpdatedText: '最后更新',
    returnToTopLabel: '回到顶部',
    sidebarMenuLabel: '目录',
    darkModeSwitchLabel: '主题',
    lightModeSwitchTitle: '切换到浅色模式',
    darkModeSwitchTitle: '切换到深色模式',

    socialLinks: [
      { icon: 'github', link: 'https://github.com/ByUsiStudio/hitokoto-api' }
    ],

    footer: {
      message: '本一言库基于 hitokoto-osc/sentences-bundle 构建，感谢原作者及所有贡献者。',
      copyright: 'Powered by ByUsi Studio · AGPL-3.0'
    }
  }
})
