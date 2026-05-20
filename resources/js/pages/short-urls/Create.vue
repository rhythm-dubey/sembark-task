<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ShortUrlController from '@/actions/App/Http/Controllers/ShortUrlController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index as shortUrlsIndex } from '@/routes/short-urls';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Short URLs',
                href: shortUrlsIndex(),
            },
            {
                title: 'Create',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Create Short URL" />

    <h1 class="sr-only">Create Short URL</h1>

    <div class="flex w-full max-w-lg flex-col gap-6 p-4 md:p-6">
        <Heading
            title="Create Short URL"
            description="Enter a long URL to generate a shortened link"
        />

        <Form
            v-bind="ShortUrlController.store.form()"
            class="w-full space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="original_url">Long URL</Label>
                <Input
                    id="original_url"
                    name="original_url"
                    type="url"
                    required
                    placeholder="https://example.com/page"
                />
                <InputError :message="errors.original_url" />
            </div>

            <Button type="submit" :disabled="processing">
                Generate
            </Button>
        </Form>
    </div>
</template>
