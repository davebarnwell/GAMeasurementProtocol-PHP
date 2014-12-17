GAMeasurementProtocol-PHP
=========================

track events from the server in Google Analytics from PHP

  init class

    require ('GAMeasurementProtocol.php');  // pref. via an autoloader
    // Init with your GA properties settings
    $ga = new GAMeasurementProtocol('UA-{yourcode}', 'yourdomainname.com');

  Then track an event server side
  
    $ga->trackEvent('event_catgeory','action','optional-label','optional-value');

  Or track an application exception

    $ga->trackException('fatal exception description',1); 
    $ga->trackException('non-fatal exception description',0);

  Or track a page view useful for ajax based page changes in place

    $ga->trackPageView('/{url_to_track}/','{your page title}');
  
