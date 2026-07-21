import { initDarkMode } from './dark-mode';
import { initMetadataExtractor } from './metadata-extractor';

document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initMetadataExtractor();
});