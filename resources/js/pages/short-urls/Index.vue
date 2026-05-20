<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { index as shortUrlsIndex, resolve } from '@/routes/short-urls';

type ShortUrlRow = {
    id: number;
    code: string;
    original_url: string;
    hits: number;
    created_at: string;
    company: { id: number; name: string };
    user: { id: number; name: string };
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

defineProps<{
    shortUrls: Paginated<ShortUrlRow>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Short URLs',
                href: shortUrlsIndex(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Short URLs" />

    <h1 class="sr-only">Short URLs</h1>

    <div class="flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
        <Heading
            title="Short URLs"
            description="View shortened links you are allowed to access"
        />

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-sidebar-border/70 bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 font-medium">Short URL</th>
                        <th class="px-4 py-3 font-medium">Long URL</th>
                        <th class="px-4 py-3 font-medium">Client</th>
                        <th class="px-4 py-3 font-medium">User</th>
                        <th class="px-4 py-3 font-medium">Hits</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="shortUrl in shortUrls.data"
                        :key="shortUrl.id"
                        class="border-b border-sidebar-border/50"
                    >
                        <td class="px-4 py-3">
                            <Link
                                :href="resolve(shortUrl.code)"
                                class="text-primary underline"
                            >
                                /s/{{ shortUrl.code }}
                            </Link>
                        </td>
                        <td class="max-w-xs truncate px-4 py-3">
                            {{ shortUrl.original_url }}
                        </td>
                        <td class="px-4 py-3">{{ shortUrl.company.name }}</td>
                        <td class="px-4 py-3">{{ shortUrl.user.name }}</td>
                        <td class="px-4 py-3">{{ shortUrl.hits }}</td>
                        <td class="px-4 py-3">
                            {{ new Date(shortUrl.created_at).toLocaleString() }}
                        </td>
                    </tr>
                    <tr v-if="shortUrls.data.length === 0">
                        <td
                            class="px-4 py-8 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No short URLs found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav
            v-if="shortUrls.links.length > 3"
            class="flex flex-wrap gap-2"
        >
            <Link
                v-for="link in shortUrls.links"
                :key="link.label"
                :href="link.url ?? '#'"
                class="rounded-md border px-3 py-1 text-sm"
                :class="{
                    'bg-primary text-primary-foreground': link.active,
                    'pointer-events-none opacity-50': !link.url,
                }"
                v-html="link.label"
            />
        </nav>
    </div>
</template>
