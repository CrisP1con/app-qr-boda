<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

defineProps({
    album: Object,
});

const form = useForm({
    photos: [],
});

const previews = ref([]);
const selectionError = ref('');
const fileErrors = ref([]);
const fileInput = ref(null);

const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
const maximumFileSize = 20 * 1024 * 1024;

const releasePreviews = () => {
    previews.value.forEach((preview) => URL.revokeObjectURL(preview.url));
    previews.value = [];
};

const selectPhotos = (event) => {
    form.photos = Array.from(event.target.files ?? []);
    selectionError.value = form.photos.length > 20
        ? 'Podés seleccionar hasta 20 fotografías por subida.'
        : '';
    fileErrors.value = form.photos.map((photo) => {
        const extension = photo.name.split('.').pop()?.toLowerCase() ?? '';
        const errors = [];

        if (!allowedExtensions.includes(extension)) {
            errors.push('formato no compatible');
        }

        if (photo.size > maximumFileSize) {
            errors.push('supera los 20 MB');
        }

        return errors.length ? `${photo.name}: ${errors.join(' y ')}.` : '';
    });
    releasePreviews();
    previews.value = form.photos.map((photo) => ({
        name: photo.name,
        url: URL.createObjectURL(photo),
    }));
};

const removePhoto = (index) => {
    const [preview] = previews.value.splice(index, 1);

    if (preview) {
        URL.revokeObjectURL(preview.url);
    }

    form.photos.splice(index, 1);
    fileErrors.value.splice(index, 1);
    selectionError.value = form.photos.length > 20
        ? 'Podés seleccionar hasta 20 fotografías por subida.'
        : '';

    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const submit = () => {
    if (selectionError.value || fileErrors.value.some(Boolean)) {
        return;
    }

    form.post('/boda/fotos', {
        forceFormData: true,
        preserveScroll: true,
    });
};

onBeforeUnmount(releasePreviews);
</script>

<template>
    <Head title="Subir fotografías" />

    <main class="min-h-screen bg-stone-950 px-5 py-8 text-stone-100 sm:px-8 sm:py-12">
        <div class="mx-auto flex min-h-[80vh] max-w-xl flex-col justify-center">
            <Link href="/boda" class="text-sm text-stone-400 transition hover:text-white">← Volver a la boda</Link>

            <div class="mt-10 rounded-3xl border border-stone-800 bg-stone-900/80 p-6 shadow-2xl sm:p-10">
                <p class="text-xs font-medium uppercase tracking-[0.3em] text-stone-400">{{ album?.name ?? 'Álbum de boda' }}</p>
                <h1 class="mt-4 font-serif text-4xl text-white sm:text-5xl">Compartí tus recuerdos</h1>
                <p class="mt-4 leading-7 text-stone-300">Seleccioná hasta 20 fotografías JPG, JPEG, PNG o WEBP. Cada archivo puede pesar hasta 20 MB.</p>

                <form class="mt-8 space-y-6" @submit.prevent="submit">
                    <label for="photos" class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-stone-600 px-6 py-12 text-center transition hover:border-stone-300 hover:bg-white/5">
                        <span class="text-lg font-semibold text-white">Elegir fotografías</span>
                        <span class="mt-2 text-sm text-stone-400">Podés seleccionar varias a la vez</span>
                        <input id="photos" ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" @change="selectPhotos">
                    </label>

                    <p v-if="form.photos.length" class="text-sm text-stone-300">{{ form.photos.length }} fotografía{{ form.photos.length === 1 ? '' : 's' }} seleccionada{{ form.photos.length === 1 ? '' : 's' }}.</p>
                    <p v-if="selectionError" class="text-sm text-amber-200">{{ selectionError }}</p>
                    <div v-if="fileErrors.some(Boolean)" class="space-y-1 text-sm text-red-300">
                        <p v-for="error in fileErrors.filter(Boolean)" :key="error">{{ error }}</p>
                    </div>
                    <div v-if="previews.length" class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        <div v-for="(preview, index) in previews" :key="preview.url" class="group relative overflow-hidden rounded-xl border border-stone-700 bg-stone-950">
                            <img :src="preview.url" :alt="preview.name" class="aspect-square w-full object-cover">
                            <p class="truncate px-2 py-1.5 text-[11px] text-stone-400" :title="preview.name">{{ preview.name }}</p>
                            <button
                                type="button"
                                class="absolute right-1.5 top-1.5 flex size-7 items-center justify-center rounded-full bg-stone-950/80 text-lg leading-none text-white transition hover:bg-red-700"
                                :aria-label="`Quitar ${preview.name}`"
                                @click="removePhoto(index)"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                    <p v-if="form.errors.photos" class="text-sm text-red-300">{{ form.errors.photos }}</p>
                    <p v-if="form.errors['photos.0']" class="text-sm text-red-300">{{ form.errors['photos.0'] }}</p>

                    <button type="submit" :disabled="form.processing || !form.photos.length || !!selectionError || fileErrors.some(Boolean) || !album || album.upload_enabled === false" class="w-full rounded-full bg-[#d6b47a] px-6 py-3 font-semibold text-stone-950 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50">
                        {{ form.processing ? 'Subiendo fotografías…' : 'Publicar fotografías' }}
                    </button>
                </form>

                <p v-if="!album" class="mt-5 text-center text-sm text-amber-200">El álbum todavía no está configurado.</p>
                <p v-if="album?.upload_enabled === false" class="mt-5 text-center text-sm text-amber-200">La recepción de fotografías está cerrada.</p>
            </div>

            <Link href="/boda/fotos" class="mt-6 text-center text-sm text-stone-400 transition hover:text-white">Ver galería</Link>
        </div>
    </main>
</template>
