<script setup lang="ts">
import { FAQS } from '~/data/faqs';
useHead({ title: 'Preguntas Frecuentes' });
</script>

<template>
  <div class="pt-28 pb-20 mx-auto max-w-4xl px-4 text-stone-200 space-y-6">
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Preguntas Frecuentes</h1>
    <div class="space-y-4">
      <article v-for="faq in FAQS" :key="faq.id" class="rounded-2xl border border-white/10 bg-[#0A2540]/80 p-6">
        <span class="text-xs font-bold text-gold uppercase">{{ faq.categoryLabel }}</span>
        <h2 class="mt-1 text-lg font-bold text-white">{{ faq.question }}</h2>
        <p class="mt-2 text-sm text-stone-300">{{ faq.answer }}</p>
      </article>
    </div>
  </div>
</template>
