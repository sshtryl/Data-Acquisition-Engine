import { initDarkMode } from './dark-mode';
import { initMetadataExtractor } from './metadata-extractor';
import { initDomainIntelligence } from './domain-intelligence';
import { initCompanyLocation } from './company-location-finder';

document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initMetadataExtractor();
    initDomainIntelligence();
    initCompanyLocation();
});