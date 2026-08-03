import type { Theme } from 'vitepress'
import DefaultTheme from 'vitepress/theme'
import { enhanceAppWithTabs } from 'vitepress-plugin-tabs/client'
import './custom.css'

// 扩展默认主题：注入 tabs 插件客户端、注册自定义交互组件。
// 组件用动态 import，便于代码分割与按需加载。
export default {
  extends: DefaultTheme,
  enhanceApp({ app }) {
    enhanceAppWithTabs(app)
    app.component('Playground', () => import('./components/Playground.vue'))
    app.component('CategoryGrid', () => import('./components/CategoryGrid.vue'))
  }
} satisfies Theme
