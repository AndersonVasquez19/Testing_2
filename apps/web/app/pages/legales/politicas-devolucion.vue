<script setup lang="ts">
import { CONTACT_INFO } from '~/data/contactos';
useHead({ title: 'Políticas de Devolución' });
</script>

<template>
  <div class="pt-28 pb-20 mx-auto max-w-4xl px-4 text-stone-200 space-y-6">
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Políticas de Devolución</h1>
    <div class="rounded-3xl border border-white/10 bg-[#0A2540]/80 p-8 space-y-4 text-sm leading-relaxed">
      <p>Puedes solicitar devolución por fuerza mayor acreditada antes del inicio de la primera sesión programada.</p>
      <p>Envía tu solicitud a <a :href="`mailto:${CONTACT_INFO.email}`" class="text-gold underline">{{ CONTACT_INFO.email }}</a> adjuntando DNI y comprobante de pago.</p>
    </div>
  </div>
</template>
