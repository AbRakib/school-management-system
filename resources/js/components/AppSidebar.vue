<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    BookOpen,
    Briefcase,
    Bus,
    CalendarDays,
    ClipboardCheck,
    ClipboardList,
    Clock,
    FileText,
    GraduationCap,
    IdCard,
    Landmark,
    LayoutGrid,
    Library,
    LogOut,
    Megaphone,
    Route,
    School,
    Settings,
    ShieldCheck,
    Tags,
    Truck,
    User,
    UserPlus,
    Users,
    Wallet,
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
            { title: 'Academic Sessions', href: '#', icon: CalendarDays },
            { title: 'Classes', href: '/classes', icon: School },
            { title: 'Sections', href: '#', icon: Tags },
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
            {
                title: 'Student Categories',
                href: '/student-categories',
                icon: Tags,
            },
            { title: 'ID Cards', href: '/students/id-cards', icon: IdCard },
        ],
    },
    {
        title: 'Staff',
        href: '#',
        icon: Users,
        items: [
            { title: 'All Staff', href: '/staff', icon: Users },
            {
                title: 'Add New Staff',
                href: '/staff/create',
                icon: UserPlus,
            },
            {
                title: 'Departments/Designations',
                href: '/departments',
                icon: Briefcase,
            },
            {
                title: 'Class & Subject Assignment',
                href: '/staff',
                icon: ClipboardCheck,
            },
        ],
    },
    {
        title: 'Attendance',
        href: '#',
        icon: ClipboardCheck,
        items: [
            {
                title: 'Student Attendance',
                href: '/attendance/students',
                icon: GraduationCap,
            },
            {
                title: 'Staff Attendance',
                href: '/attendance/staff',
                icon: Users,
            },
            {
                title: 'Attendance Reports',
                href: '/attendance/students/report',
                icon: BarChart3,
            },
        ],
    },
    {
        title: 'Exams & Results',
        href: '#',
        icon: FileText,
        items: [
            { title: 'Exam Types', href: '/exam-types', icon: Tags },
            { title: 'Exam Schedule', href: '/exams', icon: CalendarDays },
            { title: 'Marks Entry', href: '/exams', icon: ClipboardList },
            {
                title: 'Grading System',
                href: '/grading-scales',
                icon: ShieldCheck,
            },
            { title: 'Report Cards', href: '/exams', icon: FileText },
        ],
    },
    {
        title: 'Fees & Accounts',
        href: '#',
        icon: Wallet,
        items: [
            { title: 'Fee Structure', href: '/fee-structures', icon: FileText },
            { title: 'Fee Collection', href: '/fees/collect', icon: Wallet },
            {
                title: 'Due/Pending Fees',
                href: '/fees/due',
                icon: ClipboardList,
            },
            { title: 'Expenses', href: '/expenses', icon: Briefcase },
            {
                title: 'Account Balance',
                href: '/accounts/balance',
                icon: Landmark,
            },
        ],
    },
    {
        title: 'Timetable',
        href: '#',
        icon: Clock,
        items: [
            {
                title: 'Class Routine',
                href: '/timetable/builder',
                icon: CalendarDays,
            },
            {
                title: 'Teacher Schedule',
                href: '/timetable/teacher',
                icon: Clock,
            },
        ],
    },
    {
        title: 'Library',
        href: '#',
        icon: Library,
        items: [
            { title: 'Book Catalog', href: '/books', icon: BookOpen },
            {
                title: 'Issue/Return',
                href: '/library/issue',
                icon: ClipboardCheck,
            },
            { title: 'Due Books', href: '/library/due', icon: ClipboardList },
        ],
    },
    {
        title: 'Transport',
        href: '#',
        icon: Bus,
        items: [
            { title: 'Routes', href: '/transport-routes', icon: Route },
            { title: 'Vehicles', href: '/vehicles', icon: Truck },
            {
                title: 'Student Assignment',
                href: '/student-transports',
                icon: GraduationCap,
            },
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
        title: 'Reports',
        href: '#',
        icon: BarChart3,
        items: [
            {
                title: 'Attendance Report',
                href: '/reports/attendance',
                icon: ClipboardCheck,
            },
            {
                title: 'Result Report',
                href: '/reports/results',
                icon: FileText,
            },
            {
                title: 'Fee Collection Report',
                href: '/reports/fee-collection',
                icon: Wallet,
            },
            {
                title: 'Financial Report',
                href: '/reports/financial',
                icon: BarChart3,
            },
        ],
    },
    {
        title: 'Settings',
        href: '#',
        icon: Settings,
        items: [
            { title: 'Roles & Permissions', href: '#', icon: ShieldCheck },
            {
                title: 'School Profile',
                href: '/settings/profile',
                icon: School,
            },
            { title: 'Grading Scale', href: '#', icon: FileText },
            { title: 'System Settings', href: '/settings', icon: Settings },
        ],
    },
    profileItem,
    logoutItem,
];

const teacherNavItems: RoleNavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Mark Attendance', href: '#', icon: ClipboardCheck },
    { title: 'Marks Entry', href: '#', icon: ClipboardList },
    { title: 'My Schedule', href: '#', icon: Clock },
    { title: 'Notices', href: '/notices', icon: Megaphone },
    profileItem,
    logoutItem,
];

const studentParentNavItems: RoleNavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'My Attendance', href: '#', icon: ClipboardCheck },
    { title: 'My Results', href: '#', icon: FileText },
    { title: 'Fee Status', href: '#', icon: Wallet },
    { title: 'Class Routine', href: '#', icon: CalendarDays },
    { title: 'Notices', href: '/notices', icon: Megaphone },
    profileItem,
    logoutItem,
];

const accountantNavItems: RoleNavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Fee Collection', href: '#', icon: Wallet },
    { title: 'Due Fees', href: '#', icon: ClipboardList },
    { title: 'Expenses', href: '#', icon: Briefcase },
    { title: 'Account Balance', href: '#', icon: Landmark },
    { title: 'Financial Report', href: '/reports/financial', icon: BarChart3 },
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
