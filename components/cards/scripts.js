import Swiper from 'swiper';
import { Navigation, Keyboard, Mousewheel, FreeMode } from 'swiper/modules';

function readColumns(el) {
	const match = el.className.match(/cards--columns-(\d+)/);
	const cols = match ? Number.parseInt(match[1], 10) : 3;
	return Number.isFinite(cols) && cols > 0 ? cols : 3;
}

class CardsCarousel {
	constructor(element) {
		this.element = element;
		if (this.element.dataset.cardsInitialized === 'true') return;

		const swiperEl = this.element.querySelector('.cards__swiper');
		if (!swiperEl) return;

		const cols = readColumns(this.element);

		const rootFontSize = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
		const cssPxValue = (val) => {
			val = val.trim();
			if (val.endsWith('rem')) return parseFloat(val) * rootFontSize;
			return parseFloat(val);
		};

		const readSlides = () =>
			parseFloat(getComputedStyle(this.element).getPropertyValue('--cards--carousel--slides')) || 1.2;

		const readGap = () =>
			cssPxValue(getComputedStyle(this.element).getPropertyValue('--cards--carousel--gap')) || 24;

		this.swiper = new Swiper(swiperEl, {
			modules: [Navigation, Keyboard, Mousewheel, FreeMode],
			slidesPerView: readSlides(),
			spaceBetween: readGap(),
			speed: 500,
			roundLengths: true,
			grabCursor: true,
			freeMode: {
				enabled: true,
				momentum: true,
			},
			keyboard: {
				enabled: true,
				onlyInViewport: true,
			},
			mousewheel: {
				enabled: true,
				forceToAxis: true,
			},
			navigation: {
				nextEl: this.element.querySelector('.cards__next'),
				prevEl: this.element.querySelector('.cards__prev'),
			},
			breakpoints: {
				768: {
					slidesPerView: Math.min(2, cols),
				},
				1024: {
					slidesPerView: cols,
				},
			},
			on: {
				resize: (swiper) => {
					swiper.params.slidesPerView = readSlides();
					swiper.params.spaceBetween = readGap();
					swiper.update();
				},
			},
		});

		this.element.dataset.cardsInitialized = 'true';
	}
}

function initCarousels(root = document) {
	root.querySelectorAll('sq-cards').forEach((element) => {
		new CardsCarousel(element);
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => initCarousels(), { once: true });
} else {
	initCarousels();
}
