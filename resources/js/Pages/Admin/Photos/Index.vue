<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    photos: {
        type: Array,
        required: true,
    },
});

const formatBytes = (bytes) => {
    if (bytes === 0) return '0 B';

    const units = ['B', 'KB', 'MB', 'GB'];
    const unitIndex = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);

    return `${(bytes / (1024 ** unitIndex)).toFixed(unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`;
};

const deletePhoto = (photo) => {
    if (!window.confirm('¿Eliminar esta fotografía de forma permanente?')) {
        return;
    }

    useForm({}).delete(`/admin/fotos/${photo.id}`);
};
</script>

<template>
    <Head title="Fotografías" />

    <AppLayout title="Fotografías">
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Fotografías</h2>
                <Link :href="route('admin.dashboard')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Volver al dashboard</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="photos.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <article v-for="photo in photos" :key="photo.id" class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
                        <img :src="photo.preview_url" alt="Fotografía del álbum" loading="lazy" class="aspect-square w-full object-cover">
                        <div class="space-y-3 p-4">
                            <p class="truncate text-sm font-medium text-gray-800" :title="photo.original_filename">{{ photo.original_filename }}</p>
                            <p class="text-xs text-gray-500">{{ formatBytes(photo.size) }}</p>
                            <button type="button" class="w-full rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50" @click="deletePhoto(photo)">
                                Eliminar fotografía
                            </button>
                        </div>
                    </article>
                </div>

                <div v-else class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
                    <p class="text-lg font-semibold text-gray-800">Todavía no hay fotografías</p>
                    <p class="mt-2 text-sm text-gray-500">Las fotografías compartidas aparecerán aquí.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
