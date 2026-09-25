<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    photo: {
        type: Object,
        required: true,
    },
});

const form = useForm({});

const deletePhoto = () => {
    if (!window.confirm('¿Querés eliminar esta fotografía?')) {
        return;
    }

    form.delete(`/boda/fotos/${props.photo.id}`);
};
</script>

<template>
    <Head title="Fotografía" />

    <main class="flex min-h-screen flex-col bg-stone-950 px-4 py-6 text-stone-100 sm:px-8 sm:py-10">
        <header class="mx-auto flex w-full max-w-7xl items-center justify-between">
            <Link href="/boda/fotos" class="text-sm text-stone-400 transition hover:text-white">← Volver a la galería</Link>
            <div class="flex items-center gap-4">
                <button
                    v-if="photo.can_delete"
                    type="button"
                    :disabled="form.processing"
                    class="text-sm text-red-300 transition hover:text-red-100 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="deletePhoto"
                >
                    {{ form.processing ? 'Eliminando…' : 'Eliminar fotografía' }}
                </button>
                <Link href="/boda" class="text-sm text-stone-400 transition hover:text-white">La boda</Link>
            </div>
        </header>

        <div class="flex flex-1 items-center justify-center py-10">
            <figure class="w-full max-w-5xl">
                <img
                    :src="photo.preview_url"
                    alt="Fotografía del álbum"
                    class="mx-auto max-h-[75vh] w-auto max-w-full rounded-xl object-contain shadow-2xl ring-1 ring-white/10"
                >
            </figure>
        </div>
    </main>
</template>
