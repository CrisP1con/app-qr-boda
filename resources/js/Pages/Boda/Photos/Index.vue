<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    photos: {
        type: Array,
        required: true,
    },
    upload_enabled: {
        type: Boolean,
        default: false,
    },
});

const photos = ref([...props.photos]);
let weddingChannel;

const handlePhotoUploaded = ({ photo }) => {
    if (!photo || photos.value.some((item) => item.id === photo.id)) {
        return;
    }

    photos.value = [photo, ...photos.value];
};

const handlePhotoDeleted = ({ photo_id: photoId }) => {
    photos.value = photos.value.filter((photo) => photo.id !== photoId);
};

onMounted(() => {
    if (!window.Echo) {
        return;
    }

    weddingChannel = window.Echo.channel('wedding')
        .listen('.photo.uploaded', handlePhotoUploaded)
        .listen('.photo.deleted', handlePhotoDeleted);
});

onBeforeUnmount(() => {
    if (weddingChannel) {
        window.Echo.leave('wedding');
    }
});
</script>

<template>
    <Head title="Galería" />

    <main class="min-h-screen bg-stone-950 px-4 py-6 text-stone-100 sm:px-8 sm:py-10">
        <div class="mx-auto max-w-7xl">
            <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <Link href="/boda" class="text-sm text-stone-400 transition hover:text-white">← Volver a la boda</Link>
                    <h1 class="mt-4 font-serif text-4xl text-white sm:text-5xl">Galería</h1>
                    <p class="mt-2 text-stone-400">Los recuerdos compartidos de este día.</p>
                </div>
                <a v-if="upload_enabled" href="/boda/subir" class="inline-flex items-center justify-center rounded-full border border-stone-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10">
                    Subir fotografías
                </a>
                <span v-else class="inline-flex items-center justify-center rounded-full border border-stone-700 px-5 py-2.5 text-sm text-stone-500">
                    Recepción cerrada
                </span>
            </header>

            <div v-if="photos.length" class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                <Link
                    v-for="photo in photos"
                    :key="photo.id"
                    :href="`/boda/fotos/${photo.id}`"
                    class="group relative aspect-square overflow-hidden rounded-xl bg-stone-900 ring-1 ring-white/10"
                >
                    <img
                        :src="photo.thumbnail_url"
                        alt="Fotografía del álbum"
                        loading="lazy"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                    >
                    <span class="absolute inset-0 bg-black/0 transition group-hover:bg-black/10" />
                </Link>
            </div>

            <div v-else class="mt-16 rounded-2xl border border-dashed border-stone-700 px-6 py-16 text-center">
                <p class="font-serif text-2xl text-white">Todavía no hay fotografías</p>
                <p class="mx-auto mt-3 max-w-md text-stone-400">Cuando se compartan recuerdos, aparecerán aquí.</p>
            </div>
        </div>
    </main>
</template>
