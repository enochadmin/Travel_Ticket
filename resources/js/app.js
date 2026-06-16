import './bootstrap';
import { initTheme } from './theme';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
initTheme();
