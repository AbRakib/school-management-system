<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Banknote,
    CalendarDays,
    ClipboardCheck,
    ClipboardList,
    ExternalLink,
    GraduationCap,
    Megaphone,
    Plus,
    School,
    Users,
    Wallet,
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import NoticeWidget from '@/components/notices/NoticeWidget.vue';
import { dashboard, home } from '@/routes';

type Stats = {
    students: number;
    teachers: number;
    staff: number;
    parents: number;
    classes: number;
    sections: number;
    newAdmissionsThisMonth: number;
    activeNotices: number;
};

type SchoolClass = {
    id: number;
    name: string;
    grade_level: number;
    section: string | null;
    students_count: number;
    class_teacher: { id: number; name: string } | null;
};

type RecentStudent = {
    id: number;
    admission_no: string | null;
    admission_number: string;
    name: string;
    admission_date: string;
    school_class: { id: number; name: string };
    guardian: { id: number; name: string };
};

type LatestNotice = {
    id: number;
    title: string;
    audience: string;
    publish_date: string;
    expiry_date: string | null;
};

const props = defineProps<{
    roleDashboard: 'admin' | 'teacher' | 'student' | 'parent' | 'accountant';
    stats: Stats;
    classes: SchoolClass[];
    recentStudents: RecentStudent[];
    latestNotices: LatestNotice[];
}>();

const formatCurrency = (amount: number) =>
    new Intl.NumberFormat('en-BD', {
        style: 'currency',
        currency: 'BDT',
        maximumFractionDigits: 0,
    }).format(amount);

const attendance = {
    present: Math.round(props.stats.students * 0.948),
    late: Math.round(props.stats.students * 0.018),
};

const absent = Math.max(
    props.stats.students - attendance.present - attendance.late,
    0,
);
const attendancePercent = props.stats.students
    ? ((attendance.present / props.stats.students) * 100).toFixed(1)
    : '0.0';

const adminStatCards = [
    {
        label: 'Total Students',
        value: props.stats.students,
        helper: 'By class / section',
        href: '/students',
        icon: GraduationCap,
        color: 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
    },
    {
        label: 'Total Staff',
        value: props.stats.staff,
        helper: `${props.stats.teachers} teachers`,
        href: '/teachers',
        icon: Users,
        color: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    },
    {
        label: 'Classes & Sections',
        value: props.stats.classes,
        helper: `${props.stats.sections} active sections`,
        href: '/classes',
        icon: School,
        color: 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
    },
    {
        label: "Today's Collection",
        value: formatCurrency(45200),
        helper: 'Demo finance module',
        href: '#',
        icon: Banknote,
        color: 'bg-teal-500/10 text-teal-600 dark:text-teal-400',
    },
    {
        label: 'Total Due',
        value: formatCurrency(210500),
        helper: 'Outstanding fees',
        href: '#',
        icon: Wallet,
        color: 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
    },
    {
        label: 'Active Notices',
        value: props.stats.activeNotices,
        helper: 'Latest board posts',
        href: '/notices',
        icon: Megaphone,
        color: 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400',
    },
    {
        label: 'Present Today',
        value: `${attendance.present} / ${props.stats.students}`,
        helper: `${attendancePercent}% attendance`,
        href: '#',
        icon: ClipboardCheck,
        color: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
    },
    {
        label: 'New Admissions',
        value: props.stats.newAdmissionsThisMonth,
        helper: 'This month',
        href: '/students',
        icon: Plus,
        color: 'bg-fuchsia-500/10 text-fuchsia-600 dark:text-fuchsia-400',
    },
];

const feeTrend = [22000, 31000, 28000, 36400, 42000, 39200, 45200];
const maxFeeTrend = Math.max(...feeTrend);
const classDistribution = props.classes.map((schoolClass) => ({
    label: schoolClass.name,
    value: schoolClass.students_count,
}));
const maxClassStudents = Math.max(
    ...classDistribution.map((item) => item.value),
    1,
);

