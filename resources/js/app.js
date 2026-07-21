import { initDarkMode } from './dark-mode';
import { initMetadataExtractor } from './metadata-extractor';
import { initDomainIntelligence } from './domain-intelligence';

document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initMetadataExtractor();
    initDomainIntelligence();
});