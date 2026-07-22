import { initDarkMode } from './dark-mode';
import { initMetadataExtractor } from './MetadataExtractor';
import { initDomainIntelligence } from './DomainIntelligence';
import { initCompanyLocation } from './CompanyLocation';
import { initFinalIntegration } from './FinalIntegration';

document.addEventListener('DOMContentLoaded', () => {
    initDarkMode();
    initMetadataExtractor();
    initDomainIntelligence();
    initCompanyLocation();
    initFinalIntegration();
});