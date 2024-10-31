<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-lvh bg-gray-100">
            <nav class="fixed z-50 top-0 left-2 rounded-b-xl shadow border-b border-gray-100 bg-white w-fit">
                <!-- Primary Navigation Menu -->
                <div class="w-fit">
                    <div class="flex h-12 justify-start">

                        <div class="flex items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" :title="$page.props.auth.user.name"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                <span class="truncate w-12">
                                                    <svg class="w-8" viewBox="0 0 48 48" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M23.9999 32C21.9999 32 7.9999 26 5.3999 21C5.9999 19.4 6.9999 18 8.1999 16.6C6.7999 14 5.9999 11.2 5.9999 8C5.9999 6.8 6.7999 6 7.9999 6C12.342 5.95725 16.5459 7.52481 19.7999 10.4C22.586 10.0061 25.4138 10.0061 28.1999 10.4C31.3999 7.6 35.3999 6 39.9999 6C41.1999 6 41.9999 6.8 41.9999 8C41.9999 11.2 41.1999 14 39.7999 16.6C40.9999 18 41.9999 19.4 42.5999 21C39.9999 26 25.9999 32 23.9999 32ZM23.9999 32V26M17.9999 18V19M25.9999 26H21.9999M29.9999 18V19"
                                                            stroke="#757575" stroke-width="4" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M12.5999 41C14.2898 39.7094 15.6571 38.0441 16.5941 36.1353C17.5311 34.2266 18.0124 32.1263 17.9999 30H4.3999C5.9999 38 14.1999 44 23.9999 44C34.9999 44 43.9999 36.4 43.9999 27C43.9999 24.8 43.5999 22.8 42.5999 21"
                                                            stroke="#757575" stroke-width="4" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="flex">
                            <!-- Navigation Links -->
                            <div class="space-x-2 me-4 flex flex-row items-center">
                                <slot name="toolbar" />
                            </div>
                        </div>

                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="no-scrollbar">
                <slot />
            </main>
        </div>
    </div>
</template>
