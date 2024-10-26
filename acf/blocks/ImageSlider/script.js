import Swiper from 'swiper';
import { Navigation, Pagination, A11y, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/a11y';
import 'swiper/css/autoplay';

export default function(el, refs) {
    const swiper = setupSlider(
        refs,
        JSON.parse(el.dataset.swiperSettings ?? '{}')
    );

    return () => swiper.destroy();
}

const setupSlider = (refs, settings) => {
    return new Swiper(refs.swiper, {
        modules: [Navigation, Pagination, A11y, Autoplay],
        roundLengths: true,
        ...settings
    })
}