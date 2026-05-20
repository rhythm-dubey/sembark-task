<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { index as companiesIndex } from '@/routes/companies';

type CompanyRow = {
    id: number;
    name: string;
    users_count: number;
    short_urls_count: number;
    total_hits: number | null;
};

defineProps<{
    companies: CompanyRow[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clients',
                href: companiesIndex(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Clients" />

    <h1 class="sr-only">Clients</h1>

    <div class="flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <Heading
            title="Clients"
            description="Organizations using the URL shortener"
        />

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-sidebar-border/70 bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 font-medium">Client Name</th>
                        <th class="px-4 py-3 font-medium">Users</th>
                        <th class="px-4 py-3 font-medium">Total URLs</th>
                        <th class="px-4 py-3 font-medium">Total Hits</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="company in companies"
                        :key="company.id"
                        class="border-b border-sidebar-border/50"
                    >
                        <td class="px-4 py-3">{{ company.name }}</td>
                        <td class="px-4 py-3">{{ company.users_count }}</td>
                        <td class="px-4 py-3">{{ company.short_urls_count }}</td>
                        <td class="px-4 py-3">{{ company.total_hits ?? 0 }}</td>
                    </tr>
                    <tr v-if="companies.length === 0">
                        <td
                            class="px-4 py-8 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No clients yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
