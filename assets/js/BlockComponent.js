const blocksWithScript = import.meta.glob('@/acf/blocks/**/script.js');

const loadedComponents = new WeakMap();

window.requestIdleCallback = window.requestIdleCallback || function(handler) {
    let startTime = Date.now();
    return setTimeout(function() {
        handler({
            didTimeout: false,
            timeRemaining: function() {
                return Math.max(0, 50.0 - (Date.now() - startTime));
            }
        });
    }, 1);
}
window.cancelIdleCallback = window.cancelIdleCallback || function(id) {
    clearTimeout(id);
}

const getComponentPath = (node) => {
    const componentName = node.getAttribute('name');
    const componentPath = `/acf/blocks/${componentName}/script.js`;
    return componentPath;
}

const hasScript = (node) => {
    const componentPath = getComponentPath(node);
    return Object.keys(blocksWithScript).includes(componentPath);
}

const getScriptImport = (node) => {
    const componentPath = getComponentPath(node);
    return blocksWithScript[componentPath];
}

const getLoadingStrategy = (node) => {
    const strategies = {
        load: 'load',
        idle: 'idle',
        visible: 'visible',
        prevent: 'prevent'
    };

    return strategies[node.getAttribute('load:on')] ?? strategies.load;
}

const getLoadingFunctionWrapper = (node, strategy) => {
    const loadingFunctions = {
        load: (x) => x(),
        idle: (x) => requestIdleCallback(x, { timeout: 2000 }),
        visible: async (x) => {
            await visible(node);
            x();
        }
    };

    return loadingFunctions[strategy] ?? loadingFunctions.load;
}

const getMediaQuery = (node) => {
    return node.hasAttribute('load:on:media') ?
        node.getAttribute('load:on:media') :
        null;
}

const getRefsProxy = (node) => {
    return new Proxy({}, {
        get (target, prop) {
            if (!target.hasOwnProperty(prop)) {
                const ref = node.querySelectorAll(`[data-ref="${prop}"], #${prop}`);

                if (ref.length === 0) {
                    target[prop] = null;
                }
                else {
                    target[prop] = ref.length === 1 ? ref[0] : ref;
                }
            }

            return target[prop];
        }
    });
}

const getLoadingFunction = (node) => {
    return async () => {
        const componentScriptImport = getScriptImport(node);
        const componentScript = await componentScriptImport();

        if (typeof componentScript.default === 'function' && !loadedComponents.has(node)) {
            const refs = getRefsProxy(node);
            const cleanupFn = componentScript.default(node, refs);
            loadedComponents.set(node, cleanupFn);
        }
    }
}

const cleanupComponent = (node) => {
    if (loadedComponents.has(node)) {
        const cleanupFn = loadedComponents.get(node);

        if (typeof cleanupFn === 'function') {
            cleanupFn();
        }

        loadedComponents.delete(node);
    }
}

const mediaQueryMatches = (node, query) => {
    return new Promise((resolve) => {
        const mediaQuery = window.matchMedia(query);

        if (mediaQuery.matches) {
            resolve(true);
        }
        else {
            mediaQuery.addEventListener(
                'change',
                () => resolve(true),
                { once: true }
            )
        }

        node._mediaQuery = mediaQuery;
    })
}

const visible = (node) => {
    return new Promise((resolve) => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    observer.disconnect();
                    resolve(true);
                }
            })
        })

        observer.observe(node);
        node._observer = observer;
    })
}


export default class BlockComponent extends window.HTMLElement {
    constructor() {
        super();
    }

    async connectedCallback() {
        if (hasScript(this)) {
            const loadingStrategy = getLoadingStrategy(this);

            if (loadingStrategy === 'prevent') {
                return;
            }

            const loadingFunctionWrapper = getLoadingFunctionWrapper(this, loadingStrategy);
            const mediaQuery = getMediaQuery(this);
            const loadingFunction = getLoadingFunction(this);

            if (mediaQuery) {
                await mediaQueryMatches(this, mediaQuery);
            }

            loadingFunctionWrapper(loadingFunction);
        }
    }

    disconnectedCallback() {
        this?._observer?.disconnect();
        this?._mediaQuery?.removeEventListener('change', null);
        cleanupComponent(this);
    }
}