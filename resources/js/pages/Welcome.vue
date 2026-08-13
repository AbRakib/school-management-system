<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Award,
    BookOpen,
    BriefcaseBusiness,
    Bus,
    CalendarDays,
    ChevronRight,
    Download,
    FlaskConical,
    GraduationCap,
    Image,
    Library,
    Mail,
    MapPin,
    Megaphone,
    Newspaper,
    Phone,
    PlayCircle,
    Quote,
    School,
    ShieldCheck,
    Sparkles,
    Trophy,
    Users,
} from '@lucide/vue';
import { dashboard, login, register } from '@/routes';

const navigation = [
    { label: 'Home', href: '#home' },
    { label: 'About Us', href: '#about' },
    { label: 'Academics', href: '#academics' },
    { label: 'Admissions', href: '#admissions' },
    { label: 'Faculty', href: '#faculty' },
    { label: 'Student Life', href: '#student-life' },
    { label: 'Events', href: '#events' },
    { label: 'Gallery', href: '#gallery' },
    { label: 'Contact', href: '#contact' },
];

const notices = [
    'Admission form collection starts from 1 August.',
    'Half-yearly exam routine has been published.',
    'Parent teacher meeting will be held this Friday.',
];

const stats = [
    { label: 'Students', value: '1,250+' },
    { label: 'Teachers', value: '75+' },
    { label: 'Graduation Rate', value: '98%' },
    { label: 'Awards', value: '120+' },
];

const programs = [
    {
        title: 'Kindergarten',
        description:
            'Play-based learning, language readiness, numbers, art, music, and social confidence.',
        icon: Sparkles,
    },
    {
        title: 'Primary School',
        description:
            'Strong foundations in literacy, numeracy, science, creativity, and classroom habits.',
        icon: BookOpen,
    },
    {
        title: 'Middle School',
        description:
            'Inquiry-led learning across science, humanities, technology, arts, and life skills.',
        icon: School,
    },
    {
        title: 'High School',
        description:
            'Exam preparation, career guidance, mentoring, leadership, and university readiness.',
        icon: GraduationCap,
    },
];

const academicCards = [
    {
        title: 'Curriculum',
        description:
            'A balanced curriculum with languages, mathematics, science, ICT, humanities, arts, and wellbeing.',
    },
    {
        title: 'Results',
        description:
            'Regular assessments, progress reports, board exam preparation, and result analysis.',
    },
    {
        title: 'Syllabus',
        description:
            'Class-wise syllabus plans, reading lists, assignments, and exam preparation outlines.',
    },
];

const teachers = [
    {
        name: 'Nusrat Jahan',
        role: 'Head of Science',
        image: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=700&q=80',
    },
    {
        name: 'Arif Rahman',
        role: 'Mathematics Lead',
        image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=700&q=80',
    },
    {
        name: 'Sadia Karim',
        role: 'Student Counselor',
        image: 'https://images.unsplash.com/photo-1580894732444-8ecded7900cd?auto=format&fit=crop&w=700&q=80',
    },
];

const events = [
    {
        date: 'Aug 12',
        title: 'Open Day for New Families',
        description:
            'Meet teachers, tour classrooms, and learn about the admissions journey.',
    },
    {
        date: 'Sep 03',
        title: 'Science & Innovation Fair',
        description:
            'Student projects, experiments, robotics demos, and community judging.',
    },
    {
        date: 'Oct 18',
        title: 'Annual Cultural Festival',
        description:
            'Music, debate, theatre, art exhibits, and performances from every house.',
    },
];

const news = [
    {
        title: 'Students win divisional debate championship',
        category: 'Achievement',
        image: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=800&q=80',
    },
    {
        title: 'New digital library corner opens this month',
        category: 'Campus',
        image: 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=800&q=80',
    },
    {
        title: 'Annual sports day registration is now live',
        category: 'Sports',
        image: 'https://images.unsplash.com/photo-1526232373132-0e4ee643d554?auto=format&fit=crop&w=800&q=80',
    },
];

const gallery = [
    'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1544776193-352d25ca82cd?auto=format&fit=crop&w=900&q=80',
];

const testimonials = [
    {
        name: 'Farhana Ahmed',
        role: 'Parent of Class 7 student',
        quote: 'The teachers are attentive, disciplined, and kind. We always know how our child is progressing.',
    },
    {
        name: 'Rafi Hossain',
        role: 'Grade 10 student',
        quote: 'Clubs, labs, and mentoring helped me become more confident in class and outside school.',
    },
];

