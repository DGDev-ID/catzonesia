<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
// import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, CreditCard, FileText, Folder, FolderArchive, History, LayoutGrid, Package, ShoppingCart, TrendingUp } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { Component } from 'vue';

interface NavItem {
    title: string;
    url: string;
    icon: Component;
}

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutGrid,
    },
];

const masterNavItems: NavItem[] = [

    {
        title: 'Category',
        url: '/master/category',
        icon: FileText,
    },
    {
        title: 'Unit',
        url: '/master/unit',
        icon: FolderArchive,
    },
    {
        title: 'Product',
        url: '/master/product',
        icon: ShoppingCart,
    },
    {
        title: 'Package',
        url: '/master/package',
        icon: Package,
    },
];

const transactionNavItems: NavItem[] = [
    {
        title: 'Product Movement',
        url: '/product-movement',
        icon: TrendingUp,
    },
    {
        title: 'Transaction',
        url: '/transaction',
        icon: History,
    },
    {
        title: 'Cashier',
        url: '/cashier',
        icon: CreditCard,
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
            <NavMain :title="'Dashboard'" :items="mainNavItems" />
            <NavMain :title="'Master'" :items="masterNavItems" />
            <NavMain :title="'Transaction'" :items="transactionNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
