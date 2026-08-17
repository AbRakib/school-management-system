<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';
import { ref, watch } from 'vue';

export type NavMainItem = NavItem & {
    items?: NavMainItem[];
    method?: 'get' | 'post';
};

const props = defineProps<{
    items: NavMainItem[];
}>();

const { isCurrentOrParentUrl } = useCurrentUrl();

const isPlaceholder = (href: NavItem['href']) => href === '#';

const isItemActive = (item: NavMainItem): boolean => {
    if (!isPlaceholder(item.href) && isCurrentOrParentUrl(item.href)) {
        return true;
    }

    return item.items?.some(isItemActive) ?? false;
};

const activeGroupTitle = () =>
    props.items.find((item) => item.items?.length && isItemActive(item))?.title ??
    null;

const openGroupTitle = ref<string | null>(activeGroupTitle());

watch(
    () => props.items,
    () => {
        openGroupTitle.value = activeGroupTitle();
    },
    { deep: true },
);

const updateOpenGroup = (title: string, open: boolean) => {
    openGroupTitle.value = open ? title : null;
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Menu</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <Collapsible
                    v-if="item.items?.length"
                    as-child
                    :open="openGroupTitle === item.title"
                    @update:open="updateOpenGroup(item.title, $event)"
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton
                                :is-active="isItemActive(item)"
                                :tooltip="item.title"
                            >
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight
                                    class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem
                                    v-for="child in item.items"
                                    :key="child.title"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="isItemActive(child)"
                                    >
                                        <Link :href="child.href">
                                            <span>{{ child.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="isItemActive(item)"
                        :tooltip="item.title"
                    >
                        <Link
                            :href="item.href"
                            :method="item.method ?? 'get'"
                            :as="item.method === 'post' ? 'button' : undefined"
                        >
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