const facilities = [
    {
        title: 'Library',
        description:
            'Reading zones, reference books, digital resources, and quiet study support.',
        icon: Library,
    },
    {
        title: 'Science Labs',
        description:
            'Practical experiments, safety-first lab routines, and project work.',
        icon: FlaskConical,
    },
    {
        title: 'Sports',
        description:
            'Outdoor fields, indoor games, coaching, and inter-house competitions.',
        icon: Trophy,
    },
    {
        title: 'Transport',
        description:
            'Managed routes, safety checks, attendance tracking, and trained staff.',
        icon: Bus,
    },
];

const achievements = [
    {
        name: 'Ayesha Rahman',
        result: 'GPA 5.00',
        subject: 'Science Group Topper',
    },
    {
        name: 'Mehedi Hasan',
        result: 'National Merit',
        subject: 'Mathematics Olympiad',
    },
    { name: 'Team Polaris', result: 'Champion', subject: 'Robotics Challenge' },
];

const innerPages = [
    {
        title: 'About',
        links: ['History', 'Vision & Mission', 'Management', 'Principal Desk'],
    },
    {
        title: 'Academics',
        links: ['Classes', 'Timetable', 'Exam Schedule', 'Results'],
    },
    {
        title: 'Admission',
        links: ['Procedure', 'Eligibility', 'Form', 'Fee Structure'],
    },
    {
        title: 'Student Corner',
        links: ['Uniform', 'Rules', 'House', 'Clubs', 'Downloads'],
    },
    {
        title: 'Parent Corner',
        links: ['Parent Portal', 'PTM Schedule', 'Guidelines'],
    },
];

const quickLinks = [
    'Admission Form',
    'Academic Calendar',
    'Exam Routine',
    'Results',
    'Downloads',
    'Parent Portal',
];
</script>

