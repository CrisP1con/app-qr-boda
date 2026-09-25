<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    album: Object,
});
</script>

<template>
    <Head :title="album?.name ?? 'Álbum de boda'" />

    <main
        class="min-h-screen bg-stone-950 text-stone-100"
        :style="album?.primary_color ? { '--wedding-color': album.primary_color } : {}"
    >
        <section v-if="album" class="relative isolate flex min-h-screen items-center justify-center overflow-hidden px-5 py-12 sm:px-8">
            <img
                v-if="album.cover_image_url"
                :src="album.cover_image_url"
                alt=""
                class="absolute inset-0 -z-20 h-full w-full object-cover"
            >
            <div class="absolute inset-0 -z-10 bg-stone-950/65" :class="album.cover_image_url ? 'backdrop-blur-[2px]' : ''" />

            <div class="w-full max-w-2xl text-center">
                <div class="mx-auto mb-8 flex size-16 items-center justify-center rounded-full border border-[var(--wedding-color,_#d6b47a)]/60 text-[var(--wedding-color,_#d6b47a)]">
                    <span class="text-2xl" aria-hidden="true">♡</span>
                </div>

                <p class="text-xs font-medium uppercase tracking-[0.35em] text-stone-300">Nuestra boda</p>
                <h1 class="mt-5 font-serif text-4xl leading-tight text-white sm:text-6xl">{{ album.name }}</h1>
                <p class="mt-5 text-lg text-stone-200 sm:text-xl">{{ album.event_date }}</p>

                <p v-if="album.message" class="mx-auto mt-8 max-w-xl text-base leading-8 text-stone-200 sm:text-lg">
                    {{ album.message }}
                </p>

                <div class="mx-auto mt-10 flex max-w-md flex-col gap-3 sm:flex-row sm:justify-center">
                    <a
                        v-if="album.upload_enabled"
                        href="/boda/subir"
                        class="inline-flex items-center justify-center rounded-full bg-[var(--wedding-color,_#d6b47a)] px-6 py-3 text-sm font-semibold text-stone-950 transition hover:brightness-110"
                    >
                        Subir fotografías
                    </a>
                    <span
                        v-else
                        class="inline-flex items-center justify-center rounded-full border border-stone-500 px-6 py-3 text-sm font-medium text-stone-300"
                    >
                        La recepción está cerrada
                    </span>
                    <a
                        href="/boda/fotos"
                        class="inline-flex items-center justify-center rounded-full border border-stone-400 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10"
                    >
                        Ver galería
                    </a>
                </div>

                <p v-if="album.upload_enabled" class="mt-6 text-sm text-stone-300">
                    Compartí tus recuerdos con nosotros.
                </p>
            </div>
        </section>

        <section v-else class="flex min-h-screen items-center justify-center px-5 py-12 text-center">
            <div class="max-w-md">
                <p class="text-xs font-medium uppercase tracking-[0.35em] text-stone-400">Álbum de boda</p>
                <h1 class="mt-5 font-serif text-4xl text-white">El evento aún no está configurado</h1>
                <p class="mt-5 leading-7 text-stone-300">La información de la boda estará disponible próximamente.</p>
            </div>
        </section>
    </main>
</template>
