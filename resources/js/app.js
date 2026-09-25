import './bootstrap';
import '../css/app.css';
import './layout/nav.js';
import './layout/tema.js';
import { aplicarComboboxes } from './util/combobox.js';

document.addEventListener('DOMContentLoaded', () => aplicarComboboxes());
