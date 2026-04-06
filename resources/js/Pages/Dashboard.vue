<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-black leading-tight text-gray-900">Dashboard</h2>
        </template>

        <div class="p-6 sm:p-8">
            <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-white to-[#f7f8ff] p-6 sm:p-8 shadow-[0_12px_30px_rgba(15,23,42,0.06)]">
                <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary/50 mb-2">Welcome</p>
                <h3 class="text-2xl font-black text-gray-900 mb-2">You're logged in</h3>
                <p class="text-sm text-gray-500">Panel user sudah siap digunakan. Lanjutkan ke menu yang kamu butuhkan.</p>
                <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary/10 text-primary text-sm font-bold">
                    Status: Active Session
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
