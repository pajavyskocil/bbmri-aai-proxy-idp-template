# Change Log
All notable changes to this project will be documented in this file.

## [Unreleased]
#### Fixed
- Fixed the style of Added,Changed,Fixed,Removed,...

#### Changed
- Removed warning template - it is no longer needed here because it was moved to module perun

## [v2.3.0]
#### Added
- Added file phpcs.xml

#### Changed
- Changed code style to PSR-2
- addInstitution URL and email in disco-tpl.php are loaded from a config file
- Templates are included from module perun
- Using of short array syntax (from array() to [])

#### Fixed
- Fixed the email address in footer

## [v2.2.0]
#### Added
- Added support for pass selected IdP from SP in AuthnContextClassRef attribute.
    - It's required add this line into module_perun.php config file 
    <pre>
    'disco.removeAuthnContextClassRefPrefix' => 'urn:cesnet:proxyidp:',
    </pre> 
    
#### Changed
- Social Idps are not shown when adding institution

## [v2.1.1]
#### Fixed
- Fixed RegistrationURL for LifeScience Hostel


## [v2.1.0]
#### Added
- Added support for LifeScience hostel

## [V2.0.0]
#### Added
- Added favicon 

#### Changed
- Renamed directory 'themes/bbmri-eric' to 'themes/bbmri' and file 'bbmri-eric.css' to 'bbmri.css'
- Fixed names in LICENSE, footer and other files

## [v1.0.0]
#### Added
- Possibility to show a warning in disco-tpl
- Added License
- Added badges to README

#### Changed
 - Filling email is now required for reporting error
 - Changed help-block text for email in report error form
 - Whole module now uses a dictionary
 
 [Unreleased]: https://github.com/CESNET/bbmri-aai-proxy-idp-template/tree/master
 [v2.3.0]: https://github.com/CESNET/bbmri-aai-proxy-idp-template/tree/v2.3.0
 [v2.2.0]: https://github.com/CESNET/bbmri-aai-proxy-idp-template/tree/v2.2.0
 [v2.1.1]: https://github.com/CESNET/bbmri-aai-proxy-idp-template/tree/v2.1.1
 [v2.1.0]: https://github.com/CESNET/bbmri-aai-proxy-idp-template/tree/v2.1.0
 [v2.0.0]: https://github.com/CESNET/bbmri-aai-proxy-idp-template/tree/v2.0.0
 [v1.0.0]: https://github.com/CESNET/bbmri-aai-proxy-idp-template/tree/v1.0.0
