<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    GraduationCap,
    IdCard,
    LayoutGrid,
    LogOut,
    Megaphone,
    School,
    Settings,
    User,
    UserPlus,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
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
import { dashboard, logout } from '@/routes';
import type { Auth } from '@/types';
import type { NavMainItem } from './NavMain.vue';

type RoleNavItem = NavMainItem;

const page = usePage<{ auth: Auth }>();

const profileItem: RoleNavItem = {
    title: 'My Profile',
    href: '/settings/profile',
    icon: User,
};

const logoutItem: RoleNavItem = {
    title: 'Logout',
    href: logout(),
    icon: LogOut,
    method: 'post',
};

const adminNavItems: RoleNavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Academic',
        href: '#',
        icon: School,
        items: [
            { title: 'Classes', href: '/classes', icon: School },
            { title: 'Subjects', href: '/subjects', icon: BookOpen },
        ],
    },
    {
        title: 'Students',
        href: '#',
        icon: GraduationCap,
        items: [
            { title: 'All Students', href: '/students', icon: GraduationCap },
            {
                title: 'Add New Student',
                href: '/students/create',
                icon: UserPlus,
            },
            { title: 'ID Cards', href: '/students/id-cards', icon: IdCard },
        ],
    },
    {
        title: 'Teachers',
        href: '#',
        icon: Users,
        items: [
            { title: 'All Teachers', href: '/teachers', icon: Users },
            {
                title: 'Add New Teacher',
                href: '/teachers/create',
                icon: UserPlus,
            },
        ],
    },
    {
        title: 'Parents',
        href: '#',
        icon: Users,
        items: [
            { title: 'All Parents', href: '/guardians', icon: Users },
            { title: 'Add New Parent', href: '/guardians/create', icon: UserPlus },
        ],
    },
    {
        title: 'Notice Board',
        href: '#',
        icon: Megaphone,
        items: [
            { title: 'All Notices', href: '/notices', icon: Megaphone },
            { title: 'Add Notice', href: '/notices/create', icon: UserPlus },
        ],
    },
    {
        title: 'Settings',
        href: '#',
        icon: Settings,
        items: [
            {
                title: 'School Profile',
                href: '/settings/profile',
                icon: School,
            },
            { title: 'Account Security', href: '/settings/security', icon: Settings },
        ],
    },
    profileItem,
    logoutItem,
];

const teacherNavItems: RoleNavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Students', href: '/students', icon: GraduationCap },
    { title: 'Notices', href: '/notices', icon: Megaphone },
    profileItem,
    logoutItem,
];

const studentParentNavItems: RoleNavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Notices', href: '/notices', icon: Megaphone },
    profileItem,
    logoutItem,
];

const accountantNavItems: RoleNavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Notices', href: '/notices', icon: Megaphone },
    profileItem,
    logoutItem,
];

const visibleNavItems = computed(() => {
    const roles = page.props.auth.roles ?? [];

    if (roles.some((role) => ['super-admin', 'admin'].includes(role))) {
        return adminNavItems;
    }

    if (roles.includes('teacher')) {
        return teacherNavItems;
    }

    if (roles.some((role) => ['student', 'parent'].includes(role))) {
        return studentParentNavItems;
    }

    if (roles.includes('accountant')) {
        return accountantNavItems;
    }

    return [profileItem, logoutItem];
});
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
            <NavMain :items="visibleNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