const recentFeePayments = [
    { student: 'Student 1', amount: 6500, date: 'Today' },
    { student: 'Student 4', amount: 4200, date: 'Today' },
    { student: 'Student 7', amount: 7800, date: 'Yesterday' },
    { student: 'Student 2', amount: 3600, date: 'Yesterday' },
];

const pendingDues = [
    { student: 'Student 9', amount: 18500 },
    { student: 'Student 3', amount: 15400 },
    { student: 'Student 6', amount: 12100 },
    { student: 'Student 8', amount: 9800 },
    { student: 'Student 5', amount: 7600 },
];

const upcomingExams = [
    { title: 'Half Yearly Exam', date: 'Starts in 12 days' },
    { title: 'Class Test - Science', date: 'Starts in 5 days' },
    { title: 'Final Term Prep', date: 'Starts in 28 days' },
];

const quickActions = [
    { label: 'Add Student', href: '/students/create', icon: Plus },
    { label: 'Collect Fee', href: '#', icon: Banknote },
    { label: 'Post Notice', href: '#', icon: Megaphone },
    { label: 'Mark Attendance', href: '#', icon: ClipboardCheck },
];

const rolePanels = {
    teacher: {
        title: 'Teacher dashboard',
        description:
            'Assigned classes, schedule, and marks entry will appear here as the academic modules come online.',
        items: [
            { label: 'Assigned classes', value: '0', icon: School },
            { label: "Today's schedule", value: '0', icon: CalendarDays },
            { label: 'Pending marks', value: '0', icon: ClipboardList },
        ],
    },
    student: {
        title: 'Student dashboard',
        description:
            'Attendance, upcoming exams, fee status, and notices will collect here for each student.',
        items: [
            { label: 'Attendance', value: '0%', icon: ClipboardCheck },
            { label: 'Upcoming exams', value: '0', icon: CalendarDays },
            { label: 'Fee due', value: '0', icon: Banknote },
        ],
    },
    parent: {
        title: 'Parent dashboard',
        description:
            "A parent view for children's attendance, fee due, upcoming exams, and school notices.",
        items: [
            { label: 'Attendance', value: '0%', icon: ClipboardCheck },
            { label: 'Upcoming exams', value: '0', icon: CalendarDays },
            { label: 'Fee due', value: '0', icon: Banknote },
        ],
    },
    accountant: {
        title: 'Accountant dashboard',
        description:
            'Fee collection, expenses, dues, and balances will live here once the accounts module is added.',
        items: [
            { label: 'Fees collected', value: '0', icon: Banknote },
            { label: 'Pending dues', value: '0', icon: ClipboardList },
            { label: 'Expenses', value: '0', icon: ClipboardCheck },
        ],
    },
};

