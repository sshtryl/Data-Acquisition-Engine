import { initDarkMode } from './dark-mode';
import { initMetadataExtractor } from './metadata-extractor';
import { initDomainIntelligence } from './domain-intelligence';
import { initCompanyLocation } from './company-location-finder';
import { initFinalIntegration } from './final-integration';

document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initMetadataExtractor();
    initDomainIntelligence();
    initCompanyLocation();
    initFinalIntegration();
});