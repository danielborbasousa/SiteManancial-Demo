// ==========================================
// SISTEMA DE TEMA CLARO/ESCURO
// ==========================================

class ThemeManager {
    constructor() {
        this.THEME_KEY = 'mananciapp-theme';
        this.LIGHT_THEME = 'light';
        this.DARK_THEME = 'dark';
        this.init();
    }

    init() {
        // Carrega o tema salvo ou detecta preferência do sistema
        const savedTheme = localStorage.getItem(this.THEME_KEY);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (prefersDark ? this.DARK_THEME : this.LIGHT_THEME);
        this.setTheme(theme);
    }

    setTheme(theme) {
        // Valida o tema
        if (theme !== this.LIGHT_THEME && theme !== this.DARK_THEME) {
            theme = this.DARK_THEME;
        }

        // Salva no localStorage
        localStorage.setItem(this.THEME_KEY, theme);

        // Aplica ao HTML
        document.documentElement.setAttribute('data-theme', theme);

        // Atualiza os toggles na página
        this.updateToggle(theme);
        // Dispara evento customizado para outras partes da aplicação
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    getCurrentTheme() {
        return localStorage.getItem(this.THEME_KEY) || this.DARK_THEME;
    }

    toggleTheme() {
        const current = this.getCurrentTheme();
        const newTheme = current === this.DARK_THEME ? this.LIGHT_THEME : this.DARK_THEME;
        this.setTheme(newTheme);
        return newTheme;
    }

    updateToggle(theme) {
        const toggles = Array.from(document.querySelectorAll('.theme-toggle, #theme-toggle'));
        toggles.forEach((toggle) => {
            try {
                // Para inputs do tipo checkbox
                if (toggle.type === 'checkbox') {
                    toggle.checked = theme === this.LIGHT_THEME;
                }
                toggle.setAttribute('aria-label', theme === this.DARK_THEME ? 'Ativar tema claro' : 'Ativar tema escuro');
            } catch (e) {
                // ignore
            }
        });
    }
}

// Instancia o gerenciador de tema
const themeManager = new ThemeManager();

// Escuta mudanças no toggle
document.addEventListener('DOMContentLoaded', () => {
    const toggles = Array.from(document.querySelectorAll('.theme-toggle, #theme-toggle'));
    toggles.forEach((toggle) => {
        // normalize event listener for checkboxes
        toggle.addEventListener('change', (e) => {
            e.preventDefault();
            const newTheme = themeManager.toggleTheme();
            // themeManager already dispatches 'themeChanged'
        });
    });
});

// Fullscreen removed — feature deprecated per UX request
