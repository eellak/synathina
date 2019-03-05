const { debounce, includes }  = require('lodash');
const siblings = require('siblings');
const locales = require('./locales');

(function(global) {
    const { document } = global;
    const stateInstance = new SiteState();

    document.addEventListener('DOMContentLoaded', function() {
        IEcheck(this);
        mobileMenu(this);
        animateFeaturedArticles(this, global, stateInstance);
        fixFeaturedArticlesOnRatio(global, stateInstance);
        featuredSlider(global, stateInstance);
        checkIfMobile() && createFooterMenu(global);
    });

    global.addEventListener('resize', debounce(function() {
        featuredSlider(global, stateInstance);
        fixFeaturedArticlesOnRatio(global, stateInstance);
    }, 200));

})(window);

function SiteState () {
    this.state = {
        sliderInitialized: false,
        lastViewportRatio: null,
        isMapOpen: false
    };

    this.setState = function (newState) {
        this.state = Object.assign({}, this.state, newState);

        return this.state;
    };

    this.getState = function () {
        return this.state;
    };
}

function checkIfMobile () {
    const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    if (isMobileDevice) {
        return true;
    }

    return false;
}

function createFooterMenu (window) {
    const { document } = window;
    const container = document.querySelector('[rel=js-create-footer-menu]');

    if (!container ) return false;

    const button = container.querySelector('[rel=js-toggle-footer-drown]');
    const nodes = siblings(container, '[rel=js-footer-menu-item]');
    const dropdown = container.querySelector('.dropdown');
    const menu = dropdown.querySelector('.menu');
    const lang = getLanguage();

    if ( !button || !nodes || !dropdown || !menu ) return false;
    if (!Array.isArray(nodes)) return false;

    button.textContent = locales[`${lang}`].buttonContactText;

    function closeDropDown (evt) {
        const elemNoMatch = evt.target.id != 'footer-dropdown-menu' && ( !evt.target.closest('.l-footer__menus') || evt.target.closest('.l-footer__menus').length == 0);

        if (elemNoMatch) {
            document.body.removeEventListener('click', closeDropDown, false);
            dropdown.classList.remove('dropdown--open');
        }
    }

    nodes.forEach(function(node) {
        node.classList.remove('nav-site-com');
        const li = document.createElement('li');
        li.appendChild(node);
        menu.appendChild(li);
    });

    dropdown.classList.add('dropdown--inverted');
    container.classList.remove('hidden');

    button.addEventListener('click', function () {
        document.addEventListener('click', closeDropDown, false);
        dropdown.classList.toggle('dropdown--open');
    });
}

function IEcheck (context) {
    if (navigator.userAgent.indexOf('MSIE')!==-1 || navigator.appVersion.indexOf('Trident/') > 0) {
        var message = context.querySelector('[rel=js-browser-message]');
        if (!message) return false;
        message.classList.add('browser-message--open');

        return false;
    }
}

function mobileMenu (context) {
    const openButton = context.querySelector('[rel=js-toggle-menu]');
    const closeButton = context.querySelector('[rel=js-mobile-menu-close]');
    const menu = context.querySelector('[rel=js-mobile-menu]');

    if (!openButton || !closeButton || !menu) {
        return false;
    }

    openButton.addEventListener('click', function() {
        menu.classList.add('mobile-menu--active');
    });

    closeButton.addEventListener('click', function() {
        menu.classList.remove('mobile-menu--active');
    });
}

function animateFeaturedArticles (context, window, state) {
    const drawer = context.querySelector('[rel=js-drawer]');
    const toggleButton = context.querySelector('[rel=js-toggle-drawer]');
    const map = context.querySelector('#map');
    const lang = getLanguage();
    const { isMapOpen } = state.getState();

    let currentToggleButtonText = !isMapOpen ? locales[`${lang}`].openMapText :locales[`${lang}`].closeMapText;
    let proccess1, proccess2;

    if (!drawer || !toggleButton) {
        return false;
    }

    toggleButton.textContent = currentToggleButtonText;

    drawer.addEventListener('transitionend', function () {
        const { isMapOpen } = state.getState();
        let currentToggleButtonText = !isMapOpen ? locales[`${getLanguage()}`].openMapText :locales[`${getLanguage()}`].closeMapText;

        function updateLocale () {
            toggleButton.textContent = currentToggleButtonText;
            toggleButton.removeEventListener('transitionend', updateLocale);
        }

        if (isMapOpen) {
            toggleButton.classList.add('feature-toggler-label--hidden');
            toggleButton.addEventListener('transitionend', updateLocale);
            setTimeout(() => {
                toggleButton.classList.add('feature-toggler-label--hidden');
            }, 500);
        }

        if (!isMapOpen) {
            toggleButton.textContent = currentToggleButtonText;
            toggleButton.classList.remove('feature-toggler-label--hidden');
        }

    });

    toggleButton.addEventListener('click', function() {
        const { isMapOpen } = state.getState();
        proccess1 && cancelAnimationFrame(proccess1);
        proccess2 && cancelAnimationFrame(proccess2);

        window.requestAnimationFrame(function () {
            proccess1 = drawer.classList.toggle('l-homepage__featured--up');
            state.setState({isMapOpen: !isMapOpen});
        });
        window.requestAnimationFrame(function () {
            proccess2 = map.classList.toggle('synathina-map--blur');
            EVT.emit('hide-cross');
        });
    });
}


function featuredSlider (window, state) {
    const { document } = window;
    const { sliderInitialized } = state.getState();
    const gallery = document.querySelector('[rel=js-mobile-gallery]');
    const hasMobileViewPort = window.innerWidth <= 768;
    const options = {
        arrows: false,
        dots: true
    };

    $(gallery).on('init', function(event, slick) {
        state.setState({sliderInitialized: true});
    });

    $(gallery).on('destroy', function(event, slick) {
        state.setState({sliderInitialized: false});
    });

    if (!hasMobileViewPort && !sliderInitialized) {
        return false;
    }

    if (!hasMobileViewPort && sliderInitialized) {
        return $(gallery).slick('unslick');
    }

    $(gallery).slick(options);
}

function fixFeaturedArticlesOnRatio (window, state) {
    const { document } = window;
    const { lastViewportRatio } = state.getState();
    const currentViewportRatio = window.innerWidth / window.innerHeight;
    const normalArticles = document.querySelectorAll('.featured-item:not(.c-featured__super)');
    const BREAKPOINT_RATIO = 2;
    const shouldClearMaxWidth = lastViewportRatio >= BREAKPOINT_RATIO;

    if (!normalArticles && !Array.isArray(normalArticles) && normalArticles.length < 1) {
        return false;
    }

    if (currentViewportRatio < BREAKPOINT_RATIO && !shouldClearMaxWidth) {
        return false;
    }

    normalArticles.forEach(function (article) {
        if (shouldClearMaxWidth) {
            article.firstElementChild.removeAttribute('style');
        } else {
            article.firstElementChild.setAttribute('style', 'max-width: 90%');
        }
    });

    state.setState({lastViewportRatio: currentViewportRatio});
}

function getLanguage () {
    const lang = document.querySelector('html').getAttribute('lang');

    if (lang && includes(['en', 'el'], lang)) {
        return lang;
    }

    if (window.location.href.indexOf('/en') !== -1) {
        return 'en';
    }

    if (window.location.href.indexOf('/el') !== -1) {
        return 'el';
    }

    return 'el';
}
