<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useDocsStore } from '../composables/useDocsStore'

const { apiBase, initApiBase, requestTestFromCat } = useDocsStore()

interface Category { key: string; name: string; desc?: string }

const categories = ref<Category[]>([])
const stats = ref<Record<string, number | string>>({})
const loading = ref(true)
const errorMsg = ref('')

onMounted(async () => {
  initApiBase()
  try {
    const resp = await fetch(`${apiBase.value}categories.json`, { cache: 'no-cache' })
    if (!resp.ok) throw new Error('HTTP ' + resp.status)
    const data = await resp.json()
    if (!Array.isArray(data)) throw new Error('categories.json 不是数组')
    categories.value = data
    loading.value = false
    loadStats()
  } catch (e) {
    errorMsg.value = (e as Error).message || String(e)
    loading.value = false
  }
})

async function loadStats() {
  await Promise.all(categories.value.map(async (c) => {
    try {
      const resp = await fetch(
        `${apiBase.value}sentences/${encodeURIComponent(c.key)}.json`,
        { cache: 'no-cache' }
      )
      if (!resp.ok) throw new Error('HTTP ' + resp.status)
      const data = await resp.json()
      stats.value[c.key] = Array.isArray(data) ? data.length : 0
    } catch {
      stats.value[c.key] = '加载失败'
    }
  }))
}

function statText(key: string): string {
  const v = stats.value[key]
  if (v === undefined) return '统计中…'
  return typeof v === 'number' ? v + ' 条' : v
}

function onClick(key: string) {
  requestTestFromCat(key)
  const el = document.getElementById('playground')
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
</script>

<template>
  <div class="cat-grid">
    <div v-if="loading" class="cat-tip">正在加载分类数据…</div>
    <div v-else-if="errorMsg" class="cat-tip cat-tip-err">
      加载 categories.json 失败：{{ errorMsg }}
    </div>
    <template v-else>
      <div
        v-for="(c, i) in categories"
        :key="c.key"
        class="cat-card"
        :style="{ animationDelay: i * 55 + 'ms' }"
        @click="onClick(c.key)"
      >
        <div class="cat-key">{{ c.key.toUpperCase() }}</div>
        <div class="cat-name">{{ c.name }}</div>
        <div class="cat-desc">{{ c.desc || '' }}</div>
        <div class="cat-stat">{{ statText(c.key) }}</div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.cat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(clamp(220px, 38vw, 280px), 1fr));
  gap: clamp(0.75rem, 2vw, 1.15rem);
  margin: 1.25rem 0;
}
.cat-tip {
  grid-column: 1 / -1;
  color: var(--vp-c-text-3);
  font-size: 0.9rem;
  padding: 0.75rem 0.25rem;
  animation: bsFadeInUp var(--bs-duration) cubic-bezier(.4, 0, .2, 1) both;
}
.cat-tip-err { color: #C9372C; }

.cat-card {
  position: relative;
  overflow: hidden;
  padding: clamp(1.1rem, 2.4vw, 1.5rem);
  border-radius: var(--bs-radius-sm);
  background: var(--vp-c-bg-soft);
  border: 1px solid var(--vp-c-border);
  cursor: pointer;
  opacity: 0;
  animation: bsFadeInUp var(--bs-duration) cubic-bezier(.4, 0, .2, 1) forwards;
  transition: transform var(--bs-duration) cubic-bezier(.4, 0, .2, 1),
              box-shadow var(--bs-duration) cubic-bezier(.4, 0, .2, 1),
              border-color var(--bs-duration) cubic-bezier(.4, 0, .2, 1);
}
.cat-card::after {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 4px; height: 100%;
  transition: width var(--bs-duration) cubic-bezier(.4, 0, .2, 1);
}
.cat-card:nth-child(6n+1)::after { background: linear-gradient(to bottom, #FFE066, #F0B429); }
.cat-card:nth-child(6n+2)::after { background: linear-gradient(to bottom, #74B9E6, #3F9BDD); }
.cat-card:nth-child(6n+3)::after { background: linear-gradient(to bottom, #95D1A5, #52B788); }
.cat-card:nth-child(6n+4)::after { background: linear-gradient(to bottom, #F4A261, #E76F51); }
.cat-card:nth-child(6n+5)::after { background: linear-gradient(to bottom, #C9A0DC, #A56CC1); }
.cat-card:nth-child(6n+6)::after { background: linear-gradient(to bottom, #FFB3C1, #FF758F); }

.cat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 28px rgba(30, 136, 229, 0.12);
  border-color: var(--vp-c-brand-3);
}
.cat-card:hover::after { width: 6px; }
.cat-card:active { transform: translateY(-2px) scale(0.985); }

.cat-key {
  display: inline-block;
  width: 2.25rem; height: 2.25rem; line-height: 2.25rem;
  text-align: center;
  border-radius: 0.625rem;
  background: var(--vp-c-brand-soft);
  color: var(--vp-c-brand-1);
  font-weight: 700;
  font-size: 0.94rem;
  margin-bottom: 0.75rem;
}
.cat-name { font-size: 1.06rem; font-weight: 700; margin-bottom: 0.35rem; color: var(--vp-c-text-1); }
.cat-desc {
  font-size: 0.82rem;
  color: var(--vp-c-text-2);
  margin-bottom: 0.75rem;
  min-height: 2.4em;
}
.cat-stat {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.25rem 0.75rem;
  background: var(--bs-yellow-100);
  border: 1px solid var(--bs-yellow-200);
  border-radius: 999px;
  font-size: 0.75rem;
  color: #B7791F;
  font-weight: 600;
}
</style>
