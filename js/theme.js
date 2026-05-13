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

        // Atualiza o toggle se existir
        this.updateToggle(theme);
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
        const toggle = document.getElementById('theme-toggle');
        if (toggle) {
            toggle.checked = theme === this.LIGHT_THEME;
            toggle.setAttribute('aria-label', `Ativar tema ${theme === this.DARK_THEME ? 'claro' : 'escuro'}`);
        }
    }
}

// Instancia o gerenciador de tema
const themeManager = new ThemeManager();

// Escuta mudanças no toggle
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('theme-toggle');
    if (toggle) {
        toggle.addEventListener('change', (e) => {
            e.preventDefault();
            const newTheme = themeManager.toggleTheme();
            // Dispara evento customizado para outras partes da aplicação
            document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
        });
    }
});
