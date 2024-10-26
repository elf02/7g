import { disableBodyScroll, enableBodyScroll } from 'body-scroll-lock'

export default function(el, refs) {
    let mobile_open = false;

    refs.mobileToggle.addEventListener('click', (ev) => {
        ev.preventDefault();
        mobile_open = !mobile_open;
        refs.mobileToggle.setAttribute('aria-expanded', mobile_open);

        if (mobile_open) {
            disableBodyScroll(refs.NavigationMobile);
        }
        else {
            enableBodyScroll(refs.NavigationMobile);
        }
    });
}
