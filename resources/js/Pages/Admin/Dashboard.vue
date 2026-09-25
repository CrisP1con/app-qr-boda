<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    album: Object,
    photoCount: {
        type: Number,
        required: true,
    },
    storageUsedBytes: {
        type: Number,
        required: true,
    },
});

const formatBytes = (bytes) => {
    if (bytes === 0) return '0 B';

    const units = ['B', 'KB', 'MB', 'GB'];
    const unitIndex = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);

    return `${(bytes / (1024 ** unitIndex)).toFixed(unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`;
};

const toggleUploadForm = useForm({});

const toggleUpload = () => {
    toggleUploadForm.post(route('admin.album.toggle-upload'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Administración" />

    <AppLayout title="Administración">
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">Administración</h2>
                    <p class="mt-1 text-sm text-gray-500">Panel de control del álbum de la boda.</p>
                </div>
                <Link
                    :href="route('admin.configuration.edit')"
                    class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700"
                >
                    Configurar evento
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div v-if="!album" class="rounded-lg border border-amber-200 bg-amber-50 p-6 text-amber-900">
                    <h3 class="font-semibold">El evento todavía no está configurado.</h3>
                    <p class="mt-1 text-sm">Completa los datos básicos para crear el álbum único de esta boda.</p>
                </div>

                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm text-gray-500">Evento</p>
                        <p class="mt-2 font-semibold text-gray-900">{{ album.name }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm text-gray-500">Fecha</p>
                        <p class="mt-2 font-semibold text-gray-900">{{ album.event_date }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm text-gray-500">Recepción</p>
                        <p class="mt-2 font-semibold" :class="album.upload_enabled ? 'text-emerald-600' : 'text-gray-600'">
                            {{ album.upload_enabled ? 'Abierta' : 'Cerrada' }}
                        </p>
                        <button
                            v-if="album"
                            type="button"
                            :disabled="toggleUploadForm.processing"
                            class="mt-4 text-sm font-semibold text-gray-700 underline underline-offset-2 transition hover:text-gray-900 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="toggleUpload"
                        >
                            {{ toggleUploadForm.processing ? 'Actualizando…' : (album.upload_enabled ? 'Cerrar recepción' : 'Abrir recepción') }}
                        </button>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm text-gray-500">Fotografías</p>
                        <p class="mt-2 font-semibold text-gray-900">{{ photoCount }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ formatBytes(storageUsedBytes) }} almacenados</p>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h3 class="font-semibold text-gray-900">Accesos rápidos</h3>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <Link :href="route('admin.configuration.edit')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Configuración
                        </Link>
                        <a href="/boda/fotos" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Galería pública
                        </a>
                        <a v-if="album?.upload_enabled" href="/boda/subir" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Subida pública
                        </a>
                        <Link :href="route('admin.photos.index')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Gestionar fotografías
                        </Link>
                        <Link :href="route('admin.qr.show')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Código QR
                        </Link>
                        <Link :href="route('admin.projection')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Proyección
                        </Link>
                        <a href="/admin/download" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Descargar originales
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