const currentRolePanel =
    props.roleDashboard === 'admin' ? null : rolePanels[props.roleDashboard];

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-6 p-4 md:p-6">
        <div
            v-if="roleDashboard === 'admin'"
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Admin dashboard
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    A live operating view across academics, attendance, finance,
                    and recent activity.
                </p>
            </div>
            <Button as-child>
                <Link :href="home()">
                    <ExternalLink class="size-4" />
                    Visit now
                </Link>
            </Button>
        </div>

        <div v-else-if="currentRolePanel" class="space-y-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
            >
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        {{ currentRolePanel.title }}
                    </h1>
                    <p class="mt-1 max-w-3xl text-sm text-muted-foreground">
                        {{ currentRolePanel.description }}
                    </p>
                </div>
                <Button as-child>
                    <Link :href="home()">
                        <ExternalLink class="size-4" />
                        Visit now
                    </Link>
                </Button>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card v-for="item in currentRolePanel.items" :key="item.label">
                    <CardContent class="flex items-center gap-4">
                        <div
                            class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            <component :is="item.icon" class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                {{ item.label }}
                            </p>
                            <p class="text-3xl font-semibold">
                                {{ item.value }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <NoticeWidget :notices="latestNotices" />
        </div>

        <div
            v-if="roleDashboard === 'admin'"
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            <Link
                v-for="item in adminStatCards"
                :key="item.label"
                :href="item.href"
                class="rounded-xl focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
            >
                <Card
                    class="h-full transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <CardContent class="flex items-center gap-4">
                        <div
                            :class="[
                                'flex size-12 items-center justify-center rounded-xl',
                                item.color,
                            ]"
                        >
                            <component :is="item.icon" class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                {{ item.label }}
                            </p>
                            <p class="text-2xl font-semibold">
                                {{ item.value }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ item.helper }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <div v-if="roleDashboard === 'admin'" class="grid gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Fee collection trend</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex h-60 items-end gap-3">
                        <div
                            v-for="(amount, index) in feeTrend"
                            :key="index"
                            class="flex flex-1 flex-col items-center gap-2"
                        >
                            <div
                                class="w-full rounded-t-md bg-primary/80"
                                :style="{
                                    height: `${Math.max((amount / maxFeeTrend) * 100, 8)}%`,
                                }"
                            />
                            <span class="text-xs text-muted-foreground">
                                D{{ index + 1 }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Attendance overview</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-6 sm:grid-cols-2">
                    <div class="flex items-center justify-center">
                        <div
                            class="grid size-44 place-items-center rounded-full"
                            :style="{
                                background: `conic-gradient(hsl(var(--primary)) 0 ${attendancePercent}%, hsl(var(--muted)) ${attendancePercent}% 100%)`,
                            }"
                        >
                            <div
                                class="grid size-32 place-items-center rounded-full bg-card text-center"
                            >
                                <div>
                                    <p class="text-3xl font-semibold">
                                        {{ attendancePercent }}%
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        Present today
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">Present</span>
                            <span class="font-medium">{{
                                attendance.present
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">Absent</span>
                            <span class="font-medium">{{ absent }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">Late</span>
                            <span class="font-medium">{{
                                attendance.late
                            }}</span>
                        </div>
                        <div class="rounded-lg bg-muted p-3">
                            <p class="font-medium">Not marked</p>
                            <p class="text-muted-foreground">
                                Class 4, Class 8
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="roleDashboard === 'admin'" class="grid gap-6 xl:grid-cols-3">
            <Card>
                <CardHeader>
                    <CardTitle>Financial snapshot</CardTitle>
                </CardHeader>
                <CardContent class="space-y-5">
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Account balance
                        </p>
                        <p class="text-4xl font-semibold">
                            {{ formatCurrency(824300) }}
                        </p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-muted p-3">
                            <p class="text-xs text-muted-foreground">
                                Income this month
                            </p>
                            <p class="text-lg font-semibold">
                                {{ formatCurrency(356800) }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-muted p-3">
                            <p class="text-xs text-muted-foreground">
                                Expense this month
                            </p>
                            <p class="text-lg font-semibold">
                                {{ formatCurrency(184200) }}
                            </p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <p class="text-sm font-medium">Top pending dues</p>
                        <div
                            v-for="due in pendingDues"
                            :key="due.student"
                            class="flex items-center justify-between text-sm"
                        >
                            <span class="text-muted-foreground">{{
                                due.student
                            }}</span>
                            <span class="font-medium">{{
                                formatCurrency(due.amount)
                            }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Academic snapshot</CardTitle>
                </CardHeader>
                <CardContent class="space-y-5">
                    <div class="rounded-lg bg-muted p-4">
                        <p class="text-sm text-muted-foreground">
                            Academic session
                        </p>
                        <p class="text-2xl font-semibold">2025-2026, Term 2</p>
                    </div>
                    <div class="space-y-3">
                        <p class="text-sm font-medium">Upcoming exams</p>
                        <div
                            v-for="exam in upcomingExams"
                            :key="exam.title"
                            class="flex items-center justify-between gap-4 text-sm"
                        >
                            <span>{{ exam.title }}</span>
                            <Badge variant="secondary">{{ exam.date }}</Badge>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-between rounded-lg border p-3 text-sm"
                    >
                        <span class="text-muted-foreground">
                            Recent results published
                        </span>
                        <Button variant="outline" size="sm" as-child>
                            <Link href="#">View</Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Class distribution</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="item in classDistribution"
                        :key="item.label"
                        class="space-y-1"
                    >
                        <div class="flex justify-between text-sm">
                            <span>{{ item.label }}</span>
                            <span class="text-muted-foreground">{{
                                item.value
                            }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-muted">
                            <div
                                class="h-2 rounded-full bg-primary"
                                :style="{
                                    width: `${Math.max((item.value / maxClassStudents) * 100, 6)}%`,
                                }"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="roleDashboard === 'admin'" class="grid gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader
                    class="flex-row items-center justify-between space-y-0"
                >
                    <CardTitle>Recent admissions</CardTitle>
                    <Button variant="outline" size="sm" as-child>
                        <Link href="/students">View all</Link>
                    </Button>
                </CardHeader>
                <CardContent class="overflow-x-auto">
                    <table class="w-full min-w-140 text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr>
                                <th class="py-3 font-medium">Student</th>
                                <th class="py-3 font-medium">Class</th>
                                <th class="py-3 font-medium">Guardian</th>
                                <th class="py-3 text-right font-medium">ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="student in recentStudents"
                                :key="student.id"
                                class="border-b last:border-0"
                            >
                                <td class="py-3 font-medium">
                                    {{ student.name }}
                                </td>
                                <td class="py-3 text-muted-foreground">
                                    {{ student.school_class.name }}
                                </td>
                                <td class="py-3 text-muted-foreground">
                                    {{ student.guardian.name }}
                                </td>
                                <td class="py-3 text-right">
                                    <Badge variant="outline">
                                        {{
                                            student.admission_no ||
                                            student.admission_number
                                        }}
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader
                    class="flex-row items-center justify-between space-y-0"
                >
                    <CardTitle>Recent fee payments</CardTitle>
                    <Button variant="outline" size="sm" as-child>
                        <Link href="#">Open fees</Link>
                    </Button>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        v-for="payment in recentFeePayments"
                        :key="`${payment.student}-${payment.amount}`"
                        class="flex items-center justify-between gap-4 border-b pb-4 last:border-0 last:pb-0"
                    >
                        <div class="min-w-0">
                            <p class="truncate font-medium">
                                {{ payment.student }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ payment.date }}
                            </p>
                        </div>
                        <Badge variant="secondary">
                            {{ formatCurrency(payment.amount) }}
                        </Badge>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="roleDashboard === 'admin'" class="grid gap-6 xl:grid-cols-3">
            <NoticeWidget :notices="latestNotices" />

            <Card>
                <CardHeader>
                    <CardTitle>Quick actions</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <Button
                        v-for="action in quickActions"
                        :key="action.label"
                        variant="outline"
                        as-child
                    >
                        <Link :href="action.href">
                            <component :is="action.icon" class="size-4" />
                            {{ action.label }}
                        </Link>
                    </Button>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Calendar</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-7 gap-2 text-center text-sm">
                        <span
                            v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']"
                            :key="day"
                            class="text-xs text-muted-foreground"
                        >
                            {{ day }}
                        </span>
                        <span
                            v-for="day in 30"
                            :key="day"
                            :class="[
                                'rounded-md py-2',
                                [5, 12, 21].includes(day)
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted',
                            ]"
                        >
                            {{ day }}
                        </span>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <Badge variant="secondary">Exam</Badge>
                        <Badge variant="secondary">Holiday</Badge>
                        <Badge variant="secondary">Event</Badge>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
