import './bootstrap';
import Lenis from 'lenis';
import 'lenis/dist/lenis.css';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

if (!reduceMotion.matches) {
    new Lenis({
        autoRaf: true,
        duration: 1.05,
        smoothWheel: true,
    });
}
