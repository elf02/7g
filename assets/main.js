import 'vite/modulepreload-polyfill';
import BlockComponent from './js/BlockComponent';
import 'lazysizes';

if (import.meta.env.DEV) {
    import('@vite/client')
}

window.customElements.define(
    'block-component',
    BlockComponent
);