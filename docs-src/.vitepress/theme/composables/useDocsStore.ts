import { ref } from 'vue'

/**
 * 文档全局共享状态（模块级单例）。
 *
 * - apiBase：API 基址（index.php 所在目录），在 onMounted 内惰性计算，避免 SSR 读 window。
 *   文档部署在 <根>/docs/，API 在其父目录，故 computeApiBase 取当前目录的上一级。
 * - selectedCats：当前选中的分类（CategoryGrid 卡片点击与 Playground chips 共享）。
 * - runTestSignal：卡片点击后递增，Playground watch 之并触发一次测试。
 */
const apiBase = ref('')
const selectedCats = ref<string[]>([])
const runTestSignal = ref(0)

/** 开发模式直连本地 PHP（127.0.0.1:8080），避开 Vite dev server 与 / 路由冲突。 */
function computeApiBase(): string {
  if (import.meta.env.DEV) {
    return 'http://127.0.0.1:8080/'
  }
  const path = window.location.pathname
  // 当前页所在目录（保证以 / 结尾）
  const dir = path.substring(0, path.lastIndexOf('/') + 1)
  // 去尾斜杠后再取上一级目录（含尾斜杠）
  const trimmed = dir.replace(/\/$/, '')
  const parent = trimmed.substring(0, trimmed.lastIndexOf('/') + 1)
  return window.location.origin + parent
}

function initApiBase() {
  if (!apiBase.value) apiBase.value = computeApiBase()
}

function toggleCat(key: string) {
  const arr = selectedCats.value
  const i = arr.indexOf(key)
  if (i >= 0) arr.splice(i, 1)
  else arr.push(key)
}

/** 卡片点击：重置为单分类并发出测试信号。 */
function requestTestFromCat(key: string) {
  selectedCats.value = [key]
  runTestSignal.value++
}

export function useDocsStore() {
  return {
    apiBase,
    selectedCats,
    runTestSignal,
    initApiBase,
    toggleCat,
    requestTestFromCat
  }
}
