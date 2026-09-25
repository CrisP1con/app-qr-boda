<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Acceso administrador" />

    <main class="flex min-h-screen items-center justify-center bg-stone-950 px-5 py-10 text-stone-100 sm:px-8">
        <div class="w-full max-w-md">
            <Link href="/boda" class="text-sm text-stone-400 transition hover:text-white">← Volver a la boda</Link>

            <section class="mt-8 rounded-3xl border border-stone-800 bg-stone-900/80 p-7 shadow-2xl sm:p-10">
                <svg class="mx-auto size-12 text-sky-500" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Logo de la aplicación" role="img">
                    <path d="M13 29.5C15.4 24.5 18.9 22 23.5 22c3.1 0 5.6 1 7.5 3" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                    <path d="M17 36.5C19.4 31.5 22.9 29 27.5 29c3.1 0 5.6 1 7.5 3" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                </svg>
                <h1 class="mt-4 text-center font-serif text-4xl text-white">Administración</h1>
                <p class="mt-3 text-center text-sm leading-6 text-stone-400">Ingresá para configurar el álbum de la boda.</p>

                <p v-if="status" class="mt-6 rounded-xl border border-emerald-800 bg-emerald-950/50 p-3 text-sm text-emerald-200">{{ status }}</p>

                <form class="mt-8 space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="email" class="block text-sm font-medium text-stone-200">Email</label>
                        <input id="email" v-model="form.email" type="email" required autofocus autocomplete="username" class="mt-2 block w-full rounded-xl border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 shadow-sm outline-none transition placeholder:text-stone-600 focus:border-[#d6b47a] focus:ring-2 focus:ring-[#d6b47a]/30">
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-300">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-stone-200">Contraseña</label>
                        <input id="password" v-model="form.password" type="password" required autocomplete="current-password" class="mt-2 block w-full rounded-xl border-stone-700 bg-stone-950 px-4 py-3 text-stone-100 shadow-sm outline-none transition placeholder:text-stone-600 focus:border-[#d6b47a] focus:ring-2 focus:ring-[#d6b47a]/30">
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-300">{{ form.errors.password }}</p>
                    </div>

                    <label class="flex items-center gap-3 text-sm text-stone-400">
                        <input v-model="form.remember" type="checkbox" class="rounded border-stone-700 bg-stone-950 text-[#d6b47a] focus:ring-[#d6b47a]">
                        Recordarme
                    </label>

                    <div class="flex items-center justify-between gap-4 pt-2">
                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-stone-400 underline underline-offset-4 transition hover:text-white">¿Olvidaste tu contraseña?</Link>
                        <button type="submit" :disabled="form.processing" class="ml-auto rounded-full bg-[#d6b47a] px-6 py-3 font-semibold text-stone-950 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50">
                            {{ form.processing ? 'Ingresando…' : 'Ingresar' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</template>
