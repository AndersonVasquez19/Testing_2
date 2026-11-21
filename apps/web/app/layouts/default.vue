<template>
  <div class="min-h-screen flex flex-col bg-[#0A1929] text-stone-100">
    <LayoutAppNavbar />
    <main class="flex-1">
      <slot />
    </main>
    <LayoutAppFooter />
    <SharedWhatsappFloatingWidget />
  </div>
</template>