<template>
    <Head title="Welcome to Bright Future School">
        <meta
            name="description"
            content="Bright Future School homepage with admissions, academics, faculty, student life, notices, events, gallery, and contact information."
        />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin="anonymous"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="front-page min-h-screen bg-[#07111f] text-[#d8e0ed]">
        <header
            class="sticky top-0 z-40 border-b border-[#dde2d4] bg-[#f7f5ef]/95 backdrop-blur"
        >
            <div class="bg-[#164c45] text-white">
                <div
                    class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-2 text-xs font-medium sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8"
                >
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                        <span class="flex items-center gap-2"
                            ><Phone class="h-4 w-4 text-[#f6c85f]" /> +880 1712
                            345 678</span
                        >
                        <span class="flex items-center gap-2"
                            ><Mail class="h-4 w-4 text-[#f6c85f]" />
                            admissions@brightfuture.edu</span
                        >
                    </div>
                    <div class="flex min-w-0 items-center gap-2">
                        <Megaphone class="h-4 w-4 shrink-0 text-[#f6c85f]" />
                        <p class="truncate">
                            Notice: Admission test schedule and scholarship
                            circular are now available.
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
            >
                <a href="#home" class="flex items-center gap-3">
                    <span
                        class="flex h-11 w-11 items-center justify-center rounded-md bg-[#164c45] text-white"
                    >
                        <School class="h-6 w-6" />
                    </span>
                    <span>
                        <span class="block text-base leading-tight font-bold"
                            >Bright Future</span
                        >
                        <span
                            class="block text-xs font-medium tracking-[0.18em] text-[#65716d] uppercase"
                            >School</span
                        >
                    </span>
                </a>

                <nav
                    class="hidden items-center gap-5 text-sm font-medium text-[#46524f] xl:flex"
                >
                    <a
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="transition hover:text-[#164c45]"
                    >
                        {{ item.label }}
                    </a>
                </nav>

                <div class="hidden items-center gap-3 sm:flex">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-md bg-[#164c45] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0f3934]"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-md px-4 py-2 text-sm font-semibold text-[#164c45] transition hover:bg-white"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-md bg-[#164c45] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0f3934]"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>

            <nav
                class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-4 pb-3 text-sm font-semibold text-[#46524f] sm:px-6 xl:hidden"
            >
                <a
                    v-for="item in navigation"
                    :key="`mobile-${item.href}`"
                    :href="item.href"
                    class="shrink-0 rounded-md bg-white px-3 py-2"
                >
                    {{ item.label }}
                </a>
            </nav>
        </header>

        <main>
            <section id="home" class="relative overflow-hidden">
                <div
                    class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1800&q=85')] bg-cover bg-center"
                />
                <div class="absolute inset-0 bg-[#123f3a]/75" />
                <div
                    class="relative mx-auto grid min-h-[690px] max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8"
                >
                    <div class="max-w-3xl text-white">
                        <div
                            class="mb-5 inline-flex items-center gap-2 rounded-md bg-white/12 px-3 py-2 text-sm font-semibold backdrop-blur"
                        >
                            <Sparkles class="h-4 w-4 text-[#f6c85f]" />
                            Welcome to Bright Future School
                        </div>
                        <h1
                            class="max-w-4xl text-5xl leading-[1.02] font-black tracking-normal sm:text-6xl lg:text-7xl"
                        >
                            Learn with confidence. Lead with character.
                        </h1>
                        <p
                            class="mt-6 max-w-2xl text-lg leading-8 text-[#edf4ef]"
                        >
                            A caring campus where academic excellence,
                            creativity, discipline, and community grow together.
                        </p>
                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="#admissions"
                                class="rounded-md bg-[#f6c85f] px-6 py-3 text-center text-sm font-bold text-[#1d2a28] transition hover:bg-[#ffd977]"
                            >
                                Apply Now
                            </a>
                            <a
                                href="#about"
                                class="rounded-md border border-white/50 px-6 py-3 text-center text-sm font-bold text-white transition hover:bg-white/10"
                            >
                                Explore School
                            </a>
                        </div>
                    </div>

                    <div
                        class="rounded-md border border-white/20 bg-white/12 p-6 text-white backdrop-blur"
                    >
                        <p
                            class="text-sm font-bold tracking-[0.18em] text-[#f6c85f] uppercase"
                        >
                            Latest Announcements
                        </p>
                        <div class="mt-5 space-y-4">
                            <div
                                v-for="notice in notices"
                                :key="notice"
                                class="flex gap-3 rounded-md bg-white/10 p-4"
                            >
                                <Megaphone
                                    class="mt-1 h-5 w-5 shrink-0 text-[#f6c85f]"
                                />
                                <p class="text-sm leading-6 text-[#edf4ef]">
                                    {{ notice }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section
                id="notice"
                class="border-b border-[#dde2d4] bg-white py-8"
            >
                <div
                    class="mx-auto grid max-w-7xl gap-4 px-4 sm:px-6 lg:grid-cols-[220px_1fr] lg:px-8"
                >
                    <div class="flex items-center gap-3 text-[#164c45]">
                        <Megaphone class="h-6 w-6" />
                        <h2 class="text-xl font-black">Notice Board</h2>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3">
                        <a
                            v-for="notice in notices"
                            :key="`board-${notice}`"
                            href="#"
                            class="rounded-md bg-[#f1f6f2] p-4 text-sm leading-6 font-semibold transition hover:bg-[#e6efe8]"
                        >
                            {{ notice }}
                        </a>
                    </div>
                </div>
            </section>

            <section id="about" class="bg-white py-20">
                <div
                    class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8"
                >
                    <div>
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                        >
                            About School
                        </p>
                        <h2
                            class="mt-3 text-4xl font-black tracking-normal text-[#18211f]"
                        >
                            Learning with purpose, kindness, and high
                            expectations.
                        </h2>
                        <p class="mt-5 leading-8 text-[#53605c]">
                            Bright Future School provides a balanced education
                            from kindergarten to high school. Our teachers
                            combine strong classroom instruction with projects,
                            sports, arts, digital skills, and personal
                            mentoring.
                        </p>
                    </div>

                    <div class="grid gap-5">
                        <div class="rounded-md bg-[#f1f6f2] p-6">
                            <Quote class="h-8 w-8 text-[#164c45]" />
                            <p class="mt-4 text-lg leading-8 text-[#33413d]">
                                "Our mission is to help every learner become
                                capable, curious, respectful, and ready for a
                                changing world."
                            </p>
                            <p class="mt-4 font-black">Principal's Message</p>
                            <p class="text-sm font-semibold text-[#65716d]">
                                Dr. Mahmudul Karim, Principal
                            </p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-md border border-[#d9dfd5] p-5">
                                <Award class="mb-4 h-7 w-7 text-[#164c45]" />
                                <h3 class="font-bold text-[#18211f]">
                                    Excellence
                                </h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#53605c]"
                                >
                                    Consistent results and careful academic
                                    support.
                                </p>
                            </div>
                            <div class="rounded-md border border-[#d9dfd5] p-5">
                                <Users class="mb-4 h-7 w-7 text-[#164c45]" />
                                <h3 class="font-bold text-[#18211f]">
                                    Community
                                </h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#53605c]"
                                >
                                    Strong partnerships with students and
                                    families.
                                </p>
                            </div>
                            <div class="rounded-md border border-[#d9dfd5] p-5">
                                <ShieldCheck
                                    class="mb-4 h-7 w-7 text-[#164c45]"
                                />
                                <h3 class="font-bold text-[#18211f]">Care</h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#53605c]"
                                >
                                    A safe, respectful, and supportive school
                                    culture.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="programs" class="py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                        >
                            Our Programs
                        </p>
                        <h2 class="mt-3 text-4xl font-black tracking-normal">
                            Programs for every stage of growth.
                        </h2>
                    </div>
                    <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                        <article
                            v-for="program in programs"
                            :key="program.title"
                            class="rounded-md border border-[#d9dfd5] bg-white p-7 shadow-sm"
                        >
                            <component
                                :is="program.icon"
                                class="h-9 w-9 text-[#164c45]"
                            />
                            <h3 class="mt-6 text-xl font-black">
                                {{ program.title }}
                            </h3>
                            <p class="mt-3 text-sm leading-7 text-[#53605c]">
                                {{ program.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="academics" class="bg-white py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end"
                    >
                        <div class="max-w-2xl">
                            <p
                                class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                            >
                                Academics
                            </p>
                            <h2
                                class="mt-3 text-4xl font-black tracking-normal"
                            >
                                Curriculum, results, syllabus, and academic
                                planning.
                            </h2>
                        </div>
                        <a
                            href="#inner-pages"
                            class="inline-flex items-center gap-2 rounded-md bg-[#164c45] px-5 py-3 text-sm font-bold text-white"
                        >
                            View Academic Pages <ChevronRight class="h-4 w-4" />
                        </a>
                    </div>
                    <div class="mt-10 grid gap-5 md:grid-cols-3">
                        <article
                            v-for="card in academicCards"
                            :key="card.title"
                            class="rounded-md bg-[#f1f6f2] p-6"
                        >
                            <h3 class="text-xl font-black">{{ card.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-[#53605c]">
                                {{ card.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="bg-[#164c45] py-14 text-white">
                <div
                    class="mx-auto grid max-w-7xl gap-4 px-4 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8"
                >
                    <div
                        v-for="stat in stats"
                        :key="stat.label"
                        class="rounded-md border border-white/15 bg-white/10 p-6"
                    >
                        <p class="text-4xl font-black">{{ stat.value }}</p>
                        <p class="mt-2 text-sm font-semibold text-[#dce8e2]">
                            {{ stat.label }}
                        </p>
                    </div>
                </div>
            </section>

            <section id="faculty" class="bg-white py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p
                        class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                    >
                        Featured Teachers / Faculty
                    </p>
                    <h2
                        class="mt-3 max-w-3xl text-4xl font-black tracking-normal"
                    >
                        Experienced teachers who know every learner matters.
                    </h2>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        <article
                            v-for="teacher in teachers"
                            :key="teacher.name"
                            class="overflow-hidden rounded-md border border-[#d9dfd5] bg-[#f8faf6]"
                        >
                            <img
                                :src="teacher.image"
                                :alt="teacher.name"
                                class="h-72 w-full object-cover"
                            />
                            <div class="p-5">
                                <h3 class="text-lg font-black">
                                    {{ teacher.name }}
                                </h3>
                                <p
                                    class="mt-1 text-sm font-semibold text-[#65716d]"
                                >
                                    {{ teacher.role }}
                                </p>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="admissions" class="bg-[#f7f5ef] py-20">
                <div
                    class="mx-auto grid max-w-7xl items-start gap-10 px-4 sm:px-6 lg:grid-cols-[1fr_0.8fr] lg:px-8"
                >
                    <div>
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                        >
                            Admissions Info
                        </p>
                        <h2 class="mt-3 text-4xl font-black tracking-normal">
                            How to apply, fees, and prospectus download.
                        </h2>
                        <p class="mt-5 leading-8 text-[#53605c]">
                            Families can visit the campus, speak with our
                            admissions team, review class placement, and
                            complete enrollment with clear guidance at each
                            step.
                        </p>
                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            <a
                                href="#"
                                class="inline-flex items-center justify-center gap-2 rounded-md bg-[#164c45] px-5 py-3 text-sm font-bold text-white"
                            >
                                <Download class="h-4 w-4" /> Download Prospectus
                            </a>
                            <a
                                href="#contact"
                                class="inline-flex items-center justify-center gap-2 rounded-md border border-[#164c45] px-5 py-3 text-sm font-bold text-[#164c45]"
                            >
                                Ask About Fees
                            </a>
                        </div>
                    </div>
                    <div
                        class="rounded-md bg-white p-6 text-[#18211f] shadow-sm"
                    >
                        <ol class="space-y-5">
                            <li class="flex gap-4">
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-[#f6c85f] font-black"
                                    >1</span
                                >
                                <span
                                    ><strong>Submit inquiry</strong><br /><span
                                        class="text-sm text-[#5e6b67]"
                                        >Share student details and preferred
                                        class.</span
                                    ></span
                                >
                            </li>
                            <li class="flex gap-4">
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-[#f6c85f] font-black"
                                    >2</span
                                >
                                <span
                                    ><strong>Campus visit</strong><br /><span
                                        class="text-sm text-[#5e6b67]"
                                        >Tour classrooms and meet the academic
                                        team.</span
                                    ></span
                                >
                            </li>
                            <li class="flex gap-4">
                                <span
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-[#f6c85f] font-black"
                                    >3</span
                                >
                                <span
                                    ><strong>Confirm admission</strong
                                    ><br /><span class="text-sm text-[#5e6b67]"
                                        >Complete placement, documents, fees,
                                        and orientation.</span
                                    ></span
                                >
                            </li>
                        </ol>
                    </div>
                </div>
            </section>

            <section id="student-life" class="bg-white py-20">
                <div
                    class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8"
                >
                    <img
                        src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?auto=format&fit=crop&w=1200&q=85"
                        alt="Students collaborating in class"
                        class="h-full min-h-[420px] w-full rounded-md object-cover"
                    />
                    <div class="flex flex-col justify-center">
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                        >
                            Students / Student Life
                        </p>
                        <h2 class="mt-3 text-4xl font-black tracking-normal">
                            A campus alive with clubs, sports, service, and
                            creativity.
                        </h2>
                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-md bg-[#f1f6f2] p-5">
                                <h3 class="font-black">Leadership & Service</h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#53605c]"
                                >
                                    Student council, community work, mentoring,
                                    and house activities.
                                </p>
                            </div>
                            <div class="rounded-md bg-[#f1f6f2] p-5">
                                <h3 class="font-black">Sports & Wellness</h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#53605c]"
                                >
                                    Football, cricket, athletics, fitness, and
                                    healthy routines.
                                </p>
                            </div>
                            <div class="rounded-md bg-[#f1f6f2] p-5">
                                <h3 class="font-black">Arts & Culture</h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#53605c]"
                                >
                                    Music, theatre, debate, painting, and
                                    cultural programs.
                                </p>
                            </div>
                            <div class="rounded-md bg-[#f1f6f2] p-5">
                                <h3 class="font-black">Technology</h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-[#53605c]"
                                >
                                    Coding, robotics, digital literacy, and
                                    media projects.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="events" class="py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p
                        class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                    >
                        Events / Upcoming Events Calendar
                    </p>
                    <h2 class="mt-3 text-4xl font-black tracking-normal">
                        Upcoming school moments.
                    </h2>
                    <div class="mt-10 grid gap-5 lg:grid-cols-3">
                        <article
                            v-for="event in events"
                            :key="event.title"
                            class="rounded-md border border-[#d9dfd5] bg-white p-6"
                        >
                            <div class="flex items-center gap-3 text-[#164c45]">
                                <CalendarDays class="h-5 w-5" />
                                <span class="font-black">{{ event.date }}</span>
                            </div>
                            <h3 class="mt-5 text-xl font-black">
                                {{ event.title }}
                            </h3>
                            <p class="mt-3 text-sm leading-7 text-[#53605c]">
                                {{ event.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="bg-white py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <Newspaper class="h-6 w-6 text-[#164c45]" />
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                        >
                            News & Blog
                        </p>
                    </div>
                    <h2 class="mt-3 text-4xl font-black tracking-normal">
                        Stories from our school community.
                    </h2>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        <article
                            v-for="item in news"
                            :key="item.title"
                            class="overflow-hidden rounded-md border border-[#d9dfd5] bg-[#f8faf6]"
                        >
                            <img
                                :src="item.image"
                                :alt="item.title"
                                class="h-52 w-full object-cover"
                            />
                            <div class="p-5">
                                <p
                                    class="text-xs font-bold tracking-[0.16em] text-[#d48425] uppercase"
                                >
                                    {{ item.category }}
                                </p>
                                <h3 class="mt-3 text-lg leading-7 font-black">
                                    {{ item.title }}
                                </h3>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="gallery" class="py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <Image class="h-6 w-6 text-[#164c45]" />
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                        >
                            Campus Gallery / Virtual Tour
                        </p>
                    </div>
                    <h2 class="mt-3 text-4xl font-black tracking-normal">
                        Snapshots from classrooms, campus, and celebrations.
                    </h2>
                    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <img
                            v-for="photo in gallery"
                            :key="photo"
                            :src="photo"
                            alt="School gallery photo"
                            class="h-72 w-full rounded-md object-cover shadow-sm"
                        />
                    </div>
                    <a
                        href="#"
                        class="mt-6 inline-flex items-center gap-2 rounded-md bg-[#164c45] px-5 py-3 text-sm font-bold text-white"
                    >
                        <PlayCircle class="h-4 w-4" /> Start Virtual Tour
                    </a>
                </div>
            </section>

            <section class="bg-white py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p
                        class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                    >
                        Testimonials
                    </p>
                    <h2 class="mt-3 text-4xl font-black tracking-normal">
                        Parents and students share their experience.
                    </h2>
                    <div class="mt-10 grid gap-6 md:grid-cols-2">
                        <article
                            v-for="testimonial in testimonials"
                            :key="testimonial.name"
                            class="rounded-md bg-[#f1f6f2] p-7"
                        >
                            <Quote class="h-7 w-7 text-[#164c45]" />
                            <p class="mt-5 text-lg leading-8 text-[#33413d]">
                                "{{ testimonial.quote }}"
                            </p>
                            <p class="mt-5 font-black">
                                {{ testimonial.name }}
                            </p>
                            <p class="text-sm font-semibold text-[#65716d]">
                                {{ testimonial.role }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p
                        class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                    >
                        Facilities
                    </p>
                    <h2 class="mt-3 text-4xl font-black tracking-normal">
                        Spaces and services that support everyday learning.
                    </h2>
                    <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                        <article
                            v-for="facility in facilities"
                            :key="facility.title"
                            class="rounded-md bg-white p-6 shadow-sm"
                        >
                            <component
                                :is="facility.icon"
                                class="h-8 w-8 text-[#164c45]"
                            />
                            <h3 class="mt-5 text-lg font-black">
                                {{ facility.title }}
                            </h3>
                            <p class="mt-3 text-sm leading-7 text-[#53605c]">
                                {{ facility.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="bg-[#164c45] py-20 text-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p
                        class="text-sm font-bold tracking-[0.2em] text-[#f6c85f] uppercase"
                    >
                        Achievements / Toppers
                    </p>
                    <h2 class="mt-3 text-4xl font-black tracking-normal">
                        Celebrating excellence across academics and activities.
                    </h2>
                    <div class="mt-10 grid gap-5 md:grid-cols-3">
                        <article
                            v-for="achievement in achievements"
                            :key="achievement.name"
                            class="rounded-md border border-white/15 bg-white/10 p-6"
                        >
                            <Trophy class="h-8 w-8 text-[#f6c85f]" />
                            <h3 class="mt-5 text-xl font-black">
                                {{ achievement.name }}
                            </h3>
                            <p class="mt-2 text-lg font-bold text-[#f6c85f]">
                                {{ achievement.result }}
                            </p>
                            <p class="mt-2 text-sm text-[#d9e8e2]">
                                {{ achievement.subject }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="inner-pages" class="bg-white py-20">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <BriefcaseBusiness class="h-6 w-6 text-[#164c45]" />
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#d48425] uppercase"
                        >
                            Inner Pages
                        </p>
                    </div>
                    <h2 class="mt-3 text-4xl font-black tracking-normal">
                        Common pages for students, parents, and school visitors.
                    </h2>
                    <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-5">
                        <div
                            v-for="group in innerPages"
                            :key="group.title"
                            class="rounded-md border border-[#d9dfd5] p-5"
                        >
                            <h3 class="font-black">{{ group.title }}</h3>
                            <div class="mt-4 space-y-2">
                                <a
                                    v-for="link in group.links"
                                    :key="`${group.title}-${link}`"
                                    href="#"
                                    class="flex items-center justify-between rounded-md bg-[#f1f6f2] px-3 py-2 text-sm font-semibold text-[#46524f]"
                                >
                                    {{ link }}
                                    <ChevronRight class="h-4 w-4" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact" class="bg-[#18211f] py-20 text-white">
                <div
                    class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8"
                >
                    <div>
                        <p
                            class="text-sm font-bold tracking-[0.2em] text-[#f6c85f] uppercase"
                        >
                            Contact
                        </p>
                        <h2 class="mt-3 text-4xl font-black tracking-normal">
                            Visit, call, or write to the school office.
                        </h2>
                        <div class="mt-8 space-y-4 text-[#d9e8e2]">
                            <p class="flex items-center gap-3">
                                <MapPin class="h-5 w-5 text-[#f6c85f]" /> 24
                                Learning Avenue, Dhaka
                            </p>
                            <p class="flex items-center gap-3">
                                <Phone class="h-5 w-5 text-[#f6c85f]" /> +880
                                1712 345 678
                            </p>
                            <p class="flex items-center gap-3">
                                <Mail class="h-5 w-5 text-[#f6c85f]" />
                                admissions@brightfuture.edu
                            </p>
                        </div>
                        <div
                            class="mt-8 h-64 rounded-md bg-[#273431] p-5 text-[#d9e8e2]"
                        >
                            <MapPin class="h-8 w-8 text-[#f6c85f]" />
                            <p class="mt-4 font-bold">Map Placeholder</p>
                            <p class="mt-2 text-sm leading-6">
                                Embed Google Map here when the live campus
                                location is confirmed.
                            </p>
                        </div>
                    </div>
                    <form
                        class="rounded-md bg-white p-6 text-[#18211f] shadow-xl"
                    >
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block text-sm font-bold">
                                Parent name
                                <input
                                    class="mt-2 w-full rounded-md border border-[#ccd5ca] px-4 py-3 font-normal outline-none focus:border-[#164c45]"
                                    type="text"
                                />
                            </label>
                            <label class="block text-sm font-bold">
                                Phone number
                                <input
                                    class="mt-2 w-full rounded-md border border-[#ccd5ca] px-4 py-3 font-normal outline-none focus:border-[#164c45]"
                                    type="tel"
                                />
                            </label>
                        </div>
                        <label class="mt-4 block text-sm font-bold">
                            Email
                            <input
                                class="mt-2 w-full rounded-md border border-[#ccd5ca] px-4 py-3 font-normal outline-none focus:border-[#164c45]"
                                type="email"
                            />
                        </label>
                        <label class="mt-4 block text-sm font-bold">
                            Message
                            <textarea
                                class="mt-2 min-h-32 w-full rounded-md border border-[#ccd5ca] px-4 py-3 font-normal outline-none focus:border-[#164c45]"
                            />
                        </label>
                        <button
                            class="mt-5 w-full rounded-md bg-[#164c45] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#0f3934]"
                            type="button"
                        >
                            Send Message
                        </button>
                    </form>
                </div>
            </section>
        </main>

        <footer class="bg-[#101816] text-white">
            <div
                class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8"
            >
                <div>
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-md bg-[#f6c85f] text-[#18211f]"
                        >
                            <School class="h-6 w-6" />
                        </span>
                        <div>
                            <p class="font-black">Bright Future School</p>
                            <p
                                class="text-xs font-semibold tracking-[0.18em] text-[#9fb2ad] uppercase"
                            >
                                Learn. Lead. Serve.
                            </p>
                        </div>
                    </div>
                    <p class="mt-5 text-sm leading-7 text-[#c6d4d0]">
                        A complete school website layout for notices, academics,
                        admissions, students, parents, events, and contact.
                    </p>
                </div>

                <div>
                    <h3 class="font-black">Quick Links</h3>
                    <div class="mt-4 grid gap-2">
                        <a
                            v-for="link in quickLinks"
                            :key="link"
                            href="#"
                            class="text-sm text-[#c6d4d0] transition hover:text-white"
                            >{{ link }}</a
                        >
                    </div>
                </div>

                <div>
                    <h3 class="font-black">Newsletter</h3>
                    <p class="mt-4 text-sm leading-7 text-[#c6d4d0]">
                        Subscribe for notices, events, exam updates, and
                        admission news.
                    </p>
                    <div class="mt-4 flex overflow-hidden rounded-md bg-white">
                        <input
                            class="min-w-0 flex-1 px-4 py-3 text-sm text-[#18211f] outline-none"
                            type="email"
                            placeholder="Email address"
                        />
                        <button
                            class="bg-[#f6c85f] px-4 text-sm font-black text-[#18211f]"
                            type="button"
                        >
                            Join
                        </button>
                    </div>
                </div>

                <div>
                    <h3 class="font-black">Contact Us</h3>
                    <div class="mt-4 space-y-3 text-sm text-[#c6d4d0]">
                        <p>24 Learning Avenue, Dhaka</p>
                        <p>+880 1712 345 678</p>
                        <p>admissions@brightfuture.edu</p>
                    </div>
                    <div class="mt-5 flex gap-3">
                        <a
                            href="#"
                            aria-label="Facebook"
                            class="rounded-md bg-white/10 p-2 text-[#f6c85f]"
                            >Fb</a
                        >
                        <a
                            href="#"
                            aria-label="Instagram"
                            class="rounded-md bg-white/10 p-2 text-[#f6c85f]"
                            >Ig</a
                        >
                        <a
                            href="#"
                            aria-label="Youtube"
                            class="rounded-md bg-white/10 p-2 text-[#f6c85f]"
                            >Yt</a
                        >
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10">
                <div
                    class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-sm text-[#9fb2ad] sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8"
                >
                    <p>
                        Copyright 2026 Bright Future School. All rights
                        reserved.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-white">Privacy Policy</a>
                        <a href="#" class="hover:text-white">Terms</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.front-page {
    --school-bg: #07111f;
    --school-bg-deep: #040a14;
    --school-panel: #0d1728;
    --school-panel-soft: #111d31;
    --school-border: rgba(218, 166, 49, 0.18);
    --school-gold: #daa631;
    --school-gold-soft: #f0c765;
    --school-text: #d8e0ed;
    --school-muted: #8f9db2;
    --school-white: #f5ead8;
    background:
        radial-gradient(
            circle at 18% 8%,
            rgba(218, 166, 49, 0.08),
            transparent 26rem
        ),
        radial-gradient(
            circle at 82% 18%,
            rgba(50, 92, 140, 0.16),
            transparent 28rem
        ),
        var(--school-bg);
    color: var(--school-text);
    font-family: Inter, 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

.front-page :where(h1, h2, h3),
.front-page :where(.font-black) {
    color: var(--school-white);
    font-family: 'Cormorant Garamond', Georgia, serif;
    letter-spacing: 0;
}

.front-page :where(header, footer),
.front-page :where(section) {
    background: var(--school-bg) !important;
}

.front-page :where(section:nth-of-type(even)) {
    background: #0a1424 !important;
}

.front-page :where(header) {
    border-color: var(--school-border) !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.24);
}

.front-page :where(header > div:first-child) {
    background: var(--school-bg-deep) !important;
    border-bottom: 1px solid var(--school-border);
}

.front-page :where(nav a, p, span, li, label) {
    color: inherit;
}

.front-page
    :where(
        [class*='text-[#53605c]'],
        [class*='text-[#5e6b67]'],
        [class*='text-[#65716d]'],
        [class*='text-[#46524f]'],
        [class*='text-[#9fb2ad]'],
        [class*='text-[#c6d4d0]'],
        [class*='text-[#d9e8e2]'],
        [class*='text-muted']
    ) {
    color: var(--school-muted) !important;
}

.front-page
    :where(
        [class*='text-[#d48425]'],
        [class*='text-[#f6c85f]'],
        [class*='text-[#164c45]']
    ) {
    color: var(--school-gold) !important;
}

.front-page
    :where(
        [class*='bg-[#164c45]'],
        [class*='bg-[#18211f]'],
        [class*='bg-[#101816]']
    ) {
    background: var(--school-bg-deep) !important;
}

.front-page
    :where(
        [class*='bg-white'],
        [class*='bg-[#f1f6f2]'],
        [class*='bg-[#f8faf6]'],
        article,
        form
    ) {
    background: linear-gradient(
        180deg,
        var(--school-panel-soft),
        var(--school-panel)
    ) !important;
    border-color: var(--school-border) !important;
    color: var(--school-text) !important;
}

.front-page
    :where(
        [class*='border-[#d9dfd5]'],
        [class*='border-[#dde2d4]'],
        [class*='border-white'],
        [class*='border-[#ccd5ca]']
    ) {
    border-color: var(--school-border) !important;
}

.front-page :where([class*='bg-[#f6c85f]']) {
    background: linear-gradient(
        135deg,
        var(--school-gold-soft),
        var(--school-gold)
    ) !important;
    color: #08111f !important;
}

.front-page :where(a[class*='bg-[#164c45]'], button[class*='bg-[#164c45]']) {
    background: linear-gradient(
        135deg,
        var(--school-gold-soft),
        var(--school-gold)
    ) !important;
    color: #08111f !important;
}

.front-page :where(a[class*='border-[#164c45]']) {
    border-color: var(--school-gold) !important;
    color: var(--school-gold-soft) !important;
}

.front-page :where(input, textarea) {
    background: #08111f !important;
    border-color: var(--school-border) !important;
    color: var(--school-text) !important;
}

.front-page :where(input::placeholder, textarea::placeholder) {
    color: var(--school-muted);
}

.front-page :where(#home > div:first-child) {
    filter: saturate(0.72) brightness(0.54);
}

.front-page :where(#home > div:nth-child(2)) {
    background:
        linear-gradient(90deg, rgba(4, 10, 20, 0.96), rgba(7, 17, 31, 0.86)),
        radial-gradient(
            circle at 70% 25%,
            rgba(218, 166, 49, 0.16),
            transparent 22rem
        ) !important;
}

.front-page :where(#home [class*='bg-white/12'], #home [class*='bg-white/10']) {
    background: rgba(13, 23, 40, 0.78) !important;
    border: 1px solid var(--school-border);
}

.front-page :where(img) {
    border-color: var(--school-border);
}

.front-page :where(article img, #student-life img, #gallery img) {
    filter: saturate(0.78) contrast(1.04) brightness(0.82);
}

.front-page :where(.shadow-sm, .shadow-xl, .shadow-2xl) {
    box-shadow: 0 22px 70px rgba(0, 0, 0, 0.24) !important;
}

.front-page :where(a:hover) {
    color: var(--school-gold-soft) !important;
}
</style>
