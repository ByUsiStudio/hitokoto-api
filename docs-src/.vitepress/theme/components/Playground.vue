<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useDocsStore } from '../composables/useDocsStore'

const { apiBase, selectedCats, runTestSignal, initApiBase, toggleCat } = useDocsStore()

const FORMATS: { v: string; t: string }[] = [
  { v: 'json', t: 'JSON（完整信息）' },
  { v: 'text', t: 'TEXT（仅一言）' },
  { v: 'json5', t: 'JSON5（带注释）' },
  { v: 'xml', t: 'XML' },
  { v: 'ini', t: 'INI' },
  { v: 'toml', t: 'TOML' },
  { v: 'js', t: 'JS（变量赋值）' },
  { v: 'sh', t: 'SH（Shell 变量）' },
  { v: 'bat', t: 'BAT（批处理变量）' },
  { v: 'ps1', t: 'PS1（PowerShell 变量）' }
]
const CHARSETS: { v: string; t: string }[] = [
  { v: 'utf-8', t: 'UTF-8' },
  { v: 'gbk', t: 'GBK' }
]

const encode = ref('json')
const charset = ref('utf-8')
const categories = ref<{ key: string; name: string }[]>([])
const catLoading = ref(true)

const resultText = ref('点击「发送请求」开始体验')
const quoteText = ref('')
const fromText = ref('')
const showQuote = ref(false)
const showFrom = ref(false)
const statusText = ref('')
const statusOk = ref(false)
const showStatus = ref(false)
const toastMsg = ref('')
const toastShown = ref(false)

const params = computed(() => {
  const p: string[] = ['encode=' + encodeURIComponent(encode.value)]
  if (charset.value !== 'utf-8') p.push('charset=' + encodeURIComponent(charset.value))
  if (selectedCats.value.length > 0) {
    p.push('c=' + encodeURIComponent(selectedCats.value.join(',')))
  }
  return p
})

const requestUrl = computed(() => {
  return apiBase.value + (params.value.length ? '?' + params.value.join('&') : '')
})

const urlPreview = computed(() => (apiBase.value ? requestUrl.value : './'))

onMounted(async () => {
  initApiBase()
  try {
    const resp = await fetch(`${apiBase.value}categories.json`, { cache: 'no-cache' })
    if (resp.ok) categories.value = await resp.json()
  } catch {
    /* 分类芯片加载失败不阻塞核心功能 */
  }
  catLoading.value = false
})

// 卡片点击 → 滚动完成后触发测试（520ms 与原 docs.html 一致）
watch(runTestSignal, () => {
  setTimeout(runTest, 520)
})

function isActive(key: string): boolean {
  return selectedCats.value.includes(key)
}

async function runTest() {
  if (!apiBase.value) return
  const url = requestUrl.value
  resultText.value = '请求中…'
  showQuote.value = false
  showFrom.value = false
  showStatus.value = false
  try {
    const t0 = performance.now()
    const resp = await fetch(url, { cache: 'no-store' })
    const dt = Math.round(performance.now() - t0)
    const text = await resp.text()
    statusText.value = (resp.ok ? 'OK ' : 'ERR ') + resp.status + '  ' + dt + 'ms'
    statusOk.value = resp.ok
    showStatus.value = true
    if (encode.value === 'json' && resp.ok) {
      try {
        const json = JSON.parse(text)
        if (json.hitokoto) {
          quoteText.value = json.hitokoto
          showQuote.value = true
          const parts: string[] = []
          if (json.from) parts.push('——《' + json.from + '》')
          if (json.from_who) parts.push('  ' + json.from_who)
          if (parts.length) {
            fromText.value = parts.join('')
            showFrom.value = true
          }
        }
        resultText.value = JSON.stringify(json, null, 2)
      } catch {
        resultText.value = text
      }
    } else {
      resultText.value = text
    }
  } catch (e) {
    resultText.value = '请求失败：' + (e as Error).message
    statusText.value = 'NETWORK ERROR'
    statusOk.value = false
    showStatus.value = true
  }
}

function resetForm() {
  encode.value = 'json'
  charset.value = 'utf-8'
  selectedCats.value = []
  resultText.value = '点击「发送请求」开始体验'
  showQuote.value = false
  showFrom.value = false
  showStatus.value = false
}

function copyUrl() { copyText(requestUrl.value, 'URL 已复制') }
function copyResult() { copyText(resultText.value, '内容已复制') }

