<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Building2,
    FolderGit2,
    LayoutGrid,
    Link2,
    Plus,
    UserPlus,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { index as companiesIndex } from '@/routes/companies';
import { create as invitationsCreate } from '@/routes/invitations';
import { create as shortUrlsCreate, index as shortUrlsIndex } from '@/routes/short-urls';
import { dashboard } from '@/routes';
import type { Auth, NavItem } from '@/types';

const page = usePage<{ auth: Auth }>();
const user = computed(() => page.props.auth.user);

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (user.value?.is_super_admin) {
        items.push({
            title: 'Clients',
            href: companiesIndex(),
            icon: Building2,
        });
        items.push({
            title: 'Invite Client',
            href: invitationsCreate(),
            icon: UserPlus,
        });
    }

    if (user.value?.can_view_short_urls) {
        items.push({
            title: 'Short URLs',
            href: shortUrlsIndex(),
            icon: Link2,
        });
    }

    if (user.value?.can_create_short_urls) {
        items.push({
            title: 'Create Short URL',
            href: shortUrlsCreate(),
            icon: Plus,
        });
    }

    if (user.value?.can_invite && !user.value?.is_super_admin) {
        items.push({
            title: 'Invite Team Member',
            href: invitationsCreate(),
            icon: UserPlus,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
