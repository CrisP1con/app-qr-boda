<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    name: String,
    message: String,
    upload_enabled: Boolean,
});
</script>

<template>
    <Head title="Fotografías publicadas" />

    <main class="flex min-h-screen items-center justify-center bg-stone-950 px-5 py-12 text-center text-stone-100 sm:px-8">
        <div class="w-full max-w-xl rounded-3xl border border-stone-800 bg-stone-900/80 p-8 shadow-2xl sm:p-12">
            <div class="mx-auto flex size-16 items-center justify-center rounded-full border border-[#d6b47a]/60 text-3xl text-[#d6b47a]" aria-hidden="true">✓</div>
            <p class="mt-8 text-xs font-medium uppercase tracking-[0.3em] text-stone-400">{{ name ?? 'Álbum de boda' }}</p>
            <h1 class="mt-4 font-serif text-4xl text-white sm:text-5xl">¡Gracias!</h1>
            <p class="mt-5 leading-8 text-stone-300">{{ message || 'Tus fotografías se agregaron correctamente a la galería.' }}</p>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <Link href="/boda/fotos" class="rounded-full bg-[#d6b47a] px-6 py-3 font-semibold text-stone-950 transition hover:brightness-110">Volver a la galería</Link>
                <Link v-if="upload_enabled" href="/boda/subir" class="rounded-full border border-stone-500 px-6 py-3 font-semibold text-white transition hover:border-white hover:bg-white/10">Subir más fotografías</Link>
            </div>
        </div>
    </main>
</template>
