<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, CreditCard, FileText, Folder, FolderArchive, History, LayoutGrid, Package, ShieldCheck, ShoppingCart, TrendingUp, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { Component, computed } from 'vue';

interface NavItem {
    title: string;
    url: string;
    icon: Component;
    permission?: string;
}

const page = usePage<SharedData>();

const can = (permission?: string) => {
    if (!permission) return true;
    return page.props.auth.permissions.includes(permission);
};

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutGrid,
        permission: 'dashboard.view',
    },
];

const masterNavItems: NavItem[] = [
    {
        title: 'Category',
        url: '/master/category',
        icon: FileText,
        permission: 'master.category.view',
    },
    {
        title: 'Unit',
        url: '/master/unit',
        icon: FolderArchive,
        permission: 'master.unit.view',
    },
    {
        title: 'Product',
        url: '/master/product',
        icon: ShoppingCart,
        permission: 'master.product.view',
    },
    {
        title: 'Package',
        url: '/master/package',
        icon: Package,
        permission: 'master.package.view',
    },
];

const transactionNavItems: NavItem[] = [
    {
        title: 'Product Movement',
        url: '/product-movement',
        icon: TrendingUp,
        permission: 'product-movement.view',
    },
    {
        title: 'Transaction',
        url: '/transaction',
        icon: History,
        permission: 'transaction.view',
    },
    {
        title: 'Cashier',
        url: '/cashier',
        icon: CreditCard,
        permission: 'cashier.view',
    },
];

const userManagementNavItems: NavItem[] = [
    {
        title: 'User',
        url: '/user-management/user',
        icon: Users,
        permission: 'user-management.user.view',
    },
    {
        title: 'Role & Permission',
        url: '/user-management/role',
        icon: ShieldCheck,
        permission: 'user-management.role.view',
    },
];

const filtered = (items: NavItem[]) => items.filter((item) => can(item.permission));
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
            <NavMain v-if="filtered(mainNavItems).length" :title="'Dashboard'" :items="filtered(mainNavItems)" />
            <NavMain v-if="filtered(masterNavItems).length" :title="'Master'" :items="filtered(masterNavItems)" />
            <NavMain v-if="filtered(transactionNavItems).length" :title="'Transaction'" :items="filtered(transactionNavItems)" />
            <NavMain v-if="filtered(userManagementNavItems).length" :title="'User Management'" :items="filtered(userManagementNavItems)" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
