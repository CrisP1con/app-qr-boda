<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    album: Object,
});

const form = useForm({
    name: props.album?.name ?? '',
    event_date: props.album?.event_date ?? '',
    cover_image: null,
    message: props.album?.message ?? '',
    primary_color: props.album?.primary_color ?? '',
    thank_you_message: props.album?.thank_you_message ?? '',
    upload_enabled: props.album?.upload_enabled ?? true,
});

const submit = () => {
    form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.configuration.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Configuración del evento" />

    <AppLayout title="Configuración del evento">
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Configuración del evento</h2>
                <Link :href="route('admin.dashboard')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Volver al dashboard</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200" @submit.prevent="submit">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombres de los novios</label>
                        <input id="name" v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500" required>
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700">Fecha del evento</label>
                        <input id="event_date" v-model="form.event_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500" required>
                        <p v-if="form.errors.event_date" class="mt-1 text-sm text-red-600">{{ form.errors.event_date }}</p>
                    </div>

                    <div>
                        <label for="cover_image" class="block text-sm font-medium text-gray-700">Imagen de portada</label>
                        <input id="cover_image" type="file" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-600" @input="form.cover_image = $event.target.files[0]">
                        <p v-if="form.errors.cover_image" class="mt-1 text-sm text-red-600">{{ form.errors.cover_image }}</p>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Mensaje principal</label>
                        <textarea id="message" v-model="form.message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500" />
                    </div>

                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Color principal</label>
                        <input id="primary_color" v-model="form.primary_color" type="text" placeholder="#000000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                    </div>

                    <div>
                        <label for="thank_you_message" class="block text-sm font-medium text-gray-700">Mensaje posterior a la subida</label>
                        <textarea id="thank_you_message" v-model="form.thank_you_message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500" />
                    </div>

                    <label class="flex items-center gap-3">
                        <input v-model="form.upload_enabled" type="checkbox" class="rounded border-gray-300 text-gray-800 shadow-sm focus:ring-gray-500">
                        <span class="text-sm font-medium text-gray-700">Recepción de fotografías abierta</span>
                    </label>

                    <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-5">
                        <span v-if="form.recentlySuccessful" class="text-sm text-emerald-600">Guardado correctamente.</span>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50">
                            Guardar configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
