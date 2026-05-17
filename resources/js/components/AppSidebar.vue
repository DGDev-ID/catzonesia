<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, CreditCard, FileText, Folder, LayoutGrid, Package, ShoppingCart, TrendingUp } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Master',
        icon: Package,
        children: [
            {
                title: 'Category',
                href: '/master/category',
                icon: FileText,
            },
            {
                title: 'Unit',
                href: '/master/unit',
                icon: FileText,
            },
            {
                title: 'Product',
                href: '/master/product',
                icon: ShoppingCart,
            },
            {
                title: 'Package',
                href: '/master/package',
                icon: Package,
            },
        ],
    },
    {
        title: 'Product Movement',
        href: '/product-movement',
        icon: TrendingUp,
    },
    {
        title: 'Transaction',
        href: '/transaction',
        icon: FileText,
    },
    {
        title: 'Cashier',
        href: '/cashier',
        icon: CreditCard,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
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
                        <Link :href="route('dashboard')">
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
