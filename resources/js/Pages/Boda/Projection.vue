<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    is_admin: {
        type: Boolean,
        default: false,
    },
    photos: {
        type: Array,
        required: true,
    },
});

const photos = ref([...props.photos]);
const currentIndex = ref(0);
let intervalId;
let weddingChannel;

const currentPhoto = computed(() => photos.value[currentIndex.value] ?? null);

const advance = () => {
    if (photos.value.length > 1) {
        currentIndex.value = (currentIndex.value + 1) % photos.value.length;
    }
};

const handlePhotoUploaded = ({ photo }) => {
    if (!photo || photos.value.some((item) => item.id === photo.id)) {
        return;
    }

    if (photos.value.length) {
        currentIndex.value += 1;
    }

    photos.value = [photo, ...photos.value];
};

const handlePhotoDeleted = ({ photo_id: photoId }) => {
    const deletedIndex = photos.value.findIndex((photo) => photo.id === photoId);

    if (deletedIndex === -1) {
        return;
    }

    photos.value = photos.value.filter((photo) => photo.id !== photoId);

    if (deletedIndex < currentIndex.value) {
        currentIndex.value -= 1;
    }

    if (currentIndex.value >= photos.value.length) {
        currentIndex.value = Math.max(0, photos.value.length - 1);
    }
};

const enterFullscreen = async () => {
    if (!document.fullscreenElement) {
        await document.documentElement.requestFullscreen?.();
    }
};

onMounted(() => {
    intervalId = window.setInterval(advance, 5000);

    if (window.Echo) {
        weddingChannel = window.Echo.channel('wedding')
            .listen('.photo.uploaded', handlePhotoUploaded)
            .listen('.photo.deleted', handlePhotoDeleted);
    }
});

onBeforeUnmount(() => {
    window.clearInterval(intervalId);

    if (weddingChannel) {
        window.Echo.leave('wedding');
    }
});
</script>

<template>
    <Head title="Proyección" />

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-black text-white">
        <Transition name="fade" mode="out-in">
            <img
                v-if="currentPhoto"
                :key="currentPhoto.id"
                :src="currentPhoto.preview_url"
                alt="Fotografía proyectada de la boda"
                class="h-screen w-screen object-contain"
            >
            <div v-else key="empty" class="px-6 text-center">
                <p class="font-serif text-4xl text-white sm:text-6xl">Esperando fotografías</p>
                <p class="mt-4 text-stone-400">Las imágenes compartidas aparecerán aquí.</p>
            </div>
        </Transition>

        <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-4 bg-gradient-to-t from-black/80 to-transparent px-5 pb-5 pt-16 opacity-0 transition-opacity hover:opacity-100">
            <Link v-if="is_admin" href="/admin" class="text-sm text-stone-300 hover:text-white">Administración</Link>
            <span v-else />
            <button type="button" class="rounded-full border border-white/30 bg-black/40 px-4 py-2 text-sm text-white backdrop-blur transition hover:border-white" @click="enterFullscreen">
                Pantalla completa
            </button>
        </div>
    </main>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