function copyText(str: string, msg: string) {
  const done = () => {
    toastMsg.value = msg
    toastShown.value = true
    setTimeout(() => (toastShown.value = false), 1800)
  }
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(str).then(done).catch(done)
  } else {
    const ta = document.createElement('textarea')
    ta.value = str
    document.body.appendChild(ta)
    ta.select()
    try { document.execCommand('copy') } catch { /* ignore */ }
    document.body.removeChild(ta)
    done()
  }
}
</script>

<template>
  <div class="pg">
    <form class="pg-form" @submit.prevent="runTest">
      <div class="pg-field">
        <label>输出格式 (form / encode)</label>
        <select v-model="encode">
          <option v-for="f in FORMATS" :key="f.v" :value="f.v">{{ f.t }}</option>
        </select>
      </div>
      <div class="pg-field">
        <label>字符集 (charset)</label>
        <select v-model="charset">
          <option v-for="c in CHARSETS" :key="c.v" :value="c.v">{{ c.t }}</option>
        </select>
      </div>
      <div class="pg-field pg-field-full">
        <label>分类筛选 (c) — 点击切换，空 = 全部：</label>
        <div class="pg-cats">
          <span v-if="catLoading" class="pg-cats-tip">加载分类中…</span>
          <button
            v-for="c in categories"
            v-else
            :key="c.key"
            type="button"
            class="pg-cat-chip"
            :class="{ active: isActive(c.key) }"
            @click="toggleCat(c.key)"
          >
            <span>{{ c.key.toUpperCase() }}</span>
            <span>{{ c.name }}</span>
          </button>
        </div>
      </div>
    </form>

    <div class="pg-url-preview">
      <b>请求 URL：</b><span>{{ urlPreview }}</span>
    </div>

    <div class="pg-actions">
      <div class="pg-actions-left">
        <button type="button" class="pg-btn pg-btn-primary" @click="runTest">发送请求</button>
        <button type="button" class="pg-btn pg-btn-secondary" @click="resetForm">重置</button>
      </div>
      <div class="pg-actions-right">
        <button type="button" class="pg-btn-copy" @click="copyUrl">复制 URL</button>
        <span
          v-if="showStatus"
          class="status-tag"
          :class="statusOk ? 'ok' : 'err'"
        >{{ statusText }}</span>
      </div>
    </div>

    <div class="pg-result-wrap">
      <div class="pg-result-header">
        <h4>响应结果</h4>
        <button type="button" class="pg-btn-copy" @click="copyResult">复制内容</button>
      </div>
      <div v-if="showQuote" class="pg-quote">{{ quoteText }}</div>
      <div v-if="showFrom" class="pg-from">{{ fromText }}</div>
      <pre class="pg-result">{{ resultText }}</pre>
    </div>

    <div class="pg-toast" :class="{ show: toastShown }">{{ toastMsg }}</div>
  </div>
</template>

<style scoped>
.pg {
  margin: 1.25rem 0;
}

.pg-form {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: clamp(0.85rem, 2vw, 1.15rem);
  margin-bottom: 1.25rem;
}
.pg-field label {
  display: block;
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--vp-c-text-2);
  margin-bottom: 0.5rem;
}
.pg-field select {
  width: 100%;
  padding: 0.65rem 0.85rem;
  border: 1.5px solid var(--vp-c-border);
  border-radius: 0.625rem;
  font-size: 0.88rem;
  font-family: inherit;
  background: var(--vp-c-bg);
  color: var(--vp-c-text-1);
  transition: border-color var(--bs-duration) ease, box-shadow var(--bs-duration) ease;
  outline: none;
}
.pg-field select:focus {
  border-color: var(--vp-c-brand-2);
  box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.12);
}
.pg-field-full { grid-column: span 2; }

.pg-cats {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.pg-cats-tip { color: var(--vp-c-text-3); font-size: 0.82rem; }
.pg-cat-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.4rem 0.85rem;
  border: 1.5px solid var(--vp-c-border);
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  background: var(--vp-c-bg);
  color: var(--vp-c-text-2);
  transition: all var(--bs-duration) ease;
  user-select: none;
}
.pg-cat-chip:hover {
  border-color: var(--vp-c-brand-3);
  background: var(--vp-c-brand-soft);
  color: var(--vp-c-brand-1);
}
.pg-cat-chip.active {
  background: linear-gradient(135deg, var(--vp-c-brand-2), var(--vp-c-brand-1));
  border-color: transparent;
  color: #fff;
  box-shadow: 0 4px 12px rgba(30, 136, 229, 0.3);
}
.pg-cat-chip:active { transform: scale(0.94); }

