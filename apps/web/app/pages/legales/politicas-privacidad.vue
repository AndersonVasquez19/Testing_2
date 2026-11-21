<script setup lang="ts">
import { CONTACT_INFO } from '~/data/contactos';
useHead({ title: 'Políticas de Privacidad' });
</script>

<template>
  <div class="pt-28 pb-20 mx-auto max-w-4xl px-4 text-stone-200 space-y-6">
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Políticas de Privacidad</h1>
    <p class="text-sm text-stone-300">Conforme a la Ley N.° 29733 de Protección de Datos Personales del Perú.</p>
    <div class="rounded-3xl border border-white/10 bg-[#0A2540]/80 p-8 space-y-4 text-sm leading-relaxed">
      <p><strong>Titular del Banco de Datos:</strong> {{ CONTACT_INFO.name }} (RUC {{ CONTACT_INFO.ruc }}), {{ CONTACT_INFO.address }}.</p>
      <p>Los datos proporcionados en formularios de matrícula, contacto y Libro de Reclamaciones se utilizan únicamente para gestión académica, emisión de certificados verificables y atención al consumidor.</p>
      <p>Puedes ejercer tus derechos ARCO escribiendo a <a :href="`mailto:${CONTACT_INFO.email}`" class="text-gold underline">{{ CONTACT_INFO.email }}</a>.</p>
    </div>
  </div>
</template>
