GAMeasurementProtocol-PHP
=========================

track events from the server in Google Analytics from PHP

  init class

    require ('GAMeasurementProtocol.php');                             // pref. via an autoloader
    $ga = new GAMeasurementProtocol('UA-{yourcode}', 'yourname.com');  // Tracking to Google Analytics


  Then track a page view useful for ajax based page changes in place

    $ga->trackPageView('/{url_to_track}/','{your page title}');
  
  Or track an event server side
  
    $ga->trackEvent('event_catgeory','action','optional-label','optional-value');

