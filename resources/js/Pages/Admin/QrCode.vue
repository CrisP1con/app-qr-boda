<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    destination: {
        type: String,
        required: true,
    },
    png_data_uri: {
        type: String,
        required: true,
    },
    svg_data_uri: {
        type: String,
        required: true,
    },
    png_download_url: {
        type: String,
        required: true,
    },
    svg_download_url: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Código QR" />

    <AppLayout title="Código QR">
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Código QR</h2>
                <Link :href="route('admin.dashboard')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Volver al dashboard</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 sm:p-8">
                    <p class="text-sm text-gray-500">Destino del código QR</p>
                    <p class="mt-2 break-all font-mono text-sm text-gray-900">{{ destination }}</p>
                    <p class="mt-3 text-sm text-gray-500">El destino se construye desde `APP_URL` y siempre apunta a `/boda`.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <section class="rounded-lg bg-white p-6 text-center shadow-sm ring-1 ring-gray-200">
                        <h3 class="font-semibold text-gray-900">PNG</h3>
                        <img :src="png_data_uri" alt="Código QR de la boda" class="mx-auto mt-5 size-64 max-w-full">
                        <a :href="png_download_url" class="mt-6 inline-flex rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">Descargar PNG</a>
                    </section>

                    <section class="rounded-lg bg-white p-6 text-center shadow-sm ring-1 ring-gray-200">
                        <h3 class="font-semibold text-gray-900">SVG</h3>
                        <img :src="svg_data_uri" alt="Código QR de la boda en SVG" class="mx-auto mt-5 size-64 max-w-full">
                        <a :href="svg_download_url" class="mt-6 inline-flex rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">Descargar SVG</a>
                    </section>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
