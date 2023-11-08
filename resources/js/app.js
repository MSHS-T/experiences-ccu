import './bootstrap';
import Alpine from 'alpinejs';
import * as dayjs from 'dayjs';
import 'dayjs/locale/fr'; // import locale
import.meta.glob([
  '../images/**',
]);

dayjs.locale('fr'); // use locale
window.dayjs = dayjs;
window.Alpine = Alpine;

Alpine.start();