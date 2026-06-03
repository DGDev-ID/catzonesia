import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';

export function updateTheme(_value?: Appearance) {
    // Always force light theme — dark mode is disabled.
    document.documentElement.classList.remove('dark');
}

export function initializeTheme() {
    // Always force light theme regardless of saved preference or system setting.
    localStorage.setItem('appearance', 'light');
    document.documentElement.classList.remove('dark');

    // Re-apply on system theme change to prevent OS dark mode from taking effect.
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        document.documentElement.classList.remove('dark');
    });
}

export function useAppearance() {
    const appearance = ref<Appearance>('light');

    onMounted(() => {
        initializeTheme();
    });

    function updateAppearance(_value: Appearance) {
        // Always keep light theme — ignore any request to switch.
        appearance.value = 'light';
        localStorage.setItem('appearance', 'light');
        document.documentElement.classList.remove('dark');
    }

    return {
        appearance,
        updateAppearance,
    };
}
