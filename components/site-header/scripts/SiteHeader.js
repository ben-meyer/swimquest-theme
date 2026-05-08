import debounce from 'lodash.debounce';
import isElementVisible from '../../../assets/scripts/helpers/isElementVisible.js';

const OFFSET_SELECTOR = '[data-header-offset]';
const ADMIN_BAR_SELECTOR = '#wpadminbar';
const FOCUSABLE_SELECTOR = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

export default class SiteHeader {
    constructor(element) {
        this.el = element;
        this.barEl = this.el.querySelector('.site-header__bar');
        this.burgerEl = this.el.querySelector('.site-header__burger');
        this.menuPanelEl = this.el.querySelector('.site-header__menu-panel');

        this.buttonsEl = this.el.querySelector('.site-header__buttons');
        this.menuTogglerEls = this.el.querySelectorAll('.js-site-header-toggle');
        this.toggleLabelEl = this.el.querySelector('.js-site-header-toggle-label');

        this.handleTrapKeydown = this.handleTrapKeydown.bind(this);
        this.previousFocusEl = null;

        this.init();
    }

    init() {
        this.calculateOffset();
        this.initHeroMode();
        this.initStickyScroll();

        const debouncedResize = debounce(() => {
            this.calculateOffset();
            this.updateHeroScroll();
            if (!this.isMobileMode()) {
                this.closeMenu();
            }
        }, 50);

        window.addEventListener('resize', debouncedResize);

        // Watch only the admin bar and any [data-header-offset] elements for size changes
        // rather than mutations across the whole document.
        const offsetTargets = [
            document.querySelector(ADMIN_BAR_SELECTOR),
            ...document.querySelectorAll(OFFSET_SELECTOR),
        ].filter(Boolean);

        if (offsetTargets.length && typeof ResizeObserver !== 'undefined') {
            const resizeObserver = new ResizeObserver(debounce(() => this.calculateOffset(), 50));
            offsetTargets.forEach((target) => resizeObserver.observe(target));
        }

        this.menuTogglerEls?.forEach((toggle) => {
            toggle.addEventListener('click', () => this.toggleMenu());
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.menuPanelEl && !this.menuPanelEl.inert) {
                this.closeMenu();
                this.burgerEl?.focus();
            }
        });
    }

    initHeroMode() {
        const mainEl = document.querySelector('.site-main');
        if (!mainEl) return;

        const containerEl = mainEl.querySelector('.site-main__content') || mainEl.querySelector('.site-main__inner') || mainEl;
        const heroEl = containerEl.querySelector(
            '.page-header.has-hero-image, .homepage-hero-header.has-background-image, .trip-page-header'
        );

        if (!heroEl || heroEl !== containerEl.firstElementChild) return;

        this.heroEl = heroEl;
        this.el.classList.add('site-header--hero');

        this.updateHeroScroll();
        window.addEventListener('scroll', () => this.updateHeroScroll(), { passive: true });
    }

    initStickyScroll() {
        this.lastScrollY = window.scrollY;

        window.addEventListener('scroll', () => {
            if (this.isMobileMode()) return;

            const currentScrollY = window.scrollY;
            const headerHeight = this.barEl.offsetHeight;

            if (currentScrollY > headerHeight) {
                if (currentScrollY > this.lastScrollY) {
                    this.el.classList.add('site-header--hidden');
                } else {
                    this.el.classList.remove('site-header--hidden');
                }
            } else {
                this.el.classList.remove('site-header--hidden');
            }

            this.lastScrollY = currentScrollY;
        }, { passive: true });
    }

    updateHeroScroll() {
        if (!this.heroEl) return;

        const heroBottom = this.heroEl.offsetTop + this.heroEl.offsetHeight;
        const headerHeight = this.barEl.offsetHeight;

        if (window.scrollY >= heroBottom - headerHeight) {
            this.el.classList.add('site-header--scrolled');
        } else {
            this.el.classList.remove('site-header--scrolled');
        }
    }

    calculateOffset() {
        let offset = 0;

        const adminBar = document.querySelector(ADMIN_BAR_SELECTOR);
        if (adminBar) {
            offset += adminBar.offsetHeight;
        }

        document.querySelectorAll(OFFSET_SELECTOR)?.forEach((el) => {
            offset += el.offsetHeight;
        });

        document.documentElement.style.setProperty('--site-header--top', `${offset}px`);
        this.el.classList.add('site-header--positioned');
    }

    toggleMenu() {
        if (this.menuPanelEl?.inert === false) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }

    openMenu() {
        if (!this.menuPanelEl) return;

        this.previousFocusEl = document.activeElement;
        this.menuPanelEl.inert = false;

        this.menuTogglerEls?.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'true');
        });

        if (this.toggleLabelEl) {
            this.toggleLabelEl.textContent = 'Close main menu';
        }

        document.documentElement.classList.add('no-scroll');
        this.setInertOnSiblings(true);

        // Move focus into the panel and trap it there
        const focusables = this.getFocusableEls();
        if (focusables.length) {
            focusables[0].focus();
        }

        document.addEventListener('keydown', this.handleTrapKeydown);
    }

    closeMenu() {
        if (this.menuPanelEl) {
            this.menuPanelEl.inert = true;
        }

        this.menuTogglerEls?.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
        });

        if (this.toggleLabelEl) {
            this.toggleLabelEl.textContent = 'Open main menu';
        }

        document.documentElement.classList.remove('no-scroll');
        this.setInertOnSiblings(false);

        document.removeEventListener('keydown', this.handleTrapKeydown);

        // Restore focus to whatever was focused before the menu opened (usually the burger).
        if (this.previousFocusEl && typeof this.previousFocusEl.focus === 'function') {
            this.previousFocusEl.focus();
        }
        this.previousFocusEl = null;
    }

    getFocusableEls() {
        if (!this.menuPanelEl) return [];
        return Array.from(this.menuPanelEl.querySelectorAll(FOCUSABLE_SELECTOR))
            .filter((el) => !el.hasAttribute('disabled') && el.offsetParent !== null);
    }

    handleTrapKeydown(e) {
        if (e.key !== 'Tab') return;

        const focusables = this.getFocusableEls();
        if (!focusables.length) return;

        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        const active = document.activeElement;

        if (e.shiftKey && active === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && active === last) {
            e.preventDefault();
            first.focus();
        }
    }

    setInertOnSiblings(inert) {
        const parent = this.el.parentElement;
        if (!parent) return;

        Array.from(parent.children)?.forEach((sibling) => {
            if (sibling !== this.el && sibling.nodeType === Node.ELEMENT_NODE) {
                sibling.inert = inert;
            }
        });
    }

    isMobileMode() {
        return isElementVisible(this.buttonsEl);
    }
}
