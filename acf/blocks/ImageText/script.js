import Rellax from 'rellax';

export default function(el, refs) {
    let rellax = null;
    const bp = window.matchMedia('(min-width: 768px)');

    if (bp.matches) {
        rellax = initRellax(refs);
    }

    bp.addEventListener('change', () => {
        if (bp.matches) {
            rellax = initRellax(refs);
        }
        else if (rellax !== null) {
            rellax.destroy();
            rellax = null;
        }
    });

    return () => {
        if (rellax !== null) {
            rellax.destroy();
        }
    }
}

const initRellax = (refs) => {
    return new Rellax(refs.rellax, {
        center: true,
        percentage: .5,
        speed: 2
    });
}
