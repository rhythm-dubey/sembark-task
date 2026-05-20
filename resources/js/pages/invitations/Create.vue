<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InvitationController from '@/actions/App/Http/Controllers/InvitationController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create as invitationsCreate } from '@/routes/invitations';

type RoleOption = {
    id: number;
    name: string;
    label: string;
};

type CompanyOption = {
    id: number;
    name: string;
};

const props = defineProps<{
    companies: CompanyOption[];
    roles: RoleOption[];
    rolesForExistingClient: RoleOption[];
    isSuperAdmin: boolean;
    defaultNewCompany: boolean;
}>();

const newCompany = ref(props.defaultNewCompany);

const availableRoles = computed(() =>
    props.isSuperAdmin && !newCompany.value
        ? props.rolesForExistingClient
        : props.roles,
);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Invite User',
                href: invitationsCreate(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Invite User" />

    <h1 class="sr-only">Invite User</h1>

    <div class="flex w-full max-w-lg flex-col gap-6 p-4 md:p-6">
        <Heading
            title="Invite User"
            description="Create a new user account for your organization"
        />

        <Form
            v-bind="InvitationController.store.form()"
            class="w-full space-y-6"
            v-slot="{ errors, processing }"
        >
            <template v-if="isSuperAdmin">
                <div class="flex items-center gap-2">
                    <input
                        id="new_company"
                        v-model="newCompany"
                        type="checkbox"
                        name="new_company"
                        value="1"
                        class="size-4 rounded border"
                    />
                    <Label for="new_company">New client company</Label>
                </div>

                <div v-if="newCompany" class="grid gap-2">
                    <Label for="company_name">Client name</Label>
                    <Input
                        id="company_name"
                        name="company_name"
                        :required="newCompany"
                    />
                    <InputError :message="errors.company_name" />
                </div>

                <div v-else class="grid gap-2">
                    <Label for="company_id">Existing client</Label>
                    <select
                        id="company_id"
                        name="company_id"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                        :required="!newCompany"
                    >
                        <option value="">Select client</option>
                        <option
                            v-for="company in companies"
                            :key="company.id"
                            :value="company.id"
                        >
                            {{ company.name }}
                        </option>
                    </select>
                    <InputError :message="errors.company_id" />
                </div>
            </template>

            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input id="name" name="name" required />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input id="email" name="email" type="email" required />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="role_id">Role</Label>
                <select
                    id="role_id"
                    name="role_id"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                    required
                >
                    <option value="">Select role</option>
                    <option
                        v-for="role in availableRoles"
                        :key="role.id"
                        :value="role.id"
                    >
                        {{ role.label }}
                    </option>
                </select>
                <InputError :message="errors.role_id" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <Input id="password" name="password" type="password" required />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <Input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                />
            </div>

            <Button type="submit" :disabled="processing">Send invite</Button>
        </Form>
    </div>
</template>