.pg-url-preview {
  background: var(--bs-yellow-100);
  border: 1px dashed var(--bs-yellow-300);
  border-radius: 0.625rem;
  padding: 0.75rem 1rem;
  margin-bottom: 1.1rem;
  font-family: var(--vp-font-family-mono);
  font-size: 0.78rem;
  color: #7A5100;
  word-break: break-all;
}
.pg-url-preview b { color: #B7791F; }

.pg-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
}
.pg-actions-left, .pg-actions-right {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  align-items: center;
}
.pg-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: clamp(0.6rem, 1.4vw, 0.75rem) clamp(1.25rem, 3vw, 1.7rem);
  border-radius: 999px;
  font-size: clamp(0.82rem, 1.3vw, 0.92rem);
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all var(--bs-duration) cubic-bezier(.4, 0, .2, 1);
}
.pg-btn-primary {
  background: linear-gradient(135deg, var(--vp-c-brand-2), var(--vp-c-brand-1));
  color: #fff;
  box-shadow: 0 4px 14px rgba(30, 136, 229, 0.3);
}
.pg-btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(30, 136, 229, 0.4);
}
.pg-btn-secondary {
  background: var(--bs-yellow-100);
  color: #B7791F;
  border: 1px solid var(--bs-yellow-200);
}
.pg-btn-secondary:hover {
  background: var(--bs-yellow-200);
  transform: translateY(-2px);
}
.pg-btn:active { transform: scale(0.96); }

.pg-btn-copy {
  background: var(--vp-c-brand-soft);
  color: var(--vp-c-brand-1);
  border: 1px solid var(--vp-c-brand-3);
  padding: 0.6rem 1.25rem;
  border-radius: 999px;
  cursor: pointer;
  font-size: 0.82rem;
  font-weight: 600;
  transition: background var(--bs-duration) ease;
}
.pg-btn-copy:hover { background: var(--vp-c-brand-soft); filter: brightness(0.96); }

.status-tag {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  animation: bsFadeInUp 0.35s cubic-bezier(.4, 0, .2, 1) both;
}
.status-tag.ok { background: #E6F7EC; color: #2E8B57; }
.status-tag.err { background: #FDECEA; color: #C9372C; }

.pg-result-wrap {
  border: 1.5px solid var(--vp-c-border);
  border-radius: var(--bs-radius-sm);
  overflow: hidden;
  transition: border-color var(--bs-duration) ease;
}
.pg-result-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1.1rem;
  background: var(--vp-c-brand-soft);
  border-bottom: 1px solid var(--vp-c-border);
}
.pg-result-header h4 {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--vp-c-brand-1);
  margin: 0;
}
.pg-quote {
  font-family: "PingFang SC", "Microsoft YaHei", serif;
  font-size: clamp(1.1rem, 2.6vw, 1.4rem);
  line-height: 1.9;
  padding: clamp(1.5rem, 4vw, 1.85rem) clamp(1.5rem, 4vw, 2rem);
  text-align: center;
  font-weight: 500;
  color: var(--vp-c-text-1);
}
.pg-quote::before {
  content: "\201C";
  font-size: 1.8em;
  color: var(--vp-c-brand-3);
  margin-right: 0.25em;
}
.pg-quote::after {
  content: "\201D";
  font-size: 1.8em;
  color: var(--bs-yellow-300);
  margin-left: 0.25em;
}
.pg-from {
  text-align: right;
  color: var(--vp-c-text-2);
  font-size: 0.88rem;
  padding: 0 clamp(1.5rem, 4vw, 2rem) 1.75rem;
  font-style: italic;
}
.pg-result {
  background: var(--vp-c-bg-soft);
  padding: 1.1rem 1.25rem;
  max-height: 20rem;
  overflow: auto;
  font-family: var(--vp-font-family-mono);
  font-size: 0.82rem;
  color: var(--vp-c-text-1);
  white-space: pre-wrap;
  word-break: break-all;
  line-height: 1.7;
  margin: 0;
}

.pg-toast {
  position: fixed;
  top: 1.75rem;
  left: 50%;
  transform: translateX(-50%) translateY(-80px);
  background: #2C3E50;
  color: #fff;
  padding: 0.75rem 1.6rem;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 600;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  z-index: 9999;
  opacity: 0;
  pointer-events: none;
  transition: all var(--bs-duration) cubic-bezier(.4, 0, .2, 1);
}
.pg-toast.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}

@media (max-width: 640px) {
  .pg-form { grid-template-columns: 1fr; }
  .pg-field-full { grid-column: span 1; }
  .pg-actions { justify-content: stretch; }
  .pg-actions-left, .pg-actions-right { width: 100%; }
  .pg-btn { flex: 1 1 auto; justify-content: center; }
}
</style>
