import './bootstrap';

import Alpine from 'alpinejs';
import biometricService from './services/biometricService';

window.Alpine = Alpine;
window.biometricService = biometricService;

Alpine.start();
